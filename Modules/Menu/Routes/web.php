<?php

use Illuminate\Support\Facades\Route;
use Modules\Menu\Http\Controllers\Admin\MenuItemController;

Route::webSuperGroup('admin', function () {
  Route::prefix('menus')->name('menus.')->group(function () {
    Route::get('/{menuGroup}', [MenuItemController::class, 'index'])->name('index');
    Route::get('/{menuGroup}/create', [MenuItemController::class, 'create'])->name('create');
    Route::post('/', [MenuItemController::class, 'store'])->name('store');
    Route::get('/{menuItem}/edit', [MenuItemController::class, 'edit'])->name('edit');
    Route::put('/{menuItem}', [MenuItemController::class, 'update'])->name('update');
    Route::delete('/{menuItem}', [MenuItemController::class, 'destroy'])->name('destroy');
    Route::patch('/sort', [MenuItemController::class, 'sort'])->name('sort');
  });
});
