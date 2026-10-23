<?php

use Illuminate\Support\Facades\Route;
use RjCommerce\Controllers\Frontend\ShopController;
use RjCommerce\Controllers\Frontend\CartController;
use RjCommerce\Controllers\Frontend\CheckoutController;

Route::middleware(['web'])->group(function () {
    Route::get('/shop', [ShopController::class, 'index'])->name('commerce.shop.index');
    Route::get('/product/{slug}', [ShopController::class, 'showProduct'])->name('commerce.product.show');
    Route::get('/cart', [CartController::class, 'index'])->name('commerce.cart.index');
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('commerce.checkout.index');

    Route::post('/cart/add', [CartController::class, 'add'])->name('commerce.cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('commerce.cart.update');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('commerce.cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('commerce.cart.clear');

    Route::post('/checkout/process', [CheckoutController::class, 'store'])->name('commerce.checkout.store');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('commerce.checkout.success');
});
