<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\RoleController;




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
    Route::put('/suppliers/{id}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy'])->name('suppliers.delete');
    Route::get('/suppliers/list', [SupplierController::class, 'getListForDatatables'])->name('suppliers.datatables');

    Route::get('/taxes', [TaxController::class, 'index'])->name('taxes');
    Route::get('/taxes/{id}/edit', [TaxController::class, 'edit'])->name('taxes.edit');
    Route::get('/taxes/create', [TaxController::class, 'create'])->name('taxes.create');
    Route::post('/taxes', [TaxController::class, 'store'])->name('taxes.store');
    Route::put('/taxes/{id}', [TaxController::class, 'update'])->name('taxes.update');
    Route::delete('/taxes/{id}', [TaxController::class, 'destroy'])->name('taxes.delete');
    Route::get('/taxes/list', [TaxController::class, 'getListForDatatables'])->name('taxes.datatables');

    Route::get('/products', [ProductController::class, 'index'])->name('products');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.delete');
    Route::get('/products/list', [ProductController::class, 'getListForDatatables'])->name('products.datatables');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
    Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.delete');
    Route::get('/categories/list', [CategoryController::class, 'getListForDatatables'])->name('categories.datatables');


    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.delete');
    Route::get('/users/list', [UserController::class, 'getListForDatatables'])->name('users.datatables');


    Route::prefix('ajax')->group(function () {
        Route::get('/states', [StateController::class, 'getListForSelect2'])->name('ajax.states');
        Route::get('/roles', [RoleController::class, 'getListForSelect2'])->name('ajax.roles');
    });
});