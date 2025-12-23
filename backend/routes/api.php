<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\ProductController;

// 🔐 ADMIN ROUTES
// TẠM THỜI BỎ AUTH ĐỂ TEST VỚI JMETER - NHỚ BẬT LẠI SAU KHI TEST XONG!
// Route::middleware(['auth:sanctum', 'admin'])
Route::prefix('admin')
    ->group(function () {
        // Products routes
        Route::get('products/statistics', [AdminProductController::class, 'statistics']);
        Route::post('products/import', [AdminProductController::class, 'import']);
        Route::post('products/import/openlibrary', [AdminProductController::class, 'importMany']);
        Route::post('products/bulk-delete', [AdminProductController::class, 'bulkDelete']);
        Route::delete('products/delete-all', [AdminProductController::class, 'deleteAll']);
        Route::apiResource('products', AdminProductController::class);

        // Users routes
        Route::get('users/statistics', [AdminUserController::class, 'statistics']);
        Route::post('users/bulk-delete', [AdminUserController::class, 'bulkDelete']);
        Route::apiResource('users', AdminUserController::class);
    });

// 🔓 PUBLIC ROUTES
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);

// 🔐 AUTH ROUTES
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/cart', [\App\Http\Controllers\Api\CartController::class, 'index']);
    Route::post('/cart', [\App\Http\Controllers\Api\CartController::class, 'store']);
    Route::put('/cart/{id}', [\App\Http\Controllers\Api\CartController::class, 'update']);
    Route::delete('/cart/{id}', [\App\Http\Controllers\Api\CartController::class, 'destroy']);
    Route::delete('/cart', [\App\Http\Controllers\Api\CartController::class, 'clear']);
});

// �🔥 LOAD AUTH ROUTES (BREEZE)
require __DIR__.'/auth.php';
