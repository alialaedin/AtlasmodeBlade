<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\Http\Controllers\Admin\PostCategoryController;
use Modules\Blog\Http\Controllers\Admin\PostController as AdminPostController;
use Modules\Blog\Http\Controllers\Front\PostController as FrontPostController;

Route::webSuperGroup('admin', function () {

  // Post-Category
  Route::prefix('/post-categories')->name('post-categories.')->group(function () {
    Route::get('/', [PostCategoryController::class, 'index'])->name('index')->middleware('permission:read_post-category');
    Route::post('/', [PostCategoryController::class, 'store'])->name('store')->middleware('permission:write_post-category');
    Route::patch('/{postCategory}', [PostCategoryController::class, 'update'])->name('update')->middleware('permission:modify_post-category');
    Route::delete('/{postCategory}', [PostCategoryController::class, 'destroy'])->name('destroy')->middleware('permission:delete_post-category');
  });

  // Posts 
  Route::prefix('/posts')->name('posts.')->group(function () {
    Route::get('/', [AdminPostController::class, 'index'])->name('index')->middleware('permission:read_post');
    Route::get('/create', [AdminPostController::class, 'create'])->name('create')->middleware('permission:write_post');
    Route::get('/{post}', [AdminPostController::class, 'show'])->name('show')->middleware('permission:read_post');
    Route::post('/', [AdminPostController::class, 'store'])->name('store')->middleware('permission:write_post');
    Route::get('/{post}/edit', [AdminPostController::class, 'edit'])->name('edit')->middleware('permission:modify_post');
    Route::patch('/{post}', [AdminPostController::class, 'update'])->name('update')->middleware('permission:modify_post');
    Route::delete('/{post}', [AdminPostController::class, 'destroy'])->name('destroy')->middleware('permission:delete_post');
  });
});

Route::prefix('/posts')->name('front.posts.')->group(function () {
  Route::get('/', [FrontPostController::class, 'index'])->name('index');
  Route::get('/{categoryId}', [FrontPostController::class, 'byCategory'])->name('byCategory');
  Route::get('/{post}', [FrontPostController::class, 'show'])->name('show');
});
