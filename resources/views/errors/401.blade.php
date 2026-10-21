<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>401 - {{ __('Unauthorized') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600,700,900&display=swap" rel="stylesheet" />
    <style>
        :root {
            --bg: #09060f;
            --violet: #6d28d9;
            --purple: #a855f7;
            --pink: #ec4899;
            --rose: #be185d;
            --cycle: 6s; /* one "login attempt" = scan -> PIN -> denied */
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { width: 100%; height: 100%; }
        body {
            font-family: 'Figtree', sans-serif;
            background-color: var(--bg);
            color: #fff;
            overflow: hidden;
        }

        /* ---------- Background ---------- */
        .aurora { position: fixed; inset: 0; z-index: 0; overflow: hidden; pointer-events: none; }
        .blob { position: absolute; border-radius: 9999px; filter: blur(100px); }
        .b1 { width: 55vmax; height: 55vmax; left: -15vmax; top: -22vmax; background: var(--violet); opacity: .5; animation: drift1 20s ease-in-out infinite alternate; }
        .b2 { width: 46vmax; height: 46vmax; right: -14vmax; bottom: -20vmax; background: var(--rose); opacity: .4; animation: drift2 24s ease-in-out infinite alternate; }
        @keyframes drift1 { to { transform: translate(10vmax, 8vmax) scale(1.15); } }
        @keyframes drift2 { to { transform: translate(-9vmax, -6vmax) scale(1.2); } }

        /* pink flash each time access is denied */
        .flare { position: fixed; inset: 0; z-index: 1; pointer-events: none; opacity: 0; background: radial-gradient(circle at 50% 40%, rgba(236, 72, 153, .38), transparent 60%); animation: flare var(--cycle) infinite; }
        @keyframes flare { 0%, 52% { opacity: 0; } 54% { opacity: 1; } 72%, 100% { opacity: 0; } }

        .rows { position: fixed; inset: 0; z-index: 1; display: flex; flex-direction: column; justify-content: space-around; overflow: hidden; pointer-events: none; }
        .row { white-space: nowrap; font-weight: 900; font-size: 24vh; line-height: 1; letter-spacing: -.02em; }
        .row span {
            display: inline-block; color: transparent;
            -webkit-text-stroke: 1.5px rgba(168, 85, 247, .22);
            animation: slide 40s linear infinite;
        }
        .row:nth-child(even) span { animation-direction: reverse; animation-duration: 55s; -webkit-text-stroke-color: rgba(236, 72, 153, .18); }
        .row:nth-child(3) span { animation-duration: 32s; }
        @keyframes slide { to { transform: translateX(-50%); } }

        #rain { position: fixed; inset: 0; z-index: 2; width: 100%; height: 100%; pointer-events: none; opacity: .6; }

        .vignette { position: fixed; inset: 0; z-index: 3; pointer-events: none; background: radial-gradient(ellipse at 50% 40%, transparent 35%, rgba(5, 3, 10, .88) 100%); }

        /* ---------- Branding ---------- */
        .branding {
            position: fixed; top: 24px; right: 24px; z-index: 100;
            display: flex; align-items: center; gap: 10px;
            padding: 10px 16px;
            background: rgba(255, 255, 255, .04);
            border: 1px solid rgba(255, 255, 255, .09);
            border-radius: 12px; backdrop-filter: blur(10px);
            text-decoration: none; color: #fff; transition: all .3s ease;
        }
        .branding:hover { background: rgba(255, 255, 255, .07); border-color: rgba(139, 92, 246, .45); }
        .branding svg { width: 24px; height: 24px; color: var(--purple); }
        .branding:hover svg { animation: zap .5s ease; }
        @keyframes zap { 30% { transform: scale(1.3) rotate(-12deg); } 60% { transform: scale(.95) rotate(8deg); } }
        .branding-text { display: flex; flex-direction: column; text-align: left; }
        .branding-title { font-weight: 900; font-size: 14px; letter-spacing: .1em; background: linear-gradient(to right, #d8b4fe, #f9a8d4); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .branding-sub { font-size: 10px; color: #71717a; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; }

        /* ---------- Layout ---------- */
        .layout { position: fixed; inset: 0; z-index: 5; display: flex; flex-direction: column; }

        .digits {
            flex: 1; min-height: 0;
            display: flex; align-items: center; justify-content: center;
            gap: min(2vw, 2rem);
            perspective: 1200px;
            font-size: min(54vh, 30vw);
            padding-top: 2vh;
        }
        .p { will-change: transform; transition: transform .25s ease-out; }

        /* entrance: everything rushes in from deep behind the screen */
        .zin { animation: zin 1s cubic-bezier(.2, 1.1, .3, 1) both; }
        .zin.z1 { animation-delay: .1s; }
        .zin.z2 { animation-delay: .35s; }
        .zin.z3 { animation-delay: .6s; }
        @keyframes zin { from { transform: scale(3.2); opacity: 0; filter: blur(20px); } to { transform: none; opacity: 1; filter: blur(0); } }

        .digit {
            display: block; line-height: .9; font-weight: 900; letter-spacing: -.03em;
            transform-style: preserve-3d;
            background: linear-gradient(180deg, #f5d0fe 0%, #c084fc 38%, #a855f7 62%, #6d28d9 100%);
            background-size: 100% 220%;
            -webkit-background-clip: text; background-clip: text;
            -webkit-text-fill-color: transparent; color: transparent;
            filter: drop-shadow(0 0 46px rgba(168, 85, 247, .5));
            animation: shine 6s ease-in-out infinite alternate;
        }
        @keyframes shine { to { background-position: 0 100%; } }

        .d1 { animation: shine 6s ease-in-out infinite alternate, sway 5s ease-in-out infinite alternate; transform-origin: 50% 100%; }
        @keyframes sway {
            from { transform: rotateY(-22deg) rotateZ(-4deg) translateY(0); }
            to   { transform: rotateY(22deg) rotateZ(4deg) translateY(-3vh); }
        }

        /* ---------- Padlock: the one memorable thing ---------- */
        .lock-stage { position: relative; width: .8em; height: .8em; margin: 0 auto; }

        /* hangs and swings like a pendulum */
        .lock-wrap {
            position: absolute; inset: 0;
            filter: drop-shadow(0 0 46px rgba(168, 85, 247, .5));
            transform-origin: 50% -25%;
            animation: swing 4.5s ease-in-out infinite alternate;
        }
        @keyframes swing { from { transform: rotate(-3.5deg); } to { transform: rotate(3.5deg); } }

        .lock-core { width: 100%; height: 100%; animation: nope var(--cycle) infinite; }
        .lock-core svg { width: 100%; height: 100%; display: block; overflow: visible; }
        @keyframes nope {
            0%, 52% { transform: none; filter: none; }
            53% { transform: translateX(-4%) rotate(-5deg); filter: drop-shadow(0 0 22px #ec4899) brightness(1.5); }
            56% { transform: translateX(4%) rotate(5deg); }
            59% { transform: translateX(-3%) rotate(-3deg); }
            62% { transform: translateX(2%) rotate(2deg); }
            66%, 100% { transform: none; filter: none; }
        }

        /* shackle tries to pop open, then slams back */
        .shackle { animation: shackle var(--cycle) infinite; }
        @keyframes shackle {
            0%, 38% { transform: translateY(0); }
            42%, 47% { transform: translateY(-1.8px); }
            50%, 100% { transform: translateY(0); }
        }
        .lock-body { fill: rgba(168, 85, 247, .12); animation: bodyfill var(--cycle) infinite; }
        @keyframes bodyfill {
            0%, 52% { fill: rgba(168, 85, 247, .12); }
            54%, 70% { fill: rgba(236, 72, 153, .38); }
            90%, 100% { fill: rgba(168, 85, 247, .12); }
        }

        /* safe-dial ring that clicks around while the code is entered */
        .dial { position: absolute; inset: -20%; border-radius: 9999px; border: 2px dashed rgba(168, 85, 247, .38); pointer-events: none; animation: dial var(--cycle) cubic-bezier(.6, 0, .3, 1) infinite; }
        .dial.d2 { inset: -38%; border: 2px dotted rgba(236, 72, 153, .3); animation-direction: reverse; }
        @keyframes dial {
            0% { transform: rotate(0); }
            38% { transform: rotate(210deg); }
            50% { transform: rotate(210deg); }
            52% { transform: rotate(196deg); }
            58% { transform: rotate(214deg); }
            100% { transform: rotate(360deg); }
        }
        .dial-mark { position: absolute; left: 50%; top: -27%; width: 0; height: 0; margin-left: -.035em; border-left: .035em solid transparent; border-right: .035em solid transparent; border-top: .06em solid var(--pink); filter: drop-shadow(0 0 10px var(--pink)); pointer-events: none; }

        /* scanner beam */
        .beam { position: absolute; left: -18%; width: 136%; height: 3px; top: 8%; opacity: 0; border-radius: 9999px; pointer-events: none;
            background: linear-gradient(to right, transparent, #f9a8d4, transparent);
            box-shadow: 0 0 18px 4px rgba(236, 72, 153, .7), 0 -24px 36px rgba(168, 85, 247, .25);
            animation: beam var(--cycle) linear infinite; }
        @keyframes beam {
            0%, 8% { top: 12%; opacity: 0; }
            10% { opacity: 1; }
            36% { top: 90%; opacity: 1; }
            38%, 100% { top: 90%; opacity: 0; }
        }

        /* DENIED stamp */
        .stamp {
            position: absolute; left: 50%; top: 50%; z-index: 3;
            margin: -.07em 0 0 -.27em; padding: .01em .05em;
            font-size: .12em; font-weight: 900; letter-spacing: .12em; white-space: nowrap; line-height: 1.1;
            color: #f9a8d4; border: .015em solid #ec4899; border-radius: .06em;
            background: rgba(9, 6, 15, .72);
            box-shadow: 0 0 .1em rgba(236, 72, 153, .8), inset 0 0 .1em rgba(236, 72, 153, .5);
            opacity: 0; pointer-events: none;
            animation: stamp var(--cycle) infinite;
        }
        @keyframes stamp {
            0%, 52% { opacity: 0; transform: scale(2.6) rotate(-14deg); }
            55% { opacity: 1; transform: scale(1) rotate(-12deg); }
            82% { opacity: 1; transform: scale(1) rotate(-12deg); }
            90%, 100% { opacity: 0; transform: scale(1) rotate(-12deg); }
        }

        /* 1 : glitch */
        .d3 { position: relative; animation: shine 6s ease-in-out infinite alternate, jitter 4s infinite; }
        .d3::before, .d3::after {
            content: attr(data-text);
            position: absolute; left: 0; top: 0; width: 100%;
            -webkit-text-fill-color: currentColor; background: none; overflow: hidden;
        }
        .d3::before { color: #22d3ee; transform: translate(-.02em, 0); animation: gA 2.4s infinite steps(1); }
        .d3::after  { color: var(--pink); transform: translate(.02em, 0);  animation: gB 3s infinite steps(1); }
        @keyframes gA {
            0% { clip-path: inset(0 0 100% 0); } 10% { clip-path: inset(12% 0 70% 0); } 20% { clip-path: inset(60% 0 12% 0); }
            30% { clip-path: inset(0 0 100% 0); } 55% { clip-path: inset(35% 0 45% 0); } 65% { clip-path: inset(80% 0 4% 0); }
            75%, 100% { clip-path: inset(0 0 100% 0); }
        }
        @keyframes gB {
            0% { clip-path: inset(0 0 100% 0); } 15% { clip-path: inset(70% 0 10% 0); } 25% { clip-path: inset(20% 0 60% 0); }
            35% { clip-path: inset(0 0 100% 0); } 60% { clip-path: inset(50% 0 30% 0); } 70% { clip-path: inset(5% 0 85% 0); }
            80%, 100% { clip-path: inset(0 0 100% 0); }
        }
        @keyframes jitter {
            0%, 18%, 22%, 60%, 64%, 100% { transform: translate(0, 0); opacity: 1; }
            20% { transform: translate(-.015em, .01em); opacity: .5; }
            62% { transform: translate(.015em, -.01em); opacity: .6; }
            63% { transform: translate(-.01em, 0); opacity: 1; }
        }

        /* ---------- Panel ---------- */
        .panel {
            align-self: center;
            width: min(760px, calc(100% - 32px));
            margin-bottom: 3vh;
            padding: 22px 28px;
            text-align: center;
            background: rgba(255, 255, 255, .045);
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: 20px;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, .45);
            animation: up .9s 1.1s cubic-bezier(.2, .9, .3, 1) both, edge var(--cycle) infinite;
        }
        @keyframes up { from { transform: translateY(36px); opacity: 0; filter: blur(8px); } to { transform: none; opacity: 1; filter: blur(0); } }
        @keyframes edge {
            0%, 52% { border-color: rgba(255, 255, 255, .1); }
            55%, 68% { border-color: rgba(236, 72, 153, .6); }
            85%, 100% { border-color: rgba(255, 255, 255, .1); }
        }

        .pill {
            display: inline-flex; align-items: center; gap: 10px;
            padding: 5px 14px; border-radius: 9999px;
            background: rgba(236, 72, 153, .1); border: 1px solid rgba(236, 72, 153, .3);
            font-size: 11px; font-weight: 700; letter-spacing: .2em; text-transform: uppercase; color: #f9a8d4;
        }
        .pill i { width: 7px; height: 7px; border-radius: 9999px; background: var(--pink); box-shadow: 0 0 10px var(--pink); animation: dot 1.6s ease-in-out infinite; }
        @keyframes dot { 0%, 100% { opacity: .3; transform: scale(.8); } 50% { opacity: 1; transform: scale(1.2); } }

        /* typed in like a terminal prompt */
        .title {
            display: inline-block; position: relative; margin-top: .8rem;
            font-size: clamp(1.2rem, 3vw, 1.9rem); font-weight: 800; letter-spacing: .16em; text-transform: uppercase;
            animation: type 1.4s 1.5s steps(22) both;
        }
        .title::after { content: ''; display: inline-block; width: 3px; height: .95em; margin-left: .25em; vertical-align: -.1em; background: var(--pink); box-shadow: 0 0 10px var(--pink); animation: blink 1s steps(1) infinite; }
        @keyframes type { from { clip-path: inset(0 100% 0 0); } to { clip-path: inset(0 0 0 0); } }
        @keyframes blink { 50% { opacity: 0; } }
        h1.wrap { display: block; }

        .desc { margin: .5rem auto 0; max-width: 30rem; color: #a1a1aa; font-size: .95rem; line-height: 1.7; }

        /* PIN dots: filled one by one, turn pink when denied */
        .pin { margin-top: 1rem; display: inline-flex; gap: 12px; animation: denyshake var(--cycle) infinite; }
        .pin i { width: 13px; height: 13px; border-radius: 9999px; border: 2px solid rgba(168, 85, 247, .5); background: transparent; }
        .pin i:nth-child(1) { animation: pin1 var(--cycle) infinite; }
        .pin i:nth-child(2) { animation: pin2 var(--cycle) infinite; }
        .pin i:nth-child(3) { animation: pin3 var(--cycle) infinite; }
        .pin i:nth-child(4) { animation: pin4 var(--cycle) infinite; }
        @keyframes pin1 { 0%, 12% { background: transparent; transform: scale(1); } 14% { transform: scale(1.5); } 16%, 52% { background: var(--purple); box-shadow: 0 0 10px var(--purple); transform: scale(1); } 54%, 85% { background: var(--pink); border-color: var(--pink); box-shadow: 0 0 12px var(--pink); } 90%, 100% { background: transparent; box-shadow: none; } }
        @keyframes pin2 { 0%, 20% { background: transparent; transform: scale(1); } 22% { transform: scale(1.5); } 24%, 52% { background: var(--purple); box-shadow: 0 0 10px var(--purple); transform: scale(1); } 54%, 85% { background: var(--pink); border-color: var(--pink); box-shadow: 0 0 12px var(--pink); } 90%, 100% { background: transparent; box-shadow: none; } }
        @keyframes pin3 { 0%, 28% { background: transparent; transform: scale(1); } 30% { transform: scale(1.5); } 32%, 52% { background: var(--purple); box-shadow: 0 0 10px var(--purple); transform: scale(1); } 54%, 85% { background: var(--pink); border-color: var(--pink); box-shadow: 0 0 12px var(--pink); } 90%, 100% { background: transparent; box-shadow: none; } }
        @keyframes pin4 { 0%, 36% { background: transparent; transform: scale(1); } 38% { transform: scale(1.5); } 40%, 52% { background: var(--purple); box-shadow: 0 0 10px var(--purple); transform: scale(1); } 54%, 85% { background: var(--pink); border-color: var(--pink); box-shadow: 0 0 12px var(--pink); } 90%, 100% { background: transparent; box-shadow: none; } }
        @keyframes denyshake {
            0%, 52% { transform: none; }
            54% { transform: translateX(-9px); } 57% { transform: translateX(9px); } 60% { transform: translateX(-6px); } 63% { transform: translateX(4px); } 66%, 100% { transform: none; }
        }

        .actions { margin-top: 1.2rem; display: flex; gap: 12px; flex-wrap: wrap; justify-content: center; }
        .btn {
            position: relative; overflow: hidden;
            display: inline-flex; align-items: center; gap: .55rem;
            padding: .8rem 1.7rem; border: 0; border-radius: 10px; cursor: pointer;
            font-weight: 800; font-size: .82rem; text-transform: uppercase; letter-spacing: .08em;
            font-family: inherit; text-decoration: none; color: #fff;
            background: linear-gradient(to right, #8b5cf6, #db2777);
            box-shadow: 0 6px 0 #4c1d95, 0 12px 30px rgba(139, 92, 246, .4);
            transition: transform .12s, box-shadow .12s;
        }
        .btn::after {
            content: ''; position: absolute; top: 0; left: -60%; width: 40%; height: 100%;
            background: linear-gradient(100deg, transparent, rgba(255, 255, 255, .45), transparent);
            transform: skewX(-20deg); animation: glint 3.4s ease-in-out infinite;
        }
        @keyframes glint { 0%, 55% { left: -60%; } 100% { left: 140%; } }
        .btn:hover { transform: translateY(2px); box-shadow: 0 4px 0 #4c1d95, 0 8px 24px rgba(139, 92, 246, .4); }
        .btn:active { transform: translateY(6px); box-shadow: 0 0 0 #4c1d95; }
        .btn:focus-visible { outline: 2px solid #f9a8d4; outline-offset: 3px; }
        .btn svg { width: 18px; height: 18px; flex-shrink: 0; transition: transform .4s; }
        .btn:not(.ghost):hover svg { transform: rotate(-35deg) scale(1.15); }
        .btn.ghost { background: rgba(255, 255, 255, .05); border: 1px solid rgba(255, 255, 255, .14); box-shadow: 0 6px 0 rgba(0, 0, 0, .45); }
        .btn.ghost::after { display: none; }
        .btn.ghost:hover { background: rgba(255, 255, 255, .09); box-shadow: 0 4px 0 rgba(0, 0, 0, .45); }
        .btn.ghost:hover svg { transform: rotate(180deg); }

        .denied-pop { position: fixed; z-index: 200; pointer-events: none; font-size: .72rem; font-weight: 800; letter-spacing: .18em; color: #f9a8d4; text-shadow: 0 0 12px #ec4899; white-space: nowrap; }

        @media (max-width: 560px) {
            .title { animation: none; clip-path: none; }
            .title::after { display: none; }
        }
        @media (max-height: 720px) {
            .desc { display: none; }
            .panel { padding: 14px 20px; margin-bottom: 2vh; }
            .digits { font-size: min(50vh, 30vw); }
        }
        @media (max-height: 560px) {
            .pill, .pin { display: none; }
            .branding { top: 10px; right: 10px; padding: 6px 10px; }
            .branding svg { width: 16px; height: 16px; }
            .branding-title { font-size: 12px; }
            .branding-sub { font-size: 8px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
        }
    </style>
</head>
<body>

    <div class="aurora"><div class="blob b1"></div><div class="blob b2"></div></div>
    <div class="flare"></div>

    <div class="rows" aria-hidden="true">
        @for ($i = 0; $i < 4; $i++)
            <div class="row"><span>{{ str_repeat('401 ', 24) }}</span></div>
        @endfor
    </div>

    <canvas id="rain" aria-hidden="true"></canvas>
    <div class="vignette"></div>

    <a href="/" class="branding">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        <div class="branding-text">
            <span class="branding-title">RJ NEXUS</span>
            <span class="branding-sub">Powered by RJ CMS</span>
        </div>
    </a>

    <div class="layout">

        <div class="digits" aria-label="401">
            <div class="p" data-depth="38"><div class="zin z1"><span class="digit d1">4</span></div></div>

            <div class="p" data-depth="-26">
                <div class="zin z2">
                    <div class="lock-stage">
                        <span class="dial"></span>
                        <span class="dial d2"></span>
                        <span class="dial-mark"></span>
                        <div class="lock-wrap">
                            <div class="lock-core">
                                <svg viewBox="0 0 24 24" fill="none" stroke="url(#lockGradient)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <defs>
                                        <linearGradient id="lockGradient" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0" stop-color="#f5d0fe"/>
                                            <stop offset=".38" stop-color="#c084fc"/>
                                            <stop offset=".62" stop-color="#a855f7"/>
                                            <stop offset="1" stop-color="#6d28d9"/>
                                        </linearGradient>
                                    </defs>
                                    <path class="shackle" d="M8 11V7.5a4 4 0 0 1 8 0V11"></path>
                                    <rect class="lock-body" x="4" y="11" width="16" height="10.5" rx="2.5"></rect>
                                    <circle cx="12" cy="15.6" r="1.3"></circle>
                                    <line x1="12" y1="16.9" x2="12" y2="18.7"></line>
                                </svg>
                            </div>
                        </div>
                        <span class="beam"></span>
                        <span class="stamp">DENIED</span>
                    </div>
                </div>
            </div>

            <div class="p" data-depth="52"><div class="zin z3"><span class="digit d3" data-text="1">1</span></div></div>
        </div>

        <div class="panel">
            <span class="pill"><i></i>{{ __('Unauthorized') }}</span>
            <h1 class="wrap"><span class="title">{{ __('Authentication Required') }}</span></h1>
            <p class="desc">{{ __('You need to sign in to access this page. Please log in with your account and try again.') }}</p>

            <div><div class="pin" aria-hidden="true"><i></i><i></i><i></i><i></i></div></div>

            <div class="actions">
                <a href="{{ url('/login') }}" class="btn">
                    <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    {{ __('Log In') }}
                </a>
                <a href="{{ url('/') }}" class="btn ghost">
                    <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    {{ __('Back to Home') }}
                </a>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            /* parallax digits */
            var items = document.querySelectorAll('.p');
            document.addEventListener('mousemove', function (e) {
                var x = e.clientX / window.innerWidth - 0.5;
                var y = e.clientY / window.innerHeight - 0.5;
                items.forEach(function (el) {
                    var d = parseFloat(el.dataset.depth);
                    el.style.transform = 'translate(' + (x * d) + 'px,' + (y * d * 0.6) + 'px)';
                });
            });
            document.addEventListener('mouseleave', function () {
                items.forEach(function (el) { el.style.transform = ''; });
            });

            if (reduce) return;

            /* click anywhere: "ACCESS DENIED" floats up from the cursor */
            document.addEventListener('click', function (e) {
                if (e.target.closest('a, button')) return;
                var s = document.createElement('span');
                s.className = 'denied-pop';
                s.textContent = 'ACCESS DENIED';
                s.style.left = (e.clientX - 50) + 'px';
                s.style.top = (e.clientY - 8) + 'px';
                document.body.appendChild(s);
                s.animate([
                    { transform: 'translateY(0) scale(.8)', opacity: 0 },
                    { transform: 'translateY(-14px) scale(1.1)', opacity: 1, offset: .2 },
                    { transform: 'translateY(-60px) scale(1)', opacity: 0 }
                ], { duration: 1100, easing: 'ease-out' }).onfinish = function () { s.remove(); };
            });

            /* code rain, brighter around the cursor */
            var cv = document.getElementById('rain'), ctx = cv.getContext('2d');
            var dpr = Math.min(window.devicePixelRatio || 1, 2);
            var W, H, fs, cols, drops, speeds, mouse = { x: -9999, y: -9999 };
            var chars = '01010101ABCDEF0123456789#$%&@<>/\\=+*';

            function resize() {
                W = cv.width = innerWidth * dpr; H = cv.height = innerHeight * dpr;
                fs = 16 * dpr; cols = Math.ceil(W / fs);
                drops = []; speeds = [];
                for (var i = 0; i < cols; i++) { drops.push(-Math.random() * 60); speeds.push(.35 + Math.random() * .75); }
                ctx.font = fs + 'px monospace';
            }
            resize(); addEventListener('resize', resize);
            document.addEventListener('mousemove', function (e) { mouse.x = e.clientX * dpr; mouse.y = e.clientY * dpr; });
            document.addEventListener('mouseleave', function () { mouse.x = mouse.y = -9999; });

            var last = 0;
            function frame(t) {
                requestAnimationFrame(frame);
                if (t - last < 55) return;
                last = t;

                ctx.globalCompositeOperation = 'destination-out';
                ctx.fillStyle = 'rgba(0,0,0,.12)';
                ctx.fillRect(0, 0, W, H);
                ctx.globalCompositeOperation = 'source-over';

                for (var i = 0; i < cols; i++) {
                    var x = i * fs, y = drops[i] * fs;
                    if (y > 0) {
                        var near = Math.abs(x - mouse.x) < 110 * dpr && Math.abs(y - mouse.y) < 160 * dpr;
                        ctx.fillStyle = near ? 'rgba(249,168,212,1)' : (Math.random() > .96 ? 'rgba(245,208,254,.95)' : 'rgba(168,85,247,.6)');
                        ctx.fillText(chars.charAt((Math.random() * chars.length) | 0), x, y);
                    }
                    drops[i] += speeds[i] * (Math.abs(x - mouse.x) < 110 * dpr ? 1.8 : 1);
                    if (y > H && Math.random() > .975) drops[i] = -Math.random() * 20;
                }
            }
            requestAnimationFrame(frame);
        })();
    </script>
</body>
</html>