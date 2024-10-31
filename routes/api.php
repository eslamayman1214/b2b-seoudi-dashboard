<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\QuotationController;
use App\Http\Controllers\Api\TicketController;
use Illuminate\Support\Facades\Route;

// API Authentication Routes...
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // API Routes
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::put('{id}', [ProductController::class, 'update'])->middleware('auth:api');
        Route::get('products-with-tiers', [ProductController::class, 'getAllProductsWithTiers']);
        Route::post('send-updated-products-to-api', [ProductController::class, 'sendProductDataToApi']);

    });
    Route::post('/tickets', [TicketController::class, 'store']);
    Route::post('/quotations', [QuotationController::class, 'store']);

});