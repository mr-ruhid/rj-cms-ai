<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GitHubRegistry
{
    protected string $registryUrl;
    protected ?string $token;
    protected int $cacheMinutes;

    public function __construct()
    {
        $this->registryUrl = rj_get_option('registry_url', 'https://raw.githubusercontent.com/mr-ruhid/rj_cms_theme_and_plugin_list/main/registry.json');
        $this->token = rj_get_option('github_token') ?: null;
        $this->cacheMinutes = (int) rj_get_option('registry_cache_minutes', 60);
    }

    public function fetch(): array
    {
        return Cache::remember('rj_registry_json', $this->cacheMinutes * 60, function () {
            try {
                $res = Http::timeout(15)
                    ->withHeaders(['User-Agent' => 'RJ-CMS'])
                    ->get($this->registryUrl);

                if (!$res->successful()) {
                    return ['themes' => [], 'plugins' => [], 'error' => 'Registry HTTP ' . $res->status()];
                }

                $json = $res->json();

                if (!is_array($json)) {
                    return ['themes' => [], 'plugins' => [], 'error' => 'Invalid JSON'];
                }

                return [
                    'themes' => $json['themes'] ?? [],
                    'plugins' => $json['plugins'] ?? [],
                    'updated_at' => $json['updated_at'] ?? null,
                    'error' => null,
                ];
            } catch (\Throwable $e) {
                return ['themes' => [], 'plugins' => [], 'error' => $e->getMessage()];
            }
        });
    }

    public function themes(): array
    {
        return $this->fetch()['themes'] ?? [];
    }

    public function plugins(): array
    {
        return $this->fetch()['plugins'] ?? [];
    }

    public function enrichThemes(): array
    {
        $entries = $this->themes();
        $out = [];

        foreach ($entries as $entry) {
            $enriched = $this->enrichEntry($entry);

            if ($enriched) {
                $out[] = $enriched;
            }
        }

        return $out;
    }

    public function enrichPlugins(): array
    {
        $entries = $this->plugins();
        $out = [];

        foreach ($entries as $entry) {
            $enriched = $this->enrichEntry($entry);

            if ($enriched) {
                $out[] = $enriched;
            }
        }

        return $out;
    }

    protected function enrichEntry(array $entry): ?array
    {
        $repo = $entry['repo'] ?? null;

        if (!$repo || !preg_match('#^[^/]+/[^/]+$#', $repo)) {
            return null;
        }

        $meta = Cache::remember('rj_repo_' . md5($repo), 1800, function () use ($repo) {
            return $this->fetchRepoMetadata($repo);
        });

        if (!$meta) {
            return null;
        }

        return array_merge($meta, [
            'verified' => (bool) ($entry['verified'] ?? false),
            'featured' => (bool) ($entry['featured'] ?? false),
            'category' => $entry['category'] ?? 'general',
            'tags' => $entry['tags'] ?? [],
        ]);
    }

    protected function fetchRepoMetadata(string $repo): ?array
    {
        try {
            $headers = [
                'Accept' => 'application/vnd.github+json',
                'User-Agent' => 'RJ-CMS',
            ];

            if ($this->token) {
                $headers['Authorization'] = 'Bearer ' . $this->token;
            }

            $releaseRes = Http::timeout(15)->withHeaders($headers)
                ->get("https://api.github.com/repos/{$repo}/releases/latest");

            if (!$releaseRes->successful()) {
                return null;
            }

            $release = $releaseRes->json();
            $tag = $release['tag_name'] ?? null;

            if (!$tag) {
                return null;
            }

            $zip = null;
            $size = 0;

            foreach ($release['assets'] ?? [] as $asset) {
                if (str_ends_with(strtolower($asset['name'] ?? ''), '.zip')) {
                    $zip = $asset['browser_download_url'] ?? null;
                    $size = (int) ($asset['size'] ?? 0);
                    break;
                }
            }

            if (!$zip) {
                return null;
            }

            $repoRes = Http::timeout(15)->withHeaders($headers)
                ->get("https://api.github.com/repos/{$repo}");

            $repoData = $repoRes->successful() ? $repoRes->json() : [];

            $manifest = $this->fetchManifest($repo, $repoData['default_branch'] ?? 'main', $headers);

            return [
                'repo' => $repo,
                'owner' => explode('/', $repo)[0],
                'name' => $manifest['name'] ?? ($repoData['name'] ?? $repo),
                'slug' => $manifest['slug'] ?? strtolower($repoData['name'] ?? $repo),
                'description' => $manifest['description'] ?? ($repoData['description'] ?? ''),
                'author' => $manifest['author'] ?? ($repoData['owner']['login'] ?? ''),
                'author_url' => $manifest['author_url'] ?? ($repoData['owner']['html_url'] ?? ''),
                'version' => ltrim($tag, 'vV'),
                'tag' => $tag,
                'zip_url' => $zip,
                'size' => $size,
                'html_url' => $repoData['html_url'] ?? "https://github.com/{$repo}",
                'stars' => (int) ($repoData['stargazers_count'] ?? 0),
                'updated_at' => $repoData['updated_at'] ?? null,
                'screenshot' => $this->resolveScreenshotUrl($repo, $repoData['default_branch'] ?? 'main', $manifest),
                'requires' => $manifest['requires'] ?? [],
                'supports' => $manifest['supports'] ?? [],
            ];
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function fetchManifest(string $repo, string $branch, array $headers): array
    {
        foreach (['theme.json', 'plugin.json'] as $file) {
            try {
                $res = Http::timeout(10)->withHeaders($headers)
                    ->get("https://raw.githubusercontent.com/{$repo}/{$branch}/{$file}");

                if ($res->successful()) {
                    $json = $res->json();

                    if (is_array($json)) {
                        return $json;
                    }
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        return [];
    }

    protected function resolveScreenshotUrl(string $repo, string $branch, array $manifest): ?string
    {
        $screenshot = $manifest['screenshot'] ?? 'screenshot.png';

        if (preg_match('#^https?://#', $screenshot)) {
            return $screenshot;
        }

        return "https://raw.githubusercontent.com/{$repo}/{$branch}/" . ltrim($screenshot, '/');
    }

    public function clearCache(): void
    {
        Cache::forget('rj_registry_json');

        foreach ($this->themes() as $entry) {
            if (!empty($entry['repo'])) {
                Cache::forget('rj_repo_' . md5($entry['repo']));
            }
        }

        foreach ($this->plugins() as $entry) {
            if (!empty($entry['repo'])) {
                Cache::forget('rj_repo_' . md5($entry['repo']));
            }
        }
    }

    public function hasToken(): bool
    {
        return !empty($this->token);
    }
}
