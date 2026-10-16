@extends('admin.layouts.app')

@section('title', $type === 'page' ? __('Pages') : __('Posts'))

@section('content')
<div class="space-y-6" x-data="{ selected: [], bulkAction: '' }">

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-base font-semibold text-gray-900">{{ $type === 'page' ? __('Pages') : __('Posts') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Manage your content, drafts, and scheduled items.') }}</p>
        </div>

        <a href="{{ route('admin.posts.create', ['type' => $type]) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            {{ __('Add New') }}
        </a>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        @php
            $statusCards = [
                ['key' => 'all', 'label' => __('All'), 'value' => $counts['all'], 'color' => 'text-gray-900'],
                ['key' => 'published', 'label' => __('Published'), 'value' => $counts['published'], 'color' => 'text-green-600'],
                ['key' => 'draft', 'label' => __('Draft'), 'value' => $counts['draft'], 'color' => 'text-amber-600'],
                ['key' => 'scheduled', 'label' => __('Scheduled'), 'value' => $counts['scheduled'], 'color' => 'text-blue-600'],
                ['key' => 'trash', 'label' => __('Trash'), 'value' => $counts['trash'], 'color' => 'text-red-600'],
            ];
        @endphp

        @foreach ($statusCards as $card)
            <a href="{{ route('admin.posts.index', array_filter(['type' => $type, 'status' => $card['key'], 'category' => request('category'), 'tag' => request('tag'), 'q' => request('q')])) }}"
               class="bg-white rounded-lg shadow-sm border {{ $status === $card['key'] ? 'border-rj-purple ring-1 ring-rj-purple/20' : 'border-gray-100' }} p-5 hover:border-rj-purple/50 transition">
                <div class="text-xs text-gray-500 uppercase tracking-wide">{{ $card['label'] }}</div>
                <div class="text-2xl font-bold {{ $card['color'] }} mt-1">{{ number_format($card['value']) }}</div>
            </a>
        @endforeach
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">

        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between gap-3 flex-wrap">
            <form method="GET" action="{{ route('admin.posts.index') }}" class="flex items-center gap-3 flex-1 min-w-[200px] flex-wrap">
                <input type="hidden" name="type" value="{{ $type }}">
                <input type="hidden" name="status" value="{{ $status }}">

                <div class="relative flex-1 min-w-[200px] max-w-md">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="{{ __('Search posts...') }}"
                           class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                </div>

                <select name="category" class="rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    <option value="">{{ __('All Categories') }}</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>

                <select name="tag" class="rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    <option value="">{{ __('All Tags') }}</option>
                    @foreach ($tags as $tag)
                        <option value="{{ $tag->id }}" {{ request('tag') == $tag->id ? 'selected' : '' }}>{{ $tag->name }}</option>
                    @endforeach
                </select>

                <button type="submit" class="px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm font-medium text-gray-700 transition">
                    {{ __('Filter') }}
                </button>

                @if (request()->hasAny(['q', 'category', 'tag']))
                    <a href="{{ route('admin.posts.index', ['type' => $type, 'status' => $status]) }}"
                       class="text-sm text-gray-500 hover:text-gray-900">{{ __('Clear') }}</a>
                @endif
            </form>

            @if ($posts->total() > 0)
                <div class="text-xs text-gray-500">{{ $posts->total() }} {{ __('item(s)') }}</div>
            @endif
        </div>

        @if ($posts->isEmpty())
            <div class="p-16 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v8a2 2 0 01-2 2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M14 4v6h6M8 13h8M8 17h5"/></svg>
                <p class="mt-4 text-sm text-gray-500">
                    @if (request('q') || request('category') || request('tag'))
                        {{ __('No posts match your filters.') }}
                    @elseif ($status === 'trash')
                        {{ __('Trash is empty.') }}
                    @else
                        {{ __('No posts yet. Create your first one.') }}
                    @endif
                </p>
                <a href="{{ route('admin.posts.create', ['type' => $type]) }}"
                   class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    {{ __('Create First Post') }}
                </a>
            </div>
        @else
            <form method="POST" action="{{ route('admin.posts.bulk') }}" @submit.prevent="if (selected.length === 0 || !bulkAction) return; $el.submit()">
                @csrf

                <div x-show="selected.length > 0" x-cloak class="px-4 py-2.5 border-b border-gray-100 bg-rj-purple/5 flex items-center gap-3 flex-wrap">
                    <span class="text-xs text-gray-600"><b x-text="selected.length"></b> {{ __('selected') }}</span>

                    <select x-model="bulkAction" class="rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-xs">
                        <option value="">{{ __('Bulk Actions') }}</option>
                        <option value="publish">{{ __('Publish') }}</option>
                        <option value="draft">{{ __('Move to Draft') }}</option>
                        <option value="sticky">{{ __('Mark Sticky') }}</option>
                        <option value="unsticky">{{ __('Unmark Sticky') }}</option>
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
                                       @change="selected = $event.target.checked ? [{{ $posts->pluck('id')->implode(',') }}] : []"
                                       :checked="selected.length === {{ $posts->count() }} && {{ $posts->count() }} > 0"
                                       class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                            </th>
                            <th class="text-left font-medium text-gray-600 px-4 py-3">{{ __('Title') }}</th>
                            <th class="text-left font-medium text-gray-600 px-4 py-3 hidden lg:table-cell">{{ __('Author') }}</th>
                            <th class="text-left font-medium text-gray-600 px-4 py-3 hidden xl:table-cell">{{ __('Categories / Tags') }}</th>
                            <th class="text-center font-medium text-gray-600 px-4 py-3 hidden md:table-cell">{{ __('Comments') }}</th>
                            <th class="text-left font-medium text-gray-600 px-4 py-3 hidden md:table-cell">{{ __('Date') }}</th>
                            <th class="text-right font-medium text-gray-600 px-4 py-3">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($posts as $post)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-4 py-3">
                                    <input type="checkbox" :value="{{ $post->id }}" x-model.number="selected"
                                           class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex items-start gap-3 min-w-0">
                                        @if ($post->featured_image)
                                            <img src="{{ Str::startsWith($post->featured_image, 'http') ? $post->featured_image : asset($post->featured_image) }}"
                                                 alt="" class="w-12 h-12 rounded-lg object-cover flex-shrink-0 border border-gray-200">
                                        @else
                                            <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-rj-purple/15 to-rj-pink/15 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5 text-rj-purple/60" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v8a2 2 0 01-2 2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M14 4v6h6"/></svg>
                                            </div>
                                        @endif

                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                @if ($post->is_sticky)
                                                    <span title="{{ __('Sticky') }}" class="text-amber-500">
                                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                                    </span>
                                                @endif

                                                <a href="{{ route('admin.posts.edit', $post) }}"
                                                   class="text-sm font-medium text-gray-900 hover:text-rj-purple truncate">
                                                    {{ $post->title ?: __('(no title)') }}
                                                </a>

                                                @if ($post->status === 'draft')
                                                    <span class="px-1.5 py-0.5 rounded text-xs font-medium bg-amber-50 text-amber-700">{{ __('Draft') }}</span>
                                                @elseif ($post->status === 'scheduled')
                                                    <span class="px-1.5 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700">{{ __('Scheduled') }}</span>
                                                @elseif ($post->status === 'trash')
                                                    <span class="px-1.5 py-0.5 rounded text-xs font-medium bg-red-50 text-red-700">{{ __('Trash') }}</span>
                                                @endif
                                            </div>

                                            <div class="text-xs text-gray-500 mt-0.5 truncate">
                                                <code class="font-mono">/{{ $post->slug }}</code>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3 hidden lg:table-cell">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center text-white text-xs font-semibold">
                                            {{ strtoupper(substr($post->author->name ?? '?', 0, 1)) }}
                                        </div>
                                        <span class="text-sm text-gray-700">{{ $post->author->name ?? '—' }}</span>
                                    </div>
                                </td>

                                <td class="px-4 py-3 hidden xl:table-cell">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($post->categories->take(2) as $cat)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-rj-purple/10 text-rj-purple text-xs font-medium">
                                                {{ $cat->name }}
                                            </span>
                                        @endforeach

                                        @if ($post->categories->count() > 2)
                                            <span class="text-xs text-gray-400">+{{ $post->categories->count() - 2 }}</span>
                                        @endif

                                        @if ($post->categories->isEmpty() && $post->tags->isEmpty())
                                            <span class="text-xs text-gray-400">—</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-4 py-3 hidden md:table-cell text-center">
                                    <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                        {{ $post->comments_count ?? 0 }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 hidden md:table-cell">
                                    <div class="text-xs text-gray-600">
                                        @if ($post->status === 'scheduled' && $post->published_at)
                                            <div class="text-blue-600 font-medium">{{ __('Scheduled') }}</div>
                                            <div>{{ $post->published_at->format('M d, Y H:i') }}</div>
                                        @elseif ($post->published_at)
                                            <div>{{ $post->published_at->format('M d, Y') }}</div>
                                            <div class="text-gray-400">{{ $post->published_at->format('H:i') }}</div>
                                        @else
                                            <div>{{ $post->created_at->format('M d, Y') }}</div>
                                            <div class="text-gray-400">{{ __('Last modified') }}</div>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        @if ($post->status === 'trash')
                                            <form method="POST" action="{{ route('admin.posts.restore', $post) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:bg-green-50 hover:text-green-600 transition" title="{{ __('Restore') }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.posts.destroy', $post) }}"
                                                  onsubmit="return confirm('{{ __('Permanently delete this post?') }}')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:bg-red-50 hover:text-red-600 transition" title="{{ __('Delete Permanently') }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('admin.posts.edit', $post) }}"
                                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:bg-rj-purple/10 hover:text-rj-purple transition" title="{{ __('Edit') }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            </a>

                                            <a href="{{ route('admin.posts.show', $post) }}"
                                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition" title="{{ __('View') }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.46 12C3.73 7.94 7.52 5 12 5s8.27 2.94 9.54 7c-1.27 4.06-5.06 7-9.54 7S3.73 16.06 2.46 12z"/></svg>
                                            </a>

                                            <form method="POST" action="{{ route('admin.posts.duplicate', $post) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition" title="{{ __('Duplicate') }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.posts.trash-action', $post) }}"
                                                  onsubmit="return confirm('{{ __('Move this post to trash?') }}')" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:bg-red-50 hover:text-red-600 transition" title="{{ __('Move to Trash') }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </form>

            @if ($posts->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">
                    {{ $posts->links() }}
                </div>
            @endif
        @endif

    </div>

</div>
@endsection
