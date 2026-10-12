@extends('admin.settings.layout')

@section('settings')
<form method="POST" action="{{ route('admin.settings.seo.update') }}" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Meta Tags') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Default title, description and keyword patterns.') }}</p>
        </div>

        <div class="p-6 space-y-5">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Title Template') }}</label>
                <input type="text" name="seo_title_template"
                       value="{{ $options['seo_title_template'] ?? '%title% %sep% %sitename%' }}"
                       class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                <p class="text-xs text-gray-500 mt-1">
                    {{ __('Available tags:') }}
                    <code>%title%</code>,
                    <code>%sep%</code>,
                    <code>%sitename%</code>,
                    <code>%tagline%</code>
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Title Separator') }}</label>
                    <select name="seo_title_separator" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        @foreach ($data['titleSeparators'] as $value => $label)
                            <option value="{{ $value }}" {{ ($options['seo_title_separator'] ?? '-') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Default Keywords') }}</label>
                    <input type="text" name="seo_default_keywords"
                           value="{{ $options['seo_default_keywords'] ?? '' }}"
                           placeholder="cms, laravel, blog"
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Default Meta Description') }}</label>
                <textarea name="seo_default_description" rows="3"
                          placeholder="{{ __('A short description of your website...') }}"
                          class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">{{ $options['seo_default_description'] ?? '' }}</textarea>
                <p class="text-xs text-gray-500 mt-1">{{ __('Recommended: 150-160 characters.') }}</p>
            </div>

            <div class="border-t border-gray-100 pt-5 space-y-4">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="seo_canonical" value="1"
                           {{ ($options['seo_canonical'] ?? '1') === '1' ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <span>
                        <span class="block text-sm font-medium text-gray-700">{{ __('Enable canonical URLs') }}</span>
                        <span class="block text-xs text-gray-500 mt-0.5">{{ __('Prevents duplicate content issues.') }}</span>
                    </span>
                </label>

                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="seo_noindex_archives" value="1"
                           {{ ($options['seo_noindex_archives'] ?? '0') === '1' ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <span>
                        <span class="block text-sm font-medium text-gray-700">{{ __('Noindex archive pages') }}</span>
                        <span class="block text-xs text-gray-500 mt-0.5">{{ __('Hide category, tag and date archives from search engines.') }}</span>
                    </span>
                </label>
            </div>

        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Social Media') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Open Graph and Twitter Card settings.') }}</p>
        </div>

        <div class="p-6 space-y-5">

            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="og_enabled" value="1"
                       {{ ($options['og_enabled'] ?? '1') === '1' ? 'checked' : '' }}
                       class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                <span>
                    <span class="block text-sm font-medium text-gray-700">{{ __('Enable Open Graph') }}</span>
                    <span class="block text-xs text-gray-500 mt-0.5">{{ __('Controls how links look on Facebook, LinkedIn, WhatsApp.') }}</span>
                </span>
            </label>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Site Name') }}</label>
                    <input type="text" name="og_site_name"
                           value="{{ $options['og_site_name'] ?? rj_get_option('site_name', config('app.name')) }}"
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Default OG Type') }}</label>
                    <select name="og_type" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        <option value="website" {{ ($options['og_type'] ?? 'website') === 'website' ? 'selected' : '' }}>{{ __('Website') }}</option>
                        <option value="article" {{ ($options['og_type'] ?? 'website') === 'article' ? 'selected' : '' }}>{{ __('Article') }}</option>
                        <option value="product" {{ ($options['og_type'] ?? 'website') === 'product' ? 'selected' : '' }}>{{ __('Product') }}</option>
                        <option value="profile" {{ ($options['og_type'] ?? 'website') === 'profile' ? 'selected' : '' }}>{{ __('Profile') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Default OG Image') }}</label>
                    <input type="url" name="og_default_image"
                           value="{{ $options['og_default_image'] ?? '' }}"
                           placeholder="https://..."
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                    <p class="text-xs text-gray-500 mt-1">{{ __('Recommended: 1200×630 px') }}</p>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-5 space-y-5">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="twitter_enabled" value="1"
                           {{ ($options['twitter_enabled'] ?? '1') === '1' ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <span>
                        <span class="block text-sm font-medium text-gray-700">{{ __('Enable Twitter Cards') }}</span>
                        <span class="block text-xs text-gray-500 mt-0.5">{{ __('Controls how links look on Twitter/X.') }}</span>
                    </span>
                </label>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Twitter Handle') }}</label>
                        <input type="text" name="twitter_site"
                               value="{{ $options['twitter_site'] ?? '' }}"
                               placeholder="@yoursite"
                               class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Card Type') }}</label>
                        <select name="twitter_card" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                            @foreach ($data['twitterCardTypes'] as $value => $label)
                                <option value="{{ $value }}" {{ ($options['twitter_card'] ?? 'summary_large_image') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Sitemap') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('XML sitemap for search engines.') }}</p>
        </div>

        <div class="p-6 space-y-5">

            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="sitemap_enabled" value="1"
                       {{ ($options['sitemap_enabled'] ?? '1') === '1' ? 'checked' : '' }}
                       class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                <span>
                    <span class="block text-sm font-medium text-gray-700">{{ __('Enable XML sitemap') }}</span>
                    <span class="block text-xs text-gray-500 mt-0.5">{{ __('Automatically generated at /sitemap.xml') }}</span>
                </span>
            </label>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Sitemap URL') }}</label>
                <input type="url" name="sitemap_url"
                       value="{{ $options['sitemap_url'] ?? url('/sitemap.xml') }}"
                       class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
            </div>

            <div class="border-t border-gray-100 pt-5 grid grid-cols-1 md:grid-cols-3 gap-4">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="sitemap_include_posts" value="1"
                           {{ ($options['sitemap_include_posts'] ?? '1') === '1' ? 'checked' : '' }}
                           class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <span class="text-sm text-gray-700">{{ __('Include posts') }}</span>
                </label>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="sitemap_include_pages" value="1"
                           {{ ($options['sitemap_include_pages'] ?? '1') === '1' ? 'checked' : '' }}
                           class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <span class="text-sm text-gray-700">{{ __('Include pages') }}</span>
                </label>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="sitemap_include_categories" value="1"
                           {{ ($options['sitemap_include_categories'] ?? '1') === '1' ? 'checked' : '' }}
                           class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <span class="text-sm text-gray-700">{{ __('Include categories') }}</span>
                </label>
            </div>

        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Robots.txt') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Instructions for search engine crawlers.') }}</p>
        </div>

        <div class="p-6">
            <textarea name="robots_txt" rows="10"
                      class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">{{ $options['robots_txt'] ?? $data['robotsContent'] }}</textarea>
            <p class="text-xs text-gray-500 mt-1">{{ __('Saved to public/robots.txt') }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Schema.org Structured Data') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('JSON-LD markup for rich search results.') }}</p>
        </div>

        <div class="p-6 space-y-5">

            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="schema_enabled" value="1"
                       {{ ($options['schema_enabled'] ?? '1') === '1' ? 'checked' : '' }}
                       class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                <span>
                    <span class="block text-sm font-medium text-gray-700">{{ __('Enable structured data') }}</span>
                    <span class="block text-xs text-gray-500 mt-0.5">{{ __('Adds JSON-LD schema to pages.') }}</span>
                </span>
            </label>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Schema Type') }}</label>
                    <select name="schema_type" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        @foreach ($data['schemaTypes'] as $value => $label)
                            <option value="{{ $value }}" {{ ($options['schema_type'] ?? 'Organization') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Schema Name') }}</label>
                    <input type="text" name="schema_name"
                           value="{{ $options['schema_name'] ?? rj_get_option('site_name', config('app.name')) }}"
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Schema Logo URL') }}</label>
                    <input type="url" name="schema_logo"
                           value="{{ $options['schema_logo'] ?? '' }}"
                           placeholder="https://..."
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                </div>
            </div>

        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Analytics & Verification') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Connect search consoles and analytics tools.') }}</p>
        </div>

        <div class="p-6 space-y-5">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Google Analytics Measurement ID') }}</label>
                <input type="text" name="google_analytics_id"
                       value="{{ $options['google_analytics_id'] ?? '' }}"
                       placeholder="G-XXXXXXXXXX"
                       class="w-full md:w-1/2 rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 border-t border-gray-100 pt-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Google Search Console') }}</label>
                    <input type="text" name="google_search_console"
                           value="{{ $options['google_search_console'] ?? '' }}"
                           placeholder="{{ __('Verification code') }}"
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Bing Webmaster') }}</label>
                    <input type="text" name="bing_verification"
                           value="{{ $options['bing_verification'] ?? '' }}"
                           placeholder="{{ __('Verification code') }}"
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Yandex Webmaster') }}</label>
                    <input type="text" name="yandex_verification"
                           value="{{ $options['yandex_verification'] ?? '' }}"
                           placeholder="{{ __('Verification code') }}"
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                </div>
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