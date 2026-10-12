<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;

class UpdatesController extends Controller
{
    protected string $repo;
    protected string $currentVersion;

    public function __construct()
    {
        $this->repo = rj_get_option('update_repo', 'mr-ruhid/rj-cms-ai');
        $this->currentVersion = rj_get_option('rj_version', '1.0.0');
    }

    public function index()
    {
        $options = Option::where('option_group', 'updates')
            ->pluck('option_value', 'option_name')
            ->toArray();

        $data = [
            'currentVersion' => $this->currentVersion,
            'lastChecked' => $options['last_checked'] ?? null,
            'lastRelease' => $this->getCachedRelease(),
        ];

        return view('admin.updates.index', compact('options', 'data'));
    }

    public function check()
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/vnd.github+json',
                'User-Agent' => 'RJ-CMS-Updater',
            ])
                ->timeout(15)
                ->get("https://api.github.com/repos/{$this->repo}/releases/latest");

            if (!$response->successful()) {
                return back()->with('error', __('Could not reach update server. HTTP :code', ['code' => $response->status()]));
            }

            $release = $response->json();

            if (empty($release['tag_name'])) {
                return back()->with('error', __('No releases found.'));
            }

            $latest = ltrim($release['tag_name'], 'v');
            $hasUpdate = version_compare($latest, $this->currentVersion, '>');

            Option::set('last_checked', now()->toDateTimeString(), 'updates');
            Option::set('last_release', json_encode([
                'version' => $latest,
                'name' => $release['name'] ?? $release['tag_name'],
                'body' => $release['body'] ?? '',
                'url' => $release['html_url'] ?? '',
                'published_at' => $release['published_at'] ?? null,
                'has_update' => $hasUpdate,
            ]), 'updates');

            if ($hasUpdate) {
                return back()->with('success', __('New version available: :version', ['version' => $latest]));
            }

            return back()->with('success', __('You are running the latest version.'));
        } catch (\Throwable $e) {
            return back()->with('error', __('Update check failed: :message', ['message' => $e->getMessage()]));
        }
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'update_repo' => ['required', 'string', 'max:255', 'regex:/^[\w\-\.]+\/[\w\-\.]+$/'],
            'auto_check' => ['nullable', 'boolean'],
        ]);

        $data['auto_check'] = $request->boolean('auto_check') ? '1' : '0';

        foreach ($data as $name => $value) {
            Option::set($name, $value, 'updates');
        }

        return back()->with('success', __('Update settings saved.'));
    }

    public function run()
    {
        $release = $this->getCachedRelease();

        if (empty($release) || empty($release['has_update'])) {
            return back()->with('error', __('No update available to install.'));
        }

        try {
            Artisan::call('optimize:clear');

            return back()->with('success', __('Update prepared. Manual installation required for version :version.', ['version' => $release['version']]));
        } catch (\Throwable $e) {
            return back()->with('error', __('Update failed: :message', ['message' => $e->getMessage()]));
        }
    }

    protected function getCachedRelease(): ?array
    {
        $raw = rj_get_option('last_release');

        if (!$raw) {
            return null;
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : null;
    }
}