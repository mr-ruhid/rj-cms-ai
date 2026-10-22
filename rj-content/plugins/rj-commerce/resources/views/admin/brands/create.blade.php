@extends('admin.layouts.app')

@section('title', __('Add Brand'))

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="{ activeTab: '{{ config('app.locale') }}' }">

    <div class="flex items-center gap-4">
        <a href="{{ route('admin.commerce.brands.index') }}" class="p-2 rounded-lg hover:bg-gray-100 transition text-gray-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Add New Brand') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('Create a new brand for your products.') }}</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border-s-4 border-red-500 text-red-800 px-4 py-3 rounded-e-lg">
            <ul class="list-disc pl-5 text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.commerce.brands.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        @csrf

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="flex border-b border-gray-100 bg-gray-50 overflow-x-auto">
                    @foreach($locales as $locale)
                        <button type="button" @click="activeTab = '{{ $locale }}'" :class="activeTab === '{{ $locale }}' ? 'border-rj-purple text-rj-purple bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100'" class="px-6 py-4 text-sm font-bold uppercase tracking-wider border-b-2 transition">
                            {{ $locale }}
                        </button>
                    @endforeach
                </div>

                <div class="p-6 space-y-5">
                    @foreach($locales as $locale)
                        <div x-show="activeTab === '{{ $locale }}'" x-cloak class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Name') }} ({{ strtoupper($locale) }})</label>
                                <input type="text" name="translations[{{ $locale }}][name]" value="{{ old("translations.$locale.name") }}" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm" placeholder="{{ __('Brand name...') }}">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Description') }} ({{ strtoupper($locale) }})</label>
                                <textarea name="translations[{{ $locale }}][description]" rows="4" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm" placeholder="{{ __('Brand description...') }}">{{ old("translations.$locale.description") }}</textarea>
                            </div>

                            <div class="pt-4 border-t border-gray-100 space-y-4">
                                <h4 class="text-sm font-semibold text-gray-900">{{ __('SEO Settings') }} ({{ strtoupper($locale) }})</h4>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('SEO Title') }}</label>
                                    <input type="text" name="translations[{{ $locale }}][seo_title]" value="{{ old("translations.$locale.seo_title") }}" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('SEO Description') }}</label>
                                    <textarea name="translations[{{ $locale }}][seo_description]" rows="2" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">{{ old("translations.$locale.seo_description") }}</textarea>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-3">{{ __('Publish Settings') }}</h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Slug') }}</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono" placeholder="{{ __('Leave empty to auto-generate') }}">
                </div>

                <label class="flex items-center gap-2 cursor-pointer pt-2">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <span class="text-sm text-gray-700">{{ __('Active') }}</span>
                </label>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-3">{{ __('Logo') }}</h2>

                <div>
                    <input type="file" name="logo" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-rj-purple/10 file:text-rj-purple hover:file:bg-rj-purple/20">
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="w-full inline-flex justify-center items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-rj-purple to-rj-pink text-white font-bold text-sm hover:opacity-90 transition shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    {{ __('Save Brand') }}
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
