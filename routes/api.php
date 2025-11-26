<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ContractController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\InvoiceAttachmentController;
use App\Http\Controllers\Api\CompanyJoinRequestController;

Route::middleware('tenant.auth')->group(function () {
// clients
Route::get   ('/clients',        [ClientController::class, 'index']);
Route::post  ('/clients',        [ClientController::class, 'store']);
Route::get   ('/clients/{id}',   [ClientController::class, 'show']);
Route::put   ('/clients/{id}',   [ClientController::class, 'update']);
Route::patch ('/clients/{id}',   [ClientController::class, 'update']);
Route::delete('/clients/{id}',   [ClientController::class, 'destroy']);

// contracts
Route::get   ('/contracts',        [ContractController::class, 'index']);
Route::post  ('/contracts',        [ContractController::class, 'store']);
Route::get   ('/contracts/{id}',   [ContractController::class, 'show']);
Route::put   ('/contracts/{id}',   [ContractController::class, 'update']);
Route::patch ('/contracts/{id}',   [ContractController::class, 'update']);
Route::delete('/contracts/{id}',   [ContractController::class, 'destroy']);

// invoices
Route::get   ('/invoices',        [InvoiceController::class, 'index']);
Route::post  ('/invoices',        [InvoiceController::class, 'store']);
Route::get   ('/invoices/{id}',   [InvoiceController::class, 'show']);
Route::put   ('/invoices/{id}',   [InvoiceController::class, 'update']);
Route::patch ('/invoices/{id}',   [InvoiceController::class, 'update']);
Route::delete('/invoices/{id}',   [InvoiceController::class, 'destroy']);

// invoice attachments
Route::get   ('/invoices/{invoiceId}/attachments',                         [InvoiceAttachmentController::class, 'index']);
Route::post  ('/invoices/{invoiceId}/attachments',                         [InvoiceAttachmentController::class, 'store']);
Route::get   ('/invoices/{invoiceId}/attachments/{attachmentId}',          [InvoiceAttachmentController::class, 'show']);
Route::delete('/invoices/{invoiceId}/attachments/{attachmentId}',          [InvoiceAttachmentController::class, 'destroy']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



    // Список заявок текущей компании
    Route::get('/join-requests', [CompanyJoinRequestController::class, 'index']);

    // Одобрить заявку
    Route::post('/join-requests/{id}/approve', [CompanyJoinRequestController::class, 'approve']);
    
    // (опционально) отклонить
    // Route::post('/join-requests/{id}/reject', [CompanyJoinRequestController::class, 'reject']);
});