<?php

use App\Http\Controllers\Frontend\FormSubmitController;
use App\Http\Controllers\Frontend\FrontendController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendController::class, 'home'])->name('home');

Route::get('/rj-preview/{template}', [FrontendController::class, 'preview'])
    ->name('frontend.preview')
    ->middleware('auth');

Route::post('/rj-form/{slug}', [FormSubmitController::class, 'submit'])
    ->name('frontend.form.submit');

Route::get('/rj-fonts.css', [\App\Http\Controllers\Admin\CustomFontController::class, 'css'])
    ->name('frontend.fonts.css');

Route::get('/blog', [PostController::class, 'archive'])->name('frontend.posts.index');
Route::get('/blog/{slug}', [PostController::class, 'show'])->name('frontend.posts.show');

Route::get('/{slug}', [PageController::class, 'show'])->name('frontend.page');