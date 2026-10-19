<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ContentExportService;
use Illuminate\Http\Request;

class ToolsExportController extends Controller
{
    protected ContentExportService $service;

    public function __construct(ContentExportService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $available = $this->service->getAvailableOptions();

        return view('admin.tools.export', compact('available'));
    }

    public function run(Request $request)
    {
        $validated = $request->validate([
            'format' => 'required|in:json,wxr,csv',
            'items' => 'required|array|min:1',
            'items.*' => 'string',
        ]);

        $available = $this->service->getAvailableOptions();
        $allowed = array_keys($available);

        $selected = [];

        foreach ($validated['items'] as $key) {
            if (in_array($key, $allowed, true)) {
                $selected[$key] = true;
            }
        }

        if (empty($selected)) {
            return back()->with('error', __('Select at least one content type to export.'));
        }

        try {
            $result = $this->service->export($selected, $validated['format']);

            return response($result['content'], 200, [
                'Content-Type' => $result['mime'],
                'Content-Disposition' => 'attachment; filename="' . $result['filename'] . '"',
                'Cache-Control' => 'no-store, no-cache',
            ]);
        } catch (\Throwable $e) {
            return back()->with('error', __('Export failed: :msg', ['msg' => $e->getMessage()]));
        }
    }
}