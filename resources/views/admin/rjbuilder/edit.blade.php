@extends('admin.layouts.app')

@section('title', $template->name)

@section('content')

<div class="max-w-4xl mx-auto py-8" x-data="templateEdit()">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.rjbuilder.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← {{ __('Back') }}</a>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ $template->name }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $template->typeLabel() }} · {{ $template->status }}</p>
        </div>
        <div class="flex gap-2">
            @if($template->status === 'published')
                <a href="{{ route('frontend.preview', $template) }}" target="_blank" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                    {{ __('Preview on site') }} ↗
                </a>
            @endif
            <a href="{{ route('admin.rjbuilder.build', $template) }}" class="px-5 py-2 bg-gradient-to-r from-purple-600 to-pink-500 text-white rounded-lg text-sm font-semibold hover:opacity-90">
                {{ __('Open Builder') }} →
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Template Info') }}</h2>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500">{{ __('Name') }}</dt>
                <dd class="font-medium text-gray-900 mt-1">{{ $template->name }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">{{ __('Type') }}</dt>
                <dd class="font-medium text-gray-900 mt-1">{{ $template->typeLabel() }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">{{ __('Status') }}</dt>
                <dd class="mt-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $template->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                        {{ ucfirst($template->status) }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-gray-500">{{ __('Default') }}</dt>
                <dd class="font-medium text-gray-900 mt-1">{{ $template->is_default ? __('Yes') : __('No') }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">{{ __('Blocks') }}</dt>
                <dd class="font-medium text-gray-900 mt-1">{{ $template->blockCount() }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">{{ __('Updated') }}</dt>
                <dd class="font-medium text-gray-900 mt-1">{{ $template->updated_at->diffForHumans() }}</dd>
            </div>
        </dl>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Display Conditions') }}</h2>
                <p class="text-xs text-gray-500 mt-1">{{ __('Choose where this template is displayed.') }}</p>
            </div>
            <button type="button" @click="openConditions()"
                    class="px-4 py-2 border border-purple-300 text-purple-700 rounded-lg text-sm font-medium hover:bg-purple-50">
                {{ __('Edit Conditions') }}
            </button>
        </div>

        <div x-show="!hasConditions" class="text-sm text-gray-500 italic">
            {{ __('No conditions set. This template will not appear anywhere by default.') }}
        </div>

        <div x-show="hasConditions" x-cloak>
            <div class="flex items-center gap-2 mb-2 text-xs text-gray-500">
                <span>{{ __('Match') }}:</span>
                <span class="font-semibold uppercase" x-text="conditions.match"></span>
            </div>
            <ul class="space-y-2">
                <template x-for="(rule, i) in (conditions.rules || [])" :key="i">
                    <li class="flex items-center gap-2 text-sm">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                        <span class="font-medium" x-text="ruleLabel(rule.type)"></span>
                        <span x-show="rule.value" class="text-gray-500" x-text="'→ ' + rule.value"></span>
                    </li>
                </template>
            </ul>
        </div>
    </div>

    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">{{ __('Display Conditions') }}</h3>
                <button type="button" @click="showModal = false" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">{{ __('Match') }}</label>
                <select x-model="draft.match" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="all">{{ __('All conditions must match') }}</option>
                    <option value="any">{{ __('Any condition can match') }}</option>
                </select>
            </div>

            <div class="mb-4 space-y-2">
                <template x-for="(rule, i) in draft.rules" :key="i">
                    <div class="flex items-center gap-2">
                        <select x-model="rule.type" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="entire_site">{{ __('Entire Site') }}</option>
                            <option value="front_page">{{ __('Front Page') }}</option>
                            <option value="not_front_page">{{ __('Not Front Page') }}</option>
                            <option value="search">{{ __('Search Results') }}</option>
                            <option value="url_contains">{{ __('URL Contains') }}</option>
                            <option value="url_exact">{{ __('URL Is') }}</option>
                        </select>
                        <input type="text" x-model="rule.value" x-show="rule.type === 'url_contains' || rule.type === 'url_exact'"
                               placeholder="{{ __('Value') }}"
                               class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <button type="button" @click="draft.rules.splice(i, 1)" class="text-red-500 hover:text-red-700 px-2">✕</button>
                    </div>
                </template>
            </div>

            <button type="button" @click="addRule()" class="text-sm text-purple-600 hover:text-purple-800 font-medium mb-4">
                + {{ __('Add Condition') }}
            </button>

            <div class="flex justify-end gap-2 pt-4 border-t border-gray-200">
                <button type="button" @click="showModal = false" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">
                    {{ __('Cancel') }}
                </button>
                <button type="button" @click="saveConditions()" :disabled="saving"
                        class="px-5 py-2 bg-purple-600 text-white rounded-lg text-sm font-semibold hover:bg-purple-700 disabled:opacity-60">
                    <span x-text="saving ? '{{ __('Saving...') }}' : '{{ __('Save') }}'"></span>
                </button>
            </div>
        </div>
    </div>

    <script>
        window.__templateId = {{ $template->id }};
        window.__initialConditions = @json($template->conditions ?? []);
        window.__updateConditionsUrl = @json(route('admin.rjbuilder.conditions', $template));
        window.__csrf = @json(csrf_token());
    </script>

    <script>
        function templateEdit() {
            return {
                showModal: false,
                saving: false,
                conditions: window.__initialConditions || {},
                draft: { match: 'all', rules: [] },

                get hasConditions() {
                    return this.conditions && Array.isArray(this.conditions.rules) && this.conditions.rules.length > 0;
                },

                ruleLabel(type) {
                    const labels = {
                        'entire_site': '{{ __('Entire Site') }}',
                        'front_page': '{{ __('Front Page') }}',
                        'not_front_page': '{{ __('Not Front Page') }}',
                        'search': '{{ __('Search Results') }}',
                        'url_contains': '{{ __('URL Contains') }}',
                        'url_exact': '{{ __('URL Is') }}',
                    };
                    return labels[type] || type;
                },

                openConditions() {
                    this.draft = {
                        match: this.conditions.match || 'all',
                        rules: Array.isArray(this.conditions.rules) ? JSON.parse(JSON.stringify(this.conditions.rules)) : [],
                    };
                    this.showModal = true;
                },

                addRule() {
                    this.draft.rules.push({ type: 'entire_site', value: '' });
                },

                async saveConditions() {
                    this.saving = true;
                    try {
                        const res = await fetch(window.__updateConditionsUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': window.__csrf,
                            },
                            body: JSON.stringify({ conditions: this.draft }),
                        });
                        if (res.ok) {
                            this.conditions = this.draft;
                            this.showModal = false;
                        }
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.saving = false;
                    }
                },
            };
        }
    </script>
</div>

@endsection