<?php

use Illuminate\Support\Facades\Route;
use Modules\Customer\Http\Controllers\Admin\AddressController as AdminAddressController;
use Modules\Customer\Http\Controllers\Customer\AddressController as CustomerAddressController;
use Modules\Customer\Http\Controllers\Admin\CustomerController;
use Modules\Customer\Http\Controllers\Admin\CustomerRoleController;
use Modules\Customer\Http\Controllers\Customer\ProfileController;
use Modules\Customer\Http\Controllers\Admin\WithdrawController as AdminWithdrawController;
use Modules\Customer\Http\Controllers\Customer\WithdrawController as CustomerWithdrawController;

Route::webSuperGroup('admin', function () {

  Route::get('/get-cities', [AdminAddressController::class, 'getCities'])->name('getCity');

	Route::prefix('/withdraws')->name('withdraws.')->group(function () {
		Route::get('/', [AdminWithdrawController::class, 'index'])->name('index')->middleware('permission:read_withdraw');
		Route::put('/{withdraw}', [AdminWithdrawController::class, 'update'])->name('update')->middleware('permission:modify_withdraw');
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
    Route::put('/{customer}', [CustomerController::class,'update'])->name('update')->middleware('permission:modify_customer');
    Route::delete('/{customer}', [CustomerController::class,'destroy'])->name('destroy')->middleware('permission:delete_customer');

	});

  Route::prefix('/customer-roles')->name('customer-roles.')->group(function () {
    Route::get('/', [CustomerRoleController::class,'index'])->name('index')->middleware('permission:read_role');
    Route::post('/', [CustomerRoleController::class,'store'])->name('store')->middleware('permission:write_role');
    Route::put('/{customerRole}', [CustomerRoleController::class,'update'])->name('update')->middleware('permission:modify_role');
    Route::delete('/{customerRole}', [CustomerRoleController::class,'destroy'])->name('destroy')->middleware('permission:delete_role');
	});


  Route::prefix('/addresses')->name('addresses.')->group(function () {
    Route::post('/', [AdminAddressController::class,'store'])->name('store')->middleware('permission:write_address');
    Route::put('/{id}', [AdminAddressController::class,'update'])->name('update')->middleware('permission:modify_address');
    Route::delete('/{id}', [AdminAddressController::class,'destroy'])->name('destroy')->middleware('permission:delete_address');
	});

  Route::resource('addresses', 'AddressController')->only(['index','store', 'update']);
  Route::delete('/addresses/delete/{customer_id}/{address_id?}', [AdminAddressController::class, 'destroy'])->name('addresses.destroy');
  
});

Route::middleware('auth:customer')->name('customer.')->group(function () {

  Route::prefix('/addresses')->name('addresses.')->group(function() {
    Route::get('/', [CustomerAddressController::class, 'index'])->name('index');
    Route::post('/', [CustomerAddressController::class, 'store'])->name('store');
    Route::put('/{address}', [CustomerAddressController::class, 'update'])->name('update');
    Route::delete('/{address}', [CustomerAddressController::class, 'destroy'])->name('destroy');
  });

  Route::post('/deposit', [ProfileController::class, 'depositWallet'])->name('wallet.deposit');
  Route::post('/withdraw', [CustomerWithdrawController::class, 'store'])->name('wallet.withdraw');
  Route::get('/my-account', [ProfileController::class, 'myAccount'])->name('my-account');
  Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
  Route::get('/print-orders', [ProfileController::class, 'printOrders'])->name('print-orders');
});
