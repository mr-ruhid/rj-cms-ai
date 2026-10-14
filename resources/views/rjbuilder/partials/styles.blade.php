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

    .rj-h { transition: transform .35s cubic-bezier(.22,1,.36,1), box-shadow .35s, filter .35s, color .3s, background-color .3s, border-color .3s; }
    .rj-h-grow:hover  { transform: scale(1.04); }
    .rj-h-lift:hover  { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(17,24,39,.16); }
    .rj-h-glow:hover  { box-shadow: 0 0 34px rgba(124,58,237,.45); }
    .rj-h-tilt:hover  { transform: perspective(800px) rotateX(4deg) rotateY(-4deg) scale(1.02); }
    .rj-h-float:hover { animation: rjFloat 1.4s ease-in-out infinite; }

    .rj-h[style*="--h-bg"]:hover { background-color: var(--h-bg); }
    .rj-h[style*="--h-bc"]:hover { border-color: var(--h-bc); }
    .rj-h[style*="--h-tc"]:hover,
    .rj-h[style*="--h-tc"]:hover .rj-hd,
    .rj-h[style*="--h-tc"]:hover .rj-ml,
    .rj-h[style*="--h-tc"]:hover p { color: var(--h-tc); }

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
    .rj-canvas { margin: 0 auto; transition: max-width .3s; container-type: inline-size; container-name: rj; }
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

    /* Visibility segmented control */
    .rj-vis-btn {
        flex: 1;
        padding: 7px 0;
        border: 0;
        border-radius: 6px;
        background: transparent;
        color: #8e919d;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: background .15s, color .15s;
        font-family: inherit;
    }
    .rj-vis-btn:hover { color: #d4d6df; }
    .rj-vis-btn[data-on="true"] { background: #8b5cf6; color: #fff; box-shadow: 0 1px 3px rgba(139,92,246,.4); }
    .rj-vis-btn[data-on="false"] { text-decoration: line-through; text-decoration-color: #dc2626; text-decoration-thickness: 2px; }

    /* ───────── Responsive (canvas = container) ───────── */

/* Breakpointlər: tablet ≤ {{ $bpTablet ?? 860 }}px, mobil ≤ {{ $bpMobile ?? 560 }}px (canvas eninə görə) */
.rj-cg {
    display: flex;
    flex-direction: var(--dir, row);
    flex-wrap: var(--wrap, wrap);
    justify-content: var(--jc, flex-start);
    align-items: var(--al, stretch);
    gap: var(--g, 1rem);
}
.rj-cg > .rj-slot {
    flex: 1 1 0;
    min-width: 0;
}
.rj-cg > .rj-slot[style*="--sw"] {
    flex: 0 0 var(--sw);
    max-width: var(--sw);
}
.rj-cg.rj-fit {
    justify-content: space-between;
}
.rj-cg.rj-fit > .rj-slot {
    flex: 0 0 auto;
}
.rj-hd { font-size: var(--fs); font-weight: 700; margin: 0; }
.rj-img { width: var(--w); max-width: 100%; }

@container rj (max-width: {{ $bpTablet ?? 860 }}px) {
    .rj-cg:not(.rj-keep-tablet) {
        flex-wrap: wrap;
    }
    .rj-cg:not(.rj-keep-tablet) > .rj-slot,
    .rj-cg:not(.rj-keep-tablet) > .rj-slot[style*="--sw"] {
        flex: 1 1 calc(50% - var(--g, 1rem) / 2);
        max-width: calc(50% - var(--g, 1rem) / 2);
        width: auto;
    }
    .rj-menu[data-mobile="hamburger"] .rj-menu-d { display: none; }
    .rj-menu[data-mobile="hamburger"] .rj-menu-m { display: flex; justify-content: var(--mjc); }
}
@container rj (max-width: {{ $bpMobile ?? 560 }}px) {
    .rj-cg:not(.rj-keep-mobile) {
        flex-direction: column;
        flex-wrap: nowrap;
    }
    .rj-cg:not(.rj-keep-mobile) > .rj-slot,
    .rj-cg:not(.rj-keep-mobile) > .rj-slot[style*="--sw"] {
        flex: 1 1 auto;
        max-width: 100%;
        width: 100%;
    }
    .rj-hd { font-size: var(--fsm); }
    .rj-img { width: 100% !important; }
}

/* Cihaza görə gizlətmə (preview + public) */
@container rj (min-width: {{ ($bpTablet ?? 860) + 1 }}px) { .rj-hide-d { display: none !important; } }
@container rj (min-width: {{ ($bpMobile ?? 560) + 1 }}px) and (max-width: {{ $bpTablet ?? 860 }}px) { .rj-hide-t { display: none !important; } }
@container rj (max-width: {{ $bpMobile ?? 560 }}px) { .rj-hide-m { display: none !important; } }

/* ───────── Menu widget ───────── */
.rj-menu { width: 100%; }
.rj-menu ul { list-style: none; margin: 0; padding: 0; }
.rj-menu-m { display: none; }
.rj-menu-d > ul { display: flex; flex-direction: var(--dir); gap: var(--gap); align-items: var(--ai); justify-content: var(--jc); flex-wrap: wrap; }
.rj-mi { position: relative; }
.rj-ml { display: inline-block; color: var(--mt); font-size: var(--fs); font-weight: var(--fw); text-decoration: none; padding: 8px 12px; border-radius: 6px; border-bottom: 2px solid transparent; transition: all .15s; white-space: nowrap; }
.rj-ml:hover { color: var(--mh); }
.rj-menu[data-hover="underline"] .rj-ml:hover, .rj-menu[data-hover="both"] .rj-ml:hover { border-bottom-color: var(--mh); }
.rj-menu[data-hover="background"] .rj-ml:hover, .rj-menu[data-hover="both"] .rj-ml:hover { background: color-mix(in srgb, var(--mh) 10%, transparent); }
.rj-car { font-size: 9px; opacity: .6; }

.rj-dd { position: absolute; top: 100%; left: 0; min-width: 180px; background: var(--mbg); border: var(--mbd); border-radius: 8px; box-shadow: 0 12px 30px rgba(0,0,0,.12); padding: 6px; margin-top: 6px; opacity: 0; visibility: hidden; transform: translateY(-4px); transition: all .15s; z-index: 50; }
.rj-dd .rj-dd { top: 0; left: 100%; margin: 0 0 0 6px; }
.rj-mi:hover > .rj-dd, .rj-mi:focus-within > .rj-dd { opacity: 1; visibility: visible; transform: none; }
.rj-dd .rj-ml { display: block; }

/* Hamburger */
.rj-bm { position: relative; display: inline-block; }
.rj-bb { width: 42px; height: 42px; border: 0; border-radius: 10px; background: #f3f4f6; color: var(--mt); cursor: pointer; display: inline-flex; align-items: center; justify-content: center; }
.rj-bb:hover { background: color-mix(in srgb, var(--mh) 12%, transparent); color: var(--mh); }
.rj-bl { position: absolute; top: calc(100% + 8px); left: 0; min-width: 200px; max-width: 280px; background: var(--mbg); border: var(--mbd); border-radius: 10px; box-shadow: 0 12px 30px rgba(0,0,0,.15); padding: 8px; opacity: 0; visibility: hidden; transform: translateY(-4px); transition: all .15s; z-index: 50; }
.rj-menu[data-align="right"] .rj-bl { left: auto; right: 0; }
.rj-menu[data-align="center"] .rj-bl { left: 50%; transform: translate(-50%, -4px); }
.rj-menu.rj-open .rj-bl { opacity: 1; visibility: visible; transform: none; }
.rj-menu[data-align="center"].rj-open .rj-bl { transform: translate(-50%, 0); }
.rj-bl .rj-mi { display: block; }
.rj-bl .rj-ml { display: block; width: 100%; padding: 10px 12px; }
.rj-bl .rj-dd { position: static; opacity: 1; visibility: visible; transform: none; box-shadow: none; background: transparent; margin: 2px 0 4px 16px; padding: 0 0 0 8px; border: 0; border-left: 2px solid color-mix(in srgb, var(--mh) 25%, transparent); }

.rj-dim {
    border: 1px solid var(--line);
    border-radius: 8px;
    overflow: hidden;
    background: #15161a;
}

.rj-dim-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 1px;
    background: var(--line);
}

.rj-dim-cell {
    display: flex;
    flex-direction: column;
    background: #15161a;
    padding: 6px 6px 4px;
}

.rj-dim-cell > span {
    font-size: 9px;
    font-weight: 600;
    color: #777a88;
    text-transform: uppercase;
    letter-spacing: .05em;
    text-align: center;
    margin-bottom: 2px;
}

.rj-dim-cell > input {
    width: 100%;
    background: transparent;
    border: 0;
    outline: none;
    color: var(--tx);
    font-size: 13px;
    font-family: inherit;
    text-align: center;
    padding: 2px 0;
}

.rj-dim-cell > input::-webkit-outer-spin-button,
.rj-dim-cell > input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.rj-dim-cell > input[type=number] {
    -moz-appearance: textfield;
}

.rj-dim-cell:focus-within {
    background: #1f2025;
    box-shadow: inset 0 0 0 1px var(--acc);
}

.rj-dim-foot {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 4px 6px;
    border-top: 1px solid var(--line);
    background: #1a1b20;
}

.rj-dim-link {
    width: 26px;
    height: 26px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 0;
    background: transparent;
    color: #777a88;
    border-radius: 6px;
    cursor: pointer;
    transition: background .12s, color .12s;
}

.rj-dim-link:hover {
    background: var(--bg2);
    color: #fff;
}

.rj-dim-link[data-on="true"] {
    background: var(--acc);
    color: #fff;
}

.rj-dim-unit {
    background: transparent;
    border: 0;
    color: #b9bcc8;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    outline: none;
    padding: 2px 4px;
    border-radius: 4px;
    font-family: inherit;
}

.rj-dim-unit:hover {
    background: var(--bg2);
}

.rj-dim-unit option {
    background: #1a1b20;
    color: var(--tx);
}

.rj-gcp {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    padding: 6px;
    background: #15161a;
    border: 1px solid var(--line);
    border-radius: 6px;
}

.rj-gcp-sw {
    width: 24px;
    height: 24px;
    border-radius: 5px;
    border: 1px solid rgba(255,255,255,.12);
    cursor: pointer;
    padding: 0;
    transition: transform .12s, border-color .12s;
}

.rj-gcp-sw:hover {
    transform: scale(1.1);
    border-color: var(--acc);
}
.rj-gfp {
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding: 6px;
    background: #15161a;
    border: 1px solid var(--line);
    border-radius: 6px;
}

.rj-gfp-btn {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    padding: 6px 10px;
    background: transparent;
    border: 0;
    border-radius: 5px;
    color: #c4c7d3;
    font-size: 13px;
    cursor: pointer;
    text-align: left;
    transition: background .12s, color .12s;
    font-family: inherit;
}

.rj-gfp-btn:hover {
    background: var(--bg2);
    color: #fff;
}

.rj-gfp-btn[data-on="true"] {
    background: rgba(139,92,246,.22);
    color: #fff;
    font-weight: 600;
}

.rj-fdev {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 18px;
    padding: 0 5px;
    border: 1px solid var(--line);
    border-radius: 4px;
    background: #15161a;
    color: #777a88;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .03em;
    cursor: pointer;
    font-family: inherit;
    transition: background .12s, color .12s, border-color .12s;
}

.rj-fdev:hover {
    color: #fff;
    border-color: var(--acc);
}

.rj-fdev[data-on="desktop"] {
    color: #b9bcc8;
}

.rj-fdev[data-on="tablet"] {
    background: rgba(139,92,246,.18);
    color: #c4b5fd;
    border-color: rgba(139,92,246,.5);
}

.rj-fdev[data-on="mobile"] {
    background: rgba(236,72,153,.18);
    color: #f9a8d4;
    border-color: rgba(236,72,153,.5);
}
.rj-dyn-btn {
    flex-shrink: 0;
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--line);
    border-radius: 6px;
    background: #15161a;
    color: #8e919d;
    font-size: 14px;
    cursor: pointer;
    transition: background .12s, color .12s, border-color .12s;
    font-family: inherit;
    padding: 0;
}

.rj-dyn-btn:hover {
    background: rgba(139,92,246,.15);
    color: #c4b5fd;
    border-color: rgba(139,92,246,.5);
}

.rj-dyn-chip {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 7px 8px 7px 10px;
    background: rgba(139,92,246,.12);
    border: 1px solid rgba(139,92,246,.4);
    border-radius: 6px;
    color: #c4b5fd;
    font-size: 13px;
    font-weight: 500;
}

.rj-dyn-chip-icon {
    font-size: 13px;
    line-height: 1;
}

.rj-dyn-chip-label {
    flex: 1;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.rj-dyn-chip-btn {
    width: 22px;
    height: 22px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 0;
    border-radius: 4px;
    background: transparent;
    color: #c4b5fd;
    cursor: pointer;
    font-size: 11px;
    padding: 0;
    transition: background .12s, color .12s;
    font-family: inherit;
}

.rj-dyn-chip-btn:hover {
    background: rgba(255,255,255,.12);
    color: #fff;
}

.rj-dyn-chip-x:hover {
    background: rgba(239,68,68,.25);
    color: #fca5a5;
}
</style>