<?php

use Illuminate\Support\Facades\Route;
use Modules\SizeChart\Http\Controllers\Admin\SizeChartTypeController;

Route::webSuperGroup('admin', function () {
  Route::prefix('size-chart-types')->name('size-chart-types.')->group(function () {
    Route::get('/', [SizeChartTypeController::class, 'index'])->name('index')->hasPermission('read_size_chart');
    Route::post('/', [SizeChartTypeController::class, 'store'])->name('store')->hasPermission('write_size_chart');
    Route::put('/{size_chart_type}', [SizeChartTypeController::class, 'update'])->name('update')->hasPermission('modify_size_chart');
    Route::delete('/{size_chart_type}', [SizeChartTypeController::class, 'destroy'])->name('destroy')->hasPermission('delete_size_chart');
  });
});