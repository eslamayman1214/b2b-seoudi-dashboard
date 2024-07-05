<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// API Authentication Routes...
Route::post('login', [LoginController::class, 'apiStore']);
//Route::post('logout', [LoginController::class, 'apiDestroy']);

Route::middleware('auth:sanctum')->group(function () {    
    // API Routes
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'apiIndex']);
        Route::put('{id}', [ProductController::class, 'apiUpdate'])->middleware('auth:api');
    });   
});