@extends('admin.layouts.app')

@section('title', __('Categories'))

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-base font-semibold text-gray-900">{{ __('Categories') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Organize your posts into categories and subcategories.') }}</p>
        </div>

        <a href="{{ route('admin.posts.categories.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            {{ __('Add New Category') }}
        </a>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Total') }}</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Active') }}</div>
            <div class="text-2xl font-bold text-green-600 mt-1">{{ number_format($stats['active']) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Root Categories') }}</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['roots']) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Empty') }}</div>
            <div class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($stats['empty']) }}</div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">

        <div class="px-4 py-3 border-b border-gray-100">
            <form method="GET" action="{{ route('admin.posts.categories') }}" class="relative max-w-md">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="{{ __('Search categories...') }}"
                       class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
            </form>
        </div>

        @if (empty($tree))
            <div class="p-16 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                <p class="mt-4 text-sm text-gray-500">{{ __('No categories yet.') }}</p>
                <a href="{{ route('admin.posts.categories.create') }}"
                   class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    {{ __('Create First Category') }}
                </a>
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left font-medium text-gray-600 px-4 py-3">{{ __('Name') }}</th>
                        <th class="text-left font-medium text-gray-600 px-4 py-3 hidden md:table-cell">{{ __('Slug') }}</th>
                        <th class="text-center font-medium text-gray-600 px-4 py-3 hidden lg:table-cell">{{ __('Posts') }}</th>
                        <th class="text-center font-medium text-gray-600 px-4 py-3">{{ __('Status') }}</th>
                        <th class="text-right font-medium text-gray-600 px-4 py-3">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($tree as $node)
                        @php
                            $cat = $node['category'];
                            $depth = $node['depth'];
                        @endphp

                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3" style="padding-left: {{ $depth * 24 }}px;">

                                    @if ($depth > 0)
                                        <svg class="w-3.5 h-3.5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                    @endif

                                    @if ($cat->image_url)
                                        <img src="{{ $cat->image_url }}" alt="{{ $cat->name }}" class="w-9 h-9 rounded-lg object-cover flex-shrink-0 border border-gray-200">
                                    @else
                                        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-rj-purple/20 to-rj-pink/20 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-rj-purple" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                        </div>
                                    @endif

                                    <div class="min-w-0">
                                        <div class="text-sm font-medium text-gray-900 truncate">{{ $cat->name }}</div>
                                        @if ($cat->description)
                                            <div class="text-xs text-gray-500 mt-0.5 truncate">{{ Str::limit($cat->description, 60) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 hidden md:table-cell">
                                <code class="text-xs text-gray-600 font-mono bg-gray-50 px-2 py-1 rounded">{{ $cat->slug }}</code>
                            </td>

                            <td class="px-4 py-3 hidden lg:table-cell text-center">
                                <span class="inline-flex items-center justify-center min-w-[32px] px-2 py-0.5 rounded-full text-xs font-medium {{ $cat->post_count > 0 ? 'bg-rj-purple/10 text-rj-purple' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $cat->post_count }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-center">
                                @if ($cat->is_active)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-green-50 text-green-700 text-xs font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        {{ __('Active') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-xs font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                        {{ __('Inactive') }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">

                                    <a href="{{ route('admin.posts.categories.edit', $cat) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:bg-rj-purple/10 hover:text-rj-purple transition"
                                       title="{{ __('Edit') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </a>

                                    <form method="POST" action="{{ route('admin.posts.categories.destroy', $cat) }}"
                                          onsubmit="return confirm('{{ __('Delete this category?') }}')"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:bg-red-50 hover:text-red-600 transition disabled:opacity-30 disabled:cursor-not-allowed"
                                                title="{{ __('Delete') }}"
                                                {{ $cat->children->count() > 0 ? 'disabled' : '' }}>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

    </div>

</div>
@endsection