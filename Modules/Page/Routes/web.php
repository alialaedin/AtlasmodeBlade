<?php

use Illuminate\Support\Facades\Route;
use Modules\Page\Http\Controllers\Admin\PageController;

Route::webSuperGroup('admin', function () {
  Route::prefix('/pages')->name('pages.')->group(function () {
    Route::get('/', [PageController::class, 'index'])->name('index');
    Route::get('/create', [PageController::class, 'create'])->name('create');
    Route::post('/', [PageController::class, 'store'])->name('store');
    Route::get('/{page}/edit', [PageController::class, 'edit'])->name('edit');
    Route::patch('/{page}', [PageController::class, 'update'])->name('update');
    Route::delete('/{page}', [PageController::class, 'destroy'])->name('destroy');
  });
});
