@extends('rjbuilder.layout')

@section('title', $template->name)

@section('content')
@php
    $widgetsData = collect(\App\Widgets\WidgetRegistry::all())->map(function ($w) {
        return [
            'id' => $w->id(),
            'name' => $w->name(),
            'category' => $w->category(),
            'schema' => $w->fullSchema(),
            'defaults' => $w->fullDefaults(),
        ];
    })->values()->toArray();

    $blocksData = $template->content['blocks'] ?? [];
@endphp

<div x-data="builder()" x-init="init()" class="h-screen flex flex-col" @dragend.window="endDrag()">

    <header class="h-14 bg-rj-dark-1 text-white flex items-center justify-between px-4 border-b border-white/10 flex-shrink-0">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.rjbuilder.edit', $template) }}" class="w-8 h-8 rounded-lg hover:bg-white/10 transition flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div class="h-6 w-px bg-white/20"></div>
            <div class="min-w-0">
                <div class="text-sm font-semibold truncate">{{ $template->name }}</div>
                <div class="text-[10px] text-gray-400 uppercase tracking-wider">{{ $template->typeLabel() }}</div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" @click="undo()" :disabled="!canUndo"
                    class="w-8 h-8 rounded-lg hover:bg-white/10 transition disabled:opacity-30 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
            </button>
            <button type="button" @click="redo()" :disabled="!canRedo"
                    class="w-8 h-8 rounded-lg hover:bg-white/10 transition disabled:opacity-30 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 10h-10a8 8 0 00-8 8v2M21 10l-6 6m6-6l-6-6"/></svg>
            </button>

            <div class="h-6 w-px bg-white/20"></div>

            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium uppercase"
                  :class="saved ? 'bg-green-500/20 text-green-400' : 'bg-amber-500/20 text-amber-400'"
                  x-text="saved ? '{{ __('Saved') }}' : '{{ __('Unsaved') }}'"></span>

            <button type="button" @click="save()" :disabled="saving"
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition disabled:opacity-50">
                <span x-text="saving ? '{{ __('Saving...') }}' : '{{ __('Save') }}'"></span>
            </button>
        </div>
    </header>

    <div class="flex-1 flex overflow-hidden">

        <aside class="w-72 bg-white border-r border-gray-200 flex flex-col flex-shrink-0">
            <div class="px-4 py-3 border-b border-gray-100">
                <div class="relative">
                    <svg class="w-3.5 h-3.5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" x-model="widgetSearch"
                           placeholder="{{ __('Search widgets...') }}"
                           class="w-full pl-9 pr-3 py-1.5 rounded-lg border border-gray-200 focus:border-rj-purple focus:ring-rj-purple text-xs">
                </div>
            </div>

            <div class="flex-1 overflow-y-auto rj-scrollbar p-3">
                @php $grouped = \App\Widgets\WidgetRegistry::grouped(); @endphp
                @php $categories = \App\Widgets\WidgetRegistry::categories(); @endphp

                @foreach ($categories as $categoryKey => $catMeta)
                    @php $widgets = $grouped[$categoryKey] ?? []; @endphp
                    @if (!empty($widgets))
                        <div x-show="categoryVisible('{{ $categoryKey }}')">
                            <div class="px-2 py-1.5 text-[10px] font-semibold uppercase tracking-wider text-gray-400 flex items-center gap-1.5">
                                <span>{{ $catMeta['icon'] }}</span>
                                <span>{{ $catMeta['label'] }}</span>
                            </div>

                            <div class="grid grid-cols-2 gap-2 mb-3">
                                @foreach ($widgets as $widget)
                                    <button type="button"
                                            draggable="true"
                                            @dragstart="startDragNew('{{ $widget->id() }}', $event)"
                                            @click="quickAdd('{{ $widget->id() }}')"
                                            x-show="widgetVisible('{{ strtolower($widget->name()) }}', '{{ $widget->id() }}')"
                                            class="group flex flex-col items-center justify-center gap-2 px-3 py-4 rounded-lg border border-gray-200 hover:border-rj-purple hover:bg-rj-purple/5 transition cursor-grab active:cursor-grabbing">
                                        <div class="w-9 h-9 rounded-lg bg-gray-100 group-hover:bg-rj-purple/10 flex items-center justify-center text-gray-600 group-hover:text-rj-purple transition">
                                            {!! $widget->icon() !!}
                                        </div>
                                        <div class="text-[11px] font-medium text-gray-700 group-hover:text-rj-purple text-center leading-tight">
                                            {{ $widget->name() }}
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </aside>

        <main class="flex-1 bg-gray-50 overflow-y-auto rj-scrollbar relative" style="overscroll-behavior: contain;">
            <div class="min-h-full py-12 px-6 pb-32">

                <div x-show="blocks.length === 0" class="max-w-5xl mx-auto">
                    <div class="text-center mb-10">
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ __('Select a wrap layout') }}</h2>
                        <p class="text-sm text-gray-500">{{ __('Choose a structure to start building.') }}</p>
                    </div>

                    <div class="flex items-center justify-center gap-3 flex-wrap mb-10">
                        <button type="button" @click="addLayout('1col')" class="group w-20 h-20 rounded-xl border-2 border-gray-200 bg-white hover:border-rj-purple hover:shadow-lg transition flex items-center justify-center" title="1 Column">
                            <div class="w-10 h-10 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                        </button>
                        <button type="button" @click="addLayout('2col')" class="group w-20 h-20 rounded-xl border-2 border-gray-200 bg-white hover:border-rj-purple hover:shadow-lg transition flex items-center justify-center gap-1" title="2 Columns">
                            <div class="w-4 h-10 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                            <div class="w-4 h-10 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                        </button>
                        <button type="button" @click="addLayout('3col')" class="group w-20 h-20 rounded-xl border-2 border-gray-200 bg-white hover:border-rj-purple hover:shadow-lg transition flex items-center justify-center gap-1" title="3 Columns">
                            <div class="w-3 h-10 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                            <div class="w-3 h-10 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                            <div class="w-3 h-10 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                        </button>
                        <button type="button" @click="addLayout('4col')" class="group w-20 h-20 rounded-xl border-2 border-gray-200 bg-white hover:border-rj-purple hover:shadow-lg transition flex items-center justify-center gap-1" title="4 Columns">
                            <div class="w-2.5 h-10 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                            <div class="w-2.5 h-10 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                            <div class="w-2.5 h-10 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                            <div class="w-2.5 h-10 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                        </button>
                        <button type="button" @click="addLayout('2row')" class="group w-20 h-20 rounded-xl border-2 border-gray-200 bg-white hover:border-rj-purple hover:shadow-lg transition flex flex-col items-center justify-center gap-1" title="2 Rows">
                            <div class="w-10 h-4 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                            <div class="w-10 h-4 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                        </button>
                        <button type="button" @click="addLayout('1plus2')" class="group w-20 h-20 rounded-xl border-2 border-gray-200 bg-white hover:border-rj-purple hover:shadow-lg transition flex flex-col items-center justify-center gap-1" title="1 + 2">
                            <div class="w-10 h-4 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                            <div class="flex gap-1">
                                <div class="h-4 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition" style="width:18px"></div>
                                <div class="h-4 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition" style="width:18px"></div>
                            </div>
                        </button>
                        <button type="button" @click="addLayout('2row3')" class="group w-20 h-20 rounded-xl border-2 border-gray-200 bg-white hover:border-rj-purple hover:shadow-lg transition flex flex-col items-center justify-center gap-1" title="2 Rows x 3">
                            <div class="flex gap-0.5">
                                <div class="w-2.5 h-4 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                                <div class="w-2.5 h-4 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                                <div class="w-2.5 h-4 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                            </div>
                            <div class="flex gap-0.5">
                                <div class="w-2.5 h-4 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                                <div class="w-2.5 h-4 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                                <div class="w-2.5 h-4 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                            </div>
                        </button>
                    </div>

                    <div class="text-center">
                        <div class="text-xs text-gray-400 uppercase tracking-wider mb-4">{{ __('or start from scratch') }}</div>
                        <button type="button" @click="addBlank()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            {{ __('Start Blank') }}
                        </button>
                    </div>
                </div>

                <div x-show="blocks.length > 0" class="max-w-6xl mx-auto">
                    <template x-for="(block, index) in blocks" :key="block.id">
                        <div>
                            <div @dragover.prevent="onDragOver($event, 'root', index)"
                                 @drop.prevent="onDrop($event, 'root', index)"
                                 :class="dragTarget === 'root:' + index ? 'h-10 opacity-100' : 'h-2 opacity-0'"
                                 class="transition-all flex items-center">
                                <div class="w-full h-1 bg-rj-purple rounded-full"></div>
                            </div>

                            <div x-html="renderBlockHtml(block, 'root', index)"></div>
                        </div>
                    </template>

                    <div @dragover.prevent="onDragOver($event, 'root', blocks.length)"
                         @drop.prevent="onDrop($event, 'root', blocks.length)"
                         :class="dragTarget === 'root:' + blocks.length ? 'h-10 opacity-100' : 'h-2 opacity-0'"
                         class="transition-all flex items-center">
                        <div class="w-full h-1 bg-rj-purple rounded-full"></div>
                    </div>

                    <div class="mt-10 mb-20 flex items-center justify-center">
                        <button type="button" @click="showLayoutPicker = true"
                                class="group flex items-center gap-3 px-6 py-4 rounded-2xl border-2 border-dashed border-gray-300 hover:border-rj-purple hover:bg-rj-purple/5 transition">
                            <div class="w-10 h-10 rounded-full bg-white border-2 border-gray-300 group-hover:border-rj-purple group-hover:bg-rj-purple/10 flex items-center justify-center transition">
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-rj-purple transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            </div>
                            <div class="text-left">
                                <div class="text-sm font-semibold text-gray-900 group-hover:text-rj-purple">{{ __('Add New Section') }}</div>
                                <div class="text-xs text-gray-500">{{ __('Choose a layout below') }}</div>
                            </div>
                        </button>
                    </div>
                </div>

            </div>
        </main>

        <aside class="w-80 bg-white border-l border-gray-200 flex flex-col flex-shrink-0">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                <div class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Settings') }}</div>
                <div x-show="selectedBlock" x-cloak class="text-[10px] text-gray-400 font-medium uppercase" x-text="currentBlockTypeLabel()"></div>
            </div>

            <div class="flex-1 overflow-y-auto rj-scrollbar p-4">

                <div x-show="!selectedBlock" class="h-full flex items-center justify-center">
                    <div class="text-center max-w-xs">
                        <div class="w-12 h-12 mx-auto rounded-lg bg-gray-100 flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                        </div>
                        <p class="text-xs text-gray-500">{{ __('Select a block to edit its settings.') }}</p>
                    </div>
                </div>

                <div x-show="selectedBlock" x-cloak class="space-y-4">
                    <template x-for="field in currentSchema" :key="field.name">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1.5" x-text="field.label"></label>

                            <template x-if="field.type === 'text'">
                                <input type="text" :value="currentSettings[field.name] || ''" @input="updateSetting(field.name, $event.target.value)" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                            </template>

                            <template x-if="field.type === 'textarea'">
                                <textarea :value="currentSettings[field.name] || ''" @input="updateSetting(field.name, $event.target.value)" rows="4" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm"></textarea>
                            </template>

                            <template x-if="field.type === 'select'">
                                <select :value="currentSettings[field.name] || ''" @change="updateSetting(field.name, $event.target.value)" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                                    <template x-for="(label, value) in field.options" :key="value">
                                        <option :value="value" x-text="label"></option>
                                    </template>
                                </select>
                            </template>

                            <template x-if="field.type === 'color'">
                                <div class="flex items-center gap-2">
                                    <input type="color" :value="currentSettings[field.name] || '#000000'" @input="updateSetting(field.name, $event.target.value)" class="w-10 h-9 rounded-lg border border-gray-300 cursor-pointer">
                                    <input type="text" :value="currentSettings[field.name] || ''" @input="updateSetting(field.name, $event.target.value)" class="flex-1 rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-xs font-mono">
                                </div>
                            </template>

                            <template x-if="field.type === 'media'">
                                <div>
                                    <div x-show="currentSettings[field.name]" class="mb-2 rounded-lg overflow-hidden border border-gray-200 relative">
                                        <img :src="currentSettings[field.name]" class="w-full h-24 object-cover">
                                        <button type="button" @click="updateSetting(field.name, '')" class="absolute top-1 right-1 w-6 h-6 rounded-full bg-red-600 text-white flex items-center justify-center">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    <button type="button" @click="openMediaPicker(field.name)" class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg border-2 border-dashed border-gray-300 text-gray-600 text-xs font-medium hover:border-rj-purple hover:bg-rj-purple/5 transition">
                                        <span x-text="currentSettings[field.name] ? '{{ __('Change Image') }}' : '{{ __('Select Image') }}'"></span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </template>

                    <div class="pt-4 border-t border-gray-100 flex items-center gap-2">
                        <button type="button" @click="duplicateSelected()" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg border border-gray-300 text-xs text-gray-700 hover:bg-gray-50 transition">
                            {{ __('Duplicate') }}
                        </button>
                        <button type="button" @click="removeSelected()" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg border border-red-200 text-xs text-red-600 hover:bg-red-50 transition">
                            {{ __('Delete') }}
                        </button>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    <div x-show="showLayoutPicker" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
         @keydown.escape.window="showLayoutPicker = false">
        <div x-show="showLayoutPicker" x-transition.opacity @click="showLayoutPicker = false" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

        <div x-show="showLayoutPicker"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl p-8">

            <div class="text-center mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Add New Section') }}</h2>
                <p class="text-sm text-gray-500">{{ __('Choose a layout for the new section.') }}</p>
            </div>

            <div class="flex items-center justify-center gap-3 flex-wrap">
                <button type="button" @click="addLayout('1col'); showLayoutPicker = false" class="group w-16 h-16 rounded-xl border-2 border-gray-200 bg-white hover:border-rj-purple hover:shadow-lg transition flex items-center justify-center" title="1 Column">
                    <div class="w-8 h-8 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                </button>
                <button type="button" @click="addLayout('2col'); showLayoutPicker = false" class="group w-16 h-16 rounded-xl border-2 border-gray-200 bg-white hover:border-rj-purple hover:shadow-lg transition flex items-center justify-center gap-0.5" title="2 Columns">
                    <div class="w-3 h-8 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                    <div class="w-3 h-8 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                </button>
                <button type="button" @click="addLayout('3col'); showLayoutPicker = false" class="group w-16 h-16 rounded-xl border-2 border-gray-200 bg-white hover:border-rj-purple hover:shadow-lg transition flex items-center justify-center gap-0.5" title="3 Columns">
                    <div class="w-2.5 h-8 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                    <div class="w-2.5 h-8 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                    <div class="w-2.5 h-8 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                </button>
                <button type="button" @click="addLayout('4col'); showLayoutPicker = false" class="group w-16 h-16 rounded-xl border-2 border-gray-200 bg-white hover:border-rj-purple hover:shadow-lg transition flex items-center justify-center gap-0.5" title="4 Columns">
                    <div class="w-2 h-8 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                    <div class="w-2 h-8 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                    <div class="w-2 h-8 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                    <div class="w-2 h-8 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                </button>
                <button type="button" @click="addLayout('2row'); showLayoutPicker = false" class="group w-16 h-16 rounded-xl border-2 border-gray-200 bg-white hover:border-rj-purple hover:shadow-lg transition flex flex-col items-center justify-center gap-0.5" title="2 Rows">
                    <div class="w-8 h-3.5 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                    <div class="w-8 h-3.5 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                </button>
                <button type="button" @click="addLayout('1plus2'); showLayoutPicker = false" class="group w-16 h-16 rounded-xl border-2 border-gray-200 bg-white hover:border-rj-purple hover:shadow-lg transition flex flex-col items-center justify-center gap-0.5" title="1 + 2">
                    <div class="w-8 h-3.5 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                    <div class="flex gap-0.5">
                        <div class="w-3.5 h-3.5 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                        <div class="w-3.5 h-3.5 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                    </div>
                </button>
                <button type="button" @click="addLayout('2row3'); showLayoutPicker = false" class="group w-16 h-16 rounded-xl border-2 border-gray-200 bg-white hover:border-rj-purple hover:shadow-lg transition flex flex-col items-center justify-center gap-0.5" title="2 Rows x 3">
                    <div class="flex gap-0.5">
                        <div class="w-2 h-3.5 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                        <div class="w-2 h-3.5 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                        <div class="w-2 h-3.5 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                    </div>
                    <div class="flex gap-0.5">
                        <div class="w-2 h-3.5 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                        <div class="w-2 h-3.5 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                        <div class="w-2 h-3.5 border-2 border-dashed border-gray-300 group-hover:border-rj-purple rounded transition"></div>
                    </div>
                </button>
            </div>

            <div class="mt-8 text-center">
                <button type="button" @click="showLayoutPicker = false" class="text-sm text-gray-500 hover:text-gray-700">
                    {{ __('Cancel') }}
                </button>
            </div>
        </div>
    </div>

    @include('admin.partials.media-picker')
