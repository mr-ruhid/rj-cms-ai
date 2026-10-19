@extends('admin.layouts.app')

@section('title', $theme->name . ' — ' . __('Customize'))

@section('content')
@php
    $initValues = [];
    foreach ($sections as $sk => $section) {
        $initValues[$sk] = [];
        foreach ($section['controls'] as $ck => $control) {
            $initValues[$sk][$ck] = $values[$sk][$ck] ?? ($control['default'] ?? '');
        }
    }
@endphp

@if (empty($sections))
    <div class="max-w-2xl mx-auto mt-12 bg-white rounded-xl shadow-sm border border-gray-200 p-10 text-center">
        <div class="w-16 h-16 mx-auto rounded-full bg-amber-50 flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h2 class="text-lg font-semibold text-gray-900">{{ __('This theme does not support customization') }}</h2>
        <p class="text-sm text-gray-500 mt-2 max-w-md mx-auto">
            {{ __('The active theme (:name) has not declared a customizer schema. Add a "customizer" block to theme.json to enable this feature.', ['name' => $theme->name]) }}
        </p>
        <div class="flex items-center justify-center gap-3 mt-6">
            <a href="{{ route('admin.themes.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
                {{ __('Back to Themes') }}
            </a>
            <a href="{{ route('admin.themes.show', $theme) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                {{ __('View Theme Details') }}
            </a>
        </div>
    </div>
