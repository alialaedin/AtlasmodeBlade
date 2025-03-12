<?php

use Illuminate\Support\Facades\Route;
use Modules\Coupon\Http\Controllers\Admin\CouponController as AdminCouponController;
use Modules\Coupon\Http\Controllers\Customer\CouponController as CustomerCouponController;

Route::webSuperGroup('admin', function () {
  Route::prefix('/coupons')->name('coupons.')->group(function () {
    Route::get('/', [AdminCouponController::class, 'index'])->name('index')->middleware('permission:read_coupon');
    Route::get('/create', [AdminCouponController::class, 'create'])->name('create')->middleware('permission:write_coupon');
    Route::get('/{coupon}', [AdminCouponController::class, 'show'])->name('show')->middleware('permission:read_coupon');
    Route::post('/', [AdminCouponController::class, 'store'])->name('store')->middleware('permission:write_coupon');
    Route::get('/{coupon}/edit', [AdminCouponController::class, 'edit'])->name('edit')->middleware('permission:modify_coupon');
    Route::put('/{coupon}', [AdminCouponController::class, 'update'])->name('update')->middleware('permission:modify_coupon');
    Route::delete('/{coupon}', [AdminCouponController::class, 'destroy'])->name('destroy')->middleware('permission:delete_coupon');
  });
});

Route::superGroup('customer', function () {
  Route::post('/coupon/verify', [CustomerCouponController::class, 'verify'])->name('coupon_verify');
});
