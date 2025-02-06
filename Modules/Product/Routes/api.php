<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\Admin\SpecificDiscountController;
use Modules\Product\Http\Controllers\Front\ProductController;
use Modules\Product\Http\Controllers\Front\RecommendationController;
use Modules\Product\Http\Controllers\Customer\ProductController as CustomerProductController;
use Illuminate\Support\Facades\Artisan;


require base_path('vendor/shetabit/shopit/src/Modules/Product/Routes/api.php');

//    Route::get('products/search', [AllProductController::class, 'search'])->name('products.search');
//    Route::get('products', [AllProductController::class, 'index'])->name('products.index');
//    Route::get('products/compare', [CompareController::class, 'index'])->name('product.compare');
//    Route::get('products/compare/search', [CompareController::class, 'search'])->name('product.compare.search');

    Route::get('sdcc', function() {
        Artisan::call('cache:clear');
        return '<h1>'." Cache Clear Successfully".'</h1>';
    });



Route::superGroup("admin" ,function (){
    Route::permissionResource('specificDiscount', 'SpecificDiscountController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
    Route::prefix('specificDiscountType/{specificDiscount}')->name('specificDiscountType.')->group(function () {
        Route::get('/', [SpecificDiscountController::class, 'TypeIndex'])->name('index')->hasPermission('read_specificDiscount');
        Route::get('/{id}', [SpecificDiscountController::class, 'TypeShow'])->name('show')->hasPermission('read_specificDiscount');
        Route::post('/', [SpecificDiscountController::class, 'TypeStore'])->name('store')->hasPermission('write_specificDiscount');
        Route::put('/{id}', [SpecificDiscountController::class, 'TypeUpdate'])->name('update')->hasPermission('modify_specificDiscount');
        Route::delete('/{id}', [SpecificDiscountController::class, 'TypeDestroy'])->name('destroy')->hasPermission('delete_specificDiscount');
    });
    Route::prefix('specificDiscountItem/{specificDiscount}/items/{type}')->name('specificDiscountItem.')->group(function () {
        Route::get('/', [SpecificDiscountController::class, 'ItemIndex'])->name('index')->hasPermission('read_specificDiscountItem');
        Route::get('/{id}', [SpecificDiscountController::class, 'ItemShow'])->name('show')->hasPermission('read_specificDiscountItem');
        Route::post('/', [SpecificDiscountController::class, 'ItemStore'])->name('store')->hasPermission('write_specificDiscountItem') ;
        Route::put('/{id}', [SpecificDiscountController::class, 'ItemUpdate'])->name('update')->hasPermission('modify_specificDiscountItem') ;
        Route::delete('/{id}', [SpecificDiscountController::class, 'ItemDestroy'])->name('destroy')->hasPermission('delete_specificDiscountItem');
    });

    Route::get('products_excels', 'ProductController@excels')->name('products.excel');
});



Route::get('front/products_light/{product}', [ProductController::class, 'show_light'])->name('product.show');

Route::superGroup('customer' ,function (){
    Route::post('products/listen', [\Modules\Product\Http\Controllers\Customer\ListenChargeController::class,'store'])->name('products.listen');
    Route::delete('products/unlisten', [\Modules\Product\Http\Controllers\Customer\ListenChargeController::class,'destroy'])->name('products.unlisten');
    Route::get('favorites', [CustomerProductController::class, 'indexFavorites'])->name('favorites.indexFavorites');
    Route::post('products/{product}/favorite', [CustomerProductController::class, 'addToFavorites'])->name('product.addToFavorites');
    Route::delete('products/{product}/favorite', [CustomerProductController::class, 'deleteFromFavorites'])->name('product.deleteFromFavorites');
});

