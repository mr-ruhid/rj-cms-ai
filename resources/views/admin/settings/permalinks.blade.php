@extends('admin.settings.layout')

@section('settings')
<form method="POST" action="{{ route('admin.settings.permalinks.update') }}" class="space-y-6" x-data="{ structure: '{{ $options['permalink_structure'] ?? 'name' }}' }">
    @csrf
    @method('PUT')

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Permalink Settings') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Choose how URLs for posts and pages are structured.') }}</p>
        </div>

        <div class="p-6 space-y-4">

            <p class="text-sm text-gray-600">{{ __('Common Settings') }}</p>

            @foreach ($data['structures'] as $key => $item)
                <div class="space-y-2">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="radio" name="permalink_structure" value="{{ $key }}"
                               x-model="structure"
                               {{ ($options['permalink_structure'] ?? 'name') === $key ? 'checked' : '' }}
                               class="mt-1 border-gray-300 text-rj-purple focus:ring-rj-purple">
                        <span class="flex-1">
                            <span class="block text-sm font-medium text-gray-800">{{ $item['label'] }}</span>
                            <span class="block text-xs text-gray-500 mt-0.5 font-mono">{{ $item['example'] }}</span>
                        </span>
                    </label>

                    @if ($key === 'custom')
                        <div x-show="structure === 'custom'" x-cloak x-transition class="pl-8 pt-1">
                            <input type="text" name="custom_structure"
                                   value="{{ $options['custom_structure'] ?? '' }}"
                                   placeholder="/%category%/%postname%/"
                                   class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                            <p class="text-xs text-gray-500 mt-1">
                                {{ __('Available tags:') }}
                                <code class="font-mono">%year%</code>,
                                <code class="font-mono">%monthnum%</code>,
                                <code class="font-mono">%day%</code>,
                                <code class="font-mono">%postname%</code>,
                                <code class="font-mono">%post_id%</code>,
                                <code class="font-mono">%category%</code>
                            </p>
                        </div>
                    @endif
                </div>
            @endforeach

        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Optional') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Customize base paths for categories, tags and authors.') }}</p>
        </div>

        <div class="p-6 space-y-5">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Category Base') }}</label>
                    <input type="text" name="category_base"
                           value="{{ $options['category_base'] ?? 'category' }}"
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                    <p class="text-xs text-gray-500 mt-1">/{{ $options['category_base'] ?? 'category' }}/news/</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Tag Base') }}</label>
                    <input type="text" name="tag_base"
                           value="{{ $options['tag_base'] ?? 'tag' }}"
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                    <p class="text-xs text-gray-500 mt-1">/{{ $options['tag_base'] ?? 'tag' }}/laravel/</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Author Base') }}</label>
                    <input type="text" name="author_base"
                           value="{{ $options['author_base'] ?? 'author' }}"
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                    <p class="text-xs text-gray-500 mt-1">/{{ $options['author_base'] ?? 'author' }}/admin/</p>
                </div>
            </div>

        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Advanced') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Additional URL normalization and SEO options.') }}</p>
        </div>

        <div class="p-6 space-y-5">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Slug Case') }}</label>
                    <select name="post_slug_case" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        <option value="lower" {{ ($options['post_slug_case'] ?? 'lower') === 'lower' ? 'selected' : '' }}>{{ __('Lowercase only') }}</option>
                        <option value="keep" {{ ($options['post_slug_case'] ?? 'lower') === 'keep' ? 'selected' : '' }}>{{ __('Keep original case') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Trailing Slash') }}</label>
                    <select name="trailing_slash" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        <option value="yes" {{ ($options['trailing_slash'] ?? 'yes') === 'yes' ? 'selected' : '' }}>{{ __('Add trailing slash') }} (/sample-post/)</option>
                        <option value="no" {{ ($options['trailing_slash'] ?? 'yes') === 'no' ? 'selected' : '' }}>{{ __('No trailing slash') }} (/sample-post)</option>
                    </select>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-5 space-y-4">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="strip_stop_words" value="1"
                           {{ ($options['strip_stop_words'] ?? '0') === '1' ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <span>
                        <span class="block text-sm font-medium text-gray-700">{{ __('Strip stop words from slugs') }}</span>
                        <span class="block text-xs text-gray-500 mt-0.5">{{ __('Removes words like "the", "and", "of" from URLs.') }}</span>
                    </span>
                </label>

                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="redirect_old_urls" value="1"
                           {{ ($options['redirect_old_urls'] ?? '1') === '1' ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <span>
                        <span class="block text-sm font-medium text-gray-700">{{ __('Redirect old URLs') }}</span>
                        <span class="block text-xs text-gray-500 mt-0.5">{{ __('Automatically redirect when a slug changes (301).') }}</span>
                    </span>
                </label>

                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="force_lowercase" value="1"
                           {{ ($options['force_lowercase'] ?? '1') === '1' ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <span>
                        <span class="block text-sm font-medium text-gray-700">{{ __('Force lowercase URLs') }}</span>
                        <span class="block text-xs text-gray-500 mt-0.5">{{ __('Convert /Sample-Post to /sample-post automatically.') }}</span>
                    </span>
                </label>
            </div>

        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
            {{ __('Save Changes') }}
        </button>
    </div>

</form>
@endsection
