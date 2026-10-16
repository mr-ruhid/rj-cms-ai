@extends('admin.layouts.app')

@section('title', __('Themes'))

@section('content')
<div class="space-y-6" x-data="{ uploadOpen: false, uploading: false, search: '' }">

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-base font-semibold text-gray-900">{{ __('Themes') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Manage your site appearance. Only one theme can be active at a time.') }}</p>
        </div>

        <button type="button" @click="uploadOpen = true"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            {{ __('Upload Theme') }}
        </button>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Installed') }}</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($counts['all']) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Active') }}</div>
            <div class="text-2xl font-bold text-green-600 mt-1">{{ number_format($counts['active']) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Inactive') }}</div>
            <div class="text-2xl font-bold text-gray-600 mt-1">{{ number_format($counts['inactive']) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Errors') }}</div>
            <div class="text-2xl font-bold text-red-600 mt-1">{{ number_format($counts['error']) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Updates') }}</div>
            <div class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($counts['updates']) }}</div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between gap-3 flex-wrap">
            <div class="relative flex-1 max-w-md">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" x-model="search" placeholder="{{ __('Search themes...') }}"
                       class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
            </div>

            <form method="POST" action="{{ route('admin.themes.check-updates') }}" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    {{ __('Check for Updates') }}
                </button>
            </form>
        </div>

        @if ($themes->isEmpty())
            <div class="p-16 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                <p class="mt-4 text-sm text-gray-500">{{ __('No themes installed yet.') }}</p>
                <button type="button" @click="uploadOpen = true"
                        class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    {{ __('Upload First Theme') }}
                </button>
            </div>
        @else
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

                @foreach ($themes as $theme)
                    @php
                        $preview = $theme->previewUrl();
                        $isActive = $theme->isActive();
                    @endphp

                    <div x-show="search === '' || '{{ strtolower($theme->name) }}'.includes(search.toLowerCase())"
                         class="border {{ $isActive ? 'border-rj-purple ring-1 ring-rj-purple/20' : 'border-gray-200' }} rounded-xl overflow-hidden bg-white hover:shadow-lg transition group">

                        <div class="relative aspect-[16/10] bg-gradient-to-br from-gray-100 to-gray-50 overflow-hidden">
                            @if ($preview)
                                <img src="{{ $preview }}" alt="{{ $theme->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-20 h-20 text-gray-300" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>
                                </div>
                            @endif

                            @if ($isActive)
                                <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-500 text-white text-xs font-semibold shadow-lg">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                    {{ __('Active') }}
                                </span>
                            @endif

                            @if ($theme->status === 'error')
                                <span class="absolute top-3 right-3 inline-flex items-center px-2.5 py-1 rounded-full bg-red-500 text-white text-xs font-semibold shadow-lg">
                                    {{ __('Error') }}
                                </span>
                            @endif
                        </div>

                        <div class="p-5">
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <div class="min-w-0">
                                    <h3 class="text-base font-bold text-gray-900 truncate">{{ $theme->name }}</h3>
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        {{ __('Version') }} {{ $theme->version }}
                                        @if ($theme->author)
                                            · {{ $theme->author }}
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if ($theme->description)
                                <p class="text-sm text-gray-600 line-clamp-2 mb-4">{{ $theme->description }}</p>
                            @endif

                            @if ($theme->error_message)
                                <p class="text-xs text-red-600 bg-red-50 border border-red-100 rounded-lg p-2.5 mb-4">
                                    {{ $theme->error_message }}
                                </p>
                            @endif

                            <div class="flex items-center gap-2 flex-wrap">
                                @if ($isActive)
                                    <a href="{{ route('admin.themes.customizer', $theme) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-xs font-medium hover:opacity-90 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                        {{ __('Customize') }}
                                    </a>

                                    <a href="{{ route('admin.themes.show', $theme) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-gray-300 text-gray-700 text-xs font-medium hover:bg-gray-50 transition">
                                        {{ __('Details') }}
                                    </a>
                                @else
                                    <form method="POST" action="{{ route('admin.themes.activate', $theme) }}" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-xs font-medium hover:opacity-90 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            {{ __('Activate') }}
                                        </button>
                                    </form>

                                    <a href="{{ route('admin.themes.show', $theme) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-gray-300 text-gray-700 text-xs font-medium hover:bg-gray-50 transition">
                                        {{ __('Details') }}
                                    </a>

                                    <button type="button"
                                            @click="$dispatch('theme-preview', { slug: '{{ $theme->slug }}' })"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white border border-gray-300 text-gray-500 hover:bg-gray-50 hover:text-rj-purple transition"
                                            title="{{ __('Preview') }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.46 12C3.73 7.94 7.52 5 12 5s8.27 2.94 9.54 7c-1.27 4.06-5.06 7-9.54 7S3.73 16.06 2.46 12z"/></svg>
                                    </button>

                                    <a href="{{ route('admin.themes.export', $theme) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white border border-gray-300 text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition"
                                       title="{{ __('Export') }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </a>

                                    <form method="POST" action="{{ route('admin.themes.destroy', $theme) }}"
                                          onsubmit="return confirm('{{ __('Delete this theme? This action cannot be undone.') }}')"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white border border-gray-300 text-gray-500 hover:bg-red-50 hover:text-red-600 hover:border-red-300 transition"
                                                title="{{ __('Delete') }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        @endif
    </div>

    <div x-show="uploadOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="uploadOpen = false">
        <div class="absolute inset-0 bg-black/50" @click="uploadOpen = false"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-semibold text-gray-900">{{ __('Upload Theme') }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">{{ __('Select a .zip file containing theme.json and views.') }}</p>
                </div>
                <button type="button" @click="uploadOpen = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.themes.upload') }}" enctype="multipart/form-data"
                  @submit="uploading = true">
                @csrf

                <div class="p-6 space-y-4">
                    <label class="block">
                        <span class="block text-sm font-medium text-gray-700 mb-2">{{ __('Theme ZIP') }}</span>
                        <input type="file" name="file" accept=".zip" required
                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-rj-purple/10 file:text-rj-purple hover:file:bg-rj-purple/20 cursor-pointer">

                        @error('file')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </label>

                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-3 text-xs text-blue-800 leading-relaxed">
                        <strong>{{ __('Requirements:') }}</strong>
                        <ul class="mt-1 ml-4 list-disc space-y-0.5">
                            <li>{{ __('theme.json in archive root (or one level deep)') }}</li>
                            <li>{{ __('Unique slug') }}</li>
                            <li>{{ __('Max 50 MB') }}</li>
                        </ul>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <button type="button" @click="uploadOpen = false"
                            class="text-sm text-gray-600 hover:text-gray-900">
                        {{ __('Cancel') }}
                    </button>

                    <button type="submit" :disabled="uploading"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition disabled:opacity-60">
                        <svg x-show="!uploading" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        <svg x-show="uploading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                        <span x-text="uploading ? '{{ __('Uploading...') }}' : '{{ __('Upload') }}'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
document.addEventListener('theme-preview', async (e) => {
    try {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        const url = '{{ url('/admin/themes') }}/' + e.detail.slug + '/preview';

        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
        });

        const data = await res.json();

        if (data.success && data.url) {
            window.open(data.url, '_blank');
        }
    } catch (err) {
        console.error(err);
    }
});
</script>
@endsection
