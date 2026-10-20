@extends('admin.layouts.app')

@section('title', __('Add New Role'))

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="roleForm()">

    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('admin.roles.index') }}" class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ __('Add New Role') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Create a new role and assign permissions.') }}</p>
        </div>
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

    <form method="POST" action="{{ route('admin.roles.store') }}">
        @csrf

        <div class="space-y-6">

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-sm font-bold text-gray-900">{{ __('Role Details') }}</h3>
                </div>
                
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Role Name') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required x-model="name" @input="generateSlug()"
                               placeholder="e.g. Editor, Manager, Support"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Slug') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="slug" value="{{ old('slug') }}" required x-model="slug"
                               placeholder="e.g. editor, manager, support"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                        <p class="text-xs text-gray-500 mt-1">{{ __('Unique identifier used in code.') }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Description') }}</label>
                        <textarea name="description" rows="2" placeholder="{{ __('Brief description of this role...') }}"
                                  class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">{{ old('description') }}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Level') }}</label>
                        <input type="number" name="level" value="{{ old('level', 10) }}" min="1" max="89"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        <p class="text-xs text-gray-500 mt-1">{{ __('Hierarchy level. Super Admin is 100. Lower numbers have fewer privileges.') }}</p>
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
                                               {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}
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

            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">
                    {{ __('Cancel') }}
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    {{ __('Create Role') }}
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function roleForm() {
    return {
        name: '{{ old('name') }}',
        slug: '{{ old('slug') }}',
        selectedPermissions: {!! json_encode(old('permissions', [])) !!},

        generateSlug() {
            this.slug = this.name
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
        },

        selectAll() {
            const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
            checkboxes.forEach(cb => {
                cb.checked = true;
                if (!this.selectedPermissions.includes(parseInt(cb.value))) {
                    this.selectedPermissions.push(parseInt(cb.value));
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