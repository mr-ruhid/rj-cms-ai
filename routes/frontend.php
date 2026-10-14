<?php

use App\Http\Controllers\Frontend\FormSubmitController;
use App\Http\Controllers\Frontend\FrontendController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendController::class, 'home'])->name('home');

Route::get('/rj-preview/{template}', [FrontendController::class, 'preview'])
    ->name('frontend.preview')
    ->middleware('auth');

Route::post('/rj-form/{slug}', [FormSubmitController::class, 'submit'])
    ->name('frontend.form.submit');

Route::get('/rj-fonts.css', [\App\Http\Controllers\Admin\CustomFontController::class, 'css'])
    ->name('frontend.fonts.css');
