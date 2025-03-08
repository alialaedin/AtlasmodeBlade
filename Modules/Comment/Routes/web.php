<?php

use Illuminate\Support\Facades\Route;
use Modules\Comment\Http\Controllers\Admin\CommentController as AdminCommentController;
use Modules\Comment\Http\Controllers\Front\CommentController as FrontCommentController;

Route::webSuperGroup('admin', function () {
  Route::prefix('/post-comments')->name('post-comments.')->group(function () {
    Route::post('/{comment}/answer', [AdminCommentController::class, 'answer'])->name('answer')->middleware('permission:write_comment');
    Route::get('/{post}', [AdminCommentController::class, 'index'])->name('index')->middleware('permission:read_comment');
    Route::get('/', [AdminCommentController::class, 'all'])->name('all')->middleware('permission:read_comment');
    Route::get('/{comment}/show', [AdminCommentController::class, 'show'])->name('show')->middleware('permission:read_comment');
    Route::put('/{comment}', [AdminCommentController::class, 'update'])->name('update')->middleware('permission:modify_comment');
    Route::delete('/{comment}', [AdminCommentController::class, 'destroy'])->name('destroy')->middleware('permission:delete_comment');
  });
});

Route::post('/comments/{post}', [FrontCommentController::class, 'store'])->name('front.comments.store');
