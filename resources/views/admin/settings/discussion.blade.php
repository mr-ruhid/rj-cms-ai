@extends('admin.settings.layout')

@section('settings')
<form method="POST" action="{{ route('admin.settings.discussion.update') }}" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Discussion Settings') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Configure comments, moderation and avatars.') }}</p>
        </div>

        <div class="p-6 space-y-5">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Default Comment Status') }}</label>
                    <select name="default_comment_status" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        <option value="open" {{ ($options['default_comment_status'] ?? 'open') === 'open' ? 'selected' : '' }}>{{ __('Open') }}</option>
                        <option value="closed" {{ ($options['default_comment_status'] ?? 'open') === 'closed' ? 'selected' : '' }}>{{ __('Closed') }}</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">{{ __('Applied to new posts.') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Auto-close Comments After') }}</label>
                    <input type="number" name="close_comments_days" min="0" max="365"
                           value="{{ $options['close_comments_days'] ?? '14' }}"
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    <p class="text-xs text-gray-500 mt-1">{{ __('Days. Use 0 to disable auto-close.') }}</p>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-5 space-y-4">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="comment_registration" value="1"
                           {{ ($options['comment_registration'] ?? '0') === '1' ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <span>
                        <span class="block text-sm font-medium text-gray-700">{{ __('Users must be registered and logged in') }}</span>
                        <span class="block text-xs text-gray-500 mt-0.5">{{ __('Only registered users can comment.') }}</span>
                    </span>
                </label>

                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="comments_notify" value="1"
                           {{ ($options['comments_notify'] ?? '1') === '1' ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <span>
                        <span class="block text-sm font-medium text-gray-700">{{ __('Email me whenever a comment is posted') }}</span>
                    </span>
                </label>

                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="moderation_notify" value="1"
                           {{ ($options['moderation_notify'] ?? '1') === '1' ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <span>
                        <span class="block text-sm font-medium text-gray-700">{{ __('Email me whenever a comment is held for moderation') }}</span>
                    </span>
                </label>
            </div>

        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Moderation') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Control how comments are reviewed before publishing.') }}</p>
        </div>

        <div class="p-6 space-y-5">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Moderation Level') }}</label>
                <select name="moderation_level" class="w-full md:w-1/2 rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    @foreach ($data['moderationLevels'] as $value => $label)
                        <option value="{{ $value }}" {{ ($options['moderation_level'] ?? 'manual') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="border-t border-gray-100 pt-5 space-y-4">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="comment_moderation" value="1"
                           {{ ($options['comment_moderation'] ?? '0') === '1' ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <span>
                        <span class="block text-sm font-medium text-gray-700">{{ __('Comment must be manually approved') }}</span>
                    </span>
                </label>

                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="comment_previously_approved" value="1"
                           {{ ($options['comment_previously_approved'] ?? '1') === '1' ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <span>
                        <span class="block text-sm font-medium text-gray-700">{{ __('Comment author must have a previously approved comment') }}</span>
                    </span>
                </label>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 border-t border-gray-100 pt-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Max Links Before Moderation') }}</label>
                    <input type="number" name="comment_max_links" min="0" max="100"
                           value="{{ $options['comment_max_links'] ?? '2' }}"
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    <p class="text-xs text-gray-500 mt-1">{{ __('Comments with more links go to moderation.') }}</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Disallowed Comment Keys') }}</label>
                <textarea name="disallowed_keys" rows="4"
                          placeholder="{{ __('One word or IP per line...') }}"
                          class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">{{ $options['disallowed_keys'] ?? '' }}</textarea>
                <p class="text-xs text-gray-500 mt-1">{{ __('Words, URLs or IPs that trigger automatic moderation.') }}</p>
            </div>

        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Threading & Pagination') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('How comments are nested and displayed.') }}</p>
        </div>

        <div class="p-6 space-y-5">

            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="thread_comments" value="1"
                       {{ ($options['thread_comments'] ?? '1') === '1' ? 'checked' : '' }}
                       class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                <span>
                    <span class="block text-sm font-medium text-gray-700">{{ __('Enable threaded (nested) comments') }}</span>
                </span>
            </label>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Thread Depth') }}</label>
                    <input type="number" name="thread_comments_depth" min="1" max="10"
                           value="{{ $options['thread_comments_depth'] ?? '5' }}"
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    <p class="text-xs text-gray-500 mt-1">{{ __('Levels of nesting.') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Comment Order') }}</label>
                    <select name="comment_order" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        @foreach ($data['commentOrder'] as $value => $label)
                            <option value="{{ $value }}" {{ ($options['comment_order'] ?? 'asc') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <label class="flex items-start gap-3 cursor-pointer border-t border-gray-100 pt-5">
                <input type="checkbox" name="page_comments" value="1"
                       {{ ($options['page_comments'] ?? '0') === '1' ? 'checked' : '' }}
                       class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                <span>
                    <span class="block text-sm font-medium text-gray-700">{{ __('Break comments into pages') }}</span>
                    <span class="block text-xs text-gray-500 mt-0.5">{{ __('Paginate long comment threads.') }}</span>
                </span>
            </label>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Comments per Page') }}</label>
                    <input type="number" name="comments_per_page" min="1" max="200"
                           value="{{ $options['comments_per_page'] ?? '50' }}"
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Default Page Display') }}</label>
                    <select name="default_comments_page" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        @foreach ($data['commentPages'] as $value => $label)
                            <option value="{{ $value }}" {{ ($options['default_comments_page'] ?? 'newest') === $value ? 'selected' : '' }}>
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
            <h2 class="text-base font-semibold text-gray-900">{{ __('Avatars') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('How user avatars are displayed next to comments.') }}</p>
        </div>

        <div class="p-6 space-y-5">

            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="show_avatars" value="1"
                       {{ ($options['show_avatars'] ?? '1') === '1' ? 'checked' : '' }}
                       class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                <span>
                    <span class="block text-sm font-medium text-gray-700">{{ __('Show avatars') }}</span>
                </span>
            </label>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 border-t border-gray-100 pt-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Avatar Source') }}</label>
                    <select name="avatar_source" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        @foreach ($data['avatarSources'] as $value => $label)
                            <option value="{{ $value }}" {{ ($options['avatar_source'] ?? 'gravatar') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Maximum Rating') }}</label>
                    <select name="avatar_rating" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        <option value="G" {{ ($options['avatar_rating'] ?? 'G') === 'G' ? 'selected' : '' }}>G</option>
                        <option value="PG" {{ ($options['avatar_rating'] ?? 'G') === 'PG' ? 'selected' : '' }}>PG</option>
                        <option value="R" {{ ($options['avatar_rating'] ?? 'G') === 'R' ? 'selected' : '' }}>R</option>
                        <option value="X" {{ ($options['avatar_rating'] ?? 'G') === 'X' ? 'selected' : '' }}>X</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Avatar Size') }}</label>
                    <input type="number" name="avatar_size" min="16" max="256"
                           value="{{ $options['avatar_size'] ?? '48' }}"
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    <p class="text-xs text-gray-500 mt-1">{{ __('Pixels') }}</p>
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
