<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ContentImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ToolsImportController extends Controller
{
    protected ContentImportService $service;

    public function __construct(ContentImportService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $pending = $this->pendingPreview();

        return view('admin.tools.import', compact('pending'));
    }

    public function preview(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:json,xml,csv,txt|max:20480',
        ]);

        $file = $validated['file'];
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension === 'txt') {
            $extension = 'json';
        }

        try {
            $parsed = $this->service->parseFile($file->getRealPath(), $extension);
            $summary = $this->service->preview($parsed);

            $token = Str::uuid()->toString();
            $storagePath = 'imports/' . $token . '.' . $extension;

            Storage::disk('local')->put($storagePath, file_get_contents($file->getRealPath()));

            session([
                'rj_import_preview' => [
                    'token' => $token,
                    'path' => $storagePath,
                    'extension' => $extension,
                    'filename' => $file->getClientOriginalName(),
                    'summary' => $summary,
                    'created_at' => now()->toIso8601String(),
                ],
            ]);

            return redirect()
                ->route('admin.tools.import')
                ->with('success', __('File analyzed. Review the summary below and confirm import.'));
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function run(Request $request)
    {
        $validated = $request->validate([
            'strategy' => 'required|in:skip,update',
        ]);

        $pending = session('rj_import_preview');

        if (!$pending || empty($pending['path'])) {
            return redirect()
                ->route('admin.tools.import')
                ->with('error', __('No import in progress. Please upload a file first.'));
        }

        $fullPath = Storage::disk('local')->path($pending['path']);

        if (!File::exists($fullPath)) {
            session()->forget('rj_import_preview');

            return redirect()
                ->route('admin.tools.import')
                ->with('error', __('Uploaded file is no longer available. Please re-upload.'));
        }

        try {
            $parsed = $this->service->parseFile($fullPath, $pending['extension']);
            $result = $this->service->run($parsed, $validated['strategy']);

            File::delete($fullPath);
            session()->forget('rj_import_preview');

            $stats = $result['stats'];
            $totalCreated = array_sum(array_column($stats, 'created'));
            $totalUpdated = array_sum(array_column($stats, 'updated'));

            return redirect()
                ->route('admin.tools.import')
                ->with('import_result', [
                    'stats' => $stats,
                    'warnings' => $result['warnings'],
                    'created' => $totalCreated,
                    'updated' => $totalUpdated,
                ]);
        } catch (\Throwable $e) {
            return back()->with('error', __('Import failed: :msg', ['msg' => $e->getMessage()]));
        }
    }

    public function cancel(Request $request)
    {
        $pending = session('rj_import_preview');

        if ($pending && !empty($pending['path'])) {
            $fullPath = Storage::disk('local')->path($pending['path']);

            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }
        }

        session()->forget('rj_import_preview');

        return redirect()
            ->route('admin.tools.import')
            ->with('success', __('Import cancelled.'));
    }

    protected function pendingPreview(): ?array
    {
        $pending = session('rj_import_preview');

        if (!$pending) {
            return null;
        }

        if (isset($pending['created_at'])) {
            $created = \Carbon\Carbon::parse($pending['created_at']);

            if ($created->diffInMinutes(now()) > 60) {
                session()->forget('rj_import_preview');
                return null;
            }
        }

        return $pending;
    }
}