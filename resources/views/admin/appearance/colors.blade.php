@extends('admin.layouts.app')

@section('title', __('Colors'))

@section('content')
<div class="space-y-6" x-data="colorPalette(@js($colors), @js($defaults))">

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-base font-semibold text-gray-900">{{ __('Global Color Palette') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Site-wide color tokens used across RJBuilder and themes.') }}</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.appearance.colors.export') }}"
               class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                {{ __('Export') }}
            </a>

            <label class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                {{ __('Import') }}
                <input type="file" accept=".json" class="hidden" @change="importFile($event)">
            </label>

            <button type="button" @click="resetAll()" :disabled="saving"
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white border border-red-200 text-red-600 text-sm font-medium hover:bg-red-50 transition disabled:opacity-60">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                {{ __('Restore Defaults') }}
            </button>

            <button type="button" @click="saveAll()" :disabled="saving || !isDirty"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition disabled:opacity-60">
                <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <svg x-show="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                <span x-text="saving ? '{{ __('Saving...') }}' : '{{ __('Save Changes') }}'"></span>
            </button>
        </div>
    </div>

    <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div class="text-sm text-blue-800 leading-relaxed">
            <strong>{{ __('How it works:') }}</strong>
            {{ __('Each color generates a CSS variable like :var. Themes and RJBuilder widgets reference these tokens so a single change updates your whole site.', ['var' => '--rj-gc-primary']) }}
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">

        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">
                {{ __('Palette') }}
                <span class="text-xs font-normal text-gray-400 ml-1" x-text="'(' + colors.length + ')'"></span>
            </h3>

            <button type="button" @click="addColor()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-100 text-gray-700 text-xs font-medium hover:bg-gray-200 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                {{ __('Add Color') }}
            </button>
        </div>

        <div x-show="colors.length === 0" class="p-16 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
            <p class="mt-4 text-sm text-gray-500">{{ __('No colors defined. Click "Add Color" to start.') }}</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 p-6" x-show="colors.length > 0">
            <template x-for="(c, i) in colors" :key="c.id + '-' + i">
                <div class="border border-gray-200 rounded-xl overflow-hidden bg-white hover:shadow-md transition group relative"
                     draggable="true"
                     @dragstart="dragStart(i, $event)"
                     @dragover.prevent="dragOver(i, $event)"
                     @drop.prevent="dragDrop(i, $event)"
                     @dragend="dragEnd()"
                     :class="dragIndex === i ? 'opacity-40' : ''">

                    <div class="h-24 relative cursor-move" :style="'background:' + (c.color || '#e5e7eb')">
                        <div class="absolute top-2 left-2 text-white/80 opacity-0 group-hover:opacity-100 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </div>

                        <button type="button" @click="removeColor(i)"
                                class="absolute top-2 right-2 w-7 h-7 rounded-full bg-white/90 hover:bg-red-500 hover:text-white text-red-600 flex items-center justify-center opacity-0 group-hover:opacity-100 transition shadow-sm"
                                :title="'{{ __('Delete') }}'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="p-3 space-y-2">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-wide mb-1">{{ __('Name') }}</label>
                            <input type="text" x-model="c.name" @input="markDirty()"
                                   maxlength="80"
                                   class="w-full px-2 py-1.5 rounded-md border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-xs">
                        </div>

                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-wide mb-1">{{ __('ID / Token') }}</label>
                            <input type="text" x-model="c.id" @input="markDirty()"
                                   maxlength="50" pattern="[a-zA-Z0-9_-]+"
                                   class="w-full px-2 py-1.5 rounded-md border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-xs font-mono">
                            <p class="text-[10px] text-gray-400 mt-1 font-mono" x-text="'--rj-gc-' + (c.id || 'token')"></p>
                        </div>

                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-wide mb-1">{{ __('Color') }}</label>
                            <div class="flex items-center gap-2">
                                <input type="color" x-model="c.color" @input="markDirty()"
                                       class="w-9 h-8 rounded border border-gray-300 cursor-pointer p-0.5">
                                <input type="text" x-model="c.color" @input="markDirty()"
                                       maxlength="30"
                                       class="flex-1 px-2 py-1.5 rounded-md border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-xs font-mono">
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">{{ __('Preview') }}</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            <template x-for="c in colors" :key="'prev-' + c.id">
                <div class="text-center">
                    <div class="h-16 rounded-lg mb-2 border border-gray-100" :style="'background:' + (c.color || '#e5e7eb')"></div>
                    <div class="text-xs font-medium text-gray-700 truncate" x-text="c.name"></div>
                    <div class="text-[10px] text-gray-400 font-mono truncate" x-text="c.color"></div>
                </div>
            </template>
        </div>
    </div>

    <div x-show="toast.show" x-cloak x-transition
         class="fixed bottom-6 right-6 px-5 py-3 rounded-xl shadow-2xl text-white text-sm font-medium z-50"
         :style="'background:' + (toast.type === 'error' ? '#dc2626' : '#059669')"
         x-text="toast.message"></div>

