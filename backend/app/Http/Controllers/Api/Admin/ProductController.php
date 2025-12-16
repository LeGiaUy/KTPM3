<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    // GET /api/admin/products
    public function index(Request $request)
    {
        $query = Product::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('publisher', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return $query->latest()->paginate($request->get('per_page', 10));
    }

    // GET /api/admin/products/statistics
    public function statistics()
    {
        $totalProducts = Product::count();
        $activeProducts = Product::where('status', 'active')->count();
        $totalStock = (int) (Product::sum('stock') ?? 0);
        $totalValue = (float) (Product::selectRaw('SUM(price * stock) as total')
            ->value('total') ?? 0);

        return response()->json([
            'totalProducts' => (int) $totalProducts,
            'activeProducts' => (int) $activeProducts,
            'totalStock' => $totalStock,
            'totalValue' => $totalValue,
        ]);
    }

    // POST /api/admin/products
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'isbn' => 'nullable|unique:products',
            'author' => 'nullable|string',
            'publisher' => 'nullable|string',
            'cover_url' => 'nullable|url',
            'description' => 'nullable|string',
            'status' => 'in:active,inactive',
        ]);

        $product = Product::create($data);

        return response()->json([
            'message' => 'Product created',
            'product' => $product,
        ], 201);
    }

    // GET /api/admin/products/{id}
    public function show(Product $product)
    {
        return $product;
    }

    // PUT /api/admin/products/{id}
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'isbn' => 'nullable|unique:products,isbn,' . $product->id,
            'author' => 'nullable|string',
            'publisher' => 'nullable|string',
            'cover_url' => 'nullable|url',
            'description' => 'nullable|string',
            'status' => 'in:active,inactive',
        ]);

        $product->update($data);

        return response()->json([
            'message' => 'Product updated',
            'product' => $product,
        ]);
    }

    // DELETE /api/admin/products/{id}
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'message' => 'Product deleted'
        ]);
    }

    // POST /api/admin/products/import
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls|max:10240', // 10MB max
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
            'skipped' => 0,
        ];

        try {
            if (in_array($extension, ['csv', 'txt'])) {
                $results = $this->importCsv($file);
            } elseif (in_array($extension, ['xlsx', 'xls'])) {
                // Check if PhpSpreadsheet is available
                if (!class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
                    return response()->json([
                        'message' => 'Excel import requires PhpSpreadsheet library. Please install it or export your Excel file to CSV format.',
                        'results' => $results,
                    ], 400);
                }
                $results = $this->importExcel($file);
            } else {
                return response()->json([
                    'message' => 'File format not supported. Please use CSV format.',
                    'results' => $results,
                ], 400);
            }

            return response()->json([
                'message' => 'Import completed',
                'results' => $results,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Import failed: ' . $e->getMessage(),
                'results' => $results,
            ], 500);
        }
    }

    private function importCsv($file)
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
            'skipped' => 0,
        ];

        // Try to detect encoding and convert to UTF-8
        $content = file_get_contents($file->getRealPath());
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        
        if ($encoding && $encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
            $tempFile = tempnam(sys_get_temp_dir(), 'csv_import_');
            file_put_contents($tempFile, $content);
            $handle = fopen($tempFile, 'r');
        } else {
            $handle = fopen($file->getRealPath(), 'r');
        }
        
        // Skip header row
        $header = fgetcsv($handle);
        
        // Map CSV columns (adjust based on your CSV format)
        // Expected columns: title, price, stock, author, publisher, isbn, cover_url, description, status
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }
            
            if (count($row) < 3) { // At least title, price, stock
                $results['skipped']++;
                $results['errors'][] = "Row {$rowNumber}: Insufficient columns";
                continue;
            }

            try {
                // Clean and trim values
                $row = array_map('trim', $row);
                
                $data = [
                    'title' => $row[0] ?? '',
                    'price' => isset($row[1]) ? (float) str_replace([',', ' '], '', $row[1]) : 0,
                    'stock' => isset($row[2]) ? (int) str_replace([',', ' '], '', $row[2]) : 0,
                    'author' => !empty($row[3]) ? $row[3] : null,
                    'publisher' => !empty($row[4]) ? $row[4] : null,
                    'isbn' => !empty($row[5]) ? $row[5] : null,
                    'cover_url' => !empty($row[6]) ? $row[6] : null,
                    'description' => !empty($row[7]) ? $row[7] : null,
                    'status' => isset($row[8]) && in_array(strtolower(trim($row[8])), ['active', 'inactive']) 
                        ? strtolower(trim($row[8])) 
                        : 'active',
                ];

                // Validate required fields
                if (empty($data['title']) || $data['price'] < 0 || $data['stock'] < 0) {
                    $results['failed']++;
                    $results['errors'][] = "Row {$rowNumber}: Invalid required fields (Title, Price, Stock)";
                    continue;
                }

                // Validate URL if provided
                if (!empty($data['cover_url']) && !filter_var($data['cover_url'], FILTER_VALIDATE_URL)) {
                    $results['failed']++;
                    $results['errors'][] = "Row {$rowNumber}: Invalid cover URL";
                    continue;
                }

                // Check if ISBN already exists
                if (!empty($data['isbn'])) {
                    $existing = Product::where('isbn', $data['isbn'])->first();
                    if ($existing) {
                        $results['skipped']++;
                        $results['errors'][] = "Row {$rowNumber}: ISBN {$data['isbn']} already exists";
                        continue;
                    }
                }

                Product::create($data);
                $results['success']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Row {$rowNumber}: " . $e->getMessage();
            }
        }

        fclose($handle);
        
        // Clean up temp file if created
        if (isset($tempFile) && file_exists($tempFile)) {
            unlink($tempFile);
        }
        
        return $results;
    }

    private function importExcel($file)
    {
        // For Excel files, we'll use PhpSpreadsheet
        // This method is only called if PhpSpreadsheet is available (checked in import method)

        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
            'skipped' => 0,
        ];

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            // Skip header row
            array_shift($rows);
            
            $rowNumber = 1;
            
            foreach ($rows as $row) {
                $rowNumber++;
                
                if (count($row) < 3) {
                    $results['skipped']++;
                    $results['errors'][] = "Row {$rowNumber}: Insufficient columns";
                    continue;
                }

                try {
                    $data = [
                        'title' => $row[0] ?? '',
                        'price' => isset($row[1]) ? (float) $row[1] : 0,
                        'stock' => isset($row[2]) ? (int) $row[2] : 0,
                        'author' => $row[3] ?? null,
                        'publisher' => $row[4] ?? null,
                        'isbn' => $row[5] ?? null,
                        'cover_url' => $row[6] ?? null,
                        'description' => $row[7] ?? null,
                        'status' => isset($row[8]) && in_array(strtolower($row[8]), ['active', 'inactive']) 
                            ? strtolower($row[8]) 
                            : 'active',
                    ];

                    // Validate required fields
                    if (empty($data['title']) || $data['price'] < 0 || $data['stock'] < 0) {
                        $results['failed']++;
                        $results['errors'][] = "Row {$rowNumber}: Invalid required fields";
                        continue;
                    }

                    // Check if ISBN already exists
                    if (!empty($data['isbn'])) {
                        $existing = Product::where('isbn', $data['isbn'])->first();
                        if ($existing) {
                            $results['skipped']++;
                            $results['errors'][] = "Row {$rowNumber}: ISBN {$data['isbn']} already exists";
                            continue;
                        }
                    }

                    Product::create($data);
                    $results['success']++;
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = "Row {$rowNumber}: " . $e->getMessage();
                }
            }
        } catch (\Exception $e) {
            throw new \Exception('Error reading Excel file: ' . $e->getMessage());
        }

        return $results;
    }

    // POST /api/admin/products/import/openlibrary
    public function importMany(Request $request)
    {
        $request->validate([
            'keyword' => 'required|string|max:255',
            'limit' => 'required|integer|min:1|max:20',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        try {
            // 1. Gọi OpenLibrary API
            // Tắt SSL verification cho development (Windows SSL certificate issue)
            $response = Http::withOptions([
                'verify' => false,
                'timeout' => 10,
            ])->get(
                'https://openlibrary.org/search.json',
                [
                    'q' => $request->keyword,
                    'limit' => $request->limit,
                ]
            );

            if ($response->failed()) {
                \Log::error('OpenLibrary API failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                
                return response()->json([
                    'message' => 'Open Library API trả về lỗi: ' . $response->status(),
                ], 500);
            }

            $data = $response->json();
            
            if (!isset($data['docs']) || !is_array($data['docs'])) {
                return response()->json([
                    'message' => 'Invalid response from Open Library',
                ], 500);
            }
            
            $docs = $data['docs'];
            $inserted = [];
            $skipped = 0;
            $errors = [];

            // 2. Duyệt từng sách và import
            foreach ($docs as $index => $doc) {
                try {
                    // Bỏ qua nếu không có title
                    if (empty($doc['title'])) {
                        $skipped++;
                        $errors[] = "Book " . ($index + 1) . ": Missing title";
                        continue;
                    }

                    // Lấy ISBN (ưu tiên ISBN-13, fallback ISBN-10, nếu không có thì tạo UUID)
                    $isbn = null;
                    if (isset($doc['isbn']) && is_array($doc['isbn']) && !empty($doc['isbn'])) {
                        // Tìm ISBN-13 trước
                        foreach ($doc['isbn'] as $isbnValue) {
                            if (strlen($isbnValue) == 13) {
                                $isbn = $isbnValue;
                                break;
                            }
                        }
                        // Nếu không có ISBN-13, lấy ISBN-10
                        if (!$isbn) {
                            $isbn = $doc['isbn'][0];
                        }
                    }

                    // Nếu không có ISBN, tạo UUID làm identifier
                    if (!$isbn) {
                        $isbn = 'OL-' . Str::uuid()->toString();
                    }

                    // 3. Kiểm tra ISBN đã tồn tại
                    if (Product::where('isbn', $isbn)->exists()) {
                        $skipped++;
                        $errors[] = "Book " . ($index + 1) . " ({$doc['title']}): ISBN already exists";
                        continue;
                    }

                    // 4. Tạo cover URL từ ISBN hoặc cover_i
                    $coverUrl = null;
                    if ($isbn && strpos($isbn, 'OL-') !== 0) {
                        $coverUrl = "https://covers.openlibrary.org/b/isbn/{$isbn}-L.jpg";
                    } elseif (isset($doc['cover_i'])) {
                        $coverUrl = "https://covers.openlibrary.org/b/id/{$doc['cover_i']}-L.jpg";
                    } elseif (isset($doc['cover_edition_key'])) {
                        $coverUrl = "https://covers.openlibrary.org/b/olid/{$doc['cover_edition_key']}-L.jpg";
                    }

                    // 5. Lấy author (lấy tác giả đầu tiên nếu có nhiều)
                    $author = null;
                    if (isset($doc['author_name']) && is_array($doc['author_name']) && !empty($doc['author_name'])) {
                        $author = $doc['author_name'][0];
                    }

                    // 6. Lấy publisher (lấy nhà xuất bản đầu tiên nếu có nhiều)
                    $publisher = null;
                    if (isset($doc['publisher']) && is_array($doc['publisher']) && !empty($doc['publisher'])) {
                        $publisher = $doc['publisher'][0];
                    }

                    // 7. Tạo sản phẩm
                    $productData = [
                        'title' => $doc['title'],
                        'author' => $author,
                        'publisher' => $publisher,
                        'isbn' => $isbn,
                        'cover_url' => $coverUrl,
                        'description' => null,
                        'price' => $request->price,
                        'stock' => $request->stock,
                        'status' => 'active',
                    ];

                    // Thêm publish_date nếu có
                    if (isset($doc['first_publish_year']) && is_numeric($doc['first_publish_year'])) {
                        $productData['publish_date'] = $doc['first_publish_year'] . '-01-01';
                    }

                    // Thêm pages nếu có
                    if (isset($doc['number_of_pages_median']) && is_numeric($doc['number_of_pages_median'])) {
                        $productData['pages'] = (int) $doc['number_of_pages_median'];
                    }

                    $product = Product::create($productData);

                    $inserted[] = $product;
                } catch (\Exception $e) {
                    $title = isset($doc['title']) ? $doc['title'] : 'Unknown';
                    $errors[] = "Book " . ($index + 1) . " ({$title}): " . $e->getMessage();
                }
            }

            return response()->json([
                'message' => 'Import completed',
                'total_found' => count($docs),
                'total_imported' => count($inserted),
                'total_skipped' => $skipped,
                'books' => $inserted,
                'errors' => $errors,
            ], 201);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            \Log::error('OpenLibrary HTTP error: ' . $e->getMessage(), [
                'request' => $request->all(),
            ]);
            
            return response()->json([
                'message' => 'Không thể kết nối đến Open Library API. Vui lòng thử lại sau.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        } catch (\Exception $e) {
            \Log::error('OpenLibrary import error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
            
            return response()->json([
                'message' => 'Lỗi khi import: ' . $e->getMessage(),
                'error' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }
}
