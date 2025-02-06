<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\Admin\SpecificDiscountController;
use Modules\Product\Http\Controllers\Admin\SpecificDiscountItemController;
use Modules\Product\Http\Controllers\Admin\SpecificDiscountTypeController;
use Modules\Product\Http\Controllers\Admin\ProductController as AdminProductController;
use Modules\Product\Http\Controllers\Admin\RecommendationController;

Route::webSuperGroup("admin", function () {

  // Product
  Route::prefix('/products')->name('products.')->group(function (){

    Route::get('/search', [AdminProductController::class, 'search'])->name('search');
    Route::get('/load-varieties', [AdminProductController::class, 'loadVarieties'])->name('load-varieties');

    Route::post('/{product}/approve', [AdminProductController::class, 'approved_product'])->name('approve');
    Route::post('/{product}/disapprove', [AdminProductController::class, 'approved_product'])->name('disapprove');

    Route::get('/', [AdminProductController::class, 'index'])->name('index')->hasPermission('read_product');
    Route::get('/create', [AdminProductController::class, 'create'])->name('create')->hasPermission('write_product');
    Route::post('/', [AdminProductController::class, 'store'])->name('store')->hasPermission('write_product');
    Route::get('/{product}/edit', [AdminProductController::class, 'edit'])->name('edit')->hasPermission('modify_product');
    Route::put('/{product}', [AdminProductController::class, 'update'])->name('update')->hasPermission('modify_product');
    Route::delete('/{product}', [AdminProductController::class, 'destroy'])->name('destroy')->hasPermission('delete_product');
  });

  // recommendations
  Route::prefix('/recommendations')->name('recommendations.')->group(function (){
    Route::get('/groups/', [RecommendationController::class, 'groups'])->name('groups')->hasPermission('recommendation');
    Route::get('/groups/{group}', [RecommendationController::class, 'index'])->name('index')->hasPermission('recommendation');
    Route::post('/groups/{group}/sort', [RecommendationController::class, 'sort'])->name('sort')->hasPermission('recommendation');
    Route::post('/', [RecommendationController::class, 'store'])->name('store')->hasPermission('recommendation');
    Route::delete('/{recommendation}', [RecommendationController::class, 'destroy'])->name('destroy')->hasPermission('recommendation');
  });

  // SpecificDiscount
  Route::prefix('specific-discounts')->name('specific-discounts.')->group(function () {
    Route::get('/', [SpecificDiscountController::class, 'index'])->name('index')->hasPermission('read_specificDiscount');
    Route::post('/', [SpecificDiscountController::class, 'store'])->name('store')->hasPermission('write_specificDiscount');
    Route::put('/{specificDiscount}', [SpecificDiscountController::class, 'update'])->name('update')->hasPermission('modify_specificDiscount');
    Route::delete('/{specificDiscount}', [SpecificDiscountController::class, 'destroy'])->name('destroy')->hasPermission('delete_specificDiscount');
  });

  // SpecificDiscountType
  Route::prefix('/specific-discounts/types/')->name('specific-discounts.types.')->group(function () {
    Route::get('/{specificDiscount}', [SpecificDiscountTypeController::class, 'index'])->name('index')->hasPermission('read_specificDiscount');
    Route::post('/{specificDiscount}', [SpecificDiscountTypeController::class, 'store'])->name('store')->hasPermission('write_specificDiscount');
    Route::put('/{specificDiscountType}', [SpecificDiscountTypeController::class, 'update'])->name('update')->hasPermission('modify_specificDiscount');
    Route::delete('/{specificDiscountType}', [SpecificDiscountTypeController::class, 'destroy'])->name('destroy')->hasPermission('delete_specificDiscount');
  });

  // SpecificDiscountItem
  Route::prefix('/specific-discounts/types')->name('specific-discounts.items.')->group(function () {
    Route::prefix('/{specificDiscountType}/items')->group(function () {
      Route::get('/', [SpecificDiscountItemController::class, 'index'])->name('index')->hasPermission('read_specificDiscountItem');
      Route::get('/create', [SpecificDiscountItemController::class, 'create'])->name('create')->hasPermission('write_specificDiscountItem');
      Route::post('/', [SpecificDiscountItemController::class, 'store'])->name('store')->hasPermission('write_specificDiscountItem');
    });
    Route::prefix('/{specificDiscountItem}')->group(function () {
      Route::get('/edit', [SpecificDiscountItemController::class, 'edit'])->name('edit')->hasPermission('modify_specificDiscountItem');
      Route::put('/', [SpecificDiscountItemController::class, 'update'])->name('update')->hasPermission('modify_specificDiscountItem');
      Route::delete('/', [SpecificDiscountItemController::class, 'destroy'])->name('destroy')->hasPermission('delete_specificDiscountItem');
    });
  });
});
