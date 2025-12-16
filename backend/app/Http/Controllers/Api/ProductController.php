<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // GET /api/products
    public function index(Request $request)
    {
        $query = Product::where('status', 'active');

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

        return $query->latest()->paginate($request->get('per_page', 12));
    }

    // GET /api/products/{id}
    public function show(Product $product)
    {
        // Chỉ hiển thị sản phẩm đang hoạt động
        if ($product->status !== 'active') {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }

        return $product;
    }
}
