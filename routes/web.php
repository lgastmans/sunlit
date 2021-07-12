<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;

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

Route::group(['middleware' => ['auth']], function () { 

    Route::get('/', function () {
        return view('welcome');
    });
    
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers');
    Route::get('/suppliers/{id}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::get('/suppliers/list', [SupplierController::class, 'getSuppliers'])->name('suppliers.list');

    Route::get('/taxes', [TaxController::class, 'index'])->name('taxes');
    Route::get('/taxes/{id}/edit', [TaxController::class, 'edit'])->name('taxes.edit');
    Route::get('/taxes/create', [TaxController::class, 'create'])->name('taxes.create');
    Route::post('/taxes', [TaxController::class, 'store'])->name('taxes.store');
    Route::get('/taxes/list', [TaxController::class, 'getTaxes'])->name('taxes.list');

    Route::get('/products', [ProductController::class, 'index'])->name('products');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/list', [ProductController::class, 'getProducts'])->name('products.list');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
    Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.delete');
    Route::get('/categories/list', [CategoryController::class, 'getCategories'])->name('categories.list');
});