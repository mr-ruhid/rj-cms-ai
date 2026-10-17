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

    public function files(Plugin $plugin)
    {
        $basePath = $plugin->path();
        if (!File::isDirectory($basePath)) {
            return response()->json(['error' => 'Plugin directory not found at: ' . $basePath], 404);
        }

        $tree = [];
        
        // Laravel-in native File sistemi ilə bütün faylları oxuyuruq
        $allFiles = File::allFiles($basePath);

        foreach ($allFiles as $file) {
            $relativePath = str_replace('\\', '/', $file->getRelativePathname());
            
            // Vendor və node_modules qovluqlarını keç
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

    public function read(Plugin $plugin, Request $request)
    {
        $path = $this->resolveSafePath($plugin, $request->input('path'));
        if (!$path || !File::exists($path)) return response()->json(['error' => 'File not found or invalid path.'], 404);

        return response()->json([
            'content' => File::get($path),
            'path' => $request->input('path'),
        ]);
    }

    public function save(Plugin $plugin, Request $request)
    {
        $validated = $request->validate(['path' => 'required|string', 'content' => 'nullable|string']);
        $path = $this->resolveSafePath($plugin, $validated['path']);

        if (!$path || !File::exists($path)) return response()->json(['error' => 'Invalid file path.'], 403);

        try {
            File::put($path, $validated['content'] ?? '');
            return response()->json(['success' => true, 'message' => __('File saved.')]);
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
            return response()->json(['success' => false, 'error' => __('AI is not configured. Go to Settings → AI.')], 422);
        }

        $result = $service->review($validated['code'], $validated['filename']);

        return response()->json($result);
    }

    protected function resolveSafePath(Plugin $plugin, ?string $relativePath): ?string
    {
        if (!$relativePath || str_contains($relativePath, '..')) return null;

        $basePath = realpath($plugin->path());
        if (!$basePath) return null;

        $fullPath = realpath($basePath . '/' . ltrim($relativePath, '/\\'));

        if (!$fullPath) return null;

        // Windows üçün slash normallaşdırması
        $normBase = str_replace('\\', '/', $basePath);
        $normFull = str_replace('\\', '/', $fullPath);

        if (!str_starts_with($normFull, $normBase)) return null;

        return $fullPath;
    }
}