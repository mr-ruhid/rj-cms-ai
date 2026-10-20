@extends('admin.layouts.app')

@section('title', __('Edit Menu'))

@section('content')
<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }

    /* ---------- Toggle switch (Tailwind sinifindan asili deyil) ---------- */
    .rj-switch { position: relative; display: inline-block; width: 48px; height: 26px; flex-shrink: 0; }
    .rj-switch-track {
        position: absolute; inset: 0; border-radius: 9999px; background: #d1d5db;
        transition: background-color .25s ease, box-shadow .25s ease;
        box-shadow: inset 0 1px 3px rgba(0,0,0,.12);
    }
    .rj-switch-fill {
        position: absolute; inset: 0; border-radius: 9999px; opacity: 0;
        transition: opacity .25s ease; pointer-events: none;
    }
    .rj-switch-knob {
        position: absolute; top: 3px; left: 3px; width: 20px; height: 20px; border-radius: 9999px;
        background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,.3);
        transition: transform .25s cubic-bezier(.4,0,.2,1);
        pointer-events: none; z-index: 2;
    }
    .rj-switch input:checked ~ .rj-switch-knob { transform: translateX(22px); }
    .rj-switch input:focus-visible ~ .rj-switch-track { box-shadow: 0 0 0 4px rgba(124,58,237,.18); }

    /* ---------- Segmented tabs ---------- */
    .rj-tab { transition: background-color .2s, color .2s, box-shadow .2s; }

    /* ---------- Menu tree ---------- */
    #menu-tree .sortable-ghost { opacity: .4; }
    @media (prefers-reduced-motion: reduce) {
        .rj-switch-track, .rj-switch-fill, .rj-switch-knob, .rj-tab { transition: none; }
    }
</style>

