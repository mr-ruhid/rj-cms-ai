@extends('admin.layouts.app')

@section('title', __('Edit Menu'))

@section('content')
<div class="space-y-6" x-data="menuEditor()">

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.menus.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h2 class="text-base font-semibold text-gray-900">{{ $menu->name }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $menu->locationLabel() }} · {{ $menu->itemCount() }} {{ __('item(s)') }}
                </p>
            </div>
        </div>
    </div>

    @if (session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-r-lg">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-r-lg">
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 min-w-0">

        <div class="lg:col-span-2 space-y-6 min-w-0">

            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">{{ __('Add Menu Item') }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ __('Choose a type and add it to the menu.') }}</p>
                </div>

                <style>
                    .no-scrollbar::-webkit-scrollbar { display: none; }
                </style>

                <div class="border-b border-gray-100 mb-4"
                     x-data="{
                         isDragging: false,
                         isMoved: false,
                         startX: 0,
                         scrollLeft: 0,
                         scrollTabs(e) {
                             if (e.deltaY !== 0) {
                                 this.$refs.tabContainer.scrollLeft += e.deltaY;
                                 e.preventDefault();
                             }
                         },
                         startDrag(e) {
                             this.isDragging = true;
                             this.isMoved = false;
                             this.startX = e.pageX - this.$refs.tabContainer.offsetLeft;
                             this.scrollLeft = this.$refs.tabContainer.scrollLeft;
                         },
                         stopDrag() { this.isDragging = false; },
                         doDrag(e) {
                             if (!this.isDragging) return;
                             const x = e.pageX - this.$refs.tabContainer.offsetLeft;
                             const walk = (x - this.startX) * 2;
                             if (Math.abs(x - this.startX) > 5) {
                                 this.isMoved = true;
                                 e.preventDefault();
                             }
                             this.$refs.tabContainer.scrollLeft = this.scrollLeft - walk;
                         }
                     }">
                    <div x-ref="tabContainer"
                         @wheel="scrollTabs"
                         @mousedown="startDrag"
                         @mouseleave="stopDrag"
                         @mouseup="stopDrag"
                         @mousemove="doDrag"
                         :class="isDragging ? 'cursor-grabbing' : 'cursor-grab'"
                         class="flex gap-1 px-6 overflow-x-auto snap-x scroll-smooth select-none no-scrollbar"
                         style="margin-bottom:-1px; scrollbar-width: none; -ms-overflow-style: none;">
                        @foreach (\App\Models\MenuItem::types() as $key => $meta)
                            <button type="button"
                                    @click="if(!isMoved) { activeType = '{{ $key }}'; loadOptions('{{ $key }}') }"
                                    :class="activeType === '{{ $key }}' ? 'border-rj-purple text-rj-purple' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                    class="px-3 py-2 border-b-2 text-xs font-medium transition flex items-center gap-1.5 whitespace-nowrap flex-shrink-0 snap-start pointer-events-auto">
                                <span>{{ $meta['icon'] }}</span>
                                <span>{{ $meta['label'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.menus.items.store', $menu) }}" class="space-y-4 px-6 pb-6">
                    @csrf
                    <input type="hidden" name="type" :value="activeType">

                    <div x-show="activeType === 'custom'">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ __('Label') }} <span class="text-red-500">*</span>
                            <span class="text-xs font-normal text-gray-400 ml-1">({{ strtoupper(config('app.locale')) }})</span>
                        </label>
                        <input type="text" name="label" value="{{ old('label') }}"
                               placeholder="{{ __('e.g. Home') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        @error('label')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror

                        @php $locales = rj_available_locales(); @endphp
                        @if (count($locales) > 1)
                            <div class="mt-3" x-data="{ openTranslations: false }">
                                <button type="button" @click="openTranslations = !openTranslations"
                                        class="text-xs text-rj-purple hover:underline flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                    {{ __('Translations') }} ({{ count($locales) - 1 }})
                                </button>

                                <div x-show="openTranslations" x-cloak class="mt-3 space-y-3 pl-4 border-l-2 border-gray-100">
                                    @foreach ($locales as $locale)
                                        @if ($locale === config('app.locale')) @continue @endif
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                                {{ rj_lang_name($locale) }} <span class="uppercase text-gray-400">({{ $locale }})</span>
                                            </label>
                                            <input type="text" name="translations[{{ $locale }}]"
                                                   value="{{ old('translations.' . $locale) }}"
                                                   placeholder="{{ __('Optional') }}"
                                                   class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-xs">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div x-show="activeType !== 'custom'">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Select') }} <span class="text-red-500">*</span></label>
                        <select name="object_id" x-model="selectedObjectId" @change="onObjectSelect($event)"
                                class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                            <option value="">{{ __('— Choose —') }}</option>
                            <template x-for="opt in options" :key="opt.id">
                                <option :value="opt.id" x-text="opt.label"></option>
                            </template>
                        </select>
                        <p x-show="activeType !== 'custom' && options.length === 0 && !loadingOptions"
                           class="text-xs text-gray-500 mt-1">
                            {{ __('No items available yet.') }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('URL') }}</label>
                        <input type="text" name="url" value="{{ old('url') }}" x-model="url"
                               placeholder="{{ __('https://example.com or /path') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Parent') }}</label>
                        <select name="parent_id" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                            <option value="">{{ __('— Top level —') }}</option>
                            @foreach ($menu->items->whereNull('parent_id') as $item)
                                <option value="{{ $item->id }}" {{ old('parent_id') == $item->id ? 'selected' : '' }}>{{ $item->label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Target') }}</label>
                            <select name="target" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                                <option value="_self">{{ __('Same window') }}</option>
                                <option value="_blank">{{ __('New window') }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('CSS Class') }}</label>
                            <input type="text" name="css_class" value="{{ old('css_class') }}"
                                   placeholder="{{ __('Optional') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            {{ __('Add to Menu') }}
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Menu Structure') }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ __('Drag items to reorder. Items with children are collapsible.') }}</p>
                    </div>
                    <span class="text-xs text-gray-500">{{ $menu->itemCount() }} {{ __('item(s)') }}</span>
                </div>

                @if ($menu->items->isEmpty())
                    <div class="p-12 text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <p class="mt-3 text-sm text-gray-500">{{ __('No items yet. Add your first item above.') }}</p>
                    </div>
                @else
                    <div class="p-4">
                        <div id="menu-tree" class="space-y-1">
                            @foreach ($menu->items->whereNull('parent_id') as $item)
                                @include('admin.menus.partials.item-row', ['item' => $item, 'depth' => 0])
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

        </div>

        <div class="space-y-6 min-w-0">

            <form method="POST" action="{{ route('admin.menus.update', $menu) }}" class="bg-white rounded-lg shadow-sm border border-gray-100">
                @csrf
                @method('PUT')

                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">{{ __('Menu Settings') }}</h3>
                </div>

                <div class="p-6 space-y-4">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Name') }}</label>
                        <input type="text" name="name" value="{{ $menu->name }}" required
                               class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Location') }}</label>
                        <select name="location" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                            <option value="">{{ __('— Not assigned —') }}</option>
                            @foreach ($locations as $key => $label)
                                <option value="{{ $key }}" {{ $menu->location === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Description') }}</label>
                        <input type="text" name="description" value="{{ $menu->description }}"
                               class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    </div>

                    <label class="flex items-start gap-3 cursor-pointer pt-2 border-t border-gray-100">
                        <input type="checkbox" name="is_active" value="1" {{ $menu->is_active ? 'checked' : '' }}
                               class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                        <span>
                            <span class="block text-sm font-medium text-gray-700">{{ __('Active') }}</span>
                            <span class="block text-xs text-gray-500 mt-0.5">{{ __('Only one menu per location is active.') }}</span>
                        </span>
                    </label>

                </div>

                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
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