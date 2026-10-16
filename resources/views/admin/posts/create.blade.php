@extends('admin.layouts.app')

@section('title', __('Create Post'))

@section('content')
<div class="space-y-6" x-data="{ activeLocale: '{{ $baseLocale }}', editMode: '{{ old('edit_mode', $type === 'page' ? 'builder' : 'classic') }}', themeTemplate: '{{ old('theme_template') }}' }">

    <form method="POST" action="{{ route('admin.posts.store') }}" class="space-y-6" x-data="{ publishing: false }" @submit="publishing = true">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">
        <input type="hidden" name="edit_mode" :value="editMode">
        <input type="hidden" name="theme_template" :value="editMode === 'theme' ? themeTemplate : ''">
        <input type="hidden" name="open_builder" x-ref="openBuilder" value="0">

        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.posts.index', ['type' => $type]) }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <h2 class="text-base font-semibold text-gray-900">{{ $type === 'page' ? __('Create Page') : __('Create Post') }}</h2>
                    <p class="text-sm text-gray-500 mt-0.5">{{ __('Fill the basics, then choose how to design.') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.posts.index', ['type' => $type]) }}" class="text-sm text-gray-600 hover:text-gray-900">
                    {{ __('Cancel') }}
                </a>

                <template x-if="editMode !== 'builder'">
                    <button type="submit" name="status" value="draft" @click="$refs.openBuilder.value = '0'"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
                        {{ __('Save Draft') }}
                    </button>
                </template>

                <template x-if="editMode === 'builder'">
                    <button type="submit" name="status" value="draft" @click="$refs.openBuilder.value = '1'"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>
                        {{ __('Save & Open Builder') }}
                    </button>
                </template>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-r-lg">
                <ul class="text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
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
                                    @if ($locale === $baseLocale)
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
                                        {{ __('Title') }}
                                        @if ($locale === $baseLocale)
                                            <span class="text-red-500">*</span>
                                        @endif
                                    </label>
                                    <input type="text"
                                           name="{{ $locale === $baseLocale ? 'title' : "translations[{$locale}][title]" }}"
                                           value="{{ old($locale === $baseLocale ? 'title' : "translations.{$locale}.title") }}"
                                           placeholder="{{ __('Enter title...') }}"
                                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-base font-medium"
                                           {{ $locale === $baseLocale ? 'required' : '' }}>
                                    @error($locale === $baseLocale ? 'title' : "translations.{$locale}.title")
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Excerpt') }}</label>
                                    <textarea name="{{ $locale === $baseLocale ? 'excerpt' : "translations[{$locale}][excerpt]" }}"
                                              rows="3"
                                              placeholder="{{ __('Short summary.') }}"
                                              class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">{{ old($locale === $baseLocale ? 'excerpt' : "translations.{$locale}.excerpt") }}</textarea>
                                </div>

                                <template x-if="editMode === 'classic' && '{{ $locale }}' === '{{ $baseLocale }}'">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Content') }}</label>
                                        <textarea name="content"
                                                  rows="14"
                                                  class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">{{ old('content') }}</textarea>
                                        <p class="text-xs text-gray-500 mt-1">{{ __('Supports HTML and RJ shortcodes.') }}</p>
                                    </div>
                                </template>

                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <div class="space-y-6">

                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Edit Mode') }}</h3>
                        <p class="text-xs text-gray-500 mt-1">{{ __('Choose how to design this content.') }}</p>
                    </div>

                    <div class="p-6 space-y-3">

                        <label class="flex items-start gap-3 p-3 rounded-lg border-2 cursor-pointer transition"
                               :class="editMode === 'builder' ? 'border-rj-purple bg-rj-purple/5' : 'border-gray-200 hover:border-gray-300'">
                            <input type="radio" value="builder" x-model="editMode" class="mt-0.5 text-rj-purple focus:ring-rj-purple">
                            <span class="flex-1">
                                <span class="block text-sm font-semibold text-gray-900">RJBuilder</span>
                                <span class="block text-xs text-gray-500 mt-0.5">{{ __('Drag-and-drop blocks. Visual editing.') }}</span>
                            </span>
                        </label>

                        <label class="flex items-start gap-3 p-3 rounded-lg border-2 cursor-pointer transition"
                               :class="editMode === 'classic' ? 'border-rj-purple bg-rj-purple/5' : 'border-gray-200 hover:border-gray-300'">
                            <input type="radio" value="classic" x-model="editMode" class="mt-0.5 text-rj-purple focus:ring-rj-purple">
                            <span class="flex-1">
                                <span class="block text-sm font-semibold text-gray-900">{{ __('Classic Editor') }}</span>
                                <span class="block text-xs text-gray-500 mt-0.5">{{ __('HTML content in a plain textarea.') }}</span>
                            </span>
                        </label>

                        @if(!empty($themeTemplates))
                            <label class="flex items-start gap-3 p-3 rounded-lg border-2 cursor-pointer transition"
                                   :class="editMode === 'theme' ? 'border-rj-purple bg-rj-purple/5' : 'border-gray-200 hover:border-gray-300'">
                                <input type="radio" value="theme" x-model="editMode" class="mt-0.5 text-rj-purple focus:ring-rj-purple">
                                <span class="flex-1">
                                    <span class="block text-sm font-semibold text-gray-900">{{ __('Theme Template') }}</span>
                                    <span class="block text-xs text-gray-500 mt-0.5">{{ __('Use a pre-built page layout from your theme.') }}</span>
                                </span>
                            </label>
                        @endif

                        <div x-show="editMode === 'theme'" x-cloak class="pt-3 border-t border-gray-100">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Template') }}</label>
                            <select x-model="themeTemplate"
                                    class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                                <option value="">{{ __('— Select template —') }}</option>
                                @foreach($themeTemplates as $tpl)
                                    <option value="{{ $tpl['id'] }}">{{ $tpl['label'] }}</option>
                                @endforeach
                            </select>
                            @foreach($themeTemplates as $tpl)
                                <p x-show="themeTemplate === '{{ $tpl['id'] }}'" x-cloak class="text-xs text-gray-500 mt-2 leading-relaxed">
                                    {{ $tpl['description'] ?? '' }}
                                </p>
                            @endforeach
                        </div>

                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Publish Settings') }}</h3>
                    </div>

                    <div class="p-6 space-y-4">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Status') }}</label>
                            <select name="status" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                                <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                                <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>{{ __('Published') }}</option>
                                <option value="scheduled" {{ old('status') === 'scheduled' ? 'selected' : '' }}>{{ __('Scheduled') }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Publish Date') }}</label>
                            <input type="datetime-local"
                                   name="published_at"
                                   value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        </div>

                        <div class="border-t border-gray-100 pt-4 space-y-3">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="is_sticky" value="1" {{ old('is_sticky') ? 'checked' : '' }}
                                       class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                                <span>
                                    <span class="block text-sm font-medium text-gray-700">{{ __('Sticky') }}</span>
                                    <span class="block text-xs text-gray-500 mt-0.5">{{ __('Pin to top of archive.') }}</span>
                                </span>
                            </label>

                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="allow_comments" value="1" {{ old('allow_comments', '1') ? 'checked' : '' }}
                                       class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                                <span>
                                    <span class="block text-sm font-medium text-gray-700">{{ __('Allow Comments') }}</span>
                                    <span class="block text-xs text-gray-500 mt-0.5">{{ __('Enable comment form.') }}</span>
                                </span>
                            </label>
                        </div>

                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Permalink') }}</h3>
                    </div>
                    <div class="p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Slug') }}</label>
                        <input type="text" name="slug" value="{{ old('slug') }}"
                               placeholder="{{ __('auto-generated from title') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono lowercase">
                        @error('slug')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Featured Image') }}</h3>
                    </div>
                    <div class="p-6" x-data="{ imageUrl: '{{ old('featured_image') }}' }"
                         @media-selected.window="if ($event.detail.target === $refs.imageInput) imageUrl = $event.detail.url">
                        <div x-show="imageUrl" class="rounded-lg overflow-hidden border border-gray-200 relative mb-3">
                            <img :src="imageUrl" alt="" class="w-full h-40 object-cover">
                            <button type="button" @click="imageUrl = ''; $refs.imageInput.value = ''"
                                    class="absolute top-2 right-2 w-7 h-7 rounded-full bg-red-600 text-white flex items-center justify-center hover:bg-red-700 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <input type="hidden" name="featured_image" x-ref="imageInput" :value="imageUrl">
                        <button type="button"
                                @click="$dispatch('open-media-picker', { value: $refs.imageInput })"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border-2 border-dashed border-gray-300 text-gray-700 text-sm font-medium hover:border-rj-purple hover:bg-rj-purple/5 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span x-text="imageUrl ? '{{ __('Change Image') }}' : '{{ __('Select Image') }}'"></span>
                        </button>
                    </div>
                </div>

                @if($type !== 'page')
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-base font-semibold text-gray-900">{{ __('Categories') }}</h3>
                        </div>
                        <div class="p-6">
                            @if ($categories->isEmpty())
                                <p class="text-sm text-gray-500">{{ __('No categories yet.') }}
                                    <a href="{{ route('admin.posts.categories.create') }}" target="_blank" class="text-rj-purple hover:underline">{{ __('Create one →') }}</a>
                                </p>
                            @else
                                <div class="max-h-64 overflow-y-auto rj-scrollbar space-y-1.5 pr-1">
                                    @foreach ($categories as $cat)
                                        <label class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input type="checkbox" name="categories[]" value="{{ $cat->id }}"
                                                   {{ in_array($cat->id, old('categories', [])) ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                                            <span class="text-sm text-gray-700">{{ $cat->indented_name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-base font-semibold text-gray-900">{{ __('Tags') }}</h3>
                        </div>
                        <div class="p-6">
                            @if ($tags->isEmpty())
                                <p class="text-sm text-gray-500">{{ __('No tags yet.') }}
                                    <a href="{{ route('admin.posts.tags') }}" target="_blank" class="text-rj-purple hover:underline">{{ __('Create one →') }}</a>
                                </p>
                            @else
                                <div class="max-h-48 overflow-y-auto rj-scrollbar space-y-1.5 pr-1">
                                    @foreach ($tags as $tag)
                                        <label class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                                   {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                                            <span class="text-sm text-gray-700">{{ $tag->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

            </div>

        </div>

    </form>

</div>
@endsection