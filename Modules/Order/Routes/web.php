<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Controllers\Admin\OrderController as AdminOrderController;
use Modules\Order\Http\Controllers\Admin\OrderItemController;
use Modules\Order\Http\Controllers\Customer\OrderController as CustomerOrderController;
use Modules\Order\Http\Controllers\Admin\ShippingExcelController;

Route::webSuperGroup('admin', function () {

  Route::prefix('/shipping-excels')->name('shipping-excels.')->group(function () {
    Route::delete('/multiple-delete', [ShippingExcelController::class, 'multipleDelete'])->name('multiple-delete');
    Route::get('/', [ShippingExcelController::class, 'index'])->name('index');
    Route::post('/', [ShippingExcelController::class, 'store'])->name('store');
    Route::delete('/{shipping_excel}', [ShippingExcelController::class, 'destroy'])->name('destroy');
  });

  Route::prefix('/orders')->name('orders.')->group(function () {

    Route::get('/print', [AdminOrderController::class, 'print'])->name('print')->hasPermission('read_order');
    Route::put('/{order}/update-status', [AdminOrderController::class,'updateStatus'])->name('update-status')->hasPermission('modify_order');
    Route::post('/status/changes', [AdminOrderController::class,'changeStatusSelectedOrders'])->name('changeStatusSelectedOrders')->hasPermission('modify_order');
    Route::post('/get-shippings', [AdminOrderController::class,'getShippableShippings'])->name('shippable-shippings')->hasPermission('write_order');

    // Route::post('/{order}/items', [AdminOrderController::class, 'addItem'])->name('add-item')->hasPermission('write_order');
    // Route::patch('/items/{orderItem}/status', [AdminOrderController::class, 'updateItemStatus'])->name('update-item-status')->hasPermission('modify_order');
    // Route::put('/items/{orderItem}', [AdminOrderController::class, 'updateQuantityItem'])->name('update-item-quantity')->hasPermission('modify_order');
    // Route::delete('/{orderItem}', [AdminOrderController::class, 'deleteItem'])->name('delete-item')->hasPermission('modify_order');

    Route::get('/', [AdminOrderController::class, 'index'])->name('index')->hasPermission('read_order');
    Route::get('/create', [AdminOrderController::class, 'create'])->name('create')->hasPermission('write_order');
    Route::get('/{order}', [AdminOrderController::class, 'show'])->name('show')->hasPermission('read_order');
    Route::post('/', [AdminOrderController::class, 'store'])->name('store')->hasPermission('write_order');
    Route::put('/{order}', [AdminOrderController::class, 'update'])->name('update')->hasPermission('modify_order');

  });

  Route::prefix('/order-items')->name('order-items.')->group(function () {
    Route::post('/{order}', [OrderItemController::class, 'addItem'])->name('store')->hasPermission('write_order');
    Route::patch('{orderItem}/update-status', [OrderItemController::class, 'updateStatus'])->name('update-status')->hasPermission('modify_order');
    Route::patch('/{orderItem}/update-quantity', [OrderItemController::class, 'updateQuantity'])->name('update-quantity')->hasPermission('modify_order');
    Route::delete('/{orderItem}', [OrderItemController::class, 'destroy'])->name('destroy')->hasPermission('modify_order');
  });

});

Route::middleware('auth:customer')->name('customer.')->group(function () {
  Route::prefix('/orders')->name('orders.')->group(function () {
    Route::post('/', [CustomerOrderController::class, 'store'])->name('store');
  });
});

