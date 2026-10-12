@extends('admin.settings.layout')

@section('settings')
<form method="POST" action="{{ route('admin.settings.reading.update') }}" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Reading Settings') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Configure how your site displays posts and content.') }}</p>
        </div>

        <div class="p-6 space-y-5">

            <div class="space-y-3">
                <label class="block text-sm font-medium text-gray-700">{{ __('Homepage Displays') }}</label>

                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="radio" name="homepage_type" value="latest"
                           {{ ($options['homepage_type'] ?? 'latest') === 'latest' ? 'checked' : '' }}
                           class="mt-0.5 border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <span>
                        <span class="block text-sm font-medium text-gray-700">{{ __('Your latest posts') }}</span>
                        <span class="block text-xs text-gray-500 mt-0.5">{{ __('Show the most recent posts on the homepage.') }}</span>
                    </span>
                </label>

                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="radio" name="homepage_type" value="static"
                           {{ ($options['homepage_type'] ?? 'latest') === 'static' ? 'checked' : '' }}
                           class="mt-0.5 border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <span>
                        <span class="block text-sm font-medium text-gray-700">{{ __('A static page') }}</span>
                        <span class="block text-xs text-gray-500 mt-0.5">{{ __('Use a custom page as the homepage.') }}</span>
                    </span>
                </label>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 border-t border-gray-100 pt-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Homepage Page') }}</label>
                    <input type="text" name="homepage_page"
                           value="{{ $options['homepage_page'] ?? '' }}"
                           placeholder="{{ __('e.g. home, landing') }}"
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    <p class="text-xs text-gray-500 mt-1">{{ __('Only used when "A static page" is selected.') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Posts Page') }}</label>
                    <input type="text" name="posts_page"
                           value="{{ $options['posts_page'] ?? '' }}"
                           placeholder="{{ __('e.g. blog, news') }}"
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    <p class="text-xs text-gray-500 mt-1">{{ __('Page where all posts are listed.') }}</p>
                </div>
            </div>

        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Pagination & Feeds') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Control how many posts are shown and how feeds behave.') }}</p>
        </div>

        <div class="p-6 space-y-5">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Posts per Page') }}</label>
                    <input type="number" name="posts_per_page" min="1" max="100"
                           value="{{ $options['posts_per_page'] ?? '10' }}"
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    <p class="text-xs text-gray-500 mt-1">{{ __('Number of posts shown per page.') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Posts per RSS Feed') }}</label>
                    <input type="number" name="posts_per_rss" min="1" max="100"
                           value="{{ $options['posts_per_rss'] ?? '10' }}"
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    <p class="text-xs text-gray-500 mt-1">{{ __('Number of posts in RSS feed.') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 border-t border-gray-100 pt-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Feed Content') }}</label>
                    <select name="feed_type" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        @foreach ($data['feedTypes'] as $value => $label)
                            <option value="{{ $value }}" {{ ($options['feed_type'] ?? 'full') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">{{ __('Full text or summary in RSS feed.') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Feed Items Limit') }}</label>
                    <select name="feed_items" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        @foreach ($data['feedItems'] as $value => $label)
                            <option value="{{ $value }}" {{ ($options['feed_items'] ?? 20) == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">{{ __('Maximum items kept in feed.') }}</p>
                </div>
            </div>

        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Post Display Elements') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Choose which elements appear on post pages.') }}</p>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">

            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="show_featured_image" value="1"
                       {{ ($options['show_featured_image'] ?? '1') === '1' ? 'checked' : '' }}
                       class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                <span class="block text-sm text-gray-700">{{ __('Featured image') }}</span>
            </label>

            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="show_author" value="1"
                       {{ ($options['show_author'] ?? '1') === '1' ? 'checked' : '' }}
                       class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                <span class="block text-sm text-gray-700">{{ __('Author name') }}</span>
            </label>

            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="show_date" value="1"
                       {{ ($options['show_date'] ?? '1') === '1' ? 'checked' : '' }}
                       class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                <span class="block text-sm text-gray-700">{{ __('Publish date') }}</span>
            </label>

            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="show_category" value="1"
                       {{ ($options['show_category'] ?? '1') === '1' ? 'checked' : '' }}
                       class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                <span class="block text-sm text-gray-700">{{ __('Categories') }}</span>
            </label>

            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="show_tags" value="1"
                       {{ ($options['show_tags'] ?? '1') === '1' ? 'checked' : '' }}
                       class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                <span class="block text-sm text-gray-700">{{ __('Tags') }}</span>
            </label>

            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="show_reading_time" value="1"
                       {{ ($options['show_reading_time'] ?? '1') === '1' ? 'checked' : '' }}
                       class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                <span class="block text-sm text-gray-700">{{ __('Reading time') }}</span>
            </label>

            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="show_share_buttons" value="1"
                       {{ ($options['show_share_buttons'] ?? '1') === '1' ? 'checked' : '' }}
                       class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                <span class="block text-sm text-gray-700">{{ __('Share buttons') }}</span>
            </label>

            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="enable_related_posts" value="1"
                       {{ ($options['enable_related_posts'] ?? '1') === '1' ? 'checked' : '' }}
                       class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                <span class="block text-sm text-gray-700">{{ __('Related posts') }}</span>
            </label>

        </div>

        <div class="px-6 pb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Related Posts Count') }}</label>
            <input type="number" name="related_posts_count" min="1" max="12"
                   value="{{ $options['related_posts_count'] ?? '3' }}"
                   class="w-full md:w-48 rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
            <p class="text-xs text-gray-500 mt-1">{{ __('Number of related posts shown at the bottom.') }}</p>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
            {{ __('Save Changes') }}
        </button>
    </div>

</form>
@endsection
