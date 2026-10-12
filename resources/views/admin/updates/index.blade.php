@extends('admin.layouts.app')

@section('title', __('Updates'))

@section('content')
<div class="space-y-6">

    <div class="bg-gradient-to-br from-rj-dark-1 via-rj-dark-2 to-rj-dark-3 rounded-2xl p-6 text-white overflow-hidden relative">
        <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full bg-rj-purple/20 blur-3xl"></div>
        <div class="absolute -bottom-10 -left-10 w-40 h-40 rounded-full bg-rj-pink/20 blur-3xl"></div>

        <div class="relative flex items-start justify-between gap-6 flex-wrap">
            <div>
                <div class="text-xs uppercase tracking-wider text-rj-purple-light font-medium">{{ __('RJ CMS') }}</div>
                <div class="mt-2 flex items-end gap-3">
                    <h2 class="text-3xl font-bold">v{{ $data['currentVersion'] }}</h2>
                    @if (!empty($data['lastRelease']['has_update']))
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rj-pink/20 text-rj-pink border border-rj-pink/30 text-xs font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-rj-pink animate-pulse"></span>
                            {{ __('Update available') }}
                        </span>
                    @endif
                </div>
                <p class="text-sm text-gray-400 mt-2">
                    {{ __('Current installed version of RJ CMS.') }}
                </p>
            </div>

            <form method="POST" action="{{ route('admin.updates.check') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-white/10 hover:bg-white/20 border border-white/10 text-white text-sm font-medium transition backdrop-blur-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    {{ __('Check for Updates') }}
                </button>
            </form>
        </div>

        @if (!empty($data['lastChecked']))
            <div class="relative mt-4 text-xs text-gray-400">
                {{ __('Last checked') }}: {{ $data['lastChecked'] }}
            </div>
        @endif
    </div>

    @if (!empty($data['lastRelease']))
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-lg {{ !empty($data['lastRelease']['has_update']) ? 'bg-gradient-to-br from-rj-purple to-rj-pink' : 'bg-green-100' }} flex items-center justify-center flex-shrink-0">
                        @if (!empty($data['lastRelease']['has_update']))
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>
                        @else
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-semibold text-gray-900 truncate">
                            {{ $data['lastRelease']['name'] ?? $data['lastRelease']['version'] }}
                        </div>
                        <div class="text-xs text-gray-500 mt-0.5">
                            v{{ $data['lastRelease']['version'] }}
                            @if (!empty($data['lastRelease']['published_at']))
                                · {{ \Carbon\Carbon::parse($data['lastRelease']['published_at'])->diffForHumans() }}
                            @endif
                        </div>
                    </div>
                </div>

                @if (!empty($data['lastRelease']['url']))
                    <a href="{{ $data['lastRelease']['url'] }}" target="_blank"
                       class="text-xs text-rj-purple hover:underline flex items-center gap-1 flex-shrink-0">
                        {{ __('View on GitHub') }}
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                @endif
            </div>

            @if (!empty($data['lastRelease']['body']))
                <div class="px-6 py-5">
                    <div class="text-xs uppercase tracking-wider text-gray-500 font-medium mb-3">{{ __('Release Notes') }}</div>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {!! nl2br(e($data['lastRelease']['body'])) !!}
                    </div>
                </div>
            @endif

            @if (!empty($data['lastRelease']['has_update']))
                <div class="px-6 py-4 border-t border-gray-100 bg-rj-purple/5 flex items-center justify-between gap-4 flex-wrap">
                    <div class="text-sm text-gray-700">
                        {{ __('A newer version is available. Follow the installation steps below.') }}
                    </div>
                    <form method="POST" action="{{ route('admin.updates.run') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            {{ __('Prepare Update') }}
                        </button>
                    </form>
                </div>
            @endif
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-12 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>
            <p class="mt-3 text-sm text-gray-500">{{ __('No update information yet.') }}</p>
            <p class="mt-1 text-xs text-gray-400">{{ __('Click "Check for Updates" to see if a new version is available.') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Update Settings') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Configure where updates are fetched from.') }}</p>
        </div>

        <form method="POST" action="{{ route('admin.updates.update') }}" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('GitHub Repository') }}</label>
                <input type="text" name="update_repo"
                       value="{{ $options['update_repo'] ?? 'mr-ruhid/rj-cms-ai' }}"
                       placeholder="username/repository"
                       class="w-full md:w-1/2 rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                <p class="text-xs text-gray-500 mt-1">{{ __('Format: username/repository') }}</p>
            </div>

            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="auto_check" value="1"
                       {{ ($options['auto_check'] ?? '1') === '1' ? 'checked' : '' }}
                       class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                <span>
                    <span class="block text-sm font-medium text-gray-700">{{ __('Auto-check for updates daily') }}</span>
                    <span class="block text-xs text-gray-500 mt-0.5">{{ __('Sends a background request once every 24 hours.') }}</span>
                </span>
            </label>

            <div class="border-t border-gray-100 pt-5">
                <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                    {{ __('Save Settings') }}
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Manual Update Instructions') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Follow these steps to update RJ CMS manually.') }}</p>
        </div>

        <div class="p-6 space-y-4">

            <div class="flex gap-4">
                <div class="w-7 h-7 rounded-full bg-rj-purple/10 text-rj-purple flex items-center justify-center text-xs font-bold flex-shrink-0">1</div>
                <div class="text-sm text-gray-700">
                    <span class="font-medium">{{ __('Backup your site') }}</span> — {{ __('Always backup database and files before updating.') }}
                </div>
            </div>

            <div class="flex gap-4">
                <div class="w-7 h-7 rounded-full bg-rj-purple/10 text-rj-purple flex items-center justify-center text-xs font-bold flex-shrink-0">2</div>
                <div class="text-sm text-gray-700">
                    <span class="font-medium">{{ __('Enable maintenance mode') }}</span> — {{ __('Settings → Advanced → Enable maintenance mode.') }}
                </div>
            </div>

            <div class="flex gap-4">
                <div class="w-7 h-7 rounded-full bg-rj-purple/10 text-rj-purple flex items-center justify-center text-xs font-bold flex-shrink-0">3</div>
                <div class="text-sm text-gray-700">
                    <span class="font-medium">{{ __('Download the update') }}</span> — {{ __('Get the latest release from GitHub.') }}
                </div>
            </div>

            <div class="flex gap-4">
                <div class="w-7 h-7 rounded-full bg-rj-purple/10 text-rj-purple flex items-center justify-center text-xs font-bold flex-shrink-0">4</div>
                <div class="text-sm text-gray-700">
                    <span class="font-medium">{{ __('Replace files') }}</span> — {{ __('Upload new files, keeping .env and storage/ folders.') }}
                </div>
            </div>

            <div class="flex gap-4">
                <div class="w-7 h-7 rounded-full bg-rj-purple/10 text-rj-purple flex items-center justify-center text-xs font-bold flex-shrink-0">5</div>
                <div class="text-sm text-gray-700">
                    <span class="font-medium">{{ __('Run commands') }}</span>
                    <div class="mt-2 bg-gray-900 rounded-lg p-3 font-mono text-xs text-green-400 space-y-1">
                        <div>composer install --no-dev</div>
                        <div>php artisan migrate --force</div>
                        <div>php artisan optimize:clear</div>
                    </div>
                </div>
            </div>

            <div class="flex gap-4">
                <div class="w-7 h-7 rounded-full bg-rj-purple/10 text-rj-purple flex items-center justify-center text-xs font-bold flex-shrink-0">6</div>
                <div class="text-sm text-gray-700">
                    <span class="font-medium">{{ __('Disable maintenance mode') }}</span> — {{ __('Your site is now updated.') }}
                </div>
            </div>

        </div>
    </div>

</div>
@endsection