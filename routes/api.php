<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\InventoryItemController;
use App\Http\Controllers\Api\BorrowRequestController;
use App\Http\Controllers\Api\LoanTransactionController;

// Categories APIs
Route::apiResource('categories', CategoryController::class);

// Inventory Items APIs
Route::apiResource('inventory-items', InventoryItemController::class);

// Borrow Requests APIs
Route::apiResource('borrow-requests', BorrowRequestController::class);

// Loan Transactions APIs
Route::apiResource('loan-transactions', LoanTransactionController::class);