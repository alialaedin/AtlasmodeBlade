<?php

use Illuminate\Support\Facades\Route;
use Modules\Shipping\Http\Controllers\Admin\ShippingController;

Route::webSuperGroup('admin', function () {
  Route::prefix('/shippings')->name('shippings.')->group(function () {

    Route::get('/{shipping}/assign-cities', [ShippingController::class, 'cities'])->name('cities');
    Route::post('/{shipping}/assign-cities', [ShippingController::class, 'assignCities'])->name('assign-cities');

    Route::get('/', [ShippingController::class, 'index'])->name('index');
    Route::get('/create', [ShippingController::class, 'create'])->name('create');
    Route::post('/', [ShippingController::class, 'store'])->name('store');
    Route::get('/{shipping}/edit', [ShippingController::class, 'edit'])->name('edit');
    Route::put('/{shipping}', [ShippingController::class, 'update'])->name('update');
    Route::delete('/{shipping}', [ShippingController::class, 'destroy'])->name('destroy');
    Route::get('/{shipping}', [ShippingController::class, 'show'])->name('show');
    Route::patch('shippings/sort', [ShippingController::class, 'sort'])->name('sort');
  });

});
