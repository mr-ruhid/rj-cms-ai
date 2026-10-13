@extends('rjbuilder.layout')

@section('title', $template->name)

@section('content')
<div x-data="builder()" x-init="init()" class="h-screen flex flex-col">

    <header class="h-14 bg-rj-dark-1 text-white flex items-center justify-between px-4 border-b border-white/10 flex-shrink-0">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.rjbuilder.edit', $template) }}"
               class="inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-white/10 transition"
               title="{{ __('Back') }}">
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
                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-white/10 transition disabled:opacity-30 disabled:cursor-not-allowed"
                    title="{{ __('Undo') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
            </button>

            <button type="button" @click="redo()" :disabled="!canRedo"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-white/10 transition disabled:opacity-30 disabled:cursor-not-allowed"
                    title="{{ __('Redo') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 10h-10a8 8 0 00-8 8v2M21 10l-6 6m6-6l-6-6"/></svg>
            </button>

            <div class="h-6 w-px bg-white/20"></div>

            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium uppercase tracking-wider"
                  :class="saved ? 'bg-green-500/20 text-green-400' : 'bg-amber-500/20 text-amber-400'">
                <span x-text="saved ? '{{ __('Saved') }}' : '{{ __('Unsaved') }}'"></span>
            </span>

            <button type="button" @click="save()" :disabled="saving"
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition disabled:opacity-50">
                <svg x-show="!saving" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                <svg x-show="saving" x-cloak class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
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

                @if (empty($grouped))
                    <div class="p-6 text-center text-xs text-gray-400">
                        {{ __('No widgets available.') }}
                    </div>
                @else
                    @foreach ($grouped as $category => $widgets)
                        @php $cat = $categories[$category] ?? ['label' => $category, 'icon' => '📦']; @endphp
                        <div x-show="matchesCategory('{{ $category }}') || widgetSearch === ''" x-cloak>
                            <div class="px-2 py-1.5 text-[10px] font-semibold uppercase tracking-wider text-gray-400 flex items-center gap-1.5">
                                <span>{{ $cat['icon'] }}</span>
                                <span>{{ $cat['label'] }}</span>
                            </div>

                            <div class="grid grid-cols-2 gap-2 mb-3">
                                @foreach ($widgets as $widget)
                                    <button type="button"
                                            @click="addWidget('{{ $widget->id() }}')"
                                            x-show="widgetMatches('{{ strtolower($widget->name()) }}', '{{ $widget->id() }}')"
                                            class="group flex flex-col items-center justify-center gap-2 px-3 py-4 rounded-lg border border-gray-200 hover:border-rj-purple hover:bg-rj-purple/5 transition">
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
                    @endforeach

                    <div x-show="noMatches" x-cloak class="p-6 text-center text-xs text-gray-400">
                        {{ __('No widgets match your search.') }}
                    </div>
                @endif
            </div>
        </aside>

        <main class="flex-1 bg-gray-100 overflow-y-auto rj-scrollbar">
            <div class="min-h-full p-6">

                <div x-show="blocks.length === 0"
                     class="min-h-[calc(100vh-160px)] flex items-center justify-center">
                    <div class="text-center max-w-md">
                        <div class="w-20 h-20 mx-auto rounded-2xl bg-white border-2 border-dashed border-gray-300 flex items-center justify-center mb-4">
                            <svg class="w-9 h-9 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <h3 class="text-base font-semibold text-gray-900 mb-1">{{ __('Start building') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('Click a widget on the left panel to add it to your :type.', ['type' => strtolower($template->typeLabel())]) }}</p>
                    </div>
                </div>

                <div x-show="blocks.length > 0" class="max-w-4xl mx-auto space-y-2">
                    <template x-for="(block, index) in blocks" :key="block.id">
                        <div @click="selectBlock(block.id)"
                             :class="selectedBlock === block.id ? 'ring-2 ring-rj-purple shadow-lg' : 'hover:shadow-md'"
                             class="relative bg-white rounded-lg border border-gray-200 transition group cursor-pointer">

                            <div x-show="selectedBlock === block.id || hoveredBlock === block.id"
                                 class="absolute -top-3 left-1/2 -translate-x-1/2 z-10 flex items-center gap-1 bg-rj-dark-1 text-white rounded-full px-2 py-1 shadow-lg">
                                <span class="text-[10px] font-medium uppercase tracking-wider px-1.5" x-text="blockLabel(block.type)"></span>

                                <div class="w-px h-3 bg-white/20"></div>

                                <button type="button" @click.stop="moveUp(index)" :disabled="index === 0"
                                        class="w-5 h-5 rounded-full hover:bg-white/10 flex items-center justify-center disabled:opacity-30"
                                        title="{{ __('Move up') }}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
                                </button>

                                <button type="button" @click.stop="moveDown(index)" :disabled="index === blocks.length - 1"
                                        class="w-5 h-5 rounded-full hover:bg-white/10 flex items-center justify-center disabled:opacity-30"
                                        title="{{ __('Move down') }}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                </button>

                                <button type="button" @click.stop="duplicateBlock(index)"
                                        class="w-5 h-5 rounded-full hover:bg-white/10 flex items-center justify-center"
                                        title="{{ __('Duplicate') }}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </button>

                                <button type="button" @click.stop="removeBlock(index)"
                                        class="w-5 h-5 rounded-full hover:bg-red-500/30 flex items-center justify-center"
                                        title="{{ __('Delete') }}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            <div class="p-2 pointer-events-none"
                                 @mouseenter="hoveredBlock = block.id"
                                 @mouseleave="hoveredBlock = null"
                                 x-html="renderBlock(block)"></div>
                        </div>
                    </template>
                </div>

            </div>
        </main>

        <aside class="w-80 bg-white border-l border-gray-200 flex flex-col flex-shrink-0">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                <div class="text-xs font-semibold uppercase tracking-wider text-gray-500">
                    {{ __('Settings') }}
                </div>
                <div x-show="selectedBlock" x-cloak class="text-[10px] text-gray-400 font-medium uppercase tracking-wider" x-text="currentBlockTypeLabel()"></div>
            </div>

            <div class="flex-1 overflow-y-auto rj-scrollbar p-4">

                <div x-show="!selectedBlock" class="h-full flex items-center justify-center">
                    <div class="text-center max-w-xs">
                        <div class="w-12 h-12 mx-auto rounded-lg bg-gray-100 flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                        </div>
                        <p class="text-xs text-gray-500">{{ __('Select a block on the canvas to edit its settings.') }}</p>
                    </div>
                </div>

                <div x-show="selectedBlock" x-cloak class="space-y-4">
                    <template x-for="field in currentSchema" :key="field.name">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1.5" x-text="field.label"></label>

                            <template x-if="field.type === 'text'">
                                <input type="text" :value="currentSettings[field.name] || ''"
                                       @input="updateSetting(field.name, $event.target.value)"
                                       class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                            </template>

                            <template x-if="field.type === 'textarea'">
                                <textarea :value="currentSettings[field.name] || ''"
                                          @input="updateSetting(field.name, $event.target.value)"
                                          rows="4"
                                          class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm"></textarea>
                            </template>

                            <template x-if="field.type === 'select'">
                                <select :value="currentSettings[field.name] || ''"
                                        @change="updateSetting(field.name, $event.target.value)"
                                        class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                                    <template x-for="(label, value) in field.options" :key="value">
                                        <option :value="value" x-text="label"></option>
                                    </template>
                                </select>
                            </template>

                            <template x-if="field.type === 'color'">
                                <div class="flex items-center gap-2">
                                    <input type="color" :value="currentSettings[field.name] || '#000000'"
                                           @input="updateSetting(field.name, $event.target.value)"
                                           class="w-10 h-9 rounded-lg border border-gray-300 cursor-pointer">
                                    <input type="text" :value="currentSettings[field.name] || ''"
                                           @input="updateSetting(field.name, $event.target.value)"
                                           class="flex-1 rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-xs font-mono">
                                </div>
                            </template>

                            <template x-if="field.type === 'media'">
                                <div>
                                    <div x-show="currentSettings[field.name]" class="mb-2 rounded-lg overflow-hidden border border-gray-200 relative">
                                        <img :src="currentSettings[field.name]" class="w-full h-24 object-cover">
                                        <button type="button" @click="updateSetting(field.name, '')"
                                                class="absolute top-1 right-1 w-6 h-6 rounded-full bg-red-600 text-white flex items-center justify-center hover:bg-red-700 transition">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>

                                    <button type="button"
                                            @click="openMediaPicker(field.name)"
                                            class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg border-2 border-dashed border-gray-300 text-gray-600 text-xs font-medium hover:border-rj-purple hover:bg-rj-purple/5 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span x-text="currentSettings[field.name] ? '{{ __('Change Image') }}' : '{{ __('Select Image') }}'"></span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </template>

                    <div class="pt-4 border-t border-gray-100 flex items-center gap-2">
                        <button type="button" @click="duplicateSelected()"
                                class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg border border-gray-300 text-xs text-gray-700 hover:bg-gray-50 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            {{ __('Duplicate') }}
                        </button>
                        <button type="button" @click="removeSelected()"
                                class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg border border-red-200 text-xs text-red-600 hover:bg-red-50 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                            {{ __('Delete') }}
                        </button>
                    </div>
                </div>

            </div>
        </aside>

    </div>

    @include('admin.partials.media-picker')

