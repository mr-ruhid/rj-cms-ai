@extends('admin.settings.layout')

@section('title', __('Notifications'))

@section('settings')
<div class="space-y-6">

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-base font-semibold text-gray-900">{{ __('Notifications') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Configure email alerts for various events on your site.') }}</p>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded-r-lg">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-r-lg">
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.notifications.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- RECIPIENT ADDRESS --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-sm font-bold text-gray-900">{{ __('Recipient Address') }}</h3>
            </div>
            
            <div class="p-6">
                <div class="max-w-md">
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Admin Email Address') }}</label>
                    <input type="email" name="admin_email" value="{{ old('admin_email', $settings['admin_email']) }}" required
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    <p class="text-xs text-gray-500 mt-2">{{ __('Where should system notifications be sent?') }}</p>
                </div>
            </div>
        </div>

        {{-- EVENTS --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900">{{ __('Email Notifications') }}</h3>
                <span class="text-xs font-medium text-gray-500">{{ __('Toggle the events you want to be notified about.') }}</span>
            </div>

            <div class="divide-y divide-gray-100">

                {{-- New Comment --}}
                <div class="p-6 flex items-start justify-between gap-4">
                    <div>
                        <div class="text-sm font-semibold text-gray-900">{{ __('New Comment') }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ __('Get an email when someone leaves a comment on your site.') }}</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 mt-1">
                        <input type="hidden" name="events[new_comment]" value="0">
                        <input type="checkbox" name="events[new_comment]" value="1" class="sr-only peer"
                               {{ old('events.new_comment', $settings['events']['new_comment']) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-rj-purple/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rj-purple"></div>
                    </label>
                </div>

                {{-- New User --}}
                <div class="p-6 flex items-start justify-between gap-4">
                    <div>
                        <div class="text-sm font-semibold text-gray-900">{{ __('New User Registration') }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ __('Get an email when a new user registers on your site.') }}</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 mt-1">
                        <input type="hidden" name="events[new_user]" value="0">
                        <input type="checkbox" name="events[new_user]" value="1" class="sr-only peer"
                               {{ old('events.new_user', $settings['events']['new_user']) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-rj-purple/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rj-purple"></div>
                    </label>
                </div>

                {{-- Post Published --}}
                <div class="p-6 flex items-start justify-between gap-4">
                    <div>
                        <div class="text-sm font-semibold text-gray-900">{{ __('Post Published') }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ __('Get an email when a new post or page is published.') }}</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 mt-1">
                        <input type="hidden" name="events[post_published]" value="0">
                        <input type="checkbox" name="events[post_published]" value="1" class="sr-only peer"
                               {{ old('events.post_published', $settings['events']['post_published']) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-rj-purple/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rj-purple"></div>
                    </label>
                </div>

                {{-- Update Available --}}
                <div class="p-6 flex items-start justify-between gap-4">
                    <div>
                        <div class="text-sm font-semibold text-gray-900">{{ __('Update Available') }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ __('Get an email when RJ CMS, themes, or plugins have updates available.') }}</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 mt-1">
                        <input type="hidden" name="events[update_available]" value="0">
                        <input type="checkbox" name="events[update_available]" value="1" class="sr-only peer"
                               {{ old('events.update_available', $settings['events']['update_available']) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-rj-purple/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rj-purple"></div>
                    </label>
                </div>

                {{-- Failed Login --}}
                <div class="p-6 flex items-start justify-between gap-4">
                    <div>
                        <div class="text-sm font-semibold text-gray-900">{{ __('Failed Login Attempt') }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ __('Get an email when someone fails to log in to the admin panel.') }}</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 mt-1">
                        <input type="hidden" name="events[failed_login]" value="0">
                        <input type="checkbox" name="events[failed_login]" value="1" class="sr-only peer"
                               {{ old('events.failed_login', $settings['events']['failed_login']) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-rj-purple/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rj-purple"></div>
                    </label>
                </div>

                {{-- Form Submission --}}
                <div class="p-6 flex items-start justify-between gap-4">
                    <div>
                        <div class="text-sm font-semibold text-gray-900">{{ __('Form Submission') }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ __('Get an email when a user submits a form on your site.') }}</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 mt-1">
                        <input type="hidden" name="events[form_submission]" value="0">
                        <input type="checkbox" name="events[form_submission]" value="1" class="sr-only peer"
                               {{ old('events.form_submission', $settings['events']['form_submission']) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-rj-purple/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rj-purple"></div>
                    </label>
                </div>

            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ __('Save Changes') }}
            </button>
        </div>

    </form>
</div>
@endsection