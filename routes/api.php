<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoodsCategoryController;
use App\Http\Controllers\VendorsManagementController;
use App\Http\Controllers\GoodsManagementController;
use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\MenuManagementController;
use App\Http\Controllers\MeasurementUnitController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ProcurementLogController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Goods Category
Route::apiResource('goods-category', GoodsCategoryController::class);

//Vendors
Route::apiResource('vendors', VendorsManagementController::class);
Route::prefix('vendors')->group(function () {
    Route::middleware('auth:sanctum')->put('/updateStatus/{id}', [VendorsManagementController::class, 'updateVerificationStatus']);
});

//Menus/Navigation
Route::apiResource('menus', MenuManagementController::class);

//Goods
Route::apiResource('goods', GoodsManagementController::class);
Route::prefix('goods')->group(function () {
    Route::get('/fetchByCategory/{id}', [GoodsManagementController::class, 'fetchGoodsByCategory']);
});

//Measurement Unit
Route::apiResource('measurement-units', MeasurementUnitController::class);


//Purchase Request
Route::apiResource('purchase-requests', PurchaseRequestController::class);
Route::prefix('purchase-requests')->group(function () {
    Route::get('/history/{itemId}/{departmentId}', [PurchaseRequestController::class, 'getPurchaseHistory']);
    Route::middleware('auth:sanctum')->put('/followUp/{id}', [PurchaseRequestController::class, 'followUp']);
    Route::middleware('auth:sanctum')->put('/updateStatus/{id}', [PurchaseRequestController::class, 'updateStatus']);
});

//Purchase Order
Route::prefix('purchase-order')->group(function () {
    // Resource route for CRUD operations
    Route::apiResource('', PurchaseOrderController::class);

    // Custom route
    
    Route::get('category', [PurchaseOrderController::class, 'getCategoryItemCount']);
    Route::get('item-queues', [PurchaseOrderController::class, 'ItemQueues']);
    Route::middleware('auth:sanctum')->post('create-po', [PurchaseOrderController::class, 'createPo']);
    Route::middleware('auth:sanctum')->post('add-item-to-po', [PurchaseOrderController::class, 'addItemToPo']);
    Route::middleware('auth:sanctum')->patch('move-item-to-another-po', [PurchaseOrderController::class, 'moveItemToAnotherPo']);
    Route::get('list-po', [PurchaseOrderController::class, 'listPo']);
    //Route::middleware('auth:sanctum')->post('add-vendor-to-po', [PurchaseOrderController::class, 'addVendorToPurchaseOrder']);
    //Route::middleware('auth:sanctum')->put('confirm-purchase-order', [PurchaseOrderController::class, 'confirmVendorsOnPurchaseOrder']); 
    Route::middleware('auth:sanctum')->post('manage-vendors-for-po', [PurchaseOrderController::class, 'manageVendorsForPurchaseOrder']);
    Route::middleware('auth:sanctum')->post('/verification', [PurchaseOrderController::class, 'purchaseOrderVerification']);
    Route::middleware('auth:sanctum')->post('/release/{id}', [PurchaseOrderController::class, 'releasePurchaseOrder']);

    Route::get('/fetch-vendor-offer-details/{offer_id}', [PurchaseOrderController::class, 'fetchVendorOfferDetails']);
    Route::middleware('auth:sanctum')->post('submit-vendor-offers', [PurchaseOrderController::class, 'submitVendorOffers']); 
    Route::middleware('auth:sanctum')->put('update-vendor-offers/{offerId}', [PurchaseOrderController::class, 'updateVendorOffer']); 
    Route::get('{id}', [PurchaseOrderController::class, 'show']);
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

//Measurement Unit
Route::apiResource('procurement-logs', ProcurementLogController::class);



