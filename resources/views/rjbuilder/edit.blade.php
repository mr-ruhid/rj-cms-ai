@extends('rjbuilder.layout')

@section('title', $template->name)

@php
    $widgetsData = collect(\App\Widgets\WidgetRegistry::all())->map(function ($w) {
        return [
            'id' => $w->id(),
            'name' => $w->name(),
            'category' => $w->category(),
            'icon' => $w->icon(),
            'schema' => $w->fullSchema(),
            'defaults' => $w->fullDefaults(),
        ];
    })->values()->toArray();

    $categoriesData = \App\Widgets\WidgetRegistry::categories();
    $blocksData = $template->content['blocks'] ?? [];
    $templateType = $template->type;
    $locales = rj_available_locales();
    $baseLocale = config('app.locale');

    $i18n = collect([
        'Add element', 'Sections', 'Navigator', 'History', 'Settings', 'Edit element', 'Back', 'Undo', 'Redo',
        'Preview', 'Edit', 'Save', 'Saving...', 'Saved', 'Unsaved', 'Desktop', 'Tablet', 'Mobile',
        'Search elements...', 'All', 'Favourite elements', 'Recently used', 'Add to favourites', 'Remove from favourites',
        'Collect favourite elements in one place by clicking the star on any element.', 'No elements found.',
        'Layouts', 'Ready-made sections', 'Click a layout to add it to the end of the page.',
        'Content', 'Style', 'Motion', 'No options in this tab.', 'Duplicate', 'Copy', 'Paste', 'Delete', 'Select parent',
        'Move up', 'Move down', 'Reset to default', 'Replay animation', 'Hover effects and scroll-triggered animations are visible in Preview.',
        'Select a block to edit its settings.', 'Double-click a heading or text to edit it on the canvas.',
        'No layers yet.', 'Edit', 'Nothing in history yet.', 'Current', 'now',
        'Editor', 'Show hover outlines', 'Auto-save every 30 seconds', 'Canvas zoom', 'Keyboard shortcuts', 'Template',
        'Select a layout', 'Choose a structure to start building.', 'or start from scratch', 'Start Blank', 'Add New Section',
        'Choose a layout for the new section.', 'Cancel', 'Back to editor', 'Replay animations', 'Full width',
        'Editing in', 'fields marked 🌐 are translated; others are shared.', 'Edit content in',
        'Select Image', 'Change Image', 'Copied', 'Pasted', 'Nothing to paste', 'Auto-saved', 'Save failed', 'Network error — not saved',
        'Session expired — reload the page and try again', 'Added', 'Moved', 'Deleted', 'Duplicated', 'Edited', 'Initial state',
        'Hidden on', 'Image', 'Unknown widget', 'Drop here', 'Save (Ctrl+S)', 'Close panel', 'Open panel', 'Logo',
    ])->unique()->mapWithKeys(fn ($s) => [$s => __($s)])->toArray();
@endphp

@section('content')

@include('rjbuilder.partials.styles')