<div class="space-y-8" x-data="menuEditor()">

    {{-- ============ Header ============ --}}
    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.menus.index') }}" aria-label="{{ __('Back') }}" class="w-11 h-11 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-rj-purple hover:border-rj-purple/30 hover:-translate-x-0.5 transition shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h2 class="text-2xl font-extrabold tracking-tight text-gray-900">{{ $menu->name }}</h2>
                    @if ($menu->is_active)
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ __('Active') }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 border border-gray-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>{{ __('Inactive') }}
                        </span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 mt-1 flex items-center gap-2 flex-wrap">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $menu->locationLabel() }}
                    </span>
                    <span class="text-gray-300">|</span>
                    <span>{{ $menu->itemCount() }} {{ __('item(s)') }}</span>
                </p>
            </div>
        </div>
    </div>

    @if (session('error'))
        <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl shadow-sm">
            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-sm">{{ session('error') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl shadow-sm">
            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 min-w-0 items-start">

        {{-- ============ Sol sutun ============ --}}
        <div class="lg:col-span-2 space-y-8 min-w-0">

            {{-- ---------- Add Menu Item ---------- --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-rj-purple to-rj-pink text-white flex items-center justify-center shadow-sm flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">{{ __('Add Menu Item') }}</h3>
                        <p class="text-sm text-gray-500 mt-0.5">{{ __('Choose a type and add it to the menu.') }}</p>
                    </div>
                </div>

                {{-- Tabs --}}
                <div class="px-6 pt-5" x-data="{
                         isDragging: false, isMoved: false, startX: 0, scrollLeft: 0,
                         scrollTabs(e) { if (e.deltaY !== 0) { this.$refs.tabContainer.scrollLeft += e.deltaY; e.preventDefault(); } },
                         startDrag(e) { this.isDragging = true; this.isMoved = false; this.startX = e.pageX - this.$refs.tabContainer.offsetLeft; this.scrollLeft = this.$refs.tabContainer.scrollLeft; },
                         stopDrag() { this.isDragging = false; },
                         doDrag(e) { if (!this.isDragging) return; const x = e.pageX - this.$refs.tabContainer.offsetLeft; const walk = (x - this.startX) * 2; if (Math.abs(x - this.startX) > 5) { this.isMoved = true; e.preventDefault(); } this.$refs.tabContainer.scrollLeft = this.scrollLeft - walk; }
                     }">
                    <div x-ref="tabContainer" @wheel="scrollTabs" @mousedown="startDrag" @mouseleave="stopDrag" @mouseup="stopDrag" @mousemove="doDrag" :class="isDragging ? 'cursor-grabbing' : 'cursor-grab'" class="flex gap-1 p-1 bg-gray-100 rounded-xl overflow-x-auto snap-x scroll-smooth select-none no-scrollbar" style="scrollbar-width: none; -ms-overflow-style: none;">
                        @foreach (\App\Models\MenuItem::types() as $key => $meta)
                            <button type="button" @click="if(!isMoved) { activeType = '{{ $key }}'; loadOptions('{{ $key }}') }" :class="activeType === '{{ $key }}' ? 'bg-white text-rj-purple shadow-sm' : 'text-gray-500 hover:text-gray-800'" class="rj-tab flex-1 justify-center px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 whitespace-nowrap flex-shrink-0 snap-start pointer-events-auto">
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

                <form method="POST" action="{{ route('admin.menus.items.store', $menu) }}" class="space-y-5 p-6">
                    @csrf
                    <input type="hidden" name="type" :value="activeType">

                    {{-- Custom --}}
                    <div x-show="activeType === 'custom'" class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                {{ __('Label') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="label" value="{{ old('label') }}" placeholder="{{ __('e.g. Home') }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-white hover:border-gray-400 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm transition">
                            @error('label') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        @php $locales = rj_available_locales(); @endphp
                        @if (count($locales) > 1)
                            <div x-data="{ openTranslations: false }" class="rounded-xl border border-gray-200 bg-gray-50/60">
                                <button type="button" @click="openTranslations = !openTranslations" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-semibold text-gray-700 hover:text-rj-purple transition">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-rj-purple" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                                        {{ __('Translations') }}
                                        <span class="text-xs font-bold bg-white border border-gray-200 text-gray-500 rounded-full px-2 py-0.5">{{ count($locales) - 1 }}</span>
                                    </span>
                                    <svg class="w-4 h-4 transition-transform" :class="openTranslations ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="openTranslations" x-cloak x-collapse>
                                    <div class="px-4 pb-4 pt-1 space-y-3">
                                        @foreach ($locales as $locale)
                                            @if ($locale === config('app.locale')) @continue @endif
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-600 mb-1">
                                                    {{ rj_lang_name($locale) }} <span class="uppercase text-gray-400 font-normal">({{ $locale }})</span>
                                                </label>
                                                <input type="text" name="translations[{{ $locale }}]" value="{{ old('translations.' . $locale) }}" placeholder="{{ __('Optional') }}" class="w-full px-4 py-2 rounded-xl border border-gray-300 bg-white focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm transition">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Obyekt secimi --}}
                    <div x-show="activeType !== 'custom'" class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Select') }} <span class="text-red-500">*</span></label>
                            <select name="object_id" x-model="selectedObjectId" @change="onObjectSelect($event)" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-white hover:border-gray-400 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm transition">
                                <option value="">{{ __('— Choose —') }}</option>
                                <template x-for="opt in options" :key="opt.id">
                                    <option :value="opt.id" x-text="opt.label"></option>
                                </template>
                            </select>
                            <p x-show="loadingOptions" class="text-xs text-gray-500 mt-2">{{ __('Loading...') }}</p>
                            <p x-show="activeType !== 'custom' && options.length === 0 && !loadingOptions" class="text-xs text-gray-500 mt-2">
                                {{ __('No items available yet.') }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('URL') }}</label>
                        <input type="text" name="url" value="{{ old('url') }}" x-model="url" placeholder="{{ __('https://example.com or /path') }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 hover:border-gray-300 focus:bg-white focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm font-mono transition">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Parent') }}</label>
                            <select name="parent_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-white hover:border-gray-400 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm transition">
                                <option value="">{{ __('— Top level —') }}</option>
                                @foreach ($menu->items->whereNull('parent_id') as $item)
                                    <option value="{{ $item->id }}" {{ old('parent_id') == $item->id ? 'selected' : '' }}>{{ $item->label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Target') }}</label>
                            <select name="target" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-white hover:border-gray-400 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm transition">
                                <option value="_self">{{ __('Same window') }}</option>
                                <option value="_blank">{{ __('New window') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-1">
                        <button type="submit" class="inline-flex items-center justify-center w-full gap-2 px-5 py-3 rounded-xl bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-bold hover:opacity-90 active:scale-[.99] focus-visible:ring-2 focus-visible:ring-rj-purple/20 transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            {{ __('Add to Menu') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- ---------- Menu Structure ---------- --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-gray-100 text-gray-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h10M4 18h7"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">{{ __('Menu Structure') }}</h3>
                            <p class="text-sm text-gray-500 mt-0.5">{{ __('Drag items to reorder. Items with children are collapsible.') }}</p>
                        </div>
                    </div>
                    <span class="bg-gray-100 text-gray-700 text-xs font-bold px-3 py-1.5 rounded-full whitespace-nowrap">{{ $menu->itemCount() }} {{ __('items') }}</span>
                </div>

                @if ($menu->items->isEmpty())
                    <div class="p-14 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-4 border border-dashed border-gray-300">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-900">{{ __('No items yet.') }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ __('Add your first item using the form above.') }}</p>
                    </div>
                @else
                    <div class="p-5 bg-gray-50/60">
                        <div id="menu-tree" class="space-y-2">
                            @foreach ($menu->items->whereNull('parent_id') as $item)
                                @include('admin.menus.partials.item-row', ['item' => $item, 'depth' => 0])
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

        </div>

        {{-- ============ Sag sutun ============ --}}
        <div class="space-y-6 min-w-0 lg:sticky lg:top-6">

            <form method="POST" action="{{ route('admin.menus.update', $menu) }}" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                @csrf
                @method('PUT')

                <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gray-100 text-gray-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900">{{ __('Menu Settings') }}</h3>
                </div>

                <div class="p-6 space-y-5">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Name') }}</label>
                        <input type="text" name="name" value="{{ $menu->name }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-white hover:border-gray-400 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Location') }}</label>
                        <select name="location" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-white hover:border-gray-400 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm transition">
                            <option value="">{{ __('— Not assigned —') }}</option>
                            @foreach ($locations as $key => $label)
                                <option value="{{ $key }}" {{ $menu->location === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Description') }}</label>
                        <input type="text" name="description" value="{{ $menu->description }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-white hover:border-gray-400 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm transition">
                    </div>

                    {{-- Active toggle (duzeldilib) --}}
                    <div x-data="{ active: {{ $menu->is_active ? 'true' : 'false' }} }"
                         :class="active ? 'border-rj-purple/30 bg-white shadow-sm' : 'border-gray-200 bg-gray-50'"
                         class="rounded-xl border p-4 transition-colors">
                        <label class="flex items-center justify-between gap-4 cursor-pointer">
                            <div class="min-w-0">
                                <span class="flex items-center gap-2 text-sm font-bold text-gray-900">{{ __('Active Menu') }}
                                    <span class="text-xs font-semibold" :class="active ? 'text-rj-purple' : 'text-gray-400'" x-text="active ? '{{ __('On') }}' : '{{ __('Off') }}'"></span>
                                </span>
                                <span class="block text-xs text-gray-500 mt-0.5">{{ __('Only one menu per location can be active.') }}</span>
                            </div>
                            <span class="rj-switch">
                                <input type="checkbox" name="is_active" value="1" x-model="active" {{ $menu->is_active ? 'checked' : '' }} class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 m-0">
                                <span class="rj-switch-track"></span>
                                <span class="rj-switch-fill bg-gradient-to-r from-rj-purple to-rj-pink" :style="active ? 'opacity:1' : 'opacity:0'"></span>
                                <span class="rj-switch-knob" :style="active ? 'transform: translateX(22px)' : 'transform: translateX(0)'"></span>
                            </span>
                        </label>
                    </div>

                </div>

                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/70">
                    <button type="submit" class="inline-flex items-center justify-center w-full gap-2 px-5 py-3 rounded-xl bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-bold hover:opacity-90 active:scale-[.99] focus-visible:ring-2 focus-visible:ring-rj-purple/20 transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
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