<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AppearanceColorsController extends Controller
{
    public function index()
    {
        $colors = rj_global_colors();

        if (empty($colors)) {
            $colors = rj_default_global_colors();
        }

        $defaults = rj_default_global_colors();

        return view('admin.appearance.colors', compact('colors', 'defaults'));
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'colors' => 'required|array|min:1',
            'colors.*.id' => 'required|string|max:50|regex:/^[a-z0-9_-]+$/i',
            'colors.*.name' => 'required|string|max:80',
            'colors.*.color' => 'required|string|max:30|regex:/^#?[0-9a-fA-F]{3,8}$|^rgb|^hsl/',
        ]);

        $ids = array_column($validated['colors'], 'id');

        if (count($ids) !== count(array_unique($ids))) {
            return response()->json([
                'success' => false,
                'message' => __('Color IDs must be unique.'),
            ], 422);
        }

        rj_save_global_colors($validated['colors']);

        return response()->json([
            'success' => true,
            'message' => __('Colors saved.'),
            'colors' => rj_global_colors(),
        ]);
    }

    public function reset(Request $request)
    {
        rj_reset_global_colors();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Colors reset to defaults.'),
                'colors' => rj_default_global_colors(),
            ]);
        }

        return back()->with('success', __('Colors reset to defaults.'));
    }

    public function export()
    {
        $colors = rj_global_colors();

        if (empty($colors)) {
            $colors = rj_default_global_colors();
        }

        $payload = [
            'version' => 1,
            'exported_at' => now()->toIso8601String(),
            'site' => config('app.name'),
            'colors' => $colors,
        ];

        $filename = 'rj-colors-' . now()->format('Y-m-d-His') . '.json';

        return response()->json($payload, 200, [
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:json|max:512',
        ]);

        try {
            $content = file_get_contents($validated['file']->getRealPath());
            $data = json_decode($content, true);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('Could not read file.'),
            ], 422);
        }

        if (!is_array($data)) {
            return response()->json([
                'success' => false,
                'message' => __('Invalid JSON.'),
            ], 422);
        }

        $colors = $data['colors'] ?? $data;

        if (!is_array($colors) || empty($colors)) {
            return response()->json([
                'success' => false,
                'message' => __('No colors found in file.'),
            ], 422);
        }

        $clean = [];

        foreach ($colors as $c) {
            $id = preg_replace('/[^a-z0-9_-]/i', '', (string) ($c['id'] ?? ''));
            $name = trim((string) ($c['name'] ?? ''));
            $color = trim((string) ($c['color'] ?? ''));

            if ($id === '' || $name === '' || $color === '') {
                continue;
            }

            $clean[] = ['id' => $id, 'name' => $name, 'color' => $color];
        }

        if (empty($clean)) {
            return response()->json([
                'success' => false,
                'message' => __('No valid colors found.'),
            ], 422);
        }

        rj_save_global_colors($clean);

        return response()->json([
            'success' => true,
            'message' => __(':n color(s) imported.', ['n' => count($clean)]),
            'colors' => rj_global_colors(),
        ]);
    }
}