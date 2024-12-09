<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoodsCategoryController;
use App\Http\Controllers\VendorsManagementController;
use App\Http\Controllers\GoodsManagementController;
use App\Http\Controllers\PurchaseRequestController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('goods-category', GoodsCategoryController::class);
Route::apiResource('vendors', VendorsManagementController::class);
Route::apiResource('goods', GoodsManagementController::class);
Route::apiResource('purchase-requests', PurchaseRequestController::class);
Route::prefix('purchase-requests')->group(function () {
    Route::put('/followUp/{id}', [PurchaseRequestController::class, 'followUp']);
    Route::put('/updateStatus/{id}', [PurchaseRequestController::class, 'updateStatus']);
});