@else
    <div x-data='themeCustomizer(@js($initValues))' class="space-y-6">

        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-base font-semibold text-gray-900">{{ $theme->name }} — {{ __('Customize') }}</h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ __('Personalize your theme. Changes are saved per section.') }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}" target="_blank"
                   class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    {{ __('View Site') }}
                </a>

                <a href="{{ route('admin.themes.index') }}"
                   class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    {{ __('Back') }}
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <div class="lg:col-span-3 space-y-3">
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Sections') }}</h3>
                    </div>
                    <nav class="p-2 space-y-1">
                        @foreach ($sections as $sectionKey => $section)
                            <button type="button"
                                    @click="activeSection = '{{ $sectionKey }}'"
                                    :class="activeSection === '{{ $sectionKey }}'
                                        ? 'bg-gradient-to-r from-rj-purple/10 to-rj-pink/10 text-rj-purple font-medium border-l-2 border-rj-purple'
                                        : 'text-gray-600 hover:bg-gray-50 border-l-2 border-transparent'"
                                    class="w-full text-left px-3 py-2 rounded-md text-sm transition flex items-center gap-2">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                                <span class="truncate">{{ $section['label'] }}</span>
                            </button>
                        @endforeach
                    </nav>
                </div>

                <button type="button"
                        @click="resetAll()"
                        :disabled="saving['__all']"
                        class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-white border border-red-200 text-red-600 text-sm font-medium hover:bg-red-50 transition disabled:opacity-60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    {{ __('Restore Defaults') }}
                </button>
            </div>

            <div class="lg:col-span-9">
                @foreach ($sections as $sectionKey => $section)
                    <div x-show="activeSection === '{{ $sectionKey }}'" x-cloak
                         class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">

                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">{{ $section['label'] }}</h3>
                                <p class="text-xs text-gray-500 mt-0.5">{{ count($section['controls']) }} {{ __('setting(s)') }}</p>
                            </div>
                        </div>

                        <div class="p-6 space-y-5">
                            @foreach ($section['controls'] as $controlKey => $control)
                                @php
                                    $type = $control['type'] ?? 'text';
                                    $label = $control['label'] ?? $controlKey;
                                    $model = "values['{$sectionKey}']['{$controlKey}']";
                                @endphp

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ $label }}</label>

                                    @switch($type)
                                        @case('color')
                                            <div class="flex items-center gap-3">
                                                <input type="color"
                                                       x-model="{{ $model }}"
                                                       class="w-12 h-10 rounded-lg border border-gray-300 cursor-pointer p-1">
                                                <input type="text"
                                                       x-model="{{ $model }}"
                                                       class="flex-1 px-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                                            </div>
                                            @break

                                        @case('range')
                                            <div class="flex items-center gap-4">
                                                <input type="range"
                                                       x-model.number="{{ $model }}"
                                                       min="{{ $control['min'] ?? 0 }}"
                                                       max="{{ $control['max'] ?? 100 }}"
                                                       step="{{ $control['step'] ?? 1 }}"
                                                       class="flex-1 accent-rj-purple">
                                                <div class="min-w-[80px] text-right text-sm font-medium text-gray-900">
                                                    <span x-text="{{ $model }}"></span>{{ $control['suffix'] ?? '' }}
                                                </div>
                                            </div>
                                            @break

                                        @case('select')
                                            <select x-model="{{ $model }}"
                                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                                                @foreach ($control['options'] ?? [] as $optValue => $optLabel)
                                                    <option value="{{ $optValue }}">{{ $optLabel }}</option>
                                                @endforeach
                                            </select>
                                            @break

                                        @case('toggle')
                                            <label class="inline-flex items-center gap-3 cursor-pointer">
                                                <input type="checkbox"
                                                       x-model="{{ $model }}"
                                                       class="w-5 h-5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                                                <span class="text-sm text-gray-600">{{ __('Enable') }}</span>
                                            </label>
                                            @break

                                        @case('textarea')
                                            <textarea x-model="{{ $model }}" rows="4"
                                                      class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm"></textarea>
                                            @break

                                        @case('number')
                                            <input type="number"
                                                   x-model.number="{{ $model }}"
                                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                                            @break

                                        @case('url')
                                            <input type="url"
                                                   x-model="{{ $model }}"
                                                   placeholder="https://..."
                                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                                            @break

                                        @default
                                            <input type="text"
                                                   x-model="{{ $model }}"
                                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                                    @endswitch
                                </div>
                            @endforeach
                        </div>

                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between gap-3">
                            <button type="button"
                                    @click="resetSection('{{ $sectionKey }}')"
                                    :disabled="saving['{{ $sectionKey }}']"
                                    class="text-sm text-gray-500 hover:text-red-600 transition disabled:opacity-60">
                                {{ __('Reset this section') }}
                            </button>

                            <button type="button"
                                    @click="saveSection('{{ $sectionKey }}')"
                                    :disabled="saving['{{ $sectionKey }}']"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition disabled:opacity-60">
                                <svg x-show="!saving['{{ $sectionKey }}']" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <svg x-show="saving['{{ $sectionKey }}']" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                                <span x-text="saving['{{ $sectionKey }}'] ? '{{ __('Saving...') }}' : '{{ __('Save Changes') }}'"></span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div x-show="toast.show" x-cloak
             x-transition
             class="fixed bottom-6 right-6 px-5 py-3 rounded-xl shadow-2xl text-white text-sm font-medium z-50"
             :style="'background:' + (toast.type === 'error' ? '#dc2626' : '#059669')"
             x-text="toast.message"></div>
    </div>

    <script>
    function themeCustomizer(initial) {
        return {
            values: initial,
            activeSection: Object.keys(initial)[0] || '',
            saving: {},
            toast: { show: false, message: '', type: 'success' },

            csrf() {
                return document.querySelector('meta[name="csrf-token"]').content;
            },

            showToast(message, type) {
                this.toast = { show: true, message, type: type || 'success' };
                setTimeout(() => { this.toast.show = false; }, 3500);
            },

            async saveSection(sectionKey) {
                this.saving[sectionKey] = true;

                try {
                    const res = await fetch('{{ route('admin.themes.customizer.update', $theme) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrf(),
                            'X-HTTP-Method-Override': 'PUT',
                        },
                        body: JSON.stringify({
                            section: sectionKey,
                            settings: this.values[sectionKey] || {},
                        }),
                    });

                    const data = await res.json().catch(() => ({}));

                    if (res.ok && data.success) {
                        this.showToast(data.message || '{{ __('Saved.') }}', 'success');
                    } else {
                        this.showToast(data.message || '{{ __('Save failed.') }}', 'error');
                    }
                } catch (e) {
                    this.showToast('{{ __('Network error.') }}', 'error');
                } finally {
                    this.saving[sectionKey] = false;
                }
            },

            async resetSection(sectionKey) {
                if (!confirm('{{ __('Reset this section to default values?') }}')) {
                    return;
                }

                this.saving[sectionKey] = true;

                try {
                    const res = await fetch('{{ route('admin.themes.customizer.reset', $theme) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrf(),
                            'X-HTTP-Method-Override': 'DELETE',
                        },
                        body: JSON.stringify({ section: sectionKey }),
                    });

                    const data = await res.json().catch(() => ({}));

                    if (res.ok && data.success) {
                        this.showToast(data.message || '{{ __('Reset.') }}', 'success');
                        setTimeout(() => window.location.reload(), 800);
                    } else {
                        this.showToast(data.message || '{{ __('Reset failed.') }}', 'error');
                    }
                } catch (e) {
                    this.showToast('{{ __('Network error.') }}', 'error');
                } finally {
                    this.saving[sectionKey] = false;
                }
            },

            async resetAll() {
                if (!confirm('{{ __('Reset ALL customizations for this theme? This cannot be undone.') }}')) {
                    return;
                }

                this.saving['__all'] = true;

                try {
                    const res = await fetch('{{ route('admin.themes.customizer.reset', $theme) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrf(),
                            'X-HTTP-Method-Override': 'DELETE',
                        },
                        body: JSON.stringify({}),
                    });

                    const data = await res.json().catch(() => ({}));

                    if (res.ok && data.success) {
                        this.showToast(data.message || '{{ __('All customizations reset.') }}', 'success');
                        setTimeout(() => window.location.reload(), 800);
                    } else {
                        this.showToast(data.message || '{{ __('Reset failed.') }}', 'error');
                    }
                } catch (e) {
                    this.showToast('{{ __('Network error.') }}', 'error');
                } finally {
                    this.saving['__all'] = false;
                }
            },
        };
    }
    </script>
@endif
@endsection
