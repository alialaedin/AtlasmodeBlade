<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\Admin\AdminController;
use Modules\Admin\Http\Controllers\Admin\DashboardController;

Route::webSuperGroup('admin', function () {
  Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

  Route::prefix('/admins')->name('admins.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/{admin}', [AdminController::class, 'show'])->name('show');
    Route::get('/create', [AdminController::class, 'create'])->name('create');
    Route::post('/', [AdminController::class, 'store'])->name('store');
    Route::get('/{admin}/edit', [AdminController::class, 'edit'])->name('edit');
    Route::put('/{admin}', [AdminController::class, 'update'])->name('update');
    Route::delete('/{admin}', [AdminController::class, 'destroy'])->name('destroy');
  });
});
