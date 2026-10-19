<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Theme;
use App\Services\ThemeEditorService;
use App\Services\ThemeManager;
use Illuminate\Http\Request;

class ThemeEditorController extends Controller
{
    protected ThemeEditorService $editor;
    protected ThemeManager $manager;

    public function __construct(ThemeEditorService $editor, ThemeManager $manager)
    {
        $this->editor = $editor;
        $this->manager = $manager;
    }

    public function index(Request $request)
    {
        $this->manager->discover();

        $themes = Theme::orderByDesc('status')->orderBy('name')->get();

        $activeTheme = Theme::active();

        $selectedSlug = $request->input('theme', $activeTheme?->slug);
        $theme = $selectedSlug ? Theme::where('slug', $selectedSlug)->first() : $activeTheme;

        if (!$theme && $themes->isNotEmpty()) {
            $theme = $themes->first();
        }

        if (!$theme) {
            return redirect()
                ->route('admin.themes.index')
                ->with('error', __('No themes available to edit.'));
        }

        if (!\Illuminate\Support\Facades\File::isDirectory($theme->path())) {
            return redirect()
                ->route('admin.themes.index')
                ->with('error', __('Theme directory not found: :path', ['path' => $theme->path()]));
        }

        $info = $this->editor->themeInfo($theme);
        $files = $this->editor->discoverFiles($theme);

        return view('admin.themes.editor', compact('themes', 'theme', 'info', 'files'));
    }

    public function files(string $slug)
    {
        $theme = Theme::where('slug', $slug)->firstOrFail();

        return response()->json([
            'files' => $this->editor->discoverFiles($theme),
        ]);
    }

    public function read(string $slug, Request $request)
    {
        $theme = Theme::where('slug', $slug)->firstOrFail();
        $path = (string) $request->input('path', '');

        $result = $this->editor->readFile($theme, $path);

        if (!$result['success']) {
            return response()->json(['error' => $result['error']], 404);
        }

        return response()->json($result);
    }

    public function save(string $slug, Request $request)
    {
        $theme = Theme::where('slug', $slug)->firstOrFail();

        if ($theme->isActive() === false && $this->isSystemFile($request->input('path', ''))) {
            return response()->json([
                'error' => __('System files of inactive themes cannot be edited.'),
            ], 403);
        }

        $validated = $request->validate([
            'path' => 'required|string|max:500',
            'content' => 'nullable|string',
        ]);

        $result = $this->editor->saveFile($theme, $validated['path'], $validated['content']);

        if (!$result['success']) {
            return response()->json(['error' => $result['error']], 422);
        }

        $this->manager->clearCache();

        if ($validated['path'] === 'theme.json') {
            $this->manager->discover();
        }

        return response()->json($result);
    }

    public function backups(string $slug)
    {
        $theme = Theme::where('slug', $slug)->firstOrFail();

        return response()->json([
            'backups' => $this->editor->listBackups($theme),
        ]);
    }

    public function restore(string $slug, string $timestamp)
    {
        $theme = Theme::where('slug', $slug)->firstOrFail();

        $result = $this->editor->restoreBackup($theme, $timestamp);

        if (!$result['success']) {
            return response()->json(['error' => $result['error']], 422);
        }

        $this->manager->clearCache();

        return response()->json($result);
    }

    public function deleteBackup(string $slug, string $timestamp)
    {
        $theme = Theme::where('slug', $slug)->firstOrFail();

        $result = $this->editor->deleteBackup($theme, $timestamp);

        if (!$result['success']) {
            return response()->json(['error' => $result['error']], 422);
        }

        return response()->json($result);
    }

    protected function isSystemFile(string $path): bool
    {
        return in_array($path, [
            'theme.json',
            'views/layout.blade.php',
        ], true);
    }
}
