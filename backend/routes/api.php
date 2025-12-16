<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\ProductController;

// 🔐 ADMIN ROUTES
Route::middleware(['auth:sanctum', 'admin'])
    ->prefix('admin')
    ->group(function () {
        // Đặt các route đặc biệt TRƯỚC apiResource để tránh conflict
        Route::get('products/statistics', [ProductController::class, 'statistics']);
        Route::post('products/import', [ProductController::class, 'import']);
        Route::post('products/import/openlibrary', [ProductController::class, 'importMany']);
        // apiResource phải đặt SAU các route đặc biệt
        Route::apiResource('products', ProductController::class);
    });

// 🔓 PUBLIC ROUTES
use App\Models\Product;

Route::get('/products', function () {
    return Product::where('status', 'active')->paginate(10);
});

Route::get('/products/{product}', function (Product $product) {
    return $product;
});

// 🔥 LOAD AUTH ROUTES (BREEZE)
require __DIR__.'/auth.php';
