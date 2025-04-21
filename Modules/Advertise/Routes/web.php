<?php

use Illuminate\Support\Facades\Route;
use Modules\Advertise\Http\Controllers\Admin\AdvertiseController;

Route::webSuperGroup('admin', function () {

  Route::prefix('/advertisements')->name('advertisements.')->group(function () {
    Route::get('/', [AdvertiseController::class, 'index'])->name('index');
    Route::patch('/{advertise}/status', [AdvertiseController::class, 'changeStatus'])->name('change-status');
    Route::get('/{advertise}/edit', [AdvertiseController::class, 'edit'])->name('edit');
    Route::put('/{advertise}', [AdvertiseController::class, 'update'])->name('update');
  });

  // Route::patch('positions/{position}/update_possibility', [AdvertiseController::class, 'updatePossibility'])
  //   ->name('advertisements.update_possibility');
  // Route::get('positions/{position}/update_possibility', [AdvertiseController::class, 'editPossibility'])
  //   ->name('advertisements.edit_possibility');

  // Route::resource('advertise',  'AdvertiseController');
  // Route::resource('positions', 'PositionAdvertiseController');
});
