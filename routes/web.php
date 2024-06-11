<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'index'])->name('products.index');

Route::get('/upload-form', function () {
    return view('products.upload');
})->name('products.upload-form');

Route::post('/products/upload', [ProductController::class, 'upload'])->name('products.upload'); 

Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');