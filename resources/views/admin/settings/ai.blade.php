@extends('admin.settings.layout')

@section('settings')
<div class="space-y-6" x-data="aiSettings()">

    <form method="POST" action="{{ route('admin.settings.ai.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">{{ __('OpenRouter Integration') }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ __('Connect your site to 300+ AI models via OpenRouter.') }}</p>
                </div>
                <a href="https://openrouter.ai/keys" target="_blank" class="text-xs text-rj-purple hover:underline flex items-center gap-1">
                    {{ __('Get API Key') }}
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>

            <div class="p-6 space-y-5">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('API Key') }}</label>
                    <div class="relative">
                        <input :type="showKey ? 'text' : 'password'" name="ai_openrouter_api_key"
                               value="{{ $options['ai_openrouter_api_key'] ?? '' }}"
                               placeholder="sk-or-v1-..."
                               class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm pr-10 font-mono">
                        <button type="button" @click="showKey = !showKey" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg x-show="!showKey" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="showKey" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ __('Leave empty to keep the current key unchanged.') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('API Endpoint') }}</label>
                    <input type="url" name="ai_openrouter_endpoint"
                           value="{{ $options['ai_openrouter_endpoint'] ?? 'https://openrouter.ai/api/v1' }}"
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Default Model') }}</label>
                    <div class="flex gap-2">
                        <input type="text" name="ai_default_model" x-model="model"
                               value="{{ $options['ai_default_model'] ?? 'google/gemini-2.0-flash-exp:free' }}"
                               class="flex-1 rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                        <button type="button" @click="fetchModels()" :disabled="loadingModels"
                                class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-300 text-sm text-gray-700 hover:bg-gray-50 transition disabled:opacity-50">
                            <svg x-show="!loadingModels" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <svg x-show="loadingModels" x-cloak class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                            <span x-text="loadingModels ? '{{ __('Loading') }}' : '{{ __('Fetch') }}'"></span>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ __('Example: google/gemini-2.0-flash-exp:free, deepseek/deepseek-r1:free') }}</p>

                    <div x-show="models.length > 0" x-cloak class="mt-3 max-h-64 overflow-y-auto border border-gray-200 rounded-lg rj-scrollbar">
                        <template x-for="m in filteredModels" :key="m.id">
                            <button type="button" @click="model = m.id; models = []; document.querySelector('input[name=ai_default_model]').value = m.id"
                                    class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 border-b border-gray-100 last:border-b-0 flex items-center justify-between gap-2">
                                <span class="font-mono text-xs text-gray-800" x-text="m.id"></span>
                                <span class="text-xs text-gray-400" x-text="m.pricing?.prompt === '0' ? '{{ __('Free') }}' : ''"></span>
                            </button>
                        </template>
                    </div>
                    <p x-show="modelsError" x-cloak class="text-xs text-red-500 mt-1" x-text="modelsError"></p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Temperature') }}</label>
                        <input type="number" name="ai_default_temperature" step="0.1" min="0" max="2"
                               value="{{ $options['ai_default_temperature'] ?? '0.7' }}"
                               class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Max Tokens') }}</label>
                        <input type="number" name="ai_default_max_tokens" min="1" max="32000"
                               value="{{ $options['ai_default_max_tokens'] ?? '2000' }}"
                               class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Timeout (s)') }}</label>
                        <input type="number" name="ai_timeout" min="5" max="600"
                               value="{{ $options['ai_timeout'] ?? '120' }}"
                               class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    </div>
                </div>

                <label class="flex items-start gap-3 cursor-pointer border-t border-gray-100 pt-5">
                    <input type="checkbox" name="ai_log_enabled" value="1"
                           {{ ($options['ai_log_enabled'] ?? '1') === '1' ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <span>
                        <span class="block text-sm font-medium text-gray-700">{{ __('Log AI requests') }}</span>
                        <span class="block text-xs text-gray-500 mt-0.5">{{ __('Store request metadata and token usage for analytics.') }}</span>
                    </span>
                </label>

            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                {{ __('Save Changes') }}
            </button>
        </div>
    </form>

    <form method="POST" action="{{ route('admin.settings.ai.test') }}">
        @csrf
        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 text-sm text-gray-700 hover:bg-gray-50 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ __('Test Connection') }}
        </button>
    </form>
</div>

<script>
function aiSettings() {
    return {
        showKey: false,
        model: '{{ $options['ai_default_model'] ?? 'google/gemini-2.0-flash-exp:free' }}',
        models: [],
        modelsError: '',
        loadingModels: false,
        get filteredModels() {
            return this.models.filter(m => m.id).slice(0, 200);
        },
        async fetchModels() {
            this.loadingModels = true;
            this.modelsError = '';
            try {
                const res = await fetch('{{ route('admin.settings.ai.models') }}', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) {
                    const err = await res.json().catch(() => ({}));
                    this.modelsError = err.error || 'HTTP ' + res.status;
                    return;
                }
                this.models = await res.json();
                if (!Array.isArray(this.models) || this.models.length === 0) {
                    this.modelsError = '{{ __('No models returned. Check your API key.') }}';
                }
            } catch (e) {
                this.modelsError = e.message;
            } finally {
                this.loadingModels = false;
            }
        }
    }
}
</script>
@endsection
