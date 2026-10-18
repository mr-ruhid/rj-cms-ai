@extends('admin.layouts.app')

@section('title', __('Plugins'))

@section('content')
<div class="space-y-6" x-data="{ uploadOpen: false, uploading: false, search: '', selected: [], bulkAction: '' }">

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-base font-semibold text-gray-900">{{ __('Plugins') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Extend your site with plugins.') }}</p>
        </div>

        <div class="flex items-center gap-2">
            <form method="POST" action="{{ route('admin.plugins.check-updates') }}" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    {{ __('Check for Updates') }}
                </button>
            </form>

            <a href="{{ route('admin.plugins.browse') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                {{ __('Browse Community') }}
            </a>

            <button type="button" @click="uploadOpen = true"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                {{ __('Upload Plugin') }}
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded-r-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Installed') }}</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($counts['all']) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Active') }}</div>
            <div class="text-2xl font-bold text-green-600 mt-1">{{ number_format($counts['active']) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Inactive') }}</div>
            <div class="text-2xl font-bold text-gray-600 mt-1">{{ number_format($counts['inactive']) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Errors') }}</div>
            <div class="text-2xl font-bold text-red-600 mt-1">{{ number_format($counts['error']) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Updates') }}</div>
            <div class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($counts['updates']) }}</div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">

        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between gap-3 flex-wrap">
            <div class="relative flex-1 max-w-md">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" x-model="search" placeholder="{{ __('Search plugins...') }}"
                       class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
            </div>
        </div>

        @if ($plugins->isEmpty())
            <div class="p-16 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
                <p class="mt-4 text-sm text-gray-500">{{ __('No plugins installed yet.') }}</p>
            </div>
        @else
            <form method="POST" action="{{ route('admin.plugins.bulk') }}">
                @csrf

                <div x-show="selected.length > 0" x-cloak class="px-4 py-2.5 border-b border-gray-100 bg-rj-purple/5 flex items-center gap-3 flex-wrap">
                    <span class="text-xs text-gray-600"><b x-text="selected.length"></b> {{ __('selected') }}</span>

                    <select x-model="bulkAction" class="rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-xs">
                        <option value="">{{ __('Bulk Actions') }}</option>
                        <option value="activate">{{ __('Activate') }}</option>
                        <option value="deactivate">{{ __('Deactivate') }}</option>
                        <option value="delete">{{ __('Delete') }}</option>
                    </select>

                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-gray-900 text-white text-xs font-medium hover:bg-gray-800 transition">
                        {{ __('Apply') }}
                    </button>

                    <template x-for="slug in selected" :key="slug">
                        <input type="hidden" name="slugs[]" :value="slug">
                    </template>
                    <input type="hidden" name="action" :value="bulkAction">
                </div>

                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="w-10 px-4 py-3">
                                <input type="checkbox"
                                       @change="selected = $event.target.checked ? {{ $plugins->pluck('slug')->toJson() }} : []"
                                       :checked="selected.length === {{ $plugins->count() }} && {{ $plugins->count() }} > 0"
                                       class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                            </th>
                            <th class="text-left font-medium text-gray-600 px-4 py-3">{{ __('Plugin') }}</th>
                            <th class="text-left font-medium text-gray-600 px-4 py-3 hidden lg:table-cell">{{ __('Description') }}</th>
                            <th class="text-left font-medium text-gray-600 px-4 py-3 hidden md:table-cell">{{ __('Version') }}</th>
                            <th class="text-center font-medium text-gray-600 px-4 py-3">{{ __('Status') }}</th>
                            <th class="text-right font-medium text-gray-600 px-4 py-3">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($plugins as $plugin)
                            @php
                                $statusColors = [
                                    'active' => 'bg-green-50 text-green-700',
                                    'inactive' => 'bg-gray-100 text-gray-600',
                                    'error' => 'bg-red-50 text-red-700',
                                ];
                                $statusClass = $statusColors[$plugin->status] ?? 'bg-gray-100 text-gray-600';
                                $hasUpdate = !empty($plugin->metadata()['latest_version']) && version_compare($plugin->metadata()['latest_version'], $plugin->version, '>');
                            @endphp

                            <tr x-show="search === '' || '{{ strtolower($plugin->name) }}'.includes(search.toLowerCase())"
                                class="hover:bg-gray-50/50 transition">
                                <td class="px-4 py-3 align-top">
                                    <input type="checkbox" value="{{ $plugin->slug }}" x-model="selected"
                                           class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                                </td>

                                <td class="px-4 py-3 align-top">
                                    <div class="flex items-start gap-3 min-w-0">
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-rj-purple/20 to-rj-pink/20 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-rj-purple" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
                                        </div>

                                        <div class="min-w-0">
                                            <a href="{{ route('admin.plugins.show', $plugin) }}"
                                               class="text-sm font-semibold text-gray-900 hover:text-rj-purple">
                                                {{ $plugin->name }}
                                            </a>
                                            <div class="text-xs text-gray-500 mt-0.5">
                                                <code class="font-mono">{{ $plugin->slug }}</code>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3 align-top hidden lg:table-cell max-w-md">
                                    <div class="text-sm text-gray-600 line-clamp-2">
                                        {{ $plugin->description ?: '—' }}
                                    </div>
                                </td>

                                <td class="px-4 py-3 align-top hidden md:table-cell">
                                    <span class="text-sm text-gray-700 font-mono">{{ $plugin->version }}</span>
                                </td>

                                <td class="px-4 py-3 align-top text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                        {{ $plugin->statusLabel() }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 align-top">
                                    <div class="flex items-center justify-end gap-2">
                                        @if ($hasUpdate)
                                            <form method="POST" action="{{ route('admin.plugins.update', $plugin) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-500 text-white text-xs font-medium hover:bg-amber-600 transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                    {{ __('Update to') }} {{ $plugin->metadata()['latest_version'] }}
                                                </button>
                                            </form>
                                        @endif

                                        @if ($plugin->isActive())
                                            <form method="POST" action="{{ route('admin.plugins.deactivate', $plugin) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-gray-300 text-gray-700 text-xs font-medium hover:bg-gray-50 transition">
                                                    {{ __('Deactivate') }}
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.plugins.activate', $plugin) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-xs font-medium hover:opacity-90 transition">
                                                    {{ __('Activate') }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </form>
        @endif

    </div>
</div>
@endsection
