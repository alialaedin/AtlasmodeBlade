<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\Admin\AuthController as AdminAuthController;
use Modules\Auth\Http\Controllers\Customer\AuthController as CustomerAuthController;

Route::webSuperGroup('admin', function () {
  Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login-form');
  Route::post('/login', [AdminAuthController::class, 'login'])->name('login');
  Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
}, []);

Route::name('customer.')->group(function() {
  Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('login-form');
  Route::post('/send-token', [CustomerAuthController::class, 'sendToken'])->name('send-token');
  Route::post('/login', [CustomerAuthController::class, 'login'])->name('login');
  Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');
});
