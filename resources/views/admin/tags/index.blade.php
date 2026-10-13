@extends('admin.layouts.app')

@section('title', __('Tags'))

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-base font-semibold text-gray-900">{{ __('Tags') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Simple labels to group related posts. No SEO, no hierarchy.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Total') }}</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Used') }}</div>
            <div class="text-2xl font-bold text-green-600 mt-1">{{ number_format($stats['used']) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Unused') }}</div>
            <div class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($stats['unused']) }}</div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-900">{{ __('Add New Tag') }}</h3>
            <p class="text-sm text-gray-500 mt-1">{{ __('Add a tag name in each language. Base language is required.') }}</p>
        </div>

        <form method="POST" action="{{ route('admin.posts.tags.store') }}" class="p-6 space-y-4" x-data="{ activeLocale: '{{ config('app.locale') }}' }">
            @csrf

            <div class="border-b border-gray-100">
                <nav class="flex gap-1 -mb-px overflow-x-auto rj-scrollbar">
                    @foreach ($locales as $locale)
                        <button type="button"
                                @click="activeLocale = '{{ $locale }}'"
                                :class="activeLocale === '{{ $locale }}' ? 'border-rj-purple text-rj-purple' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="px-4 py-2.5 border-b-2 text-sm font-medium transition flex items-center gap-2 whitespace-nowrap">
                            @if (file_exists(public_path("images/flags/{$locale}.svg")))
                                <img src="{{ asset("images/flags/{$locale}.svg") }}" alt="{{ strtoupper($locale) }}" class="w-4 h-3 object-cover rounded-sm">
                            @endif
                            {{ rj_lang_name($locale) }}
                            <span class="text-xs uppercase opacity-60">{{ $locale }}</span>
                            @if ($locale === config('app.locale'))
                                <span class="w-1.5 h-1.5 rounded-full bg-rj-pink"></span>
                            @endif
                        </button>
                    @endforeach
                </nav>
            </div>

            @foreach ($locales as $locale)
                <div x-show="activeLocale === '{{ $locale }}'" x-cloak>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('Tag Name') }}
                        @if ($locale === config('app.locale'))
                            <span class="text-red-500">*</span>
                        @endif
                    </label>
                    <input type="text"
                           name="translations[{{ $locale }}][name]"
                           value="{{ old("translations.{$locale}.name") }}"
                           placeholder="{{ __('e.g. Laravel, Tutorial, News') }}"
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm"
                           {{ $locale === config('app.locale') ? 'required' : '' }}>
                    @error("translations.{$locale}.name")
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    {{ __('Create Tag') }}
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">

        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between gap-4 flex-wrap">
            <form method="GET" action="{{ route('admin.posts.tags') }}" class="relative flex-1 min-w-[200px] max-w-md">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="{{ __('Search tags...') }}"
                       class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
            </form>

            @if ($tags->count() > 0)
                <div class="text-xs text-gray-500">{{ $tags->count() }} {{ __('tag(s)') }}</div>
            @endif
        </div>

        @if ($tags->isEmpty())
            <div class="p-16 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                <p class="mt-4 text-sm text-gray-500">
                    @if (request('search'))
                        {{ __('No tags match your search.') }}
                    @else
                        {{ __('No tags yet. Create your first tag above.') }}
                    @endif
                </p>
            </div>
        @else
            <div class="divide-y divide-gray-100">
                @foreach ($tags as $tag)
                    <div class="px-6 py-3 flex items-center justify-between gap-4 hover:bg-gray-50/50"
                         x-data="{ editing: false, activeLocale: '{{ config('app.locale') }}' }">

                        <div x-show="!editing" class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-8 h-8 rounded-lg bg-rj-purple/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-rj-purple" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-medium text-gray-900">{{ $tag->name }}</span>
                                    <code class="text-xs text-gray-500 font-mono bg-gray-50 px-1.5 py-0.5 rounded">{{ $tag->slug }}</code>
                                </div>

                                @if ($tag->translations->count() > 1)
                                    <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                        @foreach ($tag->translations as $t)
                                            <span class="text-xs text-gray-400">
                                                <span class="uppercase">{{ $t->locale }}</span>: {{ $t->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <span class="inline-flex items-center justify-center min-w-[32px] px-2 py-0.5 rounded-full text-xs font-medium flex-shrink-0 {{ $tag->post_count > 0 ? 'bg-rj-purple/10 text-rj-purple' : 'bg-gray-100 text-gray-500' }}">
                                {{ $tag->post_count }}
                            </span>
                        </div>

                        <div x-show="!editing" class="flex items-center gap-1 flex-shrink-0">
                            <button type="button" @click="editing = true"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:bg-rj-purple/10 hover:text-rj-purple transition"
                                    title="{{ __('Edit') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </button>

                            <form method="POST" action="{{ route('admin.posts.tags.destroy', $tag) }}"
                                  onsubmit="return confirm('{{ __('Delete this tag?') }}')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:bg-red-50 hover:text-red-600 transition disabled:opacity-30 disabled:cursor-not-allowed"
                                        title="{{ __('Delete') }}"
                                        {{ $tag->post_count > 0 ? 'disabled' : '' }}>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                </button>
                            </form>
                        </div>

                        <form x-show="editing" x-cloak method="POST" action="{{ route('admin.posts.tags.update', $tag) }}" class="flex-1 space-y-3">
                            @csrf
                            @method('PUT')

                            <div class="flex gap-1 flex-wrap">
                                @foreach ($locales as $locale)
                                    <button type="button"
                                            @click="activeLocale = '{{ $locale }}'"
                                            :class="activeLocale === '{{ $locale }}' ? 'bg-rj-purple text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                                            class="px-2 py-1 rounded text-xs font-medium uppercase transition">
                                        {{ $locale }}
                                    </button>
                                @endforeach
                            </div>

                            @foreach ($locales as $locale)
                                @php
                                    $t = $tag->translations->firstWhere('locale', $locale);
                                @endphp
                                <div x-show="activeLocale === '{{ $locale }}'" x-cloak>
                                    <input type="text"
                                           name="translations[{{ $locale }}][name]"
                                           value="{{ $t?->name }}"
                                           placeholder="{{ rj_lang_name($locale) }}"
                                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm"
                                           {{ $locale === config('app.locale') ? 'required' : '' }}>
                                </div>
                            @endforeach

                            <div class="flex items-center gap-2">
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gray-900 text-white text-xs font-medium hover:bg-gray-800 transition">
                                    {{ __('Save') }}
                                </button>
                                <button type="button" @click="editing = false"
                                        class="text-xs text-gray-500 hover:text-gray-700">
                                    {{ __('Cancel') }}
                                </button>
                            </div>
                        </form>

                    </div>
                @endforeach
            </div>
        @endif

    </div>

</div>
@endsection