@extends('admin.layouts.app')

@section('title', __('View Post'))

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('admin.posts.index', ['type' => $post->type]) }}" class="text-gray-400 hover:text-gray-600 transition flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div class="min-w-0">
                <h2 class="text-base font-semibold text-gray-900 truncate">{{ $post->title ?: __('(no title)') }}</h2>
                <div class="flex items-center gap-2 mt-1 flex-wrap">
                    @if ($post->status === 'published')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-green-50 text-green-700 text-xs font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                            {{ __('Published') }}
                        </span>
                    @elseif ($post->status === 'draft')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 text-xs font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            {{ __('Draft') }}
                        </span>
                    @elseif ($post->status === 'scheduled')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-xs font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            {{ __('Scheduled') }}
                        </span>
                    @elseif ($post->status === 'trash')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-50 text-red-700 text-xs font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                            {{ __('Trash') }}
                        </span>
                    @endif

                    @if ($post->is_sticky)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 text-xs font-medium">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            {{ __('Sticky') }}
                        </span>
                    @endif

                    <span class="text-xs text-gray-500">·</span>
                    <code class="text-xs text-gray-500 font-mono">/{{ $post->slug }}</code>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if ($post->status === 'published')
                <a href="{{ url('/blog/' . $post->slug) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    {{ __('View Site') }}
                </a>
            @endif

            <a href="{{ route('admin.posts.edit', $post) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                {{ __('Edit') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                @if ($post->featured_image)
                    <img src="{{ Str::startsWith($post->featured_image, 'http') ? $post->featured_image : asset($post->featured_image) }}"
                         alt="{{ $post->title }}" class="w-full h-64 object-cover">
                @endif

                <div class="p-6">
                    <div class="text-sm text-gray-700 leading-relaxed rj-prose" style="word-break: break-word;">
                        {!! $post->content ?: '<p class="text-gray-400 italic">' . __('No content yet.') . '</p>' !!}
                    </div>
                </div>
            </div>

            @if ($post->excerpt)
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">{{ __('Excerpt') }}</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $post->excerpt }}</p>
                </div>
            @endif

            @if ($post->translations->isNotEmpty())
                <div class="bg-white rounded-lg shadow-sm border border-gray-100" x-data="{ activeLocale: '{{ $post->translations->first()->locale }}' }">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Translations') }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ __('Other language versions of this post.') }}</p>
                    </div>

                    <div class="border-b border-gray-100 px-6">
                        <nav class="flex gap-1 -mb-px overflow-x-auto rj-scrollbar">
                            @foreach ($post->translations as $t)
                                <button type="button"
                                        @click="activeLocale = '{{ $t->locale }}'"
                                        :class="activeLocale === '{{ $t->locale }}' ? 'border-rj-purple text-rj-purple' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                        class="px-4 py-2.5 border-b-2 text-sm font-medium transition flex items-center gap-2 whitespace-nowrap">
                                    @if (file_exists(public_path("images/flags/{$t->locale}.svg")))
                                        <img src="{{ asset("images/flags/{$t->locale}.svg") }}" alt="{{ strtoupper($t->locale) }}" class="w-4 h-3 object-cover rounded-sm">
                                    @endif
                                    {{ rj_lang_name($t->locale) }}
                                    <span class="text-xs uppercase opacity-60">{{ $t->locale }}</span>
                                </button>
                            @endforeach
                        </nav>
                    </div>

                    <div class="p-6 space-y-4">
                        @foreach ($post->translations as $t)
                            <div x-show="activeLocale === '{{ $t->locale }}'" x-cloak class="space-y-4">
                                <div>
                                    <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">{{ __('Title') }}</div>
                                    <div class="text-sm font-medium text-gray-900">{{ $t->title ?: '—' }}</div>
                                </div>

                                @if ($t->excerpt)
                                    <div>
                                        <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">{{ __('Excerpt') }}</div>
                                        <p class="text-sm text-gray-700 leading-relaxed">{{ $t->excerpt }}</p>
                                    </div>
                                @endif

                                @if ($t->content)
                                    <div>
                                        <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">{{ __('Content') }}</div>
                                        <div class="text-sm text-gray-700 leading-relaxed max-h-64 overflow-y-auto rj-scrollbar border border-gray-100 rounded-lg p-4 bg-gray-50">
                                            {!! $t->content !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        <div class="space-y-6">

            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">{{ __('Details') }}</h3>
                </div>

                <div class="p-6 space-y-4 text-sm">

                    <div class="flex items-start justify-between gap-3">
                        <span class="text-gray-500">{{ __('Author') }}</span>
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center text-white text-xs font-semibold">
                                {{ strtoupper(substr($post->author->name ?? '?', 0, 1)) }}
                            </div>
                            <span class="text-gray-900 font-medium">{{ $post->author->name ?? '—' }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-3">
                        <span class="text-gray-500">{{ __('Type') }}</span>
                        <span class="text-gray-900 font-medium">{{ $post->typeLabel() }}</span>
                    </div>

                    <div class="flex items-center justify-between gap-3">
                        <span class="text-gray-500">{{ __('Status') }}</span>
                        <span class="text-gray-900 font-medium">{{ $post->statusLabel() }}</span>
                    </div>

                    <div class="flex items-center justify-between gap-3">
                        <span class="text-gray-500">{{ __('Comments') }}</span>
                        <span class="text-gray-900 font-medium">{{ $post->allow_comments ? __('Allowed') : __('Disabled') }}</span>
                    </div>

                    <div class="flex items-center justify-between gap-3">
                        <span class="text-gray-500">{{ __('Views') }}</span>
                        <span class="text-gray-900 font-medium">{{ number_format($post->view_count) }}</span>
                    </div>

                    <div class="border-t border-gray-100 pt-4 space-y-3">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-gray-500">{{ __('Created') }}</span>
                            <span class="text-gray-900">{{ $post->created_at->format('M d, Y H:i') }}</span>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <span class="text-gray-500">{{ __('Updated') }}</span>
                            <span class="text-gray-900">{{ $post->updated_at->format('M d, Y H:i') }}</span>
                        </div>

                        @if ($post->published_at)
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-gray-500">{{ __('Published') }}</span>
                                <span class="text-gray-900">{{ $post->published_at->format('M d, Y H:i') }}</span>
                            </div>
                        @endif
                    </div>

                </div>
            </div>

            @if ($post->categories->isNotEmpty())
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Categories') }}</h3>
                    </div>
                    <div class="p-6 flex flex-wrap gap-2">
                        @foreach ($post->categories as $cat)
                            <a href="{{ route('admin.posts.categories.edit', $cat) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rj-purple/10 text-rj-purple text-xs font-medium hover:bg-rj-purple/20 transition">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                {{ $cat->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($post->tags->isNotEmpty())
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Tags') }}</h3>
                    </div>
                    <div class="p-6 flex flex-wrap gap-2">
                        @foreach ($post->tags as $tag)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-gray-100 text-gray-700 text-xs font-medium">
                                #{{ $tag->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($post->template)
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Template') }}</h3>
                    </div>
                    <div class="p-6">
                        <a href="{{ route('admin.rjbuilder.edit', $post->template) }}"
                           class="inline-flex items-center gap-2 text-sm text-rj-purple hover:underline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>
                            {{ $post->template->name }}
                        </a>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">{{ __('Actions') }}</h3>
                </div>
                <div class="p-6 space-y-2">

                    <form method="POST" action="{{ route('admin.posts.duplicate', $post) }}">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            {{ __('Duplicate') }}
                        </button>
                    </form>

                    @if ($post->status === 'published')
                        <form method="POST" action="{{ route('admin.posts.unpublish', $post) }}">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                {{ __('Unpublish') }}
                            </button>
                        </form>
                    @elseif ($post->status !== 'trash')
                        <form method="POST" action="{{ route('admin.posts.publish', $post) }}">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                {{ __('Publish') }}
                            </button>
                        </form>
                    @endif

                    @if ($post->status === 'trash')
                        <form method="POST" action="{{ route('admin.posts.restore', $post) }}">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-green-700 hover:bg-green-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                {{ __('Restore') }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.posts.destroy', $post) }}"
                              onsubmit="return confirm('{{ __('Permanently delete this post?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-red-600 hover:bg-red-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                {{ __('Delete Permanently') }}
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.posts.trash-action', $post) }}"
                              onsubmit="return confirm('{{ __('Move this post to trash?') }}')">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-red-600 hover:bg-red-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                {{ __('Move to Trash') }}
                            </button>
                        </form>
                    @endif

                </div>
            </div>

        </div>

    </div>

</div>
@endsection
