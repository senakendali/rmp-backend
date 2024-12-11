<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoodsCategoryController;
use App\Http\Controllers\VendorsManagementController;
use App\Http\Controllers\GoodsManagementController;
use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RolePermissionController;

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

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->get('/permissions/{user}', [AuthController::class, 'fetchPermissions']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/roles', [RolePermissionController::class, 'index']); // List all roles
    Route::post('/roles', [RolePermissionController::class, 'store']); // Create a new role
    Route::put('/roles/{role}', [RolePermissionController::class, 'update']); // Update a role
    Route::delete('/roles/{role}', [RolePermissionController::class, 'destroy']); // Delete a role

    Route::post('/roles/{role}/permissions', [RolePermissionController::class, 'assignPermission']); // Assign permissions to a role
    Route::delete('/roles/{role}/permissions/{permission}', [RolePermissionController::class, 'revokePermission']); // Revoke a permission from a role

    Route::post('/users/{user}/roles', [RolePermissionController::class, 'assignRoleToUser']); // Assign a role to a user
    Route::delete('/users/{user}/roles/{role}', [RolePermissionController::class, 'removeRoleFromUser']); // Remove a role from a user
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/roles', [RolePermissionController::class, 'index']); // List all roles
    Route::post('/roles', [RolePermissionController::class, 'store']); // Create a new role
    Route::put('/roles/{role}', [RolePermissionController::class, 'update']); // Update a role
    Route::delete('/roles/{role}', [RolePermissionController::class, 'destroy']); // Delete a role

    Route::post('/roles/{role}/permissions', [RolePermissionController::class, 'assignPermission']); // Assign permissions to a role
    Route::delete('/roles/{role}/permissions/{permission}', [RolePermissionController::class, 'revokePermission']); // Revoke a permission from a role

    Route::post('/users/{user}/roles', [RolePermissionController::class, 'assignRoleToUser']); // Assign a role to a user
    Route::delete('/users/{user}/roles/{role}', [RolePermissionController::class, 'removeRoleFromUser']); // Remove a role from a user

   
});



