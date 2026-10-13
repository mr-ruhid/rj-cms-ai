@extends('admin.layouts.app')

@section('title', __('RJBuilder'))

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-base font-semibold text-gray-900">{{ __('RJBuilder') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Design headers, footers, pages, popups and reusable blocks.') }}</p>
        </div>

        <a href="{{ route('admin.rjbuilder.create', ['type' => $type]) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            {{ __('Add New') }}
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">

        <div class="border-b border-gray-100 px-4 overflow-x-auto rj-scrollbar">
            <nav class="flex gap-1 -mb-px">
                @foreach (\App\Models\Template::types() as $key => $meta)
                    <a href="{{ route('admin.rjbuilder.index', ['type' => $key]) }}"
                       class="px-4 py-3 border-b-2 text-sm font-medium transition flex items-center gap-2 whitespace-nowrap {{ $type === $key ? 'border-rj-purple text-rj-purple' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        {{ $meta['label'] }}
                        <span class="inline-flex items-center justify-center min-w-[20px] px-1.5 py-0.5 rounded-full text-[10px] font-medium {{ $type === $key ? 'bg-rj-purple/10 text-rj-purple' : 'bg-gray-100 text-gray-500' }}">
                            {{ $counts[$key] ?? 0 }}
                        </span>
                    </a>
                @endforeach
            </nav>
        </div>

        <div class="px-4 py-3 border-b border-gray-100">
            <form method="GET" action="{{ route('admin.rjbuilder.index') }}" class="flex items-center gap-3">
                <input type="hidden" name="type" value="{{ $type }}">
                <div class="relative flex-1 max-w-md">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="{{ __('Search templates...') }}"
                           class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                </div>
                @if (request('search'))
                    <a href="{{ route('admin.rjbuilder.index', ['type' => $type]) }}" class="text-xs text-gray-500 hover:text-gray-700">
                        {{ __('Clear') }}
                    </a>
                @endif
            </form>
        </div>

        @if ($templates->isEmpty())
            <div class="p-16 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                <p class="mt-4 text-sm text-gray-500">
                    @if (request('search'))
                        {{ __('No templates match your search.') }}
                    @else
                        {{ __('No :type templates yet.', ['type' => strtolower(\App\Models\Template::types()[$type]['label'])]) }}
                    @endif
                </p>
                <a href="{{ route('admin.rjbuilder.create', ['type' => $type]) }}"
                   class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    {{ __('Create First Template') }}
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-4">
                @foreach ($templates as $template)
                    <div class="bg-white rounded-xl border border-gray-200 hover:border-rj-purple/40 transition overflow-hidden group">

                        <div class="h-36 bg-gradient-to-br from-rj-dark-1 via-rj-dark-2 to-rj-dark-3 relative overflow-hidden">
                            <div class="absolute inset-0 flex items-center justify-center text-white/20">
                                @if ($template->type === 'header')
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                                @elseif ($template->type === 'footer')
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h10M4 18h7"/></svg>
                                @elseif ($template->type === 'page')
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                @elseif ($template->type === 'popup')
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/></svg>
                                @else
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>
                                @endif
                            </div>

                            @if ($template->is_default)
                                <span class="absolute top-2 left-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-rj-pink/90 text-white text-[10px] font-semibold uppercase tracking-wide">
                                    <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    {{ __('Default') }}
                                </span>
                            @endif

                            <span class="absolute top-2 right-2 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $template->status === 'published' ? 'bg-green-500/90 text-white' : 'bg-gray-500/90 text-white' }} uppercase tracking-wide">
                                {{ $template->status }}
                            </span>
                        </div>

                        <div class="p-4">
                            <div class="flex items-start justify-between gap-2 mb-3">
                                <div class="min-w-0">
                                    <div class="text-sm font-semibold text-gray-900 truncate">{{ $template->name }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5 font-mono truncate">{{ $template->slug }}</div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                                <span>{{ $template->blockCount() }} {{ __('block(s)') }}</span>
                                <span>{{ $template->updated_at->diffForHumans() }}</span>
                            </div>

                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('admin.rjbuilder.edit', $template) }}"
                                   class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-xs font-medium hover:opacity-90 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    {{ __('Open') }}
                                </a>

                                <form method="POST" action="{{ route('admin.rjbuilder.duplicate', $template) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 text-gray-500 hover:bg-gray-50 transition"
                                            title="{{ __('Duplicate') }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    </button>
                                </form>

                                @if (!$template->is_default && $template->status === 'published')
                                    <form method="POST" action="{{ route('admin.rjbuilder.set-default', $template) }}" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 text-gray-500 hover:bg-rj-pink/10 hover:text-rj-pink transition"
                                                title="{{ __('Set as default') }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                        </button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('admin.rjbuilder.destroy', $template) }}"
                                      onsubmit="return confirm('{{ __('Delete this template?') }}')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 text-gray-500 hover:bg-red-50 hover:text-red-600 transition disabled:opacity-30 disabled:cursor-not-allowed"
                                            title="{{ __('Delete') }}"
                                            {{ $template->is_default ? 'disabled' : '' }}>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

            @if ($templates->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">
                    {{ $templates->links() }}
                </div>
            @endif
        @endif

    </div>

</div>
@endsection