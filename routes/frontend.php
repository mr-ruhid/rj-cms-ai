<?php

use App\Http\Controllers\Frontend\FormSubmitController;
use App\Http\Controllers\Frontend\FrontendController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/theme-assets/{slug}/{path}', function (string $slug, string $path) {
    $manager = app(\App\Services\ThemeManager::class);
    $file = $manager->resolveAsset($slug, $path);

    if (!$file) {
        abort(404);
    }

    $mime = \Illuminate\Support\Facades\File::mimeType($file) ?: 'application/octet-stream';

    return response()->file($file, [
        'Content-Type' => $mime,
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*')->name('theme.asset');

Route::get('/plugin-assets/{slug}/{path}', function (string $slug, string $path) {
    $manager = app(\App\Services\PluginManager::class);
    $file = $manager->resolveAsset($slug, $path);

    if (!$file) {
        abort(404);
    }

    $mime = \Illuminate\Support\Facades\File::mimeType($file) ?: 'application/octet-stream';

    return response()->file($file, [
        'Content-Type' => $mime,
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*')->name('plugin.asset');

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

Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', '^(?!admin|login|register|logout|password|email|verification|api|rj-preview|rj-form|rj-fonts|theme-assets|plugin-assets|blog|storage|up|sanctum|livewire).*$')
    ->name('frontend.page');

