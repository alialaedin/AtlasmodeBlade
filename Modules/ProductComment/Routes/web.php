<?php

use Illuminate\Support\Facades\Route;
use Modules\ProductComment\Http\Controllers\Admin\ProductCommentController as AdminProductCommentController;
use Modules\ProductComment\Http\Controllers\Customer\ProductCommentController as CustomerProductCommentController;

Route::webSuperGroup('admin', function () {
  Route::prefix('/product-comments')->name('product-comments.')->group(function () {
    Route::post('/answer', [AdminProductCommentController::class, 'answer'])->name('answer')->middleware('permission:write_productComment');
    Route::get('/', [AdminProductCommentController::class, 'index'])->name('index')->middleware('permission:read_productComment');
    Route::get('/{productComment}', [AdminProductCommentController::class, 'show'])->name('show')->middleware('permission:read_productComment');
    Route::delete('/{productComment}', [AdminProductCommentController::class, 'destroy'])->name('destroy')->middleware('permission:delete_productComment');
    Route::post('/', [AdminProductCommentController::class, 'assignStatus'])->name('assign-status')->middleware('permission:modify_productComment');
  });
});
Route::post('/product-comments', [CustomerProductCommentController::class, 'store'])->name('product-comments.store');
