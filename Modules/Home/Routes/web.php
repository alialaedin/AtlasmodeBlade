<?php

use Illuminate\Support\Facades\Route;
use Modules\Home\Http\Controllers\BladeHomeController;

Route::get('/' , [BladeHomeController::class, 'index'])->name('front.home.index');