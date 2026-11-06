<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\InventoryItemController;

// Categories APIs (GET, POST, PUT, DELETE)
Route::apiResource('categories', CategoryController::class);

// Inventory Items APIs (GET, POST, PUT, DELETE)
Route::apiResource('inventory-items', InventoryItemController::class);