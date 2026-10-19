<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Widgets\WidgetRegistry;
use Illuminate\Http\Request;

class AppearanceWidgetsController extends Controller
{
    public function index(Request $request)
    {
        WidgetRegistry::reset();

        $widgets = WidgetRegistry::discover();
        $categories = WidgetRegistry::categories();
        $contexts = rj_widget_contexts_list();
        $storedSettings = rj_widget_settings();

        $items = [];

        foreach ($widgets as $widget) {
            $id = $widget->id();
            $defaultCategory = $widget->category();

            $items[] = [
                'id' => $id,
                'name' => $widget->name(),
                'description' => $widget->description(),
                'icon' => $widget->icon(),
                'default_category' => $defaultCategory,
                'category' => rj_widget_category($id, $defaultCategory),
                'source' => $this->detectSource($widget),
                'enabled' => rj_widget_enabled($id),
                'contexts' => rj_widget_contexts($id),
                'has_override' => isset($storedSettings[$id]),
            ];
        }

        $q = trim((string) $request->input('q', ''));
        $catFilter = (string) $request->input('category', '');
        $srcFilter = (string) $request->input('source', '');
        $statusFilter = (string) $request->input('status', '');

        if ($q !== '') {
            $items = array_values(array_filter($items, function ($i) use ($q) {
                return stripos($i['name'], $q) !== false
                    || stripos((string) $i['description'], $q) !== false
                    || stripos($i['id'], $q) !== false;
            }));
        }

        if ($catFilter !== '') {
            $items = array_values(array_filter($items, fn($i) => $i['category'] === $catFilter));
        }

        if ($srcFilter !== '') {
            $items = array_values(array_filter($items, fn($i) => $i['source'] === $srcFilter));
        }

        if ($statusFilter === 'enabled') {
            $items = array_values(array_filter($items, fn($i) => $i['enabled']));
        } elseif ($statusFilter === 'disabled') {
            $items = array_values(array_filter($items, fn($i) => !$i['enabled']));
        }

        $grouped = [];

        foreach ($items as $item) {
            $grouped[$item['category']][] = $item;
        }

        $categoryOrder = array_keys($categories);

        uksort($grouped, function ($a, $b) use ($categoryOrder) {
            $ai = array_search($a, $categoryOrder, true);
            $bi = array_search($b, $categoryOrder, true);
            if ($ai === false) $ai = 999;
            if ($bi === false) $bi = 999;
            return $ai <=> $bi;
        });

        $counts = [
            'total' => count($widgets),
            'enabled' => count(array_filter($items, fn($i) => $i['enabled'])),
            'disabled' => count(array_filter($items, fn($i) => !$i['enabled'])),
            'builtin' => count(array_filter($widgets, fn($w) => $this->detectSource($w) === 'builtin')),
            'plugin' => count(array_filter($widgets, fn($w) => $this->detectSource($w) === 'plugin')),
            'uploaded' => count(array_filter($widgets, fn($w) => $this->detectSource($w) === 'uploaded')),
        ];

        $sources = [
            'builtin' => __('Core'),
            'plugin' => __('Plugin'),
            'uploaded' => __('Uploaded'),
            'custom' => __('Custom'),
        ];

        return view('admin.appearance.widgets', compact(
            'grouped',
            'categories',
            'contexts',
            'counts',
            'sources',
            'q',
            'catFilter',
            'srcFilter',
            'statusFilter'
        ));
    }

    public function toggle(Request $request, string $id)
    {
        $enabled = $request->boolean('enabled');

        rj_widget_save_settings($id, ['enabled' => $enabled]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'enabled' => $enabled,
                'message' => $enabled ? __('Widget enabled.') : __('Widget disabled.'),
            ]);
        }

        return back()->with('success', $enabled ? __('Widget enabled.') : __('Widget disabled.'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'category' => 'nullable|string|max:50',
            'contexts' => 'nullable|array',
            'contexts.*' => 'string|in:page,header,footer,popup,block',
        ]);

        $data = [];

        if (!empty($validated['category'])) {
            $data['category'] = $validated['category'];
        }

        if (isset($validated['contexts'])) {
            $data['contexts'] = array_values($validated['contexts']);
        }

        if (!empty($data)) {
            rj_widget_save_settings($id, $data);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Widget updated.'),
            ]);
        }

        return back()->with('success', __('Widget updated.'));
    }

    public function reset(Request $request, string $id)
    {
        rj_widget_reset_settings($id);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Widget reset to defaults.'),
            ]);
        }

        return back()->with('success', __('Widget reset to defaults.'));
    }

    public function bulk(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|string',
            'action' => 'required|in:enable,disable,reset',
        ]);

        $count = 0;

        foreach ($validated['ids'] as $id) {
            if ($validated['action'] === 'reset') {
                rj_widget_reset_settings($id);
            } else {
                rj_widget_save_settings($id, ['enabled' => $validated['action'] === 'enable']);
            }

            $count++;
        }

        return response()->json([
            'success' => true,
            'count' => $count,
            'message' => __(':n widget(s) updated.', ['n' => $count]),
        ]);
    }

    protected function detectSource($widget): string
    {
        $class = get_class($widget);

        if (str_starts_with($class, 'RjPlugins\\')) {
            return 'plugin';
        }

        if (str_starts_with($class, 'RjWidgets\\')) {
            return 'uploaded';
        }

        if (str_starts_with($class, 'App\\Widgets\\Builtin\\')) {
            return 'builtin';
        }

        return 'custom';
    }
}
