@extends('admin.layouts.app')

@section('title', __('Media Cleanup'))

@section('content')
<div class="space-y-6" x-data="cleanupPage()">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.media.index') }}" class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h2 class="text-base font-semibold text-gray-900">{{ __('Media Cleanup') }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('Find and remove unused files and orphaned database records.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Database Records') }}</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['db_count']) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Orphaned Files') }}</div>
            <div class="text-2xl font-bold {{ $stats['orphan_count'] > 0 ? 'text-amber-600' : 'text-gray-900' }} mt-1">{{ number_format($stats['orphan_count']) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Reclaimable Size') }}</div>
            <div class="text-2xl font-bold {{ $stats['orphan_size'] > 0 ? 'text-amber-600' : 'text-gray-900' }} mt-1">{{ number_format($stats['orphan_size'] / 1048576, 2) }} MB</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Missing Files') }}</div>
            <div class="text-2xl font-bold {{ $stats['missing_count'] > 0 ? 'text-red-600' : 'text-gray-900' }} mt-1">{{ number_format($stats['missing_count']) }}</div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h3 class="text-base font-semibold text-gray-900">{{ __('Orphaned Files') }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ __('Files in public/uploads that are not registered in the database and not referenced in settings.') }}</p>
            </div>

            @if (!empty($orphans))
                <form method="POST" action="{{ route('admin.media.cleanup.delete') }}" @submit="return confirmDelete()">
                    @csrf
                    <template x-for="file in selected" :key="file">
                        <input type="hidden" name="files[]" :value="file">
                    </template>
                    <button type="submit" :disabled="selected.length === 0"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-medium hover:bg-red-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                        <span x-text="'{{ __('Delete Selected') }} (' + selected.length + ')'"></span>
                    </button>
                </form>
            @endif
        </div>

        @if (empty($orphans))
            <div class="p-12 text-center">
                <svg class="w-12 h-12 mx-auto text-green-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="mt-3 text-sm text-gray-600 font-medium">{{ __('No orphaned files found.') }}</p>
                <p class="mt-1 text-xs text-gray-400">{{ __('Your media library is clean.') }}</p>
            </div>
        @else
            <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-3">
                <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700">
                    <input type="checkbox" @change="toggleAll($event.target.checked)"
                           :checked="selected.length === {{ count($orphans) }}"
                           class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    {{ __('Select all') }}
                </label>
                <span class="text-xs text-gray-500" x-text="selected.length + ' / {{ count($orphans) }} {{ __('selected') }}'"></span>
            </div>

            <div class="divide-y divide-gray-100 max-h-[600px] overflow-y-auto rj-scrollbar">
                @foreach ($orphans as $orphan)
                    <div class="px-6 py-3 flex items-center justify-between gap-4 hover:bg-gray-50/50">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <input type="checkbox" value="{{ $orphan['path'] }}"
                                   x-model="selected"
                                   class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple flex-shrink-0">

                            <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0 overflow-hidden">
                                @if ($orphan['type'] === 'image')
                                    <img src="{{ $orphan['url'] }}" class="w-full h-full object-cover" onerror="this.style.display='none'">
                                @elseif ($orphan['type'] === 'video')
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                @elseif ($orphan['type'] === 'audio')
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-mono text-gray-900 truncate">{{ $orphan['name'] }}</div>
                                <div class="text-xs text-gray-500 mt-0.5 truncate">
                                    {{ $orphan['path'] }} · {{ $orphan['size_human'] }} · {{ $orphan['age'] }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 flex-shrink-0">
                            <a href="{{ $orphan['url'] }}" target="_blank"
                               class="text-xs text-rj-purple hover:underline">
                                {{ __('View') }}
                            </a>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 uppercase font-medium">
                                {{ $orphan['type'] }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h3 class="text-base font-semibold text-gray-900">{{ __('Missing Files') }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ __('Database records whose physical files are missing from disk.') }}</p>
            </div>

            @if (!empty($missing))
                <form method="POST" action="{{ route('admin.media.cleanup.prune-db') }}"
                      onsubmit="return confirm('{{ __('Remove all orphaned database records?') }}')">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border-2 border-red-200 text-red-600 text-sm font-medium hover:bg-red-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                        {{ __('Clean Database Records') }}
                    </button>
                </form>
            @endif
        </div>

        @if (empty($missing))
            <div class="p-12 text-center">
                <svg class="w-12 h-12 mx-auto text-green-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="mt-3 text-sm text-gray-600 font-medium">{{ __('All database records have matching files.') }}</p>
            </div>
        @else
            <div class="divide-y divide-gray-100">
                @foreach ($missing as $item)
                    <div class="px-6 py-3 flex items-center justify-between gap-4 hover:bg-gray-50/50">
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-medium text-gray-900 truncate">{{ $item['name'] }}</div>
                            <div class="text-xs text-gray-500 mt-0.5 font-mono truncate">
                                {{ $item['path'] }} · {{ $item['size_human'] }} · {{ $item['created_at'] }}
                            </div>
                        </div>

                        <a href="{{ $item['edit_url'] }}"
                           class="text-xs text-rj-purple hover:underline flex-shrink-0">
                            {{ __('Edit Record') }}
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

<script>
function cleanupPage() {
    return {
        selected: [],
        toggleAll(checked) {
            if (checked) {
                this.selected = [
                    @foreach ($orphans as $orphan)
                        @if ($orphan['path'])
                            '{{ $orphan['path'] }}',
                        @endif
                    @endforeach
                ];
            } else {
                this.selected = [];
            }
        },
        confirmDelete() {
            if (this.selected.length === 0) {
                return false;
            }
            return confirm('{{ __('Delete') }} ' + this.selected.length + ' {{ __('file(s)? This cannot be undone.') }}');
        }
    }
}
</script>
@endsection