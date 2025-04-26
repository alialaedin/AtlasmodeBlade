<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\Http\Controllers\Admin\CategoryController;

Route::webSuperGroup('admin', function () {
  Route::prefix('categories')->name('categories.')->group(function () {

    Route::patch('/sort', [CategoryController::class, 'sort'])->name('sort');
    Route::get('/create', [CategoryController::class, 'create'])->name('create');
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::post('/', [CategoryController::class, 'store'])->name('store');
    Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
    Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
    Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');

  });
});
