<?php

use Illuminate\Support\Facades\Route;
use Modules\ProductComment\Http\Controllers\Admin\ProductCommentController;

Route::webSuperGroup('admin', function () {
  Route::prefix('/product-comments')->name('product-comments.')->group(function () {
    Route::post('/answer', [ProductCommentController::class, 'answer'])->name('answer')->middleware('permission:write_productComment');
    Route::get('/', [ProductCommentController::class, 'index'])->name('index')->middleware('permission:read_productComment');
    Route::get('/{productComment}', [ProductCommentController::class, 'show'])->name('show')->middleware('permission:read_productComment');
    Route::delete('/{productComment}', [ProductCommentController::class, 'destroy'])->name('destroy')->middleware('permission:delete_productComment');
    Route::post('/', [ProductCommentController::class, 'assignStatus'])->name('assign-status')->middleware('permission:modify_productComment');
  });
});
Route::post('/product-comments', [Modules\ProductComment\Http\Controllers\Customer\ProductCommentController::class, 'store'])->name('product-comments.store');
