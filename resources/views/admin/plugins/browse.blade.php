@extends('admin.layouts.app')

@section('title', __('Browse Plugins'))

@section('content')
<div class="space-y-6" x-data="pluginBrowse()">

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.plugins.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h2 class="text-base font-semibold text-gray-900">{{ __('Browse Community Plugins') }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ __('Discover and install plugins from the official registry.') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.plugins.index') }}"
               class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
                {{ __('Installed') }}
            </a>
        </div>
    </div>

    @if ($registryError)
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-r-lg">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <div class="text-sm font-medium">{{ __('Could not load registry') }}</div>
                    <div class="text-xs mt-1 opacity-80">{{ $registryError }}</div>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">

        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between gap-3 flex-wrap">
            <form method="GET" action="{{ route('admin.plugins.browse') }}" class="flex items-center gap-3 flex-1 flex-wrap">
                <div class="relative flex-1 min-w-[200px] max-w-md">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="{{ __('Search plugins...') }}"
                           class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                </div>

                <select name="category" class="rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    <option value="">{{ __('All Categories') }}</option>
                    @foreach ($categories as $key => $label)
                        <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>

                <button type="submit" class="px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm font-medium text-gray-700 transition">
                    {{ __('Filter') }}
                </button>

                @if (request()->hasAny(['q', 'category']))
                    <a href="{{ route('admin.plugins.browse') }}" class="text-sm text-gray-500 hover:text-gray-900">{{ __('Clear') }}</a>
                @endif
            </form>

            <div class="text-xs text-gray-500">{{ count($plugins) }} {{ __('plugin(s)') }}</div>
        </div>

        @if (empty($plugins))
            <div class="p-16 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <p class="mt-4 text-sm text-gray-500">
                    @if (request('q') || request('category'))
                        {{ __('No plugins match your filters.') }}
                    @else
                        {{ __('No plugins available in the registry yet.') }}
                    @endif
                </p>
            </div>
        @else
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach ($plugins as $plugin)
                    @php
                        $isInstalled = in_array($plugin['slug'] ?? '', $installedSlugs, true);

                        $versionStr = strtolower($plugin['version'] ?? '');
                        $isBeta = !empty($plugin['is_prerelease'])
                            || str_contains($versionStr, 'beta')
                            || str_contains($versionStr, 'alpha')
                            || str_contains($versionStr, 'rc');
                    @endphp

                    <div class="border border-gray-200 rounded-xl overflow-hidden bg-white hover:shadow-lg transition flex flex-col">

                        <div class="relative aspect-[16/9] bg-gradient-to-br from-gray-100 to-gray-50 overflow-hidden flex items-center justify-center">
                            @if (!empty($plugin['screenshot']))
                                <img src="{{ $plugin['screenshot'] }}" alt="{{ $plugin['name'] }}"
                                     class="w-full h-full object-cover"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="w-full h-full items-center justify-center" style="display:none;">
                                    <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
                                </div>
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
                                </div>
                            @endif

                            @if ($isInstalled)
                                <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-500 text-white text-xs font-semibold shadow-lg z-10">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    {{ __('Installed') }}
                                </span>
                            @endif

                            <div class="absolute top-3 right-3 flex flex-col items-end gap-1.5 z-10">
                                @if ($isBeta)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-500 text-white text-[11px] font-bold shadow-lg shadow-amber-500/30 uppercase tracking-wider" title="{{ __('This is a pre-release (beta) version.') }}">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L1 21h22L12 2zm0 3.8L19.5 19h-15L12 5.8z"/></svg>
                                        {{ __('Beta') }}
                                    </span>
                                @endif

                                @if (!empty($plugin['featured']))
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-gradient-to-r from-rj-purple to-rj-pink text-white text-xs font-semibold shadow-lg">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                        {{ __('Featured') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="p-5 flex-1 flex flex-col">
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-base font-bold text-gray-900 truncate">{{ $plugin['name'] }}</h3>
                                        @if (!empty($plugin['verified']))
                                            <span title="{{ __('Verified') }}" class="text-blue-500 flex-shrink-0">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l2.4 2.4 3.4-.6 1.2 3.2 3.2 1.2-.6 3.4L24 14l-2.4 2.4.6 3.4-3.2 1.2-1.2 3.2-3.4-.6L12 26l-2.4-2.4-3.4.6-1.2-3.2-3.2-1.2.6-3.4L0 14l2.4-2.4-.6-3.4 3.2-1.2 1.2-3.2 3.4.6L12 2z" transform="scale(0.85) translate(2 1)"/><path d="M9 12l2 2 4-4" stroke="white" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        v{{ $plugin['version'] }}
                                        @if (!empty($plugin['author']))
                                            · {{ $plugin['author'] }}
                                        @endif
                                        @if (!empty($plugin['stars']))
                                            · ⭐ {{ number_format($plugin['stars']) }}
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if (!empty($plugin['description']))
                                <p class="text-sm text-gray-600 line-clamp-2 mb-4 flex-1">{{ $plugin['description'] }}</p>
                            @endif

                            <div class="flex items-center gap-2 flex-wrap mt-auto pt-2 border-t border-gray-100">
                                @if ($isInstalled)
                                    <a href="{{ route('admin.plugins.index') }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-100 text-gray-700 text-xs font-medium hover:bg-gray-200 transition">
                                        {{ __('View in Installed') }}
                                    </a>
                                @else
                                    <button type="button"
                                            @click="install(@js($plugin))"
                                            :disabled="installing === @js($plugin['slug'])"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-xs font-medium hover:opacity-90 transition disabled:opacity-60">
                                        <svg x-show="installing !== @js($plugin['slug'])" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        <svg x-show="installing === @js($plugin['slug'])" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                                        <span x-text="installing === @js($plugin['slug']) ? '{{ __('Installing...') }}' : '{{ __('Install') }}'"></span>
                                    </button>
                                @endif

                                <a href="{{ $plugin['html_url'] }}" target="_blank"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white border border-gray-300 text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition ml-auto"
                                   title="{{ __('View on GitHub') }}">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 .5C5.4.5 0 5.9 0 12.5c0 5.3 3.4 9.8 8.2 11.4.6.1.8-.3.8-.6v-2c-3.3.7-4-1.6-4-1.6-.5-1.4-1.3-1.8-1.3-1.8-1.1-.7.1-.7.1-.7 1.2.1 1.8 1.2 1.8 1.2 1.1 1.8 2.8 1.3 3.5 1 .1-.8.4-1.3.7-1.6-2.7-.3-5.5-1.3-5.5-5.9 0-1.3.5-2.4 1.2-3.2-.1-.3-.5-1.5.1-3.2 0 0 1-.3 3.3 1.2.9-.3 2-.4 3-.4s2.1.1 3 .4c2.3-1.5 3.3-1.2 3.3-1.2.6 1.7.2 2.9.1 3.2.8.8 1.2 1.9 1.2 3.2 0 4.6-2.8 5.6-5.5 5.9.4.4.8 1.1.8 2.2v3.3c0 .3.2.7.8.6C20.6 22.3 24 17.8 24 12.5 24 5.9 18.6.5 12 .5z"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div x-show="toast.show" x-cloak
         x-transition
         class="fixed bottom-6 right-6 px-5 py-3 rounded-xl shadow-2xl text-white text-sm font-medium z-50"
         :style="'background:' + (toast.type === 'error' ? '#dc2626' : '#059669')"
         x-text="toast.message"></div>

</div>

<script>
function pluginBrowse() {
    return {
        installing: null,
        toast: { show: false, message: '', type: 'success' },

        showToast(message, type) {
            this.toast = { show: true, message, type: type || 'success' };
            setTimeout(() => { this.toast.show = false; }, 4000);
        },

        async install(plugin) {
            if (this.installing) return;

            const ok = confirm(
                '{{ __('Install') }} "' + plugin.name + '"?\n\n' +
                '{{ __('Author') }}: ' + (plugin.author || '—') + '\n' +
                '{{ __('Version') }}: ' + plugin.version
            );

            if (!ok) return;

            this.installing = plugin.slug;

            try {
                const csrf = document.querySelector('meta[name="csrf-token"]').content;

                const res = await fetch('{{ route('admin.plugins.install-github') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({
                        repo: plugin.repo,
                        zip_url: plugin.zip_url,
                        slug: plugin.slug,
                    }),
                });

                const data = await res.json().catch(() => ({}));

                if (res.ok && data.success) {
                    this.showToast('{{ __('Plugin installed successfully.') }}', 'success');
                    setTimeout(() => { window.location.reload(); }, 1200);
                } else {
                    this.showToast(data.message || '{{ __('Installation failed.') }}', 'error');
                }
            } catch (e) {
                this.showToast('{{ __('Network error. Please try again.') }}', 'error');
            } finally {
                this.installing = null;
            }
        },
    };
}
</script>
@endsection
