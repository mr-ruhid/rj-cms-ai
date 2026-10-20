@extends('admin.layouts.app')

@section('title', __('AI Models'))

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="aiModelsForm()">

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-6 h-6 text-rj-purple" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                {{ __('AI Center') }}
            </h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Manage models, prompts, and monitor AI usage across your site.') }}</p>
        </div>
    </div>

    <div class="border-b border-gray-200">
        <nav class="flex gap-4 overflow-x-auto">
            <a href="{{ route('admin.ai.index') }}" class="px-4 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                {{ __('Overview') }}
            </a>
            <a href="{{ route('admin.ai.models') }}" class="px-4 py-3 border-b-2 border-rj-purple text-rj-purple font-semibold text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                {{ __('Models') }}
            </a>
            <a href="{{ route('admin.ai.prompts') }}" class="px-4 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                {{ __('Prompts') }}
            </a>
            <a href="{{ route('admin.ai.usage') }}" class="px-4 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                {{ __('Usage') }}
            </a>
            <a href="{{ route('admin.ai.tools') }}" class="px-4 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                {{ __('Tools') }}
            </a>
        </nav>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded-r-lg shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.ai.models.save') }}" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        @csrf
        
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-gray-900">{{ __('Model Assignments') }}</h3>
                <p class="text-xs text-gray-500 mt-1">{{ __('Select which AI model should be used for specific tasks.') }}</p>
            </div>
            <span class="text-xs font-semibold bg-white border border-gray-200 px-2.5 py-1 rounded-full shadow-sm text-gray-600">
                {{ count($models) }} {{ __('models available') }}
            </span>
        </div>

        <div class="p-6 space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-900">{{ __('Default Model') }}</label>
                    <p class="text-xs text-gray-500 mb-2">{{ __('Used as a fallback for all tasks.') }}</p>
                    <select name="default_model" x-model="form.default_model" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-white focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm">
                        <template x-for="model in models" :key="model.id">
                            <option :value="model.id" x-text="model.name + ' (' + model.id + ')'"></option>
                        </template>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-900">{{ __('SEO Tasks') }}</label>
                    <p class="text-xs text-gray-500 mb-2">{{ __('Meta generation, keyword suggestions, etc.') }}</p>
                    <select name="seo_model" x-model="form.seo_model" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-white focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm">
                        <option value="">{{ __('Use Default') }}</option>
                        <template x-for="model in models" :key="model.id">
                            <option :value="model.id" x-text="model.name + ' (' + model.id + ')'"></option>
                        </template>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-900">{{ __('Translation Tasks') }}</label>
                    <p class="text-xs text-gray-500 mb-2">{{ __('Bulk translation and content localization.') }}</p>
                    <select name="translate_model" x-model="form.translate_model" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-white focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm">
                        <option value="">{{ __('Use Default') }}</option>
                        <template x-for="model in models" :key="model.id">
                            <option :value="model.id" x-text="model.name + ' (' + model.id + ')'"></option>
                        </template>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-900">{{ __('Builder Tasks') }}</label>
                    <p class="text-xs text-gray-500 mb-2">{{ __('Text generation inside RJBuilder and Content Generator.') }}</p>
                    <select name="builder_model" x-model="form.builder_model" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-white focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm">
                        <option value="">{{ __('Use Default') }}</option>
                        <template x-for="model in models" :key="model.id">
                            <option :value="model.id" x-text="model.name + ' (' + model.id + ')'"></option>
                        </template>
                    </select>
                </div>
            </div>

        </div>

        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-end">
            <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-bold hover:opacity-90 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ __('Save Model Assignments') }}
            </button>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
function aiModelsForm() {
    return {
        models: @js($models),
        form: {
            default_model: @js($defaultModel),
            seo_model: @js($seoModel),
            translate_model: @js($translateModel),
            builder_model: @js($builderModel),
        }
    };
}
</script>
@endpush