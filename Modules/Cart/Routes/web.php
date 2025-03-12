<?php

use Illuminate\Support\Facades\Route;
use Modules\Cart\Http\Controllers\Customer\CartController;

Route::middleware('auth:customer')->name('customer.')->group(function() {
  Route::prefix('/cart')->name('carts.')->group(function () {
    Route::post('/get-shippings', [CartController::class,'getShippableShippings'])->name('shippable-shippings');
    Route::post('check-free-shipping', [CartController::class, 'checkFreeShipping']);
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::delete('/{cart}', [CartController::class, 'remove'])->name('remove');
    Route::put('/{cart}', [CartController::class, 'update'])->name('update');
  });
});