<?php

use Illuminate\Support\Facades\Route;
use Modules\Store\Http\Controllers\Admin\StoreController;

Route::webSuperGroup('admin', function () {
  Route::prefix('/stores')->name('stores.')->middleware('permission:read_store')->group(function() {
    Route::get('/', [StoreController::class, 'index'])->name('index');
    Route::post('/', [StoreController::class, 'store'])->name('store');
    Route::get('/transactions', [StoreController::class, 'transactions'])->name('transactions');
  });
});
