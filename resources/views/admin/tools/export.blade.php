@extends('admin.layouts.app')

@section('title', __('Export'))

@section('content')
@php
    $selectedCount = 0;
    $totalAvailable = 0;
    foreach ($available as $key => $opt) {
        $totalAvailable += $opt['count'];
    }
@endphp

<div class="max-w-5xl mx-auto space-y-6" x-data="exportTool()">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h2 class="text-base font-semibold text-gray-900">{{ __('Export Content') }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('Download your content as JSON, WordPress WXR, or CSV.') }}</p>
        </div>
    </div>

    @if (session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-r-lg">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.tools.export.run') }}" x-ref="exportForm" target="_blank">
        @csrf

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 space-y-5">

            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-3">{{ __('What to include') }}</label>

                <div class="flex items-center gap-3 mb-3">
                    <button type="button" @click="selectAll()" class="text-xs text-rj-purple hover:underline">
                        {{ __('Select all') }}
                    </button>
                    <span class="text-gray-300">|</span>
                    <button type="button" @click="selectNone()" class="text-xs text-gray-500 hover:underline">
                        {{ __('Clear selection') }}
                    </button>
                    <span class="text-xs text-gray-400 ml-auto" x-text="selected.length + ' / ' + {{ count($available) }} + ' {{ __('selected') }}'"></span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                    @foreach ($available as $key => $opt)
                        <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-rj-purple/40 cursor-pointer transition"
                               :class="items.includes('{{ $key }}') ? 'border-rj-purple bg-rj-purple/5' : ''">
                            <input type="checkbox" name="items[]" value="{{ $key }}"
                                   x-model="items"
                                   class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-gray-900">{{ $opt['label'] }}</div>
                                <div class="text-xs text-gray-500">{{ number_format($opt['count']) }} {{ __('items') }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="border-t border-gray-100 pt-5">
                <label class="block text-sm font-semibold text-gray-900 mb-3">{{ __('Format') }}</label>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">

                    <label class="cursor-pointer">
                        <input type="radio" name="format" value="json" x-model="format" class="peer sr-only">
                        <div class="p-4 rounded-lg border-2 border-gray-200 peer-checked:border-rj-purple peer-checked:bg-rj-purple/5 hover:border-gray-300 transition">
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-5 h-5 text-rj-purple" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                                <span class="text-sm font-semibold text-gray-900">{{ __('JSON') }}</span>
                                <span class="ml-auto text-xs text-rj-purple font-medium">{{ __('Recommended') }}</span>
                            </div>
                            <p class="text-xs text-gray-500 leading-relaxed">{{ __('Full backup format. Preserves all data, relations and multilanguage. Ideal for re-import into RJ CMS.') }}</p>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="format" value="wxr" x-model="format" class="peer sr-only">
                        <div class="p-4 rounded-lg border-2 border-gray-200 peer-checked:border-rj-purple peer-checked:bg-rj-purple/5 hover:border-gray-300 transition">
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12.158 12.786L9.46 4h2.42l1.486 5.706L15.467 4h1.984l-3.14 9.98c-.243.79-.402 1.523-.477 2.198-.075.676-.113 1.282-.113 1.82 0 1.132.164 2.022.492 2.67.328.648.787.972 1.377.972.59 0 1.043-.324 1.36-.972.317-.648.475-1.538.475-2.67 0-1.242-.086-2.477-.258-3.704l1.36-.35c.19 1.44.284 2.79.284 4.05 0 1.606-.267 2.844-.802 3.715-.535.87-1.314 1.306-2.336 1.306-.985 0-1.752-.437-2.301-1.31-.55-.873-.824-2.11-.824-3.71 0-.626.04-1.276.12-1.95.08-.673.195-1.348.345-2.023zm-5.29 0L4.17 4h2.43l1.5 5.706L9.65 4h1.983l-3.14 9.98c-.243.79-.402 1.523-.477 2.198-.075.676-.113 1.282-.113 1.82 0 1.132.164 2.022.492 2.67.328.648.787.972 1.377.972.59 0 1.043-.324 1.36-.972.317-.648.475-1.538.475-2.67 0-1.242-.086-2.477-.258-3.704l1.36-.35c.19 1.44.284 2.79.284 4.05 0 1.606-.267 2.844-.802 3.715-.535.87-1.314 1.306-2.336 1.306-.985 0-1.752-.437-2.301-1.31-.55-.873-.824-2.11-.824-3.71 0-.626.04-1.276.12-1.95.08-.673.195-1.348.345-2.023z"/></svg>
                                <span class="text-sm font-semibold text-gray-900">{{ __('WordPress WXR') }}</span>
                            </div>
                            <p class="text-xs text-gray-500 leading-relaxed">{{ __('XML format compatible with WordPress import. Exports posts and pages only (categories and tags as terms).') }}</p>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="format" value="csv" x-model="format" class="peer sr-only">
                        <div class="p-4 rounded-lg border-2 border-gray-200 peer-checked:border-rj-purple peer-checked:bg-rj-purple/5 hover:border-gray-300 transition">
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                <span class="text-sm font-semibold text-gray-900">{{ __('CSV') }}</span>
                            </div>
                            <p class="text-xs text-gray-500 leading-relaxed">{{ __('Spreadsheet format. Opens in Excel or Google Sheets. Best for review, not full restore.') }}</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="bg-amber-50 border border-amber-100 rounded-lg p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="text-xs text-amber-800 leading-relaxed">
                    <strong>{{ __('Not included in export:') }}</strong>
                    {{ __('Media files, users, and full database backup. Use Settings → Backup for complete system snapshots.') }}
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    {{ __('Cancel') }}
                </a>
                <button type="submit" :disabled="items.length === 0"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition disabled:opacity-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>{{ __('Download Export') }}</span>
                </button>
            </div>
        </div>
    </form>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">{{ __('Need backup instead?') }}</h3>
        <p class="text-xs text-gray-500 mb-3">{{ __('For complete database + media + file snapshots, use the Backup tool.') }}</p>
        <a href="{{ route('admin.settings.backup') }}" class="inline-flex items-center gap-2 text-sm text-rj-purple hover:underline">
            {{ __('Go to Backup settings') }}
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
    </div>
</div>

<script>
function exportTool() {
    return {
        items: {!! json_encode(array_keys(array_filter($available, fn($opt) => $opt['default']))) !!},
        format: 'json',

        selectAll() {
            this.items = {!! json_encode(array_keys($available)) !!};
        },

        selectNone() {
            this.items = [];
        },
    };
}
</script>
@endsection