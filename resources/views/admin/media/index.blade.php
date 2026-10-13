@extends('admin.layouts.app')

@section('title', __('Media Library'))

@section('content')
<div class="space-y-6" x-data="mediaLibrary()">

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-base font-semibold text-gray-900">{{ __('Media Library') }}</h2>
            <p class="text-sm text-gray-500 mt-1">
                {{ __('Total') }}: <strong>{{ number_format($stats['total']) }}</strong> ·
                {{ __('Images') }}: <strong>{{ number_format($stats['images']) }}</strong> ·
                {{ __('Size') }}: <strong>{{ \App\Models\Media::query()->first()?->humanSize() ? '' : '' }}{{ number_format($stats['size'] / 1048576, 2) }} MB</strong>
            </p>
        </div>

        <a href="{{ route('admin.media.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            {{ __('Upload New') }}
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">

        <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-3 flex-wrap">
            <form method="GET" action="{{ route('admin.media.index') }}" class="flex items-center gap-2 flex-1 min-w-[200px]">
                @if (request('type'))
                    <input type="hidden" name="type" value="{{ request('type') }}">
                @endif
                <div class="relative flex-1">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="{{ __('Search by name, title or alt text...') }}"
                           class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                </div>
                <button type="submit" class="inline-flex items-center px-3 py-2 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-gray-800 transition">
                    {{ __('Search') }}
                </button>
            </form>

            <div class="flex items-center gap-1">
                @php
                    $types = [
                        '' => __('All'),
                        'image' => __('Images'),
                        'video' => __('Videos'),
                        'audio' => __('Audio'),
                        'document' => __('Documents'),
                    ];
                @endphp
                @foreach ($types as $key => $label)
                    <a href="{{ route('admin.media.index', array_filter(['type' => $key, 'search' => request('search')])) }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ request('type', '') === $key ? 'bg-rj-purple text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        <form method="POST" action="{{ route('admin.media.bulk-delete') }}" id="bulkForm">
            @csrf

            <div x-show="selected.length > 0" x-cloak class="px-4 py-2.5 bg-rj-purple/5 border-b border-rj-purple/20 flex items-center justify-between">
                <span class="text-sm text-gray-700">
                    <strong x-text="selected.length"></strong> {{ __('item(s) selected') }}
                </span>
                <button type="submit" onclick="return confirm('{{ __('Delete selected items?') }}')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-600 text-white text-xs font-medium hover:bg-red-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                    {{ __('Delete Selected') }}
                </button>
            </div>

            @if ($media->isEmpty())
                <div class="p-16 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <p class="mt-4 text-sm text-gray-500">{{ __('No media files yet.') }}</p>
                    <a href="{{ route('admin.media.create') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        {{ __('Upload First File') }}
                    </a>
                </div>
            @else
                <div class="p-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    @foreach ($media as $item)
                        <div class="group relative rounded-lg overflow-hidden border border-gray-200 hover:border-rj-purple transition cursor-pointer"
                             @click="toggle({{ $item->id }})"
                             :class="selected.includes({{ $item->id }}) ? 'ring-2 ring-rj-purple' : ''">

                            <input type="checkbox" name="ids[]" value="{{ $item->id }}"
                                   x-model.number="selected"
                                   @click.stop
                                   class="absolute top-2 left-2 z-10 rounded border-gray-300 text-rj-purple focus:ring-rj-purple cursor-pointer">

                            <div class="aspect-square bg-gray-50 flex items-center justify-center overflow-hidden">
                                @if ($item->isImage())
                                    <img src="{{ $item->url }}" alt="{{ $item->alt_text ?? $item->original_name }}" class="w-full h-full object-cover group-hover:scale-105 transition">
                                @elseif ($item->isVideo())
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                @elseif ($item->isAudio())
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                                @else
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                @endif
                            </div>

                            <div class="p-2 bg-white">
                                <div class="text-xs text-gray-900 truncate font-medium" title="{{ $item->original_name }}">{{ $item->original_name }}</div>
                                <div class="text-[10px] text-gray-500 mt-0.5">{{ $item->humanSize() }}</div>
                            </div>

                            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2"
                                 @click.stop>
                                <a href="{{ route('admin.media.edit', $item) }}"
                                   class="w-8 h-8 rounded-full bg-white flex items-center justify-center hover:bg-gray-100 transition"
                                   title="{{ __('Edit') }}">
                                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </a>
                                <a href="{{ $item->url }}" target="_blank"
                                   class="w-8 h-8 rounded-full bg-white flex items-center justify-center hover:bg-gray-100 transition"
                                   title="{{ __('View') }}">
                                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <button type="button"
                                        onclick="if(confirm('{{ __('Delete this file?') }}')) document.getElementById('del-{{ $item->id }}').submit()"
                                        class="w-8 h-8 rounded-full bg-white flex items-center justify-center hover:bg-red-50 transition"
                                        title="{{ __('Delete') }}">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($media->hasPages())
                    <div class="px-4 py-3 border-t border-gray-100">
                        {{ $media->links() }}
                    </div>
                @endif
            @endif
        </form>

    </div>

    @foreach ($media as $item)
        <form method="POST" action="{{ route('admin.media.destroy', $item) }}" id="del-{{ $item->id }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach

</div>

<script>
function mediaLibrary() {
    return {
        selected: [],
        toggle(id) {
            const index = this.selected.indexOf(id);
            if (index === -1) {
                this.selected.push(id);
            } else {
                this.selected.splice(index, 1);
            }
        }
    }
}
</script>
@endsection