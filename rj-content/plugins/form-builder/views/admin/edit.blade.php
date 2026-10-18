@extends('admin.layouts.app')

@section('title', __('Edit Form'))

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ fields: @js($form->fields ?? []) }">

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.plugin.forms.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h2 class="text-base font-semibold text-gray-900">{{ __('Edit Form') }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $form->name }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.plugin.forms.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                {{ __('Cancel') }}
            </a>
            <button type="submit" form="edit-form" class="inline-flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                {{ __('Save Form') }}
            </button>
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-r-lg">
            <ul class="text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="edit-form" method="POST" action="{{ route('admin.plugin.forms.update', $form) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <input type="hidden" name="fields" :value="JSON.stringify(fields)">

        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-900">{{ __('Form Details') }}</h3>
            </div>

            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ __('Name') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $form->name) }}" required
                               class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Slug') }}</label>
                        <input type="text" name="slug" value="{{ old('slug', $form->slug) }}"
                               class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono lowercase">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Description') }}</label>
                    <textarea name="description" rows="2"
                              class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">{{ old('description', $form->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Status') }}</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        <option value="active" {{ old('status', $form->status) === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="inactive" {{ old('status', $form->status) === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">{{ __('Form Fields') }}</h3>
                <button type="button" @click="fields.push({ type: 'text', name: '', label: '', placeholder: '', is_required: false })"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-xs font-medium text-gray-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    {{ __('Add Field') }}
                </button>
            </div>

            <div class="p-6 space-y-4">
                <template x-if="fields.length === 0">
                    <div class="text-center py-8 text-sm text-gray-500">
                        {{ __('No fields added yet. Click "Add Field" to start building your form.') }}
                    </div>
                </template>

                <template x-for="(field, index) in fields" :key="index">
                    <div class="p-4 border border-gray-200 rounded-lg bg-gray-50 relative group">
                        <button type="button" @click="fields.splice(index, 1)"
                                class="absolute top-2 right-2 w-6 h-6 rounded-md bg-red-100 text-red-600 flex items-center justify-center opacity-0 group-hover:opacity-100 transition hover:bg-red-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('Label') }}</label>
                                <input type="text" x-model="field.label" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm" placeholder="{{ __('e.g. Full Name') }}">
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('Name (ID)') }}</label>
                                <input type="text" x-model="field.name" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono" placeholder="{{ __('e.g. full_name') }}">
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('Type') }}</label>
                                <select x-model="field.type" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                                    <option value="text">Text</option>
                                    <option value="email">Email</option>
                                    <option value="tel">Phone</option>
                                    <option value="textarea">Textarea</option>
                                    <option value="select">Select</option>
                                    <option value="checkbox">Checkbox</option>
                                </select>
                            </div>

                            <div class="flex items-center pt-5">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" x-model="field.is_required" class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                                    <span class="text-sm text-gray-700">{{ __('Required') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </form>

</div>
@endsection
