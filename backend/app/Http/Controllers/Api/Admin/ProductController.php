<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // GET /api/admin/products
    public function index()
    {
        return Product::latest()->paginate(10);
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
}
