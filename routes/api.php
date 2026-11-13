<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\InventoryItemController;
use App\Http\Controllers\Api\BorrowRequestController;
use App\Http\Controllers\Api\LoanTransactionController;
use App\Http\Controllers\Api\ItemMaintenanceLogController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Frontend ko Role batane ke liye yeh route zaroori tha
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('inventory-items', InventoryItemController::class);
    Route::apiResource('borrow-requests', BorrowRequestController::class);
    Route::apiResource('loan-transactions', LoanTransactionController::class);
    Route::apiResource('maintenance-logs', ItemMaintenanceLogController::class);
});