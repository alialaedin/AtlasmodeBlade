<?php

use Illuminate\Support\Facades\Route;
use Modules\Color\Http\Controllers\Admin\ColorController;

Route::webSuperGroup('admin', function () {
  Route::prefix('/colors')->name('colors.')->group(function () {
    Route::get('/', [ColorController::class, 'index'])->name('index');
    Route::post('/', [ColorController::class, 'store'])->name('store');
    Route::patch('/{color}', [ColorController::class, 'update'])->name('update');
    Route::delete('/{color}', [ColorController::class, 'destroy'])->name('destroy');
  });
});
