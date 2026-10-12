@extends('admin.settings.layout')

@section('settings')
<div class="space-y-6" x-data="{ mode: '{{ $options['backup_storage_mode'] ?? 'local' }}' }">

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Backup Settings') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Configure automatic backups and storage mode.') }}</p>
        </div>

        <form method="POST" action="{{ route('admin.settings.backup.update') }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('Storage Mode') }}</label>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                    <label class="relative cursor-pointer">
                        <input type="radio" name="backup_storage_mode" value="local" x-model="mode" class="sr-only">
                        <div class="p-4 rounded-xl border-2 transition"
                             :class="mode === 'local' ? 'border-rj-purple bg-rj-purple/5' : 'border-gray-200 hover:border-gray-300'">
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-lg bg-rj-purple/10 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-rj-purple" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ __('Local Storage') }}</div>
                                    <div class="text-xs text-gray-500 mt-1">{{ __('Backups are saved on your server. Can download later.') }}</div>
                                </div>
                            </div>
                        </div>
                    </label>

                    <label class="relative cursor-pointer">
                        <input type="radio" name="backup_storage_mode" value="download" x-model="mode" class="sr-only">
                        <div class="p-4 rounded-xl border-2 transition"
                             :class="mode === 'download' ? 'border-rj-purple bg-rj-purple/5' : 'border-gray-200 hover:border-gray-300'">
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-lg bg-rj-purple/10 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-rj-purple" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ __('Download Only') }}</div>
                                    <div class="text-xs text-gray-500 mt-1">{{ __('Not saved on server. Downloads immediately. For small hosting.') }}</div>
                                </div>
                            </div>
                        </div>
                    </label>

                </div>
            </div>

            <div x-show="mode === 'local'" x-cloak x-transition class="border-t border-gray-100 pt-5 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Automatic Schedule') }}</label>
                    <select name="backup_schedule" class="w-full md:w-80 rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        @foreach ($data['schedules'] as $value => $label)
                            <option value="{{ $value }}" {{ ($options['backup_schedule'] ?? 'never') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">{{ __('Requires cron job: php artisan schedule:run every minute.') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Keep Last N Backups') }}</label>
                    <input type="number" name="backup_retention" min="1" max="100"
                           value="{{ $options['backup_retention'] ?? '10' }}"
                           class="w-full md:w-40 rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    <p class="text-xs text-gray-500 mt-1">{{ __('Older backups are automatically deleted.') }}</p>
                </div>
            </div>

            <div x-show="mode === 'download'" x-cloak class="border-t border-gray-100 pt-5">
                <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium">{{ __('Download mode active') }}</p>
                        <p class="mt-1 text-blue-700">{{ __('Backups are not stored on the server. When you click "Create Backup", the file downloads directly to your device. Automatic schedule is disabled.') }}</p>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-5">
                <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('Include in Backup') }}</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="backup_include_db" value="1"
                               {{ ($options['backup_include_db'] ?? '1') === '1' ? 'checked' : '' }}
                               class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                        <span>
                            <span class="block text-sm font-medium text-gray-700">{{ __('Database') }}</span>
                            <span class="block text-xs text-gray-500 mt-0.5">{{ __('MySQL dump of all tables') }}</span>
                        </span>
                    </label>

                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="backup_include_files" value="1"
                               {{ ($options['backup_include_files'] ?? '1') === '1' ? 'checked' : '' }}
                               class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                        <span>
                            <span class="block text-sm font-medium text-gray-700">{{ __('Files') }}</span>
                            <span class="block text-xs text-gray-500 mt-0.5">{{ __('Uploads and public storage') }}</span>
                        </span>
                    </label>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-5">
                <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                    {{ __('Save Settings') }}
                </button>
            </div>

        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-semibold text-gray-900">{{ __('Create Manual Backup') }}</h2>
                <p class="text-sm text-gray-500 mt-1">
                    <span x-show="mode === 'local'">{{ __('Backup will be saved on server.') }}</span>
                    <span x-show="mode === 'download'" x-cloak>{{ __('Backup will download immediately.') }}</span>
                </p>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-3">

            <form method="POST" action="{{ route('admin.settings.backup.create') }}">
                @csrf
                <input type="hidden" name="type" value="database">
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-4 rounded-xl border border-gray-200 hover:border-rj-purple/30 hover:bg-rj-purple/5 transition text-left">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900">{{ __('Database Only') }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">{{ __('MySQL dump (.sql)') }}</div>
                    </div>
                </button>
            </form>

            <form method="POST" action="{{ route('admin.settings.backup.create') }}">
                @csrf
                <input type="hidden" name="type" value="site">
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-4 rounded-xl border border-gray-200 hover:border-rj-purple/30 hover:bg-rj-purple/5 transition text-left">
                    <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900">{{ __('Files Only') }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">{{ __('Uploads and storage (.zip)') }}</div>
                    </div>
                </button>
            </form>

            <form method="POST" action="{{ route('admin.settings.backup.create') }}">
                @csrf
                <input type="hidden" name="type" value="full">
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-4 rounded-xl border-2 border-rj-purple/20 hover:border-rj-purple/40 bg-rj-purple/5 hover:bg-rj-purple/10 transition text-left">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900">{{ __('Full Backup') }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">{{ __('Database + files (.zip)') }}</div>
                    </div>
                </button>
            </form>

        </div>
    </div>

    <div x-show="mode === 'local'" x-cloak class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-semibold text-gray-900">{{ __('Stored Backups') }}</h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $data['stats']['count'] }} {{ __('files') }} · {{ $data['stats']['total_size'] }}
                    · {{ __('Disk free') }}: {{ $data['stats']['disk_free'] }} / {{ $data['stats']['disk_total'] }}
                </p>
            </div>
        </div>

        @if (empty($data['backups']))
            <div class="p-12 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                <p class="mt-3 text-sm text-gray-500">{{ __('No backups yet. Create your first backup above.') }}</p>
            </div>
        @else
            <div class="divide-y divide-gray-100">
                @foreach ($data['backups'] as $backup)
                    <div class="px-6 py-4 flex items-center justify-between gap-4 hover:bg-gray-50/50">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                @if (str_ends_with($backup['name'], '.sql'))
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                                @else
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8"/></svg>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-medium text-gray-900 font-mono truncate">{{ $backup['name'] }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $backup['created_at'] }} · {{ $backup['age'] }} · {{ $backup['size'] }}</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 flex-shrink-0">
                            <a href="{{ route('admin.settings.backup.download', $backup['name']) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-300 text-xs text-gray-700 hover:bg-gray-50 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                {{ __('Download') }}
                            </a>

                            <form method="POST" action="{{ route('admin.settings.backup.delete', $backup['name']) }}"
                                  onsubmit="return confirm('{{ __('Delete this backup?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-500 hover:bg-red-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection