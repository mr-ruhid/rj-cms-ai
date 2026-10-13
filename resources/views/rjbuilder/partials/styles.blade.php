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

    .rj-app {
        --bg0: #16171b; --bg1: #1f2025; --bg2: #2a2b32; --bg3: #373843; --line: #383a43;
        --tx: #e8e9ed; --mut: #8e919d; --acc: #8b5cf6; --acc2: #ec4899; --ok: #2f9e5f;
        height: 100vh; height: 100dvh; display: flex; flex-direction: column; overflow: hidden;
        color: var(--tx);
    }
    .rj-app * { box-sizing: border-box; }
    .rj-app button { font-family: inherit; }

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

    .rj-hist { display: flex; align-items: center; gap: 10px; width: 100%; text-align: left; padding: 9px 10px; border-radius: 6px; border: 0; background: transparent; color: #c4c7d3; font-size: 12.5px; cursor: pointer; }
    .rj-hist:hover { background: var(--bg2); }
    .rj-hist[data-cur="true"] { background: rgba(139,92,246,.22); color: #fff; }
    .rj-hist[data-future="true"] { opacity: .45; }
    .rj-hist i { width: 8px; height: 8px; border-radius: 50%; background: #5d6070; flex-shrink: 0; }
    .rj-hist[data-cur="true"] i { background: var(--acc); }
    .rj-hist small { margin-left: auto; color: var(--mut); font-size: 10.5px; }

    .rj-handle { position: absolute; top: 50%; transform: translateY(-50%); z-index: 40; width: 18px; height: 64px; background: #16171b; border: 0; border-radius: 0 8px 8px 0; color: #c4c7d3; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px; }
    .rj-handle:hover { color: #fff; background: #000; }

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