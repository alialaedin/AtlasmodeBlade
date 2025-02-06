<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\Admin\AuthController as AdminAuthController;

Route::webSuperGroup('admin', function () {
  Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login-form');
  Route::post('/login', [AdminAuthController::class, 'login'])->name('login');
  Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
}, []);
