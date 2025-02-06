<?php

use Illuminate\Support\Facades\Route;
use Modules\Attribute\Http\Controllers\Admin\AttributeController;

Route::webSuperGroup('admin', function () {
  Route::prefix('attributes')->name('attributes.')->group(function () {
    Route::get('/', [AttributeController::class,'index'])->name('index');
    Route::get('/create', [AttributeController::class,'create'])->name('create');
    Route::post('/', [AttributeController::class,'store'])->name('store');
    Route::get('/{attribute}/edit', [AttributeController::class,'edit'])->name('edit');
    Route::put('/{attribute}', [AttributeController::class,'update'])->name('update');
    Route::delete('/{attribute}', [AttributeController::class,'destroy'])->name('destroy');
  });
});