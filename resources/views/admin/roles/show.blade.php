@extends('admin.layouts.app')

@section('title', __('Role Details: :name', ['name' => $role->name]))

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <div class="flex items-center justify-between gap-3 mb-2">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.roles.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ __('Role Details') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $role->name }}</p>
            </div>
        </div>
        
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.roles.edit', $role) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                {{ __('Edit Role') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-sm font-bold text-gray-900">{{ __('Role Information') }}</h3>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">{{ __('Name') }}</div>
                        <div class="text-sm font-medium text-gray-900">{{ $role->name }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">{{ __('Slug') }}</div>
                        <div class="text-sm font-mono text-gray-700 bg-gray-100 px-2 py-0.5 rounded inline-block">{{ $role->slug }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">{{ __('Level') }}</div>
                        <div class="text-sm font-medium text-gray-900">{{ $role->level }}</div>
                    </div>
                    @if($role->description)
                        <div>
                            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">{{ __('Description') }}</div>
                            <div class="text-sm text-gray-700">{{ $role->description }}</div>
                        </div>
                    @endif
                    @if($role->level >= 90)
                        <div class="pt-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-amber-100 text-amber-800 text-xs font-bold">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                {{ __('System Role') }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900">{{ __('Assigned Users') }}</h3>
                    <span class="bg-rj-purple/10 text-rj-purple text-xs font-bold px-2 py-0.5 rounded-full">{{ $role->users->count() }}</span>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($role->users as $user)
                        <div class="p-4 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-gray-600 font-bold text-xs flex-shrink-0 border border-gray-300">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-bold text-gray-900 truncate">{{ $user->name }}</div>
                                <div class="text-xs text-gray-500 truncate">{{ $user->email }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-sm text-gray-500">
                            {{ __('No users assigned to this role yet.') }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900">{{ __('Granted Permissions') }}</h3>
                    <span class="bg-blue-50 text-blue-600 text-xs font-bold px-2 py-0.5 rounded-full">{{ $role->permissions->count() }}</span>
                </div>
                
                <div class="p-6">
                    @if($role->permissions->isEmpty())
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <p class="text-sm text-gray-500">{{ __('This role has no permissions assigned.') }}</p>
                        </div>
                    @else
                        @php
                            $groupedPermissions = $role->permissions->groupBy('group');
                        @endphp
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($groupedPermissions as $group => $permissions)
                                <div>
                                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 pb-2 border-b border-gray-100">
                                        {{ ucfirst($group ?? 'General') }}
                                    </h4>
                                    <ul class="space-y-2.5">
                                        @foreach($permissions as $permission)
                                            <li class="flex items-start gap-2">
                                                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                <div>
                                                    <span class="text-sm font-medium text-gray-800">{{ $permission->name }}</span>
                                                    @if($permission->description)
                                                        <p class="text-[11px] text-gray-500">{{ $permission->description }}</p>
                                                    @endif
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection