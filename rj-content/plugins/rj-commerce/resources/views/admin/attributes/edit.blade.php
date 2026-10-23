@extends('admin.layouts.app')

@section('title', __('Edit Attribute'))

@section('content')
@php
    $formattedValues = $attribute->values->map(function($value) {
        $trans = [];
        foreach ($value->translations as $t) {
            $trans[$t->locale] = ['name' => $t->name];
        }
        return [
            'id' => $value->id,
            'translations' => $trans,
            'color_code' => $value->color_code ?? '#000000'
        ];
    })->values();
@endphp

<div class="max-w-6xl mx-auto space-y-6" x-data="attributeEditForm()">

    <div class="flex items-center gap-4">
        <a href="{{ route('admin.commerce.attributes.index') }}" class="p-2 rounded-lg hover:bg-gray-100 transition text-gray-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Edit Attribute') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('Update details for') }}: <span class="font-semibold text-gray-900">{{ $attribute->translation()?->name }}</span></p>
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

    <form method="POST" action="{{ route('admin.commerce.attributes.update', $attribute) }}" class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        @csrf
        @method('PUT')

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
                        @php $trans = $attribute->translations->firstWhere('locale', $locale); @endphp
                        <div x-show="activeTab === '{{ $locale }}'" x-cloak class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Attribute Name') }} ({{ strtoupper($locale) }}) <span class="text-red-500">*</span></label>
                                <input type="text" name="translations[{{ $locale }}][name]" value="{{ old("translations.$locale.name", $trans?->name) }}" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h2 class="text-lg font-semibold text-gray-900">{{ __('Attribute Values') }}</h2>
                    <button type="button" @click="addValue()" class="text-sm font-medium text-rj-purple hover:text-rj-pink transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        {{ __('Add Value') }}
                    </button>
                </div>

                <div class="space-y-4">
                    <template x-for="(value, index) in values" :key="index">
                        <div class="p-4 rounded-xl border border-gray-200 bg-gray-50 relative">
                            <button type="button" @click="removeValue(index)" class="absolute top-3 right-3 p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                            </button>

                            <input type="hidden" :name="'values[' + index + '][id]'" x-model="value.id">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pr-10">
                                <div class="space-y-3">
                                    <template x-for="locale in locales" :key="locale">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1" x-text="'Name (' + locale.toUpperCase() + ')'"></label>
                                            <input type="text" :name="'values[' + index + '][translations][' + locale + '][name]'" x-model="value.translations[locale].name" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm" placeholder="Value name...">
                                        </div>
                                    </template>
                                </div>

                                <div x-show="attributeType === 'color'" class="flex flex-col justify-center">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('Color Code') }}</label>
                                    <div class="flex items-center gap-3">
                                        <input type="color" :name="'values[' + index + '][color_code]'" x-model="value.color_code" class="w-10 h-10 rounded cursor-pointer border-0 p-0">
                                        <input type="text" x-model="value.color_code" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono" placeholder="#000000">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div x-show="values.length === 0" class="text-center py-8 border-2 border-dashed border-gray-200 rounded-xl">
                        <p class="text-sm text-gray-500">{{ __('No values added yet. Click "Add Value" to start.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-3">{{ __('Settings') }}</h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Attribute Type') }}</label>
                    <select name="type" x-model="attributeType" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        <option value="select">{{ __('Dropdown (Select)') }}</option>
                        <option value="color">{{ __('Color (Swatch)') }}</option>
                        <option value="button">{{ __('Button (Bordered)') }}</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-2">{{ __('This determines how customers will see the options on the product page.') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Slug') }}</label>
                    <input type="text" name="slug" value="{{ old('slug', $attribute->slug) }}" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono" required>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <button type="submit" class="w-full inline-flex justify-center items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-rj-purple to-rj-pink text-white font-bold text-sm hover:opacity-90 transition shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    {{ __('Update Attribute') }}
                </button>
                <a href="{{ route('admin.commerce.attributes.index') }}" class="w-full inline-flex justify-center px-6 py-3 rounded-xl bg-gray-100 text-gray-700 font-bold text-sm hover:bg-gray-200 transition">
                    {{ __('Cancel') }}
                </a>
            </div>
        </div>
    </form>
</div>

<script>
function attributeEditForm() {
    return {
        activeTab: '{{ config('app.locale') }}',
        attributeType: '{{ $attribute->type }}',
        locales: @json($locales),
        values: @json($formattedValues),
        init() {
            if (this.values.length === 0) {
                this.addValue();
            } else {
                this.values.forEach(value => {
                    this.locales.forEach(locale => {
                        if (!value.translations[locale]) {
                            value.translations[locale] = { name: '' };
                        }
                    });
                });
            }
        },
        addValue() {
            let trans = {};
            this.locales.forEach(locale => {
                trans[locale] = { name: '' };
            });
            this.values.push({
                id: null,
                translations: trans,
                color_code: '#000000'
            });
        },
        removeValue(index) {
            this.values.splice(index, 1);
        }
    }
}
</script>
@endsection
