@extends('admin.layouts.app')

@section('title', __('Backup Manager'))

@section('content')
<div class="max-w-6xl mx-auto space-y-6 pb-12" x-data="backupTool(@js($settings))">

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ __('Backup Manager') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Create, download, and restore your site data.') }}</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" @click="createBackup('local')" :disabled="creating"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition disabled:opacity-60 shadow-sm">
                <svg x-show="!creating" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                <svg x-show="creating" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                <span x-text="creating ? '{{ __('Creating...') }}' : '{{ __('Create Backup Now') }}'"></span>
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                {{ __('Backup Settings') }}
            </h3>
        </div>
        
        <form @submit.prevent="saveSettings()" class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Backup Mode') }}</label>
                        <div class="flex items-center gap-4">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" x-model="settings.mode" value="manual" class="w-4 h-4 text-rj-purple focus:ring-rj-purple border-gray-300">
                                <span class="ml-2 text-sm text-gray-700">{{ __('Manual') }}</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" x-model="settings.mode" value="auto" class="w-4 h-4 text-rj-purple focus:ring-rj-purple border-gray-300">
                                <span class="ml-2 text-sm text-gray-700">{{ __('Automatic') }}</span>
                            </label>
                        </div>
                    </div>

                    <div x-show="settings.mode === 'auto'" x-collapse x-cloak>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Auto Backup Schedule') }}</label>
                        <select x-model="settings.schedule" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                            <option value="daily">{{ __('Daily') }}</option>
                            <option value="weekly">{{ __('Weekly') }}</option>
                            <option value="monthly">{{ __('Monthly') }}</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">{{ __('Requires system cron job to be configured.') }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Local Backups Retention') }}</label>
                        <div class="flex items-center gap-2">
                            <input type="number" x-model.number="settings.local_limit" min="1" max="20" class="w-24 px-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                            <span class="text-sm text-gray-500">{{ __('files') }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ __('Keep the last N backups on the server. (Default: 5, Max: 20)') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Download Backups Retention') }}</label>
                        <div class="flex items-center gap-2">
                            <input type="number" x-model.number="settings.download_limit" min="1" max="3" class="w-24 px-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                            <span class="text-sm text-gray-500">{{ __('files') }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ __('Backups saved for downloading. (Default: 2, Max: 3)') }}</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-100">
                <button type="submit" :disabled="saving"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-gray-800 transition disabled:opacity-60">
                    <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <svg x-show="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                    <span x-text="saving ? '{{ __('Saving...') }}' : '{{ __('Save Settings') }}'"></span>
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">{{ __('Local Backups (Server)') }}</h3>
            <span class="text-xs font-medium text-gray-500">{{ count($localBackups) }} / <span x-text="settings.local_limit"></span> {{ __('saved') }}</span>
        </div>
        
        <div class="divide-y divide-gray-100">
            @forelse ($localBackups as $backup)
                <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-gray-50/50 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-gray-900">{{ $backup['filename'] }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">{{ $backup['created_at'] }} · {{ $backup['size_human'] }}</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <form method="POST" action="{{ route('admin.tools.backup.restore', $backup['filename']) }}" onsubmit="return confirm('{{ __('WARNING: This will overwrite your current database and files. Are you sure?') }}')">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-amber-50 text-amber-700 text-xs font-medium hover:bg-amber-100 transition">
                                {{ __('Restore') }}
                            </button>
                        </form>
                        
                        <a href="{{ route('admin.tools.backup.download', ['filename' => $backup['filename'], 'type' => 'local']) }}" class="px-3 py-1.5 rounded-lg bg-gray-100 text-gray-700 text-xs font-medium hover:bg-gray-200 transition">
                            {{ __('Download') }}
                        </a>

                        <form method="POST" action="{{ route('admin.tools.backup.delete', $backup['filename']) }}" onsubmit="return confirm('{{ __('Delete this backup permanently?') }}')">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="type" value="local">
                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 text-red-600 text-xs font-medium hover:bg-red-100 transition">
                                {{ __('Delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                    <p class="text-sm text-gray-500">{{ __('No local backups found.') }}</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">{{ __('Downloadable Backups') }}</h3>
            <span class="text-xs font-medium text-gray-500">{{ count($downloadBackups) }} / <span x-text="settings.download_limit"></span> {{ __('saved') }}</span>
        </div>
        
        <div class="divide-y divide-gray-100">
            @forelse ($downloadBackups as $backup)
                <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-gray-50/50 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-gray-900">{{ $backup['filename'] }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">{{ $backup['created_at'] }} · {{ $backup['size_human'] }}</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.tools.backup.download', ['filename' => $backup['filename'], 'type' => 'download']) }}" class="px-4 py-1.5 rounded-lg bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-xs font-medium hover:opacity-90 transition shadow-sm">
                            {{ __('Download to PC') }}
                        </a>

                        <form method="POST" action="{{ route('admin.tools.backup.delete', $backup['filename']) }}" onsubmit="return confirm('{{ __('Delete this backup permanently?') }}')">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="type" value="download">
                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 text-red-600 text-xs font-medium hover:bg-red-100 transition">
                                {{ __('Delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <p class="text-sm text-gray-500">{{ __('No downloadable backups found.') }}</p>
                </div>
            @endforelse
        </div>
    </div>

    <div x-show="toast.show" x-cloak x-transition
         class="fixed bottom-6 right-6 px-5 py-3 rounded-xl shadow-2xl text-white text-sm font-medium z-50"
         :style="'background:' + (toast.type === 'error' ? '#dc2626' : '#059669')"
         x-text="toast.message"></div>
</div>

<script>
function backupTool(initialSettings) {
    return {
        settings: initialSettings,
        creating: false,
        saving: false,
        toast: { show: false, message: '', type: 'success' },

        showToast(message, type) {
            this.toast = { show: true, message, type: type || 'success' };
            setTimeout(() => { this.toast.show = false; }, 3500);
        },

        async saveSettings() {
            this.saving = true;
            try {
                const res = await fetch('{{ route('admin.tools.backup.settings') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(this.settings),
                });
                const data = await res.json().catch(() => ({}));
                if (res.ok && data.success) {
                    this.showToast(data.message || '{{ __('Settings saved.') }}', 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    this.showToast(data.message || '{{ __('Save failed.') }}', 'error');
                }
            } catch (e) {
                this.showToast('{{ __('Network error.') }}', 'error');
            } finally {
                this.saving = false;
            }
        },

        async createBackup(type) {
            if (!confirm('{{ __('Create a new backup now? This may take a few moments.') }}')) return;
            this.creating = true;
            try {
                const res = await fetch('{{ route('admin.tools.backup.create') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ type: type }),
                });
                const data = await res.json().catch(() => ({}));
                if (res.ok && data.success) {
                    this.showToast(data.message || '{{ __('Backup created.') }}', 'success');
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    this.showToast(data.message || '{{ __('Creation failed.') }}', 'error');
                }
            } catch (e) {
                this.showToast('{{ __('Network error.') }}', 'error');
            } finally {
                this.creating = false;
            }
        }
    };
}
</script>
@endsection