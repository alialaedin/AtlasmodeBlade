<?php

use Illuminate\Support\Facades\Route;
use Modules\Setting\Http\Controllers\Admin\SettingController;

Route::webSuperGroup('admin', function () {
  Route::prefix('/settings')->name('settings.')->group(function () {
    Route::delete('/{setting}/file', [SettingController::class, 'destroyFile'])->name('destroy-file');
    Route::get('/{group_name}', [SettingController::class, 'show'])->name('show');
    Route::put('/', [SettingController::class, 'update'])->name('update');
  });
});
