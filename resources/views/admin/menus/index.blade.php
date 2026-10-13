@extends('admin.layouts.app')

@section('title', __('Menus'))

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-base font-semibold text-gray-900">{{ __('Menus') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Create navigation menus and assign them to header, footer or custom locations.') }}</p>
        </div>

        <a href="{{ route('admin.menus.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            {{ __('Add New Menu') }}
        </a>
    </div>

    @if ($menus->isEmpty())
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-16 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            <p class="mt-4 text-sm text-gray-500">{{ __('No menus yet.') }}</p>
            <p class="mt-1 text-xs text-gray-400">{{ __('Create your first menu to use in your header or footer.') }}</p>
            <a href="{{ route('admin.menus.create') }}"
               class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                {{ __('Create First Menu') }}
            </a>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left font-medium text-gray-600 px-4 py-3">{{ __('Name') }}</th>
                        <th class="text-left font-medium text-gray-600 px-4 py-3 hidden md:table-cell">{{ __('Location') }}</th>
                        <th class="text-center font-medium text-gray-600 px-4 py-3">{{ __('Items') }}</th>
                        <th class="text-center font-medium text-gray-600 px-4 py-3 hidden lg:table-cell">{{ __('Status') }}</th>
                        <th class="text-right font-medium text-gray-600 px-4 py-3">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($menus as $menu)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-rj-purple/15 to-rj-pink/15 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-rj-purple" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-sm font-medium text-gray-900 truncate">{{ $menu->name }}</div>
                                        @if ($menu->description)
                                            <div class="text-xs text-gray-500 mt-0.5 truncate">{{ $menu->description }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 hidden md:table-cell">
                                @if ($menu->location)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-rj-purple/10 text-rj-purple text-xs font-medium">
                                        {{ $menu->locationLabel() }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400 italic">{{ __('Not assigned') }}</span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center justify-center min-w-[32px] px-2 py-0.5 rounded-full text-xs font-medium {{ $menu->items_count > 0 ? 'bg-rj-purple/10 text-rj-purple' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $menu->items_count }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-center hidden lg:table-cell">
                                @if ($menu->is_active)
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
                                    <a href="{{ route('admin.menus.edit', $menu) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:bg-rj-purple/10 hover:text-rj-purple transition"
                                       title="{{ __('Edit') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </a>

                                    <form method="POST" action="{{ route('admin.menus.destroy', $menu) }}"
                                          onsubmit="return confirm('{{ __('Delete this menu? All items will be removed.') }}')"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:bg-red-50 hover:text-red-600 transition"
                                                title="{{ __('Delete') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>
@endsection