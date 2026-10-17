<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plugin;
use App\Services\GitHubRegistry;
use App\Services\PluginManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PluginController extends Controller
{
    protected PluginManager $manager;

    public function __construct(PluginManager $manager)
    {
        $this->manager = $manager;
    }

    public function index(Request $request)
    {
        $this->manager->discover();

        $query = Plugin::with('activator')->orderBy('name');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('slug', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $plugins = $query->get();

        $counts = [
            'all' => Plugin::count(),
            'active' => Plugin::where('status', 'active')->count(),
            'inactive' => Plugin::where('status', 'inactive')->count(),
            'error' => Plugin::where('status', 'error')->count(),
            'updates' => $this->manager->updatesAvailable(),
        ];

        return view('admin.plugins.index', compact('plugins', 'counts'));
    }

    public function browse(Request $request)
    {
        $registry = app(GitHubRegistry::class);

        $plugins = $registry->enrichPlugins();

        $search = strtolower(trim((string) $request->input('q', '')));
        $category = $request->input('category', '');

        if ($search !== '') {
            $plugins = array_values(array_filter($plugins, function ($p) use ($search) {
                return str_contains(strtolower($p['name'] ?? ''), $search)
                    || str_contains(strtolower($p['description'] ?? ''), $search)
                    || str_contains(strtolower($p['author'] ?? ''), $search)
                    || str_contains(strtolower($p['repo'] ?? ''), $search);
            }));
        }

        if ($category !== '') {
            $plugins = array_values(array_filter($plugins, fn($p) => ($p['category'] ?? '') === $category));
        }

        usort($plugins, function ($a, $b) {
            if (($a['featured'] ?? false) !== ($b['featured'] ?? false)) {
                return ($b['featured'] ?? false) <=> ($a['featured'] ?? false);
            }
            if (($a['verified'] ?? false) !== ($b['verified'] ?? false)) {
                return ($b['verified'] ?? false) <=> ($a['verified'] ?? false);
            }
            return strcasecmp($a['name'] ?? '', $b['name'] ?? '');
        });

        $installedSlugs = Plugin::pluck('slug')->toArray();

        $categories = [
            'seo' => __('SEO'),
            'analytics' => __('Analytics'),
            'forms' => __('Forms'),
            'ecommerce' => __('E-commerce'),
            'social' => __('Social'),
            'security' => __('Security'),
            'performance' => __('Performance'),
            'marketing' => __('Marketing'),
            'utilities' => __('Utilities'),
        ];

        $registryError = $registry->fetch()['error'] ?? null;

        return view('admin.plugins.browse', compact(
            'plugins',
            'installedSlugs',
            'categories',
            'category',
            'search',
            'registryError'
        ));
    }

    public function installFromGitHub(Request $request)
    {
        $validated = $request->validate([
            'repo' => 'required|string|regex:#^[^/]+/[^/]+$#',
            'zip_url' => 'required|url',
            'slug' => 'required|string|max:100',
        ]);

        if (Plugin::where('slug', $validated['slug'])->exists()) {
            $message = __('Plugin already installed.');

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        try {
            $tmp = tempnam(sys_get_temp_dir(), 'rj_plugin_');

            $response = Http::timeout(120)
                ->withOptions(['sink' => $tmp])
                ->get($validated['zip_url']);

            if (!$response->successful()) {
                @unlink($tmp);
                $message = __('Could not download plugin package.');

                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return back()->with('error', $message);
            }

            if (filesize($tmp) > 50 * 1024 * 1024) {
                @unlink($tmp);
                $message = __('Plugin package exceeds 50 MB limit.');

                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return back()->with('error', $message);
            }

            $file = new \Illuminate\Http\UploadedFile($tmp, 'plugin.zip', 'application/zip', null, true);

            $result = $this->manager->installFromZip($file);

            @unlink($tmp);

            $result['slug'] = $validated['slug'];

            if ($request->expectsJson()) {
                return response()->json($result, $result['success'] ? 200 : 422);
            }

            if ($result['success']) {
                return redirect()->route('admin.plugins.index')->with('success', $result['message']);
            }

            return back()->with('error', $result['message']);
        } catch (\Throwable $e) {
            $message = __('Installation failed: :msg', ['msg' => $e->getMessage()]);

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return back()->with('error', $message);
        }
    }

    public function show(Plugin $plugin)
    {
        $plugin->load('activator');

        $metadata = $plugin->metadata();
        $requires = $plugin->requires();

        return view('admin.plugins.show', compact('plugin', 'metadata', 'requires'));
    }

    public function activate(Request $request, Plugin $plugin)
    {
        $result = $this->manager->activate($plugin);

        if ($request->expectsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    public function deactivate(Request $request, Plugin $plugin)
    {
        $result = $this->manager->deactivate($plugin);

        if ($request->expectsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    public function destroy(Request $request, Plugin $plugin)
    {
        if ($plugin->isActive()) {
            $message = __('Deactivate the plugin before deleting.');

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        $result = $this->manager->delete($plugin);

        if ($request->expectsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        if ($result['success']) {
            return redirect()
                ->route('admin.plugins.index')
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
                ->route('admin.plugins.index')
                ->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    public function settings(Plugin $plugin)
    {
        $schema = $plugin->metadata()['settings_schema'] ?? [];

        $values = $plugin->settings();

        return view('admin.plugins.settings', compact('plugin', 'schema', 'values'));
    }

    public function updateSettings(Request $request, Plugin $plugin)
    {
        $validated = $request->validate([
            'settings' => 'nullable|array',
        ]);

        $plugin->update(['settings' => $validated['settings'] ?? []]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', __('Plugin settings saved.'));
    }

    public function checkUpdates(Request $request)
    {
        $result = $this->manager->checkForUpdates();

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        return back()->with('success', __(':n update(s) available.', ['n' => $result['count']]));
    }

    public function update(Request $request, Plugin $plugin)
    {
        $result = $this->manager->update($plugin);

        if ($request->expectsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    public function bulk(Request $request)
    {
        $validated = $request->validate([
            'slugs' => 'required|array',
            'slugs.*' => 'string|exists:rj_plugins,slug',
            'action' => 'required|in:activate,deactivate,delete',
        ]);

        $result = $this->manager->bulkAction($validated['slugs'], $validated['action']);

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        return back()->with('success', $result['message']);
    }
}