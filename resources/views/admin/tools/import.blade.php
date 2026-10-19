@extends('admin.layouts.app')

@section('title', __('Import'))

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="importTool()">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h2 class="text-base font-semibold text-gray-900">{{ __('Import Content') }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('Restore content from RJ CMS JSON, WordPress WXR, or CSV.') }}</p>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded-r-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-r-lg">
            {{ session('error') }}
        </div>
    @endif

    @if (session('import_result'))
        @php $result = session('import_result'); @endphp
        <div class="bg-white rounded-lg shadow-sm border border-green-200 p-6">
            <div class="flex items-start gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-semibold text-gray-900">{{ __('Import completed') }}</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ __('Created') }}: <strong class="text-green-600">{{ $result['created'] }}</strong>
                        · {{ __('Updated') }}: <strong class="text-blue-600">{{ $result['updated'] }}</strong>
                    </p>
                </div>
            </div>

            @if (!empty($result['stats']))
                <div class="border-t border-gray-100 pt-4">
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">{{ __('By content type') }}</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach ($result['stats'] as $group => $stat)
                            @if (($stat['created'] + $stat['updated'] + $stat['skipped']) > 0)
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <div class="text-xs font-medium text-gray-700 capitalize">{{ $group }}</div>
                                    <div class="text-xs text-gray-500 mt-1 space-y-0.5">
                                        @if ($stat['created'] > 0)<div class="text-green-600">+{{ $stat['created'] }} {{ __('created') }}</div>@endif
                                        @if ($stat['updated'] > 0)<div class="text-blue-600">~{{ $stat['updated'] }} {{ __('updated') }}</div>@endif
                                        @if ($stat['skipped'] > 0)<div class="text-gray-400">{{ $stat['skipped'] }} {{ __('skipped') }}</div>@endif
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            @if (!empty($result['warnings']))
                <div class="mt-4 bg-amber-50 border border-amber-100 rounded-lg p-3">
                    <div class="text-xs font-semibold text-amber-800 mb-1">{{ __('Warnings') }}</div>
                    <ul class="text-xs text-amber-700 list-disc ml-4 space-y-0.5">
                        @foreach ($result['warnings'] as $warn)
                            <li>{{ $warn }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-4 flex items-center gap-3">
                <a href="{{ route('admin.posts.index') }}" class="text-sm text-rj-purple hover:underline">{{ __('View Posts') }}</a>
                <a href="{{ route('admin.pages.index') }}" class="text-sm text-rj-purple hover:underline">{{ __('View Pages') }}</a>
            </div>
        </div>
    @endif

    @if ($pending)
        <div class="bg-white rounded-lg shadow-sm border border-rj-purple/30 p-6">
            <div class="flex items-start gap-3 mb-5">
                <div class="w-10 h-10 rounded-full bg-rj-purple/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-rj-purple" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-semibold text-gray-900">{{ __('Preview') }}</h3>
                    <p class="text-sm text-gray-500 mt-0.5">
                        <code class="font-mono text-xs">{{ $pending['filename'] }}</code>
                        · <span class="uppercase text-xs">{{ $pending['summary']['format'] }}</span>
                        @if (!empty($pending['summary']['generator']))
                            · {{ __('from') }} <strong>{{ $pending['summary']['generator'] }}</strong>
                        @endif
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
                @foreach ($pending['summary']['items'] as $group => $count)
                    <div class="bg-gray-50 rounded-lg p-3">
                        <div class="text-xs text-gray-500 capitalize">{{ $group }}</div>
                        <div class="text-lg font-bold text-gray-900 mt-0.5">{{ $count }}</div>
                    </div>
                @endforeach
            </div>

            <form method="POST" action="{{ route('admin.tools.import.run') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('If item already exists') }}</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="strategy" value="skip" checked class="peer sr-only">
                            <div class="p-3 rounded-lg border-2 border-gray-200 peer-checked:border-rj-purple peer-checked:bg-rj-purple/5 hover:border-gray-300 transition">
                                <div class="text-sm font-semibold text-gray-900">{{ __('Skip existing') }}</div>
                                <p class="text-xs text-gray-500 mt-0.5">{{ __('Keep current data. Only new items are added.') }}</p>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="strategy" value="update" class="peer sr-only">
                            <div class="p-3 rounded-lg border-2 border-gray-200 peer-checked:border-rj-purple peer-checked:bg-rj-purple/5 hover:border-gray-300 transition">
                                <div class="text-sm font-semibold text-gray-900">{{ __('Update existing') }}</div>
                                <p class="text-xs text-gray-500 mt-0.5">{{ __('Overwrite with imported data.') }}</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="bg-amber-50 border border-amber-100 rounded-lg p-3 flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="text-xs text-amber-800">
                        {{ __('"Update existing" overwrites titles, content and SEO fields. Consider exporting a backup first.') }}
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                    <form method="POST" action="{{ route('admin.tools.import.cancel') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 hover:text-red-600">
                            {{ __('Cancel import') }}
                        </button>
                    </form>

                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        {{ __('Confirm Import') }}
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <form method="POST" action="{{ route('admin.tools.import.preview') }}" enctype="multipart/form-data" @submit="uploading = true">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-3">{{ __('Select file') }}</label>

                    <input type="file" name="file" accept=".json,.xml,.csv" x-ref="fileInput" @change="handleFile" class="hidden" required>

                    <div @click="$refs.fileInput.click()"
                         @dragover.prevent="dragover = true"
                         @dragleave.prevent="dragover = false"
                         @drop.prevent="handleDrop($event)"
                         :class="dragover ? 'border-rj-purple bg-rj-purple/5' : 'border-gray-300 hover:border-gray-400 bg-gray-50'"
                         class="min-h-[200px] border-2 border-dashed rounded-lg flex flex-col items-center justify-center text-center cursor-pointer transition p-6">

                        <template x-if="!file">
                            <div class="space-y-3">
                                <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.755-7.93 5 5 0 019.58-1.55A5 5 0 0119 16M12 12v9m0 0l-3-3m3 3l3-3"/></svg>
                                <p class="text-sm text-gray-600 font-medium">{{ __('Drop file here or click to browse') }}</p>
                                <p class="text-xs text-gray-400">{{ __('Supported: .json (RJ CMS), .xml (WordPress WXR), .csv') }}</p>
                                <p class="text-xs text-gray-400">{{ __('Max size: 20 MB') }}</p>
                            </div>
                        </template>

                        <template x-if="file">
                            <div class="space-y-3 w-full">
                                <svg class="w-12 h-12 mx-auto text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <p class="text-sm text-gray-900 font-medium break-all px-4" x-text="file ? file.name : ''"></p>
                                <p class="text-xs text-gray-500" x-text="file ? formatSize(file.size) : ''"></p>
                                <button type="button" @click.stop="clearFile()" class="text-xs text-red-600 hover:underline">{{ __('Remove') }}</button>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="mt-5 flex justify-end">
                    <button type="submit" :disabled="!file || uploading"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition disabled:opacity-50">
                        <svg x-show="!uploading" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.46 12C3.73 7.94 7.52 5 12 5s8.27 2.94 9.54 7c-1.27 4.06-5.06 7-9.54 7S3.73 16.06 2.46 12z"/></svg>
                        <svg x-show="uploading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                        <span x-text="uploading ? '{{ __('Analyzing...') }}' : '{{ __('Analyze File') }}'"></span>
                    </button>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-rj-purple" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                    <span class="text-sm font-semibold text-gray-900">{{ __('JSON') }}</span>
                </div>
                <p class="text-xs text-gray-500">{{ __('RJ CMS native format. Preserves everything including translations and relations.') }}</p>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zM7.5 16.5L4 8h2.3l1.6 4.6L9.4 8h1.5l1.6 4.6L14.2 8h2.3l-3.7 8.5h-1.6l-1.3-3.7-1.3 3.7H7.5zm11.2 0h-1.5V8h1.5v8.5z"/></svg>
                    <span class="text-sm font-semibold text-gray-900">{{ __('WordPress WXR') }}</span>
                </div>
                <p class="text-xs text-gray-500">{{ __('XML export from WordPress. Posts, pages, categories and tags are imported.') }}</p>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    <span class="text-sm font-semibold text-gray-900">{{ __('CSV') }}</span>
                </div>
                <p class="text-xs text-gray-500">{{ __('Spreadsheet format. One row per locale. Requires columns: type, slug, locale, title.') }}</p>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="text-xs text-blue-800 leading-relaxed">
                <strong>{{ __('How it works:') }}</strong>
                {{ __('Upload a file → review the summary → confirm. Nothing is imported until you confirm. Media files are not included — use Settings → Backup for complete system restore.') }}
            </div>
        </div>
    @endif
</div>

<script>
function importTool() {
    return {
        file: null,
        dragover: false,
        uploading: false,

        handleFile(e) {
            const f = e.target.files?.[0];
            if (f) this.file = f;
        },

        handleDrop(e) {
            this.dragover = false;
            const f = e.dataTransfer.files?.[0];
            if (!f) return;
            const ok = ['.json', '.xml', '.csv'].some(ext => f.name.toLowerCase().endsWith(ext));
            if (!ok) {
                alert('{{ __('Only .json, .xml or .csv files are supported.') }}');
                return;
            }
            this.file = f;
            const dt = new DataTransfer();
            dt.items.add(f);
            this.$refs.fileInput.files = dt.files;
        },

        clearFile() {
            this.file = null;
            this.$refs.fileInput.value = '';
        },

        formatSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / 1024 / 1024).toFixed(2) + ' MB';
        },
    };
}
</script>
@endsection