@extends('admin.layouts.app')

@section('title', __('Comments'))

@section('content')
@php $commentsEnabled = (bool) rj_get_option('comments_enabled', true); @endphp

<div class="space-y-6" x-data="{ selected: [], bulkAction: '' }">

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-base font-semibold text-gray-900">{{ __('Comments') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Manage comments across your site.') }}</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button"
                    @click="$dispatch('open-ai-panel')"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2l1.09 3.26L16 6.27l-2.91.97L12 10.5l-1.09-3.26L8 6.27l2.91-1.01L12 2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19 15l.71 2.13L22 17.7l-2.29.71L19 20.5l-.71-2.09L16 17.7l2.29-.57L19 15z"/><path stroke-linecap="round" stroke-linejoin="round" d="M5 14l.71 2.13L8 16.7l-2.29.71L5 19.5l-.71-2.09L2 16.7l2.29-.57L5 14z"/></svg>
                {{ __('AI Management') }}
            </button>

            <form method="POST" action="{{ route('admin.comments.toggle-global') }}" class="inline">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition {{ $commentsEnabled ? 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' : 'bg-red-50 border border-red-200 text-red-700 hover:bg-red-100' }}">
                    @if ($commentsEnabled)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        {{ __('Disable Comments') }}
                    @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ __('Enable Comments') }}
                    @endif
                </button>
            </form>
        </div>
    </div>

    @if (!$commentsEnabled)
        <div class="bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 text-sm text-amber-900 flex items-start gap-3">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <strong>{{ __('Comments are disabled globally.') }}</strong>
                <span class="opacity-80">{{ __('No new comments will be accepted on the frontend.') }}</span>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        @php
            $statusCards = [
                ['key' => 'all', 'label' => __('All'), 'value' => $counts['all'], 'color' => 'text-gray-900', 'route' => 'admin.comments.index'],
                ['key' => 'pending', 'label' => __('Pending'), 'value' => $counts['pending'], 'color' => 'text-amber-600', 'route' => 'admin.comments.pending'],
                ['key' => 'approved', 'label' => __('Approved'), 'value' => $counts['approved'], 'color' => 'text-green-600', 'route' => 'admin.comments.approved'],
                ['key' => 'spam', 'label' => __('Spam'), 'value' => $counts['spam'], 'color' => 'text-red-600', 'route' => 'admin.comments.spam'],
                ['key' => 'trash', 'label' => __('Trash'), 'value' => $counts['trash'], 'color' => 'text-gray-600', 'route' => 'admin.comments.trash'],
            ];

            $currentRoute = request()->route()->getName();
        @endphp

        @foreach ($statusCards as $card)
            <a href="{{ route($card['route']) }}"
               class="bg-white rounded-lg shadow-sm border {{ $currentRoute === $card['route'] ? 'border-rj-purple ring-1 ring-rj-purple/20' : 'border-gray-100' }} p-5 hover:border-rj-purple/50 transition">
                <div class="text-xs text-gray-500 uppercase tracking-wide">{{ $card['label'] }}</div>
                <div class="text-2xl font-bold {{ $card['color'] }} mt-1">{{ number_format($card['value']) }}</div>
            </a>
        @endforeach
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">

        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between gap-3 flex-wrap">
            <form method="GET" action="{{ request()->url() }}" class="flex items-center gap-3 flex-1 flex-wrap">
                <div class="relative flex-1 min-w-[200px] max-w-md">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="{{ __('Search comments...') }}"
                           class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                </div>

                <button type="submit" class="px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm font-medium text-gray-700 transition">
                    {{ __('Search') }}
                </button>

                @if (request()->has('q'))
                    <a href="{{ request()->url() }}" class="text-sm text-gray-500 hover:text-gray-900">{{ __('Clear') }}</a>
                @endif
            </form>

            @if ($comments->total() > 0)
                <div class="text-xs text-gray-500">{{ $comments->total() }} {{ __('comment(s)') }}</div>
            @endif
        </div>

        @if ($comments->isEmpty())
            <div class="p-16 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                <p class="mt-4 text-sm text-gray-500">
                    @if (request('q'))
                        {{ __('No comments match your search.') }}
                    @else
                        {{ __('No comments yet.') }}
                    @endif
                </p>
            </div>
        @else
            <form method="POST" action="{{ route('admin.comments.bulk') }}">
                @csrf

                <div x-show="selected.length > 0" x-cloak class="px-4 py-2.5 border-b border-gray-100 bg-rj-purple/5 flex items-center gap-3 flex-wrap">
                    <span class="text-xs text-gray-600"><b x-text="selected.length"></b> {{ __('selected') }}</span>

                    <select x-model="bulkAction" class="rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-xs">
                        <option value="">{{ __('Bulk Actions') }}</option>
                        <option value="approve">{{ __('Approve') }}</option>
                        <option value="unapprove">{{ __('Unapprove') }}</option>
                        <option value="spam">{{ __('Mark as Spam') }}</option>
                        <option value="trash">{{ __('Move to Trash') }}</option>
                        <option value="delete">{{ __('Delete Permanently') }}</option>
                    </select>

                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-gray-900 text-white text-xs font-medium hover:bg-gray-800 transition">
                        {{ __('Apply') }}
                    </button>

                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                    <input type="hidden" name="action" :value="bulkAction">
                </div>

                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="w-10 px-4 py-3">
                                <input type="checkbox"
                                       @change="selected = $event.target.checked ? [{{ $comments->pluck('id')->implode(',') }}] : []"
                                       :checked="selected.length === {{ $comments->count() }} && {{ $comments->count() }} > 0"
                                       class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                            </th>
                            <th class="text-left font-medium text-gray-600 px-4 py-3">{{ __('Author') }}</th>
                            <th class="text-left font-medium text-gray-600 px-4 py-3">{{ __('Comment') }}</th>
                            <th class="text-left font-medium text-gray-600 px-4 py-3 hidden lg:table-cell">{{ __('In Response To') }}</th>
                            <th class="text-center font-medium text-gray-600 px-4 py-3 hidden md:table-cell">{{ __('Status') }}</th>
                            <th class="text-right font-medium text-gray-600 px-4 py-3">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($comments as $comment)
                            @php
                                $statusColors = [
                                    'approved' => 'bg-green-50 text-green-700',
                                    'pending' => 'bg-amber-50 text-amber-700',
                                    'spam' => 'bg-red-50 text-red-700',
                                    'trash' => 'bg-gray-100 text-gray-600',
                                ];
                                $statusClass = $statusColors[$comment->status] ?? 'bg-gray-100 text-gray-600';
                            @endphp

                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-4 py-3 align-top">
                                    <input type="checkbox" :value="{{ $comment->id }}" x-model.number="selected"
                                           class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                                </td>

                                <td class="px-4 py-3 align-top">
                                    <div class="flex items-start gap-3 min-w-0">
                                        <img src="{{ $comment->avatar_url }}" alt="" class="w-10 h-10 rounded-full flex-shrink-0 border border-gray-200">

                                        <div class="min-w-0">
                                            <div class="text-sm font-medium text-gray-900 truncate">
                                                {{ $comment->author_name ?: __('Anonymous') }}
                                            </div>
                                            @if ($comment->author_email)
                                                <a href="mailto:{{ $comment->author_email }}" class="text-xs text-gray-500 hover:text-rj-purple truncate block">
                                                    {{ $comment->author_email }}
                                                </a>
                                            @endif
                                            @if ($comment->author_ip)
                                                <div class="text-xs text-gray-400 mt-0.5 font-mono">{{ $comment->author_ip }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3 align-top max-w-md">
                                    @if ($comment->isReply())
                                        <div class="text-xs text-gray-500 mb-1 inline-flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                            {{ __('Reply to') }} {{ $comment->parent?->author_name ?: __('Anonymous') }}
                                        </div>
                                    @endif

                                    <div class="text-sm text-gray-700 line-clamp-3">
                                        {{ Str::limit($comment->content, 200) }}
                                    </div>

                                    <div class="text-xs text-gray-400 mt-1.5">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </div>
                                </td>

                                <td class="px-4 py-3 align-top hidden lg:table-cell">
                                    @if ($comment->post)
                                        <a href="{{ route('admin.posts.edit', $comment->post) }}"
                                           class="text-sm text-rj-purple hover:underline line-clamp-2">
                                            {{ $comment->post->title }}
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 align-top hidden md:table-cell text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                        {{ $comment->statusLabel() }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 align-top">
                                    <div class="flex items-center justify-end gap-1">

                                        @if ($comment->status !== 'approved')
                                            <form method="POST" action="{{ route('admin.comments.approve', $comment) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:bg-green-50 hover:text-green-600 transition" title="{{ __('Approve') }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.comments.unapprove', $comment) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:bg-amber-50 hover:text-amber-600 transition" title="{{ __('Unapprove') }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </form>
                                        @endif

                                        @if ($comment->status !== 'spam' && $comment->status !== 'trash')
                                            <form method="POST" action="{{ route('admin.comments.spam-action', $comment) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:bg-red-50 hover:text-red-600 transition" title="{{ __('Spam') }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                </button>
                                            </form>
                                        @endif

                                        @if ($comment->status !== 'trash')
                                            <form method="POST" action="{{ route('admin.comments.trash-action', $comment) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition" title="{{ __('Trash') }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.comments.restore', $comment) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:bg-green-50 hover:text-green-600 transition" title="{{ __('Restore') }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('admin.comments.show', $comment) }}"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:bg-rj-purple/10 hover:text-rj-purple transition" title="{{ __('View') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.46 12C3.73 7.94 7.52 5 12 5s8.27 2.94 9.54 7c-1.27 4.06-5.06 7-9.54 7S3.73 16.06 2.46 12z"/></svg>
                                        </a>

                                        <form method="POST" action="{{ route('admin.comments.destroy', $comment) }}"
                                              onsubmit="return confirm('{{ __('Permanently delete this comment?') }}')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:bg-red-50 hover:text-red-600 transition" title="{{ __('Delete') }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </form>

            @if ($comments->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">
                    {{ $comments->links() }}
                </div>
            @endif
        @endif

    </div>

    <div x-data="{
            aiOpen: false,
            aiLoading: false,
            aiStatus: null,
            async loadAiStatus() {
                this.aiLoading = true;
                try {
                    const res = await fetch('{{ route('admin.comments.ai.status') }}', {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    this.aiStatus = await res.json();
                } catch (e) {
                    this.aiStatus = { enabled: false };
                } finally {
                    this.aiLoading = false;
                }
            }
         }"
         @open-ai-panel.window="aiOpen = true; loadAiStatus()"
         x-show="aiOpen" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="aiOpen = false">

        <div class="absolute inset-0 bg-black/50" @click="aiOpen = false"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2l1.09 3.26L16 6.27l-2.91.97L12 10.5l-1.09-3.26L8 6.27l2.91-1.01L12 2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19 15l.71 2.13L22 17.7l-2.29.71L19 20.5l-.71-2.09L16 17.7l2.29-.57L19 15z"/><path stroke-linecap="round" stroke-linejoin="round" d="M5 14l.71 2.13L8 16.7l-2.29.71L5 19.5l-.71-2.09L2 16.7l2.29-.57L5 14z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">{{ __('AI Comment Management') }}</h3>
                        <p class="text-xs text-gray-500 mt-0.5">{{ __('Automate moderation and replies.') }}</p>
                    </div>
                </div>
                <button type="button" @click="aiOpen = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 space-y-3">
                <div x-show="aiLoading" class="text-center py-4">
                    <svg class="w-6 h-6 mx-auto text-rj-purple animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                </div>

                <template x-if="!aiLoading && aiStatus && !aiStatus.enabled">
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-900">
                        {{ __('AI is not configured. Go to Settings → AI to add your OpenRouter API key.') }}
                    </div>
                </template>

                <template x-if="!aiLoading && aiStatus && aiStatus.enabled">
                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-gray-50 rounded-lg p-3 text-center">
                                <div class="text-xs text-gray-500 uppercase">{{ __('Pending') }}</div>
                                <div class="text-xl font-bold text-amber-600 mt-1" x-text="aiStatus.pending_count"></div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3 text-center">
                                <div class="text-xs text-gray-500 uppercase">{{ __('Spam') }}</div>
                                <div class="text-xl font-bold text-red-600 mt-1" x-text="aiStatus.spam_count"></div>
                            </div>
                        </div>

                        <div class="bg-blue-50 border border-blue-100 rounded-lg p-3 text-xs text-blue-800 leading-relaxed">
                            {{ __('AI moderation tools are in development. Coming soon:') }}
                            <ul class="mt-2 ml-4 list-disc space-y-0.5">
                                <li>{{ __('Auto-classify pending comments') }}</li>
                                <li>{{ __('Detect spam with AI') }}</li>
                                <li>{{ __('Suggest replies to comments') }}</li>
                                <li>{{ __('Sentiment analysis') }}</li>
                            </ul>
                        </div>
                    </div>
                </template>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-end gap-3">
                <button type="button" @click="aiOpen = false"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">
                    {{ __('Close') }}
                </button>
            </div>
        </div>
    </div>

</div>
@endsection