<?php

use Illuminate\Support\Facades\Route;
use RjCommerce\Http\Controllers\Admin\ProductController;

Route::middleware(['web', 'auth', 'admin'])->prefix('admin/commerce')->name('admin.commerce.')->group(function () {
    Route::resource('products', ProductController::class)->except(['show']);
});
