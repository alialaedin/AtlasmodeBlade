<?php

use App\Http\Controllers\testController;
use Illuminate\Support\Facades\Route;
use Modules\Product\Jobs\SpecificDiscountApplierJob;

//Route::post('get-posts',
//    [\Modules\Blog\Http\Controllers\Front\PostController::class, 'pay']);
//
//Route::post('verify',
//    [\Modules\Blog\Http\Controllers\Front\PostController::class, 'verify']);

//Route::get('/test', [testController::class, 'index'])->name('test');
Route::get('/add', [testController::class, 'add'])->name('add');

Route::get('/new-advertisements-table', [testController::class, 'makeAdvertisementsTable']);
Route::get('/dispatch-job', function() {
  SpecificDiscountApplierJob::dispatch();
});
