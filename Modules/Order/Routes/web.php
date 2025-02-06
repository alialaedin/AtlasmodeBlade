<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Controllers\Admin\OrderController;
use Modules\Order\Http\Controllers\Admin\ShippingExcelController;

Route::webSuperGroup('admin', function () {

  Route::prefix('/shipping-excels')->name('shipping-excels.')->group(function () {
    Route::delete('/multiple-delete', [ShippingExcelController::class, 'multipleDelete'])->name('multiple-delete');
    Route::get('/', [ShippingExcelController::class, 'index'])->name('index');
    Route::post('/', [ShippingExcelController::class, 'store'])->name('store');
    Route::delete('/{shipping_excel}', [ShippingExcelController::class, 'destroy'])->name('destroy');
  });

  Route::prefix('/orders')->name('orders.')->group(function () {

    Route::get('/print', [OrderController::class, 'print'])->name('print')->hasPermission('read_order');
    Route::put('/{order}/update-status', [OrderController::class,'updateStatus'])->name('update-status')->hasPermission('modify_order');
    Route::post('/status/changes', [OrderController::class,'changeStatusSelectedOrders'])->name('changeStatusSelectedOrders')->hasPermission('modify_order');
    Route::post('/get-shippings', [OrderController::class,'getShippableShippings'])->name('shippable-shippings')->hasPermission('write_order');

    Route::post('/{order}/items', [OrderController::class, 'addItem'])->name('add-item')->hasPermission('write_order');
    Route::put('/items/{order_item}', [OrderController::class, 'updateQuantityItem'])->name('update-item-quantity')->hasPermission('modify_order');
    Route::put('/items/{order_item}/status', [OrderController::class, 'updateItemStatus'])->name('update-item-status')->hasPermission('modify_order');

    Route::get('/', [OrderController::class, 'index'])->name('index')->hasPermission('read_order');
    Route::get('/create', [OrderController::class, 'create'])->name('create')->hasPermission('write_order');
    Route::get('/{order}', [OrderController::class, 'show'])->name('show')->hasPermission('read_order');
    Route::post('/', [OrderController::class, 'store'])->name('store')->hasPermission('write_order');
    Route::put('/{order}', [OrderController::class, 'update'])->name('update')->hasPermission('modify_order');

  });

});

