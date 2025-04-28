<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Private routes
Route::middleware(['auth:sanctum'])->group(function () {
    // Stores
    Route::get('/stores', [StoreController::class, 'getStores'])->name('getStores');
    Route::post('/stores/create', [StoreController::class, 'createStore'])->name('createStore');

    Route::prefix('/{storeId}')->middleware(['checkUserStore'])->group(function () {
        // Category
        Route::get('/categories', [CategoryController::class, 'getCategories'])->name('getCategories');
        Route::post('/categories/create', [CategoryController::class, 'createCategory'])->name('createCategory')->middleware('isAdmin');
        Route::put('/categories/{categoryId}', [CategoryController::class, 'updateCategory'])->name('updateCategory')->middleware('isAdmin');
        Route::delete('/categories/{categoryId}', [CategoryController::class, 'deleteCategory'])->name('deleteCategory')->middleware('isAdmin');

        // Brand
        Route::get('/brands', [BrandController::class, 'getBrands'])->name('getBrands');
        Route::post('/brands/create', [BrandController::class, 'createBrand'])->name('createBrand')->middleware('isAdmin');
        Route::put('/brands/{brandId}', [BrandController::class, 'updateBrand'])->name('updateBrand')->middleware('isAdmin');
        Route::delete('/brands/{brandId}', [BrandController::class, 'deleteBrand'])->name('deleteBrand')->middleware('isAdmin');

        // Suppliers
        Route::get('/suppliers', [SupplierController::class, 'getSuppliers'])->name('getSuppliers');
        Route::post('/suppliers/create', [SupplierController::class, 'createSupplier'])->name('createSupplier')->middleware('isAdmin');
        Route::put('/suppliers/{supplierId}', [SupplierController::class, 'updateSupplier'])->name('updateSupplier')->middleware('isAdmin');
        Route::delete('/suppliers/{supplierId}', [SupplierController::class, 'deleteSupplier'])->name('deleteSupplier')->middleware('isAdmin');
        // Products
        Route::get('/products', [ProductController::class, 'getAllProducts'])->name('getAllProducts');
        Route::get('/products/{productId}', [ProductController::class, 'getSingleProduct'])->name('getSingleProduct');
        Route::post('/products/create', [ProductController::class, 'createProduct'])->name('createProduct')->middleware('isAdmin');
        Route::put('/products/{productId}', [ProductController::class, 'updateProduct'])->name('updateProduct')->middleware('isAdmin');
        Route::delete('/products/{productId}', [ProductController::class, 'deleteProduct'])->name('deleteProduct')->middleware('isAdmin');
        // Purchases
        Route::get('/purchases', [PurchaseController::class, 'getAllPurchases'])->name('getAllPurchases');
        Route::post('/purchases/create', [PurchaseController::class, 'addPurchase'])->name('addPurchase');

        // Sell
        Route::get('/sells', [SellController::class, 'getAllSells'])->name('getAllSells');
        Route::post('/sells/create', [SellController::class, 'createSell'])->name('createSell');
        // Activity Logs
        Route::get('/logs', [ActivityLogController::class, 'getActivityLog'])->name('getActivityLog');
        Route::get('/dashboard', [DashboardController::class, 'getDashboardData'])->name('getDashboardData');

        // Auth routes
        Route::get('/users', [AuthController::class, 'getUsers'])->name('getUsers');

    });

    Route::post('/create-user', [AuthController::class, 'createUser'])->name('createUser')->middleware('isAdmin');
    Route::put('/user/update/{userId}', [AuthController::class, 'updateUser'])->name('updateUser')->middleware('isAdmin');
    Route::put('/user/change-status', [AuthController::class, 'statusUpdate'])->name('statusUpdate')->middleware('isAdmin');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('changePassword');
});
