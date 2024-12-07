<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoodsCategoryController;
use App\Http\Controllers\VendorsManagementController;
use App\Http\Controllers\GoodsManagementController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('goods-category', GoodsCategoryController::class);
Route::apiResource('vendors', VendorsManagementController::class);
Route::apiResource('goods', GoodsManagementController::class);
