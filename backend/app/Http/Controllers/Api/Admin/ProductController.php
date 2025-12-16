<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

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
}