</div>

<script>
function builder() {
    return {
        widgets: @json($widgetsData),
        blocks: @json($blocksData),
        selectedBlock: null,
        widgetSearch: '',
        saving: false,
        saved: true,
        history: [],
        historyIndex: -1,
        mediaPickerTarget: null,
        dragging: null,
        dragTarget: null,
        showLayoutPicker: false,

        get canUndo() { return this.historyIndex > 0; },
        get canRedo() { return this.historyIndex < this.history.length - 1; },

        get currentBlock() {
            if (!this.selectedBlock) return null;
            return this.findBlock(this.selectedBlock);
        },

        get currentSchema() {
            if (!this.currentBlock) return [];
            const w = this.widgets.find(x => x.id === this.currentBlock.type);
            return w ? w.schema : [];
        },

        get currentSettings() {
            if (!this.currentBlock) return {};
            return this.currentBlock.settings || {};
        },

        init() {
            this.pushHistory();
            setTimeout(() => { window.__builder = this; }, 50);
        },

        widgetVisible(name, id) {
            if (this.widgetSearch === '') return true;
            const q = this.widgetSearch.toLowerCase();
            return name.includes(q) || id.includes(q);
        },

        categoryVisible(category) {
            if (this.widgetSearch === '') return true;
            const q = this.widgetSearch.toLowerCase();
            return this.widgets.some(w => w.category === category && (w.name.toLowerCase().includes(q) || w.id.includes(q)));
        },

        blockLabel(type) {
            const w = this.widgets.find(x => x.id === type);
            return w ? w.name : type;
        },

        currentBlockTypeLabel() {
            return this.currentBlock ? this.blockLabel(this.currentBlock.type) : '';
        },

        generateId() {
            return 'blk_' + Math.random().toString(36).substr(2, 9);
        },

        findBlock(id, list) {
            list = list || this.blocks;
            for (const b of list) {
                if (b.id === id) return b;
                if (b.children && b.children.length) {
                    const found = this.findBlock(id, b.children);
                    if (found) return found;
                }
            }
            return null;
        },

        findBlockAndParent(id, list, parent) {
            list = list || this.blocks;
            parent = parent || null;
            for (let i = 0; i < list.length; i++) {
                if (list[i].id === id) return { block: list[i], parent: parent, index: i, list: list };
                if (list[i].children && list[i].children.length) {
                    const found = this.findBlockAndParent(id, list[i].children, list[i]);
                    if (found) return found;
                }
            }
            return null;
        },

        addLayout(preset) {
            const presets = {
                '1col': { cols: 1 },
                '2col': { cols: 2 },
                '3col': { cols: 3 },
                '4col': { cols: 4 },
                '2row': { cols: 1, rows: 2 },
                '1plus2': { special: true },
                '2row3': { rows: 2, rowCols: 3 },
            };

            const p = presets[preset];
            if (!p) return;

            const container = this.createBlock('container', {
                columns: String(p.cols || 1),
                gap: 'md',
                padding: 'md',
                align: 'stretch',
                width: 'boxed',
            });

            container.children = [];

            if (p.special) {
                container.settings.columns = '1';
                const topRow = this.createBlock('container', { columns: '1' });
                topRow.children = [this.createBlock('container', { columns: '1' })];
                container.children.push(topRow);

                const bottomRow = this.createBlock('container', { columns: '2' });
                bottomRow.children = [this.createBlock('container', { columns: '1' }), this.createBlock('container', { columns: '1' })];
                container.children.push(bottomRow);
            } else if (p.rowCols) {
                container.settings.columns = '1';
                for (let r = 0; r < p.rows; r++) {
                    const row = this.createBlock('container', { columns: String(p.rowCols) });
                    row.children = [];
                    for (let c = 0; c < p.rowCols; c++) {
                        row.children.push(this.createBlock('container', { columns: '1' }));
                    }
                    container.children.push(row);
                }
            } else if (p.rows && p.rows > 1) {
                container.settings.columns = '1';
                for (let r = 0; r < p.rows; r++) {
                    container.children.push(this.createBlock('container', { columns: '1' }));
                }
            } else {
                for (let i = 0; i < p.cols; i++) {
                    container.children.push(this.createBlock('container', { columns: '1' }));
                }
            }

            this.blocks.push(container);
            this.selectedBlock = container.id;
            this.markDirty();
            this.pushHistory();
        },

        addBlank() {
            const container = this.createBlock('container', {
                columns: '1',
                gap: 'md',
                padding: 'md',
                width: 'boxed',
            });
            container.children = [this.createBlock('container', { columns: '1' })];
            this.blocks.push(container);
            this.selectedBlock = container.id;
            this.markDirty();
            this.pushHistory();
        },

        createBlock(widgetId, settingsOverride) {
            const w = this.widgets.find(x => x.id === widgetId);
            const defaults = w ? JSON.parse(JSON.stringify(w.defaults || {})) : {};
            const settings = Object.assign({}, defaults, settingsOverride || {});

            return {
                id: this.generateId(),
                type: widgetId,
                settings: settings,
                children: [],
            };
        },

        quickAdd(widgetId) {
            if (this.selectedBlock) {
                const block = this.findBlock(this.selectedBlock);
                if (block && block.type === 'container') {
                    if (!block.children) block.children = [];
                    const newBlock = this.createBlock(widgetId);
                    let emptyIndex = block.children.findIndex(c => c.type === 'container' && (!c.children || c.children.length === 0));
                    if (emptyIndex === -1) {
                        block.children.push(newBlock);
                    } else {
                        block.children[emptyIndex] = newBlock;
                    }
                    this.selectedBlock = newBlock.id;
                    this.markDirty();
                    this.pushHistory();
                    return;
                }
            }
            this.insertAt(widgetId, this.blocks, this.blocks.length);
        },

        insertAt(widgetId, list, index) {
            const block = this.createBlock(widgetId);
            list.splice(index, 0, block);
            this.selectedBlock = block.id;
            this.markDirty();
            this.pushHistory();
        },

        startDragNew(widgetId, event) {
            this.dragging = { type: 'new', widgetId };
            event.dataTransfer.effectAllowed = 'copy';
            event.dataTransfer.setData('text/plain', widgetId);
        },

        onDragOver(event, parentKey, index) {
            event.preventDefault();
            this.dragTarget = parentKey + ':' + index;
        },

        onDrop(event, parentKey, index) {
            event.preventDefault();
            this.dragTarget = null;

            if (!this.dragging || this.dragging.type !== 'new') { this.dragging = null; return; }

            if (parentKey === 'root') {
                this.insertAt(this.dragging.widgetId, this.blocks, index);
            }
            this.dragging = null;
        },

        dropInto(containerId, slotIndex) {
            this.dragTarget = null;
            if (!this.dragging || this.dragging.type !== 'new') { this.dragging = null; return; }

            const container = this.findBlock(containerId);
            if (!container) { this.dragging = null; return; }

            if (!container.children) container.children = [];

            let slot = container.children[slotIndex];

            if (slot && slot.type === 'container') {
                if (!slot.children) slot.children = [];
                if (slot.children.length === 0) {
                    slot.children.push(this.createBlock(this.dragging.widgetId));
                    this.selectedBlock = slot.children[0].id;
                } else {
                    container.children.splice(slotIndex + 1, 0, this.createBlock(this.dragging.widgetId));
                }
            } else {
                const newBlock = this.createBlock(this.dragging.widgetId);
                container.children[slotIndex] = newBlock;
                this.selectedBlock = newBlock.id;
            }

            this.markDirty();
            this.pushHistory();
            this.dragging = null;
        },

        endDrag() {
            this.dragging = null;
            this.dragTarget = null;
        },

        selectBlock(id) { this.selectedBlock = id; },

        removeBlock(id) {
            const found = this.findBlockAndParent(id);
            if (!found) return;
            found.list.splice(found.index, 1);
            if (this.selectedBlock === id) this.selectedBlock = null;
            this.markDirty();
            this.pushHistory();
        },

        duplicateBlock(id) {
            const found = this.findBlockAndParent(id);
            if (!found) return;
            const copy = JSON.parse(JSON.stringify(found.block));
            this.reassignIds(copy);
            found.list.splice(found.index + 1, 0, copy);
            this.selectedBlock = copy.id;
            this.markDirty();
            this.pushHistory();
        },

        reassignIds(block) {
            block.id = this.generateId();
            if (block.children && block.children.length) {
                for (const child of block.children) this.reassignIds(child);
            }
        },

        moveUp(id) {
            const found = this.findBlockAndParent(id);
            if (!found || found.index === 0) return;
            [found.list[found.index - 1], found.list[found.index]] = [found.list[found.index], found.list[found.index - 1]];
            this.markDirty();
            this.pushHistory();
        },

        moveDown(id) {
            const found = this.findBlockAndParent(id);
            if (!found || found.index === found.list.length - 1) return;
            [found.list[found.index + 1], found.list[found.index]] = [found.list[found.index], found.list[found.index + 1]];
            this.markDirty();
            this.pushHistory();
        },

        updateSetting(name, value) {
            if (!this.currentBlock) return;
            if (!this.currentBlock.settings) this.currentBlock.settings = {};
            this.currentBlock.settings[name] = value;
            this.markDirty();
        },

        duplicateSelected() { if (this.selectedBlock) this.duplicateBlock(this.selectedBlock); },
        removeSelected() { if (this.selectedBlock) this.removeBlock(this.selectedBlock); },

        openMediaPicker(fieldName) {
            this.mediaPickerTarget = fieldName;
            const mockInput = document.createElement('input');
            mockInput.type = 'hidden';
            const handler = (e) => {
                this.updateSetting(this.mediaPickerTarget, e.detail.url);
                window.removeEventListener('media-selected', handler);
            };
            window.addEventListener('media-selected', handler);
            window.dispatchEvent(new CustomEvent('open-media-picker', { detail: { value: mockInput } }));
        },

        renderBlockHtml(block, parentKey, index) {
            const w = this.widgets.find(x => x.id === block.type);
            if (!w) return '<div class="p-3 text-xs text-red-500">Unknown widget</div>';

            const isSelected = this.selectedBlock === block.id;
            let innerHtml = block.type === 'container' ? this.renderContainer(block) : this.renderSimpleWidget(block);

            const anim = block.settings && block.settings._animation && block.settings._animation !== 'none'
                ? this.animationWrapper(innerHtml, block.settings)
                : innerHtml;

            const bid = block.id;

            return `
                <div class="relative transition" onclick="event.stopPropagation(); window.__builder.selectBlock('${bid}')">
                    <div style="position:absolute;top:-14px;left:50%;transform:translateX(-50%);z-index:30;display:flex;align-items:center;gap:2px;background:#1a1030;color:#fff;border-radius:9999px;padding:2px 6px;box-shadow:0 4px 12px rgba(0,0,0,0.2);opacity:${isSelected ? 1 : 0};pointer-events:${isSelected ? 'auto' : 'none'};transition:opacity 0.15s;">
                        <span style="font-size:9px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;padding:0 4px;color:#a855f7;">${this.blockLabel(block.type)}</span>
                        <button type="button" onclick="event.stopPropagation(); window.__builder.moveUp('${bid}')" style="width:20px;height:20px;border-radius:9999px;background:transparent;border:0;color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:12px;line-height:1;" onmouseover="this.style.background='rgba(255,255,255,0.15)'" onmouseout="this.style.background='transparent'" title="Move up">↑</button>
                        <button type="button" onclick="event.stopPropagation(); window.__builder.moveDown('${bid}')" style="width:20px;height:20px;border-radius:9999px;background:transparent;border:0;color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:12px;line-height:1;" onmouseover="this.style.background='rgba(255,255,255,0.15)'" onmouseout="this.style.background='transparent'" title="Move down">↓</button>
                        <button type="button" onclick="event.stopPropagation(); window.__builder.duplicateBlock('${bid}')" style="width:20px;height:20px;border-radius:9999px;background:transparent;border:0;color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:12px;line-height:1;" onmouseover="this.style.background='rgba(255,255,255,0.15)'" onmouseout="this.style.background='transparent'" title="Duplicate">⧉</button>
                        <button type="button" onclick="event.stopPropagation(); window.__builder.removeBlock('${bid}')" style="width:20px;height:20px;border-radius:9999px;background:transparent;border:0;color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:12px;line-height:1;" onmouseover="this.style.background='rgba(239,68,68,0.5)'" onmouseout="this.style.background='transparent'" title="Delete">✕</button>
                    </div>
                    <div style="${isSelected ? 'outline:2px solid #7c3aed; outline-offset:-2px;' : ''} border-radius:12px;">${anim}</div>
                </div>
            `;
        },

        renderContainer(block) {
            const settings = block.settings || {};
            const cols = parseInt(settings.columns) || 1;
            const gapMap = { none: '0', sm: '0.5rem', md: '1rem', lg: '1.5rem', xl: '2rem' };
            const paddingMap = { none: '0', sm: '0.5rem', md: '1rem', lg: '1.5rem', xl: '2.5rem' };
            const bg = settings.background || 'transparent';
            const gap = gapMap[settings.gap] || '1rem';
            const padding = paddingMap[settings.padding] || '1rem';
            const minHeight = settings.min_height && settings.min_height !== 'auto' ? { sm: '200px', md: '400px', lg: '600px' }[settings.min_height] : 'auto';
            const border = settings.border === 'solid' ? '2px solid #e5e7eb' : settings.border === 'dashed' ? '2px dashed #e5e7eb' : 'none';
            const alignMap = { start: 'flex-start', center: 'center', end: 'flex-end', stretch: 'stretch' };
            const align = alignMap[settings.align] || 'stretch';

            const isFullWidth = settings.width === 'full';

            const children = block.children || [];
            const bid = block.id;

            let slotsHtml = '';
            for (let i = 0; i < cols; i++) {
                const child = children[i];
                let inner = '';

                if (child) {
                    inner = this.renderBlockHtml(child, bid, i);
                } else {
                    inner = `
                        <div class="min-h-[80px] flex flex-col items-center justify-center text-gray-300">
                            <svg class="w-5 h-5 mb-1 pointer-events-none" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span class="text-[9px] uppercase tracking-wider pointer-events-none">${i + 1}</span>
                        </div>
                    `;
                }

                slotsHtml += `
                    <div class="relative min-h-[80px] rounded-lg border-2 border-dashed transition"
                         :class="dragTarget === '${bid}:${i}' ? 'border-rj-purple bg-rj-purple/10' : 'border-gray-200 hover:border-gray-300'"
                         ondragover="event.preventDefault(); event.stopPropagation(); window.__builder.dragTarget = '${bid}:${i}';"
                         ondrop="event.preventDefault(); event.stopPropagation(); window.__builder.dropInto('${bid}', ${i});"
                         ondragleave="window.__builder.dragTarget = null;">
                        ${inner}
                    </div>
                `;
            }

            const outerStyle = isFullWidth
                ? `background:${bg}; padding:${padding}; border:${border}; min-height:${minHeight}; width:100%; margin:0;`
                : `background:${bg}; padding:${padding}; border:${border}; min-height:${minHeight}; max-width:1100px; margin:0 auto; width:100%;`;

            return `
                <div class="rounded-xl" style="${outerStyle}">
                    <div style="display:grid; grid-template-columns: repeat(${cols}, minmax(0, 1fr)); gap:${gap}; align-items:${align};">
                        ${slotsHtml}
                    </div>
                </div>
            `;
        },

        renderSimpleWidget(block) {
            const settings = block.settings || {};
            const escape = (s) => { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; };

            if (block.type === 'heading') {
                const level = settings.level || 'h2';
                const sizes = { h1: 'text-3xl', h2: 'text-2xl', h3: 'text-xl', h4: 'text-lg', h5: 'text-base', h6: 'text-sm' };
                const aligns = { left: 'text-left', center: 'text-center', right: 'text-right' };
                return `<${level} class="${sizes[level] || 'text-2xl'} font-bold ${aligns[settings.align] || 'text-left'} py-3 px-4" style="color:${settings.color || '#111827'}">${escape(settings.text || 'Heading')}</${level}>`;
            }

            if (block.type === 'text') {
                const aligns = { left: 'text-left', center: 'text-center', right: 'text-right', justify: 'text-justify' };
                const sizes = { sm: 'text-sm', base: 'text-base', lg: 'text-lg', xl: 'text-xl' };
                return `<p class="${sizes[settings.size] || 'text-base'} ${aligns[settings.align] || 'text-left'} py-3 px-4 leading-relaxed" style="color:${settings.color || '#374151'}; white-space: pre-wrap;">${escape(settings.text || 'Text')}</p>`;
            }

            if (block.type === 'image') {
                if (!settings.url) return `<div class="py-8 text-center text-gray-400 text-xs">${escape('Image')}</div>`;
                const aligns = { left: 'mr-auto', center: 'mx-auto', right: 'ml-auto' };
                const widths = { auto: 'w-auto', full: 'w-full', half: 'w-1/2', third: 'w-1/3' };
                const rounded = { none: 'rounded-none', sm: 'rounded', md: 'rounded-lg', lg: 'rounded-2xl', full: 'rounded-full' };
                return `<figure class="py-3 px-4"><img src="${settings.url}" alt="${escape(settings.alt || '')}" class="${widths[settings.width] || 'w-full'} ${rounded[settings.rounded] || 'rounded-lg'} ${aligns[settings.align] || 'mx-auto'}"></figure>`;
            }

            const w = this.widgets.find(x => x.id === block.type);
            return `<div class="p-4 text-center text-xs text-gray-500">${w ? w.name : block.type}</div>`;
        },

        animationWrapper(html, settings) {
            const dur = settings._animation_duration || 'md';
            const delay = settings._animation_delay || '0';
            const durations = { sm: '300ms', md: '600ms', lg: '1000ms', xl: '1500ms' };
            let style = `animation-duration: ${durations[dur] || '600ms'};`;
            if (delay !== '0') style += ` animation-delay: ${delay};`;
            return `<div class="rj-animate rj-animate-${settings._animation}" style="${style}">${html}</div>`;
        },

        markDirty() { this.saved = false; },

        pushHistory() {
            this.history = this.history.slice(0, this.historyIndex + 1);
            this.history.push(JSON.stringify(this.blocks));
            this.historyIndex = this.history.length - 1;
            if (this.history.length > 50) { this.history.shift(); this.historyIndex--; }
        },

        undo() {
            if (!this.canUndo) return;
            this.historyIndex--;
            this.blocks = JSON.parse(this.history[this.historyIndex]);
            this.selectedBlock = null;
            this.markDirty();
        },

        redo() {
            if (!this.canRedo) return;
            this.historyIndex++;
            this.blocks = JSON.parse(this.history[this.historyIndex]);
            this.selectedBlock = null;
            this.markDirty();
        },

        async save() {
            this.saving = true;
            try {
                const res = await fetch('{{ route('admin.rjbuilder.update', $template) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-HTTP-Method-Override': 'PUT',
                    },
                    body: JSON.stringify({
                        name: '{{ $template->name }}',
                        content: { blocks: this.blocks },
                        status: '{{ $template->status }}',
                    }),
                });
                if (res.ok || res.redirected) this.saved = true;
            } catch (e) { console.error(e); }
            finally { this.saving = false; }
        },
    };
}
</script>
@endsection