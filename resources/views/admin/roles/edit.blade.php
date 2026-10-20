@extends('admin.layouts.app')

@section('title', __('Edit Role: :name', ['name' => $role->name]))

@section('content')
@php
    $rolePermissionIds = old('permissions', $role->permissions->pluck('id')->toArray());
@endphp

<div class="max-w-5xl mx-auto space-y-6" x-data="roleEditForm(@js($rolePermissionIds))">

    <div class="flex items-center justify-between gap-3 mb-2">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.roles.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ __('Edit Role') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $role->name }}</p>
            </div>
        </div>
        
        @if ($role->level < 90)
            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('{{ __('Are you sure you want to delete this role? This cannot be undone.') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-red-50 text-red-600 text-sm font-medium hover:bg-red-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                    {{ __('Delete Role') }}
                </button>
            </form>
        @endif
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-r-lg">
            <ul class="text-sm space-y-1 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.roles.update', $role) }}">
        @csrf
        @method('PUT')

        <div class="space-y-6">

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-sm font-bold text-gray-900">{{ __('Role Details') }}</h3>
                </div>
                
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Role Name') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $role->name) }}" required
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm {{ $role->level >= 90 ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                               {{ $role->level >= 90 ? 'readonly' : '' }}>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Slug') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="slug" value="{{ old('slug', $role->slug) }}" required
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono {{ $role->level >= 90 ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                               {{ $role->level >= 90 ? 'readonly' : '' }}>
                        <p class="text-xs text-gray-500 mt-1">{{ __('Unique identifier used in code.') }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Description') }}</label>
                        <textarea name="description" rows="2"
                                  class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">{{ old('description', $role->description) }}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Level') }}</label>
                        <input type="number" name="level" value="{{ old('level', $role->level) }}" min="1" max="89"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm {{ $role->level >= 90 ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                               {{ $role->level >= 90 ? 'readonly' : '' }}>
                        <p class="text-xs text-gray-500 mt-1">{{ __('Hierarchy level. Super Admin is 100.') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900">{{ __('Permissions') }}</h3>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="selectAll()" class="text-xs font-medium text-rj-purple hover:underline">{{ __('Select All') }}</button>
                        <span class="text-gray-300">|</span>
                        <button type="button" @click="deselectAll()" class="text-xs font-medium text-gray-500 hover:underline">{{ __('Deselect All') }}</button>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    @foreach ($permissions as $group => $groupPermissions)
                        <div class="border border-gray-100 rounded-lg overflow-hidden">
                            <div class="px-4 py-2.5 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                                <span class="text-xs font-bold text-gray-700 uppercase tracking-wide">
                                    {{ $groupLabels[$group] ?? ucfirst($group) }}
                                </span>
                                <button type="button" @click="toggleGroup('{{ $group }}')" class="text-[10px] font-medium text-gray-500 hover:text-gray-800">
                                    {{ __('Toggle Group') }}
                                </button>
                            </div>
                            
                            <div class="p-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                @foreach ($groupPermissions as $permission)
                                    <label class="flex items-start gap-2.5 p-2 rounded hover:bg-gray-50 cursor-pointer transition">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                               x-model="selectedPermissions"
                                               data-group="{{ $group }}"
                                               {{ in_array($permission->id, $rolePermissionIds) ? 'checked' : '' }}
                                               class="mt-0.5 w-4 h-4 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $permission->name }}</div>
                                            @if($permission->description)
                                                <div class="text-[10px] text-gray-500">{{ $permission->description }}</div>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">
                    {{ __('Cancel') }}
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    {{ __('Save Changes') }}
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function roleEditForm(initialPermissions) {
    return {
        selectedPermissions: initialPermissions.map(id => parseInt(id)),

        selectAll() {
            const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
            checkboxes.forEach(cb => {
                cb.checked = true;
                const val = parseInt(cb.value);
                if (!this.selectedPermissions.includes(val)) {
                    this.selectedPermissions.push(val);
                }
            });
        },

        deselectAll() {
            const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
            checkboxes.forEach(cb => {
                cb.checked = false;
            });
            this.selectedPermissions = [];
        },

        toggleGroup(group) {
            const checkboxes = document.querySelectorAll(`input[data-group="${group}"]`);
            const anyUnchecked = Array.from(checkboxes).some(cb => !cb.checked);
            
            checkboxes.forEach(cb => {
                cb.checked = anyUnchecked;
                const val = parseInt(cb.value);
                if (anyUnchecked) {
                    if (!this.selectedPermissions.includes(val)) this.selectedPermissions.push(val);
                } else {
                    this.selectedPermissions = this.selectedPermissions.filter(id => id !== val);
                }
            });
        }
    };
}
</script>
@endsection