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
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SaleOrderController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\FreightZoneController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\SaleOrderItemController;
use App\Http\Controllers\SaleOrderPaymentController;
use App\Http\Controllers\InventoryMovementController;
use App\Http\Controllers\PurchaseOrderItemController;
use App\Http\Controllers\PurchaseOrderInvoiceController;
use App\Http\Controllers\PurchaseOrderInvoiceItemController;


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

    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');




    Route::get('/sale-orders', [SaleOrderController::class, 'index'])->name('sale-orders');
    Route::get('/sale-orders/create', [SaleOrderController::class, 'create'])->name('sale-orders.create');
    Route::get('/sale-orders/list', [SaleOrderController::class, 'getListForDatatables'])->name('sale-orders.datatables');
    Route::get('/sale-orders/report', [SaleOrderController::class, 'report'])->name('sale-orders.report');
    Route::get('/sale-orders/{order_number}', [SaleOrderController::class, 'show'])->name('sale-orders.show');
    Route::get('/sale-orders/{order_number}/cart', [SaleOrderController::class, 'cart'])->name('sale-orders.cart');
    // Route::get('/sale-orders/{id}/edit', [SaleOrderController::class, 'edit'])->name('sale-orders.edit');
    Route::get('/sale-orders/{order_number_slug}/proforma/pdf', [SaleOrderController::class, 'exportProformaToPdf'])->name('sale-orders.proforma-pdf');
    Route::get('/sale-orders/{order_number_slug}/proforma/', [SaleOrderController::class, 'proforma'])->name('sale-orders.proforma');
    Route::post('/sale-orders', [SaleOrderController::class, 'store'])->name('sale-orders.store');
    Route::put('/sale-orders/{id}/blocked', [SaleOrderController::class, 'blocked'])->name('sale-orders.blocked');
    Route::put('/sale-orders/{id}/booked', [SaleOrderController::class, 'booked'])->name('sale-orders.booked');
    Route::put('/sale-orders/{id}/dispatched', [SaleOrderController::class, 'dispatched'])->name('sale-orders.dispatched');
    Route::put('/sale-orders/{id}/delivered', [SaleOrderController::class, 'delivered'])->name('sale-orders.delivered');
    Route::put('/sale-orders/{id}', [SaleOrderController::class, 'update'])->name('sale-orders.update');
    Route::delete('/sale-orders/{id}', [SaleOrderController::class, 'destroy'])->name('sale-orders.delete');


    Route::get('/sale-order-payments', [SaleOrderPaymentController::class, 'index'])->name('sale-order-payments');
    Route::get('/sale-order-payments/create', [SaleOrderPaymentController::class, 'create'])->name('sale-order-payments.create');
    Route::get('/sale-order-payments/list', [SaleOrderPaymentController::class, 'getListForDatatables'])->name('sale-order-payments.datatables');
    Route::get('/sale-order-payments/{order_number}', [SaleOrderPaymentController::class, 'show'])->name('sale-order-payments.show');
    Route::post('/sale-order-payments', [SaleOrderPaymentController::class, 'store'])->name('sale-order-payments.store');
    Route::put('/sale-order-payments/{id}', [SaleOrderPaymentController::class, 'update'])->name('sale-order-payments.update');
    Route::delete('/sale-order-payments/{id}', [SaleOrderPaymentController::class, 'destroy'])->name('sale-order-payments.delete');


    // Route::get('/sale-orders-items', [SaleOrderItemController::class, 'index'])->name('sale-orders-items');
    // Route::get('/sale-orders-items/create', [SaleOrderItemController::class, 'create'])->name('sale-orders-items.create');
    Route::get('/sale-orders-items/list', [SaleOrderItemController::class, 'getListForDatatables'])->name('sale-orders-items.datatables');
    // Route::get('/sale-orders-items/{id}', [SaleOrderItemController::class, 'show'])->name('sale-orders-items.show');
    // Route::get('/sale-orders-items/{id}/edit', [SaleOrderItemController::class, 'edit'])->name('sale-orders-items.edit');
    Route::post('/sale-orders-items', [SaleOrderItemController::class, 'store'])->name('sale-orders-items.store');
    Route::put('/sale-orders-items/{id}', [SaleOrderItemController::class, 'update'])->name('sale-orders-items.update');
    Route::delete('/sale-orders-items/{id}', [SaleOrderItemController::class, 'destroy'])->name('sale-orders-items.delete');
    Route::get('/sales-by-category/{range}', [SaleOrderItemController::class, 'getNumberAndTotalSaleByRange'])->name('sale-orders-items.sales-by-category');



    Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase-orders');
    Route::get('/purchase-orders/create', [PurchaseOrderController::class, 'create'])->name('purchase-orders.create');
    Route::get('/purchase-orders/list', [PurchaseOrderController::class, 'getListForDatatables'])->name('purchase-orders.datatables');
    Route::get('/purchase-orders/filter/{status}', [PurchaseOrderController::class, 'filter'])->name('purchase-orders.filtered');
    Route::get('/purchase-orders/{order_number}', [PurchaseOrderController::class, 'show'])->name('purchase-orders.show');
    Route::get('/purchase-orders/{order_number}/cart', [PurchaseOrderController::class, 'cart'])->name('purchase-orders.cart');
    Route::get('/purchase-orders/{order_number}/invoice/pdf', [PurchaseOrderController::class, 'exportInvoiceToPdf'])->name('purchase-orders.invoice-pdf');
    Route::get('/purchase-orders/{order_number}/invoice/', [PurchaseOrderController::class, 'invoice'])->name('purchase-orders.invoice');
    Route::get('/purchase-orders/{id}/edit', [PurchaseOrderController::class, 'edit'])->name('purchase-orders.edit');
    Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
    Route::put('/purchase-orders/{id}/ordered', [PurchaseOrderController::class, 'ordered'])->name('purchase-orders.ordered');
    Route::put('/purchase-orders/{id}/confirmed', [PurchaseOrderController::class, 'confirmed'])->name('purchase-orders.confirmed');
    // Route::put('/purchase-orders/{id}/shipped', [PurchaseOrderController::class, 'shipped'])->name('purchase-orders.shipped');
    // Route::put('/purchase-orders/{id}/customs', [PurchaseOrderController::class, 'customs'])->name('purchase-orders.customs');
    // Route::put('/purchase-orders/{id}/cleared', [PurchaseOrderController::class, 'cleared'])->name('purchase-orders.cleared');
    Route::put('/purchase-orders/{id}/received', [PurchaseOrderController::class, 'received'])->name('purchase-orders.received');
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


    Route::get('/purchase-order-invoices', [PurchaseOrderInvoiceController::class, 'index'])->name('purchase-order-invoices');
    Route::get('/purchase-order-invoices/list', [PurchaseOrderInvoiceController::class, 'getListForDatatables'])->name('purchase-order-invoices.datatables');
    Route::get('/purchase-order-invoices/{invoice_number_slug}', [PurchaseOrderInvoiceController::class, 'show'])->name('purchase-order-invoices.show');
    Route::get('/purchase-order-invoices/{invoice_number_slug}/proforma/pdf', [PurchaseOrderInvoiceController::class, 'exportProformaToPdf'])->name('purchase-order-invoices.proforma-pdf');
    Route::get('/purchase-order-invoices/{invoice_number_slug}/proforma/', [PurchaseOrderInvoiceController::class, 'proforma'])->name('purchase-order-invoices.proforma');
    Route::post('/purchase-order-invoices', [PurchaseOrderInvoiceController::class, 'store'])->name('purchase-order-invoices.store');
    Route::put('/purchase-order-invoices/{id}/customs', [PurchaseOrderInvoiceController::class, 'customs'])->name('purchase-order-invoices.customs');
    Route::put('/purchase-order-invoices/{id}/cleared', [PurchaseOrderInvoiceController::class, 'cleared'])->name('purchase-order-invoices.cleared');
    Route::put('/purchase-order-invoices/{id}/received', [PurchaseOrderInvoiceController::class, 'received'])->name('purchase-order-invoices.received');
    Route::put('/purchase-order-invoices/{id}/paid', [PurchaseOrderInvoiceController::class, 'paid'])->name('purchase-order-invoices.paid');
    Route::put('/purchase-order-invoices/{id}', [PurchaseOrderInvoiceController::class, 'update'])->name('purchase-order-invoices.update');    

    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
    Route::get('/inventory/resetOrderedStock', [InventoryController::class, 'resetOrderedStock'])->name('inventory.resetOrdered');
    Route::get('/inventory/list', [InventoryController::class, 'getListForDatatables'])->name('inventory.datatables');

    Route::get('/inventory-movement', [InventoryMovementController::class, 'index'])->name('inventory-movement');
    Route::get('/inventory-movement/list', [InventoryMovementController::class, 'getListForDatatables'])->name('inventory-movement.datatables');
    Route::get('/inventory-movement/{id}', [InventoryMovementController::class, 'show'])->name('inventory-movement.show');

    Route::get('/products', [ProductController::class, 'index'])->name('products');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::get('/products/list', [ProductController::class, 'getListForDatatables'])->name('products.datatables');
    Route::get('/products/import', [ProductController::class, 'importCsv'])->name('products.import');
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
    Route::get('/suppliers/{id}/stats', [SupplierController::class, 'stats'])->name('suppliers.stats');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::put('/suppliers/{id}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy'])->name('suppliers.delete');

    Route::get('/dealers', [DealerController::class, 'index'])->name('dealers');
    Route::get('/dealers/create', [DealerController::class, 'create'])->name('dealers.create');
    Route::get('/dealers/list', [DealerController::class, 'getListForDatatables'])->name('dealers.datatables');
    Route::get('/dealers/ledger', [DealerController::class, 'ledger'])->name('dealers.ledger');
    Route::get('/dealers/ledger-summary', [DealerController::class, 'ledgerSummary'])->name('dealers.ledger-summary');
    Route::get('/dealers/{id}', [DealerController::class, 'show'])->name('dealers.show');
    Route::get('/dealers/{id}/edit', [DealerController::class, 'edit'])->name('dealers.edit');
    Route::post('/dealers', [DealerController::class, 'store'])->name('dealers.store');
    Route::put('/dealers/{id}', [DealerController::class, 'update'])->name('dealers.update');
    Route::delete('/dealers/{id}', [DealerController::class, 'destroy'])->name('dealers.delete');

    Route::get('/warehouses', [WarehouseController::class, 'index'])->name('warehouses');
    Route::get('/warehouses/create', [WarehouseController::class, 'create'])->name('warehouses.create');
    Route::get('/warehouses/list', [WarehouseController::class, 'getListForDatatables'])->name('warehouses.datatables');
    Route::get('/warehouses/{id}', [WarehouseController::class, 'show'])->name('warehouses.show');
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
    

    Route::get('/freight-zones', [FreightZoneController::class, 'index'])->name('freight-zones');
    Route::get('/freight-zones/create', [FreightZoneController::class, 'create'])->name('freight-zones.create');
    Route::get('/freight-zones/list', [FreightZoneController::class, 'getListForDatatables'])->name('freight-zones.datatables');
    Route::get('/freight-zones/{id}', [FreightZoneController::class, 'show'])->name('freight-zones.show');
    Route::get('/freight-zones/{id}/edit', [FreightZoneController::class, 'edit'])->name('freight-zones.edit');
    Route::post('/freight-zones', [FreightZoneController::class, 'store'])->name('freight-zones.store');
    Route::put('/freight-zones/{id}', [FreightZoneController::class, 'update'])->name('freight-zones.update');
    Route::delete('/freight-zones/{id}', [FreightZoneController::class, 'destroy'])->name('freight-zones.delete');

    Route::get('/states', [StateController::class, 'index'])->name('states');
    Route::get('/states/create', [StateController::class, 'create'])->name('states.create');
    Route::get('/states/list', [StateController::class, 'getListForDatatables'])->name('states.datatables');
    Route::get('/states/{id}', [StateController::class, 'show'])->name('states.show');
    Route::get('/states/{id}/edit', [StateController::class, 'edit'])->name('states.edit');
    Route::post('/states', [StateController::class, 'store'])->name('states.store');
    Route::put('/states/{id}', [StateController::class, 'update'])->name('states.update');
    Route::delete('/states/{id}', [StateController::class, 'destroy'])->name('states.delete');

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
        Route::get('/category', [CategoryController::class, 'getCategory'])->name('ajax.category');
        Route::get('/suppliers', [SupplierController::class, 'getListForSelect2'])->name('ajax.suppliers');
        Route::get('/dealers', [DealerController::class, 'getListForSelect2'])->name('ajax.dealers');
        Route::get('/dealers-ledger', [DealerController::class, 'getListForLedger'])->name('ajax.dealers-ledger');
        Route::get('/warehouses', [WarehouseController::class, 'getListForSelect2'])->name('ajax.warehouses');
        Route::get('/taxes', [TaxController::class, 'getListForSelect2'])->name('ajax.taxes');
        Route::get('/roles', [RoleController::class, 'getListForSelect2'])->name('ajax.roles');
        Route::get('/products', [ProductController::class, 'getListForSelect2'])->name('ajax.products');
        Route::get('/freight-zones', [FreightZoneController::class, 'getListForSelect2'])->name('ajax.freightzones');
        Route::get('/products/supplier/{id}', [ProductController::class, 'getListPerSupplier'])->name('ajax.products.supplier');
        Route::get('/products/warehouse/{id}', [ProductController::class, 'getListPerWarehouse'])->name('ajax.products.warehouse');
        Route::get('/product/{id}/{warehouse_id?}', [ProductController::class, 'getById'])->name('product.json');
        Route::get('/sale-orders', [SaleOrderController::class, 'getListForReport'])->name('ajax.sales-report');
    });

    Route::prefix('export')->group(function () {
        Route::get('/products', [ProductController::class, 'getExportList'])->name('export.products');
        Route::get('/inventory', [InventoryController::class, 'getExportList'])->name('export.inventory');
    });

    Route::get('/permissions/add', [PermissionController::class, 'store'])->name('permissions.add');
    
});