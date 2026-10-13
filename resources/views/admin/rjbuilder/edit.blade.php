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

            @if ($template->is_default)
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rj-pink/10 text-rj-pink text-xs font-medium">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    {{ __('Default') }}
                </span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">

            <div class="bg-gradient-to-br from-rj-dark-1 via-rj-dark-2 to-rj-dark-3 rounded-2xl p-8 text-white relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full bg-rj-purple/20 blur-3xl"></div>
                <div class="absolute -bottom-10 -left-10 w-32 h-32 rounded-full bg-rj-pink/20 blur-3xl"></div>

                <div class="relative">
                    <div class="text-xs uppercase tracking-wider text-rj-purple-light font-medium mb-2">
                        {{ __('Ready to build') }}
                    </div>
                    <h3 class="text-2xl font-bold mb-2">{{ $template->name }}</h3>
                    <p class="text-sm text-gray-300 mb-6">
                        {{ __('Open the full-screen builder to design your :type with drag-and-drop widgets.', ['type' => strtolower($template->typeLabel())]) }}
                    </p>

                    <a href="{{ route('admin.rjbuilder.build', $template) }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-white text-gray-900 text-sm font-semibold hover:bg-gray-100 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        {{ __('Open Builder') }}
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.rjbuilder.update', $template) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Template Settings') }}</h3>
                    </div>

                    <div class="p-6 space-y-5">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Name') }}</label>
                            <input type="text" name="name" value="{{ old('name', $template->name) }}" required
                                   class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                            @error('name')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Status') }}</label>
                            <select name="status" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                                <option value="draft" {{ $template->status === 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                                <option value="published" {{ $template->status === 'published' ? 'selected' : '' }}>{{ __('Published') }}</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">{{ __('Only published templates are used on the frontend.') }}</p>
                        </div>

                    </div>

                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center gap-3">
                        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                            {{ __('Save Settings') }}
                        </button>
                    </div>
                </div>

            </form>

        </div>

        <div class="space-y-6">

            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('Details') }}</h3>
                </div>

                <div class="p-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">{{ __('Type') }}</span>
                        <span class="font-medium text-gray-900">{{ $template->typeLabel() }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">{{ __('Blocks') }}</span>
                        <span class="font-medium text-gray-900">{{ $template->blockCount() }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">{{ __('Created') }}</span>
                        <span class="text-gray-900">{{ $template->created_at->format('Y-m-d') }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">{{ __('Updated') }}</span>
                        <span class="text-gray-900">{{ $template->updated_at->diffForHumans() }}</span>
                    </div>

                    @if ($template->author)
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">{{ __('Author') }}</span>
                            <span class="text-gray-900">{{ $template->author->name }}</span>
                        </div>
                    @endif
                </div>
            </div>

            @if (!$template->is_default && $template->status === 'published')
                <form method="POST" action="{{ route('admin.rjbuilder.set-default', $template) }}">
                    @csrf
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border-2 border-rj-pink/30 text-rj-pink text-sm font-medium hover:bg-rj-pink/5 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        {{ __('Set as Default') }}
                    </button>
                </form>
            @endif

            <div class="bg-white rounded-lg shadow-sm border border-red-100">
                <div class="px-6 py-4 border-b border-red-100">
                    <h3 class="text-sm font-semibold text-red-700">{{ __('Danger Zone') }}</h3>
                </div>

                <div class="p-4">
                    @if ($template->is_default)
                        <p class="text-xs text-gray-500 mb-3">{{ __('Cannot delete the default template.') }}</p>
                    @endif

                    <form method="POST" action="{{ route('admin.rjbuilder.destroy', $template) }}"
                          onsubmit="return confirm('{{ __('Delete this template? This cannot be undone.') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                {{ $template->is_default ? 'disabled' : '' }}
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border-2 border-red-200 text-red-600 text-sm font-medium hover:bg-red-50 transition disabled:opacity-40 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                            {{ __('Delete Template') }}
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection