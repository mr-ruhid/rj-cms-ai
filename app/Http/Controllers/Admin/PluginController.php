<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plugin;
use App\Services\PluginManager;
use Illuminate\Http\Request;

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
            'file' => 'required|file|mimes:zip|max:20480',
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