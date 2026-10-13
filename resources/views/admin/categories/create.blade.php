@extends('admin.layouts.app')

@section('title', __('Create Category'))

@section('content')
<div class="space-y-6" x-data="{ activeLocale: '{{ config('app.locale') }}' }">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.posts.categories') }}" class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h2 class="text-base font-semibold text-gray-900">{{ __('Create Category') }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('Add a new category with translations and SEO data.') }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.posts.categories.store') }}" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Translations') }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ __('Category name and description in each language.') }}</p>
                    </div>

                    <div class="border-b border-gray-100 px-6">
                        <nav class="flex gap-1 -mb-px overflow-x-auto rj-scrollbar">
                            @foreach ($locales as $locale)
                                <button type="button"
                                        @click="activeLocale = '{{ $locale }}'"
                                        :class="activeLocale === '{{ $locale }}' ? 'border-rj-purple text-rj-purple' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                        class="px-4 py-3 border-b-2 text-sm font-medium transition flex items-center gap-2 whitespace-nowrap">
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

                    <div class="p-6 space-y-5">
                        @foreach ($locales as $locale)
                            <div x-show="activeLocale === '{{ $locale }}'" x-cloak class="space-y-5">

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                        {{ __('Name') }}
                                        @if ($locale === config('app.locale'))
                                            <span class="text-red-500">*</span>
                                        @endif
                                    </label>
                                    <input type="text"
                                           name="translations[{{ $locale }}][name]"
                                           value="{{ old("translations.{$locale}.name") }}"
                                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm"
                                           {{ $locale === config('app.locale') ? 'required' : '' }}>
                                    @error("translations.{$locale}.name")
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Description') }}</label>
                                    <textarea name="translations[{{ $locale }}][description]" rows="4"
                                              class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">{{ old("translations.{$locale}.description") }}</textarea>
                                    <p class="text-xs text-gray-500 mt-1">{{ __('Shown on category archive pages. Optional.') }}</p>
                                </div>

                                <div class="border-t border-gray-100 pt-5">
                                    <h4 class="text-sm font-semibold text-gray-900 mb-4">{{ __('SEO') }}</h4>

                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('SEO Title') }}</label>
                                            <input type="text"
                                                   name="translations[{{ $locale }}][seo_title]"
                                                   value="{{ old("translations.{$locale}.seo_title") }}"
                                                   maxlength="255"
                                                   class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                                            <p class="text-xs text-gray-500 mt-1">{{ __('Recommended: 50-60 characters.') }}</p>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('SEO Description') }}</label>
                                            <textarea name="translations[{{ $locale }}][seo_description]" rows="3"
                                                      maxlength="500"
                                                      class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">{{ old("translations.{$locale}.seo_description") }}</textarea>
                                            <p class="text-xs text-gray-500 mt-1">{{ __('Recommended: 150-160 characters.') }}</p>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('SEO Image URL') }}</label>
                                            <input type="text"
                                                   name="translations[{{ $locale }}][seo_image]"
                                                   value="{{ old("translations.{$locale}.seo_image") }}"
                                                   placeholder="https://..."
                                                   class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                                            <p class="text-xs text-gray-500 mt-1">{{ __('Open Graph image for social sharing.') }}</p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <div class="space-y-6">

                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Publish') }}</h3>
                    </div>

                    <div class="p-6 space-y-4">

                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1"
                                   {{ old('is_active', '1') ? 'checked' : '' }}
                                   class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                            <span>
                                <span class="block text-sm font-medium text-gray-700">{{ __('Active') }}</span>
                                <span class="block text-xs text-gray-500 mt-0.5">{{ __('Visible on the site.') }}</span>
                            </span>
                        </label>

                        <div class="border-t border-gray-100 pt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Sort Order') }}</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                                   min="0" max="99999"
                                   class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                            <p class="text-xs text-gray-500 mt-1">{{ __('Lower numbers appear first.') }}</p>
                        </div>

                    </div>

                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center gap-3">
                        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                            {{ __('Create Category') }}
                        </button>
                        <a href="{{ route('admin.posts.categories') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Hierarchy') }}</h3>
                    </div>

                    <div class="p-6 space-y-4">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Parent Category') }}</label>
                            <select name="parent_id" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                                <option value="">{{ __('— None (root category) —') }}</option>
                                @foreach ($categories as $item)
                                    <option value="{{ $item->id }}" {{ old('parent_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->indented_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Slug') }}</label>
                            <input type="text" name="slug" value="{{ old('slug') }}"
                                   pattern="[a-z0-9\-]+"
                                   placeholder="{{ __('auto-generated') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono lowercase">
                            <p class="text-xs text-gray-500 mt-1">{{ __('Leave empty to auto-generate from name.') }}</p>
                            @error('slug')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Featured Image') }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ __('Category thumbnail (any language).') }}</p>
                    </div>

                    <div class="p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Image URL') }}</label>
                        <input type="text" name="image" value="{{ old('image') }}"
                               placeholder="{{ __('/uploads/2026/10/image.jpg') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                        <p class="text-xs text-gray-500 mt-1">
                            {{ __('Paste URL from') }}
                            <a href="{{ route('admin.media.index') }}" class="text-rj-purple hover:underline">{{ __('Media Library') }}</a>
                        </p>
                    </div>
                </div>

            </div>

        </div>

    </form>

</div>
@endsection