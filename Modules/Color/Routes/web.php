<?php

use Illuminate\Support\Facades\Route;
// use Modules\Color\Http\Controllers\Admin\ColorController;
use Modules\Color\Http\Controllers\Admin\ColorRangeController;

Route::webSuperGroup('admin', function () {
  
  // Route::prefix('/colors')->name('colors.')->group(function () {
  //   Route::get('/', [ColorController::class, 'index'])->name('index');
  //   Route::post('/', [ColorController::class, 'store'])->name('store');
  //   Route::patch('/{color}', [ColorController::class, 'update'])->name('update');
  //   Route::delete('/{color}', [ColorController::class, 'destroy'])->name('destroy');
  // });

  Route::prefix('/color-ranges')->name('color-ranges.')->group(function () {
    Route::patch('/sort', [ColorRangeController::class, 'sort'])->name('sort');
    Route::get('/', [ColorRangeController::class, 'index'])->name('index');
    Route::get('/create', [ColorRangeController::class, 'create'])->name('create');
    Route::post('/', [ColorRangeController::class, 'store'])->name('store');
    Route::get('/{colorRange}/edit', [ColorRangeController::class, 'edit'])->name('edit');
    Route::put('/{colorRange}', [ColorRangeController::class, 'update'])->name('update');
    Route::delete('/{colorRange}', [ColorRangeController::class, 'destroy'])->name('destroy');
  });

});