<div x-data="builder()" x-init="init()" class="rj-app"
     @dragend.window="endDrag()"
     @keydown.window="onKey($event)"
     @beforeunload.window="if (!saved) { $event.preventDefault(); $event.returnValue = ''; }">

    {{-- ═════════════ TOP BAR (editor) ═════════════ --}}
    <header class="rj-top" x-show="!preview">
        <div class="rj-grp" style="flex:1;">
            <a href="{{ route('admin.rjbuilder.edit', $template) }}" class="rj-ib" :title="t('Back')">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div class="rj-sep"></div>

            <button type="button" class="rj-ib" :data-active="shownPanel === 'add' && panelOpen" @click="openPanel('add')" :title="t('Add element')">
                <svg fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/></svg>
            </button>
            <button type="button" class="rj-ib" :data-active="shownPanel === 'sections' && panelOpen" @click="openPanel('sections')" :title="t('Sections')">
                <svg fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
            </button>
            <button type="button" class="rj-ib" :data-active="shownPanel === 'navigator' && panelOpen" @click="openPanel('navigator')" :title="t('Navigator')">
                <svg fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 3l9 5-9 5-9-5 9-5z"/><path d="M3 13l9 5 9-5"/></svg>
            </button>
            <button type="button" class="rj-ib" :data-active="shownPanel === 'history' && panelOpen" @click="openPanel('history')" :title="t('History')">
                <svg fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M3 12a9 9 0 109-9 9 9 0 00-6.4 2.6L3 8"/><path d="M3 3v5h5M12 7v5l3 2"/></svg>
            </button>
            <button type="button" class="rj-ib" :data-active="shownPanel === 'settings' && panelOpen" @click="openPanel('settings')" :title="t('Settings')">
                <svg fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 6h9M17 6h3M4 12h3M11 12h9M4 18h11M19 18h1"/><circle cx="15" cy="6" r="2"/><circle cx="9" cy="12" r="2"/><circle cx="17" cy="18" r="2"/></svg>
            </button>
        </div>

        <div class="rj-grp" style="justify-content:center; gap:14px;">
            <div class="rj-title">
                <b>{{ $template->name }}</b>
                <small>{{ $template->typeLabel() }}</small>
            </div>
            <div class="rj-seg">
                <template x-for="d in devices" :key="d.id">
                    <button type="button" class="rj-ib" :data-active="device === d.id" @click="setDevice(d.id)" :title="t(d.label)" x-html="d.svg"></button>
                </template>
            </div>
        </div>

        <div class="rj-grp" style="flex:1; justify-content:flex-end; gap:6px;">
            <div x-show="templateType === 'page' && locales.length > 1" x-cloak class="rj-seg">
                <template x-for="loc in locales" :key="loc">
                    <button type="button" class="rj-lc" :data-active="activeLocale === loc" @click="activeLocale = loc" x-text="loc" :title="t('Edit content in') + ' ' + loc"></button>
                </template>
            </div>

            <button type="button" class="rj-ib" @click="undo()" :disabled="!canUndo" :title="t('Undo') + ' (Ctrl+Z)'">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
            </button>
            <button type="button" class="rj-ib" @click="redo()" :disabled="!canRedo" :title="t('Redo') + ' (Ctrl+Shift+Z)'">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 10h-10a8 8 0 00-8 8v2M21 10l-6 6m6-6l-6-6"/></svg>
            </button>
            <div class="rj-sep"></div>

            <span class="rj-badge" :data-saved="saved"><i></i><span x-text="saved ? t('Saved') : t('Unsaved')"></span></span>

            <button type="button" class="rj-btn" @click="togglePreview()" :title="t('Preview') + ' (P)'">
                <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.46 12C3.73 7.94 7.52 5 12 5s8.27 2.94 9.54 7c-1.27 4.06-5.06 7-9.54 7S3.73 16.06 2.46 12z"/></svg>
                <span x-text="t('Preview')"></span>
            </button>
            <button type="button" class="rj-btn rj-save" @click="save()" :disabled="saving" :title="t('Save (Ctrl+S)')" x-text="saving ? t('Saving...') : t('Save')"></button>
        </div>
    </header>

    {{-- ═════════════ TOP BAR (preview) ═════════════ --}}
    <header class="rj-prevbar" x-show="preview" x-cloak>
        <div class="rj-grp" style="flex:1;">
            <button type="button" class="rj-btn" @click="togglePreview()">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                <span x-text="t('Back to editor')"></span>
                <small style="opacity:.55; font-size:10px;">Esc</small>
            </button>
        </div>
        <div class="rj-grp" style="gap:12px;">
            <div class="rj-seg">
                <template x-for="d in devices" :key="d.id">
                    <button type="button" class="rj-ib" :data-active="device === d.id" @click="setDevice(d.id)" :title="t(d.label)" x-html="d.svg"></button>
                </template>
            </div>
            <span class="rj-badge" x-text="deviceWidthLabel"></span>
        </div>
        <div class="rj-grp" style="flex:1; justify-content:flex-end; gap:8px;">
            <div x-show="templateType === 'page' && locales.length > 1" class="rj-seg">
                <template x-for="loc in locales" :key="'p' + loc">
                    <button type="button" class="rj-lc" :data-active="activeLocale === loc" @click="activeLocale = loc" x-text="loc"></button>
                </template>
            </div>
            <button type="button" class="rj-btn" @click="replayPreview()">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v6h6M20 20v-6h-6M20 10A8 8 0 006.3 6.3L4 10m16 4l-2.3 3.7A8 8 0 014 14"/></svg>
                <span x-text="t('Replay animations')"></span>
            </button>
        </div>
    </header>

    <div class="rj-body">

        {{-- ═════════════ LEFT PANEL ═════════════ --}}
        <aside class="rj-panel" x-show="!preview && panelOpen">

            {{-- ── ADD ELEMENT ── --}}
            <div class="rj-pcol" x-show="shownPanel === 'add'">
                <div class="rj-ph">
                    <span x-text="t('Add element')"></span>
                    <select x-model="catFilter" class="rj-sel" aria-label="Category">
                        <option value="all" x-text="t('All')"></option>
                        <template x-for="c in categoryList" :key="c.key">
                            <option :value="c.key" x-text="c.label"></option>
                        </template>
                    </select>
                </div>
                <div class="rj-pb">
                    <input type="text" x-model="widgetSearch" class="rj-in" style="padding:11px 12px; margin-bottom:18px;" :placeholder="t('Search elements...')">

                    <div x-show="widgetSections.length === 0" class="rj-hint" x-text="t('No elements found.')"></div>

                    <template x-for="sec in widgetSections" :key="sec.key">
                        <div>
                            <button type="button" class="rj-sech" @click="toggleSec(sec.key)">
                                <span x-text="sec.icon"></span>
                                <span x-text="sec.label"></span>
                                <span class="rj-chev" :data-open="!isCollapsed(sec.key)">⌄</span>
                            </button>
                            <div x-show="!isCollapsed(sec.key)">
                                <div class="rj-hint" x-show="sec.kind === 'fav' && sec.items.length === 0" x-text="t('Collect favourite elements in one place by clicking the star on any element.')"></div>
                                <div class="rj-grid">
                                    <template x-for="w in sec.items" :key="sec.key + w.id">
                                        <div class="rj-tile" role="button" tabindex="0" draggable="true"
                                             @dragstart="startDragNew(w.id, $event)"
                                             @click.stop="quickAdd(w.id)"
                                             @keydown.enter.prevent="quickAdd(w.id)">
                                            <span class="rj-star" role="button" :data-on="isFav(w.id)" @click.stop="toggleFav(w.id)"
                                                  :title="isFav(w.id) ? t('Remove from favourites') : t('Add to favourites')" x-text="isFav(w.id) ? '★' : '☆'"></span>
                                            <div class="rj-ico" x-html="w.icon"></div>
                                            <div x-text="w.name"></div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- ── SECTIONS ── --}}
            <div class="rj-pcol" x-show="shownPanel === 'sections'" x-cloak>
                <div class="rj-ph"><span x-text="t('Sections')"></span></div>
                <div class="rj-pb">
                    <p class="rj-h3" x-text="t('Layouts')"></p>
                    <div class="rj-grid" style="grid-template-columns:repeat(3, minmax(0,1fr)); gap:10px;">
                        <template x-for="p in layoutPresets" :key="p.key">
                            <button type="button" class="rj-mini" @click="addLayout(p.key)" :title="p.title">
                                <template x-for="(row, ri) in p.rows" :key="ri">
                                    <div class="r">
                                        <template x-for="(c, ci) in row" :key="ci"><div class="c"></div></template>
                                    </div>
                                </template>
                                <span x-text="p.title"></span>
                            </button>
                        </template>
                    </div>

                    <div x-show="sectionTemplates.length">
                        <p class="rj-h3" style="margin-top:8px;" x-text="t('Ready-made sections')"></p>
                        <div style="display:flex; flex-direction:column; gap:10px;">
                            <template x-for="s in sectionTemplates" :key="s.key">
                                <button type="button" class="rj-sect" @click="addSection(s.key)">
                                    <b x-text="s.title"></b>
                                    <span x-text="s.desc"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── NAVIGATOR ── --}}
            <div class="rj-pcol" x-show="shownPanel === 'navigator'" x-cloak>
                <div class="rj-ph"><span x-text="t('Navigator')"></span></div>
                <div class="rj-pb" style="padding:10px;">
                    <div x-show="blocks.length === 0" class="rj-hint" x-text="t('No layers yet.')"></div>
                    <div x-html="navigatorHtml()"></div>
                </div>
            </div>

            {{-- ── HISTORY ── --}}
            <div class="rj-pcol" x-show="shownPanel === 'history'" x-cloak>
                <div class="rj-ph">
                    <span x-text="t('History')"></span>
                    <span style="display:flex; gap:2px;">
                        <button type="button" class="rj-ib" style="margin:0; width:30px; height:30px;" @click="undo()" :disabled="!canUndo" :title="t('Undo')">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                        </button>
                        <button type="button" class="rj-ib" style="margin:0; width:30px; height:30px;" @click="redo()" :disabled="!canRedo" :title="t('Redo')">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 10h-10a8 8 0 00-8 8v2M21 10l-6 6m6-6l-6-6"/></svg>
                        </button>
                    </span>
                </div>
                <div class="rj-pb" style="padding:10px;">
                    <div x-show="historyList.length <= 1" class="rj-hint" x-text="t('Nothing in history yet.')"></div>
                    <template x-for="h in historyList" :key="h.i">
                        <button type="button" class="rj-hist" :data-cur="h.i === historyIndex" :data-future="h.i > historyIndex" @click="goToHistory(h.i)">
                            <i></i><span x-text="h.label"></span><small x-text="ago(h.t)"></small>
                        </button>
                    </template>
                </div>
            </div>

            {{-- ── SETTINGS ── --}}
            <div class="rj-pcol" x-show="shownPanel === 'settings'" x-cloak>
                <div class="rj-ph"><span x-text="t('Settings')"></span></div>
                <div class="rj-pb">
                    <p class="rj-h3" x-text="t('Editor')"></p>
                    <label class="rj-row">
                        <span x-text="t('Show hover outlines')"></span>
                        <span class="rj-switch"><input type="checkbox" :checked="prefs.outlines" @change="setPref('outlines', $event.target.checked)"><span></span></span>
                    </label>
                    <label class="rj-row">
                        <span x-text="t('Auto-save every 30 seconds')"></span>
                        <span class="rj-switch"><input type="checkbox" :checked="prefs.autosave" @change="setPref('autosave', $event.target.checked)"><span></span></span>
                    </label>
                    <div class="rj-field" style="margin-top:10px;">
                        <div class="rj-lbl"><span x-text="t('Canvas zoom')"></span><span x-text="prefs.zoom + '%'"></span></div>
                        <input type="range" min="50" max="100" step="5" :value="prefs.zoom" @input="setPref('zoom', parseInt($event.target.value))" style="width:100%; accent-color:#8b5cf6;">
                    </div>

                    <p class="rj-h3" style="margin-top:22px;" x-text="t('Keyboard shortcuts')"></p>
                    <template x-for="s in shortcuts" :key="s[0]">
                        <div class="rj-row" style="padding:5px 0;">
                            <span x-text="s[1]"></span>
                            <kbd style="background:var(--bg2); border:1px solid var(--line); border-radius:5px; padding:1px 7px; font-size:11px; color:#c4c7d3;" x-text="s[0]"></kbd>
                        </div>
                    </template>

                    <p class="rj-h3" style="margin-top:22px;" x-text="t('Template')"></p>
                    <div class="rj-row"><span>{{ $template->name }}</span><span style="color:var(--mut); font-size:12px;">{{ $template->typeLabel() }}</span></div>
                </div>
            </div>

            {{-- ── EDIT ELEMENT ── --}}
            <div class="rj-pcol" x-show="shownPanel === 'edit'" x-cloak>
                <div class="rj-ph">
                    <span style="display:flex; align-items:center; gap:6px; min-width:0;">
                        <button type="button" class="rj-ib" @click="panel = 'add'" :title="t('Add element')">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" x-text="t('Edit') + ' ' + currentBlockTypeLabel()"></span>
                    </span>
                    <button type="button" class="rj-ib" style="margin:0; width:30px; height:30px;" @click="selectParent(selectedBlock)" :title="t('Select parent')">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l-4-4 4-4M5 10h9a5 5 0 015 5v3"/></svg>
                    </button>
                </div>

                <div class="rj-crumbs" x-show="breadcrumb.length > 1">
                    <template x-for="(c, i) in breadcrumb" :key="c.id">
                        <span style="display:inline-flex; align-items:center;">
                            <i x-show="i > 0">›</i>
                            <button type="button" :data-last="i === breadcrumb.length - 1" @click="selectBlock(c.id)" x-text="blockLabel(c.type)"></button>
                        </span>
                    </template>
                </div>

                <div class="rj-tabs">
                    <button type="button" class="rj-tab" :data-active="editTab === 'content'" @click="editTab = 'content'" x-text="t('Content')"></button>
                    <button type="button" class="rj-tab" :data-active="editTab === 'style'" @click="editTab = 'style'" x-text="t('Style')"></button>
                    <button type="button" class="rj-tab" :data-active="editTab === 'advanced'" @click="editTab = 'advanced'" x-text="t('Motion')"></button>
                </div>

                <div class="rj-pb" @click.stop>
                    <div x-show="editTab === 'content' && templateType === 'page' && locales.length > 1" class="rj-hint" style="text-align:left; border-style:solid; background:rgba(139,92,246,.1); border-color:rgba(139,92,246,.35); color:#c4b5fd;">
                        🌐 <span x-text="t('Editing in')"></span>: <strong x-text="activeLocale.toUpperCase()"></strong>
                        <span x-show="activeLocale !== baseLocale" style="opacity:.8;">— <span x-text="t('fields marked 🌐 are translated; others are shared.')"></span></span>
                    </div>

                    <div x-show="activeFields.length === 0" class="rj-hint" x-text="t('No options in this tab.')"></div>

                    <template x-for="field in activeFields" :key="editTab + field.name">
                        <div class="rj-field">
                            <template x-if="field.type === 'toggle'">
                                <label class="rj-row" style="padding:2px 0;">
                                    <span x-text="field.label"></span>
                                    <span class="rj-switch"><input type="checkbox" :checked="!!currentSettings[field.name]" @change="updateSetting(field.name, $event.target.checked)"><span></span></span>
                                </label>
                            </template>

                            <template x-if="field.type !== 'toggle'">
                                <div class="rj-lbl">
                                    <span x-text="field.label + (templateType === 'page' && locales.length > 1 && isTranslatableField(field.name) ? ' 🌐' : '')"></span>
                                    <button type="button" class="rj-reset" x-show="!isDefault(field)" @click="resetField(field)" :title="t('Reset to default')">↺</button>
                                </div>
                            </template>

                            <template x-if="field.type === 'text' || field.type === 'url' || field.type === 'email'">
                                <input type="text" class="rj-in" :value="fieldValue(field)" :placeholder="field.placeholder || ''" @input="updateSetting(field.name, $event.target.value)">
                            </template>

                            <template x-if="field.type === 'number'">
                                <input type="number" class="rj-in" :value="fieldValue(field)" :min="field.min" :max="field.max" :step="field.step || 1" @input="updateSetting(field.name, $event.target.value)">
                            </template>

                            <template x-if="field.type === 'range'">
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <input type="range" :value="fieldValue(field)" :min="field.min ?? 0" :max="field.max ?? 100" :step="field.step || 1" @input="updateSetting(field.name, $event.target.value)" style="flex:1; accent-color:#8b5cf6;">
                                    <span style="font-size:12px; color:#b9bcc8; min-width:32px; text-align:right;" x-text="fieldValue(field)"></span>
                                </div>
                            </template>

                            <template x-if="field.type === 'textarea'">
                                <textarea class="rj-ta" rows="4" :value="fieldValue(field)" @input="updateSetting(field.name, $event.target.value)"></textarea>
                            </template>

                            <template x-if="field.type === 'select'">
                                <select class="rj-in" @change="updateSetting(field.name, $event.target.value)">
                                    <template x-for="(label, value) in field.options" :key="value">
                                        <option :value="value" :selected="String(fieldValue(field)) === String(value)" x-text="label"></option>
                                    </template>
                                </select>
                            </template>

                            <template x-if="field.type === 'color'">
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <input type="color" :value="colorHex(fieldValue(field))" @input="updateSetting(field.name, $event.target.value)" style="width:40px; height:36px; border:1px solid var(--line); border-radius:6px; background:#15161a; cursor:pointer; padding:2px;">
                                    <input type="text" class="rj-in" style="font-family:monospace; font-size:12px;" :value="fieldValue(field)" @input="updateSetting(field.name, $event.target.value)">
                                </div>
                            </template>

                            <template x-if="field.type === 'media'">
                                <div>
                                    <div x-show="currentSettings[field.name]" style="position:relative; margin-bottom:8px; border:1px solid var(--line); border-radius:6px; overflow:hidden;">
                                        <img :src="currentSettings[field.name]" style="width:100%; height:110px; object-fit:cover; display:block;" alt="">
                                        <button type="button" @click="updateSetting(field.name, '')" style="position:absolute; top:6px; right:6px; width:24px; height:24px; border-radius:50%; background:#dc2626; color:#fff; border:0; cursor:pointer; font-size:11px;">✕</button>
                                    </div>
                                    <button type="button" class="rj-btn" style="width:100%; justify-content:center; border-style:dashed;" @click="openMediaPicker(field.name)" x-text="currentSettings[field.name] ? t('Change Image') : t('Select Image')"></button>
                                </div>
                            </template>

                            <p class="rj-help" x-show="field.help" x-text="field.help"></p>
                        </div>
                    </template>

                    <div x-show="editTab === 'advanced'" style="padding-top:4px;">
                        <button type="button" class="rj-cta" style="width:100%; justify-content:center; font-size:13px; padding:9px 12px;" @click="playAnimation(selectedBlock)">▶ <span x-text="t('Replay animation')"></span></button>
                        <p class="rj-help" style="line-height:1.5; margin-top:10px;" x-text="t('Hover effects and scroll-triggered animations are visible in Preview.')"></p>
                    </div>
                </div>

                <div class="rj-pf">
                    <button type="button" class="rj-btn" style="flex:1; justify-content:center; padding:0 6px;" @click="duplicateSelected()" x-text="t('Duplicate')"></button>
                    <button type="button" class="rj-btn" style="flex:1; justify-content:center; padding:0 6px;" @click="copySelected()" x-text="t('Copy')"></button>
                    <button type="button" class="rj-btn" style="flex:1; justify-content:center; padding:0 6px; color:#f87171; border-color:rgba(248,113,113,.4);" @click="removeSelected()" x-text="t('Delete')"></button>
                </div>
            </div>
        </aside>

        {{-- panel open / close handle --}}
        <button type="button" class="rj-handle" x-show="!preview" :style="'left:' + (panelOpen ? '340px' : '0px')" @click="panelOpen = !panelOpen" :title="panelOpen ? t('Close panel') : t('Open panel')" x-text="panelOpen ? '‹' : '›'"></button>

        {{-- ═════════════ CANVAS ═════════════ --}}
        <main id="rj-main" class="rj-main" :data-preview="preview" @click="selectedBlock = null" @dragover="autoScroll($event)">
            <div :style="preview ? '' : 'padding:32px 28px 180px;'">

                {{-- empty state --}}
                <div x-show="blocks.length === 0 && !preview" class="rj-empty">
                    <h2 style="font-size:20px; font-weight:600; margin:0 0 6px;" x-text="t('Select a layout')"></h2>
                    <p style="font-size:14px; color:#6b7280; margin:0 0 28px;" x-text="t('Choose a structure to start building.')"></p>

                    <div style="display:flex; justify-content:center; flex-wrap:wrap; gap:12px; margin-bottom:36px;">
                        <template x-for="p in layoutPresets" :key="'e' + p.key">
                            <button type="button" class="rj-preset" @click.stop="addLayout(p.key)" :title="p.title">
                                <template x-for="(row, ri) in p.rows" :key="ri">
                                    <div class="r"><template x-for="(c, ci) in row" :key="ci"><div class="c"></div></template></div>
                                </template>
                            </button>
                        </template>
                    </div>

                    <div style="font-size:11px; text-transform:uppercase; letter-spacing:.08em; color:#9ca3af; margin-bottom:14px;" x-text="t('or start from scratch')"></div>
                    <button type="button" class="rj-cta" @click.stop="addBlank()">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <span x-text="t('Start Blank')"></span>
                    </button>
                </div>

                {{-- device label --}}
                <div class="rj-devlabel" x-show="device !== 'desktop' && blocks.length > 0" x-text="t(deviceLabel) + ' · ' + deviceWidthLabel"></div>

                <div x-show="blocks.length > 0" id="rj-canvas" class="rj-canvas" :class="(prefs.outlines ? 'rj-outlines ' : '') + (preview && device === 'desktop' ? '' : 'rj-page')" :style="canvasStyle">
                    <template x-for="(block, index) in blocks" :key="block.id">
                        <div>
                            <div x-show="!preview"
                                 @dragover.prevent="onDragOver($event, 'root', index)"
                                 @dragleave="dragTarget = null"
                                 @drop.prevent="onDrop($event, 'root', index)"
                                 :class="dragTarget === 'root:' + index ? 'opacity-100' : 'opacity-0'"
                                 :style="'display:flex; align-items:center; transition:all .15s; height:' + (dragTarget === 'root:' + index ? '40px' : '8px') + '; opacity:' + (dragTarget === 'root:' + index ? 1 : 0)">
                                <div style="width:100%; height:4px; border-radius:9999px; background:#7c3aed;"></div>
                            </div>
                            <div x-html="renderBlockHtml(block, 'root', index)"></div>
                        </div>
                    </template>

                    <div x-show="!preview"
                         @dragover.prevent="onDragOver($event, 'root', blocks.length)"
                         @dragleave="dragTarget = null"
                         @drop.prevent="onDrop($event, 'root', blocks.length)"
                         :style="'display:flex; align-items:center; transition:all .15s; height:' + (dragTarget === 'root:' + blocks.length ? '40px' : '8px') + '; opacity:' + (dragTarget === 'root:' + blocks.length ? 1 : 0)">
                        <div style="width:100%; height:4px; border-radius:9999px; background:#7c3aed;"></div>
                    </div>

                    <div x-show="!preview" style="margin-top:40px; display:flex; justify-content:center; padding-bottom:20px;">
                        <button type="button" class="rj-addsec" @click.stop="showLayoutPicker = true">
                            <span style="width:40px; height:40px; border-radius:50%; border:2px solid #c3c7d0; display:flex; align-items:center; justify-content:center; color:#9ca3af;">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            </span>
                            <span>
                                <span style="display:block; font-size:14px; font-weight:600; color:#111827;" x-text="t('Add New Section')"></span>
                                <span style="display:block; font-size:12px; color:#6b7280;" x-text="t('Choose a layout for the new section.')"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- Toast --}}
    <div x-show="toast.show" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="rj-toast" :style="'background:' + (toast.type === 'error' ? '#dc2626' : '#059669')" x-text="toast.message"></div>

    {{-- Layout picker --}}
    <div x-show="showLayoutPicker" x-cloak class="rj-modal" @keydown.escape.window="showLayoutPicker = false">
        <div class="bg" @click="showLayoutPicker = false"></div>
        <div class="box">
            <div style="text-align:center; margin-bottom:26px;">
                <h2 style="font-size:18px; font-weight:600; margin:0 0 6px;" x-text="t('Add New Section')"></h2>
                <p style="font-size:14px; color:#6b7280; margin:0;" x-text="t('Choose a layout for the new section.')"></p>
            </div>
            <div style="display:flex; justify-content:center; flex-wrap:wrap; gap:12px;">
                <template x-for="p in layoutPresets" :key="'m' + p.key">
                    <button type="button" class="rj-preset" @click="addLayout(p.key); showLayoutPicker = false" :title="p.title">
                        <template x-for="(row, ri) in p.rows" :key="ri">
                            <div class="r"><template x-for="(c, ci) in row" :key="ci"><div class="c"></div></template></div>
                        </template>
                    </button>
                </template>
            </div>
            <div style="margin-top:26px; text-align:center;">
                <button type="button" @click="showLayoutPicker = false" style="background:transparent; border:0; color:#6b7280; font-size:14px; cursor:pointer;" x-text="t('Cancel')"></button>
            </div>
        </div>
    </div>

    @include('admin.partials.media-picker')
</div>

@include('rjbuilder.partials.scripts.data')
@include('rjbuilder.partials.scripts.state')
@include('rjbuilder.partials.scripts.actions')
@include('rjbuilder.partials.scripts.render')
@include('rjbuilder.partials.scripts.panels')
@include('rjbuilder.partials.scripts.index')

@endsection