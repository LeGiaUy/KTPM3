<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\ProductController;

// 🔐 ADMIN ROUTES
Route::middleware(['auth:sanctum', 'admin'])
    ->prefix('admin')
    ->group(function () {
        // Đặt các route đặc biệt TRƯỚC apiResource để tránh conflict
        Route::get('products/statistics', [AdminProductController::class, 'statistics']);
        Route::post('products/import', [AdminProductController::class, 'import']);
        Route::post('products/import/openlibrary', [AdminProductController::class, 'importMany']);
        Route::post('products/bulk-delete', [AdminProductController::class, 'bulkDelete']);
        Route::delete('products/delete-all', [AdminProductController::class, 'deleteAll']);
        // apiResource phải đặt SAU các route đặc biệt
        Route::apiResource('products', AdminProductController::class);
    });

// 🔓 PUBLIC ROUTES
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);

// 🔥 LOAD AUTH ROUTES (BREEZE)
require __DIR__.'/auth.php';
