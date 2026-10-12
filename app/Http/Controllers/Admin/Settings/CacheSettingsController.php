<?php

namespace App\Http\Controllers\Admin\Settings;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use App\Models\Option;

class CacheSettingsController extends BaseSettingsController
{
    public function index()
    {
        $options = $this->getGroup('cache');

        $data = [
            'drivers' => $this->drivers(),
            'ttlOptions' => $this->ttlOptions(),
            'cacheInfo' => $this->cacheInfo(),
        ];

        return view('admin.settings.cache', compact('options', 'data'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'cache_enabled' => ['nullable', 'boolean'],
            'cache_driver' => ['required', 'in:file,redis,memcached,database,array'],
            'cache_ttl' => ['required', 'integer', 'min:60', 'max:604800'],
            'query_cache' => ['nullable', 'boolean'],
            'view_cache' => ['nullable', 'boolean'],
            'opcache_enabled' => ['nullable', 'boolean'],

            'browser_cache' => ['nullable', 'boolean'],
            'browser_cache_ttl' => ['nullable', 'integer', 'min:0', 'max:31536000'],
            'gzip_enabled' => ['nullable', 'boolean'],

            'cdn_enabled' => ['nullable', 'boolean'],
            'cdn_url' => ['nullable', 'url', 'max:255'],
        ]);

        $booleanKeys = [
            'cache_enabled',
            'query_cache',
            'view_cache',
            'opcache_enabled',
            'browser_cache',
            'gzip_enabled',
            'cdn_enabled',
        ];

        foreach ($booleanKeys as $key) {
            $data[$key] = $request->boolean($key) ? '1' : '0';
        }

        $typeMap = [
            'cache_ttl' => 'number',
            'browser_cache_ttl' => 'number',
        ];

        foreach ($data as $name => $value) {
            Option::set($name, $value, 'cache', $typeMap[$name] ?? 'text');
        }

        return back()->with('success', __('Cache settings saved.'));
    }

    public function clear(string $type)
    {
        $allowed = ['data', 'views', 'routes', 'config', 'all'];

        if (!in_array($type, $allowed, true)) {
            return back()->with('error', __('Invalid cache type.'));
        }

        try {
            match ($type) {
                'data' => Cache::flush(),
                'views' => Artisan::call('view:clear'),
                'routes' => Artisan::call('route:clear'),
                'config' => Artisan::call('config:clear'),
                'all' => $this->clearAll(),
            };

            return back()->with('success', __('Cache cleared: :type', ['type' => ucfirst($type)]));
        } catch (\Throwable $e) {
            return back()->with('error', __('Failed to clear cache: :message', ['message' => $e->getMessage()]));
        }
    }

    protected function clearAll(): void
    {
        Cache::flush();
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('optimize:clear');
    }

    protected function drivers(): array
    {
        return [
            'file' => __('File System') . ' — ' . __('Simple, no setup required'),
            'database' => __('Database') . ' — ' . __('Uses database table'),
            'redis' => __('Redis') . ' — ' . __('Fast, requires Redis server'),
            'memcached' => __('Memcached') . ' — ' . __('Fast, requires Memcached server'),
            'array' => __('Array') . ' — ' . __('In-memory, per request only'),
        ];
    }

    protected function ttlOptions(): array
    {
        return [
            300 => __('5 minutes'),
            900 => __('15 minutes'),
            1800 => __('30 minutes'),
            3600 => __('1 hour'),
            21600 => __('6 hours'),
            43200 => __('12 hours'),
            86400 => __('1 day'),
            604800 => __('1 week'),
        ];
    }

    protected function cacheInfo(): array
    {
        return [
            'driver' => config('cache.default'),
            'store' => config('cache.stores.' . config('cache.default') . '.driver', 'unknown'),
            'path' => config('cache.stores.file.path', storage_path('framework/cache/data')),
            'prefix' => config('cache.prefix'),
        ];
    }
}
