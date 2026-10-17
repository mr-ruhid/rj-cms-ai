<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plugin;
use App\Services\AiCodeReviewerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PluginEditorController extends Controller
{
    protected array $allowedExtensions = ['php', 'js', 'css', 'json', 'md', 'txt', 'html', 'svg'];

    public function index()
    {
        $plugins = Plugin::where('status', 'active')->get();
        return view('admin.plugins.editor', compact('plugins'));
    }

    public function files($slug)
    {
        $plugin = Plugin::where('slug', $slug)->firstOrFail();
        $basePath = $plugin->path();
        
        if (!File::isDirectory($basePath)) {
            return response()->json(['error' => 'Plugin directory not found at: ' . $basePath], 404);
        }

        $tree = [];
        $allFiles = File::allFiles($basePath);

        foreach ($allFiles as $file) {
            $relativePath = str_replace('\\', '/', $file->getRelativePathname());
            
            if (str_contains($relativePath, 'vendor/') || str_contains($relativePath, 'node_modules/')) {
                continue;
            }

            $ext = $file->getExtension();
            if (!in_array($ext, $this->allowedExtensions) && !str_ends_with($relativePath, '.blade.php')) {
                continue;
            }

            $tree[] = [
                'path' => $relativePath,
                'name' => $file->getFilename(),
                'dir' => str_replace('\\', '/', $file->getRelativePath()),
            ];
        }

        usort($tree, fn($a, $b) => strcmp($a['path'], $b['path']));

        return response()->json(['files' => $tree]);
    }

    public function read($slug, Request $request)
    {
        try {
            $plugin = Plugin::where('slug', $slug)->firstOrFail();
            $path = $this->resolveSafePath($plugin, $request->input('path'));
            
            if (!$path || !File::exists($path)) {
                return response()->json(['error' => 'Fayl tapılmadı.'], 404);
            }

            return response()->json([
                'content' => File::get($path),
                'path' => $request->input('path'),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Xəta: ' . $e->getMessage()], 500);
        }
    }

    public function save($slug, Request $request)
    {
        $plugin = Plugin::where('slug', $slug)->firstOrFail();
        $validated = $request->validate(['path' => 'required|string', 'content' => 'nullable|string']);
        $path = $this->resolveSafePath($plugin, $validated['path']);

        if (!$path || !File::exists($path)) {
            return response()->json(['error' => 'Yanlış fayl yolu.'], 403);
        }

        try {
            File::put($path, $validated['content'] ?? '');
            return response()->json(['success' => true, 'message' => __('Fayl yadda saxlanıldı.')]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function review(Request $request)
    {
        $validated = $request->validate([
            'filename' => 'required|string',
            'code' => 'required|string',
        ]);

        $service = app(AiCodeReviewerService::class);
        if (!$service->enabled()) {
            return response()->json(['success' => false, 'error' => __('AI konfiqurasiya edilməyib.')], 422);
        }

        return response()->json($service->review($validated['code'], $validated['filename']));
    }

    protected function resolveSafePath(Plugin $plugin, ?string $relativePath): ?string
    {
        if (!$relativePath || str_contains($relativePath, '..')) return null;

        $basePath = str_replace('\\', '/', $plugin->path());
        $relativePath = str_replace('\\', '/', $relativePath);
        $relativePath = ltrim($relativePath, '/');
        
        $fullPath = $basePath . '/' . $relativePath;
        
        if (!str_starts_with($fullPath, $basePath)) return null;

        return $fullPath;
    }
}