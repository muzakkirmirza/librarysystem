<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\InventoryItemController;
use App\Http\Controllers\Api\BorrowRequestController;
use App\Http\Controllers\Api\LoanTransactionController;
use App\Http\Controllers\Api\ItemMaintenanceLogController;

// Public Auth Routes (Bina Token Ke Access Ho Sakti Hain)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected APIs (Token Required - Faqt Logged In Users Ke Liye)
Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);

    // Resource Endpoints
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('inventory-items', InventoryItemController::class);
    Route::apiResource('borrow-requests', BorrowRequestController::class);
    Route::apiResource('loan-transactions', LoanTransactionController::class);
    Route::apiResource('maintenance-logs', ItemMaintenanceLogController::class);
});