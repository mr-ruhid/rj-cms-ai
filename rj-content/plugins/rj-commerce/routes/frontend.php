<?php

use Illuminate\Support\Facades\Route;
use RjCommerce\Controllers\Frontend\ShopController;

Route::middleware(['web'])->group(function () {
    Route::get('/product/{slug}', [ShopController::class, 'showProduct'])->name('commerce.product.show');
    Route::get('/shop', [ShopController::class, 'index'])->name('commerce.shop.index');
});
