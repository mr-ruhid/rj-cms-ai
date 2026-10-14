<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Theme;
use App\Services\ThemeManager;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    protected ThemeManager $manager;

    public function __construct(ThemeManager $manager)
    {
        $this->manager = $manager;
    }

    public function index(Request $request)
    {
        $this->manager->discover();

        $query = Theme::orderBy('name');

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('slug', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $themes = $query->get();

        $activeSlug = rj_get_option('active_theme', '');

        $counts = [
            'all' => Theme::count(),
            'active' => Theme::where('status', 'active')->count(),
            'inactive' => Theme::where('status', 'inactive')->count(),
            'error' => Theme::where('status', 'error')->count(),
            'updates' => $this->manager->updatesAvailable(),
        ];

        return view('admin.themes.index', compact('themes', 'counts', 'activeSlug'));
    }

    public function show(Theme $theme)
    {
        $metadata = $theme->metadata();
        $customizations = $theme->customizations()->count();
        $children = $theme->children();

        return view('admin.themes.show', compact('theme', 'metadata', 'customizations', 'children'));
    }

    public function activate(Request $request, Theme $theme)
    {
        $result = $this->manager->activate($theme);

        if ($request->expectsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    public function destroy(Request $request, Theme $theme)
    {
        if ($theme->isActive()) {
            $message = __('Cannot delete the active theme.');

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        if ($theme->children()->isNotEmpty()) {
            $message = __('Cannot delete a parent theme while child themes exist.');

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        $result = $this->manager->delete($theme);

        if ($request->expectsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        if ($result['success']) {
            return redirect()
                ->route('admin.themes.index')
                ->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    public function upload(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:zip|max:51200',
        ]);

        $result = $this->manager->installFromZip($validated['file']);

        if ($request->expectsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        if ($result['success']) {
            return redirect()
                ->route('admin.themes.index')
                ->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    public function customizer(Theme $theme)
    {
        if (!$theme->isActive()) {
            return redirect()
                ->route('admin.themes.index')
                ->with('error', __('Only the active theme can be customized.'));
        }

        $schema = $this->manager->customizerSchema($theme);
        $sections = $this->manager->customizerSections($theme);

        $values = [];

        foreach ($sections as $sectionKey => $section) {
            $values[$sectionKey] = $theme->sectionCustomizations($sectionKey);
        }

        return view('admin.themes.customizer', compact('theme', 'schema', 'sections', 'values'));
    }

    public function updateCustomizer(Request $request, Theme $theme)
    {
        if (!$theme->isActive()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => __('Theme is not active.')], 422);
            }
            return back()->with('error', __('Theme is not active.'));
        }

        $validated = $request->validate([
            'section' => 'required|string|max:100',
            'settings' => 'required|array',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            $theme->setCustomization($validated['section'], (string) $key, $value);
        }

        $this->manager->clearCache();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Customization saved.'),
            ]);
        }

        return back()->with('success', __('Customization saved.'));
    }

    public function resetCustomizer(Request $request, Theme $theme)
    {
        if (!$theme->isActive()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => __('Theme is not active.')], 422);
            }
            return back()->with('error', __('Theme is not active.'));
        }

        $validated = $request->validate([
            'section' => 'nullable|string|max:100',
        ]);

        $query = $theme->customizations();

        if (!empty($validated['section'])) {
            $query->where('section', $validated['section']);
        }

        $count = $query->delete();

        $this->manager->clearCache();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'count' => $count,
                'message' => __(':n customization(s) reset.', ['n' => $count]),
            ]);
        }

        return back()->with('success', __('Customization reset.'));
    }

    public function export(Theme $theme)
    {
        $result = $this->manager->export($theme);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return response()
            ->download($result['path'], $result['filename'], [
                'Content-Type' => 'application/zip',
            ])
            ->deleteFileAfterSend(true);
    }

    public function checkUpdates(Request $request)
    {
        $result = $this->manager->checkForUpdates();

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        return back()->with('success', __(':n update(s) available.', ['n' => $result['count']]));
    }

    public function update(Request $request, Theme $theme)
    {
        $result = $this->manager->update($theme);

        if ($request->expectsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    public function preview(Theme $theme)
    {
        $url = url('/') . '?rj_theme_preview=' . $theme->slug . '&token=' . $this->manager->previewToken($theme);

        return response()->json([
            'success' => true,
            'url' => $url,
        ]);
    }
}