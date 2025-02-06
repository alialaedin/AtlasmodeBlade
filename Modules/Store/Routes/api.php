<?php

use Illuminate\Support\Facades\Route;
use Modules\Store\Http\Controllers\Admin\StoreTransactionController;
use Modules\Store\Http\Controllers\Admin\StoreController as AdminStoreController;
use Modules\Store\Http\Controllers\Admin\VarietyTransferController;
Route::superGroup('admin', function() {
    Route::name('store_transactions')
        ->get('store_transactions' , 'StoreTransactionController@index')
    ->hasPermission('read_store');
    Route::get('store/store-wealth-report',[AdminStoreController::class,'storeWealthReport']);

    Route::get('/store-excel',[StoreTransactionController::class,'storeExcel']);


    Route::get('store/balance_report',[AdminStoreController::class,'storeBalanceReport'])->hasPermission('read_store');

    Route::prefix('store_transactions/statuses')->group(function(){
        Route::get('/pending_list',[StoreTransactionController::class,'pending_list']);
        Route::post('/mark-as-done',[StoreTransactionController::class,'markAsDoneBatch']);
        Route::put('/{transaction}/mark-as-done',[StoreTransactionController::class,'markAsDone']);

    });

    Route::permissionResource('stores' , 'StoreController', ['only' => ['index', 'show', 'store']]);

    Route::prefix('varietyTransfers')->name('varietyTransfers.')->group(function (){
        Route::get('/', [VarietyTransferController::class, 'index'])->name('index')->hasPermission('read_store');
        Route::get('/create', [VarietyTransferController::class, 'create'])->name('create')->hasPermission('write_store');
        Route::get('/{id}', [VarietyTransferController::class, 'show'])->name('show')->hasPermission('read_store');
        Route::post('/', [VarietyTransferController::class, 'store'])->name('store')->hasPermission('write_store');
        Route::delete('/{id}', [VarietyTransferController::class, 'destroy'])->name('destroy')->hasPermission('write_store');
    });

    Route::apiResource('varietyTransferLocations', 'VarietyTransferLocationController')->only(['index', 'store', 'update', 'destroy']);
    Route::get('varietyTransferReport', [VarietyTransferController::class, 'report'])->hasPermission('read_store');
});


