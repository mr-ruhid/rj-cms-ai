@extends('admin.layouts.app')

@section('title', __('System Diagnostics'))

@section('content')
@php
    $titles = [
        'server' => ['label' => __('Server Environment'), 'icon' => 'server'],
        'database' => ['label' => __('Database'), 'icon' => 'database'],
        'rj_cms' => ['label' => __('RJ CMS Core'), 'icon' => 'cube'],
        'paths' => ['label' => __('File Paths'), 'icon' => 'folder'],
        'security' => ['label' => __('Security & Sessions'), 'icon' => 'shield'],
    ];
@endphp

<div class="max-w-6xl mx-auto space-y-6 pb-12" x-data="diagnosticsTool(@js($groups))">

    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ __('System Diagnostics') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Deep technical information about your server, database, and configuration. Useful for debugging and support.') }}</p>
        </div>

        <button type="button" @click="copyReport()"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-gray-800 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            {{ __('Copy Report to Clipboard') }}
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        @foreach ($groups as $key => $items)
            @php
                $meta = $titles[$key] ?? ['label' => ucfirst($key), 'icon' => 'circle'];
            @endphp
            
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col {{ $key === 'paths' || $key === 'security' ? 'lg:col-span-2' : '' }}">
                <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-gray-600 shadow-sm">
                        @switch($meta['icon'])
                            @case('server')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/></svg>
                                @break
                            @case('database')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                                @break
                            @case('cube')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                @break
                            @case('folder')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                @break
                            @case('shield')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                @break
                            @default
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/></svg>
                        @endswitch
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">{{ $meta['label'] }}</h3>
                </div>

                <div class="p-5 flex-1">
                    <div class="grid grid-cols-1 gap-y-3 gap-x-6 {{ $key === 'paths' || $key === 'security' ? 'md:grid-cols-2' : '' }}">
                        @foreach ($items as $label => $value)
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 pb-3 border-b border-gray-100 last:border-0 last:pb-0">
                                <span class="text-sm text-gray-500">{{ __($label) }}</span>
                                <span class="text-sm font-medium text-gray-900 font-mono text-left sm:text-right break-all">{{ $value ?: '—' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach

    </div>

    <div x-show="toast.show" x-cloak x-transition
         class="fixed bottom-6 right-6 px-5 py-3 rounded-xl shadow-2xl text-white text-sm font-bold z-[60]"
         :class="toast.type === 'error' ? 'bg-red-600' : 'bg-emerald-600'" x-text="toast.message"></div>
</div>

<script>
function diagnosticsTool(groups) {
    return {
        toast: { show: false, message: '', type: 'success' },
        _toastTimer: null,

        async copyReport() {
            let report = "=== RJ CMS SYSTEM DIAGNOSTICS ===\n";
            report += "Generated: " + new Date().toISOString() + "\n\n";

            for (const [key, items] of Object.entries(groups)) {
                report += "--- " + key.toUpperCase() + " ---\n";
                for (const [label, value] of Object.entries(items)) {
                    report += label + ": " + (value || 'N/A') + "\n";
                }
                report += "\n";
            }

            try {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(report);
                } else {
                    const ta = document.createElement('textarea');
                    ta.value = report;
                    ta.style.position = 'fixed';
                    ta.style.opacity = '0';
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                }
                this.showToast('{{ __('Report copied to clipboard.') }}', 'success');
            } catch (e) {
                this.showToast('{{ __('Could not copy to clipboard.') }}', 'error');
            }
        },

        showToast(message, type) {
            this.toast = { show: true, message, type: type || 'success' };
            clearTimeout(this._toastTimer);
            this._toastTimer = setTimeout(() => { this.toast.show = false; }, 3500);
        }
    };
}
</script>
@endsection