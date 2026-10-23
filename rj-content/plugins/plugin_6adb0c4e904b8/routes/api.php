<?php

use Illuminate\Support\Facades\Route;
use RjCommerce\Controllers\Api\ApiOrderController;
use RjCommerce\Controllers\Api\ApiProductController;

Route::middleware(['api', \RjCommerce\Middleware\AuthenticateCommerceApi::class])->prefix('commerce/v1')->group(function () {
    Route::get('/orders', [ApiOrderController::class, 'index']);
    Route::get('/orders/{order}', [ApiOrderController::class, 'show']);
    Route::put('/orders/{order}/status', [ApiOrderController::class, 'updateStatus']);
    Route::put('/orders/{order}/shipping', [ApiOrderController::class, 'updateShipping']);

    Route::get('/products', [ApiProductController::class, 'index']);
    Route::get('/products/{slug}', [ApiProductController::class, 'show']);
    Route::get('/categories', [ApiProductController::class, 'categories']);
    Route::get('/brands', [ApiProductController::class, 'brands']);
});
