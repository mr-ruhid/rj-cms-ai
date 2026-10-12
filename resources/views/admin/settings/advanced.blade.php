@extends('admin.settings.layout')

@section('settings')
<div class="space-y-6">

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('System Information') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Environment and server details.') }}</p>
        </div>

        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
            <div class="bg-gray-50 rounded-lg p-3">
                <div class="text-xs text-gray-500">{{ __('PHP Version') }}</div>
                <div class="font-mono font-medium text-gray-900 mt-0.5">{{ $data['environment']['php_version'] }}</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <div class="text-xs text-gray-500">{{ __('Laravel Version') }}</div>
                <div class="font-mono font-medium text-gray-900 mt-0.5">{{ $data['environment']['laravel_version'] }}</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <div class="text-xs text-gray-500">{{ __('MySQL Version') }}</div>
                <div class="font-mono font-medium text-gray-900 mt-0.5">{{ $data['environment']['mysql_version'] }}</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <div class="text-xs text-gray-500">{{ __('Environment') }}</div>
                <div class="font-mono font-medium text-gray-900 mt-0.5">{{ $data['environment']['environment'] }}</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <div class="text-xs text-gray-500">{{ __('Server OS') }}</div>
                <div class="font-mono font-medium text-gray-900 mt-0.5">{{ $data['environment']['server_os'] }}</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <div class="text-xs text-gray-500">{{ __('Server Software') }}</div>
                <div class="font-mono font-medium text-gray-900 mt-0.5 truncate">{{ $data['environment']['server_software'] }}</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <div class="text-xs text-gray-500">{{ __('Memory Limit') }}</div>
                <div class="font-mono font-medium text-gray-900 mt-0.5">{{ $data['environment']['memory_limit'] }}</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <div class="text-xs text-gray-500">{{ __('Max Execution Time') }}</div>
                <div class="font-mono font-medium text-gray-900 mt-0.5">{{ $data['environment']['max_execution_time'] }}</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <div class="text-xs text-gray-500">{{ __('Upload Max File Size') }}</div>
                <div class="font-mono font-medium text-gray-900 mt-0.5">{{ $data['environment']['upload_max_filesize'] }}</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <div class="text-xs text-gray-500">{{ __('Post Max Size') }}</div>
                <div class="font-mono font-medium text-gray-900 mt-0.5">{{ $data['environment']['post_max_size'] }}</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <div class="text-xs text-gray-500">{{ __('Timezone') }}</div>
                <div class="font-mono font-medium text-gray-900 mt-0.5">{{ $data['environment']['timezone'] }}</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <div class="text-xs text-gray-500">{{ __('Debug Mode') }}</div>
                <div class="font-mono font-medium {{ $data['environment']['debug_mode'] === 'Enabled' ? 'text-red-600' : 'text-green-600' }} mt-0.5">
                    {{ $data['environment']['debug_mode'] }}
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.settings.advanced.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900">{{ __('Debug & Maintenance') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ __('Debug tools and maintenance mode configuration.') }}</p>
            </div>

            <div class="p-6 space-y-5">

                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="debug_mode" value="1"
                           {{ ($options['debug_mode'] ?? '0') === '1' ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <span>
                        <span class="block text-sm font-medium text-gray-700">{{ __('Enable debug mode') }}</span>
                        <span class="block text-xs text-gray-500 mt-0.5">{{ __('Show detailed error messages. Disable on production sites.') }}</span>
                    </span>
                </label>

                <label class="flex items-start gap-3 cursor-pointer border-t border-gray-100 pt-5">
                    <input type="checkbox" name="maintenance_mode" value="1"
                           {{ ($options['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <span>
                        <span class="block text-sm font-medium text-gray-700">{{ __('Enable maintenance mode') }}</span>
                        <span class="block text-xs text-gray-500 mt-0.5">{{ __('Visitors will see a maintenance page. Admins can still access.') }}</span>
                    </span>
                </label>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Maintenance Message') }}</label>
                        <textarea name="maintenance_message" rows="3"
                                  placeholder="{{ __('We are performing scheduled maintenance. Please check back soon.') }}"
                                  class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">{{ $options['maintenance_message'] ?? '' }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Retry After (seconds)') }}</label>
                        <input type="number" name="maintenance_retry" min="0" max="86400"
                               value="{{ $options['maintenance_retry'] ?? '3600' }}"
                               class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        <p class="text-xs text-gray-500 mt-1">{{ __('Sends Retry-After header. 0 to disable.') }}</p>
                    </div>
                </div>

            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900">{{ __('Logging') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ __('Application log level and retention.') }}</p>
            </div>

            <div class="p-6 space-y-5">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Log Level') }}</label>
                        <select name="log_level" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                            @foreach ($data['logLevels'] as $value => $label)
                                <option value="{{ $value }}" {{ ($options['log_level'] ?? 'error') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">{{ __('Only events at this level or higher are logged.') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Log Retention (days)') }}</label>
                        <input type="number" name="log_retention_days" min="1" max="365"
                               value="{{ $options['log_retention_days'] ?? '14' }}"
                               class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        <p class="text-xs text-gray-500 mt-1">{{ __('Auto-delete logs older than this.') }}</p>
                    </div>
                </div>

                <label class="flex items-start gap-3 cursor-pointer border-t border-gray-100 pt-5">
                    <input type="checkbox" name="error_reporting" value="1"
                           {{ ($options['error_reporting'] ?? '0') === '1' ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <span>
                        <span class="block text-sm font-medium text-gray-700">{{ __('Report PHP errors to log') }}</span>
                        <span class="block text-xs text-gray-500 mt-0.5">{{ __('Log all PHP errors, warnings and notices.') }}</span>
                    </span>
                </label>

            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900">{{ __('Custom Code') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ __('Inject custom HTML, CSS or JavaScript.') }}</p>
            </div>

            <div class="p-6 space-y-5">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Custom CSS') }}</label>
                    <textarea name="custom_css" rows="5"
                              placeholder=".my-class { color: red; }"
                              class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">{{ $options['custom_css'] ?? '' }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">{{ __('Applied to admin panel.') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Custom Head Code') }}</label>
                    <textarea name="custom_head_code" rows="4"
                              placeholder="<meta name='...' />"
                              class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">{{ $options['custom_head_code'] ?? '' }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">{{ __('Inserted before </head> tag.') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Custom Footer Code') }}</label>
                    <textarea name="custom_footer_code" rows="4"
                              placeholder="<script>...</script>"
                              class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">{{ $options['custom_footer_code'] ?? '' }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">{{ __('Inserted before </body> tag.') }}</p>
                </div>

            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900">{{ __('Miscellaneous') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ __('Additional options for power users.') }}</p>
            </div>

            <div class="p-6 space-y-4">

                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="track_visitors" value="1"
                           {{ ($options['track_visitors'] ?? '0') === '1' ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <span>
                        <span class="block text-sm font-medium text-gray-700">{{ __('Track visitor analytics') }}</span>
                        <span class="block text-xs text-gray-500 mt-0.5">{{ __('Collect basic visitor data (IP, browser, page).') }}</span>
                    </span>
                </label>

                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="allow_file_editing" value="1"
                           {{ ($options['allow_file_editing'] ?? '0') === '1' ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <span>
                        <span class="block text-sm font-medium text-gray-700">{{ __('Allow theme/plugin file editing') }}</span>
                        <span class="block text-xs text-gray-500 mt-0.5">{{ __('Enable built-in code editor in admin. Disable for security.') }}</span>
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

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-semibold text-gray-900">{{ __('Log File') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ __('Application error logs are stored in storage/logs/laravel.log') }}</p>
            </div>
            <form method="POST" action="{{ route('admin.settings.advanced.clear-logs') }}"
                  onsubmit="return confirm('{{ __('Clear the log file? This cannot be undone.') }}')">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-red-200 text-red-600 text-sm font-medium hover:bg-red-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                    {{ __('Clear Logs') }}
                </button>
            </form>
        </div>
    </div>

</div>
@endsection