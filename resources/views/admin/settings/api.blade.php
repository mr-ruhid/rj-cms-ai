@extends('admin.settings.layout')

@section('title', __('API & Integrations'))

@section('settings')
<div class="space-y-6" x-data="apiSettings()">

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-base font-semibold text-gray-900">{{ __('API & Integrations') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Manage API keys to allow external applications to access your site data.') }}</p>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded-r-lg shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-r-lg shadow-sm">
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/30 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">{{ __('Active API Keys') }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">{{ __('Use these keys to authenticate your external requests.') }}</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('admin.settings.api.generate') }}" class="flex items-end gap-3 max-w-lg mb-8">
                @csrf
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Application Name') }}</label>
                    <input type="text" name="name" required placeholder="e.g. Mobile App, Zapier"
                           class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm">
                </div>
                <button type="submit" class="px-5 py-2 rounded-xl bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-bold hover:opacity-90 transition shadow-sm">
                    {{ __('Generate Key') }}
                </button>
            </form>

            @if(empty($apiKeys))
                <div class="text-center py-10 border-2 border-dashed border-gray-200 rounded-xl">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    <p class="text-sm font-medium text-gray-900">{{ __('No API keys generated yet.') }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ __('Create one above to start using the API.') }}</p>
                </div>
            @else
                <div class="space-y-4">
                    <template x-for="(key, index) in keys" :key="key.id">
                        <div class="border border-gray-200 rounded-xl p-5 hover:border-indigo-200 transition bg-white">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                                        <span x-text="key.name.substring(0, 1).toUpperCase()"></span>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900" x-text="key.name"></h4>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            {{ __('Created') }}: <span x-text="key.created_at"></span>
                                            <span x-show="key.last_used_at"> · {{ __('Last used') }}: <span x-text="key.last_used_at"></span></span>
                                        </p>
                                    </div>
                                </div>
                                <button type="button" @click="revokeKey(key.id)" class="text-red-500 hover:text-red-700 text-xs font-semibold px-3 py-1.5 rounded-lg hover:bg-red-50 transition">
                                    {{ __('Revoke') }}
                                </button>
                            </div>

                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wide mb-1">{{ __('Public Key') }}</label>
                                    <div class="flex items-center gap-2 bg-gray-50 rounded-lg p-2 border border-gray-200">
                                        <code class="text-xs text-gray-800 font-mono flex-1 truncate" x-text="key.public_key"></code>
                                        <button type="button" @click="copyText(key.public_key)" class="text-gray-400 hover:text-gray-700 transition" title="Copy">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wide mb-1">{{ __('Private Key') }}</label>
                                    <div class="flex items-center gap-2 bg-gray-50 rounded-lg p-2 border border-gray-200">
                                        <code class="text-xs text-gray-800 font-mono flex-1 truncate" 
                                              x-text="showPrivate[key.id] ? key.private_key : '••••••••••••••••••••••••'"></code>
                                        <button type="button" @click="showPrivate[key.id] = !showPrivate[key.id]" class="text-gray-400 hover:text-gray-700 transition" title="Toggle visibility">
                                            <svg x-show="!showPrivate[key.id]" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <svg x-show="showPrivate[key.id]" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/30 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-gray-900">{{ __('API Documentation') }}</h3>
                <p class="text-xs text-gray-500 mt-0.5">{{ __('Available endpoints for external integrations.') }}</p>
            </div>
        </div>

        <div class="p-6">
            <div class="mb-6 bg-gray-900 rounded-xl p-4 overflow-x-auto">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">{{ __('Authentication') }}</div>
                <pre class="text-xs text-gray-100 font-mono leading-relaxed">curl -X GET "{{ url('/api/v1/posts') }}" \
  -H "X-Public-Key: YOUR_PUBLIC_KEY" \
  -H "X-Private-Key: YOUR_PRIVATE_KEY" \
  -H "Accept: application/json"</pre>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left font-semibold text-gray-600 px-4 py-2 w-24">{{ __('Method') }}</th>
                            <th class="text-left font-semibold text-gray-600 px-4 py-2">{{ __('Endpoint') }}</th>
                            <th class="text-left font-semibold text-gray-600 px-4 py-2">{{ __('Description') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($availableEndpoints as $endpoint)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide
                                        @if($endpoint['method'] === 'GET') bg-blue-100 text-blue-700
                                        @elseif($endpoint['method'] === 'POST') bg-emerald-100 text-emerald-700
                                        @elseif($endpoint['method'] === 'PUT') bg-amber-100 text-amber-700
                                        @else bg-red-100 text-red-700 @endif">
                                        {{ $endpoint['method'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-gray-800">{{ $endpoint['uri'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $endpoint['desc'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div x-show="toast.show" x-cloak x-transition
         class="fixed bottom-6 right-6 px-5 py-3 rounded-xl shadow-2xl text-white text-sm font-bold z-[60]"
         :class="toast.type === 'error' ? 'bg-red-600' : 'bg-emerald-600'" x-text="toast.message"></div>

</div>
@endsection

@push('scripts')
<script>
    function apiSettings() {
        return {
            keys: @js($apiKeys),
            showPrivate: {},
            toast: { show: false, message: '', type: 'success' },
            _toastTimer: null,

            init() {
                this.keys.forEach(k => { this.showPrivate[k.id] = false; });
            },

            async copyText(text) {
                try {
                    await navigator.clipboard.writeText(text);
                    this.showToast('{{ __('Copied to clipboard.') }}', 'success');
                } catch (e) {
                    this.showToast('{{ __('Could not copy.') }}', 'error');
                }
            },

            async revokeKey(id) {
                if (!confirm('{{ __('Are you sure you want to revoke this API key? External applications using it will lose access immediately.') }}')) {
                    return;
                }

                try {
                    const res = await fetch(`/admin/settings/api/${id}/revoke`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-HTTP-Method-Override': 'DELETE'
                        }
                    });

                    const data = await res.json();

                    if (res.ok && data.success) {
                        this.keys = this.keys.filter(k => k.id !== id);
                        this.showToast(data.message || '{{ __('Key revoked.') }}', 'success');
                    } else {
                        this.showToast(data.message || '{{ __('Failed to revoke key.') }}', 'error');
                    }
                } catch (e) {
                    this.showToast('{{ __('Network error.') }}', 'error');
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
@endpush