@extends('admin.layouts.app')

@section('title', __('Widgets'))

@section('content')
@php
    $totalItems = 0;
    foreach ($grouped as $items) {
        $totalItems += count($items);
    }
@endphp

<div class="space-y-6">

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-base font-semibold text-gray-900">{{ __('Widget Library') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Manage which widgets appear in RJBuilder and where they can be used.') }}</p>
        </div>
        <a href="{{ route('admin.rjbuilder.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            {{ __('Open Builder') }}
        </a>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Total') }}</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">{{ $counts['total'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Enabled') }}</div>
            <div class="text-2xl font-bold text-green-600 mt-1">{{ $counts['enabled'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Disabled') }}</div>
            <div class="text-2xl font-bold text-gray-400 mt-1">{{ $counts['disabled'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Core') }}</div>
            <div class="text-2xl font-bold text-rj-purple mt-1">{{ $counts['builtin'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Plugins') }}</div>
            <div class="text-2xl font-bold text-blue-600 mt-1">{{ $counts['plugin'] + $counts['uploaded'] }}</div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <form method="GET" action="{{ route('admin.appearance.widgets') }}" class="p-4 flex items-center gap-3 flex-wrap">
            <div class="relative flex-1 min-w-[200px] max-w-md">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="q" value="{{ $q }}" placeholder="{{ __('Search widgets...') }}"
                       class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
            </div>

            <select name="category" class="rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                <option value="">{{ __('All Categories') }}</option>
                @foreach ($categories as $key => $cat)
                    <option value="{{ $key }}" {{ $catFilter === $key ? 'selected' : '' }}>{{ $cat['label'] }}</option>
                @endforeach
            </select>

            <select name="source" class="rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                <option value="">{{ __('All Sources') }}</option>
                @foreach ($sources as $key => $label)
                    <option value="{{ $key }}" {{ $srcFilter === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            <select name="status" class="rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                <option value="">{{ __('All Status') }}</option>
                <option value="enabled" {{ $statusFilter === 'enabled' ? 'selected' : '' }}>{{ __('Enabled') }}</option>
                <option value="disabled" {{ $statusFilter === 'disabled' ? 'selected' : '' }}>{{ __('Disabled') }}</option>
            </select>

            <button type="submit" class="px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm font-medium text-gray-700 transition">
                {{ __('Filter') }}
            </button>

            @if ($q || $catFilter || $srcFilter || $statusFilter)
                <a href="{{ route('admin.appearance.widgets') }}" class="text-sm text-gray-500 hover:text-gray-900">{{ __('Clear') }}</a>
            @endif
        </form>
    </div>

    @if ($totalItems === 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-16 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            <p class="mt-4 text-sm text-gray-500">{{ __('No widgets match your filters.') }}</p>
        </div>
    @else
        @foreach ($grouped as $category => $items)
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">
                        {{ $categories[$category]['label'] ?? $category }}
                    </h3>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-xs font-medium">
                        {{ count($items) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach ($items as $item)
                        <div x-data="widgetCard(@js($item), @js(array_keys($contexts)))"
                             class="bg-white border rounded-xl overflow-hidden transition"
                             :class="enabled ? 'border-gray-200' : 'border-gray-200 opacity-60 bg-gray-50'">

                            <div class="p-4 flex items-start gap-3">
                                <div class="w-10 h-10 rounded-lg bg-rj-purple/10 text-rj-purple flex items-center justify-center flex-shrink-0">
                                    {!! $item['icon'] !!}
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="text-sm font-semibold text-gray-900 truncate">{{ $item['name'] }}</h4>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wide
                                            @if($item['source'] === 'builtin') bg-rj-purple/10 text-rj-purple
                                            @elseif($item['source'] === 'plugin') bg-blue-100 text-blue-700
                                            @elseif($item['source'] === 'uploaded') bg-emerald-100 text-emerald-700
                                            @else bg-gray-100 text-gray-600 @endif">
                                            {{ $sources[$item['source']] ?? $item['source'] }}
                                        </span>
                                        @if ($item['has_override'])
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wide bg-amber-100 text-amber-700">
                                                {{ __('Customized') }}
                                            </span>
                                        @endif
                                    </div>
                                    @if ($item['description'])
                                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $item['description'] }}</p>
                                    @endif
                                </div>

                                <label class="inline-flex items-center cursor-pointer flex-shrink-0">
                                    <input type="checkbox" x-model="enabled" @change="saveToggle()" :disabled="saving" class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-200 peer-checked:bg-rj-purple rounded-full relative transition">
                                        <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transform transition" :class="enabled ? 'translate-x-4' : ''"></div>
                                    </div>
                                </label>
                            </div>

                            <div class="px-4 pb-4 pt-3 border-t border-gray-100 grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-medium text-gray-500 uppercase tracking-wide mb-1">{{ __('Category') }}</label>
                                    <select x-model="category" @change="saveCategory()" :disabled="saving"
                                            class="w-full px-2 py-1.5 rounded-md border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-xs">
                                        @foreach ($categories as $key => $cat)
                                            <option value="{{ $key }}">{{ $cat['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-medium text-gray-500 uppercase tracking-wide mb-1">{{ __('Contexts') }}</label>
                                    <div class="flex items-center gap-1 flex-wrap">
                                        <template x-for="ctx in Object.keys(contextLabels)" :key="ctx">
                                            <button type="button"
                                                    @click="toggleContext(ctx)"
                                                    :disabled="saving"
                                                    class="px-1.5 py-0.5 rounded text-[10px] font-medium border transition"
                                                    :class="contexts.includes(ctx)
                                                        ? 'bg-rj-purple/10 text-rj-purple border-rj-purple/30'
                                                        : 'bg-white text-gray-500 border-gray-200 hover:bg-gray-50'"
                                                    x-text="contextLabels[ctx]"></button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div class="px-4 py-2 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                                <span class="text-[10px] text-gray-400 font-mono">{{ $item['id'] }}</span>
                                <button type="button" @click="resetWidget()" :disabled="saving"
                                        class="text-[11px] text-gray-500 hover:text-red-600 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                    {{ __('Reset') }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif

    <div x-data="widgetToasts()" @widget-toast.window="show($event.detail.message, $event.detail.type)">
        <div x-show="toast.show" x-cloak x-transition
             class="fixed bottom-6 right-6 px-5 py-3 rounded-xl shadow-2xl text-white text-sm font-medium z-50"
             :style="'background:' + (toast.type === 'error' ? '#dc2626' : '#059669')"
             x-text="toast.message"></div>
    </div>

</div>

<script>
function widgetToasts() {
    return {
        toast: { show: false, message: '', type: 'success' },
        show(message, type) {
            this.toast = { show: true, message, type: type || 'success' };
            setTimeout(() => { this.toast.show = false; }, 3500);
        }
    };
}

function widgetCard(item, contextKeys) {
    const base = '{{ url('/admin/appearance/widgets') }}';
    const csrf = '{{ csrf_token() }}';
    const contextLabels = {!! json_encode(collect($contexts)->map(fn($c) => $c['label'])->toArray()) !!};

    return {
        id: item.id,
        enabled: item.enabled,
        category: item.category,
        contexts: item.contexts || contextKeys.slice(),
        contextLabels,
        saving: false,

        async request(path, body) {
            this.saving = true;
            try {
                const res = await fetch(base + '/' + this.id + '/' + path, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify(body || {}),
                });
                const data = await res.json().catch(() => ({}));
                return { ok: res.ok && data.success, data };
            } catch (e) {
                return { ok: false, data: { message: '{{ __('Network error') }}' } };
            } finally {
                this.saving = false;
            }
        },

        notify(message, type) {
            window.dispatchEvent(new CustomEvent('widget-toast', { detail: { message, type } }));
        },

        async saveToggle() {
            const desired = this.enabled;
            const result = await this.request('toggle', { enabled: desired });
            if (result.ok) {
                this.notify(result.data.message || '{{ __('Saved') }}', 'success');
            } else {
                this.enabled = !desired;
                this.notify(result.data.message || '{{ __('Save failed') }}', 'error');
            }
        },

        async saveCategory() {
            const result = await this.request('update', { category: this.category });
            if (result.ok) {
                this.notify(result.data.message || '{{ __('Saved') }}', 'success');
            } else {
                this.notify(result.data.message || '{{ __('Save failed') }}', 'error');
            }
        },

        async toggleContext(ctx) {
            const idx = this.contexts.indexOf(ctx);
            if (idx === -1) {
                this.contexts.push(ctx);
            } else if (this.contexts.length > 1) {
                this.contexts.splice(idx, 1);
            } else {
                this.notify('{{ __('At least one context is required') }}', 'error');
                return;
            }
            const result = await this.request('update', { contexts: this.contexts });
            if (result.ok) {
                this.notify(result.data.message || '{{ __('Saved') }}', 'success');
            } else {
                if (idx === -1) {
                    const i2 = this.contexts.indexOf(ctx);
                    if (i2 !== -1) this.contexts.splice(i2, 1);
                } else {
                    this.contexts.push(ctx);
                }
                this.notify(result.data.message || '{{ __('Save failed') }}', 'error');
            }
        },

        async resetWidget() {
            if (!confirm('{{ __('Reset this widget to defaults?') }}')) return;
            const result = await this.request('reset');
            if (result.ok) {
                this.notify(result.data.message || '{{ __('Reset') }}', 'success');
                setTimeout(() => window.location.reload(), 600);
            } else {
                this.notify(result.data.message || '{{ __('Reset failed') }}', 'error');
            }
        },
    };
}
</script>
@endsection
