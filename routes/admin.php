<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LangController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn() => redirect()->route('admin.dashboard'))->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/lang/{locale}', [LangController::class, 'switch'])->name('lang.switch');

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
    });

    Route::resource('users', UserController::class)->except(['show']);

    Route::view('/posts', 'admin.coming-soon')->name('posts.index');
    Route::view('/posts/create', 'admin.coming-soon')->name('posts.create');
    Route::view('/posts/categories', 'admin.coming-soon')->name('posts.categories');
    Route::view('/posts/tags', 'admin.coming-soon')->name('posts.tags');

    Route::view('/pages', 'admin.coming-soon')->name('pages.index');
    Route::view('/pages/create', 'admin.coming-soon')->name('pages.create');

    Route::view('/media', 'admin.coming-soon')->name('media.index');
    Route::view('/media/create', 'admin.coming-soon')->name('media.create');

    Route::view('/comments', 'admin.coming-soon')->name('comments.index');
    Route::view('/comments/pending', 'admin.coming-soon')->name('comments.pending');
    Route::view('/comments/approved', 'admin.coming-soon')->name('comments.approved');
    Route::view('/comments/spam', 'admin.coming-soon')->name('comments.spam');
    Route::view('/comments/trash', 'admin.coming-soon')->name('comments.trash');

    Route::view('/appearance', 'admin.coming-soon')->name('appearance.index');
    Route::view('/appearance/themes', 'admin.coming-soon')->name('appearance.themes');
    Route::view('/appearance/customize', 'admin.coming-soon')->name('appearance.customize');
    Route::view('/appearance/widgets', 'admin.coming-soon')->name('appearance.widgets');
    Route::view('/appearance/menus', 'admin.coming-soon')->name('appearance.menus');
    Route::view('/appearance/editor', 'admin.coming-soon')->name('appearance.editor');
    Route::view('/appearance/colors', 'admin.coming-soon')->name('appearance.colors');
    Route::view('/appearance/options', 'admin.coming-soon')->name('appearance.options');

    Route::view('/plugins', 'admin.coming-soon')->name('plugins.index');
    Route::view('/plugins/create', 'admin.coming-soon')->name('plugins.create');
    Route::view('/plugins/editor', 'admin.coming-soon')->name('plugins.editor');

    Route::view('/ai', 'admin.coming-soon')->name('ai.index');
    Route::view('/ai/settings', 'admin.coming-soon')->name('ai.settings');
    Route::view('/ai/models', 'admin.coming-soon')->name('ai.models');
    Route::view('/ai/prompts', 'admin.coming-soon')->name('ai.prompts');
    Route::view('/ai/usage', 'admin.coming-soon')->name('ai.usage');
    Route::view('/ai/tools', 'admin.coming-soon')->name('ai.tools');

    Route::view('/support', 'admin.coming-soon')->name('support.index');
    Route::view('/support/tickets', 'admin.coming-soon')->name('support.tickets');
    Route::view('/support/chat', 'admin.coming-soon')->name('support.chat');
    Route::view('/support/faq', 'admin.coming-soon')->name('support.faq');
    Route::view('/support/contact', 'admin.coming-soon')->name('support.contact');
    Route::view('/support/docs', 'admin.coming-soon')->name('support.docs');
    Route::view('/support/status', 'admin.coming-soon')->name('support.status');

    Route::view('/tools', 'admin.coming-soon')->name('tools.index');
    Route::view('/tools/import', 'admin.coming-soon')->name('tools.import');
    Route::view('/tools/export', 'admin.coming-soon')->name('tools.export');
    Route::view('/tools/health', 'admin.coming-soon')->name('tools.health');
    Route::view('/tools/backup', 'admin.coming-soon')->name('tools.backup');
    Route::view('/tools/logs', 'admin.coming-soon')->name('tools.logs');
    Route::view('/tools/diagnostics', 'admin.coming-soon')->name('tools.diagnostics');

    Route::view('/updates', 'admin.coming-soon')->name('updates.index');

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::get('/general', [SettingsController::class, 'general'])->name('general');
        Route::put('/general', [SettingsController::class, 'updateGeneral'])->name('general.update');

        Route::view('/writing', 'admin.coming-soon')->name('writing');
        Route::view('/reading', 'admin.coming-soon')->name('reading');
        Route::view('/discussion', 'admin.coming-soon')->name('discussion');
        Route::view('/media', 'admin.coming-soon')->name('media');
        Route::view('/permalinks', 'admin.coming-soon')->name('permalinks');
        Route::view('/language', 'admin.coming-soon')->name('language');
        Route::view('/colors', 'admin.coming-soon')->name('colors');
        Route::view('/seo', 'admin.coming-soon')->name('seo');
        Route::get('/email', [SettingsController::class, 'email'])->name('email');
        Route::put('/email', [SettingsController::class, 'updateEmail'])->name('email.update');
        Route::view('/payments', 'admin.coming-soon')->name('payments');
        Route::view('/security', 'admin.coming-soon')->name('security');
        Route::view('/backup', 'admin.coming-soon')->name('backup');
        Route::view('/cache', 'admin.coming-soon')->name('cache');
        Route::view('/social', 'admin.coming-soon')->name('social');
        Route::view('/notifications', 'admin.coming-soon')->name('notifications');
        Route::view('/api', 'admin.coming-soon')->name('api');
        Route::view('/ai', 'admin.coming-soon')->name('ai');
        Route::view('/advanced', 'admin.coming-soon')->name('advanced');
        Route::view('/roles', 'admin.coming-soon')->name('roles');
    });

    Route::prefix('about')->name('about.')->group(function () {
        Route::view('/', 'admin.coming-soon')->name('index');
        Route::view('/api', 'admin.coming-soon')->name('api');
        Route::view('/theme', 'admin.coming-soon')->name('theme');
        Route::view('/admin', 'admin.coming-soon')->name('admin');
        Route::view('/plugin', 'admin.coming-soon')->name('plugin');
        Route::view('/developer', 'admin.coming-soon')->name('developer');
        Route::view('/hooks', 'admin.coming-soon')->name('hooks');
        Route::view('/other', 'admin.coming-soon')->name('other');
        Route::view('/credits', 'admin.coming-soon')->name('credits');
        Route::view('/license', 'admin.coming-soon')->name('license');
    });
});
