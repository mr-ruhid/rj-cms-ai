<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LogViewerService;
use Illuminate\Http\Request;

class ToolsLogsController extends Controller
{
    protected LogViewerService $logViewer;

    public function __construct(LogViewerService $logViewer)
    {
        $this->logViewer = $logViewer;
    }

    public function index()
    {
        $files = $this->logViewer->getFiles();

        return view('admin.tools.logs', compact('files'));
    }

    public function fetch(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|string',
            'limit' => 'nullable|integer|min:50|max:2000',
        ]);

        $limit = $validated['limit'] ?? 500;
        $entries = $this->logViewer->getEntries($validated['file'], $limit);

        $counts = [
            'EMERGENCY' => 0,
            'ALERT' => 0,
            'CRITICAL' => 0,
            'ERROR' => 0,
            'WARNING' => 0,
            'NOTICE' => 0,
            'INFO' => 0,
            'DEBUG' => 0,
        ];

        foreach ($entries as $entry) {
            if (isset($counts[$entry['level']])) {
                $counts[$entry['level']]++;
            }
        }

        return response()->json([
            'success' => true,
            'entries' => $entries,
            'counts' => $counts,
            'total_loaded' => count($entries),
        ]);
    }

    public function clear(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|string',
        ]);

        $result = $this->logViewer->clear($validated['file']);

        if ($request->expectsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function download(string $filename)
    {
        return $this->logViewer->download($filename);
    }
}