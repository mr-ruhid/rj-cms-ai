@extends('admin.layouts.app')

@section('title', $template->name)

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.rjbuilder.index', ['type' => $template->type]) }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h2 class="text-base font-semibold text-gray-900">{{ $template->name }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $template->typeLabel() }} · <code class="text-xs font-mono">{{ $template->slug }}</code>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $template->status === 'published' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                {{ ucfirst($template->status) }}
            </span>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-12 text-center">
        <div class="max-w-md mx-auto">
            <div class="w-16 h-16 mx-auto rounded-full bg-rj-purple/10 flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-rj-purple" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-2">{{ __('Builder UI coming next') }}</h3>
            <p class="text-sm text-gray-500">
                {{ __('The full drag-and-drop builder interface will be added in the next step.') }}
            </p>
            <p class="text-xs text-gray-400 mt-4">
                {{ __('Available widgets:') }} <strong>{{ count(\App\Widgets\WidgetRegistry::all()) }}</strong>
            </p>
        </div>
    </div>

</div>
@endsection