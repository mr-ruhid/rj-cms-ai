<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LangController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\Settings\ReadingSettingsController;
use App\Http\Controllers\Admin\Settings\DiscussionSettingsController;
use App\Http\Controllers\Admin\Settings\MediaSettingsController;
use App\Http\Controllers\Admin\Settings\PermalinksSettingsController;
use App\Http\Controllers\Admin\Settings\ColorsSettingsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Settings\CacheSettingsController;
use App\Http\Controllers\Admin\Settings\AdvancedSettingsController;
use App\Http\Controllers\Admin\Settings\SeoSettingsController;
use App\Http\Controllers\Admin\Settings\SocialSettingsController;
use App\Http\Controllers\Admin\Settings\BackupSettingsController;
use App\Http\Controllers\Admin\UpdatesController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MediaCleanupController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BuilderController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\MenuItemController;

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
    Route::get('/posts/categories', [CategoryController::class, 'index'])->name('posts.categories');
    Route::get('/posts/categories/create', [CategoryController::class, 'create'])->name('posts.categories.create');
    Route::post('/posts/categories', [CategoryController::class, 'store'])->name('posts.categories.store');
    Route::get('/posts/categories/{category}/edit', [CategoryController::class, 'edit'])->name('posts.categories.edit');
    Route::put('/posts/categories/{category}', [CategoryController::class, 'update'])->name('posts.categories.update');
    Route::delete('/posts/categories/{category}', [CategoryController::class, 'destroy'])->name('posts.categories.destroy');
    Route::get('/posts/tags', [TagController::class, 'index'])->name('posts.tags');
    Route::post('/posts/tags', [TagController::class, 'store'])->name('posts.tags.store');
    Route::put('/posts/tags/{tag}', [TagController::class, 'update'])->name('posts.tags.update');
    Route::delete('/posts/tags/{tag}', [TagController::class, 'destroy'])->name('posts.tags.destroy');
    Route::view('/pages', 'admin.coming-soon')->name('pages.index');
    Route::view('/pages/create', 'admin.coming-soon')->name('pages.create');

    Route::get('/media', [MediaController::class, 'index'])->name('media.index');
    Route::get('/media/picker', [MediaController::class, 'picker'])->name('media.picker');
    Route::get('/media/create', [MediaController::class, 'create'])->name('media.create');
    Route::post('/media', [MediaController::class, 'store'])->name('media.store');
    Route::post('/media/bulk-delete', [MediaController::class, 'bulkDestroy'])->name('media.bulk-delete');
    Route::get('/media/{medium}/edit', [MediaController::class, 'edit'])->name('media.edit');
    Route::put('/media/{medium}', [MediaController::class, 'update'])->name('media.update');
    Route::delete('/media/{medium}', [MediaController::class, 'destroy'])->name('media.destroy');
    Route::get('/media-cleanup', [MediaCleanupController::class, 'index'])->name('media.cleanup');
    Route::post('/media-cleanup/delete', [MediaCleanupController::class, 'delete'])->name('media.cleanup.delete');
    Route::post('/media-cleanup/prune-db', [MediaCleanupController::class, 'pruneDatabase'])->name('media.cleanup.prune-db');

    Route::view('/comments', 'admin.coming-soon')->name('comments.index');
    Route::view('/comments/pending', 'admin.coming-soon')->name('comments.pending');
    Route::view('/comments/approved', 'admin.coming-soon')->name('comments.approved');
    Route::view('/comments/spam', 'admin.coming-soon')->name('comments.spam');
    Route::view('/comments/trash', 'admin.coming-soon')->name('comments.trash');

    Route::redirect('/appearance/menus', '/admin/menus')->name('appearance.menus');

    Route::prefix('menus')->name('menus.')->group(function () {
        Route::get('/', [MenuController::class, 'index'])->name('index');
        Route::post('/', [MenuController::class, 'store'])->name('store');
        Route::post('/locations', [MenuController::class, 'addLocation'])->name('locations.store');
        Route::get('/{menu}/edit', [MenuController::class, 'edit'])->name('edit');
        Route::put('/{menu}', [MenuController::class, 'update'])->name('update');
        Route::delete('/{menu}', [MenuController::class, 'destroy'])->name('destroy');

        Route::post('/{menu}/items', [MenuItemController::class, 'store'])->name('items.store');
        Route::put('/{menu}/items/{item}', [MenuItemController::class, 'update'])->name('items.update');
        Route::delete('/{menu}/items/{item}', [MenuItemController::class, 'destroy'])->name('items.destroy');
        Route::post('/{menu}/items/reorder', [MenuItemController::class, 'reorder'])->name('items.reorder');
        Route::get('/items/options', [MenuItemController::class, 'options'])->name('items.options');
    });


    Route::view('/appearance', 'admin.coming-soon')->name('appearance.index');
    Route::view('/appearance/themes', 'admin.coming-soon')->name('appearance.themes');
    Route::view('/appearance/customize', 'admin.coming-soon')->name('appearance.customize');
    Route::view('/appearance/widgets', 'admin.coming-soon')->name('appearance.widgets');
    Route::view('/appearance/editor', 'admin.coming-soon')->name('appearance.editor');
    Route::view('/appearance/colors', 'admin.coming-soon')->name('appearance.colors');
    Route::view('/appearance/options', 'admin.coming-soon')->name('appearance.options');

    Route::view('/plugins', 'admin.coming-soon')->name('plugins.index');
    Route::view('/plugins/create', 'admin.coming-soon')->name('plugins.create');
    Route::view('/plugins/editor', 'admin.coming-soon')->name('plugins.editor');

    Route::view('/ai', 'admin.coming-soon')->name('ai.index');
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

    Route::prefix('rjbuilder')->name('rjbuilder.')->group(function () {
        Route::get('/', [BuilderController::class, 'index'])->name('index');
        Route::get('/create', [BuilderController::class, 'create'])->name('create');
        Route::post('/', [BuilderController::class, 'store'])->name('store');
        Route::get('/{template}/edit', [BuilderController::class, 'edit'])->name('edit');
        Route::get('/{template}/build', [BuilderController::class, 'build'])->name('build');
        Route::put('/{template}', [BuilderController::class, 'update'])->name('update');
        Route::delete('/{template}', [BuilderController::class, 'destroy'])->name('destroy');
        Route::post('/{template}/duplicate', [BuilderController::class, 'duplicate'])->name('duplicate');
        Route::post('/{template}/set-default', [BuilderController::class, 'setDefault'])->name('set-default');
        Route::post('/{template}/conditions', [BuilderController::class, 'updateConditions'])->name('conditions');

        Route::prefix('ai')->name('ai.')->group(function () {
            Route::get('/status', [\App\Http\Controllers\Admin\AiBuilderController::class, 'status'])->name('status');
            Route::post('/generate-text', [\App\Http\Controllers\Admin\AiBuilderController::class, 'generateText'])->name('generate-text');
            Route::post('/improve-text', [\App\Http\Controllers\Admin\AiBuilderController::class, 'improveText'])->name('improve-text');
            Route::post('/translate-text', [\App\Http\Controllers\Admin\AiBuilderController::class, 'translateText'])->name('translate-text');
            Route::post('/suggest-section', [\App\Http\Controllers\Admin\AiBuilderController::class, 'suggestSection'])->name('suggest-section');
        });
        
        Route::post('/{template}/export', [\App\Http\Controllers\Admin\TemplateLibraryController::class, 'export'])->name('export');
        Route::post('/{template}/popup-settings', [BuilderController::class, 'updatePopupSettings'])->name('popup-settings');
    });

    Route::prefix('template-library')->name('template-library.')->group(function () {
        Route::post('/export-multiple', [\App\Http\Controllers\Admin\TemplateLibraryController::class, 'exportMultiple'])->name('export-multiple');
        Route::post('/import', [\App\Http\Controllers\Admin\TemplateLibraryController::class, 'import'])->name('import');
        Route::post('/preview', [\App\Http\Controllers\Admin\TemplateLibraryController::class, 'preview'])->name('preview');
    });

    Route::prefix('forms')->name('forms.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\FormController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\FormController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\FormController::class, 'store'])->name('store');
        Route::get('/{form}/edit', [\App\Http\Controllers\Admin\FormController::class, 'edit'])->name('edit');
        Route::put('/{form}', [\App\Http\Controllers\Admin\FormController::class, 'update'])->name('update');
        Route::delete('/{form}', [\App\Http\Controllers\Admin\FormController::class, 'destroy'])->name('destroy');
        Route::post('/{form}/duplicate', [\App\Http\Controllers\Admin\FormController::class, 'duplicate'])->name('duplicate');
        Route::prefix('{form}/submissions')->name('submissions.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\FormSubmissionController::class, 'index'])->name('index');
            Route::get('/export', [\App\Http\Controllers\Admin\FormSubmissionController::class, 'export'])->name('export');
            Route::post('/bulk', [\App\Http\Controllers\Admin\FormSubmissionController::class, 'bulk'])->name('bulk');
            Route::get('/{submission}', [\App\Http\Controllers\Admin\FormSubmissionController::class, 'show'])->name('show');
            Route::put('/{submission}', [\App\Http\Controllers\Admin\FormSubmissionController::class, 'update'])->name('update');
            Route::delete('/{submission}', [\App\Http\Controllers\Admin\FormSubmissionController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('global-widgets')->name('global-widgets.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\GlobalWidgetController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\GlobalWidgetController::class, 'store'])->name('store');
        Route::get('/options', [\App\Http\Controllers\Admin\GlobalWidgetController::class, 'options'])->name('options');
        Route::get('/{globalWidget}/edit', [\App\Http\Controllers\Admin\GlobalWidgetController::class, 'edit'])->name('edit');
        Route::put('/{globalWidget}', [\App\Http\Controllers\Admin\GlobalWidgetController::class, 'update'])->name('update');
        Route::delete('/{globalWidget}', [\App\Http\Controllers\Admin\GlobalWidgetController::class, 'destroy'])->name('destroy');
        Route::post('/{globalWidget}/duplicate', [\App\Http\Controllers\Admin\GlobalWidgetController::class, 'duplicate'])->name('duplicate');
    });

        Route::prefix('custom-fonts')->name('custom-fonts.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\CustomFontController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\CustomFontController::class, 'store'])->name('store');
        Route::put('/{customFont}', [\App\Http\Controllers\Admin\CustomFontController::class, 'update'])->name('update');
        Route::delete('/{customFont}', [\App\Http\Controllers\Admin\CustomFontController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('custom-icons')->name('custom-icons.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\CustomIconController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\CustomIconController::class, 'store'])->name('store');
        Route::post('/bulk', [\App\Http\Controllers\Admin\CustomIconController::class, 'storeBulk'])->name('bulk');
        Route::get('/options', [\App\Http\Controllers\Admin\CustomIconController::class, 'options'])->name('options');
        Route::put('/{customIcon}', [\App\Http\Controllers\Admin\CustomIconController::class, 'update'])->name('update');
        Route::delete('/{customIcon}', [\App\Http\Controllers\Admin\CustomIconController::class, 'destroy'])->name('destroy');
    });
    
    Route::get('/updates', [UpdatesController::class, 'index'])->name('updates.index');
    Route::post('/updates/check', [UpdatesController::class, 'check'])->name('updates.check');
    Route::put('/updates', [UpdatesController::class, 'update'])->name('updates.update');
    Route::post('/updates/run', [UpdatesController::class, 'run'])->name('updates.run');

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::get('/general', [SettingsController::class, 'general'])->name('general');
        Route::put('/general', [SettingsController::class, 'updateGeneral'])->name('general.update');

        Route::get('/writing', [SettingsController::class, 'writing'])->name('writing');
        Route::put('/writing', [SettingsController::class, 'updateWriting'])->name('writing.update');

        Route::get('/reading', [ReadingSettingsController::class, 'index'])->name('reading');
        Route::put('/reading', [ReadingSettingsController::class, 'update'])->name('reading.update');

        Route::get('/discussion', [DiscussionSettingsController::class, 'index'])->name('discussion');
        Route::put('/discussion', [DiscussionSettingsController::class, 'update'])->name('discussion.update');

        Route::get('/media', [MediaSettingsController::class, 'index'])->name('media');
        Route::put('/media', [MediaSettingsController::class, 'update'])->name('media.update');

        Route::get('/permalinks', [PermalinksSettingsController::class, 'index'])->name('permalinks');
        Route::put('/permalinks', [PermalinksSettingsController::class, 'update'])->name('permalinks.update');

        Route::get('/language', [SettingsController::class, 'language'])->name('language');
        Route::put('/language', [SettingsController::class, 'updateLanguage'])->name('language.update');
        Route::post('/language/{locale}/meta', [SettingsController::class, 'updateLanguageMeta'])->name('language.meta');
        Route::post('/language/add', [SettingsController::class, 'addLanguage'])->name('language.add');
        Route::delete('/language/{locale}', [SettingsController::class, 'removeLanguage'])->name('language.remove');
        Route::get('/language/{locale}/translations', [SettingsController::class, 'translations'])->name('language.translations');
        Route::put('/language/{locale}/translations', [SettingsController::class, 'updateTranslations'])->name('language.translations.update');
        Route::post('/language/{locale}/ai-translate', [SettingsController::class, 'aiTranslate'])->name('language.ai-translate');

        Route::get('/colors', [ColorsSettingsController::class, 'index'])->name('colors');
        Route::put('/colors', [ColorsSettingsController::class, 'update'])->name('colors.update');

        Route::get('/seo', [SeoSettingsController::class, 'index'])->name('seo');
        Route::put('/seo', [SeoSettingsController::class, 'update'])->name('seo.update');
        Route::get('/email', [SettingsController::class, 'email'])->name('email');
        Route::put('/email', [SettingsController::class, 'updateEmail'])->name('email.update');
        Route::post('/email/test', [SettingsController::class, 'testEmail'])->name('email.test');
        Route::view('/payments', 'admin.coming-soon')->name('payments');
        Route::get('/security', [SettingsController::class, 'security'])->name('security');
        Route::put('/security', [SettingsController::class, 'updateSecurity'])->name('security.update');
        Route::get('/backup', [BackupSettingsController::class, 'index'])->name('backup');
        Route::put('/backup', [BackupSettingsController::class, 'update'])->name('backup.update');
        Route::post('/backup/create', [BackupSettingsController::class, 'create'])->name('backup.create');
        Route::get('/backup/download/{file}', [BackupSettingsController::class, 'download'])->name('backup.download');
        Route::delete('/backup/{file}', [BackupSettingsController::class, 'delete'])->name('backup.delete');
        Route::get('/cache', [CacheSettingsController::class, 'index'])->name('cache');
        Route::put('/cache', [CacheSettingsController::class, 'update'])->name('cache.update');
        Route::get('/social', [SocialSettingsController::class, 'index'])->name('social');
        Route::put('/social', [SocialSettingsController::class, 'update'])->name('social.update');
        Route::view('/notifications', 'admin.coming-soon')->name('notifications');
        Route::view('/api', 'admin.coming-soon')->name('api');

        Route::get('/ai', [SettingsController::class, 'ai'])->name('ai');
        Route::put('/ai', [SettingsController::class, 'updateAi'])->name('ai.update');
        Route::post('/ai/test', [SettingsController::class, 'testAi'])->name('ai.test');
        Route::get('/ai/models', [SettingsController::class, 'fetchAiModels'])->name('ai.models');

        Route::get('/advanced', [AdvancedSettingsController::class, 'index'])->name('advanced');
        Route::put('/advanced', [AdvancedSettingsController::class, 'update'])->name('advanced.update');
        Route::post('/advanced/clear-logs', [AdvancedSettingsController::class, 'clearLogs'])->name('advanced.clear-logs');
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
