@extends('admin.layouts.app')

@section('title', __('Roles & Permissions'))

@section('content')
<div class="space-y-6" x-data="{ search: '' }">

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-base font-semibold text-gray-900">{{ __('Roles') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Manage user roles and their permissions.') }}</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.roles.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                {{ __('Add New Role') }}
            </a>
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

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between gap-3">
            <div class="relative flex-1 max-w-md">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" x-model="search" placeholder="{{ __('Search roles...') }}"
                       class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
            </div>
        </div>

        @if ($roles->isEmpty())
            <div class="p-16 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <p class="mt-4 text-sm text-gray-500">{{ __('No roles found.') }}</p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left font-medium text-gray-600 px-5 py-3">{{ __('Role Name') }}</th>
                        <th class="text-left font-medium text-gray-600 px-5 py-3 hidden md:table-cell">{{ __('Slug') }}</th>
                        <th class="text-center font-medium text-gray-600 px-5 py-3">{{ __('Users') }}</th>
                        <th class="text-center font-medium text-gray-600 px-5 py-3">{{ __('Permissions') }}</th>
                        <th class="text-right font-medium text-gray-600 px-5 py-3">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($roles as $role)
                        <tr x-show="search === '' || '{{ strtolower($role->name) }}'.includes(search.toLowerCase())"
                            class="hover:bg-gray-50/50 transition group">
                            
                            <td class="px-5 py-4 align-middle">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-rj-purple/10 text-rj-purple flex items-center justify-center font-bold text-sm flex-shrink-0">
                                        {{ strtoupper(substr($role->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.roles.show', $role) }}" class="text-sm font-bold text-gray-900 group-hover:text-rj-purple transition">
                                            {{ $role->name }}
                                        </a>
                                        @if ($role->level >= 90)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-700 ml-2">
                                                {{ __('System') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-5 py-4 align-middle hidden md:table-cell">
                                <span class="font-mono text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded">{{ $role->slug }}</span>
                            </td>
                            
                            <td class="px-5 py-4 align-middle text-center">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-gray-600 text-xs font-bold">
                                    {{ $role->users_count }}
                                </span>
                            </td>
                            
                            <td class="px-5 py-4 align-middle text-center">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-50 text-blue-600 text-xs font-bold">
                                    {{ $role->permissions->count() }}
                                </span>
                            </td>
                            
                            <td class="px-5 py-4 align-middle text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.roles.edit', $role) }}" 
                                       class="p-1.5 rounded text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition" title="{{ __('Edit') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </a>
                                    
                                    @if ($role->level < 90)
                                        <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('{{ __('Are you sure you want to delete this role?') }}')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="{{ __('Delete') }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                            </button>
                                        </form>
                                    @endif
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