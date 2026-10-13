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
    $templateType = $template->type;
    $locales = rj_available_locales();
    $baseLocale = config('app.locale');
@endphp

@verbatim
<style>
    /* ───────── Entrance animations (editor + preview). Copy this block to your public renderer too. ───────── */
    .rj-a { animation-fill-mode: both; animation-timing-function: cubic-bezier(.22,1,.36,1); }
    .rj-wait { opacity: 0; }
    .rj-play.rj-a-fadeIn      { animation-name: rjFadeIn; }
    .rj-play.rj-a-fadeInUp    { animation-name: rjFadeInUp; }
    .rj-play.rj-a-fadeInDown  { animation-name: rjFadeInDown; }
    .rj-play.rj-a-fadeInLeft  { animation-name: rjFadeInLeft; }
    .rj-play.rj-a-fadeInRight { animation-name: rjFadeInRight; }
    .rj-play.rj-a-zoomIn      { animation-name: rjZoomIn; }
    .rj-play.rj-a-bounceIn    { animation-name: rjBounceIn; animation-timing-function: ease-out; }
    .rj-play.rj-a-flipInX     { animation-name: rjFlipInX; }
    .rj-play.rj-a-rotateIn    { animation-name: rjRotateIn; }
    .rj-play.rj-a-slideUp     { animation-name: rjSlideUp; }
    .rj-play.rj-a-blurIn      { animation-name: rjBlurIn; }
    .rj-play.rj-a-pulse       { animation-name: rjPulse; animation-timing-function: ease-in-out; }

    @keyframes rjFadeIn      { from { opacity: 0 } to { opacity: 1 } }
    @keyframes rjFadeInUp    { from { opacity: 0; transform: translate3d(0, 40px, 0) } to { opacity: 1; transform: none } }
    @keyframes rjFadeInDown  { from { opacity: 0; transform: translate3d(0, -40px, 0) } to { opacity: 1; transform: none } }
    @keyframes rjFadeInLeft  { from { opacity: 0; transform: translate3d(-60px, 0, 0) } to { opacity: 1; transform: none } }
    @keyframes rjFadeInRight { from { opacity: 0; transform: translate3d(60px, 0, 0) } to { opacity: 1; transform: none } }
    @keyframes rjZoomIn      { from { opacity: 0; transform: scale(.6) } to { opacity: 1; transform: none } }
    @keyframes rjBounceIn    { 0% { opacity: 0; transform: scale(.3) } 50% { opacity: 1; transform: scale(1.08) } 70% { transform: scale(.95) } 100% { opacity: 1; transform: scale(1) } }
    @keyframes rjFlipInX     { from { opacity: 0; transform: perspective(600px) rotateX(90deg) } to { opacity: 1; transform: perspective(600px) rotateX(0) } }
    @keyframes rjRotateIn    { from { opacity: 0; transform: rotate(-12deg) scale(.9) } to { opacity: 1; transform: none } }
    @keyframes rjSlideUp     { from { opacity: 0; clip-path: inset(100% 0 0 0); transform: translateY(24px) } to { opacity: 1; clip-path: inset(0); transform: none } }
    @keyframes rjBlurIn      { from { opacity: 0; filter: blur(14px) } to { opacity: 1; filter: blur(0) } }
    @keyframes rjPulse       { 0%, 100% { opacity: 1; transform: scale(1) } 50% { opacity: 1; transform: scale(1.05) } }
    @keyframes rjFloat       { 0%, 100% { transform: translateY(0) } 50% { transform: translateY(-8px) } }

    /* ───────── Hover effects (applied in preview / frontend) ───────── */
    .rj-h { transition: transform .35s cubic-bezier(.22,1,.36,1), box-shadow .35s, filter .35s; }
    .rj-h-grow:hover  { transform: scale(1.04); }
    .rj-h-lift:hover  { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(17,24,39,.16); }
    .rj-h-glow:hover  { box-shadow: 0 0 34px rgba(124,58,237,.45); }
    .rj-h-tilt:hover  { transform: perspective(800px) rotateX(4deg) rotateY(-4deg) scale(1.02); }
    .rj-h-float:hover { animation: rjFloat 1.4s ease-in-out infinite; }

    @media (prefers-reduced-motion: reduce) {
        .rj-a { animation: none !important; }
        .rj-wait { opacity: 1 !important; }
        .rj-h, .rj-h:hover { transition: none; transform: none; animation: none; }
    }

    /* ───────── Editor chrome ───────── */
    .rj-block:hover:not(:has(.rj-block:hover)) > .rj-ring { outline: 1px solid rgba(124,58,237,.5); outline-offset: -1px; }
    .rj-drop { border-color: #7c3aed !important; background: rgba(124,58,237,.07) !important; }
    .rj-nav { cursor: pointer; transition: background .12s; }
    .rj-nav:hover { background: #f3f4f6; }
    .rj-tab { position: relative; transition: color .15s; }
    .rj-tab[data-active="true"] { color: #7c3aed; }
    .rj-tab[data-active="true"]::after { content: ''; position: absolute; left: 12px; right: 12px; bottom: -1px; height: 2px; background: #7c3aed; border-radius: 2px; }
    .rj-device[data-active="true"] { background: rgba(255,255,255,.14); color: #fff; }
    .rj-inline-edit { cursor: text; }
</style>
@endverbatim

<div x-data="builder()" x-init="init()"
     class="flex flex-col overflow-hidden" style="height:100vh; height:100dvh;"
     @dragend.window="endDrag()"
     @keydown.window="onKey($event)"
     @beforeunload.window="if (!saved) { $event.preventDefault(); $event.returnValue = ''; }">

    {{-- ═════════════ HEADER ═════════════ --}}
    <header class="h-14 flex items-center justify-between px-4 flex-shrink-0" style="background:#1a1030; border-bottom:1px solid rgba(255,255,255,0.08);">
        <div class="flex items-center gap-3 min-w-0" style="width:30%;">
            <a href="{{ route('admin.rjbuilder.edit', $template) }}" class="w-8 h-8 rounded-lg flex items-center justify-center transition" style="color:#cbd5e1;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='transparent'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div class="h-6 w-px" style="background:rgba(255,255,255,0.15);"></div>
            <div class="min-w-0">
                <div class="text-sm font-semibold truncate" style="color:#ffffff;">{{ $template->name }}</div>
                <div class="text-[10px] uppercase tracking-wider" style="color:#a855f7;">{{ $template->typeLabel() }}</div>
            </div>
        </div>

                {{-- Language switcher (page type only) --}}
        <div x-show="templateType === 'page'" class="flex items-center gap-1 p-1 rounded-xl" style="background:rgba(255,255,255,0.06);">
            <template x-for="loc in locales" :key="loc">
                <button type="button" @click="activeLocale = loc"
                        :style="activeLocale === loc ? 'background:rgba(124,58,237,0.5); color:#fff;' : 'color:#94a3b8;'"
                        class="px-3 h-8 rounded-lg text-xs font-semibold uppercase transition"
                        x-text="loc"
                        :title="'{{ __('Edit content in') }} ' + loc"></button>
            </template>
        </div>

        {{-- Device switcher --}}
        <div class="flex items-center gap-1 p-1 rounded-xl" style="background:rgba(255,255,255,0.06); color:#94a3b8;">

        {{-- Device switcher --}}
        <div class="flex items-center gap-1 p-1 rounded-xl" style="background:rgba(255,255,255,0.06); color:#94a3b8;">
            <button type="button" class="rj-device w-9 h-8 rounded-lg flex items-center justify-center transition" :data-active="device === 'desktop'" @click="setDevice('desktop')" title="{{ __('Desktop') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </button>
            <button type="button" class="rj-device w-9 h-8 rounded-lg flex items-center justify-center transition" :data-active="device === 'tablet'" @click="setDevice('tablet')" title="{{ __('Tablet') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2"/><path stroke-linecap="round" d="M11 18h2"/></svg>
            </button>
            <button type="button" class="rj-device w-9 h-8 rounded-lg flex items-center justify-center transition" :data-active="device === 'mobile'" @click="setDevice('mobile')" title="{{ __('Mobile') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="7" y="2" width="10" height="20" rx="2"/><path stroke-linecap="round" d="M11 18h2"/></svg>
            </button>
        </div>

        <div class="flex items-center justify-end gap-2" style="width:30%;">
            <button type="button" @click="undo()" :disabled="!canUndo" title="Undo (Ctrl+Z)"
                    class="w-8 h-8 rounded-lg flex items-center justify-center transition disabled:opacity-30"
                    style="color:#e2e8f0;"
                    onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='transparent'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
            </button>
            <button type="button" @click="redo()" :disabled="!canRedo" title="Redo (Ctrl+Shift+Z)"
                    class="w-8 h-8 rounded-lg flex items-center justify-center transition disabled:opacity-30"
                    style="color:#e2e8f0;"
                    onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='transparent'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 10h-10a8 8 0 00-8 8v2M21 10l-6 6m6-6l-6-6"/></svg>
            </button>

            <div class="h-6 w-px" style="background:rgba(255,255,255,0.15);"></div>

            <span x-show="!saved" class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold uppercase tracking-wider" style="background:rgba(245,158,11,0.15); color:#fbbf24;">
                {{ __('Unsaved') }}
            </span>
            <span x-show="saved" x-cloak class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold uppercase tracking-wider" style="background:rgba(16,185,129,0.15); color:#34d399;">
                {{ __('Saved') }}
            </span>

            <button type="button" @click="togglePreview()" title="{{ __('Preview') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition"
                    style="color:#e2e8f0; border:1px solid rgba(255,255,255,0.18);"
                    onmouseover="this.style.background='rgba(255,255,255,0.08)'" onmouseout="this.style.background='transparent'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.46 12C3.73 7.94 7.52 5 12 5s8.27 2.94 9.54 7c-1.27 4.06-5.06 7-9.54 7S3.73 16.06 2.46 12z"/></svg>
                <span x-text="preview ? '{{ __('Edit') }}' : '{{ __('Preview') }}'"></span>
            </button>

            <button type="button" @click="save()" :disabled="saving" title="Save (Ctrl+S)"
                    class="inline-flex items-center gap-2 px-4 py-1.5 rounded-lg text-white text-sm font-medium transition disabled:opacity-50"
                    style="background:linear-gradient(to right, #7c3aed, #ec4899);">
                <span x-text="saving ? '{{ __('Saving...') }}' : '{{ __('Save') }}'"></span>
            </button>
        </div>
    </header>

    <div class="flex-1 min-h-0 flex overflow-hidden" style="min-height:0;">

        {{-- ═════════════ LEFT: WIDGETS / LAYERS ═════════════ --}}
        <aside x-show="!preview" class="w-72 flex flex-col flex-shrink-0 min-h-0" style="background:#ffffff; border-right:1px solid #e5e7eb;">
            <div class="flex flex-shrink-0 px-2" style="border-bottom:1px solid #f3f4f6;">
                <button type="button" class="rj-tab flex-1 py-3 text-xs font-semibold" style="color:#6b7280;" :data-active="leftTab === 'widgets'" @click="leftTab = 'widgets'">{{ __('Widgets') }}</button>
                <button type="button" class="rj-tab flex-1 py-3 text-xs font-semibold" style="color:#6b7280;" :data-active="leftTab === 'layers'" @click="leftTab = 'layers'">{{ __('Layers') }}</button>
            </div>

            <div x-show="leftTab === 'widgets'" class="flex-1 min-h-0 flex flex-col" style="min-height:0;">
                <div class="px-4 py-3 flex-shrink-0" style="border-bottom:1px solid #f3f4f6;">
                    <div class="relative">
                        <svg class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" style="color:#9ca3af;" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" x-model="widgetSearch"
                               placeholder="{{ __('Search widgets...') }}"
                               class="w-full pl-9 pr-3 py-1.5 rounded-lg text-xs"
                               style="border:1px solid #e5e7eb; outline:none;">
                    </div>
                </div>

                <div class="flex-1 min-h-0 p-3 rj-scrollbar" style="min-height:0; overflow-y:auto; overscroll-behavior:contain;">
                    @php $grouped = \App\Widgets\WidgetRegistry::grouped(); @endphp
                    @php $categories = \App\Widgets\WidgetRegistry::categories(); @endphp

                    @foreach ($categories as $categoryKey => $catMeta)
                        @php $widgets = $grouped[$categoryKey] ?? []; @endphp
                        @if (!empty($widgets))
                            <div x-show="categoryVisible('{{ $categoryKey }}')">
                                <div class="px-2 py-1.5 text-[10px] font-semibold uppercase tracking-wider flex items-center gap-1.5" style="color:#9ca3af;">
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
                                                class="group flex flex-col items-center justify-center gap-2 px-3 py-4 rounded-lg transition cursor-grab active:cursor-grabbing"
                                                style="border:1px solid #e5e7eb; background:#ffffff;"
                                                onmouseover="this.style.borderColor='#7c3aed'; this.style.background='rgba(124,58,237,0.04)'; this.style.transform='translateY(-2px)';"
                                                onmouseout="this.style.borderColor='#e5e7eb'; this.style.background='#ffffff'; this.style.transform='none';">
                                            <div class="w-9 h-9 rounded-lg flex items-center justify-center transition" style="background:#f3f4f6; color:#6b7280;">
                                                {!! $widget->icon() !!}
                                            </div>
                                            <div class="text-[11px] font-medium text-center leading-tight" style="color:#374151;">
                                                {{ $widget->name() }}
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <div x-show="leftTab === 'layers'" x-cloak class="flex-1 min-h-0 p-2 rj-scrollbar" style="min-height:0; overflow-y:auto; overscroll-behavior:contain;">
                <div x-show="blocks.length === 0" class="p-6 text-center text-xs" style="color:#9ca3af;">{{ __('No layers yet.') }}</div>
                <div x-html="navigatorHtml()"></div>
            </div>
        </aside>

        {{-- ═════════════ CANVAS ═════════════ --}}
        <main id="rj-main" class="flex-1 min-h-0 rj-scrollbar" style="background:#f9fafb; min-height:0; overflow-y:auto; overflow-x:hidden; overscroll-behavior:contain;" @click="selectedBlock = null" @dragover="autoScroll($event)">
            <div :class="preview ? 'py-0 px-0' : 'py-12 px-6 pb-40'">

                <div x-show="blocks.length === 0 && !preview" class="max-w-5xl mx-auto">
                    <div class="text-center mb-10">
                        <h2 class="text-xl font-semibold mb-2" style="color:#111827;">{{ __('Select a wrap layout') }}</h2>
                        <p class="text-sm" style="color:#6b7280;">{{ __('Choose a structure to start building.') }}</p>
                    </div>

                    <div class="flex items-center justify-center gap-3 flex-wrap mb-10">
                        <button type="button" @click.stop="addLayout('1col')" class="group w-20 h-20 rounded-xl flex items-center justify-center transition" style="border:2px solid #e5e7eb; background:#ffffff;" onmouseover="this.style.borderColor='#7c3aed';" onmouseout="this.style.borderColor='#e5e7eb';" title="1 Column">
                            <div class="w-10 h-10 rounded" style="border:2px dashed #d1d5db;"></div>
                        </button>
                        <button type="button" @click.stop="addLayout('2col')" class="group w-20 h-20 rounded-xl flex items-center justify-center gap-1 transition" style="border:2px solid #e5e7eb; background:#ffffff;" onmouseover="this.style.borderColor='#7c3aed';" onmouseout="this.style.borderColor='#e5e7eb';" title="2 Columns">
                            <div class="w-4 h-10 rounded" style="border:2px dashed #d1d5db;"></div>
                            <div class="w-4 h-10 rounded" style="border:2px dashed #d1d5db;"></div>
                        </button>
                        <button type="button" @click.stop="addLayout('3col')" class="group w-20 h-20 rounded-xl flex items-center justify-center gap-1 transition" style="border:2px solid #e5e7eb; background:#ffffff;" onmouseover="this.style.borderColor='#7c3aed';" onmouseout="this.style.borderColor='#e5e7eb';" title="3 Columns">
                            <div class="w-3 h-10 rounded" style="border:2px dashed #d1d5db;"></div>
                            <div class="w-3 h-10 rounded" style="border:2px dashed #d1d5db;"></div>
                            <div class="w-3 h-10 rounded" style="border:2px dashed #d1d5db;"></div>
                        </button>
                        <button type="button" @click.stop="addLayout('4col')" class="group w-20 h-20 rounded-xl flex items-center justify-center gap-1 transition" style="border:2px solid #e5e7eb; background:#ffffff;" onmouseover="this.style.borderColor='#7c3aed';" onmouseout="this.style.borderColor='#e5e7eb';" title="4 Columns">
                            <div class="w-2.5 h-10 rounded" style="border:2px dashed #d1d5db;"></div>
                            <div class="w-2.5 h-10 rounded" style="border:2px dashed #d1d5db;"></div>
                            <div class="w-2.5 h-10 rounded" style="border:2px dashed #d1d5db;"></div>
                            <div class="w-2.5 h-10 rounded" style="border:2px dashed #d1d5db;"></div>
                        </button>
                        <button type="button" @click.stop="addLayout('2row')" class="group w-20 h-20 rounded-xl flex flex-col items-center justify-center gap-1 transition" style="border:2px solid #e5e7eb; background:#ffffff;" onmouseover="this.style.borderColor='#7c3aed';" onmouseout="this.style.borderColor='#e5e7eb';" title="2 Rows">
                            <div class="w-10 h-4 rounded" style="border:2px dashed #d1d5db;"></div>
                            <div class="w-10 h-4 rounded" style="border:2px dashed #d1d5db;"></div>
                        </button>
                        <button type="button" @click.stop="addLayout('1plus2')" class="group w-20 h-20 rounded-xl flex flex-col items-center justify-center gap-1 transition" style="border:2px solid #e5e7eb; background:#ffffff;" onmouseover="this.style.borderColor='#7c3aed';" onmouseout="this.style.borderColor='#e5e7eb';" title="1 + 2">
                            <div class="w-10 h-4 rounded" style="border:2px dashed #d1d5db;"></div>
                            <div class="flex gap-1">
                                <div class="h-4 rounded" style="border:2px dashed #d1d5db; width:18px;"></div>
                                <div class="h-4 rounded" style="border:2px dashed #d1d5db; width:18px;"></div>
                            </div>
                        </button>
                        <button type="button" @click.stop="addLayout('2row3')" class="group w-20 h-20 rounded-xl flex flex-col items-center justify-center gap-1 transition" style="border:2px solid #e5e7eb; background:#ffffff;" onmouseover="this.style.borderColor='#7c3aed';" onmouseout="this.style.borderColor='#e5e7eb';" title="2 Rows x 3">
                            <div class="flex gap-0.5">
                                <div class="w-2.5 h-4 rounded" style="border:2px dashed #d1d5db;"></div>
                                <div class="w-2.5 h-4 rounded" style="border:2px dashed #d1d5db;"></div>
                                <div class="w-2.5 h-4 rounded" style="border:2px dashed #d1d5db;"></div>
                            </div>
                            <div class="flex gap-0.5">
                                <div class="w-2.5 h-4 rounded" style="border:2px dashed #d1d5db;"></div>
                                <div class="w-2.5 h-4 rounded" style="border:2px dashed #d1d5db;"></div>
                                <div class="w-2.5 h-4 rounded" style="border:2px dashed #d1d5db;"></div>
                            </div>
                        </button>
                    </div>

                    <div class="text-center">
                        <div class="text-xs uppercase tracking-wider mb-4" style="color:#9ca3af;">{{ __('or start from scratch') }}</div>
                        <button type="button" @click.stop="addBlank()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-white text-sm font-medium transition" style="background:linear-gradient(to right, #7c3aed, #ec4899);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            {{ __('Start Blank') }}
                        </button>
                    </div>
                </div>

                <div x-show="blocks.length > 0" id="rj-canvas" class="mx-auto transition-all duration-300" :style="canvasStyle">
                    <template x-for="(block, index) in blocks" :key="block.id">
                        <div>
                            <div x-show="!preview"
                                 @dragover.prevent="onDragOver($event, 'root', index)"
                                 @dragleave="dragTarget = null"
                                 @drop.prevent="onDrop($event, 'root', index)"
                                 :class="dragTarget === 'root:' + index ? 'h-10 opacity-100' : 'h-2 opacity-0'"
                                 class="transition-all flex items-center">
                                <div class="w-full h-1 rounded-full" style="background:#7c3aed;"></div>
                            </div>

                            <div x-html="renderBlockHtml(block, 'root', index)"></div>
                        </div>
                    </template>

                    <div x-show="!preview"
                         @dragover.prevent="onDragOver($event, 'root', blocks.length)"
                         @dragleave="dragTarget = null"
                         @drop.prevent="onDrop($event, 'root', blocks.length)"
                         :class="dragTarget === 'root:' + blocks.length ? 'h-10 opacity-100' : 'h-2 opacity-0'"
                         class="transition-all flex items-center">
                        <div class="w-full h-1 rounded-full" style="background:#7c3aed;"></div>
                    </div>

                    <div x-show="!preview" class="mt-10 flex items-center justify-center">
                        <button type="button" @click.stop="showLayoutPicker = true"
                                class="group flex items-center gap-3 px-6 py-4 rounded-2xl transition"
                                style="border:2px dashed #d1d5db; background:#ffffff;"
                                onmouseover="this.style.borderColor='#7c3aed'; this.style.background='rgba(124,58,237,0.04)';"
                                onmouseout="this.style.borderColor='#d1d5db'; this.style.background='#ffffff';">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center transition" style="background:#ffffff; border:2px solid #d1d5db; color:#9ca3af;">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            </div>
                            <div class="text-left">
                                <div class="text-sm font-semibold" style="color:#111827;">{{ __('Add New Section') }}</div>
                                <div class="text-xs" style="color:#6b7280;">{{ __('Choose a layout below') }}</div>
                            </div>
                        </button>
                    </div>
                </div>

            </div>
        </main>

        {{-- ═════════════ RIGHT: SETTINGS ═════════════ --}}
        <aside x-show="!preview" class="w-80 flex flex-col flex-shrink-0 min-h-0" style="background:#ffffff; border-left:1px solid #e5e7eb;">
            <div class="px-4 py-3 flex items-center justify-between flex-shrink-0" style="border-bottom:1px solid #f3f4f6;">
                <div class="text-xs font-semibold uppercase tracking-wider" style="color:#6b7280;">{{ __('Settings') }}</div>
                <div x-show="selectedBlock" x-cloak class="text-[10px] font-medium uppercase tracking-wider" style="color:#7c3aed;" x-text="currentBlockTypeLabel()"></div>
            </div>

            <div x-show="selectedBlock" x-cloak class="flex flex-shrink-0 px-2" style="border-bottom:1px solid #f3f4f6;">
                <button type="button" class="rj-tab flex-1 py-2.5 text-xs font-semibold" style="color:#6b7280;" :data-active="rightTab === 'content'" @click="rightTab = 'content'">{{ __('Content') }}</button>
                <button type="button" class="rj-tab flex-1 py-2.5 text-xs font-semibold" style="color:#6b7280;" :data-active="rightTab === 'style'" @click="rightTab = 'style'">{{ __('Style') }}</button>
                <button type="button" class="rj-tab flex-1 py-2.5 text-xs font-semibold" style="color:#6b7280;" :data-active="rightTab === 'advanced'" @click="rightTab = 'advanced'">{{ __('Motion') }}</button>
            </div>

            <div class="flex-1 min-h-0 p-4 rj-scrollbar" style="min-height:0; overflow-y:auto; overscroll-behavior:contain;" @click.stop>

                <div x-show="!selectedBlock" class="h-full flex items-center justify-center">
                    <div class="text-center max-w-xs">
                        <div class="w-12 h-12 mx-auto rounded-lg flex items-center justify-center mb-3" style="background:#f3f4f6;">
                            <svg class="w-6 h-6" style="color:#9ca3af;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                        </div>
                        <p class="text-xs" style="color:#6b7280;">{{ __('Select a block to edit its settings.') }}</p>
                        <p class="text-[11px] mt-2" style="color:#9ca3af;">{{ __('Double-click a heading or text to edit it on the canvas.') }}</p>
                    </div>
                </div>

                <div x-show="selectedBlock" x-cloak class="space-y-4">

                                        <div x-show="rightTab === 'content' && templateType === 'page'" class="text-[11px] px-3 py-2 rounded-lg mb-3" style="background:rgba(124,58,237,.08); color:#5b21b6;">
                        ✏️ {{ __('Editing in') }}: <strong x-text="activeLocale.toUpperCase()"></strong>
                    </div>

                    <div x-show="activeFields.length === 0" class="text-xs text-center py-6" style="color:#9ca3af;">{{ __('No options in this tab.') }}</div>

                    <template x-for="field in activeFields" :key="rightTab + field.name">
                        <div>
                            <template x-if="field.type !== 'toggle'">
                                <label class="block text-xs font-medium mb-1.5" style="color:#374151;" x-text="field.label"></label>
                            </template>

                            <template x-if="field.type === 'text'">
                                <input type="text" :value="fieldValue(field)" @input="updateSetting(field.name, $event.target.value)" class="w-full rounded-lg text-sm" style="border:1px solid #d1d5db; padding:0.5rem 0.75rem; outline:none;">
                            </template>

                            <template x-if="field.type === 'textarea'">
                                <textarea :value="fieldValue(field)" @input="updateSetting(field.name, $event.target.value)" rows="4" class="w-full rounded-lg text-sm" style="border:1px solid #d1d5db; padding:0.5rem 0.75rem; outline:none;"></textarea>
                            </template>

                            <template x-if="field.type === 'select'">
                                <select @change="updateSetting(field.name, $event.target.value)" class="w-full rounded-lg text-sm" style="border:1px solid #d1d5db; padding:0.5rem 0.75rem; outline:none;">
                                    <template x-for="(label, value) in field.options" :key="value">
                                        <option :value="value" :selected="String(fieldValue(field)) === String(value)" x-text="label"></option>
                                    </template>
                                </select>
                            </template>

                            <template x-if="field.type === 'color'">
                                <div class="flex items-center gap-2">
                                    <input type="color" :value="fieldValue(field) || '#000000'" @input="updateSetting(field.name, $event.target.value)" class="rounded-lg cursor-pointer" style="width:40px; height:36px; border:1px solid #d1d5db;">
                                    <input type="text" :value="fieldValue(field)" @input="updateSetting(field.name, $event.target.value)" class="flex-1 rounded-lg text-xs font-mono" style="border:1px solid #d1d5db; padding:0.5rem 0.75rem; outline:none;">
                                </div>
                            </template>

                            <template x-if="field.type === 'toggle'">
                                <label class="flex items-center justify-between cursor-pointer py-1">
                                    <span class="text-xs font-medium" style="color:#374151;" x-text="field.label"></span>
                                    <input type="checkbox" :checked="!!currentSettings[field.name]" @change="updateSetting(field.name, $event.target.checked)" class="rounded" style="accent-color:#7c3aed; width:16px; height:16px;">
                                </label>
                            </template>

                            <template x-if="field.type === 'media'">
                                <div>
                                    <div x-show="currentSettings[field.name]" class="mb-2 rounded-lg overflow-hidden relative" style="border:1px solid #e5e7eb;">
                                        <img :src="currentSettings[field.name]" class="w-full h-24 object-cover">
                                        <button type="button" @click="updateSetting(field.name, '')" class="absolute top-1 right-1 w-6 h-6 rounded-full flex items-center justify-center" style="background:#dc2626; color:#ffffff;">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    <button type="button" @click="openMediaPicker(field.name)" class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-xs font-medium transition" style="border:2px dashed #d1d5db; color:#4b5563; background:#ffffff;" onmouseover="this.style.borderColor='#7c3aed';" onmouseout="this.style.borderColor='#d1d5db';">
                                        <span x-text="currentSettings[field.name] ? '{{ __('Change Image') }}' : '{{ __('Select Image') }}'"></span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </template>

                    <div x-show="rightTab === 'advanced'" class="pt-1">
                        <button type="button" @click="playAnimation(selectedBlock)"
                                class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold text-white transition"
                                style="background:linear-gradient(to right, #7c3aed, #ec4899);">
                            ▶ {{ __('Replay animation') }}
                        </button>
                        <p class="text-[11px] mt-2 leading-relaxed" style="color:#9ca3af;">{{ __('Hover effects and scroll-triggered animations are visible in Preview.') }}</p>
                    </div>

                    <div class="pt-4 flex items-center gap-2" style="border-top:1px solid #f3f4f6;">
                        <button type="button" @click="duplicateSelected()" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-xs transition" style="border:1px solid #d1d5db; color:#374151;" onmouseover="this.style.background='#f9fafb';" onmouseout="this.style.background='transparent';">
                            {{ __('Duplicate') }}
                        </button>
                        <button type="button" @click="removeSelected()" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-xs transition" style="border:1px solid #fecaca; color:#dc2626;" onmouseover="this.style.background='#fef2f2';" onmouseout="this.style.background='transparent';">
                            {{ __('Delete') }}
                        </button>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    {{-- Floating exit-preview button --}}
    <button type="button" x-show="preview" x-cloak @click="togglePreview()"
            class="fixed bottom-6 left-1/2 -translate-x-1/2 inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-medium text-white"
            style="z-index:9000; background:#1a1030; box-shadow:0 10px 30px rgba(0,0,0,0.35);">
        ✎ {{ __('Back to editor') }} <span class="text-[10px] opacity-60">Esc</span>
    </button>

    {{-- Toast --}}
    <div x-show="toast.show" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed top-16 right-6 px-4 py-2.5 rounded-lg text-sm font-medium text-white"
         :style="'z-index:10000; box-shadow:0 10px 30px rgba(0,0,0,0.25); background:' + (toast.type === 'error' ? '#dc2626' : '#059669')"
         x-text="toast.message"></div>

    {{-- Layout picker --}}
    <div x-show="showLayoutPicker" x-cloak class="fixed inset-0 flex items-center justify-center p-4" style="z-index:9999;" @keydown.escape.window="showLayoutPicker = false">
        <div x-show="showLayoutPicker" @click="showLayoutPicker = false" class="absolute inset-0" style="background:rgba(0,0,0,0.6); backdrop-filter:blur(4px);"></div>

        <div x-show="showLayoutPicker" class="relative bg-white rounded-2xl w-full max-w-3xl p-8" style="box-shadow:0 25px 50px -12px rgba(0,0,0,0.4);">
            <div class="text-center mb-8">
                <h2 class="text-lg font-semibold mb-2" style="color:#111827;">{{ __('Add New Section') }}</h2>
                <p class="text-sm" style="color:#6b7280;">{{ __('Choose a layout for the new section.') }}</p>
            </div>

            <div class="flex items-center justify-center gap-3 flex-wrap">
                <button type="button" @click="addLayout('1col'); showLayoutPicker = false" class="w-16 h-16 rounded-xl flex items-center justify-center transition" style="border:2px solid #e5e7eb; background:#ffffff;" onmouseover="this.style.borderColor='#7c3aed';" onmouseout="this.style.borderColor='#e5e7eb';">
                    <div class="w-8 h-8 rounded" style="border:2px dashed #d1d5db;"></div>
                </button>
                <button type="button" @click="addLayout('2col'); showLayoutPicker = false" class="w-16 h-16 rounded-xl flex items-center justify-center gap-0.5 transition" style="border:2px solid #e5e7eb; background:#ffffff;" onmouseover="this.style.borderColor='#7c3aed';" onmouseout="this.style.borderColor='#e5e7eb';">
                    <div class="w-3 h-8 rounded" style="border:2px dashed #d1d5db;"></div>
                    <div class="w-3 h-8 rounded" style="border:2px dashed #d1d5db;"></div>
                </button>
                <button type="button" @click="addLayout('3col'); showLayoutPicker = false" class="w-16 h-16 rounded-xl flex items-center justify-center gap-0.5 transition" style="border:2px solid #e5e7eb; background:#ffffff;" onmouseover="this.style.borderColor='#7c3aed';" onmouseout="this.style.borderColor='#e5e7eb';">
                    <div class="w-2.5 h-8 rounded" style="border:2px dashed #d1d5db;"></div>
                    <div class="w-2.5 h-8 rounded" style="border:2px dashed #d1d5db;"></div>
                    <div class="w-2.5 h-8 rounded" style="border:2px dashed #d1d5db;"></div>
                </button>
                <button type="button" @click="addLayout('4col'); showLayoutPicker = false" class="w-16 h-16 rounded-xl flex items-center justify-center gap-0.5 transition" style="border:2px solid #e5e7eb; background:#ffffff;" onmouseover="this.style.borderColor='#7c3aed';" onmouseout="this.style.borderColor='#e5e7eb';">
                    <div class="w-2 h-8 rounded" style="border:2px dashed #d1d5db;"></div>
                    <div class="w-2 h-8 rounded" style="border:2px dashed #d1d5db;"></div>
                    <div class="w-2 h-8 rounded" style="border:2px dashed #d1d5db;"></div>
                    <div class="w-2 h-8 rounded" style="border:2px dashed #d1d5db;"></div>
                </button>
                <button type="button" @click="addLayout('2row'); showLayoutPicker = false" class="w-16 h-16 rounded-xl flex flex-col items-center justify-center gap-0.5 transition" style="border:2px solid #e5e7eb; background:#ffffff;" onmouseover="this.style.borderColor='#7c3aed';" onmouseout="this.style.borderColor='#e5e7eb';">
                    <div class="w-8 h-3.5 rounded" style="border:2px dashed #d1d5db;"></div>
                    <div class="w-8 h-3.5 rounded" style="border:2px dashed #d1d5db;"></div>
                </button>
                <button type="button" @click="addLayout('1plus2'); showLayoutPicker = false" class="w-16 h-16 rounded-xl flex flex-col items-center justify-center gap-0.5 transition" style="border:2px solid #e5e7eb; background:#ffffff;" onmouseover="this.style.borderColor='#7c3aed';" onmouseout="this.style.borderColor='#e5e7eb';">
                    <div class="w-8 h-3.5 rounded" style="border:2px dashed #d1d5db;"></div>
                    <div class="flex gap-0.5">
                        <div class="w-3.5 h-3.5 rounded" style="border:2px dashed #d1d5db;"></div>
                        <div class="w-3.5 h-3.5 rounded" style="border:2px dashed #d1d5db;"></div>
                    </div>
                </button>
                <button type="button" @click="addLayout('2row3'); showLayoutPicker = false" class="w-16 h-16 rounded-xl flex flex-col items-center justify-center gap-0.5 transition" style="border:2px solid #e5e7eb; background:#ffffff;" onmouseover="this.style.borderColor='#7c3aed';" onmouseout="this.style.borderColor='#e5e7eb';">
                    <div class="flex gap-0.5">
                        <div class="w-2 h-3.5 rounded" style="border:2px dashed #d1d5db;"></div>
                        <div class="w-2 h-3.5 rounded" style="border:2px dashed #d1d5db;"></div>
                        <div class="w-2 h-3.5 rounded" style="border:2px dashed #d1d5db;"></div>
                    </div>
                    <div class="flex gap-0.5">
                        <div class="w-2 h-3.5 rounded" style="border:2px dashed #d1d5db;"></div>
                        <div class="w-2 h-3.5 rounded" style="border:2px dashed #d1d5db;"></div>
                        <div class="w-2 h-3.5 rounded" style="border:2px dashed #d1d5db;"></div>
                    </div>
                </button>
            </div>

            <div class="mt-8 text-center">
                <button type="button" @click="showLayoutPicker = false" class="text-sm" style="color:#6b7280;">
                    {{ __('Cancel') }}
                </button>
            </div>
        </div>
    </div>

    @include('admin.partials.media-picker')
</div>

<script>
const RJ = {
    updateUrl: @json(route('admin.rjbuilder.update', $template)),
    csrf: @json(csrf_token()),
    name: @json($template->name),
    status: @json($template->status),
    templateType: @json($templateType),
    baseLocale: @json($baseLocale),
};

// IntersectionObserver must live outside Alpine's reactive proxy.
let rjObserver = null;

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

        leftTab: 'widgets',
        rightTab: 'content',
        device: 'desktop',
        preview: false,
        animPlayId: null,
        toast: { show: false, message: '', type: 'success' },

        templateType: RJ.templateType,
        baseLocale: RJ.baseLocale,
        locales: @json($locales),
        activeLocale: RJ.baseLocale,

        // Built-in "Motion / Advanced" fields (available on every widget)
        advancedSchema: [
            { name: '_animation', label: 'Entrance animation', type: 'select', default: 'none', options: {
                none: 'None', fadeIn: 'Fade In', fadeInUp: 'Fade In Up', fadeInDown: 'Fade In Down',
                fadeInLeft: 'Fade In Left', fadeInRight: 'Fade In Right', zoomIn: 'Zoom In',
                bounceIn: 'Bounce In', flipInX: 'Flip In X', rotateIn: 'Rotate In',
                slideUp: 'Slide Reveal', blurIn: 'Blur In', pulse: 'Pulse',
            }},
            { name: '_animation_duration', label: 'Duration', type: 'select', default: 'md', options: { sm: 'Fast (300ms)', md: 'Normal (600ms)', lg: 'Slow (1s)', xl: 'Very slow (1.5s)' } },
            { name: '_animation_delay', label: 'Delay', type: 'select', default: '0', options: { '0': 'None', '200ms': '200ms', '400ms': '400ms', '600ms': '600ms', '1s': '1s' } },
            { name: '_hover', label: 'Hover effect', type: 'select', default: 'none', options: { none: 'None', grow: 'Grow', lift: 'Lift + shadow', glow: 'Glow', tilt: '3D tilt', float: 'Float' } },
            { name: '_hide_desktop', label: 'Hide on desktop', type: 'toggle' },
            { name: '_hide_tablet', label: 'Hide on tablet', type: 'toggle' },
            { name: '_hide_mobile', label: 'Hide on mobile', type: 'toggle' },
            { name: '_css_class', label: 'Custom CSS class', type: 'text' },
        ],

        /* ───────── getters ───────── */
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

        get activeFields() {
            if (this.rightTab === 'advanced') return this.advancedSchema;
            const wantStyle = this.rightTab === 'style';
            return this.currentSchema.filter(f => this.isStyleField(f) === wantStyle);
        },

        get canvasStyle() {
            if (this.preview) {
                if (this.device === 'desktop') return 'max-width:100%;';
                return `max-width:${this.device === 'tablet' ? '820px' : '420px'}; background:#fff; box-shadow:0 0 0 1px #e5e7eb, 0 20px 50px rgba(17,24,39,.12); margin-top:24px; margin-bottom:24px; border-radius:18px; overflow:hidden;`;
            }
            if (this.device === 'desktop') return 'max-width:1152px;';
            const w = this.device === 'tablet' ? '820px' : '420px';
            return `max-width:${w}; background:#fff; box-shadow:0 0 0 1px #e5e7eb, 0 20px 50px rgba(17,24,39,.10); border-radius:18px; padding:24px 12px;`;
        },

        /* ───────── lifecycle ───────── */
        init() {
            this.pushHistory();
            setTimeout(() => { window.__builder = this; }, 50);
        },

        onKey(e) {
            const t = e.target;
            const typing = t && (['INPUT', 'TEXTAREA', 'SELECT'].includes(t.tagName) || t.isContentEditable);
            const mod = e.ctrlKey || e.metaKey;
            const key = (e.key || '').toLowerCase();

            if (mod && key === 's') { e.preventDefault(); this.save(); return; }
            if (key === 'escape') {
                if (this.preview) this.togglePreview();
                else if (!typing) this.selectedBlock = null;
                return;
            }
            if (typing || this.preview) return;

            if (mod && key === 'z') { e.preventDefault(); e.shiftKey ? this.redo() : this.undo(); return; }
            if (mod && key === 'y') { e.preventDefault(); this.redo(); return; }
            if (mod && key === 'd' && this.selectedBlock) { e.preventDefault(); this.duplicateSelected(); return; }
            if ((key === 'delete' || key === 'backspace') && this.selectedBlock) { e.preventDefault(); this.removeSelected(); }
        },

        showToast(message, type) {
            this.toast = { show: true, message, type: type || 'success' };
            clearTimeout(this._toastTimer);
            this._toastTimer = setTimeout(() => { this.toast.show = false; }, 2200);
        },

        /* ───────── helpers ───────── */
        esc(s) {
            return String(s == null ? '' : s)
                .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        },

        isStyleField(f) {
            if (f.tab) return f.tab === 'style';
            return /color|background|bg|padding|margin|gap|border|radius|rounded|size|align|width|height|font|weight|shadow|spacing|opacity/i.test(f.name);
        },

                isTranslatableField(name) {
            if (this.templateType !== 'page') return false;
            return ['text', 'alt', 'caption', 'title', 'description'].includes(name);
        },

        getBlockText(block) {
            const s = block.settings || {};
            if (this.templateType !== 'page') return s.text || '';
            if (this.activeLocale === this.baseLocale) return s.text || '';
            const t = (s.translations || {})[this.activeLocale];
            return (t && t.text) || s.text || '';
        },

        getBlockField(block, name) {
            const s = block.settings || {};
            if (!this.isTranslatableField(name)) return s[name];
            if (this.activeLocale === this.baseLocale) return s[name];
            const t = (s.translations || {})[this.activeLocale] || {};
            return t[name] !== undefined ? t[name] : s[name];
        },

        setBlockField(block, name, value) {
            const s = block.settings = block.settings || {};
            if (!this.isTranslatableField(name) || this.activeLocale === this.baseLocale) {
                s[name] = value;
                return;
            }
            if (!s.translations) s.translations = {};
            if (!s.translations[this.activeLocale]) s.translations[this.activeLocale] = {};
            s.translations[this.activeLocale][name] = value;
        },

        fieldValue(field) {
            const b = this.currentBlock;
            if (!b) return field.default !== undefined ? field.default : '';
            const v = this.getBlockField(b, field.name);
            if (v !== undefined && v !== null) return v;
            return field.default !== undefined ? field.default : '';
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

        emptySlot() {
            return this.createBlock('container', { columns: '1' });
        },

        /* ───────── layouts ───────── */
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
                topRow.children = [this.emptySlot()];
                container.children.push(topRow);

                const bottomRow = this.createBlock('container', { columns: '2' });
                bottomRow.children = [this.emptySlot(), this.emptySlot()];
                container.children.push(bottomRow);
            } else if (p.rowCols) {
                container.settings.columns = '1';
                for (let r = 0; r < p.rows; r++) {
                    const row = this.createBlock('container', { columns: String(p.rowCols) });
                    row.children = [];
                    for (let c = 0; c < p.rowCols; c++) row.children.push(this.emptySlot());
                    container.children.push(row);
                }
            } else if (p.rows && p.rows > 1) {
                container.settings.columns = '1';
                for (let r = 0; r < p.rows; r++) container.children.push(this.emptySlot());
            } else {
                for (let i = 0; i < p.cols; i++) container.children.push(this.emptySlot());
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
            container.children = [this.emptySlot()];
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
                const found = this.findBlockAndParent(this.selectedBlock);
                const block = found ? found.block : null;

                if (block && block.type === 'container') {
                    if (!block.children) block.children = [];
                    const newBlock = this.createBlock(widgetId);
                    const emptyIndex = block.children.findIndex(c => c.type === 'container' && (!c.children || c.children.length === 0));
                    if (emptyIndex === -1) block.children.push(newBlock);
                    else block.children[emptyIndex] = newBlock;
                    this.afterInsert(newBlock);
                    return;
                }

                // A leaf widget is selected → add the new one right after it
                if (block && found) {
                    const newBlock = this.createBlock(widgetId);
                    found.list.splice(found.index + 1, 0, newBlock);
                    this.afterInsert(newBlock);
                    return;
                }
            }
            this.insertAt(widgetId, this.blocks, this.blocks.length);
        },

        afterInsert(block) {
            this.selectedBlock = block.id;
            this.markDirty();
            this.pushHistory();
            this.playAnimation(block.id);
        },

        insertAt(widgetId, list, index) {
            const block = this.createBlock(widgetId);
            list.splice(index, 0, block);
            this.afterInsert(block);
        },

        /* ───────── drag & drop (new + move) ───────── */
        startDragNew(widgetId, event) {
            this.dragging = { type: 'new', widgetId };
            event.dataTransfer.effectAllowed = 'copy';
            event.dataTransfer.setData('text/plain', widgetId);
        },

        startDragMove(id, event) {
            this.dragging = { type: 'move', id };
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', id);
            const wrap = event.target.closest('[data-block-id]');
            if (wrap && event.dataTransfer.setDragImage) event.dataTransfer.setDragImage(wrap, 24, 16);
        },

        onDragOver(event, parentKey, index) {
            event.preventDefault();
            this.dragTarget = parentKey + ':' + index;
        },

        /** Take the block out of the tree. Multi-column parents keep an empty slot so the grid doesn't shift. */
        detach(found) {
            const { parent, index, list, block } = found;
            const cols = parent ? (parseInt((parent.settings || {}).columns) || 1) : 1;
            if (parent && parent.type === 'container' && cols > 1) list.splice(index, 1, this.emptySlot());
            else list.splice(index, 1);
            return block;
        },

        onDrop(event, parentKey, index) {
            event.preventDefault();
            this.dragTarget = null;
            const d = this.dragging;
            this.dragging = null;
            if (!d || parentKey !== 'root') return;

            if (d.type === 'new') {
                this.insertAt(d.widgetId, this.blocks, index);
                return;
            }

            const found = this.findBlockAndParent(d.id);
            if (!found) return;
            const sameRoot = found.parent === null;
            const oldIndex = found.index;
            const block = this.detach(found);
            let target = index;
            if (sameRoot && oldIndex < index) target--;
            this.blocks.splice(target, 0, block);
            this.selectedBlock = block.id;
            this.markDirty();
            this.pushHistory();
        },

        dropInto(containerId, slotIndex) {
            const d = this.dragging;
            this.dragging = null;
            this.dragTarget = null;
            document.querySelectorAll('.rj-drop').forEach(el => el.classList.remove('rj-drop'));
            if (!d) return;

            const container = this.findBlock(containerId);
            if (!container) return;

            let block;
            if (d.type === 'new') {
                block = this.createBlock(d.widgetId);
            } else {
                const found = this.findBlockAndParent(d.id);
                if (!found) return;
                // can't drop a block into itself or its own descendants
                if (d.id === containerId || this.findBlock(containerId, found.block.children || [])) return;
                block = this.detach(found);
            }

            if (!container.children) container.children = [];
            while (container.children.length <= slotIndex) container.children.push(this.emptySlot());

            const slot = container.children[slotIndex];
            if (slot.type === 'container') {
                if (!slot.children) slot.children = [];
                slot.children.push(block);
            } else {
                const wrap = this.emptySlot();
                wrap.children = [slot, block];
                container.children[slotIndex] = wrap;
            }

            this.selectedBlock = block.id;
            this.markDirty();
            this.pushHistory();
            if (d.type === 'new') this.playAnimation(block.id);
        },

        endDrag() {
            this.dragging = null;
            this.dragTarget = null;
            document.querySelectorAll('.rj-drop').forEach(el => el.classList.remove('rj-drop'));
        },

        /* ───────── block actions ───────── */
        selectBlock(id) {
            if (this.preview) return;
            const m = document.getElementById('rj-main');
            const top = m ? m.scrollTop : 0;
            this.selectedBlock = id;
            // re-render must not move the page
            this.$nextTick(() => { if (m) m.scrollTop = top; });
        },

        /** Scrolls the canvas while dragging near its top/bottom edge. */
        autoScroll(e) {
            if (!this.dragging) return;
            const m = document.getElementById('rj-main');
            if (!m) return;
            const r = m.getBoundingClientRect();
            const edge = 90;
            if (e.clientY < r.top + edge) m.scrollTop -= Math.ceil((r.top + edge - e.clientY) / 3);
            else if (e.clientY > r.bottom - edge) m.scrollTop += Math.ceil((e.clientY - (r.bottom - edge)) / 3);
        },

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
            this.setBlockField(this.currentBlock, name, value);
            this.markDirty();
            this.commitSoon();

            if (name.startsWith('_animation')) {
                const id = this.currentBlock.id;
                clearTimeout(this._animTimer);
                this._animTimer = setTimeout(() => this.playAnimation(id), 250);
            }
        },

        duplicateSelected() { if (this.selectedBlock) this.duplicateBlock(this.selectedBlock); },
        removeSelected() { if (this.selectedBlock) this.removeBlock(this.selectedBlock); },

        /* ───────── inline editing (double-click heading/text) ───────── */
        startInline(el, id) {
            if (this.preview) return;
            const b = this.findBlock(id);
            if (!b) return;

            this.selectedBlock = id;
            el.contentEditable = 'true';
            el.style.outline = '2px solid #ec4899';
            el.style.outlineOffset = '2px';
            el.focus();

            const range = document.createRange();
            range.selectNodeContents(el);
            const sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);

            const onKey = (e) => {
                if (e.key === 'Escape' || (e.key === 'Enter' && !e.shiftKey && b.type === 'heading')) {
                    e.preventDefault();
                    el.blur();
                }
            };
            const done = () => {
                el.removeEventListener('blur', done);
                el.removeEventListener('keydown', onKey);
                el.contentEditable = 'false';
                el.style.outline = '';
                const txt = el.innerText.replace(/\n+$/, '');
                const old = this.getBlockText(b);
                if (old !== txt) {
                    this.setBlockField(b, 'text', txt);
                    this.markDirty();
                    this.pushHistory();
                }
            };
            el.addEventListener('blur', done);
            el.addEventListener('keydown', onKey);
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
            window.dispatchEvent(new CustomEvent('open-media-picker', { detail: { value: mockInput } }));
        },

        /* ───────── device + preview + animation playback ───────── */
        setDevice(d) {
            this.device = d;
            if (this.preview) this.runPreviewAnimations();
        },

        togglePreview() {
            this.preview = !this.preview;
            this.selectedBlock = null;
            this.runPreviewAnimations();
        },

        runPreviewAnimations() {
            if (rjObserver) { rjObserver.disconnect(); rjObserver = null; }
            if (!this.preview) return;
            setTimeout(() => {
                const els = document.querySelectorAll('#rj-canvas .rj-wait');
                rjObserver = new IntersectionObserver((entries) => {
                    entries.forEach(en => {
                        if (!en.isIntersecting) return;
                        en.target.classList.remove('rj-wait');
                        en.target.classList.add('rj-play');
                        rjObserver.unobserve(en.target);
                    });
                }, { threshold: 0.12 });
                els.forEach(el => rjObserver.observe(el));
            }, 80);
        },

        durationMs(settings) {
            return { sm: 300, md: 600, lg: 1000, xl: 1500 }[settings._animation_duration || 'md'] || 600;
        },

        delayMs(settings) {
            const d = settings._animation_delay || '0';
            if (d === '0') return 0;
            return d.endsWith('ms') ? parseInt(d) : Math.round(parseFloat(d) * 1000);
        },

        /** Replays a block's entrance animation in the editor. */
        playAnimation(id) {
            const b = id ? this.findBlock(id) : null;
            if (!b || !b.settings || !b.settings._animation || b.settings._animation === 'none') return;
            this.animPlayId = null;
            setTimeout(() => {
                this.animPlayId = id;
                clearTimeout(this._playTimer);
                this._playTimer = setTimeout(() => { this.animPlayId = null; },
                    this.durationMs(b.settings) + this.delayMs(b.settings) + 200);
            }, 30);
        },

        /* ───────── rendering ───────── */
        toolbarBtn(icon, title, action, danger) {
            const hover = danger ? 'rgba(239,68,68,0.5)' : 'rgba(255,255,255,0.15)';
            return `<button type="button" title="${title}" onclick="event.stopPropagation(); ${action}" style="width:22px;height:22px;border-radius:9999px;background:transparent;border:0;color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:12px;line-height:1;" onmouseover="this.style.background='${hover}'" onmouseout="this.style.background='transparent'">${icon}</button>`;
        },

        renderBlockHtml(block, parentKey, index) {
            const w = this.widgets.find(x => x.id === block.type);
            if (!w) return '<div class="p-3 text-xs" style="color:#dc2626;">Unknown widget</div>';

            const s = block.settings || {};
            const hiddenHere = !!s['_hide_' + this.device];
            const hasAnim = s._animation && s._animation !== 'none';

            if (this.preview && hiddenHere) return '';

            let inner = block.type === 'container' ? this.renderContainer(block) : this.renderSimpleWidget(block);

            // entrance animation
            if (hasAnim) {
                if (this.preview) inner = this.animationWrapper(inner, s, 'rj-wait');
                else if (this.animPlayId === block.id) inner = this.animationWrapper(inner, s, 'rj-play');
            }

            // hover effect (preview only)
            if (this.preview && s._hover && s._hover !== 'none') {
                inner = `<div class="rj-h rj-h-${s._hover}">${inner}</div>`;
            }

            const customClass = s._css_class ? ' ' + this.esc(s._css_class) : '';

            if (this.preview) return `<div class="${customClass.trim()}">${inner}</div>`;

            const isSelected = this.selectedBlock === block.id;
            const bid = block.id;
            const hiddenNote = hiddenHere
                ? `<div style="position:absolute;top:6px;right:8px;z-index:20;font-size:9px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;background:#1a1030;color:#fbbf24;border-radius:9999px;padding:2px 8px;">Hidden on ${this.device}</div>`
                : '';

            return `
                <div class="rj-block relative${customClass}" data-block-id="${bid}" style="${hiddenHere ? 'opacity:.35;' : ''}" onclick="event.stopPropagation(); window.__builder.selectBlock('${bid}')">
                    ${hiddenNote}
                    <div style="position:absolute;top:-14px;left:50%;transform:translateX(-50%);z-index:30;display:flex;align-items:center;gap:2px;background:#1a1030;color:#fff;border-radius:9999px;padding:2px 6px;box-shadow:0 4px 12px rgba(0,0,0,0.2);opacity:${isSelected ? 1 : 0};pointer-events:${isSelected ? 'auto' : 'none'};transition:opacity 0.15s;white-space:nowrap;">
                        <span style="font-size:9px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;padding:0 4px;color:#a855f7;">${this.esc(this.blockLabel(block.type))}${hasAnim ? ' ✦' : ''}</span>
                        <span draggable="true" ondragstart="event.stopPropagation(); window.__builder.startDragMove('${bid}', event)" title="Drag to move" style="width:22px;height:22px;display:flex;align-items:center;justify-content:center;cursor:grab;font-size:13px;">⠿</span>
                        ${this.toolbarBtn('↑', 'Move up', `window.__builder.moveUp('${bid}')`)}
                        ${this.toolbarBtn('↓', 'Move down', `window.__builder.moveDown('${bid}')`)}
                        ${this.toolbarBtn('⧉', 'Duplicate', `window.__builder.duplicateBlock('${bid}')`)}
                        ${this.toolbarBtn('✕', 'Delete', `window.__builder.removeBlock('${bid}')`, true)}
                    </div>
                    <div class="rj-ring" style="${isSelected ? 'outline:2px solid #7c3aed; outline-offset:-2px;' : ''} border-radius:12px;">${inner}</div>
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
            const minHeight = settings.min_height && settings.min_height !== 'auto' ? ({ sm: '200px', md: '400px', lg: '600px' }[settings.min_height] || 'auto') : 'auto';
            const border = settings.border === 'solid' ? '2px solid #e5e7eb' : settings.border === 'dashed' ? '2px dashed #e5e7eb' : 'none';
            const alignMap = { start: 'flex-start', center: 'center', end: 'flex-end', stretch: 'stretch' };
            const align = alignMap[settings.align] || 'stretch';
            const isFullWidth = settings.width === 'full';

            const children = block.children || [];
            const bid = block.id;

            // responsive columns
            let effCols = cols;
            if (this.device === 'tablet') effCols = Math.min(cols, 2);
            if (this.device === 'mobile') effCols = 1;

            // render every child (row presets have 1 column but several children)
            const slotCount = Math.max(cols, children.length);
            let slotsHtml = '';

            for (let i = 0; i < slotCount; i++) {
                const child = children[i];

                if (this.preview) {
                    slotsHtml += `<div style="min-width:0;">${child ? this.renderBlockHtml(child, bid, i) : ''}</div>`;
                    continue;
                }

                const inner = child
                    ? this.renderBlockHtml(child, bid, i)
                    : `<div class="min-h-[80px] flex flex-col items-center justify-center" style="color:#d1d5db;">
                           <svg class="w-5 h-5 mb-1 pointer-events-none" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                           <span class="text-[9px] uppercase tracking-wider pointer-events-none">${i + 1}</span>
                       </div>`;

                slotsHtml += `
                    <div class="relative min-h-[80px] rounded-lg transition" style="min-width:0; border:2px ${child ? 'solid transparent' : 'dashed #e5e7eb'};"
                         ondragover="event.preventDefault(); event.stopPropagation(); this.classList.add('rj-drop');"
                         ondragleave="this.classList.remove('rj-drop');"
                         ondrop="event.preventDefault(); event.stopPropagation(); window.__builder.dropInto('${bid}', ${i});">
                        ${inner}
                    </div>
                `;
            }

            const chrome = this.preview
                ? `background:${bg}; padding:${padding}; border:${border}; min-height:${minHeight};`
                : `background:${bg}; padding:${padding}; border:${border}; min-height:${minHeight};`;
            const outerStyle = isFullWidth
                ? `${chrome} width:100%; margin:0;`
                : `${chrome} max-width:1100px; margin:0 auto; width:100%;`;

            return `
                <div class="rounded-xl" style="${outerStyle}">
                    <div style="display:grid; grid-template-columns: repeat(${effCols}, minmax(0, 1fr)); gap:${gap}; align-items:${align};">
                        ${slotsHtml}
                    </div>
                </div>
            `;
        },

        renderSimpleWidget(block) {
            const settings = block.settings || {};
            const esc = (s) => this.esc(s);
            const bid = block.id;
            const inline = this.preview ? '' : `class="rj-inline-edit" ondblclick="event.stopPropagation(); window.__builder.startInline(this, '${bid}')"`;
            const mobile = this.device === 'mobile';

            }


            const w = this.widgets.find(x => x.id === block.type);
            return `<div style="padding:1rem; text-align:center; font-size:0.75rem; color:#6b7280;">${esc(w ? w.name : block.type)}</div>`;
        },

        animationWrapper(html, settings, stateClass) {
            const dur = settings._animation_duration || 'md';
            const delay = settings._animation_delay || '0';
            const durations = { sm: '300ms', md: '600ms', lg: '1000ms', xl: '1500ms' };
            let style = `animation-duration:${durations[dur] || '600ms'};`;
            if (delay !== '0') style += ` animation-delay:${delay};`;
            return `<div class="rj-a rj-a-${this.esc(settings._animation)} ${stateClass}" style="${style}">${html}</div>`;
        },

        /* ───────── layers panel ───────── */
        navigatorHtml() {
            const walk = (list, depth) => list.map(b => {
                const sel = this.selectedBlock === b.id;
                const isContainer = b.type === 'container';
                const s = b.settings || {};
                const hint = isContainer
                    ? `${parseInt(s.columns) || 1} col`
                    : this.esc(String(s.text || s.alt || '').slice(0, 22));
                const anim = s._animation && s._animation !== 'none' ? ' <span style="color:#a855f7;">✦</span>' : '';
                const row = `
                    <div class="rj-nav flex items-center gap-2 rounded-md pr-2 py-1.5 text-xs"
                         style="padding-left:${8 + depth * 14}px; ${sel ? 'background:rgba(124,58,237,.10); color:#5b21b6; font-weight:600;' : 'color:#374151;'}"
                         onclick="event.stopPropagation(); window.__builder.selectBlock('${b.id}')">
                        <span style="width:14px;text-align:center;color:#9ca3af;">${isContainer ? '▣' : '◦'}</span>
                        <span class="truncate">${this.esc(this.blockLabel(b.type))}${anim}</span>
                        <span class="truncate ml-auto" style="color:#9ca3af;font-size:10px;max-width:90px;">${hint}</span>
                    </div>`;
                const kids = b.children && b.children.length ? walk(b.children, depth + 1) : '';
                return row + kids;
            }).join('');
            return walk(this.blocks, 0);
        },

        /* ───────── history & saving ───────── */
        markDirty() { this.saved = false; },

        commitSoon() {
            clearTimeout(this._historyTimer);
            this._historyTimer = setTimeout(() => this.pushHistory(), 500);
        },

        pushHistory() {
            const snap = JSON.stringify(this.blocks);
            if (this.history[this.historyIndex] === snap) return;
            this.history = this.history.slice(0, this.historyIndex + 1);
            this.history.push(snap);
            this.historyIndex = this.history.length - 1;
            if (this.history.length > 50) { this.history.shift(); this.historyIndex--; }
        },

        undo() {
            this.pushHistory();
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
            if (this.saving) return;
            this.saving = true;
            try {
                const res = await fetch(RJ.updateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': RJ.csrf,
                        'X-HTTP-Method-Override': 'PUT',
                    },
                    body: JSON.stringify({
                        name: RJ.name,
                        content: { blocks: this.blocks },
                        status: RJ.status,
                    }),
                });
                if (res.ok || res.redirected) {
                    this.saved = true;
                    this.showToast('Saved');
                } else {
                    this.showToast('Save failed (' + res.status + ')', 'error');
                }
            } catch (e) {
                console.error(e);
                this.showToast('Network error — not saved', 'error');
            } finally {
                this.saving = false;
            }
        },
    };
}
</script>
@endsection