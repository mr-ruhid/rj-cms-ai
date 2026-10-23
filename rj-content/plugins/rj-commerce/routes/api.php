<?php

use Illuminate\Support\Facades\Route;
use RjCommerce\Http\Controllers\Api\ApiOrderController;

Route::middleware(['api', \RjCommerce\Http\Middleware\AuthenticateCommerceApi::class])->prefix('commerce/v1')->group(function () {
    Route::get('/orders', [ApiOrderController::class, 'index']);
    Route::get('/orders/{order}', [ApiOrderController::class, 'show']);
    Route::put('/orders/{order}/status', [ApiOrderController::class, 'updateStatus']);
    Route::put('/orders/{order}/shipping', [ApiOrderController::class, 'updateShipping']);
});
