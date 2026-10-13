@extends('admin.layouts.app')

@section('title', __('Edit Media'))

@section('content')
<div class="space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.media.index') }}" class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h2 class="text-base font-semibold text-gray-900">{{ __('Edit Media') }}</h2>
            <p class="text-sm text-gray-500 mt-0.5 truncate">{{ $media->original_name }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">

            <form method="POST" action="{{ route('admin.media.update', $media) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Metadata') }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ __('Information about this file for SEO and accessibility.') }}</p>
                    </div>

                    <div class="p-6 space-y-5">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Title') }}</label>
                            <input type="text" name="title" value="{{ old('title', $media->title) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Alt Text') }}</label>
                            <input type="text" name="alt_text" value="{{ old('alt_text', $media->alt_text) }}"
                                   placeholder="{{ __('Describe the image for screen readers and SEO') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                            <p class="text-xs text-gray-500 mt-1">{{ __('Important for accessibility and search engines.') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Caption') }}</label>
                            <input type="text" name="caption" value="{{ old('caption', $media->caption) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Description') }}</label>
                            <textarea name="description" rows="4"
                                      class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">{{ old('description', $media->description) }}</textarea>
                        </div>

                    </div>

                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center gap-3">
                        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                            {{ __('Save Changes') }}
                        </button>
                        <a href="{{ route('admin.media.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </div>

            </form>

            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">{{ __('File URL') }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ __('Copy this URL to use the file in posts or pages.') }}</p>
                </div>

                <div class="p-6" x-data="{ copied: false }">
                    <div class="flex items-center gap-2">
                        <input type="text" value="{{ $media->url }}" readonly
                               class="flex-1 rounded-lg border-gray-300 bg-gray-50 text-sm font-mono text-gray-700">
                        <button type="button"
                                @click="navigator.clipboard.writeText('{{ $media->url }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-300 text-sm text-gray-700 hover:bg-gray-50 transition whitespace-nowrap">
                            <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            <svg x-show="copied" x-cloak class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span x-text="copied ? '{{ __('Copied!') }}' : '{{ __('Copy') }}'"></span>
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <div class="space-y-6">

            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                <div class="aspect-square bg-gray-50 flex items-center justify-center overflow-hidden">
                    @if ($media->isImage())
                        <img src="{{ $media->url }}" alt="{{ $media->alt_text ?? $media->original_name }}" class="w-full h-full object-contain">
                    @elseif ($media->isVideo())
                        <video src="{{ $media->url }}" controls class="w-full h-full"></video>
                    @elseif ($media->isAudio())
                        <div class="w-full p-6">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                            <audio src="{{ $media->url }}" controls class="w-full"></audio>
                        </div>
                    @else
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    @endif
                </div>

                <div class="px-4 py-3 border-t border-gray-100">
                    <a href="{{ $media->url }}" target="_blank"
                       class="text-xs text-rj-purple hover:underline flex items-center gap-1">
                        {{ __('Open in new tab') }}
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('File Details') }}</h3>
                </div>

                <div class="p-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">{{ __('Type') }}</span>
                        <span class="font-mono text-gray-900 text-xs">{{ $media->mime_type }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">{{ __('Size') }}</span>
                        <span class="font-mono text-gray-900">{{ $media->humanSize() }}</span>
                    </div>

                    @if ($media->width && $media->height)
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">{{ __('Dimensions') }}</span>
                            <span class="font-mono text-gray-900">{{ $media->width }} × {{ $media->height }} px</span>
                        </div>
                    @endif

                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">{{ __('Uploaded') }}</span>
                        <span class="text-gray-900">{{ $media->created_at->format('Y-m-d H:i') }}</span>
                    </div>

                    @if ($media->uploader)
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">{{ __('Uploaded by') }}</span>
                            <span class="text-gray-900">{{ $media->uploader->name }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <form method="POST" action="{{ route('admin.media.destroy', $media) }}"
                  onsubmit="return confirm('{{ __('Delete this file permanently? This cannot be undone.') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border-2 border-red-200 text-red-600 text-sm font-medium hover:bg-red-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                    {{ __('Delete Permanently') }}
                </button>
            </form>

        </div>

    </div>

</div>
@endsection