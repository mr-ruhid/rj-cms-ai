@extends('admin.settings.layout')

@section('settings')
<div class="space-y-6">

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Cache Configuration') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Configure caching to speed up your site.') }}</p>
        </div>

        <div class="p-6 space-y-5">

            <div class="bg-gray-50 rounded-lg p-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                <div>
                    <div class="text-gray-500">{{ __('Current Driver') }}</div>
                    <div class="font-mono font-medium text-gray-900 mt-0.5">{{ $data['cacheInfo']['driver'] }}</div>
                </div>
                <div>
                    <div class="text-gray-500">{{ __('Store') }}</div>
                    <div class="font-mono font-medium text-gray-900 mt-0.5">{{ $data['cacheInfo']['store'] }}</div>
                </div>
                <div>
                    <div class="text-gray-500">{{ __('Prefix') }}</div>
                    <div class="font-mono font-medium text-gray-900 mt-0.5 truncate">{{ $data['cacheInfo']['prefix'] }}</div>
                </div>
                <div>
                    <div class="text-gray-500">{{ __('PHP Version') }}</div>
                    <div class="font-mono font-medium text-gray-900 mt-0.5">{{ PHP_VERSION }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.settings.cache.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="cache_enabled" value="1"
                           {{ ($options['cache_enabled'] ?? '1') === '1' ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <span>
                        <span class="block text-sm font-medium text-gray-700">{{ __('Enable cache') }}</span>
                        <span class="block text-xs text-gray-500 mt-0.5">{{ __('Cache queries and rendered content for faster page loads.') }}</span>
                    </span>
                </label>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Cache Driver') }}</label>
                        <select name="cache_driver" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                            @foreach ($data['drivers'] as $value => $label)
                                <option value="{{ $value }}" {{ ($options['cache_driver'] ?? $data['cacheInfo']['driver']) === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Default TTL') }}</label>
                        <select name="cache_ttl" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                            @foreach ($data['ttlOptions'] as $value => $label)
                                <option value="{{ $value }}" {{ ($options['cache_ttl'] ?? 3600) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-5 space-y-4">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="query_cache" value="1"
                               {{ ($options['query_cache'] ?? '1') === '1' ? 'checked' : '' }}
                               class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                        <span>
                            <span class="block text-sm font-medium text-gray-700">{{ __('Cache database queries') }}</span>
                        </span>
                    </label>

                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="view_cache" value="1"
                               {{ ($options['view_cache'] ?? '1') === '1' ? 'checked' : '' }}
                               class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                        <span>
                            <span class="block text-sm font-medium text-gray-700">{{ __('Cache compiled views') }}</span>
                        </span>
                    </label>

                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="opcache_enabled" value="1"
                               {{ ($options['opcache_enabled'] ?? '0') === '1' ? 'checked' : '' }}
                               class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                        <span>
                            <span class="block text-sm font-medium text-gray-700">{{ __('Enable OPcache') }}</span>
                            <span class="block text-xs text-gray-500 mt-0.5">{{ __('Requires PHP OPcache extension enabled on server.') }}</span>
                        </span>
                    </label>
                </div>

                <div class="border-t border-gray-100 pt-5">
                    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                        {{ __('Save Changes') }}
                    </button>
                </div>
            </form>

        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Browser Cache & Compression') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Client-side caching and HTTP compression.') }}</p>
        </div>

        <form method="POST" action="{{ route('admin.settings.cache.update') }}" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <input type="hidden" name="cache_driver" value="{{ $options['cache_driver'] ?? $data['cacheInfo']['driver'] }}">
            <input type="hidden" name="cache_ttl" value="{{ $options['cache_ttl'] ?? 3600 }}">

            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="browser_cache" value="1"
                       {{ ($options['browser_cache'] ?? '1') === '1' ? 'checked' : '' }}
                       class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                <span>
                    <span class="block text-sm font-medium text-gray-700">{{ __('Enable browser cache headers') }}</span>
                    <span class="block text-xs text-gray-500 mt-0.5">{{ __('Send Cache-Control and Expires headers to browsers.') }}</span>
                </span>
            </label>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Browser Cache TTL') }}</label>
                <input type="number" name="browser_cache_ttl" min="0" max="31536000"
                       value="{{ $options['browser_cache_ttl'] ?? '86400' }}"
                       class="w-full md:w-64 rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                <p class="text-xs text-gray-500 mt-1">{{ __('Seconds. Recommended: 86400 (1 day).') }}</p>
            </div>

            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="gzip_enabled" value="1"
                       {{ ($options['gzip_enabled'] ?? '1') === '1' ? 'checked' : '' }}
                       class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                <span>
                    <span class="block text-sm font-medium text-gray-700">{{ __('Enable Gzip compression') }}</span>
                    <span class="block text-xs text-gray-500 mt-0.5">{{ __('Compress HTML, CSS and JS responses.') }}</span>
                </span>
            </label>

            <div class="border-t border-gray-100 pt-5">
                <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                    {{ __('Save Changes') }}
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('CDN') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Serve static assets from a Content Delivery Network.') }}</p>
        </div>

        <form method="POST" action="{{ route('admin.settings.cache.update') }}" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <input type="hidden" name="cache_driver" value="{{ $options['cache_driver'] ?? $data['cacheInfo']['driver'] }}">
            <input type="hidden" name="cache_ttl" value="{{ $options['cache_ttl'] ?? 3600 }}">

            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="cdn_enabled" value="1"
                       {{ ($options['cdn_enabled'] ?? '0') === '1' ? 'checked' : '' }}
                       class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                <span>
                    <span class="block text-sm font-medium text-gray-700">{{ __('Enable CDN') }}</span>
                    <span class="block text-xs text-gray-500 mt-0.5">{{ __('Rewrite asset URLs to use CDN.') }}</span>
                </span>
            </label>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('CDN URL') }}</label>
                <input type="url" name="cdn_url"
                       value="{{ $options['cdn_url'] ?? '' }}"
                       placeholder="https://cdn.example.com"
                       class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
            </div>

            <div class="border-t border-gray-100 pt-5">
                <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                    {{ __('Save Changes') }}
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Clear Cache') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Remove cached data. Use this after updating content or code.') }}</p>
        </div>

        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">

            <form method="POST" action="{{ route('admin.settings.cache.clear', 'data') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-lg border border-gray-200 hover:border-rj-purple/30 hover:bg-rj-purple/5 transition text-left">
                    <div>
                        <div class="text-sm font-medium text-gray-900">{{ __('Data Cache') }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">{{ __('Cached queries & values') }}</div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </button>
            </form>

            <form method="POST" action="{{ route('admin.settings.cache.clear', 'views') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-lg border border-gray-200 hover:border-rj-purple/30 hover:bg-rj-purple/5 transition text-left">
                    <div>
                        <div class="text-sm font-medium text-gray-900">{{ __('View Cache') }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">{{ __('Compiled Blade templates') }}</div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </button>
            </form>

            <form method="POST" action="{{ route('admin.settings.cache.clear', 'routes') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-lg border border-gray-200 hover:border-rj-purple/30 hover:bg-rj-purple/5 transition text-left">
                    <div>
                        <div class="text-sm font-medium text-gray-900">{{ __('Route Cache') }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">{{ __('Compiled route definitions') }}</div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </button>
            </form>

            <form method="POST" action="{{ route('admin.settings.cache.clear', 'config') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-lg border border-gray-200 hover:border-rj-purple/30 hover:bg-rj-purple/5 transition text-left">
                    <div>
                        <div class="text-sm font-medium text-gray-900">{{ __('Config Cache') }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">{{ __('Cached configuration') }}</div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </button>
            </form>

            <form method="POST" action="{{ route('admin.settings.cache.clear', 'all') }}" class="sm:col-span-2 lg:col-span-2">
                @csrf
                <button type="submit" onclick="return confirm('{{ __('Clear all caches? Site may slow down briefly.') }}')"
                        class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-lg border-2 border-red-200 hover:border-red-300 hover:bg-red-50 transition text-left">
                    <div>
                        <div class="text-sm font-medium text-red-700">{{ __('Clear All Caches') }}</div>
                        <div class="text-xs text-red-500 mt-0.5">{{ __('Flushes every cache type at once') }}</div>
                    </div>
                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                </button>
            </form>

        </div>
    </div>

</div>
@endsection
