@extends('admin.settings.layout')

@section('settings')
<form method="POST" action="{{ route('admin.settings.writing.update') }}" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Writing Settings') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Configure defaults for posts, editors and content creation.') }}</p>
        </div>

        <div class="p-6 space-y-5">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Default Category') }}</label>
                    <select name="default_category" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        @foreach ($data['categories'] as $value => $label)
                            <option value="{{ $value }}" {{ ($options['default_category'] ?? 'uncategorized') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Default Post Format') }}</label>
                    <select name="default_post_format" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        @foreach ($data['postFormats'] as $value => $label)
                            <option value="{{ $value }}" {{ ($options['default_post_format'] ?? 'standard') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Default Editor') }}</label>
                    <select name="default_editor" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        @foreach ($data['editors'] as $value => $label)
                            <option value="{{ $value }}" {{ ($options['default_editor'] ?? 'block') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Default Post Status') }}</label>
                    <select name="default_post_status" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        @foreach ($data['postStatuses'] as $value => $label)
                            <option value="{{ $value }}" {{ ($options['default_post_status'] ?? 'draft') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Content Limits') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Maximum lengths and content constraints.') }}</p>
        </div>

        <div class="p-6 space-y-5">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Title Max Length') }}</label>
                    <input type="number" name="title_max_length" min="10" max="500"
                           value="{{ $options['title_max_length'] ?? '200' }}"
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    <p class="text-xs text-gray-500 mt-1">{{ __('Characters') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Slug Max Length') }}</label>
                    <input type="number" name="slug_max_length" min="10" max="200"
                           value="{{ $options['slug_max_length'] ?? '80' }}"
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    <p class="text-xs text-gray-500 mt-1">{{ __('Characters') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Excerpt Length') }}</label>
                    <input type="number" name="excerpt_length" min="10" max="200"
                           value="{{ $options['excerpt_length'] ?? '55' }}"
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    <p class="text-xs text-gray-500 mt-1">{{ __('Words') }}</p>
                </div>
            </div>

        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Auto-save & Revisions') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Control autosave frequency and revision history.') }}</p>
        </div>

        <div class="p-6 space-y-5">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Autosave Interval') }}</label>
                    <input type="number" name="autosave_interval" min="10" max="600"
                           value="{{ $options['autosave_interval'] ?? '60' }}"
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    <p class="text-xs text-gray-500 mt-1">{{ __('Seconds between auto-saves') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Revision Count') }}</label>
                    <input type="number" name="revision_count" min="1" max="100"
                           value="{{ $options['revision_count'] ?? '10' }}"
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    <p class="text-xs text-gray-500 mt-1">{{ __('Number of revisions to keep per post') }}</p>
                </div>
            </div>

        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Default Behavior') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('New post defaults and content features.') }}</p>
        </div>

        <div class="p-6 space-y-4">

            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="allow_comments_default" value="1"
                       {{ ($options['allow_comments_default'] ?? '1') === '1' ? 'checked' : '' }}
                       class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                <span>
                    <span class="block text-sm font-medium text-gray-700">{{ __('Allow comments by default') }}</span>
                    <span class="block text-xs text-gray-500 mt-0.5">{{ __('New posts will have comments enabled automatically.') }}</span>
                </span>
            </label>

            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="allow_pingbacks" value="1"
                       {{ ($options['allow_pingbacks'] ?? '1') === '1' ? 'checked' : '' }}
                       class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                <span>
                    <span class="block text-sm font-medium text-gray-700">{{ __('Allow pingbacks and trackbacks') }}</span>
                    <span class="block text-xs text-gray-500 mt-0.5">{{ __('Notify other sites when you link to them.') }}</span>
                </span>
            </label>

        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
            {{ __('Save Changes') }}
        </button>
    </div>

</form>
@endsection
