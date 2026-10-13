@extends('rjbuilder.layout')

@section('title', $template->name)

@section('content')
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

    // Every UI string used by the builder JS. Missing keys fall back to English.
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
        'Hidden on', 'Image', 'Unknown widget', 'Drop here', 'Save (Ctrl+S)', 'Close panel', 'Open panel',
    ])->unique()->mapWithKeys(fn ($s) => [$s => __($s)])->toArray();
@endphp

@verbatim
<style>
    [x-cloak] { display: none !important; }

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

    /* ───────── Editor chrome (dark, BeBuilder-style) ───────── */
    .rj-app {
        --bg0: #16171b; --bg1: #1f2025; --bg2: #2a2b32; --bg3: #373843; --line: #383a43;
        --tx: #e8e9ed; --mut: #8e919d; --acc: #8b5cf6; --acc2: #ec4899; --ok: #2f9e5f;
        height: 100vh; height: 100dvh; display: flex; flex-direction: column; overflow: hidden;
        color: var(--tx);
    }
    .rj-app * { box-sizing: border-box; }
    .rj-app button { font-family: inherit; }

    /* top bar */
    .rj-top, .rj-prevbar { height: 52px; flex-shrink: 0; background: var(--bg0); border-bottom: 1px solid #000; display: flex; align-items: center; justify-content: space-between; padding: 0 10px; gap: 12px; }
    .rj-grp { display: flex; align-items: center; gap: 4px; min-width: 0; }
    .rj-sep { width: 1px; height: 24px; background: var(--line); margin: 0 6px; flex-shrink: 0; }
    .rj-ib { width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; color: #a9acb8; background: transparent; border: 0; cursor: pointer; transition: background .12s, color .12s; flex-shrink: 0; text-decoration: none; }
    .rj-ib:hover { background: var(--bg2); color: #fff; }
    .rj-ib[data-active="true"] { background: var(--bg3); color: #fff; }
    .rj-ib:disabled { opacity: .3; cursor: default; background: transparent; }
    .rj-ib svg { width: 19px; height: 19px; }
    .rj-ib:focus-visible, .rj-btn:focus-visible, .rj-tile:focus-visible, .rj-tab:focus-visible { outline: 2px solid var(--acc); outline-offset: 1px; }
    .rj-seg { display: flex; align-items: center; gap: 2px; padding: 3px; border-radius: 10px; background: var(--bg1); }
    .rj-seg .rj-ib { width: 34px; height: 30px; border-radius: 7px; }
    .rj-seg .rj-lc { height: 30px; padding: 0 10px; border-radius: 7px; border: 0; background: transparent; color: #a9acb8; font-size: 11px; font-weight: 700; text-transform: uppercase; cursor: pointer; }
    .rj-seg .rj-lc[data-active="true"] { background: var(--acc); color: #fff; }
    .rj-title { min-width: 0; line-height: 1.25; }
    .rj-title b { display: block; font-size: 13px; font-weight: 600; color: #fff; max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .rj-title small { font-size: 10px; letter-spacing: .06em; text-transform: uppercase; color: #a78bfa; }
    .rj-btn { height: 34px; padding: 0 14px; border-radius: 8px; border: 1px solid var(--line); background: transparent; color: var(--tx); font-size: 13px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 7px; transition: background .12s; white-space: nowrap; }
    .rj-btn:hover { background: var(--bg2); }
    .rj-btn svg { width: 16px; height: 16px; }
    .rj-save { border: 0; background: var(--ok); color: #fff; font-weight: 600; padding: 0 20px; }
    .rj-save:hover { background: #38b06c; }
    .rj-save:disabled { opacity: .55; cursor: default; }
    .rj-badge { display: inline-flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 600; color: var(--mut); padding: 0 6px; }
    .rj-badge i { width: 7px; height: 7px; border-radius: 50%; background: #f59e0b; display: inline-block; }
    .rj-badge[data-saved="true"] i { background: #34d399; }

    /* side panel */
    .rj-body { flex: 1; min-height: 0; display: flex; overflow: hidden; position: relative; }
    .rj-panel { width: 340px; flex-shrink: 0; background: var(--bg1); border-right: 1px solid #000; display: flex; flex-direction: column; min-height: 0; }
    .rj-pcol { flex: 1; min-height: 0; display: flex; flex-direction: column; }
    .rj-ph { height: 50px; flex-shrink: 0; display: flex; align-items: center; justify-content: space-between; padding: 0 16px; font-size: 15px; font-weight: 600; background: #1a1b20; border-bottom: 1px solid #14151a; gap: 8px; }
    .rj-ph .rj-ib { width: 30px; height: 30px; margin-left: -8px; }
    .rj-pb { flex: 1; min-height: 0; overflow-y: auto; overscroll-behavior: contain; padding: 16px; }
    .rj-pf { flex-shrink: 0; padding: 12px 16px; border-top: 1px solid var(--line); background: #1a1b20; display: flex; gap: 8px; }
    .rj-sel, .rj-in, .rj-ta { width: 100%; background: #15161a; color: var(--tx); border: 1px solid var(--line); border-radius: 6px; padding: 8px 10px; font-size: 13px; outline: none; font-family: inherit; }
    .rj-sel { width: auto; padding: 4px 8px; font-size: 12px; background: var(--bg2); }
    .rj-in:focus, .rj-ta:focus, .rj-sel:focus { border-color: var(--acc); box-shadow: 0 0 0 2px rgba(139,92,246,.25); }
    .rj-in::placeholder { color: #666977; }
    .rj-ta { resize: vertical; line-height: 1.5; }
    .rj-lbl { display: flex; align-items: center; justify-content: space-between; font-size: 12px; font-weight: 500; color: #b9bcc8; margin-bottom: 6px; }
    .rj-reset { border: 0; background: transparent; color: var(--mut); cursor: pointer; font-size: 12px; padding: 0 2px; }
    .rj-reset:hover { color: #fff; }
    .rj-help { font-size: 11px; color: var(--mut); margin-top: 4px; }
    .rj-field { margin-bottom: 16px; }
    .rj-hint { font-size: 12px; color: var(--mut); line-height: 1.5; text-align: center; padding: 10px 12px 14px; border: 1px dashed var(--line); border-radius: 6px; margin-bottom: 14px; }
    .rj-tabs { display: flex; padding: 0 8px; background: #1a1b20; border-bottom: 1px solid var(--line); flex-shrink: 0; }
    .rj-tab { position: relative; flex: 1; padding: 11px 0; background: transparent; border: 0; color: var(--mut); font-size: 12px; font-weight: 600; cursor: pointer; }
    .rj-tab:hover { color: #fff; }
    .rj-tab[data-active="true"] { color: #fff; }
    .rj-tab[data-active="true"]::after { content: ''; position: absolute; left: 14px; right: 14px; bottom: -1px; height: 2px; background: var(--acc); border-radius: 2px; }

    .rj-sech { width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 8px 0; margin-bottom: 8px; background: transparent; border: 0; color: #c9ccd8; font-size: 14px; cursor: pointer; position: relative; }
    .rj-sech .rj-chev { position: absolute; right: 8px; color: var(--mut); transition: transform .15s; }
    .rj-sech .rj-chev[data-open="false"] { transform: rotate(-90deg); }
    .rj-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; margin-bottom: 18px; }
    .rj-tile { position: relative; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px; padding: 20px 8px 14px; min-height: 100px; background: var(--bg2); border: 1px solid var(--line); border-radius: 6px; color: #c4c7d3; font-size: 12.5px; text-align: center; cursor: grab; user-select: none; transition: border-color .12s, background .12s, color .12s; }
    .rj-tile:hover { border-color: var(--acc); background: #30313a; color: #fff; }
    .rj-tile:active { cursor: grabbing; }
    .rj-ico { color: #8a8d9b; display: flex; align-items: center; justify-content: center; height: 30px; }
    .rj-tile:hover .rj-ico { color: #c4b5fd; }
    .rj-ico svg { width: 28px; height: 28px; stroke: currentColor; }
    .rj-star { position: absolute; top: 4px; right: 4px; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; border-radius: 6px; font-size: 14px; color: #777a88; opacity: 0; cursor: pointer; transition: opacity .12s; }
    .rj-tile:hover .rj-star, .rj-star[data-on="true"] { opacity: 1; }
    .rj-star[data-on="true"] { color: #fbbf24; }
    .rj-star:hover { background: rgba(255,255,255,.08); color: #fbbf24; }

    .rj-mini { display: flex; flex-direction: column; gap: 3px; align-items: center; justify-content: center; width: 100%; padding: 10px 6px; background: var(--bg2); border: 1px solid var(--line); border-radius: 6px; cursor: pointer; transition: border-color .12s; }
    .rj-mini:hover { border-color: var(--acc); }
    .rj-mini .r { display: flex; gap: 3px; width: 100%; }
    .rj-mini .c { flex: 1; height: 16px; border: 1.5px dashed #5d6070; border-radius: 3px; }
    .rj-mini:hover .c { border-color: #a78bfa; }
    .rj-mini span { font-size: 11px; color: var(--mut); margin-top: 6px; }
    .rj-sect { text-align: left; padding: 12px; background: var(--bg2); border: 1px solid var(--line); border-radius: 6px; cursor: pointer; color: var(--tx); width: 100%; transition: border-color .12s; }
    .rj-sect:hover { border-color: var(--acc); }
    .rj-sect b { display: block; font-size: 13px; margin-bottom: 2px; }
    .rj-sect span { font-size: 11.5px; color: var(--mut); }
    .rj-h3 { font-size: 11px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: var(--mut); margin: 0 0 10px; }

    .rj-crumbs { display: flex; flex-wrap: wrap; align-items: center; gap: 2px; padding: 10px 16px; border-bottom: 1px solid var(--line); background: #1a1b20; font-size: 11px; flex-shrink: 0; }
    .rj-crumbs button { background: transparent; border: 0; color: var(--mut); cursor: pointer; padding: 2px 4px; border-radius: 4px; font-size: 11px; }
    .rj-crumbs button:hover { color: #fff; background: var(--bg2); }
    .rj-crumbs button[data-last="true"] { color: #c4b5fd; font-weight: 600; }
    .rj-crumbs i { color: #555866; font-style: normal; }

    .rj-switch { position: relative; width: 36px; height: 20px; flex-shrink: 0; }
    .rj-switch input { opacity: 0; position: absolute; inset: 0; margin: 0; cursor: pointer; z-index: 2; }
    .rj-switch span { position: absolute; inset: 0; border-radius: 20px; background: #444654; transition: background .15s; }
    .rj-switch span::after { content: ''; position: absolute; left: 3px; top: 3px; width: 14px; height: 14px; border-radius: 50%; background: #fff; transition: transform .15s; }
    .rj-switch input:checked + span { background: var(--acc); }
    .rj-switch input:checked + span::after { transform: translateX(16px); }
    .rj-switch input:focus-visible + span { outline: 2px solid var(--acc); outline-offset: 2px; }
    .rj-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 8px 0; font-size: 13px; color: #d4d6df; }

    /* layers */
    .rj-nav { display: flex; align-items: center; gap: 6px; padding: 6px 8px 6px 4px; border-radius: 6px; font-size: 12.5px; color: #c4c7d3; cursor: pointer; }
    .rj-nav:hover { background: var(--bg2); }
    .rj-nav[data-sel="true"] { background: rgba(139,92,246,.22); color: #fff; }
    .rj-nav .rj-caret { width: 16px; text-align: center; color: var(--mut); transition: transform .12s; font-size: 10px; flex-shrink: 0; }
    .rj-nav .rj-caret[data-open="true"] { transform: rotate(90deg); }
    .rj-nav .rj-nlabel { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .rj-nav .rj-nhint { margin-left: auto; color: #6d7080; font-size: 10.5px; max-width: 80px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .rj-nav .rj-nact { display: none; gap: 2px; margin-left: auto; }
    .rj-nav:hover .rj-nact { display: flex; }
    .rj-nav:hover .rj-nhint { display: none; }
    .rj-nact span { width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center; border-radius: 5px; font-size: 12px; color: #b9bcc8; }
    .rj-nact span:hover { background: rgba(255,255,255,.12); color: #fff; }

    /* history */
    .rj-hist { display: flex; align-items: center; gap: 10px; width: 100%; text-align: left; padding: 9px 10px; border-radius: 6px; border: 0; background: transparent; color: #c4c7d3; font-size: 12.5px; cursor: pointer; }
    .rj-hist:hover { background: var(--bg2); }
    .rj-hist[data-cur="true"] { background: rgba(139,92,246,.22); color: #fff; }
    .rj-hist[data-future="true"] { opacity: .45; }
    .rj-hist i { width: 8px; height: 8px; border-radius: 50%; background: #5d6070; flex-shrink: 0; }
    .rj-hist[data-cur="true"] i { background: var(--acc); }
    .rj-hist small { margin-left: auto; color: var(--mut); font-size: 10.5px; }

    /* panel handle */
    .rj-handle { position: absolute; top: 50%; transform: translateY(-50%); z-index: 40; width: 18px; height: 64px; background: #16171b; border: 0; border-radius: 0 8px 8px 0; color: #c4c7d3; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px; }
    .rj-handle:hover { color: #fff; background: #000; }

    /* canvas */
    .rj-main { flex: 1; min-width: 0; min-height: 0; background: #e9ebf0; overflow-y: auto; overflow-x: hidden; overscroll-behavior: contain; }
    .rj-main[data-preview="true"] { background: #d7dae1; }
    .rj-canvas { margin: 0 auto; transition: max-width .3s; }
    .rj-page { background: #fff; box-shadow: 0 1px 0 rgba(0,0,0,.04), 0 12px 40px rgba(17,24,39,.10); border-radius: 10px; min-height: 60vh; }
    .rj-devlabel { text-align: center; font-size: 11px; color: #7b7f8c; margin: 0 0 10px; }
    .rj-block:hover { z-index: 2; }
    .rj-outlines .rj-block:hover:not(:has(.rj-block:hover)) > .rj-ring { outline: 1px solid rgba(124,58,237,.55); outline-offset: -1px; }
    .rj-drop { border-color: #7c3aed !important; background: rgba(124,58,237,.08) !important; }
    .rj-inline-edit { cursor: text; }
    .rj-empty { max-width: 860px; margin: 0 auto; text-align: center; padding-top: 24px; color: #111827; }
    .rj-preset { width: 84px; padding: 10px 8px 8px; background: #fff; border: 2px solid #e5e7eb; border-radius: 12px; cursor: pointer; transition: border-color .12s, transform .12s; }
    .rj-preset:hover { border-color: #7c3aed; transform: translateY(-2px); }
    .rj-preset .r { display: flex; gap: 3px; margin-bottom: 3px; }
    .rj-preset .c { flex: 1; height: 16px; border: 2px dashed #d1d5db; border-radius: 4px; }
    .rj-addsec { display: inline-flex; align-items: center; gap: 12px; padding: 14px 22px; border: 2px dashed #c3c7d0; border-radius: 16px; background: #fff; cursor: pointer; text-align: left; transition: border-color .12s, background .12s; }
    .rj-addsec:hover { border-color: #7c3aed; background: rgba(124,58,237,.04); }
    .rj-cta { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 10px; border: 0; color: #fff; font-size: 14px; font-weight: 600; cursor: pointer; background: linear-gradient(to right, #7c3aed, #ec4899); }

    .rj-modal { position: fixed; inset: 0; z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 16px; }
    .rj-modal > .bg { position: absolute; inset: 0; background: rgba(0,0,0,.6); backdrop-filter: blur(4px); }
    .rj-modal > .box { position: relative; background: #fff; color: #111827; border-radius: 18px; width: 100%; max-width: 720px; padding: 32px; box-shadow: 0 25px 50px -12px rgba(0,0,0,.4); }
    .rj-toast { position: fixed; top: 64px; right: 24px; padding: 10px 16px; border-radius: 8px; color: #fff; font-size: 13px; font-weight: 500; z-index: 10000; box-shadow: 0 10px 30px rgba(0,0,0,.25); }
</style>
@endverbatim

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

{{-- Data from PHP (kept separate so the JS below can live inside @verbatim) --}}
<script>
window.RJ = {
    updateUrl: @json(route('admin.rjbuilder.update', $template)),
    csrf: @json(csrf_token()),
    name: @json($template->name),
    status: @json($template->status),
    templateType: @json($templateType),
    baseLocale: @json($baseLocale),
    locales: @json($locales),
    widgets: @json($widgetsData),
    categories: @json($categoriesData),
    blocks: @json($blocksData),
    i18n: @json($i18n),
};
</script>

@verbatim
<script>
// IntersectionObserver must live outside Alpine's reactive proxy.
let rjObserver = null;

function builder() {
    const L = window.RJ;

    const SVG = (inner) => '<svg fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">' + inner + '</svg>';

    const store = {
        get(key, fallback) { try { const v = localStorage.getItem(key); return v === null ? fallback : JSON.parse(v); } catch (e) { return fallback; } },
        set(key, value) { try { localStorage.setItem(key, JSON.stringify(value)); } catch (e) { /* storage unavailable */ } },
    };

    return {
        widgets: L.widgets,
        categories: L.categories || {},
        blocks: L.blocks,
        selectedBlock: null,
        widgetSearch: '',
        catFilter: 'all',
        saving: false,
        saved: true,
        savedSnap: '',
        history: [],
        historyIndex: -1,
        clipboard: null,
        mediaPickerTarget: null,
        dragging: null,
        dragTarget: null,
        showLayoutPicker: false,
        _inited: false,

        // panels
        panel: 'add',
        panelOpen: true,
        editTab: 'content',
        collapsed: {},
        navCollapsed: {},
        favs: store.get('rj.favs', []),
        recents: store.get('rj.recent', []),
        prefs: Object.assign({ outlines: true, autosave: false, zoom: 100 }, store.get('rj.prefs', {})),

        // view
        device: 'desktop',
        preview: false,
        previewRun: 0,
        animPlayId: null,
        toast: { show: false, message: '', type: 'success' },

        templateType: L.templateType,
        baseLocale: L.baseLocale,
        locales: (function (l) { return Array.isArray(l) ? l : Object.keys(l || {}); })(L.locales),
        activeLocale: L.baseLocale,
        // settings keys that are translated per language (add your own widget fields here)
        translatableFields: ['text', 'alt', 'caption', 'title', 'subtitle', 'description', 'label', 'button_text', 'content', 'placeholder'],

        devices: [
            { id: 'desktop', label: 'Desktop', svg: SVG('<path d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>') },
            { id: 'tablet',  label: 'Tablet',  svg: SVG('<rect x="5" y="2" width="14" height="20" rx="2"/><path d="M11 18h2"/>') },
            { id: 'mobile',  label: 'Mobile',  svg: SVG('<rect x="7" y="2" width="10" height="20" rx="2"/><path d="M11 18h2"/>') },
        ],

        layoutPresets: [
            { key: '1col',   title: '1 Column',   rows: [[1]] },
            { key: '2col',   title: '2 Columns',  rows: [[1, 1]] },
            { key: '3col',   title: '3 Columns',  rows: [[1, 1, 1]] },
            { key: '4col',   title: '4 Columns',  rows: [[1, 1, 1, 1]] },
            { key: '2row',   title: '2 Rows',     rows: [[1], [1]] },
            { key: '1plus2', title: '1 + 2',      rows: [[1], [1, 1]] },
            { key: '2row3',  title: '2 Rows × 3', rows: [[1, 1, 1], [1, 1, 1]] },
        ],

        shortcuts: [
            ['Ctrl S', 'Save'], ['Ctrl Z', 'Undo'], ['Ctrl Shift Z', 'Redo'], ['Ctrl D', 'Duplicate'],
            ['Ctrl C / V', 'Copy / Paste'], ['Del', 'Delete'], ['Alt ↑ / ↓', 'Move up / down'], ['P', 'Preview'], ['Esc', 'Deselect / exit preview'],
        ],

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

        get shownPanel() {
            if (this.panel === 'edit' && !this.selectedBlock) return 'add';
            return this.panel;
        },

        get currentBlock() {
            if (!this.selectedBlock) return null;
            return this.findBlock(this.selectedBlock);
        },

        get currentSchema() {
            if (!this.currentBlock) return [];
            const w = this.widgets.find(x => x.id === this.currentBlock.type);
            return w ? (w.schema || []) : [];
        },

        get currentSettings() {
            if (!this.currentBlock) return {};
            return this.currentBlock.settings || {};
        },

        get activeFields() {
            if (this.editTab === 'advanced') return this.advancedSchema;
            const wantStyle = this.editTab === 'style';
            return this.currentSchema.filter(f => this.isStyleField(f) === wantStyle);
        },

        get breadcrumb() {
            if (!this.selectedBlock) return [];
            return this.findPath(this.selectedBlock) || [];
        },

        get categoryList() {
            const keys = Object.keys(this.categories);
            const used = new Set(this.widgets.map(w => w.category));
            const list = keys.filter(k => used.has(k)).map(k => ({ key: k, label: this.categories[k].label || k, icon: this.categories[k].icon || '' }));
            const extra = [...used].filter(k => !keys.includes(k));
            if (extra.length) list.push({ key: '__other', label: 'Other', icon: '•' });
            return list;
        },

        get widgetSections() {
            const q = this.widgetSearch.trim().toLowerCase();
            const match = (w) => !q || w.name.toLowerCase().includes(q) || w.id.toLowerCase().includes(q);
            const byId = (id) => this.widgets.find(w => w.id === id);
            const out = [];

            if (!q && this.catFilter === 'all') {
                out.push({ key: '__fav', kind: 'fav', icon: '☆', label: this.t('Favourite elements'), items: this.favs.map(byId).filter(Boolean) });
                const rec = this.recents.map(byId).filter(Boolean);
                if (rec.length) out.push({ key: '__rec', kind: 'rec', icon: '◷', label: this.t('Recently used'), items: rec });
            }

            const known = Object.keys(this.categories);
            this.categoryList.forEach(c => {
                if (this.catFilter !== 'all' && this.catFilter !== c.key) return;
                const items = this.widgets.filter(w => (c.key === '__other' ? !known.includes(w.category) : w.category === c.key) && match(w));
                if (items.length) out.push({ key: c.key, kind: 'cat', icon: c.icon, label: c.label, items });
            });

            return out;
        },

        get sectionTemplates() {
            if (!this.hasWidget('heading') || !this.hasWidget('text')) return [];
            const list = [
                { key: 'hero', title: 'Hero', desc: 'Large headline with a short intro.' },
                { key: 'features', title: 'Three features', desc: 'Three columns, each with a title and text.' },
                { key: 'cta', title: 'Call to action', desc: 'Dark banner with a headline and one line.' },
            ];
            if (this.hasWidget('image')) list.splice(2, 0, { key: 'split', title: 'Image + text', desc: 'Picture on the left, copy on the right.' });
            return list;
        },

        get historyList() {
            return this.history.map((h, i) => ({ i, label: h.label, t: h.t })).reverse();
        },

        get deviceLabel() {
            const d = this.devices.find(x => x.id === this.device);
            return d ? d.label : 'Desktop';
        },

        get deviceWidthLabel() {
            return { desktop: this.t('Full width'), tablet: '820 px', mobile: '420 px' }[this.device];
        },

        get canvasStyle() {
            const z = this.prefs.zoom;
            const zoom = (!this.preview && z !== 100) ? ` zoom:${z / 100};` : '';
            if (this.preview) {
                if (this.device === 'desktop') return 'max-width:100%;' + 'background:#fff; min-height:100%;';
                const w = this.device === 'tablet' ? '820px' : '420px';
                return `max-width:${w}; background:#fff; box-shadow:0 0 0 1px #cfd3db, 0 20px 50px rgba(17,24,39,.18); margin-top:24px; margin-bottom:24px; border-radius:18px; overflow:hidden;`;
            }
            if (this.device === 'desktop') return 'max-width:1152px;' + zoom;
            const w = this.device === 'tablet' ? '820px' : '420px';
            return `max-width:${w}; padding:24px 12px;${zoom}`;
        },

        /* ───────── lifecycle ───────── */
        init() {
            if (this._inited) return;
            this._inited = true;

            this.savedSnap = JSON.stringify(this.blocks);
            this.pushHistory(this.t('Initial state'));

            this.$watch('activeLocale', () => { if (this.preview) this.runPreviewAnimations(); });
            this.$watch('selectedBlock', (v) => { if (v) this.editTab = this.editTab || 'content'; });

            setTimeout(() => { window.__builder = this; }, 50);
            window.__builder = this;

            setInterval(() => {
                if (this.prefs.autosave && !this.saved && !this.saving && !this.preview) this.save(true);
            }, 30000);
        },

        t(s) { return (L.i18n && L.i18n[s]) || s; },

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
            if (typing) return;
            if (!mod && !e.altKey && key === 'p') { e.preventDefault(); this.togglePreview(); return; }
            if (this.preview) return;

            if (mod && key === 'z') { e.preventDefault(); e.shiftKey ? this.redo() : this.undo(); return; }
            if (mod && key === 'y') { e.preventDefault(); this.redo(); return; }
            if (mod && key === 'd' && this.selectedBlock) { e.preventDefault(); this.duplicateSelected(); return; }
            if (mod && key === 'c' && this.selectedBlock && !String(window.getSelection()).length) { e.preventDefault(); this.copySelected(); return; }
            if (mod && key === 'v') { e.preventDefault(); this.pasteBlock(); return; }
            if (e.altKey && key === 'arrowup' && this.selectedBlock) { e.preventDefault(); this.moveUp(this.selectedBlock); return; }
            if (e.altKey && key === 'arrowdown' && this.selectedBlock) { e.preventDefault(); this.moveDown(this.selectedBlock); return; }
            if ((key === 'delete' || key === 'backspace') && this.selectedBlock) { e.preventDefault(); this.removeSelected(); }
        },

        showToast(message, type) {
            this.toast = { show: true, message, type: type || 'success' };
            clearTimeout(this._toastTimer);
            this._toastTimer = setTimeout(() => { this.toast.show = false; }, 2200);
        },

        /* ───────── panels / favourites / prefs ───────── */
        openPanel(name) {
            if (this.shownPanel === name && this.panelOpen) { this.panelOpen = false; return; }
            this.panel = name;
            this.panelOpen = true;
        },

        isFav(id) { return this.favs.includes(id); },
        toggleFav(id) {
            this.favs = this.isFav(id) ? this.favs.filter(x => x !== id) : [...this.favs, id];
            store.set('rj.favs', this.favs);
        },
        touchRecent(id) {
            if (id === 'container') return;
            this.recents = [id, ...this.recents.filter(x => x !== id)].slice(0, 6);
            store.set('rj.recent', this.recents);
        },
        isCollapsed(key) { return !this.widgetSearch && !!this.collapsed[key]; },
        toggleSec(key) { this.collapsed = Object.assign({}, this.collapsed, { [key]: !this.collapsed[key] }); },
        setPref(key, value) {
            this.prefs = Object.assign({}, this.prefs, { [key]: value });
            store.set('rj.prefs', this.prefs);
        },

        ago(ts) {
            const s = Math.round((Date.now() - ts) / 1000);
            if (s < 10) return this.t('now');
            if (s < 60) return s + 's';
            if (s < 3600) return Math.floor(s / 60) + 'm';
            return Math.floor(s / 3600) + 'h';
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
            return this.translatableFields.includes(name);
        },

        getBlockField(block, name) {
            const s = block.settings || {};
            if (!this.isTranslatableField(name) || this.activeLocale === this.baseLocale) return s[name];
            const tr = (s.translations || {})[this.activeLocale] || {};
            return tr[name] !== undefined ? tr[name] : s[name]; // fall back to base language
        },

        getBlockText(block) {
            return this.getBlockField(block, 'text') || '';
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
            if (b) {
                const v = this.getBlockField(b, field.name);
                if (v !== undefined && v !== null) return v;
            }
            return field.default !== undefined ? field.default : '';
        },

        isDefault(field) {
            const b = this.currentBlock;
            if (!b) return true;
            const v = this.getBlockField(b, field.name);
            if (v === undefined || v === null) return true;
            const d = field.default !== undefined ? field.default : '';
            return String(v) === String(d) || (field.type === 'toggle' && !v && !d);
        },

        resetField(field) {
            const b = this.currentBlock;
            if (!b) return;
            if (this.isTranslatableField(field.name) && this.activeLocale !== this.baseLocale) {
                const tr = (b.settings.translations || {})[this.activeLocale];
                if (tr) delete tr[field.name];
            } else {
                this.setBlockField(b, field.name, field.default !== undefined ? field.default : '');
            }
            this.markDirty();
            this.commitSoon(this.t('Edited') + ' ' + this.blockLabel(b.type));
        },

        colorHex(v) {
            return /^#[0-9a-f]{6}$/i.test(v || '') ? v : '#000000';
        },

        hasWidget(id) { return this.widgets.some(w => w.id === id); },

        blockLabel(type) {
            const w = this.widgets.find(x => x.id === type);
            return w ? w.name : type;
        },

        currentBlockTypeLabel() {
            return this.currentBlock ? this.blockLabel(this.currentBlock.type) : '';
        },

        generateId() {
            return 'blk_' + Math.random().toString(36).slice(2, 11);
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

        findPath(id, list, trail) {
            list = list || this.blocks;
            trail = trail || [];
            for (const b of list) {
                const next = trail.concat([b]);
                if (b.id === id) return next;
                if (b.children && b.children.length) {
                    const found = this.findPath(id, b.children, next);
                    if (found) return found;
                }
            }
            return null;
        },

        emptySlot() {
            return this.createBlock('container', { columns: '1' });
        },

        /* ───────── layouts & ready-made sections ───────── */
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
            this.pushHistory(this.t('Added') + ' ' + this.t('Add New Section'));
            this.scrollToBlock(container.id);
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
            this.pushHistory(this.t('Added') + ' ' + this.t('Add New Section'));
        },

        addSection(key) {
            const mk = (type, s, kids) => { const b = this.createBlock(type, s); b.children = kids || []; return b; };
            const slot = (...kids) => mk('container', { columns: '1' }, kids);
            const wrap = (cols, extra, slots) => mk('container', Object.assign({ columns: String(cols), gap: 'lg', padding: 'xl', align: 'center', width: 'boxed' }, extra), slots);
            let section;

            if (key === 'hero') {
                section = wrap(1, { background: '#f5f3ff' }, [slot(
                    mk('heading', { level: 'h1', align: 'center', text: 'Your headline goes here' }),
                    mk('text', { align: 'center', size: 'lg', text: 'Explain what you offer in one or two clear sentences.' })
                )]);
            } else if (key === 'features') {
                const col = (title) => slot(
                    mk('heading', { level: 'h3', align: 'center', text: title }),
                    mk('text', { align: 'center', text: 'A short description of this point.' })
                );
                section = wrap(3, { align: 'start' }, [col('First feature'), col('Second feature'), col('Third feature')]);
            } else if (key === 'split') {
                section = wrap(2, {}, [
                    slot(mk('image', { align: 'center', width: 'full', rounded: 'lg' })),
                    slot(
                        mk('heading', { level: 'h2', align: 'left', text: 'A heading for this section' }),
                        mk('text', { align: 'left', text: 'Tell the story behind the picture in a few sentences.' })
                    ),
                ]);
            } else if (key === 'cta') {
                section = wrap(1, { background: '#1a1030' }, [slot(
                    mk('heading', { level: 'h2', align: 'center', color: '#ffffff', text: 'Ready to get started?' }),
                    mk('text', { align: 'center', color: '#cbd5e1', text: 'Add one sentence that tells people what to do next.' })
                )]);
            }
            if (!section) return;

            this.blocks.push(section);
            this.selectedBlock = section.id;
            this.markDirty();
            this.pushHistory(this.t('Added') + ' ' + (this.sectionTemplates.find(s => s.key === key) || {}).title);
            this.scrollToBlock(section.id);
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
            this.touchRecent(widgetId);
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
            this.pushHistory(this.t('Added') + ' ' + this.blockLabel(block.type));
            this.playAnimation(block.id);
            this.scrollToBlock(block.id);
        },

        insertAt(widgetId, list, index) {
            this.touchRecent(widgetId);
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
            this.pushHistory(this.t('Moved') + ' ' + this.blockLabel(block.type));
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
                this.touchRecent(d.widgetId);
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
            this.pushHistory((d.type === 'new' ? this.t('Added') : this.t('Moved')) + ' ' + this.blockLabel(block.type));
            if (d.type === 'new') this.playAnimation(block.id);
        },

        endDrag() {
            this.dragging = null;
            this.dragTarget = null;
            document.querySelectorAll('.rj-drop').forEach(el => el.classList.remove('rj-drop'));
        },

        /* ───────── block actions ───────── */
        /** Select from the canvas → opens the edit panel. */
        selectBlock(id) {
            if (this.preview) return;
            const m = document.getElementById('rj-main');
            const top = m ? m.scrollTop : 0;
            this.selectedBlock = id;
            this.panel = 'edit';
            this.panelOpen = true;
            // re-render must not move the page
            this.$nextTick(() => { if (m) m.scrollTop = top; });
        },

        /** Select from the navigator → keeps the navigator open and scrolls the canvas to the block. */
        selectFromNav(id) {
            this.selectedBlock = id;
            this.scrollToBlock(id);
        },

        openEdit(id) {
            this.selectedBlock = id;
            this.panel = 'edit';
            this.panelOpen = true;
            this.scrollToBlock(id);
        },

        selectParent(id) {
            const found = this.findBlockAndParent(id);
            if (found && found.parent) this.selectBlock(found.parent.id);
        },

        scrollToBlock(id) {
            this.$nextTick(() => {
                const el = document.querySelector('#rj-canvas [data-block-id="' + id + '"]');
                if (el && el.scrollIntoView) el.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            });
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
            const label = this.blockLabel(found.block.type);
            found.list.splice(found.index, 1);
            if (this.selectedBlock === id) this.selectedBlock = null;
            this.markDirty();
            this.pushHistory(this.t('Deleted') + ' ' + label);
        },

        duplicateBlock(id) {
            const found = this.findBlockAndParent(id);
            if (!found) return;
            const copy = JSON.parse(JSON.stringify(found.block));
            this.reassignIds(copy);
            found.list.splice(found.index + 1, 0, copy);
            this.selectedBlock = copy.id;
            this.markDirty();
            this.pushHistory(this.t('Duplicated') + ' ' + this.blockLabel(copy.type));
        },

        reassignIds(block) {
            block.id = this.generateId();
            if (block.children && block.children.length) {
                for (const child of block.children) this.reassignIds(child);
            }
        },

        copySelected() {
            if (!this.currentBlock) return;
            this.clipboard = JSON.stringify(this.currentBlock);
            this.showToast(this.t('Copied'));
        },

        pasteBlock() {
            if (!this.clipboard) { this.showToast(this.t('Nothing to paste'), 'error'); return; }
            const copy = JSON.parse(this.clipboard);
            this.reassignIds(copy);
            const found = this.selectedBlock ? this.findBlockAndParent(this.selectedBlock) : null;
            if (found) found.list.splice(found.index + 1, 0, copy);
            else this.blocks.push(copy);
            this.selectedBlock = copy.id;
            this.markDirty();
            this.pushHistory(this.t('Pasted') + ' ' + this.blockLabel(copy.type));
            this.scrollToBlock(copy.id);
        },

        moveUp(id) {
            const found = this.findBlockAndParent(id);
            if (!found || found.index === 0) return;
            [found.list[found.index - 1], found.list[found.index]] = [found.list[found.index], found.list[found.index - 1]];
            this.markDirty();
            this.pushHistory(this.t('Moved') + ' ' + this.blockLabel(found.block.type));
        },

        moveDown(id) {
            const found = this.findBlockAndParent(id);
            if (!found || found.index === found.list.length - 1) return;
            [found.list[found.index + 1], found.list[found.index]] = [found.list[found.index], found.list[found.index + 1]];
            this.markDirty();
            this.pushHistory(this.t('Moved') + ' ' + this.blockLabel(found.block.type));
        },

        updateSetting(name, value) {
            if (!this.currentBlock) return;
            this.setBlockField(this.currentBlock, name, value);
            this.markDirty();
            this.commitSoon(this.t('Edited') + ' ' + this.blockLabel(this.currentBlock.type));

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
                if (this.getBlockText(b) !== txt) {
                    this.setBlockField(b, 'text', txt);
                    this.markDirty();
                    this.pushHistory(this.t('Edited') + ' ' + this.blockLabel(b.type));
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
            if (this.preview) {
                this.previewRun++;
                this.runPreviewAnimations();
            }
        },

        togglePreview() {
            this.preview = !this.preview;
            this.selectedBlock = null;
            this.previewRun++;
            this.$nextTick(() => {
                const m = document.getElementById('rj-main');
                if (m) m.scrollTop = 0;
            });
            this.runPreviewAnimations();
        },

        replayPreview() {
            this.previewRun++;
            const m = document.getElementById('rj-main');
            if (m) m.scrollTop = 0;
            this.runPreviewAnimations();
        },

        /** Reveals .rj-wait elements as they scroll into view. Runs after Alpine has re-rendered the canvas. */
        runPreviewAnimations() {
            if (rjObserver) { rjObserver.disconnect(); rjObserver = null; }
            if (!this.preview) return;

            this.$nextTick(() => requestAnimationFrame(() => {
                const els = document.querySelectorAll('#rj-canvas .rj-wait');
                const reveal = (el) => { el.classList.remove('rj-wait'); el.classList.add('rj-play'); };

                if (!('IntersectionObserver' in window)) { els.forEach(reveal); return; }

                rjObserver = new IntersectionObserver((entries) => {
                    entries.forEach(en => {
                        if (!en.isIntersecting) return;
                        reveal(en.target);
                        rjObserver.unobserve(en.target);
                    });
                }, { root: document.getElementById('rj-main'), threshold: 0, rootMargin: '0px 0px -8% 0px' });

                els.forEach(el => rjObserver.observe(el));
            }));
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
            if (!w) return `<div style="padding:12px; font-size:12px; color:#dc2626;">${this.esc(this.t('Unknown widget'))}</div>`;

            const s = block.settings || {};
            const hiddenHere = !!s['_hide_' + this.device];
            const hasAnim = s._animation && s._animation !== 'none';

            if (this.preview && hiddenHere) return '';

            let inner = block.type === 'container' ? this.renderContainer(block) : this.renderSimpleWidget(block);

            if (this.preview && inner === '') return '';

            // entrance animation
            if (hasAnim) {
                if (this.preview) inner = this.animationWrapper(inner, s, 'rj-wait');
                else if (this.animPlayId === block.id) inner = this.animationWrapper(inner, s, 'rj-play');
            }

            // hover effect (preview only)
            if (this.preview && s._hover && s._hover !== 'none') {
                inner = `<div class="rj-h rj-h-${this.esc(s._hover)}">${inner}</div>`;
            }

            const customClass = s._css_class ? ' ' + this.esc(s._css_class) : '';

            if (this.preview) return `<div class="${customClass.trim()}" data-run="${this.previewRun}">${inner}</div>`;

            const isSelected = this.selectedBlock === block.id;
            const bid = block.id;
            const hiddenNote = hiddenHere
                ? `<div style="position:absolute;top:6px;right:8px;z-index:20;font-size:9px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;background:#1a1030;color:#fbbf24;border-radius:9999px;padding:2px 8px;">${this.esc(this.t('Hidden on'))} ${this.esc(this.device)}</div>`
                : '';

            return `
                <div class="rj-block${customClass}" data-block-id="${bid}" style="position:relative; ${hiddenHere ? 'opacity:.35;' : ''}" onclick="event.stopPropagation(); window.__builder.selectBlock('${bid}')">
                    ${hiddenNote}
                    <div style="position:absolute;top:-14px;left:50%;transform:translateX(-50%);z-index:30;display:flex;align-items:center;gap:2px;background:#1a1030;color:#fff;border-radius:9999px;padding:2px 6px;box-shadow:0 4px 12px rgba(0,0,0,0.2);opacity:${isSelected ? 1 : 0};pointer-events:${isSelected ? 'auto' : 'none'};transition:opacity 0.15s;white-space:nowrap;">
                        <span style="font-size:9px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;padding:0 4px;color:#a855f7;">${this.esc(this.blockLabel(block.type))}${hasAnim ? ' ✦' : ''}</span>
                        <span draggable="true" ondragstart="event.stopPropagation(); window.__builder.startDragMove('${bid}', event)" title="Drag to move" style="width:22px;height:22px;display:flex;align-items:center;justify-content:center;cursor:grab;font-size:13px;">⠿</span>
                        ${this.toolbarBtn('⇱', this.esc(this.t('Select parent')), `window.__builder.selectParent('${bid}')`)}
                        ${this.toolbarBtn('↑', this.esc(this.t('Move up')), `window.__builder.moveUp('${bid}')`)}
                        ${this.toolbarBtn('↓', this.esc(this.t('Move down')), `window.__builder.moveDown('${bid}')`)}
                        ${this.toolbarBtn('⧉', this.esc(this.t('Duplicate')), `window.__builder.duplicateBlock('${bid}')`)}
                        ${this.toolbarBtn('✕', this.esc(this.t('Delete')), `window.__builder.removeBlock('${bid}')`, true)}
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

            // Preview: an empty, unstyled container is just dead space
            if (this.preview && children.every(c => !c || (c.type === 'container' && (!c.children || !c.children.length) && (!c.settings || !c.settings.background || c.settings.background === 'transparent'))) && bg === 'transparent' && minHeight === 'auto' && border === 'none') {
                return '';
            }

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
                    : `<div style="min-height:80px; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#c3c7d0; pointer-events:none;">
                           <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                           <span style="font-size:9px; text-transform:uppercase; letter-spacing:.08em; margin-top:2px;">${this.esc(this.t('Drop here'))} · ${i + 1}</span>
                       </div>`;

                slotsHtml += `
                    <div style="position:relative; min-height:80px; min-width:0; border-radius:8px; transition:all .15s; border:2px ${child ? 'solid transparent' : 'dashed #e5e7eb'};"
                         ondragover="event.preventDefault(); event.stopPropagation(); this.classList.add('rj-drop');"
                         ondragleave="this.classList.remove('rj-drop');"
                         ondrop="event.preventDefault(); event.stopPropagation(); window.__builder.dropInto('${bid}', ${i});">
                        ${inner}
                    </div>
                `;
            }

            const chrome = `background:${esc2(bg)}; padding:${padding}; border:${border}; min-height:${minHeight};`;
            const outerStyle = isFullWidth
                ? `${chrome} width:100%; margin:0;`
                : `${chrome} max-width:1100px; margin:0 auto; width:100%;`;
            const radius = (this.preview && isFullWidth) ? '0' : '12px';

            return `
                <div style="border-radius:${radius}; ${outerStyle}">
                    <div style="display:grid; grid-template-columns: repeat(${effCols}, minmax(0, 1fr)); gap:${gap}; align-items:${align};">
                        ${slotsHtml}
                    </div>
                </div>
            `;

            function esc2(v) { return String(v).replace(/[<>"']/g, ''); }
        },

        renderSimpleWidget(block) {
            const settings = block.settings || {};
            const esc = (s) => this.esc(s);
            const bid = block.id;
            const inline = this.preview ? '' : `class="rj-inline-edit" ondblclick="event.stopPropagation(); window.__builder.startInline(this, '${bid}')"`;
            const mobile = this.device === 'mobile';

            if (block.type === 'heading') {
                const level = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'].includes(settings.level) ? settings.level : 'h2';
                const sizes = { h1: '2rem', h2: '1.5rem', h3: '1.25rem', h4: '1.125rem', h5: '1rem', h6: '0.875rem' };
                const mobileSizes = { h1: '1.6rem', h2: '1.3rem', h3: '1.15rem', h4: '1.05rem', h5: '1rem', h6: '0.875rem' };
                const aligns = { left: 'left', center: 'center', right: 'right' };
                const size = (mobile ? mobileSizes : sizes)[level];
                return `<${level} ${inline} style="font-size:${size}; font-weight:700; text-align:${aligns[settings.align] || 'left'}; color:${esc(settings.color || '#111827')}; padding:0.75rem 1rem; margin:0;">${esc(this.getBlockField(block, 'text') || 'Heading')}</${level}>`;
            }

            if (block.type === 'text') {
                const aligns = { left: 'left', center: 'center', right: 'right', justify: 'justify' };
                const sizes = { sm: '0.875rem', base: '1rem', lg: '1.125rem', xl: '1.25rem' };
                return `<p ${inline} style="font-size:${sizes[settings.size] || '1rem'}; text-align:${aligns[settings.align] || 'left'}; color:${esc(settings.color || '#374151')}; padding:0.75rem 1rem; line-height:1.6; white-space:pre-wrap; margin:0;">${esc(this.getBlockField(block, 'text') || 'Text')}</p>`;
            }

            if (block.type === 'image') {
                if (!settings.url) {
                    if (this.preview) return '';
                    return `<div style="padding:2rem 1rem; text-align:center; color:#9ca3af; font-size:0.75rem; background:#f3f4f6; border-radius:8px; margin:0.75rem 1rem;">
                                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="display:block; margin:0 auto 4px;"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                                ${esc(this.t('Image'))}
                            </div>`;
                }
                const aligns = { left: '0 auto 0 0', center: '0 auto', right: '0 0 0 auto' };
                const widths = { auto: 'auto', full: '100%', half: '50%', third: '33.333%' };
                const rounded = { none: '0', sm: '4px', md: '8px', lg: '16px', full: '9999px' };
                const width = mobile ? '100%' : (widths[settings.width] || '100%');
                return `<figure style="padding:0.75rem 1rem; margin:0;"><img src="${esc(settings.url)}" alt="${esc(this.getBlockField(block, 'alt') || '')}" style="width:${width}; max-width:100%; border-radius:${rounded[settings.rounded] || '8px'}; margin:${aligns[settings.align] || '0 auto'}; display:block;"></figure>`;
            }

            // Widgets without a dedicated canvas renderer: show a labelled placeholder
            const w = this.widgets.find(x => x.id === block.type);
            if (this.preview) return '';
            return `<div style="padding:14px 16px; margin:0.5rem 1rem; display:flex; align-items:center; gap:12px; border:1px dashed #d1d5db; border-radius:10px; background:#fafafa; color:#6b7280; font-size:12px;">
                        <span style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; color:#8b5cf6; flex-shrink:0;" class="rj-ico">${w && w.icon ? w.icon : ''}</span>
                        <span style="font-weight:600; color:#374151;">${esc(w ? w.name : block.type)}</span>
                    </div>`;
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
        toggleNav(id) {
            this.navCollapsed = Object.assign({}, this.navCollapsed, { [id]: !this.navCollapsed[id] });
        },

        navigatorHtml() {
            const nc = this.navCollapsed;
            const walk = (list, depth) => list.map(b => {
                const sel = this.selectedBlock === b.id;
                const isContainer = b.type === 'container';
                const s = b.settings || {};
                const hasKids = b.children && b.children.length;
                const open = !nc[b.id];
                const hint = isContainer
                    ? `${parseInt(s.columns) || 1} col`
                    : this.esc(String(this.getBlockField(b, 'text') || this.getBlockField(b, 'alt') || '').slice(0, 22));
                const anim = s._animation && s._animation !== 'none' ? ' <span style="color:#a78bfa;">✦</span>' : '';
                const hidden = (s._hide_desktop || s._hide_tablet || s._hide_mobile) ? ' <span style="color:#fbbf24;" title="Hidden on some devices">◌</span>' : '';
                const caret = hasKids
                    ? `<span class="rj-caret" data-open="${open}" onclick="event.stopPropagation(); window.__builder.toggleNav('${b.id}')">▶</span>`
                    : '<span class="rj-caret"></span>';

                const row = `
                    <div class="rj-nav" data-sel="${sel}" style="margin-left:${depth * 14}px;"
                         onclick="event.stopPropagation(); window.__builder.selectFromNav('${b.id}')"
                         ondblclick="event.stopPropagation(); window.__builder.openEdit('${b.id}')">
                        ${caret}
                        <span style="width:14px; text-align:center; color:#6d7080;">${isContainer ? '▣' : '◦'}</span>
                        <span class="rj-nlabel">${this.esc(this.blockLabel(b.type))}${anim}${hidden}</span>
                        <span class="rj-nhint">${hint}</span>
                        <span class="rj-nact">
                            <span title="${this.esc(this.t('Edit'))}" onclick="event.stopPropagation(); window.__builder.openEdit('${b.id}')">✎</span>
                            <span title="${this.esc(this.t('Duplicate'))}" onclick="event.stopPropagation(); window.__builder.duplicateBlock('${b.id}')">⧉</span>
                            <span title="${this.esc(this.t('Delete'))}" onclick="event.stopPropagation(); window.__builder.removeBlock('${b.id}')">✕</span>
                        </span>
                    </div>`;
                const kids = hasKids && open ? walk(b.children, depth + 1) : '';
                return row + kids;
            }).join('');
            return walk(this.blocks, 0);
        },

        /* ───────── history & saving ───────── */
        markDirty() { this.saved = false; },

        commitSoon(label) {
            this._pendingLabel = label || this.t('Edited');
            clearTimeout(this._historyTimer);
            this._historyTimer = setTimeout(() => this.pushHistory(this._pendingLabel), 500);
        },

        pushHistory(label) {
            clearTimeout(this._historyTimer);
            const snap = JSON.stringify(this.blocks);
            const cur = this.history[this.historyIndex];
            if (cur && cur.snap === snap) return;
            this.history = this.history.slice(0, this.historyIndex + 1);
            this.history.push({ snap, label: label || this.t('Edited'), t: Date.now() });
            this.historyIndex = this.history.length - 1;
            if (this.history.length > 80) { this.history.shift(); this.historyIndex--; }
        },

        restoreHistory(i) {
            this.historyIndex = i;
            this.blocks = JSON.parse(this.history[i].snap);
            this.selectedBlock = null;
            this.saved = this.history[i].snap === this.savedSnap;
        },

        undo() {
            this.pushHistory(this._pendingLabel);
            if (!this.canUndo) return;
            this.restoreHistory(this.historyIndex - 1);
        },

        redo() {
            if (!this.canRedo) return;
            this.restoreHistory(this.historyIndex + 1);
        },

        goToHistory(i) {
            this.pushHistory(this._pendingLabel);
            if (i === this.historyIndex || !this.history[i]) return;
            this.restoreHistory(i);
        },

        async save(silent) {
            if (this.saving) return;
            clearTimeout(this._historyTimer);
            this.pushHistory(this._pendingLabel);
            this.saving = true;
            const snap = JSON.stringify(this.blocks);
            try {
                const res = await fetch(L.updateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': L.csrf,
                        'X-HTTP-Method-Override': 'PUT',
                    },
                    body: JSON.stringify({
                        name: L.name,
                        content: { blocks: this.blocks },
                        status: L.status,
                    }),
                });
                if (res.ok || res.redirected) {
                    this.savedSnap = snap;
                    this.saved = JSON.stringify(this.blocks) === snap;
                    this.showToast(silent === true ? this.t('Auto-saved') : this.t('Saved'));
                } else if (res.status === 419) {
                    this.showToast(this.t('Session expired — reload the page and try again'), 'error');
                } else {
                    this.showToast(this.t('Save failed') + ' (' + res.status + ')', 'error');
                }
            } catch (e) {
                console.error(e);
                this.showToast(this.t('Network error — not saved'), 'error');
            } finally {
                this.saving = false;
            }
        },
    };
}
</script>
@endverbatim
@endsection