<?php

use App\Http\Controllers\Frontend\FrontendController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendController::class, 'home'])->name('home');

Route::get('/rj-preview/{template}', [FrontendController::class, 'preview'])
    ->name('frontend.preview')
    ->middleware('auth');