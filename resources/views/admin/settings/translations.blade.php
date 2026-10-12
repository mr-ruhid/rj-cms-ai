@extends('admin.settings.layout')

@section('settings')
<div class="space-y-6" x-data="translationsPage()">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.settings.language') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h2 class="text-base font-semibold text-gray-900">
                    {{ __('Translations') }}: <span class="uppercase text-rj-purple">{{ $locale }}</span>
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ __('Search, edit manually, or use AI to translate missing keys.') }}
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.language.ai-translate', $locale) }}">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                {{ __('AI Translate Missing') }}
            </button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">

        <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-3">
            <div class="relative flex-1">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" x-model="search" placeholder="{{ __('Search keys or translations...') }}"
                       class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
            </div>

            <select x-model="filterMode" class="rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                <option value="all">{{ __('All') }}</option>
                <option value="missing">{{ __('Missing only') }}</option>
                <option value="translated">{{ __('Translated only') }}</option>
            </select>

            <span class="text-xs text-gray-500 whitespace-nowrap">
                <span x-text="filteredCount"></span> / {{ count($translations) }}
            </span>
        </div>

        <form method="POST" action="{{ route('admin.settings.language.translations.update', $locale) }}" id="translationsForm">
            @csrf
            @method('PUT')

            <div class="max-h-[65vh] overflow-y-auto rj-scrollbar">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr>
                            <th class="text-left font-medium text-gray-600 px-4 py-2.5 w-1/3">{{ __('Key (English)') }}</th>
                            <th class="text-left font-medium text-gray-600 px-4 py-2.5 w-2/3">{{ __('Translation') }} ({{ strtoupper($locale) }})</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="row in filteredRows" :key="row.key">
                            <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                                <td class="px-4 py-2 font-mono text-xs text-gray-700 break-all align-top pt-3" x-text="row.key"></td>
                                <td class="px-4 py-2">
                                    <input type="text"
                                           :name="'translations[' + row.key + ']'"
                                           x-model="row.value"
                                           @input="row.value = $event.target.value"
                                           placeholder="{{ __('Empty — needs translation') }}"
                                           class="w-full rounded-md border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm"
                                           :class="row.value && row.value.trim() !== '' ? '' : 'bg-red-50/50 border-red-200'">
                                </td>
                            </tr>
                        </template>

                        <tr x-show="filteredRows.length === 0">
                            <td colspan="2" class="px-4 py-8 text-center text-gray-400 text-sm">
                                {{ __('No results.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t border-gray-100 bg-gray-50 flex items-center justify-between sticky bottom-0">
                <div class="text-xs text-gray-500">
                    <span class="inline-block w-3 h-3 bg-red-100 border border-red-200 rounded-sm mr-1 align-middle"></span>
                    {{ __('Missing translation') }}
                </div>
                <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-gray-800 transition">
                    {{ __('Save Changes') }}
                </button>
            </div>
        </form>

    </div>

</div>

<script>
function translationsPage() {
    return {
        search: '',
        filterMode: 'all',
        rows: @json(collect($translations)->map(fn($v, $k) => ['key' => $k, 'value' => $v])->values()),
        get filteredRows() {
            const q = this.search.toLowerCase().trim();
            return this.rows.filter(r => {
                const isEmpty = !r.value || r.value.trim() === '';
                if (this.filterMode === 'missing' && !isEmpty) return false;
                if (this.filterMode === 'translated' && isEmpty) return false;
                if (q === '') return true;
                return r.key.toLowerCase().includes(q) || (r.value || '').toLowerCase().includes(q);
            });
        },
        get filteredCount() {
            return this.filteredRows.length;
        }
    }
}
</script>
@endsection
