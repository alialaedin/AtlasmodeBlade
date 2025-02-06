<?php

use Illuminate\Support\Facades\Route;
use Modules\Customer\Http\Controllers\Admin\AddressController;
use Modules\Customer\Http\Controllers\Admin\CustomerController;
use Modules\Customer\Http\Controllers\Admin\CustomerRoleController;
use Modules\Customer\Http\Controllers\Customer\ProfileController;
use Modules\Customer\Http\Controllers\Admin\WithdrawController;

Route::webSuperGroup('admin', function () {

  Route::get('/get-cities', [AddressController::class, 'getCities'])->name('getCity');

	Route::prefix('/withdraws')->name('withdraws.')->group(function () {
		Route::get('/', [WithdrawController::class, 'index'])->name('index')->middleware('permission:read_withdraw');
		Route::put('/{withdraw}', [WithdrawController::class, 'update'])->name('update')->middleware('permission:modify_withdraw');
	});

	Route::get('/transactions', [CustomerController::class, 'transactionsWallet'])
    ->name('transactions.index')
    ->middleware('permission:read_transaction');

	Route::prefix('/customers')->name('customers.')->group(function () {

    Route::post('/withdraw', [CustomerController::class, 'withdrawCustomerWallet'])->name('withdraw');
    Route::post('/deposit', [CustomerController::class, 'depositCustomerWallet'])->name('deposit');

    Route::get('/search', [CustomerController::class, 'search'])->name('search');
    Route::get('/{customer}/addresses', [CustomerController::class,'getAddresses'])->name('addresses');

    Route::get('/', [CustomerController::class,'index'])->name('index')->middleware('permission:read_customer');
    Route::get('/create', [CustomerController::class,'create'])->name('create')->middleware('permission:write_customer');
    Route::post('/', [CustomerController::class,'store'])->name('store')->middleware('permission:write_customer');
    Route::get('/{customer}', [CustomerController::class,'show'])->name('show')->middleware('permission:read_customer');
    Route::get('/{customer}/edit', [CustomerController::class,'edit'])->name('edit')->middleware('permission:modify_customer');
    Route::put('/{id}', [CustomerController::class,'update'])->name('update')->middleware('permission:modify_customer');
    Route::delete('/{id}', [CustomerController::class,'destroy'])->name('destroy')->middleware('permission:delete_customer');

	});

  Route::prefix('/customer-roles')->name('customer-roles.')->group(function () {

    Route::get('/', [CustomerRoleController::class,'index'])->name('index')->middleware('permission:read_role');
    Route::post('/', [CustomerRoleController::class,'store'])->name('store')->middleware('permission:write_role');
    Route::put('/{customerRole}', [CustomerRoleController::class,'update'])->name('update')->middleware('permission:modify_role');
    Route::delete('/{customerRole}', [CustomerRoleController::class,'destroy'])->name('destroy')->middleware('permission:delete_role');

	});

  Route::resource('addresses', 'AddressController')->only(['index','store', 'update']);
  Route::delete('/addresses/delete/{customer_id}/{address_id?}', [AddressController::class, 'destroy'])->name('addresses.destroy');
  
});

Route::webSuperGroup('customer', function () {
  Route::resource('addresses', 'AddressController')->only(['index','store', 'update', 'destroy']);
  Route::get('/my-account', [ProfileController::class, 'myAccount'])->name('my-account');
  Route::delete('/favorites/{product_id}', [ProfileController::class, 'removeProductFromFavorites'])->name('favorites.destroy');
  Route::get('/print-orders', [ProfileController::class, 'printOrders'])->name('print-orders');
});
