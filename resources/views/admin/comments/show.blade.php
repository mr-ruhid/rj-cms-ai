@extends('admin.layouts.app')

@section('title', __('Comment Details'))

@section('content')
@php
    $statusColors = [
        'approved' => ['bg-green-50 text-green-700 border-green-200', 'text-green-600'],
        'pending' => ['bg-amber-50 text-amber-700 border-amber-200', 'text-amber-600'],
        'spam' => ['bg-red-50 text-red-700 border-red-200', 'text-red-600'],
        'trash' => ['bg-gray-100 text-gray-600 border-gray-200', 'text-gray-600'],
    ];
    [$badgeClass, $dotColor] = $statusColors[$comment->status] ?? ['bg-gray-100 text-gray-600 border-gray-200', 'text-gray-600'];
@endphp

<div class="space-y-6" x-data="commentShow()" x-init="init()">

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('admin.comments.index') }}" class="text-gray-400 hover:text-gray-600 transition flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div class="min-w-0">
                <h2 class="text-base font-semibold text-gray-900">{{ __('Comment Details') }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ __('ID: :id · :time', ['id' => $comment->id, 'time' => $comment->created_at->diffForHumans()]) }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if ($comment->status !== 'approved')
                <form method="POST" action="{{ route('admin.comments.approve', $comment) }}" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-green-600 text-white text-sm font-medium hover:bg-green-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        {{ __('Approve') }}
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.comments.unapprove', $comment) }}" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        {{ __('Unapprove') }}
                    </button>
                </form>
            @endif

            @if ($comment->status !== 'spam' && $comment->status !== 'trash')
                <form method="POST" action="{{ route('admin.comments.spam-action', $comment) }}" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-white border border-red-300 text-red-700 text-sm font-medium hover:bg-red-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        {{ __('Spam') }}
                    </button>
                </form>
            @endif

            @if ($comment->status !== 'trash')
                <form method="POST" action="{{ route('admin.comments.trash-action', $comment) }}" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                        {{ __('Trash') }}
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.comments.restore', $comment) }}" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-white border border-green-300 text-green-700 text-sm font-medium hover:bg-green-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        {{ __('Restore') }}
                    </button>
                </form>
            @endif

            <form method="POST" action="{{ route('admin.comments.destroy', $comment) }}"
                  onsubmit="return confirm('{{ __('Permanently delete this comment?') }}')" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white border border-gray-300 text-gray-500 hover:bg-red-50 hover:text-red-600 hover:border-red-300 transition" title="{{ __('Delete') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                </button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded-r-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-r-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between flex-wrap gap-3">
                    <div class="flex items-center gap-3">
                        <img src="{{ $comment->avatar_url }}" alt="" class="w-12 h-12 rounded-full border border-gray-200">
                        <div>
                            <div class="text-sm font-semibold text-gray-900">{{ $comment->author_name ?: __('Anonymous') }}</div>
                            @if ($comment->author_email)
                                <a href="mailto:{{ $comment->author_email }}" class="text-xs text-gray-500 hover:text-rj-purple">
                                    {{ $comment->author_email }}
                                </a>
                            @endif
                        </div>
                    </div>

                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border {{ $badgeClass }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }} bg-current"></span>
                        {{ $comment->statusLabel() }}
                    </span>
                </div>

                <div class="p-6">
                    @if ($comment->isReply())
                        <div class="text-xs text-gray-500 mb-3 inline-flex items-center gap-1 px-2 py-1 rounded bg-gray-50">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                            {{ __('Reply to') }} <b>{{ $comment->parent?->author_name ?: __('Anonymous') }}</b>
                        </div>
                    @endif

                    <div class="text-sm text-gray-800 leading-relaxed whitespace-pre-wrap">{{ $comment->content }}</div>

                    @if ($comment->author_url)
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <div class="text-xs text-gray-500">
                                {{ __('Website') }}:
                                <a href="{{ $comment->author_url }}" target="_blank" rel="nofollow noopener" class="text-rj-purple hover:underline ml-1">{{ $comment->author_url }}</a>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between flex-wrap gap-3 text-xs text-gray-500">
                    <div>
                        {{ __('Posted') }} {{ $comment->created_at->format('M d, Y H:i') }}
                        @if ($comment->author_ip)
                            · <span class="font-mono">{{ $comment->author_ip }}</span>
                        @endif
                    </div>

                    @if ($comment->post)
                        <div>
                            {{ __('On') }}:
                            <a href="{{ route('admin.posts.edit', $comment->post) }}" class="text-rj-purple hover:underline">{{ $comment->post->title }}</a>
                        </div>
                    @endif
                </div>
            </div>

            @if ($comment->children->isNotEmpty())
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Replies') }}</h3>
                        <span class="text-xs text-gray-500">{{ $comment->children->count() }}</span>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @foreach ($comment->children as $reply)
                            <div class="px-6 py-4 flex items-start gap-3">
                                <img src="{{ $reply->avatar_url }}" alt="" class="w-9 h-9 rounded-full border border-gray-200 flex-shrink-0">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-sm font-medium text-gray-900">{{ $reply->author_name ?: __('Anonymous') }}</span>
                                        <span class="text-xs text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>

                                        @php
                                            $replyColors = [
                                                'approved' => 'bg-green-50 text-green-700',
                                                'pending' => 'bg-amber-50 text-amber-700',
                                                'spam' => 'bg-red-50 text-red-700',
                                                'trash' => 'bg-gray-100 text-gray-600',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $replyColors[$reply->status] ?? 'bg-gray-100 text-gray-600' }}">
                                            {{ $reply->statusLabel() }}
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-700 mt-1.5 whitespace-pre-wrap">{{ $reply->content }}</div>

                                    <div class="mt-2 flex items-center gap-2">
                                        <a href="{{ route('admin.comments.show', $reply) }}" class="text-xs text-rj-purple hover:underline">{{ __('View') }}</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">{{ __('Edit Comment') }}</h3>
                    <p class="text-xs text-gray-500 mt-1">{{ __('Modify the comment content and author details.') }}</p>
                </div>

                <form method="POST" action="{{ route('admin.comments.update', $comment) }}" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Author Name') }}</label>
                            <input type="text" name="author_name" value="{{ old('author_name', $comment->author_name) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Author Email') }}</label>
                            <input type="email" name="author_email" value="{{ old('author_email', $comment->author_email) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Website URL') }}</label>
                        <input type="url" name="author_url" value="{{ old('author_url', $comment->author_url) }}"
                               placeholder="https://"
                               class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Content') }}</label>
                        <textarea name="content" rows="5"
                                  class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">{{ old('content', $comment->content) }}</textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            {{ __('Save Changes') }}
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <div class="space-y-6">

            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center text-white">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2l1.09 3.26L16 6.27l-2.91.97L12 10.5l-1.09-3.26L8 6.27l2.91-1.01L12 2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19 15l.71 2.13L22 17.7l-2.29.71L19 20.5l-.71-2.09L16 17.7l2.29-.57L19 15z"/><path stroke-linecap="round" stroke-linejoin="round" d="M5 14l.71 2.13L8 16.7l-2.29.71L5 19.5l-.71-2.09L2 16.7l2.29-.57L5 14z"/></svg>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900">{{ __('AI Analysis') }}</h3>
                    </div>

                    <button type="button" @click="analyze()" :disabled="analyzing || !aiEnabled"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-xs font-medium hover:opacity-90 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg x-show="!analyzing" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        <svg x-show="analyzing" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                        <span x-text="analyzing ? '{{ __('Analyzing...') }}' : (analysis ? '{{ __('Re-analyze') }}' : '{{ __('Analyze') }}')"></span>
                    </button>
                </div>

                <div class="p-6">
                    <template x-if="!aiEnabled">
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-xs text-amber-900">
                            {{ __('AI is not configured. Go to Settings → AI.') }}
                        </div>
                    </template>

                    <template x-if="aiEnabled && !analysis && !analyzing">
                        <div class="text-xs text-gray-500 text-center py-6">
                            {{ __('Click "Analyze" to run AI spam and sentiment detection.') }}
                        </div>
                    </template>

                    <template x-if="analyzing">
                        <div class="text-center py-8">
                            <svg class="w-8 h-8 mx-auto text-rj-purple animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                            <p class="text-xs text-gray-500 mt-3">{{ __('AI is analyzing the comment...') }}</p>
                        </div>
                    </template>

                    <template x-if="analysis">
                        <div class="space-y-4">

                            <div class="rounded-lg p-4 border-2 text-center"
                                 :class="analysis.is_spam
                                    ? 'bg-red-50 border-red-200'
                                    : (analysis.spam_score > 40 ? 'bg-amber-50 border-amber-200' : 'bg-green-50 border-green-200')">

                                <div class="text-xs font-semibold uppercase tracking-wide"
                                     :class="analysis.is_spam ? 'text-red-700' : (analysis.spam_score > 40 ? 'text-amber-700' : 'text-green-700')">
                                    <span x-text="analysis.is_spam ? '{{ __('SPAM') }}' : (analysis.spam_score > 40 ? '{{ __('SUSPICIOUS') }}' : '{{ __('LEGITIMATE') }}')"></span>
                                </div>

                                <div class="text-3xl font-bold mt-1"
                                     :class="analysis.is_spam ? 'text-red-600' : (analysis.spam_score > 40 ? 'text-amber-600' : 'text-green-600')">
                                    <span x-text="analysis.spam_score"></span><span class="text-lg opacity-50">%</span>
                                </div>

                                <div class="text-xs mt-1"
                                     :class="analysis.is_spam ? 'text-red-700' : (analysis.spam_score > 40 ? 'text-amber-700' : 'text-green-700')">
                                    {{ __('spam score') }}
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-gray-50 rounded-lg p-3 text-center">
                                    <div class="text-xs text-gray-500 uppercase">{{ __('Sentiment') }}</div>
                                    <div class="text-sm font-bold mt-1"
                                         :class="{
                                            'text-green-600': analysis.sentiment === 'positive',
                                            'text-gray-700': analysis.sentiment === 'neutral',
                                            'text-red-600': analysis.sentiment === 'negative'
                                         }"
                                         x-text="analysis.sentiment.charAt(0).toUpperCase() + analysis.sentiment.slice(1)"></div>
                                </div>

                                <div class="bg-gray-50 rounded-lg p-3 text-center">
                                    <div class="text-xs text-gray-500 uppercase">{{ __('Toxicity') }}</div>
                                    <div class="text-sm font-bold mt-1"
                                         :class="analysis.toxicity > 60 ? 'text-red-600' : (analysis.toxicity > 30 ? 'text-amber-600' : 'text-green-600')">
                                        <span x-text="analysis.toxicity"></span>%
                                    </div>
                                </div>
                            </div>

                            <template x-if="analysis.reason">
                                <div class="border-t border-gray-100 pt-3">
                                    <div class="text-xs font-medium text-gray-700 mb-1">{{ __('Reason') }}</div>
                                    <div class="text-xs text-gray-600 leading-relaxed" x-text="analysis.reason"></div>
                                </div>
                            </template>

                            <template x-if="analysis.flags && analysis.flags.length > 0">
                                <div class="border-t border-gray-100 pt-3">
                                    <div class="text-xs font-medium text-gray-700 mb-2">{{ __('Flags') }}</div>
                                    <div class="flex flex-wrap gap-1.5">
                                        <template x-for="flag in analysis.flags" :key="flag">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-red-50 text-red-700 text-xs font-medium border border-red-100"
                                                  x-text="flag"></span>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <template x-if="analysis.is_spam && {{ $comment->status === 'spam' ? 'false' : 'true' }}">
                                <div class="border-t border-gray-100 pt-3">
                                    <form method="POST" action="{{ route('admin.comments.spam-action', $comment) }}">
                                        @csrf
                                        <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-red-600 text-white text-xs font-medium hover:bg-red-700 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                            {{ __('Mark as Spam') }}
                                        </button>
                                    </form>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('Actions') }}</h3>
                </div>

                <div class="p-4 space-y-2">
                    <form method="POST" action="{{ route('admin.comments.bulk') }}">
                        @csrf
                        <input type="hidden" name="ids[]" value="{{ $comment->id }}">
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="w-full inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition text-left">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            {{ __('Approve') }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.comments.bulk') }}">
                        @csrf
                        <input type="hidden" name="ids[]" value="{{ $comment->id }}">
                        <input type="hidden" name="action" value="spam">
                        <button type="submit" class="w-full inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition text-left">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            {{ __('Mark as Spam') }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.comments.bulk') }}">
                        @csrf
                        <input type="hidden" name="ids[]" value="{{ $comment->id }}">
                        <input type="hidden" name="action" value="trash">
                        <button type="submit" class="w-full inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition text-left">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                            {{ __('Move to Trash') }}
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </div>

</div>

<script>
function commentShow() {
    return {
        aiEnabled: false,
        analyzing: false,
        analysis: null,

        async init() {
            try {
                const res = await fetch('{{ route('admin.comments.ai.status') }}', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                this.aiEnabled = !!data.enabled;
            } catch (e) {
                this.aiEnabled = false;
            }
        },

        async analyze() {
            if (this.analyzing) return;

            this.analyzing = true;
            this.analysis = null;

            try {
                const csrf = document.querySelector('meta[name="csrf-token"]').content;

                const res = await fetch('{{ route('admin.comments.ai.analyze', $comment) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                });

                const data = await res.json();

                if (data.success && data.data) {
                    this.analysis = data.data;
                } else {
                    alert(data.message || '{{ __('Analysis failed.') }}');
                }
            } catch (e) {
                alert('{{ __('Network error.') }}');
            } finally {
                this.analyzing = false;
            }
        },
    };
}
</script>
@endsection