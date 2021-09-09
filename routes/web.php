<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\DealerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PurchaseOrderItemController;
use App\Http\Controllers\InventoryController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


require __DIR__.'/auth.php';


Route::get('/invite/{email}/{token}', [UserController::class, 'registration'])->name('registration.store');
Route::post('/invite', [UserController::class, 'registrationPassword'])->name('registration.password');



Route::group(['middleware' => ['auth']], function () { 

    Route::get('/', function () {
        return view('dashboard');
    })->name('home');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase-orders');
    Route::get('/purchase-orders/create', [PurchaseOrderController::class, 'create'])->name('purchase-orders.create');
    Route::get('/purchase-orders/list', [PurchaseOrderController::class, 'getListForDatatables'])->name('purchase-orders.datatables');
    
    Route::get('/purchase-orders/{order_number}', [PurchaseOrderController::class, 'show'])->name('purchase-orders.show');
    Route::get('/purchase-orders/{order_number}/cart', [PurchaseOrderController::class, 'cart'])->name('purchase-orders.cart');
    Route::get('/purchase-orders/{id}/edit', [PurchaseOrderController::class, 'edit'])->name('purchase-orders.edit');
    Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
    Route::put('/purchase-orders/{id}/ordered', [PurchaseOrderController::class, 'ordered'])->name('purchase-orders.ordered');
    Route::put('/purchase-orders/{id}/confirmed', [PurchaseOrderController::class, 'confirmed'])->name('purchase-orders.confirmed');
    Route::put('/purchase-orders/{id}/shipped', [PurchaseOrderController::class, 'shipped'])->name('purchase-orders.shipped');
    Route::put('/purchase-orders/{id}', [PurchaseOrderController::class, 'update'])->name('purchase-orders.update');
    Route::delete('/purchase-orders/{id}', [PurchaseOrderController::class, 'destroy'])->name('purchase-orders.delete');

    Route::get('/purchase-orders-items', [PurchaseOrderItemController::class, 'index'])->name('purchase-orders-items');
    Route::get('/purchase-orders-items/create', [PurchaseOrderItemController::class, 'create'])->name('purchase-orders-items.create');
    Route::get('/purchase-orders-items/list', [PurchaseOrderItemController::class, 'getListForDatatables'])->name('purchase-orders-items.datatables');
    Route::get('/purchase-orders-items/{id}', [PurchaseOrderItemController::class, 'show'])->name('purchase-orders-items.show');
    Route::get('/purchase-orders-items/{id}/edit', [PurchaseOrderItemController::class, 'edit'])->name('purchase-orders-items.edit');
    Route::post('/purchase-orders-items', [PurchaseOrderItemController::class, 'store'])->name('purchase-orders-items.store');
    Route::put('/purchase-orders-items/{id}', [PurchaseOrderItemController::class, 'update'])->name('purchase-orders-items.update');
    Route::delete('/purchase-orders-items/{id}', [PurchaseOrderItemController::class, 'destroy'])->name('purchase-orders-items.delete');

    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
    Route::get('/inventory/list', [InventoryController::class, 'getListForDatatables'])->name('inventory.datatables');

    Route::get('/products', [ProductController::class, 'index'])->name('products');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::get('/products/list', [ProductController::class, 'getListForDatatables'])->name('products.datatables');
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.delete');
    
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers');
    Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
    Route::get('/suppliers/list', [SupplierController::class, 'getListForDatatables'])->name('suppliers.datatables');
    Route::get('/suppliers/{id}', [SupplierController::class, 'show'])->name('suppliers.show');
    Route::get('/suppliers/{id}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::put('/suppliers/{id}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy'])->name('suppliers.delete');

    Route::get('/dealers', [DealerController::class, 'index'])->name('dealers');
    Route::get('/dealers/create', [DealerController::class, 'create'])->name('dealers.create');
    Route::get('/dealers/list', [DealerController::class, 'getListForDatatables'])->name('dealers.datatables');
    Route::get('/dealers/{id}', [DealerController::class, 'show'])->name('dealers.show');
    Route::get('/dealers/{id}/edit', [DealerController::class, 'edit'])->name('dealers.edit');
    Route::post('/dealers', [DealerController::class, 'store'])->name('dealers.store');
    Route::put('/dealers/{id}', [DealerController::class, 'update'])->name('dealers.update');
    Route::delete('/dealers/{id}', [DealerController::class, 'destroy'])->name('dealers.delete');

    Route::get('/warehouses', [WarehouseController::class, 'index'])->name('warehouses');
    Route::get('/warehouses/create', [WarehouseController::class, 'create'])->name('warehouses.create');
    Route::get('/warehouses/list', [WarehouseController::class, 'getListForDatatables'])->name('warehouses.datatables');
    Route::get('/warehouses/{id}/edit', [WarehouseController::class, 'edit'])->name('warehouses.edit');
    Route::post('/warehouses', [WarehouseController::class, 'store'])->name('warehouses.store');
    Route::put('/warehouses/{id}', [WarehouseController::class, 'update'])->name('warehouses.update');
    Route::delete('/warehouses/{id}', [WarehouseController::class, 'destroy'])->name('warehouses.delete');

    Route::get('/taxes', [TaxController::class, 'index'])->name('taxes');
    Route::get('/taxes/create', [TaxController::class, 'create'])->name('taxes.create');
    Route::get('/taxes/list', [TaxController::class, 'getListForDatatables'])->name('taxes.datatables');
    Route::get('/taxes/{id}/edit', [TaxController::class, 'edit'])->name('taxes.edit');
    Route::post('/taxes', [TaxController::class, 'store'])->name('taxes.store');
    Route::put('/taxes/{id}', [TaxController::class, 'update'])->name('taxes.update');
    Route::delete('/taxes/{id}', [TaxController::class, 'destroy'])->name('taxes.delete');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::get('/categories/list', [CategoryController::class, 'getListForDatatables'])->name('categories.datatables');
    Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.delete');
    
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::get('/users/list', [UserController::class, 'getListForDatatables'])->name('users.datatables');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::get('/users/{id}/enable', [UserController::class, 'enable'])->name('users.enable');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.delete');

    Route::get('/profile', [UserController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [UserController::class, 'editProfile'])->name('profile-edit');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');


    Route::prefix('ajax')->group(function () {
        Route::get('/states', [StateController::class, 'getListForSelect2'])->name('ajax.states');
        Route::get('/categories', [CategoryController::class, 'getListForSelect2'])->name('ajax.categories');
        Route::get('/suppliers', [SupplierController::class, 'getListForSelect2'])->name('ajax.suppliers');
        Route::get('/warehouses', [WarehouseController::class, 'getListForSelect2'])->name('ajax.warehouses');
        Route::get('/taxes', [TaxController::class, 'getListForSelect2'])->name('ajax.taxes');
        Route::get('/roles', [RoleController::class, 'getListForSelect2'])->name('ajax.roles');
        Route::get('/products/{supplier?}', [ProductController::class, 'getListForSelect2'])->name('ajax.products');
        Route::get('/product/{id}', [ProductController::class, 'getById'])->name('product.json');

    });

    Route::prefix('export')->group(function () {
        Route::get('/products', [ProductController::class, 'getExportList'])->name('export.products');
    });

    Route::get('/permissions/add', [PermissionController::class, 'store'])->name('permissions.add');
    
});