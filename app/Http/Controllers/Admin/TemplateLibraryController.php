<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Services\TemplateLibraryService;
use Illuminate\Http\Request;

class TemplateLibraryController extends Controller
{
    protected TemplateLibraryService $service;

    public function __construct(TemplateLibraryService $service)
    {
        $this->service = $service;
    }

    public function export(Template $template)
    {
        $data = $this->service->exportJson($template);
        $filename = 'rj-template-' . $template->slug . '-' . now()->format('Y-m-d') . '.json';

        return response($data, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportMultiple(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:rj_templates,id',
        ]);

        $data = $this->service->exportMultiple($validated['ids']);
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $filename = 'rj-template-pack-' . now()->format('Y-m-d-H-i') . '.json';

        return response($json, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:json,txt|max:10240',
            'prefix' => 'nullable|string|max:100',
            'overwrite' => 'nullable|boolean',
        ]);

        $content = file_get_contents($validated['file']->getRealPath());
        $data = json_decode($content, true);

        if (!is_array($data)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => __('Invalid JSON file.')], 422);
            }
            return back()->with('error', __('Invalid JSON file.'));
        }

        try {
            $stats = $this->service->import($data, [
                'prefix' => $validated['prefix'] ?? '',
                'overwrite' => !empty($validated['overwrite']),
            ]);
        } catch (\Throwable $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->with('error', $e->getMessage());
        }

        $message = __(':c created, :u updated, :s skipped.', [
            'c' => $stats['created'],
            'u' => $stats['updated'],
            's' => $stats['skipped'],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'stats' => $stats,
            ]);
        }

        return redirect()
            ->route('admin.rjbuilder.index')
            ->with('success', $message);
    }

    public function preview(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:json,txt|max:10240',
        ]);

        $content = file_get_contents($validated['file']->getRealPath());
        $data = json_decode($content, true);

        if (!is_array($data)) {
            return response()->json(['success' => false, 'message' => __('Invalid JSON file.')], 422);
        }

        $format = $data['format'] ?? '';
        $preview = [
            'format' => $format,
            'version' => $data['version'] ?? '?',
            'site' => $data['site'] ?? null,
            'templates' => [],
        ];

        if ($format === 'rj-template' && !empty($data['template'])) {
            $preview['templates'][] = [
                'name' => $data['template']['name'] ?? '',
                'slug' => $data['template']['slug'] ?? '',
                'type' => $data['template']['type'] ?? '',
                'blocks' => count($data['template']['content']['blocks'] ?? []),
            ];
        }

        if ($format === 'rj-template-pack' && !empty($data['templates'])) {
            foreach ($data['templates'] as $tpl) {
                $preview['templates'][] = [
                    'name' => $tpl['name'] ?? '',
                    'slug' => $tpl['slug'] ?? '',
                    'type' => $tpl['type'] ?? '',
                    'blocks' => count($tpl['content']['blocks'] ?? []),
                ];
            }
        }

        return response()->json(['success' => true, 'preview' => $preview]);
    }
}