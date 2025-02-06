<?php

use Illuminate\Support\Facades\Route;
use Modules\GiftPackage\Http\Controllers\Admin\GiftPackageController;

Route::webSuperGroup('admin', function () {
  Route::prefix('/gift-packages')->name('gift-packages.')->group(function () {
    Route::get('/', [GiftPackageController::class, 'index'])->name('index')->hasPermission('read_gift_package');
    Route::post('/', [GiftPackageController::class, 'store'])->name('store')->hasPermission('write_gift_package');
    Route::put('/{giftPackage}', [GiftPackageController::class, 'update'])->name('update')->hasPermission('modify_gift_package');
    Route::delete('/{giftPackage}', [GiftPackageController::class, 'destroy'])->name('destroy')->hasPermission('delete_gift_package');
  });
});