</div>

@php
    $widgetsData = collect(\App\Widgets\WidgetRegistry::all())->map(function ($w) {
        return [
            'id' => $w->id(),
            'name' => $w->name(),
            'category' => $w->category(),
            'schema' => $w->schema(),
            'defaults' => $w->defaults(),
        ];
    })->values()->toArray();

    $blocksData = $template->content['blocks'] ?? [];
@endphp

<script>
function builder() {
    const widgets = @json($widgetsData);

    return {
        widgets: widgets,
        blocks: @json($blocksData),
        selectedBlock: null,
        hoveredBlock: null,
        widgetSearch: '',
        saving: false,
        saved: true,
        history: [],
        historyIndex: -1,
        mediaPickerTarget: null,

        get canUndo() { return this.historyIndex > 0; },
        get canRedo() { return this.historyIndex < this.history.length - 1; },

        get noMatches() {
            if (this.widgetSearch === '') return false;
            const q = this.widgetSearch.toLowerCase();
            return !this.widgets.some(w => w.name.toLowerCase().includes(q) || w.id.includes(q));
        },

        get currentBlock() {
            if (!this.selectedBlock) return null;
            return this.blocks.find(b => b.id === this.selectedBlock) || null;
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
        },

        matchesCategory(category) {
            if (this.widgetSearch === '') return true;
            return true;
        },

        widgetMatches(name, id) {
            if (this.widgetSearch === '') return true;
            const q = this.widgetSearch.toLowerCase();
            return name.includes(q) || id.includes(q);
        },

        blockLabel(type) {
            const w = this.widgets.find(x => x.id === type);
            return w ? w.name : type;
        },

        currentBlockTypeLabel() {
            if (!this.currentBlock) return '';
            return this.blockLabel(this.currentBlock.type);
        },

        generateId() {
            return 'blk_' + Math.random().toString(36).substr(2, 9);
        },

        addWidget(widgetId) {
            const w = this.widgets.find(x => x.id === widgetId);
            if (!w) return;

            const block = {
                id: this.generateId(),
                type: widgetId,
                settings: JSON.parse(JSON.stringify(w.defaults || {})),
            };

            this.blocks.push(block);
            this.selectedBlock = block.id;
            this.markDirty();
            this.pushHistory();
        },

        selectBlock(id) {
            this.selectedBlock = id;
        },

        removeBlock(index) {
            this.blocks.splice(index, 1);
            if (!this.blocks.find(b => b.id === this.selectedBlock)) {
                this.selectedBlock = null;
            }
            this.markDirty();
            this.pushHistory();
        },

        duplicateBlock(index) {
            const original = this.blocks[index];
            const copy = {
                id: this.generateId(),
                type: original.type,
                settings: JSON.parse(JSON.stringify(original.settings || {})),
            };
            this.blocks.splice(index + 1, 0, copy);
            this.selectedBlock = copy.id;
            this.markDirty();
            this.pushHistory();
        },

        moveUp(index) {
            if (index === 0) return;
            const tmp = this.blocks[index];
            this.blocks[index] = this.blocks[index - 1];
            this.blocks[index - 1] = tmp;
            this.markDirty();
            this.pushHistory();
        },

        moveDown(index) {
            if (index === this.blocks.length - 1) return;
            const tmp = this.blocks[index];
            this.blocks[index] = this.blocks[index + 1];
            this.blocks[index + 1] = tmp;
            this.markDirty();
            this.pushHistory();
        },

        updateSetting(name, value) {
            if (!this.currentBlock) return;
            if (!this.currentBlock.settings) this.currentBlock.settings = {};
            this.currentBlock.settings[name] = value;
            this.markDirty();
        },

        duplicateSelected() {
            const index = this.blocks.findIndex(b => b.id === this.selectedBlock);
            if (index !== -1) this.duplicateBlock(index);
        },

        removeSelected() {
            const index = this.blocks.findIndex(b => b.id === this.selectedBlock);
            if (index !== -1) this.removeBlock(index);
        },

        openMediaPicker(fieldName) {
            this.mediaPickerTarget = fieldName;

            const mockInput = document.createElement('input');
            mockInput.type = 'hidden';

            const handler = (e) => {
                this.updateSetting(this.mediaPickerTarget, e.detail.url);
                window.removeEventListener('media-selected', handler);
            };
            window.addEventListener('media-selected', handler);

            window.dispatchEvent(new CustomEvent('open-media-picker', {
                detail: { value: mockInput }
            }));
        },

        renderBlock(block) {
            const w = this.widgets.find(x => x.id === block.type);
            if (!w) return '<div class="p-3 text-xs text-red-500">Unknown widget: ' + block.type + '</div>';

            const settings = block.settings || {};

            if (block.type === 'heading') {
                const level = settings.level || 'h2';
                const sizes = { h1: 'text-3xl', h2: 'text-2xl', h3: 'text-xl', h4: 'text-lg', h5: 'text-base', h6: 'text-sm' };
                const aligns = { left: 'text-left', center: 'text-center', right: 'text-right' };
                return '<' + level + ' class="' + (sizes[level] || 'text-2xl') + ' font-bold ' + (aligns[settings.align] || 'text-left') + ' py-3 px-4" style="color:' + (settings.color || '#111827') + '">' + this.escapeHtml(settings.text || 'Heading') + '</' + level + '>';
            }

            if (block.type === 'text') {
                const aligns = { left: 'text-left', center: 'text-center', right: 'text-right', justify: 'text-justify' };
                const sizes = { sm: 'text-sm', base: 'text-base', lg: 'text-lg', xl: 'text-xl' };
                return '<p class="' + (sizes[settings.size] || 'text-base') + ' ' + (aligns[settings.align] || 'text-left') + ' py-3 px-4 leading-relaxed" style="color:' + (settings.color || '#374151') + '; white-space: pre-wrap;">' + this.escapeHtml(settings.text || 'Text') + '</p>';
            }

            if (block.type === 'image') {
                if (!settings.url) {
                    return '<div class="p-8 text-center text-gray-400 text-xs">No image selected</div>';
                }
                const aligns = { left: 'mr-auto', center: 'mx-auto', right: 'ml-auto' };
                const widths = { auto: 'w-auto', full: 'w-full', half: 'w-1/2', third: 'w-1/3' };
                const rounded = { none: 'rounded-none', sm: 'rounded', md: 'rounded-lg', lg: 'rounded-2xl', full: 'rounded-full' };
                return '<figure class="py-3 px-4"><img src="' + settings.url + '" alt="' + this.escapeHtml(settings.alt || '') + '" class="' + (widths[settings.width] || 'w-full') + ' ' + (rounded[settings.rounded] || 'rounded-lg') + ' ' + (aligns[settings.align] || 'mx-auto') + '"></figure>';
            }

            return '<div class="p-4 text-center text-xs text-gray-500">' + w.name + '</div>';
        },

        escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        },

        markDirty() {
            this.saved = false;
        },

        pushHistory() {
            this.history = this.history.slice(0, this.historyIndex + 1);
            this.history.push(JSON.stringify(this.blocks));
            this.historyIndex = this.history.length - 1;

            if (this.history.length > 50) {
                this.history.shift();
                this.historyIndex--;
            }
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

                if (res.ok || res.redirected) {
                    this.saved = true;
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.saving = false;
            }
        },
    };
}
</script>
@endsection