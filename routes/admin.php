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
use App\Http\Controllers\Admin\Settings\NotificationSettingsController;
use App\Http\Controllers\Admin\Settings\ApiSettingsController;
use App\Http\Controllers\Admin\UpdatesController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MediaCleanupController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BuilderController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\AppearanceWidgetsController;
use App\Http\Controllers\Admin\ThemeEditorController;
use App\Http\Controllers\Admin\AppearanceColorsController;
use App\Http\Controllers\Admin\AppearanceOptionsController;
use App\Http\Controllers\Admin\ToolsImportController;
use App\Http\Controllers\Admin\ToolsExportController;
use App\Http\Controllers\Admin\ToolsHealthController;
use App\Http\Controllers\Admin\ToolsBackupController;
use App\Http\Controllers\Admin\ToolsLogsController;
use App\Http\Controllers\Admin\ToolsDiagnosticsController;
use App\Http\Controllers\Admin\AiCenterController;
use App\Http\Controllers\Admin\AiToolController;
use App\Http\Controllers\Admin\AboutController;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn() => redirect()->route('admin.dashboard'))->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/ask-ai', [DashboardController::class, 'askAi'])->name('ask-ai');

    Route::get('/lang/{locale}', [LangController::class, 'switch'])->name('lang.switch');

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
    });

    Route::resource('users', UserController::class)->except(['show']);

    Route::prefix('posts')->name('posts.')->group(function () {
        Route::get('/', [PostController::class, 'index'])->name('index');
        Route::get('/create', [PostController::class, 'create'])->name('create');
        Route::post('/', [PostController::class, 'store'])->name('store');
        Route::post('/bulk', [PostController::class, 'bulk'])->name('bulk');

        Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('/tags', [TagController::class, 'index'])->name('tags');
        Route::post('/tags', [TagController::class, 'store'])->name('tags.store');
        Route::put('/tags/{tag}', [TagController::class, 'update'])->name('tags.update');
        Route::delete('/tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');

        Route::get('/{post}/build', [PostController::class, 'build'])->name('build');
        Route::post('/{post}/blocks', [PostController::class, 'updateBlocks'])->name('blocks');
        Route::get('/{post}/edit', [PostController::class, 'edit'])->name('edit');
        Route::put('/{post}', [PostController::class, 'update'])->name('update');
        Route::delete('/{post}', [PostController::class, 'destroy'])->name('destroy');
        Route::post('/{post}/duplicate', [PostController::class, 'duplicate'])->name('duplicate');
        Route::post('/{post}/publish', [PostController::class, 'publish'])->name('publish');
        Route::post('/{post}/unpublish', [PostController::class, 'unpublish'])->name('unpublish');
        Route::post('/{post}/trash-action', [PostController::class, 'trash'])->name('trash-action');
        Route::post('/{post}/restore', [PostController::class, 'restore'])->name('restore');
        Route::post('/{post}/ai/seo-analyze', [PostController::class, 'analyzeSeo'])->name('ai.seo-analyze');
        Route::post('/{post}/ai/generate-meta', [PostController::class, 'aiGenerateMeta'])->name('ai.generate-meta');
        Route::post('/{post}/ai/generate-title', [PostController::class, 'aiGenerateTitle'])->name('ai.generate-title');
        Route::post('/{post}/ai/generate-description', [PostController::class, 'aiGenerateDescription'])->name('ai.generate-description');
        Route::post('/{post}/ai/suggest-keyword', [PostController::class, 'aiSuggestKeyword'])->name('ai.suggest-keyword');
        Route::get('/{post}', [PostController::class, 'show'])->name('show');
    });

    Route::prefix('pages')->name('pages.')->group(function () {
        Route::get('/', [PostController::class, 'index'])->name('index')->defaults('type', 'page');
        Route::get('/create', [PostController::class, 'create'])->name('create')->defaults('type', 'page');
        Route::post('/', [PostController::class, 'store'])->name('store');
        Route::post('/bulk', [PostController::class, 'bulk'])->name('bulk');
        Route::get('/{post}/build', [PostController::class, 'build'])->name('build');
        Route::post('/{post}/blocks', [PostController::class, 'updateBlocks'])->name('blocks');
        Route::get('/{post}/edit', [PostController::class, 'edit'])->name('edit');
        Route::put('/{post}', [PostController::class, 'update'])->name('update');
        Route::delete('/{post}', [PostController::class, 'destroy'])->name('destroy');
        Route::post('/{post}/duplicate', [PostController::class, 'duplicate'])->name('duplicate');
        Route::post('/{post}/publish', [PostController::class, 'publish'])->name('publish');
        Route::post('/{post}/unpublish', [PostController::class, 'unpublish'])->name('unpublish');
        Route::post('/{post}/trash-action', [PostController::class, 'trash'])->name('trash-action');
        Route::post('/{post}/restore', [PostController::class, 'restore'])->name('restore');
        Route::post('/{post}/ai/seo-analyze', [PostController::class, 'analyzeSeo'])->name('ai.seo-analyze');
        Route::post('/{post}/ai/generate-meta', [PostController::class, 'aiGenerateMeta'])->name('ai.generate-meta');
        Route::post('/{post}/ai/generate-title', [PostController::class, 'aiGenerateTitle'])->name('ai.generate-title');
        Route::post('/{post}/ai/generate-description', [PostController::class, 'aiGenerateDescription'])->name('ai.generate-description');
        Route::post('/{post}/ai/suggest-keyword', [PostController::class, 'aiSuggestKeyword'])->name('ai.suggest-keyword');
        Route::get('/{post}', [PostController::class, 'show'])->name('show');
    });

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

    Route::redirect('/appearance/menus', '/admin/menus')->name('appearance.menus');
    Route::redirect('/appearance/themes', '/admin/themes')->name('appearance.themes');

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

    Route::get('/appearance/customize', function () {
        $theme = app(\App\Services\ThemeManager::class)->activeTheme();

        if (!$theme) {
            return redirect()
                ->route('admin.themes.index')
                ->with('error', __('No active theme. Please activate a theme first.'));
        }

        return redirect()->route('admin.themes.customizer', $theme);
    })->name('appearance.customize');

    Route::get('/appearance/widgets', [AppearanceWidgetsController::class, 'index'])->name('appearance.widgets');
    Route::post('/appearance/widgets/bulk', [AppearanceWidgetsController::class, 'bulk'])->name('appearance.widgets.bulk');
    Route::post('/appearance/widgets/{id}/toggle', [AppearanceWidgetsController::class, 'toggle'])->name('appearance.widgets.toggle');
    Route::post('/appearance/widgets/{id}/update', [AppearanceWidgetsController::class, 'update'])->name('appearance.widgets.update');
    Route::post('/appearance/widgets/{id}/reset', [AppearanceWidgetsController::class, 'reset'])->name('appearance.widgets.reset');

    Route::get('/appearance/editor', [ThemeEditorController::class, 'index'])->name('appearance.editor');
    Route::get('/appearance/editor/{slug}/files', [ThemeEditorController::class, 'files'])->name('appearance.editor.files');
    Route::get('/appearance/editor/{slug}/read', [ThemeEditorController::class, 'read'])->name('appearance.editor.read');
    Route::post('/appearance/editor/{slug}/save', [ThemeEditorController::class, 'save'])->name('appearance.editor.save');
    Route::get('/appearance/editor/{slug}/backups', [ThemeEditorController::class, 'backups'])->name('appearance.editor.backups');
    Route::post('/appearance/editor/{slug}/backups/{timestamp}/restore', [ThemeEditorController::class, 'restore'])->name('appearance.editor.restore');
    Route::delete('/appearance/editor/{slug}/backups/{timestamp}', [ThemeEditorController::class, 'deleteBackup'])->name('appearance.editor.backups.delete');

    Route::get('/appearance/colors', [AppearanceColorsController::class, 'index'])->name('appearance.colors');
    Route::post('/appearance/colors/save', [AppearanceColorsController::class, 'save'])->name('appearance.colors.save');
    Route::post('/appearance/colors/reset', [AppearanceColorsController::class, 'reset'])->name('appearance.colors.reset');
    Route::get('/appearance/colors/export', [AppearanceColorsController::class, 'export'])->name('appearance.colors.export');
    Route::post('/appearance/colors/import', [AppearanceColorsController::class, 'import'])->name('appearance.colors.import');

    Route::get('/appearance/options', [AppearanceOptionsController::class, 'index'])->name('appearance.options');
    Route::post('/appearance/options/save', [AppearanceOptionsController::class, 'save'])->name('appearance.options.save');
    Route::post('/appearance/options/reset', [AppearanceOptionsController::class, 'reset'])->name('appearance.options.reset');

    Route::get('/ai', [AiCenterController::class, 'index'])->name('ai.index');
    Route::get('/ai/models', [AiCenterController::class, 'models'])->name('ai.models');
    Route::post('/ai/models', [AiCenterController::class, 'saveModels'])->name('ai.models.save');
    Route::get('/ai/prompts', [AiCenterController::class, 'prompts'])->name('ai.prompts');
    Route::post('/ai/prompts', [AiCenterController::class, 'savePrompts'])->name('ai.prompts.save');
    Route::get('/ai/usage', [AiCenterController::class, 'usage'])->name('ai.usage');
    Route::get('/ai/tools', [AiToolController::class, 'index'])->name('ai.tools');
    Route::post('/ai/tools/generate-content', [AiToolController::class, 'generateContent'])->name('ai.tools.generate-content');
    Route::post('/ai/tools/bulk-translate', [AiToolController::class, 'bulkTranslate'])->name('ai.tools.bulk-translate');
    Route::get('/ai/tools/seo-audit', [AiToolController::class, 'seoAudit'])->name('ai.tools.seo-audit');

    Route::view('/support', 'admin.coming-soon')->name('support.index');
    Route::view('/support/tickets', 'admin.coming-soon')->name('support.tickets');
    Route::view('/support/chat', 'admin.coming-soon')->name('support.chat');
    Route::view('/support/faq', 'admin.coming-soon')->name('support.faq');
    Route::view('/support/contact', 'admin.coming-soon')->name('support.contact');
    Route::view('/support/docs', 'admin.coming-soon')->name('support.docs');
    Route::view('/support/status', 'admin.coming-soon')->name('support.status');

    Route::view('/tools', 'admin.coming-soon')->name('tools.index');

    Route::get('/tools/import', [ToolsImportController::class, 'index'])->name('tools.import');
    Route::post('/tools/import/preview', [ToolsImportController::class, 'preview'])->name('tools.import.preview');
    Route::post('/tools/import/run', [ToolsImportController::class, 'run'])->name('tools.import.run');
    Route::post('/tools/import/cancel', [ToolsImportController::class, 'cancel'])->name('tools.import.cancel');

    Route::get('/tools/export', [ToolsExportController::class, 'index'])->name('tools.export');
    Route::post('/tools/export/run', [ToolsExportController::class, 'run'])->name('tools.export.run');

    Route::get('/tools/health', [ToolsHealthController::class, 'index'])->name('tools.health');
    Route::post('/tools/health/analyze', [ToolsHealthController::class, 'analyze'])->name('tools.health.analyze');
    Route::post('/tools/health/refresh', [ToolsHealthController::class, 'refresh'])->name('tools.health.refresh');

    Route::get('/tools/backup', [ToolsBackupController::class, 'index'])->name('tools.backup');
    Route::post('/tools/backup/create', [ToolsBackupController::class, 'create'])->name('tools.backup.create');
    Route::get('/tools/backup/download/{filename}', [ToolsBackupController::class, 'download'])->name('tools.backup.download');
    Route::delete('/tools/backup/{filename}', [ToolsBackupController::class, 'delete'])->name('tools.backup.delete');
    Route::post('/tools/backup/restore/{filename}', [ToolsBackupController::class, 'restore'])->name('tools.backup.restore');
    Route::post('/tools/backup/settings', [ToolsBackupController::class, 'updateSettings'])->name('tools.backup.settings');

    Route::get('/tools/logs', [ToolsLogsController::class, 'index'])->name('tools.logs');
    Route::post('/tools/logs/fetch', [ToolsLogsController::class, 'fetch'])->name('tools.logs.fetch');
    Route::post('/tools/logs/clear', [ToolsLogsController::class, 'clear'])->name('tools.logs.clear');
    Route::get('/tools/logs/download/{filename}', [ToolsLogsController::class, 'download'])->name('tools.logs.download');

    Route::get('/tools/diagnostics', [ToolsDiagnosticsController::class, 'index'])->name('tools.diagnostics');

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
        Route::post('/{template}/publish', [BuilderController::class, 'publish'])->name('publish');
        Route::post('/{template}/unpublish', [BuilderController::class, 'unpublish'])->name('unpublish');
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

    Route::prefix('comments')->name('comments.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\CommentController::class, 'index'])->name('index');
        Route::get('/pending', [\App\Http\Controllers\Admin\CommentController::class, 'index'])->name('pending')->defaults('status_filter', 'pending');
        Route::get('/approved', [\App\Http\Controllers\Admin\CommentController::class, 'index'])->name('approved')->defaults('status_filter', 'approved');
        Route::get('/spam', [\App\Http\Controllers\Admin\CommentController::class, 'index'])->name('spam')->defaults('status_filter', 'spam');
        Route::get('/trash', [\App\Http\Controllers\Admin\CommentController::class, 'index'])->name('trash')->defaults('status_filter', 'trash');
        Route::post('/', [\App\Http\Controllers\Admin\CommentController::class, 'store'])->name('store');
        Route::post('/bulk', [\App\Http\Controllers\Admin\CommentController::class, 'bulk'])->name('bulk');
        Route::post('/toggle-global', [\App\Http\Controllers\Admin\CommentController::class, 'toggleGlobal'])->name('toggle-global');
        Route::get('/ai/status', [\App\Http\Controllers\Admin\CommentController::class, 'aiStatus'])->name('ai.status');

        Route::get('/{comment}', [\App\Http\Controllers\Admin\CommentController::class, 'show'])->name('show');
        Route::put('/{comment}', [\App\Http\Controllers\Admin\CommentController::class, 'update'])->name('update');
        Route::delete('/{comment}', [\App\Http\Controllers\Admin\CommentController::class, 'destroy'])->name('destroy');
        Route::post('/{comment}/approve', [\App\Http\Controllers\Admin\CommentController::class, 'approve'])->name('approve');
        Route::post('/{comment}/unapprove', [\App\Http\Controllers\Admin\CommentController::class, 'unapprove'])->name('unapprove');
        Route::post('/{comment}/spam-action', [\App\Http\Controllers\Admin\CommentController::class, 'spam'])->name('spam-action');
        Route::post('/{comment}/trash-action', [\App\Http\Controllers\Admin\CommentController::class, 'trash'])->name('trash-action');
        Route::post('/{comment}/restore', [\App\Http\Controllers\Admin\CommentController::class, 'restore'])->name('restore');
        Route::post('/{comment}/ai/analyze', [\App\Http\Controllers\Admin\CommentController::class, 'aiAnalyze'])->name('ai.analyze');
    });

    Route::prefix('post-types')->name('post-types.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PostTypeController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\PostTypeController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\PostTypeController::class, 'store'])->name('store');
        Route::get('/{postType}/edit', [\App\Http\Controllers\Admin\PostTypeController::class, 'edit'])->name('edit');
        Route::put('/{postType}', [\App\Http\Controllers\Admin\PostTypeController::class, 'update'])->name('update');
        Route::delete('/{postType}', [\App\Http\Controllers\Admin\PostTypeController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('taxonomies')->name('taxonomies.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\TaxonomyController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\TaxonomyController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\TaxonomyController::class, 'store'])->name('store');
        Route::get('/options', [\App\Http\Controllers\Admin\TaxonomyController::class, 'options'])->name('options');
        Route::get('/{taxonomy}/edit', [\App\Http\Controllers\Admin\TaxonomyController::class, 'edit'])->name('edit');
        Route::put('/{taxonomy}', [\App\Http\Controllers\Admin\TaxonomyController::class, 'update'])->name('update');
        Route::delete('/{taxonomy}', [\App\Http\Controllers\Admin\TaxonomyController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('terms')->name('terms.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\TermController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\TermController::class, 'store'])->name('store');
        Route::post('/bulk', [\App\Http\Controllers\Admin\TermController::class, 'bulk'])->name('bulk');
        Route::post('/reorder', [\App\Http\Controllers\Admin\TermController::class, 'reorder'])->name('reorder');
        Route::get('/options', [\App\Http\Controllers\Admin\TermController::class, 'options'])->name('options');
        Route::get('/{term}', [\App\Http\Controllers\Admin\TermController::class, 'show'])->name('show');
        Route::get('/{term}/edit', [\App\Http\Controllers\Admin\TermController::class, 'edit'])->name('edit');
        Route::put('/{term}', [\App\Http\Controllers\Admin\TermController::class, 'update'])->name('update');
        Route::delete('/{term}', [\App\Http\Controllers\Admin\TermController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('custom-fields')->name('custom-fields.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\CustomFieldController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\CustomFieldController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\CustomFieldController::class, 'store'])->name('store');
        Route::post('/reorder', [\App\Http\Controllers\Admin\CustomFieldController::class, 'reorder'])->name('reorder');
        Route::get('/options', [\App\Http\Controllers\Admin\CustomFieldController::class, 'options'])->name('options');
        Route::get('/{customField}/edit', [\App\Http\Controllers\Admin\CustomFieldController::class, 'edit'])->name('edit');
        Route::put('/{customField}', [\App\Http\Controllers\Admin\CustomFieldController::class, 'update'])->name('update');
        Route::delete('/{customField}', [\App\Http\Controllers\Admin\CustomFieldController::class, 'destroy'])->name('destroy');
        Route::post('/{customField}/duplicate', [\App\Http\Controllers\Admin\CustomFieldController::class, 'duplicate'])->name('duplicate');
    });

    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\RoleController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\RoleController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\RoleController::class, 'store'])->name('store');
        Route::get('/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'show'])->name('show');
        Route::get('/{role}/edit', [\App\Http\Controllers\Admin\RoleController::class, 'edit'])->name('edit');
        Route::put('/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'update'])->name('update');
        Route::delete('/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('destroy');
        Route::post('/{role}/duplicate', [\App\Http\Controllers\Admin\RoleController::class, 'duplicate'])->name('duplicate');
        Route::post('/{role}/assign-users', [\App\Http\Controllers\Admin\RoleController::class, 'assignUsers'])->name('assign-users');
        Route::get('/{role}/users', [\App\Http\Controllers\Admin\RoleController::class, 'users'])->name('users');
    });

    Route::prefix('permissions')->name('permissions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PermissionController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\PermissionController::class, 'store'])->name('store');
        Route::post('/sync', [\App\Http\Controllers\Admin\PermissionController::class, 'sync'])->name('sync');
        Route::post('/group', [\App\Http\Controllers\Admin\PermissionController::class, 'groupStore'])->name('group-store');
        Route::get('/{permission}/roles', [\App\Http\Controllers\Admin\PermissionController::class, 'roles'])->name('roles');
        Route::post('/{permission}/sync-roles', [\App\Http\Controllers\Admin\PermissionController::class, 'syncRoles'])->name('sync-roles');
        Route::put('/{permission}', [\App\Http\Controllers\Admin\PermissionController::class, 'update'])->name('update');
        Route::delete('/{permission}', [\App\Http\Controllers\Admin\PermissionController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('revisions')->name('revisions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\RevisionController::class, 'index'])->name('index');
        Route::post('/prune', [\App\Http\Controllers\Admin\RevisionController::class, 'prune'])->name('prune');
        Route::get('/{revision}', [\App\Http\Controllers\Admin\RevisionController::class, 'show'])->name('show');
        Route::post('/{revision}/restore', [\App\Http\Controllers\Admin\RevisionController::class, 'restore'])->name('restore');
        Route::delete('/{revision}', [\App\Http\Controllers\Admin\RevisionController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('index');
        Route::get('/recent', [\App\Http\Controllers\Admin\NotificationController::class, 'recent'])->name('recent');
        Route::get('/unread-count', [\App\Http\Controllers\Admin\NotificationController::class, 'unreadCount'])->name('unread-count');
        Route::post('/mark-all-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllRead'])->name('mark-all-read');
        Route::post('/bulk', [\App\Http\Controllers\Admin\NotificationController::class, 'bulk'])->name('bulk');
        Route::get('/settings', [\App\Http\Controllers\Admin\NotificationController::class, 'settings'])->name('settings');
        Route::put('/settings', [\App\Http\Controllers\Admin\NotificationController::class, 'updateSettings'])->name('settings.update');
        Route::get('/{notification}', [\App\Http\Controllers\Admin\NotificationController::class, 'show'])->name('show');
        Route::post('/{notification}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/{notification}/unread', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsUnread'])->name('unread');
        Route::delete('/{notification}', [\App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('activity-log')->name('activity-log.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('index');
        Route::post('/clear', [\App\Http\Controllers\Admin\ActivityLogController::class, 'clear'])->name('clear');
        Route::post('/bulk', [\App\Http\Controllers\Admin\ActivityLogController::class, 'bulk'])->name('bulk');
        Route::get('/{activityLog}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('show');
        Route::delete('/{activityLog}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('plugins')->name('plugins.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PluginController::class, 'index'])->name('index');
        Route::get('/browse', [\App\Http\Controllers\Admin\PluginController::class, 'browse'])->name('browse');
        Route::post('/install-github', [\App\Http\Controllers\Admin\PluginController::class, 'installFromGitHub'])->name('install-github');
        Route::view('/create', 'admin.plugins.create')->name('create');

        Route::get('/editor', [\App\Http\Controllers\Admin\PluginEditorController::class, 'index'])->name('editor');
        Route::get('/editor/{plugin}/files', [\App\Http\Controllers\Admin\PluginEditorController::class, 'files'])->name('editor.files');
        Route::get('/editor/{plugin}/read', [\App\Http\Controllers\Admin\PluginEditorController::class, 'read'])->name('editor.read');
        Route::post('/editor/{plugin}/save', [\App\Http\Controllers\Admin\PluginEditorController::class, 'save'])->name('editor.save');
        Route::post('/editor/review', [\App\Http\Controllers\Admin\PluginEditorController::class, 'review'])->name('editor.review');

        Route::post('/upload', [\App\Http\Controllers\Admin\PluginController::class, 'upload'])->name('upload');
        Route::post('/bulk', [\App\Http\Controllers\Admin\PluginController::class, 'bulk'])->name('bulk');
        Route::post('/check-updates', [\App\Http\Controllers\Admin\PluginController::class, 'checkUpdates'])->name('check-updates');
        Route::get('/{plugin}', [\App\Http\Controllers\Admin\PluginController::class, 'show'])->name('show');
        Route::post('/{plugin}/activate', [\App\Http\Controllers\Admin\PluginController::class, 'activate'])->name('activate');
        Route::post('/{plugin}/deactivate', [\App\Http\Controllers\Admin\PluginController::class, 'deactivate'])->name('deactivate');
        Route::post('/{plugin}/update', [\App\Http\Controllers\Admin\PluginController::class, 'update'])->name('update');
        Route::delete('/{plugin}', [\App\Http\Controllers\Admin\PluginController::class, 'destroy'])->name('destroy');
        Route::get('/{plugin}/settings', [\App\Http\Controllers\Admin\PluginController::class, 'settings'])->name('settings');
        Route::put('/{plugin}/settings', [\App\Http\Controllers\Admin\PluginController::class, 'updateSettings'])->name('settings.update');
    });

    Route::prefix('themes')->name('themes.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ThemeController::class, 'index'])->name('index');
        Route::get('/browse', [\App\Http\Controllers\Admin\ThemeController::class, 'browse'])->name('browse');
        Route::post('/install-github', [\App\Http\Controllers\Admin\ThemeController::class, 'installFromGitHub'])->name('install-github');
        Route::post('/upload', [\App\Http\Controllers\Admin\ThemeController::class, 'upload'])->name('upload');
        Route::post('/check-updates', [\App\Http\Controllers\Admin\ThemeController::class, 'checkUpdates'])->name('check-updates');
        Route::get('/{theme}', [\App\Http\Controllers\Admin\ThemeController::class, 'show'])->name('show');
        Route::post('/{theme}/activate', [\App\Http\Controllers\Admin\ThemeController::class, 'activate'])->name('activate');
        Route::post('/{theme}/update', [\App\Http\Controllers\Admin\ThemeController::class, 'update'])->name('update');
        Route::post('/{theme}/preview', [\App\Http\Controllers\Admin\ThemeController::class, 'preview'])->name('preview');
        Route::get('/{theme}/export', [\App\Http\Controllers\Admin\ThemeController::class, 'export'])->name('export');
        Route::delete('/{theme}', [\App\Http\Controllers\Admin\ThemeController::class, 'destroy'])->name('destroy');
        Route::get('/{theme}/customizer', [\App\Http\Controllers\Admin\ThemeController::class, 'customizer'])->name('customizer');
        Route::put('/{theme}/customizer', [\App\Http\Controllers\Admin\ThemeController::class, 'updateCustomizer'])->name('customizer.update');
        Route::delete('/{theme}/customizer', [\App\Http\Controllers\Admin\ThemeController::class, 'resetCustomizer'])->name('customizer.reset');
    });

    Route::prefix('widgets')->name('widgets.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\UploadedWidgetController::class, 'index'])->name('index');
        Route::post('/upload', [\App\Http\Controllers\Admin\UploadedWidgetController::class, 'upload'])->name('upload');
        Route::post('/bulk', [\App\Http\Controllers\Admin\UploadedWidgetController::class, 'bulk'])->name('bulk');
        Route::get('/{widget}', [\App\Http\Controllers\Admin\UploadedWidgetController::class, 'show'])->name('show');
        Route::post('/{widget}/activate', [\App\Http\Controllers\Admin\UploadedWidgetController::class, 'activate'])->name('activate');
        Route::post('/{widget}/deactivate', [\App\Http\Controllers\Admin\UploadedWidgetController::class, 'deactivate'])->name('deactivate');
        Route::get('/{widget}/preview', [\App\Http\Controllers\Admin\UploadedWidgetController::class, 'preview'])->name('preview');
        Route::delete('/{widget}', [\App\Http\Controllers\Admin\UploadedWidgetController::class, 'destroy'])->name('destroy');
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
        Route::get('/security', [SettingsController::class, 'security'])->name('security');
        Route::put('/security', [SettingsController::class, 'updateSecurity'])->name('security.update');
        Route::get('/backup', [BackupSettingsController::class, 'index'])->name('backup');
        Route::put('/backup', [BackupSettingsController::class, 'update'])->name('backup.update');
        Route::post('/backup/create', [BackupSettingsController::class, 'create'])->name('backup.create');
        Route::get('/backup/download/{file}', [BackupSettingsController::class, 'download'])->name('backup.download');
        Route::delete('/backup/{file}', [BackupSettingsController::class, 'delete'])->name('backup.delete');
        Route::get('/cache', [CacheSettingsController::class, 'index'])->name('cache');
        Route::put('/cache', [CacheSettingsController::class, 'update'])->name('cache.update');
        Route::post('/cache/clear/{type}', [CacheSettingsController::class, 'clear'])->name('cache.clear');
        Route::get('/social', [SocialSettingsController::class, 'index'])->name('social');
        Route::put('/social', [SocialSettingsController::class, 'update'])->name('social.update');
        
        Route::get('/notifications', [NotificationSettingsController::class, 'index'])->name('notifications');
        Route::put('/notifications', [NotificationSettingsController::class, 'update'])->name('notifications.update');
        
        Route::get('/api', [ApiSettingsController::class, 'index'])->name('api');
        Route::post('/api/generate', [ApiSettingsController::class, 'generate'])->name('api.generate');
        Route::delete('/api/{id}/revoke', [ApiSettingsController::class, 'revoke'])->name('api.revoke');

        Route::get('/ai', [SettingsController::class, 'ai'])->name('ai');
        Route::put('/ai', [SettingsController::class, 'updateAi'])->name('ai.update');
        Route::post('/ai/test', [SettingsController::class, 'testAi'])->name('ai.test');
        Route::get('/ai/models', [SettingsController::class, 'fetchAiModels'])->name('ai.models');

        Route::get('/advanced', [AdvancedSettingsController::class, 'index'])->name('advanced');
        Route::put('/advanced', [AdvancedSettingsController::class, 'update'])->name('advanced.update');
        Route::post('/advanced/clear-logs', [AdvancedSettingsController::class, 'clearLogs'])->name('advanced.clear-logs');
        Route::redirect('/roles', '/admin/roles')->name('roles');
    });

    Route::prefix('about')->name('about.')->group(function () {
        Route::get('/', [AboutController::class, 'index'])->name('index')->defaults('slug', 'info');
        Route::get('/api', [AboutController::class, 'index'])->name('api')->defaults('slug', 'api');
        Route::get('/theme', [AboutController::class, 'index'])->name('theme')->defaults('slug', 'theme');
        Route::get('/admin', [AboutController::class, 'index'])->name('admin')->defaults('slug', 'admin');
        Route::get('/plugin', [AboutController::class, 'index'])->name('plugin')->defaults('slug', 'plugin');
        Route::get('/developer', [AboutController::class, 'index'])->name('developer')->defaults('slug', 'developer');
        Route::get('/hooks', [AboutController::class, 'index'])->name('hooks')->defaults('slug', 'hooks');
        Route::get('/other', [AboutController::class, 'index'])->name('other')->defaults('slug', 'other');
        Route::get('/credits', [AboutController::class, 'index'])->name('credits')->defaults('slug', 'credits');
        Route::get('/license', [AboutController::class, 'index'])->name('license')->defaults('slug', 'license');
        Route::get('/user-guide', [AboutController::class, 'index'])->name('user-guide')->defaults('slug', 'user-guide');
        Route::get('/faq', [AboutController::class, 'index'])->name('faq')->defaults('slug', 'faq');
        Route::get('/troubleshooting', [AboutController::class, 'index'])->name('troubleshooting')->defaults('slug', 'troubleshooting');
    });
});