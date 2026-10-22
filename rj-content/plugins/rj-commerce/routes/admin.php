<?php

use Illuminate\Support\Facades\Route;
use RjCommerce\Http\Controllers\Admin\ProductController;
use RjCommerce\Http\Controllers\Admin\OrderController;
use RjCommerce\Http\Controllers\Admin\CategoryController;
use RjCommerce\Http\Controllers\Admin\BrandController;
use RjCommerce\Http\Controllers\Admin\TagController;
use RjCommerce\Http\Controllers\Admin\SettingsController;

Route::middleware(['web', 'auth', 'admin'])->prefix('admin/commerce')->name('admin.commerce.')->group(function () {
    Route::resource('products', ProductController::class)->except(['show']);
    Route::resource('orders', OrderController::class)->only(['index', 'show', 'update']);
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('brands', BrandController::class)->except(['show']);
    Route::resource('tags', TagController::class)->except(['show']);

    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
});
