<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>419 - {{ __('Page Expired') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600,700,900&display=swap" rel="stylesheet" />
    <style>
        :root {
            --bg: #09060f;
            --violet: #6d28d9;
            --purple: #a855f7;
            --pink: #ec4899;
            --rose: #be185d;
            --mx: 0;
            --my: 0;
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
        .b3 { width: 28vmax; height: 28vmax; left: 38vw; top: 30vh; background: #7c3aed; opacity: .22; animation: drift3 16s ease-in-out infinite alternate; }
        @keyframes drift1 { to { transform: translate(10vmax, 8vmax) scale(1.15); } }
        @keyframes drift2 { to { transform: translate(-9vmax, -6vmax) scale(1.2); } }
        @keyframes drift3 { to { transform: translate(-12vmax, 6vmax) scale(1.4); } }

        #stars { position: fixed; inset: 0; z-index: 1; width: 100%; height: 100%; pointer-events: auto; }

        /* cursor spotlight */
        .spot {
            position: fixed; inset: 0; z-index: 1; pointer-events: none;
            background: radial-gradient(420px circle at var(--sx, 50%) var(--sy, 40%), rgba(168, 85, 247, .16), transparent 70%);
            transition: background .15s;
        }

        .vignette { position: fixed; inset: 0; z-index: 2; pointer-events: none; background: radial-gradient(ellipse at 50% 40%, transparent 35%, rgba(5, 3, 10, .88) 100%); }

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
        .layout { position: fixed; inset: 0; z-index: 5; display: flex; flex-direction: column; pointer-events: none; }
        .layout > * { pointer-events: auto; }

        .digits {
            flex: 1; min-height: 0;
            display: flex; align-items: center; justify-content: center;
            gap: min(2vw, 2rem);
            perspective: 1200px;
            font-size: min(54vh, 30vw);
            padding-top: 2vh;
        }
        .p { will-change: transform; transition: transform .25s ease-out; }

        /* one orchestrated entrance: digits drop in with a bounce */
        .drop { animation: drop 1s cubic-bezier(.2, 1.3, .4, 1) both; }
        .p:nth-child(1) .drop { animation-delay: .05s; }
        .p:nth-child(2) .drop { animation-delay: .25s; }
        .p:nth-child(3) .drop { animation-delay: .45s; }
        @keyframes drop {
            from { transform: translateY(-90vh) rotateX(80deg); opacity: 0; filter: blur(14px); }
            to   { transform: none; opacity: 1; filter: blur(0); }
        }

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

        /* 4 : sways in 3D */
        .d1 { animation: shine 6s ease-in-out infinite alternate, sway 5s ease-in-out infinite alternate; transform-origin: 50% 100%; }
        @keyframes sway {
            from { transform: rotateY(-22deg) rotateZ(-4deg) translateY(0); }
            to   { transform: rotateY(22deg) rotateZ(4deg) translateY(-3vh); }
        }

        /* ---------- The clock: the one memorable thing ---------- */
        .clock-stage { position: relative; width: .8em; height: .8em; margin: 0 auto; }
        .clock-wrap {
            position: absolute; inset: 0;
            filter: drop-shadow(0 0 46px rgba(168, 85, 247, .5));
            animation: flip 7s cubic-bezier(.65, 0, .35, 1) infinite;
        }
        .clock-wrap svg { width: 100%; height: 100%; display: block; overflow: visible; }
        @keyframes flip {
            0%, 35% { transform: rotateX(0) scale(1); }
            50% { transform: rotateX(180deg) scale(1.08); }
            65%, 100% { transform: rotateX(360deg) scale(1); }
        }

        /* hands tick like a real clock; minute hand runs fast = time is slipping away */
        .hand { transform-origin: 12px 12px; transform-box: view-box; }
        .hand-m { animation: spin 6s steps(60) infinite; }
        .hand-h { animation: spin 72s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ticks around the dial */
        .ticks line { stroke: #c084fc; stroke-width: .5; stroke-linecap: round; opacity: .7; }
        .ticks line:nth-child(3n) { stroke: #f9a8d4; stroke-width: .9; opacity: 1; }

        /* orbiting dashed rings + satellites */
        .orbit {
            position: absolute; inset: -22%; border-radius: 9999px;
            border: 2px dashed rgba(168, 85, 247, .35);
            animation: spin 22s linear infinite;
            pointer-events: none;
        }
        .orbit.o2 { inset: -42%; border-color: rgba(236, 72, 153, .22); animation-duration: 34s; animation-direction: reverse; }
        .orbit::after {
            content: ''; position: absolute; top: -.045em; left: 50%; width: .09em; height: .09em; margin-left: -.045em;
            border-radius: 9999px; background: #f9a8d4; box-shadow: 0 0 18px 4px rgba(236, 72, 153, .8);
        }
        .orbit.o2::after { background: #d8b4fe; box-shadow: 0 0 18px 4px rgba(168, 85, 247, .8); }

        /* sound-wave pulse emitted every few seconds, like an alarm */
        .pulse {
            position: absolute; inset: 0; border-radius: 9999px;
            border: 2px solid rgba(236, 72, 153, .6);
            opacity: 0; animation: ping 3.5s ease-out infinite;
            pointer-events: none;
        }
        .pulse.p2 { animation-delay: 1.15s; }
        .pulse.p3 { animation-delay: 2.3s; }
        @keyframes ping { 0% { transform: scale(.7); opacity: .8; } 100% { transform: scale(2.2); opacity: 0; } }

        /* 9 : glitch */
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
            position: relative;
            align-self: center;
            width: min(760px, calc(100% - 32px));
            margin-bottom: 3vh;
            padding: 22px 28px;
            text-align: center;
            background: rgba(255, 255, 255, .045);
            border-radius: 20px;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, .45);
            transform-style: preserve-3d;
            transition: transform .2s ease-out;
            animation: rise .9s .9s cubic-bezier(.2, .9, .3, 1) both;
        }
        @keyframes rise { from { transform: translateY(40px); opacity: 0; } to { transform: none; opacity: 1; } }

        /* rotating gradient border */
        @property --ang { syntax: '<angle>'; initial-value: 0deg; inherits: false; }
        .panel::before {
            content: ''; position: absolute; inset: 0; border-radius: inherit; padding: 1px;
            background: conic-gradient(from var(--ang), rgba(255, 255, 255, .08), var(--purple), rgba(255, 255, 255, .08) 40%, var(--pink), rgba(255, 255, 255, .08));
            -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
            -webkit-mask-composite: xor; mask-composite: exclude;
            animation: borderspin 6s linear infinite;
            pointer-events: none;
        }
        @keyframes borderspin { to { --ang: 360deg; } }

        .pill {
            display: inline-flex; align-items: center; gap: 10px;
            padding: 5px 14px; border-radius: 9999px;
            background: rgba(236, 72, 153, .1); border: 1px solid rgba(236, 72, 153, .3);
            font-size: 11px; font-weight: 700; letter-spacing: .2em; text-transform: uppercase; color: #f9a8d4;
        }
        .pill i { width: 7px; height: 7px; border-radius: 9999px; background: var(--pink); box-shadow: 0 0 10px var(--pink); animation: dot 1.6s ease-in-out infinite; }
        @keyframes dot { 0%, 100% { opacity: .3; transform: scale(.8); } 50% { opacity: 1; transform: scale(1.2); } }

        .title {
            margin-top: .8rem; font-size: clamp(1.2rem, 3vw, 1.9rem); font-weight: 800; letter-spacing: .16em; text-transform: uppercase;
            background: linear-gradient(100deg, #fff 30%, #f0abfc 45%, #fff 60%);
            background-size: 250% 100%;
            -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;
            animation: sweep 4.5s ease-in-out infinite;
        }
        @keyframes sweep { 0% { background-position: 120% 0; } 60%, 100% { background-position: -60% 0; } }

        .desc { margin: .5rem auto 0; max-width: 30rem; color: #a1a1aa; font-size: .95rem; line-height: 1.7; }

        /* idle timer: shows how long the page has been "expired" */
        .idle { margin-top: .9rem; display: inline-flex; align-items: center; gap: 8px; font-size: .78rem; font-weight: 600; color: #71717a; font-variant-numeric: tabular-nums; }
        .idle b { color: #d8b4fe; font-weight: 700; }
        .idle .bar { width: 90px; height: 3px; border-radius: 9999px; background: rgba(255, 255, 255, .08); overflow: hidden; }
        .idle .bar::after { content: ''; display: block; height: 100%; width: 100%; background: linear-gradient(to right, var(--purple), var(--pink)); transform-origin: left; animation: drain 10s linear infinite; }
        @keyframes drain { from { transform: scaleX(1); } to { transform: scaleX(0); } }

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
        .btn svg { width: 18px; height: 18px; flex-shrink: 0; }
        .btn:not(.ghost) svg { animation: nudge 3.4s ease-in-out infinite; }
        @keyframes nudge { 0%, 70%, 100% { transform: rotate(0); } 85% { transform: rotate(360deg); } }
        .btn.ghost { background: rgba(255, 255, 255, .05); border: 1px solid rgba(255, 255, 255, .14); box-shadow: 0 6px 0 rgba(0, 0, 0, .45); }
        .btn.ghost::after { display: none; }
        .btn.ghost:hover { background: rgba(255, 255, 255, .09); box-shadow: 0 4px 0 rgba(0, 0, 0, .45); }
        .btn.ghost svg { transition: transform .5s; }
        .btn.ghost:hover svg { transform: rotate(180deg); }

        /* click burst */
        .spark { position: fixed; z-index: 200; width: 6px; height: 6px; border-radius: 9999px; pointer-events: none; }

        @media (max-height: 720px) {
            .desc, .idle { display: none; }
            .panel { padding: 14px 20px; margin-bottom: 2vh; }
            .digits { font-size: min(50vh, 30vw); }
        }
        @media (max-height: 560px) {
            .pill { display: none; }
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

    <div class="aurora"><div class="blob b1"></div><div class="blob b2"></div><div class="blob b3"></div></div>

    <div class="rows" aria-hidden="true">
        @for ($i = 0; $i < 4; $i++)
            <div class="row"><span>{{ str_repeat('419 ', 24) }}</span></div>
        @endfor
    </div>

    <canvas id="stars" aria-hidden="true"></canvas>
    <div class="spot" aria-hidden="true"></div>
    <div class="vignette"></div>

    <a href="/" class="branding">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        <div class="branding-text">
            <span class="branding-title">RJ NEXUS</span>
            <span class="branding-sub">Powered by RJ CMS</span>
        </div>
    </a>

    <div class="layout">

        <div class="digits" aria-label="419">
            <div class="p" data-depth="38"><div class="drop"><span class="digit d1">4</span></div></div>

            <div class="p" data-depth="-26">
                <div class="drop">
                    <div class="clock-stage">
                        <span class="pulse"></span><span class="pulse p2"></span><span class="pulse p3"></span>
                        <span class="orbit"></span>
                        <span class="orbit o2"></span>
                        <div class="clock-wrap">
                            <svg viewBox="0 0 24 24" fill="none" stroke="url(#clockGradient)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <defs>
                                    <linearGradient id="clockGradient" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0" stop-color="#f5d0fe"/>
                                        <stop offset=".38" stop-color="#c084fc"/>
                                        <stop offset=".62" stop-color="#a855f7"/>
                                        <stop offset="1" stop-color="#6d28d9"/>
                                    </linearGradient>
                                </defs>
                                <circle cx="12" cy="12" r="10"></circle>
                                <g class="ticks" id="ticks"></g>
                                <line class="hand hand-h" x1="12" y1="12" x2="12" y2="8"></line>
                                <line class="hand hand-m" x1="12" y1="12" x2="12" y2="5.5" stroke-width="1.6"></line>
                                <circle cx="12" cy="12" r="1" fill="#f5d0fe" stroke="none"></circle>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p" data-depth="52"><div class="drop"><span class="digit d3" data-text="9">9</span></div></div>
        </div>

        <div class="panel" id="panel">
            <span class="pill"><i></i>{{ __('Page Expired') }}</span>
            <h1 class="title">{{ __('Session Expired') }}</h1>
            <p class="desc">{{ __('Sorry, your session has expired due to inactivity. Please refresh the page and try again.') }}</p>

            <div class="idle" aria-hidden="true">
                <span class="bar"></span>
                <span>{{ __('Expired') }}: <b id="idle">0:00</b></span>
            </div>

            <div class="actions">
                <button class="btn" onclick="window.location.reload()">
                    <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    {{ __('Refresh Page') }}
                </button>
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

            /* clock ticks */
            var ticks = document.getElementById('ticks'), t = '';
            for (var i = 0; i < 12; i++) {
                var a = i * 30 * Math.PI / 180;
                t += '<line x1="' + (12 + Math.sin(a) * 8.2).toFixed(2) + '" y1="' + (12 - Math.cos(a) * 8.2).toFixed(2) +
                     '" x2="' + (12 + Math.sin(a) * 9.2).toFixed(2) + '" y2="' + (12 - Math.cos(a) * 9.2).toFixed(2) + '"/>';
            }
            ticks.innerHTML = t;

            /* idle counter */
            var idle = document.getElementById('idle'), start = Date.now();
            setInterval(function () {
                var s = Math.floor((Date.now() - start) / 1000);
                idle.textContent = Math.floor(s / 60) + ':' + ('0' + (s % 60)).slice(-2);
            }, 1000);

            /* parallax digits + spotlight + panel tilt */
            var items = document.querySelectorAll('.p');
            var spot = document.querySelector('.spot');
            var panel = document.getElementById('panel');
            var mouse = { x: -9999, y: -9999 };

            document.addEventListener('mousemove', function (e) {
                mouse.x = e.clientX; mouse.y = e.clientY;
                var x = e.clientX / window.innerWidth - 0.5;
                var y = e.clientY / window.innerHeight - 0.5;
                spot.style.setProperty('--sx', e.clientX + 'px');
                spot.style.setProperty('--sy', e.clientY + 'px');
                items.forEach(function (el) {
                    var d = parseFloat(el.dataset.depth);
                    el.style.transform = 'translate(' + (x * d) + 'px,' + (y * d * 0.6) + 'px)';
                });
                panel.style.transform = 'perspective(900px) rotateX(' + (-y * 6) + 'deg) rotateY(' + (x * 8) + 'deg)';
            });
            document.addEventListener('mouseleave', function () {
                mouse.x = mouse.y = -9999;
                items.forEach(function (el) { el.style.transform = ''; });
                panel.style.transform = '';
            });

            /* click burst */
            var colors = ['#c084fc', '#f9a8d4', '#a855f7', '#ec4899', '#f5d0fe'];
            document.addEventListener('click', function (e) {
                if (reduce) return;
                for (var i = 0; i < 18; i++) {
                    var s = document.createElement('span');
                    s.className = 'spark';
                    s.style.left = e.clientX + 'px';
                    s.style.top = e.clientY + 'px';
                    s.style.background = colors[i % colors.length];
                    s.style.boxShadow = '0 0 10px ' + colors[i % colors.length];
                    document.body.appendChild(s);
                    var ang = Math.random() * Math.PI * 2, dist = 50 + Math.random() * 110;
                    s.animate([
                        { transform: 'translate(0,0) scale(1)', opacity: 1 },
                        { transform: 'translate(' + Math.cos(ang) * dist + 'px,' + Math.sin(ang) * dist + 'px) scale(0)', opacity: 0 }
                    ], { duration: 650 + Math.random() * 350, easing: 'cubic-bezier(.2,.8,.3,1)' }).onfinish = (function (n) { return function () { n.remove(); }; })(s);
                }
            });

            /* particle network that reacts to the cursor */
            if (reduce) return;
            var cv = document.getElementById('stars'), ctx = cv.getContext('2d');
            var W, H, dpr = Math.min(window.devicePixelRatio || 1, 2), dots = [];

            function resize() {
                W = cv.width = innerWidth * dpr; H = cv.height = innerHeight * dpr;
                var n = Math.min(90, Math.floor(innerWidth * innerHeight / 14000));
                dots = [];
                for (var i = 0; i < n; i++) {
                    dots.push({
                        x: Math.random() * W, y: Math.random() * H,
                        vx: (Math.random() - .5) * .35 * dpr, vy: (Math.random() - .5) * .35 * dpr,
                        r: (Math.random() * 1.6 + .6) * dpr,
                        c: Math.random() > .5 ? '192,132,252' : '244,114,182'
                    });
                }
            }
            resize();
            addEventListener('resize', resize);

            function frame() {
                ctx.clearRect(0, 0, W, H);
                var mx = mouse.x * dpr, my = mouse.y * dpr, link = 130 * dpr;
                for (var i = 0; i < dots.length; i++) {
                    var a = dots[i];
                    var dx = a.x - mx, dy = a.y - my, d = Math.sqrt(dx * dx + dy * dy);
                    if (d < 140 * dpr) { a.vx += dx / d * .06; a.vy += dy / d * .06; }
                    a.vx *= .99; a.vy *= .99;
                    a.x += a.vx; a.y += a.vy;
                    if (a.x < 0) a.x = W; else if (a.x > W) a.x = 0;
                    if (a.y < 0) a.y = H; else if (a.y > H) a.y = 0;

                    ctx.beginPath();
                    ctx.arc(a.x, a.y, a.r, 0, 6.283);
                    ctx.fillStyle = 'rgba(' + a.c + ',.8)';
                    ctx.fill();

                    for (var j = i + 1; j < dots.length; j++) {
                        var b = dots[j], ex = a.x - b.x, ey = a.y - b.y, ed = Math.sqrt(ex * ex + ey * ey);
                        if (ed < link) {
                            ctx.strokeStyle = 'rgba(' + a.c + ',' + (1 - ed / link) * .22 + ')';
                            ctx.lineWidth = dpr;
                            ctx.beginPath(); ctx.moveTo(a.x, a.y); ctx.lineTo(b.x, b.y); ctx.stroke();
                        }
                    }
                }
                requestAnimationFrame(frame);
            }
            frame();
        })();
    </script>
</body>
</html>