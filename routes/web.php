<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TaxController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

Route::group(['middleware' => ['auth']], function () { 
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers');
    Route::get('/suppliers/{id}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::get('/suppliers/list', [SupplierController::class, 'getSuppliers'])->name('suppliers.list');

    Route::get('/taxes', [TaxController::class, 'index'])->name('taxes');
    Route::get('/taxes/{id}/edit', [TaxController::class, 'edit'])->name('taxes.edit');
    Route::get('/taxes/create', [TaxController::class, 'create'])->name('taxes.create');
    Route::post('/taxes', [TaxController::class, 'store'])->name('taxes.store');
});