</div>

<script>
function colorPalette(initial, defaults) {
    return {
        colors: JSON.parse(JSON.stringify(initial)),
        originalSnapshot: JSON.stringify(initial),
        defaults: defaults,
        saving: false,
        isDirty: false,
        dragIndex: null,
        toast: { show: false, message: '', type: 'success' },

        markDirty() {
            this.isDirty = JSON.stringify(this.colors) !== this.originalSnapshot;
        },

        showToast(message, type) {
            this.toast = { show: true, message, type: type || 'success' };
            setTimeout(() => { this.toast.show = false; }, 3500);
        },

        addColor() {
            const base = 'color';
            let id = base;
            let n = 1;
            const ids = this.colors.map(c => c.id);
            while (ids.includes(id)) {
                id = base + '-' + n;
                n++;
            }
            this.colors.push({ id, name: 'New Color', color: '#7c3aed' });
            this.markDirty();
        },

        removeColor(i) {
            if (!confirm('{{ __('Delete this color?') }}')) return;
            this.colors.splice(i, 1);
            this.markDirty();
        },

        dragStart(i, e) {
            this.dragIndex = i;
            e.dataTransfer.effectAllowed = 'move';
        },

        dragOver(i, e) {
            e.dataTransfer.dropEffect = 'move';
        },

        dragDrop(i, e) {
            if (this.dragIndex === null || this.dragIndex === i) return;
            const moved = this.colors.splice(this.dragIndex, 1)[0];
            this.colors.splice(i, 0, moved);
            this.dragIndex = null;
            this.markDirty();
        },

        dragEnd() {
            this.dragIndex = null;
        },

        async saveAll() {
            const ids = this.colors.map(c => c.id).filter(Boolean);
            if (new Set(ids).size !== ids.length) {
                this.showToast('{{ __('Color IDs must be unique.') }}', 'error');
                return;
            }

            for (const c of this.colors) {
                if (!c.id || !c.name || !c.color) {
                    this.showToast('{{ __('All fields are required.') }}', 'error');
                    return;
                }
                if (!/^[a-z0-9_-]+$/i.test(c.id)) {
                    this.showToast('{{ __('IDs may only contain letters, numbers, dash, underscore.') }}', 'error');
                    return;
                }
            }

            this.saving = true;

            try {
                const res = await fetch('{{ route('admin.appearance.colors.save') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ colors: this.colors }),
                });

                const data = await res.json().catch(() => ({}));

                if (res.ok && data.success) {
                    this.originalSnapshot = JSON.stringify(this.colors);
                    this.isDirty = false;
                    this.showToast(data.message || '{{ __('Saved.') }}', 'success');
                } else {
                    this.showToast(data.message || '{{ __('Save failed.') }}', 'error');
                }
            } catch (e) {
                this.showToast('{{ __('Network error.') }}', 'error');
            } finally {
                this.saving = false;
            }
        },

        async resetAll() {
            if (!confirm('{{ __('Reset palette to default colors? This cannot be undone.') }}')) return;
            this.saving = true;

            try {
                const res = await fetch('{{ route('admin.appearance.colors.reset') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });

                const data = await res.json().catch(() => ({}));

                if (res.ok && data.success) {
                    this.colors = JSON.parse(JSON.stringify(data.colors || this.defaults));
                    this.originalSnapshot = JSON.stringify(this.colors);
                    this.isDirty = false;
                    this.showToast(data.message || '{{ __('Reset.') }}', 'success');
                } else {
                    this.showToast(data.message || '{{ __('Reset failed.') }}', 'error');
                }
            } catch (e) {
                this.showToast('{{ __('Network error.') }}', 'error');
            } finally {
                this.saving = false;
            }
        },

        async importFile(e) {
            const file = e.target.files?.[0];
            if (!file) return;

            if (this.isDirty && !confirm('{{ __('Unsaved changes will be lost. Continue?') }}')) {
                e.target.value = '';
                return;
            }

            const fd = new FormData();
            fd.append('file', file);
            fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            this.saving = true;

            try {
                const res = await fetch('{{ route('admin.appearance.colors.import') }}', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: fd,
                });

                const data = await res.json().catch(() => ({}));

                if (res.ok && data.success) {
                    this.colors = JSON.parse(JSON.stringify(data.colors || []));
                    this.originalSnapshot = JSON.stringify(this.colors);
                    this.isDirty = false;
                    this.showToast(data.message || '{{ __('Imported.') }}', 'success');
                } else {
                    this.showToast(data.message || '{{ __('Import failed.') }}', 'error');
                }
            } catch (err) {
                this.showToast('{{ __('Network error.') }}', 'error');
            } finally {
                this.saving = false;
                e.target.value = '';
            }
        },
    };
}
</script>
@endsection