<?php

use Illuminate\Support\Facades\Route;
use RjCommerce\Controllers\Admin\ProductController;
use RjCommerce\Controllers\Admin\OrderController;
use RjCommerce\Controllers\Admin\CategoryController;
use RjCommerce\Controllers\Admin\BrandController;
use RjCommerce\Controllers\Admin\TagController;
use RjCommerce\Controllers\Admin\AttributeController;
use RjCommerce\Controllers\Admin\CouponController;
use RjCommerce\Controllers\Admin\ReviewController;
use RjCommerce\Controllers\Admin\ShippingController;
use RjCommerce\Controllers\Admin\SettingsController;

Route::middleware(['web', 'auth', 'admin'])->prefix('admin/commerce')->name('admin.commerce.')->group(function () {
    Route::resource('products', ProductController::class)->except(['show']);
    Route::resource('orders', OrderController::class)->only(['index', 'show', 'update']);
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('brands', BrandController::class)->except(['show']);
    Route::resource('tags', TagController::class)->except(['show']);
    Route::resource('attributes', AttributeController::class)->except(['show']);
    Route::resource('coupons', CouponController::class)->except(['show', 'create', 'edit']);

    Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::put('reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
    Route::put('reviews/{review}/reject', [ReviewController::class, 'reject'])->name('reviews.reject');
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    Route::get('shipping', [ShippingController::class, 'index'])->name('shipping.zones.index');
    Route::post('shipping/zones', [ShippingController::class, 'storeZone'])->name('shipping.zones.store');
    Route::put('shipping/zones/{zone}', [ShippingController::class, 'updateZone'])->name('shipping.zones.update');
    Route::delete('shipping/zones/{zone}', [ShippingController::class, 'destroyZone'])->name('shipping.zones.destroy');
    Route::post('shipping/zones/{zone}/methods', [ShippingController::class, 'storeMethod'])->name('shipping.zones.methods.store');
    Route::delete('shipping/methods/{method}', [ShippingController::class, 'destroyMethod'])->name('shipping.methods.destroy');

    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::get('api-docs', [SettingsController::class, 'apiDocs'])->name('api.docs');
});
