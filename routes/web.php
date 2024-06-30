<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Authentication Routes...
Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('products.index');

    Route::get('/upload-form', [ProductController::class, 'uploadfile'])->name('products.upload-form');
    Route::post('/products/upload', [ProductController::class, 'upload'])->name('products.upload');

    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');

    Route::get('/invite', [InviteController::class, 'create'])->name('invite.create');
    Route::post('/invite', [InviteController::class, 'send'])->name('invite.send');

    Route::get('/change-password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('/change-password', [PasswordController::class, 'update'])->name('password.update');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::put('/users/{id}/role', [UserController::class, 'updateRole'])->name('users.update-role');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/toggleLogging', [SettingController::class, 'toggleLogging'])->name('settings.toggleLogging');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/api/products', [ProductController::class, 'apiIndex']);
    });
    Route::middleware('auth:api')->group(function () {
        Route::put('/api/products/{id}', [ProductController::class, 'apiupdate']);
    });
    Route::post('/api/login', [LoginController::class, 'apiStore']);
    Route::post('/api/logout', [LoginController::class, 'apiDestroy']);
});