@extends('admin.layouts.app')

@section('title', __('Edit Post'))

@section('content')
<div class="space-y-6" x-data="{ activeLocale: '{{ $baseLocale }}' }">

    <form method="POST" action="{{ route('admin.posts.update', $post) }}" class="space-y-6" x-data="{ publishing: false }" @submit="publishing = true">
        @csrf
        @method('PUT')

        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.posts.index', ['type' => $post->type]) }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <h2 class="text-base font-semibold text-gray-900">{{ __('Edit Post') }}</h2>
                    <p class="text-sm text-gray-500 mt-0.5 truncate max-w-md">{{ $post->title ?: __('(no title)') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if ($post->status === 'published')
                    <a href="{{ url('/blog/' . $post->slug) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 text-sm text-gray-600 hover:text-rj-purple transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        {{ __('View') }}
                    </a>
                @endif

                <a href="{{ route('admin.posts.index', ['type' => $post->type]) }}" class="text-sm text-gray-600 hover:text-gray-900">
                    {{ __('Cancel') }}
                </a>

                <button type="submit" name="status" value="draft"
                        class="inline-flex items-center px-4 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
                    {{ __('Save Draft') }}
                </button>

                <button type="submit" name="status" value="published"
                        :disabled="publishing"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition disabled:opacity-60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span x-text="publishing ? '{{ __('Saving...') }}' : '{{ $post->status === 'published' ? __('Update') : __('Publish') }}'"></span>
                </button>
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

                <a href="{{ route('admin.posts.build', $post) }}"
                   class="block bg-gradient-to-r from-rj-purple/10 to-rj-pink/10 rounded-lg border border-rj-purple/30 p-6 hover:border-rj-purple transition group">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center text-white flex-shrink-0">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-semibold text-gray-900">{{ __('Open in RJBuilder') }}</h3>
                            <p class="text-sm text-gray-600 mt-0.5">
                                @if ($post->hasBlocks())
                                    {{ __(':n block(s) in this post. Click to continue editing.', ['n' => count($post->blocks['blocks'] ?? [])]) }}
                                @else
                                    {{ __('Start building your post with drag-and-drop blocks.') }}
                                @endif
                            </p>
                        </div>
                        <svg class="w-5 h-5 text-rj-purple group-hover:translate-x-1 transition-transform flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </a>

                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="border-b border-gray-100 px-6">
                        <nav class="flex gap-1 -mb-px overflow-x-auto rj-scrollbar">
                            @foreach ($locales as $locale)
                                @php
                                    $translation = $post->translations->firstWhere('locale', $locale);
                                    $hasTranslation = $locale === $baseLocale || ($translation && $translation->title);
                                @endphp
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
                                    @elseif ($hasTranslation)
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    @endif
                                </button>
                            @endforeach
                        </nav>
                    </div>

                    <div class="p-6 space-y-5">
                        @foreach ($locales as $locale)
                            @php
                                $t = $post->translations->firstWhere('locale', $locale);
                                $isBase = $locale === $baseLocale;
                                $titleValue = $isBase ? $post->getRawOriginal('title') : ($t?->title);
                                $excerptValue = $isBase ? $post->getRawOriginal('excerpt') : ($t?->excerpt);
                            @endphp

                            <div x-show="activeLocale === '{{ $locale }}'" x-cloak class="space-y-5">

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                        {{ __('Title') }}
                                        @if ($isBase)
                                            <span class="text-red-500">*</span>
                                        @endif
                                    </label>
                                    <input type="text"
                                           name="{{ $isBase ? 'title' : "translations[{$locale}][title]" }}"
                                           value="{{ old($isBase ? 'title' : "translations.{$locale}.title", $titleValue) }}"
                                           placeholder="{{ __('Enter post title...') }}"
                                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-base font-medium"
                                           {{ $isBase ? 'required' : '' }}>
                                    @error($isBase ? 'title' : "translations.{$locale}.title")
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Excerpt') }}</label>
                                    <textarea name="{{ $isBase ? 'excerpt' : "translations[{$locale}][excerpt]" }}"
                                              rows="3"
                                              placeholder="{{ __('Short summary. Auto-generated from content if empty.') }}"
                                              class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">{{ old($isBase ? 'excerpt' : "translations.{$locale}.excerpt", $excerptValue) }}</textarea>
                                </div>

                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <div class="space-y-6">

                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Publish Settings') }}</h3>

                        @if ($post->status === 'trash')
                            <span class="px-2 py-0.5 rounded-full bg-red-50 text-red-700 text-xs font-medium">{{ __('In Trash') }}</span>
                        @endif
                    </div>

                    <div class="p-6 space-y-4">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Status') }}</label>
                            <select name="status" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                                <option value="draft" {{ old('status', $post->status) === 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                                <option value="published" {{ old('status', $post->status) === 'published' ? 'selected' : '' }}>{{ __('Published') }}</option>
                                <option value="scheduled" {{ old('status', $post->status) === 'scheduled' ? 'selected' : '' }}>{{ __('Scheduled') }}</option>
                                @if ($post->status === 'trash')
                                    <option value="trash" selected>{{ __('Trash') }}</option>
                                @endif
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Publish Date') }}</label>
                            <input type="datetime-local"
                                   name="published_at"
                                   value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                            <p class="text-xs text-gray-500 mt-1">{{ __('Set future date + Scheduled status for auto-publish.') }}</p>
                        </div>

                        <div class="border-t border-gray-100 pt-4 space-y-3">

                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="is_sticky" value="1" {{ old('is_sticky', $post->is_sticky) ? 'checked' : '' }}
                                       class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                                <span>
                                    <span class="block text-sm font-medium text-gray-700">{{ __('Sticky') }}</span>
                                    <span class="block text-xs text-gray-500 mt-0.5">{{ __('Pin to top of archive.') }}</span>
                                </span>
                            </label>

                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="allow_comments" value="1" {{ old('allow_comments', $post->allow_comments) ? 'checked' : '' }}
                                       class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                                <span>
                                    <span class="block text-sm font-medium text-gray-700">{{ __('Allow Comments') }}</span>
                                    <span class="block text-xs text-gray-500 mt-0.5">{{ __('Enable comment form on this post.') }}</span>
                                </span>
                            </label>

                        </div>

                        <div class="border-t border-gray-100 pt-4">
                            <div class="text-xs text-gray-500 space-y-1">
                                <div class="flex items-center justify-between">
                                    <span>{{ __('Created') }}</span>
                                    <span class="text-gray-700">{{ $post->created_at->format('M d, Y H:i') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>{{ __('Updated') }}</span>
                                    <span class="text-gray-700">{{ $post->updated_at->format('M d, Y H:i') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>{{ __('Views') }}</span>
                                    <span class="text-gray-700">{{ number_format($post->view_count) }}</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Permalink') }}</h3>
                    </div>
                    <div class="p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Slug') }}</label>
                        <input type="text" name="slug" value="{{ old('slug', $post->slug) }}"
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
                    <div class="p-6" x-data="{ imageUrl: '{{ old('featured_image', $post->featured_image) }}' }"
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

                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Categories') }}</h3>
                    </div>
                    <div class="p-6">
                        @php $selectedCats = old('categories', $post->categories->pluck('id')->toArray()); @endphp
                        @if ($categories->isEmpty())
                            <p class="text-sm text-gray-500">{{ __('No categories yet.') }}
                                <a href="{{ route('admin.posts.categories.create') }}" target="_blank" class="text-rj-purple hover:underline">{{ __('Create one →') }}</a>
                            </p>
                        @else
                            <div class="max-h-64 overflow-y-auto rj-scrollbar space-y-1.5 pr-1">
                                @foreach ($categories as $cat)
                                    <label class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                                        <input type="checkbox" name="categories[]" value="{{ $cat->id }}"
                                               {{ in_array($cat->id, $selectedCats) ? 'checked' : '' }}
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
                        @php $selectedTags = old('tags', $post->tags->pluck('id')->toArray()); @endphp
                        @if ($tags->isEmpty())
                            <p class="text-sm text-gray-500">{{ __('No tags yet.') }}
                                <a href="{{ route('admin.posts.tags') }}" target="_blank" class="text-rj-purple hover:underline">{{ __('Create one →') }}</a>
                            </p>
                        @else
                            <div class="max-h-48 overflow-y-auto rj-scrollbar space-y-1.5 pr-1">
                                @foreach ($tags as $tag)
                                    <label class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                               {{ in_array($tag->id, $selectedTags) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                                        <span class="text-sm text-gray-700">{{ $tag->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                @if ($templates->isNotEmpty())
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-base font-semibold text-gray-900">{{ __('Template') }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ __('Assign a builder template to override.') }}</p>
                        </div>
                        <div class="p-6">
                            <select name="template_id" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                                <option value="">{{ __('— Default —') }}</option>
                                @foreach ($templates as $template)
                                    <option value="{{ $template->id }}" {{ old('template_id', $post->template_id) == $template->id ? 'selected' : '' }}>
                                        {{ $template->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif

                @if ($post->status === 'trash')
                    <div class="bg-red-50 rounded-lg border border-red-100 p-4">
                        <h3 class="text-sm font-semibold text-red-900 mb-3">{{ __('Trash Actions') }}</h3>
                        <div class="flex items-center gap-2">
                            <button type="submit" form="restoreForm"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-green-300 text-green-700 text-xs font-medium hover:bg-green-50 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                {{ __('Restore') }}
                            </button>
                            <button type="submit" form="deleteForm"
                                    onclick="return confirm('{{ __('Permanently delete this post? This cannot be undone.') }}')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-red-300 text-red-700 text-xs font-medium hover:bg-red-50 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                {{ __('Delete Permanently') }}
                            </button>
                        </div>
                    </div>
                @endif

            </div>

        </div>

    </form>

    @if ($post->status !== 'trash')
        <div class="flex items-center justify-between border-t border-gray-200 pt-4">
            <div class="text-xs text-gray-500">
                {{ __('Post ID: :id', ['id' => $post->id]) }}
            </div>

            <form method="POST" action="{{ route('admin.posts.trash-action', $post) }}"
                  onsubmit="return confirm('{{ __('Move this post to trash?') }}')">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 text-sm text-red-600 hover:text-red-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                    {{ __('Move to Trash') }}
                </button>
            </form>
        </div>
    @endif

    @if ($post->status === 'trash')
        <form id="restoreForm" method="POST" action="{{ route('admin.posts.restore', $post) }}" class="hidden">
            @csrf
        </form>
        <form id="deleteForm" method="POST" action="{{ route('admin.posts.destroy', $post) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endif

</div>
@endsection
