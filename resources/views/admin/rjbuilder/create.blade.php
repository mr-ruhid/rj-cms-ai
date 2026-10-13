@extends('admin.layouts.app')

@section('title', __('Create Template'))

@section('content')
<div class="space-y-6 max-w-3xl">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.rjbuilder.index', ['type' => $type]) }}" class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h2 class="text-base font-semibold text-gray-900">{{ __('Create Template') }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('Name your template and choose what you want to build.') }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.rjbuilder.store') }}" class="space-y-6" x-data="{ selectedType: '{{ $type }}' }">
        @csrf

        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-900">{{ __('Template Name') }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ __('A descriptive name to help you find it later.') }}</p>
            </div>

            <div class="p-6">
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                       placeholder="{{ __('e.g. Main Header, Blog Footer, Contact Page') }}"
                       class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                @error('name')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-900">{{ __('Template Type') }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ __('What kind of template do you want to build?') }}</p>
            </div>

            <div class="p-6">
                <input type="hidden" name="type" x-model="selectedType">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach (\App\Models\Template::types() as $key => $meta)
                        <button type="button"
                                @click="selectedType = '{{ $key }}'"
                                :class="selectedType === '{{ $key }}' ? 'border-rj-purple bg-rj-purple/5 ring-2 ring-rj-purple/20' : 'border-gray-200 hover:border-gray-300'"
                                class="text-left p-4 rounded-xl border-2 transition">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 transition"
                                     :class="selectedType === '{{ $key }}' ? 'bg-gradient-to-br from-rj-purple to-rj-pink text-white' : 'bg-gray-100 text-gray-500'">
                                    @if ($key === 'header')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                                    @elseif ($key === 'footer')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h10M4 18h7"/></svg>
                                    @elseif ($key === 'page')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    @elseif ($key === 'popup')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/></svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-medium text-gray-900">{{ $meta['label'] }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        @if ($key === 'header')
                                            {{ __('Site top navigation and branding') }}
                                        @elseif ($key === 'footer')
                                            {{ __('Site bottom links and copyright') }}
                                        @elseif ($key === 'page')
                                            {{ __('Full page layout with content') }}
                                        @elseif ($key === 'popup')
                                            {{ __('Modal window shown on conditions') }}
                                        @else
                                            {{ __('Reusable content block') }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                {{ __('Create & Open Builder') }}
            </button>
            <a href="{{ route('admin.rjbuilder.index', ['type' => $type]) }}" class="text-sm text-gray-600 hover:text-gray-900">
                {{ __('Cancel') }}
            </a>
        </div>

    </form>

</div>
@endsection