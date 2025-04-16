<?php

use Illuminate\Support\Facades\Route;
use Modules\Slider\Http\Controllers\Admin\SliderController;

Route::webSuperGroup('admin', function () {
  Route::prefix('sliders')->name('sliders.')->group(function () {
    Route::get('/{group}', [SliderController::class, 'index'])->name('index');
    Route::get('/{group}/create', [SliderController::class, 'create'])->name('create');
    Route::post('/', [SliderController::class, 'store'])->name('store');
    Route::get('/{slider}/edit', [SliderController::class, 'edit'])->name('edit');
    Route::put('/{slider}', [SliderController::class, 'update'])->name('update');
    Route::delete('/{slider}', [SliderController::class, 'destroy'])->name('destroy');
    Route::patch('/sort/{group}', [SliderController::class, 'sort'])->name('sort');
  });
});
