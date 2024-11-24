<?php

use App\Http\Controllers\Api\ProductController as ApiProductController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\RejectionReasonController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Authentication Routes...
Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/validate-otp', [LoginController::class, 'validateOtp'])->name('validate.otp');
Route::post('/resend-otp', [LoginController::class, 'resendOtp'])->name('resend.otp');

Route::middleware(['auth'])->group(function () {

    Route::get('/', [ProductController::class, 'index'])->name('products.index');
    Route::get('upload-form', [ProductController::class, 'uploadfile'])->name('products.upload-form');

    Route::prefix('products')->group(function () {
        Route::post('upload', [ProductController::class, 'upload'])->name('products.upload');
        Route::get('{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('{id}', [ProductController::class, 'update'])->name('products.update');
        Route::post('upload-tiers', [ProductController::class, 'uploadTiers'])->name('tiers.upload');
        Route::post('store', [ProductController::class, 'store'])->name('products.store');

    });
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customerId}/download-document', [CustomerController::class, 'downloadDocument'])
        ->name('customers.downloadDocument');

    Route::get('invite', [InviteController::class, 'create'])->name('invite.create');
    Route::post('invite', [InviteController::class, 'send'])->name('invite.send');

    Route::get('/change-password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('/change-password', [PasswordController::class, 'update'])->name('password.update');

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::put('{id}/role', [UserController::class, 'updateRole'])->name('users.update-role');
        Route::delete('{id}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('settings.index');
        Route::post('saveSettings', [SettingController::class, 'saveSettings'])->name('settings.saveSettings');
        Route::post('toggleLogging', [SettingController::class, 'toggleLogging'])->name('settings.toggleLogging');
        Route::post('sla-limit', [SettingController::class, 'saveSlaLimit'])->name('settings.saveSlaLimit');
        Route::post('fetch-customer-groups', [ProductController::class, 'fetchCustomerGroups'])->name('settings.fetchCustomerGroups');
        Route::post('fetch-products', [ApiProductController::class, 'sendProductDataToApi'])->name('settings.fetchProducts');

    });

    Route::prefix('tickets')->group(function () {
        Route::get('/', [TicketController::class, 'index'])->name('tickets.index');
        Route::get('create', [TicketController::class, 'create'])->name('tickets.create');
        Route::post('/', [TicketController::class, 'store'])->name('tickets.store');
        Route::get('tickets-sla', [TicketController::class, 'showSlaTickets'])->name('tickets.sla');
        Route::get('{ticket}', [TicketController::class, 'show'])->name('tickets.show');
        Route::put('{ticket}', [TicketController::class, 'update'])->name('tickets.update');
        Route::post('update/{ticket}', [TicketController::class, 'update_index']);
        Route::get('{ticket}/download', [TicketController::class, 'downloadAttachment'])->name('tickets.downloadAttachment');
        Route::delete('{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');
    });
    Route::prefix('quotations')->name('quotations.')->group(function () {
        Route::get('/', [QuotationController::class, 'index'])->name('index');
        Route::get('/reply/{id}', [QuotationController::class, 'reply'])->name('reply');
        Route::post('/send-reply/{id}', [QuotationController::class, 'sendReply'])->name('sendReply');
    });
    Route::prefix('rejection-reasons')->name('rejection-reasons.')->group(function () {
        Route::get('/', [RejectionReasonController::class, 'index'])->name('index');
        Route::post('/', [RejectionReasonController::class, 'store'])->name('store');
        Route::delete('/{optionId}', [RejectionReasonController::class, 'destroy'])->name('destroy');
    });

    Route::get('/customers/{customerId}/rejection-reason', [CustomerController::class, 'showRejectionReason'])->name('customers.rejectionReason');
    Route::post('/customers/rejection-reason/save', [CustomerController::class, 'saveRejectionReason'])->name('customers.saveRejectionReason');
    Route::delete('/tiers/{id}', [TierController::class, 'destroy'])->name('tiers.destroy');
    Route::post('/customers/update-document-status', [CustomerController::class, 'updateDocumentStatus'])->name('customers.updateDocumentStatus');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});
