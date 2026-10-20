@extends('admin.layouts.app')

@section('title', __('Edit Menu'))

@section('content')
<div class="space-y-6" x-data="menuEditor()">

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.menus.index') }}" class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-rj-purple hover:border-rj-purple/30 transition shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $menu->name }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $menu->locationLabel() }} · {{ $menu->itemCount() }} {{ __('item(s)') }}
                </p>
            </div>
        </div>
    </div>

    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl shadow-sm">
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 min-w-0">

        <div class="lg:col-span-2 space-y-6 min-w-0">

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-base font-bold text-gray-900">{{ __('Add Menu Item') }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ __('Choose a type and add it to the menu.') }}</p>
                </div>

                <style>
                    .no-scrollbar::-webkit-scrollbar { display: none; }
                </style>

                <div class="border-b border-gray-100" x-data="{
                         isDragging: false, isMoved: false, startX: 0, scrollLeft: 0,
                         scrollTabs(e) { if (e.deltaY !== 0) { this.$refs.tabContainer.scrollLeft += e.deltaY; e.preventDefault(); } },
                         startDrag(e) { this.isDragging = true; this.isMoved = false; this.startX = e.pageX - this.$refs.tabContainer.offsetLeft; this.scrollLeft = this.$refs.tabContainer.scrollLeft; },
                         stopDrag() { this.isDragging = false; },
                         doDrag(e) { if (!this.isDragging) return; const x = e.pageX - this.$refs.tabContainer.offsetLeft; const walk = (x - this.startX) * 2; if (Math.abs(x - this.startX) > 5) { this.isMoved = true; e.preventDefault(); } this.$refs.tabContainer.scrollLeft = this.scrollLeft - walk; }
                     }">
                    <div x-ref="tabContainer" @wheel="scrollTabs" @mousedown="startDrag" @mouseleave="stopDrag" @mouseup="stopDrag" @mousemove="doDrag" :class="isDragging ? 'cursor-grabbing' : 'cursor-grab'" class="flex gap-1 px-4 overflow-x-auto snap-x scroll-smooth select-none no-scrollbar bg-gray-50/30 pt-3" style="margin-bottom:-1px; scrollbar-width: none; -ms-overflow-style: none;">
                        @foreach (\App\Models\MenuItem::types() as $key => $meta)
                            <button type="button" @click="if(!isMoved) { activeType = '{{ $key }}'; loadOptions('{{ $key }}') }" :class="activeType === '{{ $key }}' ? 'bg-white border-gray-200 border-b-white text-rj-purple shadow-[0_-2px_10px_rgba(0,0,0,0.02)]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100/50'" class="px-4 py-2.5 border border-b-0 rounded-t-xl text-sm font-semibold transition flex items-center gap-2 whitespace-nowrap flex-shrink-0 snap-start pointer-events-auto">
                                @if($key === 'custom')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                @elseif($key === 'page')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                @elseif($key === 'post')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v8a2 2 0 01-2 2z M14 4v6h6M8 13h8M8 17h5"/></svg>
                                @elseif($key === 'category')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                @elseif($key === 'tag')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z M7 7h.01"/></svg>
                                @endif
                                <span>{{ $meta['label'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.menus.items.store', $menu) }}" class="space-y-5 p-6 bg-white">
                    @csrf
                    <input type="hidden" name="type" :value="activeType">

                    <div x-show="activeType === 'custom'" class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                {{ __('Label') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="label" value="{{ old('label') }}" placeholder="{{ __('e.g. Home') }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm transition-colors">
                            @error('label') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        @php $locales = rj_available_locales(); @endphp
                        @if (count($locales) > 1)
                            <div x-data="{ openTranslations: false }">
                                <button type="button" @click="openTranslations = !openTranslations" class="text-xs font-semibold text-rj-purple hover:text-rj-pink transition flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 transition-transform" :class="openTranslations ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                    {{ __('Translations') }} ({{ count($locales) - 1 }})
                                </button>
                                <div x-show="openTranslations" x-cloak x-collapse class="mt-3 space-y-3 pl-4 border-l-2 border-rj-purple/20">
                                    @foreach ($locales as $locale)
                                        @if ($locale === config('app.locale')) @continue @endif
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">
                                                {{ rj_lang_name($locale) }} <span class="uppercase text-gray-400 font-normal">({{ $locale }})</span>
                                            </label>
                                            <input type="text" name="translations[{{ $locale }}]" value="{{ old('translations.' . $locale) }}" placeholder="{{ __('Optional') }}" class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div x-show="activeType !== 'custom'" class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Select') }} <span class="text-red-500">*</span></label>
                            <select name="object_id" x-model="selectedObjectId" @change="onObjectSelect($event)" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm bg-white">
                                <option value="">{{ __('— Choose —') }}</option>
                                <template x-for="opt in options" :key="opt.id">
                                    <option :value="opt.id" x-text="opt.label"></option>
                                </template>
                            </select>
                            <p x-show="activeType !== 'custom' && options.length === 0 && !loadingOptions" class="text-xs text-gray-500 mt-2">
                                {{ __('No items available yet.') }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('URL') }}</label>
                        <input type="text" name="url" value="{{ old('url') }}" x-model="url" placeholder="{{ __('https://example.com or /path') }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm font-mono bg-gray-50">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Parent') }}</label>
                            <select name="parent_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm bg-white">
                                <option value="">{{ __('— Top level —') }}</option>
                                @foreach ($menu->items->whereNull('parent_id') as $item)
                                    <option value="{{ $item->id }}" {{ old('parent_id') == $item->id ? 'selected' : '' }}>{{ $item->label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Target') }}</label>
                            <select name="target" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm bg-white">
                                <option value="_self">{{ __('Same window') }}</option>
                                <option value="_blank">{{ __('New window') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="inline-flex items-center justify-center w-full gap-2 px-5 py-3 rounded-xl bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-bold hover:opacity-90 transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            {{ __('Add to Menu') }}
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">{{ __('Menu Structure') }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ __('Drag items to reorder. Items with children are collapsible.') }}</p>
                    </div>
                    <span class="bg-white border border-gray-200 text-gray-600 text-xs font-bold px-3 py-1 rounded-full shadow-sm">{{ $menu->itemCount() }} {{ __('items') }}</span>
                </div>

                @if ($menu->items->isEmpty())
                    <div class="p-14 text-center">
                        <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-3 border border-gray-100">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </div>
                        <p class="text-sm font-medium text-gray-900">{{ __('No items yet.') }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ __('Add your first item using the form above.') }}</p>
                    </div>
                @else
                    <div class="p-5 bg-gray-50/30">
                        <div id="menu-tree" class="space-y-2">
                            @foreach ($menu->items->whereNull('parent_id') as $item)
                                @include('admin.menus.partials.item-row', ['item' => $item, 'depth' => 0])
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

        </div>

        <div class="space-y-6 min-w-0">

            <form method="POST" action="{{ route('admin.menus.update', $menu) }}" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                @csrf
                @method('PUT')

                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-base font-bold text-gray-900">{{ __('Menu Settings') }}</h3>
                </div>

                <div class="p-6 space-y-5">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Name') }}</label>
                        <input type="text" name="name" value="{{ $menu->name }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Location') }}</label>
                        <select name="location" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm bg-white">
                            <option value="">{{ __('— Not assigned —') }}</option>
                            @foreach ($locations as $key => $label)
                                <option value="{{ $key }}" {{ $menu->location === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Description') }}</label>
                        <input type="text" name="description" value="{{ $menu->description }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm">
                    </div>

                    <div class="pt-2 border-t border-gray-100">
                        <label class="flex items-start gap-3 cursor-pointer group">
                            <div class="relative flex items-center justify-center mt-0.5">
                                <input type="checkbox" name="is_active" value="1" {{ $menu->is_active ? 'checked' : '' }} class="peer sr-only">
                                <div class="w-10 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-rj-purple/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rj-purple"></div>
                            </div>
                            <div>
                                <span class="block text-sm font-bold text-gray-900 group-hover:text-rj-purple transition">{{ __('Active Menu') }}</span>
                                <span class="block text-xs text-gray-500 mt-0.5">{{ __('Only one menu per location can be active.') }}</span>
                            </div>
                        </label>
                    </div>

                </div>

                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-end">
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 rounded-xl bg-gray-900 text-white text-sm font-bold hover:bg-gray-800 transition shadow-sm">
                        {{ __('Save Settings') }}
                    </button>
                </div>
            </form>

        </div>

    </div>

</div>

<script>
function toggleEdit(id) {
    const el = document.getElementById('edit-item-' + id);
    if (el) el.classList.toggle('hidden');
}
</script>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
<script>
function menuEditor() {
    return {
        activeType: 'custom',
        selectedObjectId: '',
        url: '',
        options: [],
        loadingOptions: false,

        async loadOptions(type) {
            this.options = [];
            this.selectedObjectId = '';
            this.url = '';

            if (type === 'custom') return;

            this.loadingOptions = true;
            try {
                const res = await fetch('{{ route('admin.menus.items.options') }}?type=' + type, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                this.options = await res.json();
            } catch (e) {
                console.error(e);
            } finally {
                this.loadingOptions = false;
            }
        },

        onObjectSelect(e) {
            const id = e.target.value;
            const opt = this.options.find(o => String(o.id) === String(id));
            this.url = opt ? opt.url : '';
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const tree = document.getElementById('menu-tree');
    if (!tree || typeof Sortable === 'undefined') return;

    Sortable.create(tree, {
        animation: 150,
        handle: '.drag-handle',
        ghostClass: 'opacity-40',
        onEnd: function () {
            const items = [];
            tree.querySelectorAll('[data-id]').forEach((el, index) => {
                items.push({
                    id: parseInt(el.dataset.id),
                    parent_id: null,
                    sort_order: index,
                });
            });

            fetch('{{ route('admin.menus.items.reorder', $menu) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ items })
            });
        }
    });
});
</script>
@endsection