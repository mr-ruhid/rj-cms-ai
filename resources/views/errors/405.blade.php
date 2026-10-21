@php
    // Real request info, so the page shows exactly what was rejected
    $method = strtoupper(request()->method());
    $path   = '/' . ltrim(request()->path(), '/');
    $allow  = null;
    if (isset($exception) && method_exists($exception, 'getHeaders')) {
        $allow = $exception->getHeaders()['Allow'] ?? null;
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>405 - {{ __('Method Not Allowed') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600,700,900&display=swap" rel="stylesheet" />
    <style>
        :root {
            --bg: #09060f;
            --violet: #6d28d9;
            --purple: #a855f7;
            --pink: #ec4899;
            --rose: #be185d;
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

        /* hazard tape crossing the screen: the road is closed to this method */
        .tape {
            position: fixed; left: -20vw; width: 140vw; height: 46px; z-index: 1; pointer-events: none; overflow: hidden;
            display: flex; align-items: center;
            background: repeating-linear-gradient(-45deg, rgba(168, 85, 247, .16) 0 22px, rgba(9, 6, 15, 0) 22px 44px);
            border-top: 1px solid rgba(168, 85, 247, .28); border-bottom: 1px solid rgba(168, 85, 247, .28);
        }
        .tape.t1 { top: 20%; transform: rotate(-7deg); }
        .tape.t2 { bottom: 12%; transform: rotate(5deg); border-color: rgba(236, 72, 153, .28);
            background: repeating-linear-gradient(45deg, rgba(236, 72, 153, .13) 0 22px, rgba(9, 6, 15, 0) 22px 44px); }
        .tape span {
            display: inline-block; white-space: nowrap; font-size: 13px; font-weight: 800; letter-spacing: .3em;
            color: rgba(249, 168, 212, .5); animation: slide 38s linear infinite;
        }
        .tape.t2 span { animation-direction: reverse; animation-duration: 46s; color: rgba(216, 180, 254, .45); }

        .vignette { position: fixed; inset: 0; z-index: 3; pointer-events: none; background: radial-gradient(ellipse at 50% 40%, transparent 35%, rgba(5, 3, 10, .88) 100%); }

        /* flying request chips */
        .chip {
            position: fixed; left: 0; top: 0; z-index: 4; pointer-events: none; will-change: transform;
            padding: 6px 14px; border-radius: 9999px; font-size: .78rem; font-weight: 800; letter-spacing: .12em;
            color: #e9d5ff; background: rgba(168, 85, 247, .16); border: 1px solid rgba(192, 132, 252, .55);
            box-shadow: 0 0 18px rgba(168, 85, 247, .45); backdrop-filter: blur(6px);
        }
        .chip.hot { color: #fbcfe8; background: rgba(236, 72, 153, .16); border-color: rgba(244, 114, 182, .6); box-shadow: 0 0 18px rgba(236, 72, 153, .5); }

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

        /* entrance: digits flip down like a departure board */
        .flip { transform-origin: 50% 100%; animation: flip .9s cubic-bezier(.2, .9, .3, 1.25) both; }
        .flip.f1 { animation-delay: .1s; }
        .flip.f2 { animation-delay: .35s; }
        .flip.f3 { animation-delay: .6s; }
        @keyframes flip { from { transform: perspective(900px) rotateX(-95deg); opacity: 0; } to { transform: perspective(900px) rotateX(0); opacity: 1; } }

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

        /* ---------- Prohibited sign: the one memorable thing ---------- */
        .ban-stage { position: relative; width: .8em; height: .8em; margin: 0 auto; }

        /* turns on its post like a road sign */
        .ban-wrap {
            position: absolute; inset: 0;
            filter: drop-shadow(0 0 46px rgba(168, 85, 247, .5));
            animation: swivel 4.5s ease-in-out infinite alternate;
        }
        @keyframes swivel { from { transform: perspective(700px) rotateY(-28deg); } to { transform: perspective(700px) rotateY(28deg); } }

        .ban-core { width: 100%; height: 100%; }
        .ban-core svg { width: 100%; height: 100%; display: block; overflow: visible; }
        .ban-core.hit { animation: hit .3s ease-out; }
        @keyframes hit {
            0% { transform: scale(1); filter: brightness(1); }
            30% { transform: scale(.93) rotate(var(--kick, 3deg)); filter: brightness(1.8) drop-shadow(0 0 22px #ec4899); }
            100% { transform: scale(1); filter: brightness(1); }
        }

        /* sign draws itself: ring first, slash slams in after */
        .ban-ring { fill: rgba(168, 85, 247, .08); stroke-dasharray: 63; stroke-dashoffset: 0; animation: ring 1s 1s cubic-bezier(.6, 0, .3, 1) both; }
        .ban-slash { stroke-dasharray: 20; stroke-dashoffset: 0; animation: slash .45s 1.9s cubic-bezier(.3, 1.4, .5, 1) both; }
        @keyframes ring  { from { stroke-dashoffset: 63; fill: rgba(168, 85, 247, 0); } }
        @keyframes slash { from { stroke-dashoffset: 20; } }

        /* rotating dashed ring around the sign */
        .orbit { position: absolute; inset: -20%; border-radius: 9999px; border: 2px dashed rgba(168, 85, 247, .32); pointer-events: none; animation: spin 26s linear infinite; }
        .orbit.o2 { inset: -36%; border: 2px dotted rgba(236, 72, 153, .28); animation-duration: 40s; animation-direction: reverse; }
        @keyframes spin { to { transform: rotate(360deg); } }

        .shock { position: absolute; inset: 0; border-radius: 9999px; border: 3px solid var(--pink); pointer-events: none; opacity: 0; }

        /* 5 : glitch */
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
            animation: crt .9s 1.2s cubic-bezier(.2, .9, .3, 1) both;
        }
        /* switches on like an old monitor: line, then full height */
        @keyframes crt {
            0%   { transform: scale(.15, .01); opacity: 0; }
            45%  { transform: scale(1, .01); opacity: 1; }
            100% { transform: none; opacity: 1; }
        }

        .pill {
            display: inline-flex; align-items: center; gap: 10px;
            padding: 5px 14px; border-radius: 9999px;
            background: rgba(236, 72, 153, .1); border: 1px solid rgba(236, 72, 153, .3);
            font-size: 11px; font-weight: 700; letter-spacing: .2em; text-transform: uppercase; color: #f9a8d4;
        }
        .pill i { width: 7px; height: 7px; border-radius: 9999px; background: var(--pink); box-shadow: 0 0 10px var(--pink); animation: dot 1.6s ease-in-out infinite; }
        @keyframes dot { 0%, 100% { opacity: .3; transform: scale(.8); } 50% { opacity: 1; transform: scale(1.2); } }

        .title { margin-top: .8rem; font-size: clamp(1.2rem, 3vw, 1.9rem); font-weight: 800; letter-spacing: .16em; text-transform: uppercase; }
        .desc { margin: .5rem auto 0; max-width: 30rem; color: #a1a1aa; font-size: .95rem; line-height: 1.7; }

        /* the actual rejected request */
        .req {
            margin-top: 1rem; display: inline-flex; align-items: center; gap: 12px; flex-wrap: wrap; justify-content: center; max-width: 100%;
            padding: 8px 16px; border-radius: 10px;
            background: rgba(0, 0, 0, .28); border: 1px solid rgba(255, 255, 255, .08);
            font-family: ui-monospace, 'SF Mono', Menlo, Consolas, monospace; font-size: .85rem;
        }
        .req .verb { position: relative; color: #f9a8d4; font-weight: 700; }
        .req .verb::after { content: ''; position: absolute; left: -3px; right: -3px; top: 52%; height: 2px; background: var(--pink); box-shadow: 0 0 8px var(--pink); transform-origin: left; animation: strike 4.5s 2.4s infinite; }
        @keyframes strike { 0% { transform: scaleX(0); } 12%, 82% { transform: scaleX(1); } 92%, 100% { transform: scaleX(0); } }
        .req .path { color: #d8b4fe; overflow-wrap: anywhere; }
        .req .code { padding: 1px 9px; border-radius: 6px; font-weight: 700; color: #fff; background: rgba(236, 72, 153, .2); border: 1px solid rgba(236, 72, 153, .5); }
        .req .arrow { color: #71717a; }

        .allow { margin-top: .7rem; font-size: .78rem; font-weight: 600; color: #71717a; }
        .allow b { margin-left: 4px; padding: 2px 9px; border-radius: 6px; font-weight: 800; letter-spacing: .08em; color: #d8b4fe; background: rgba(168, 85, 247, .14); border: 1px solid rgba(168, 85, 247, .4); animation: glow 2.4s ease-in-out infinite; }
        @keyframes glow { 50% { box-shadow: 0 0 14px rgba(168, 85, 247, .6); } }

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
        .btn svg { width: 18px; height: 18px; flex-shrink: 0; transition: transform .35s; }
        .btn:not(.ghost):hover svg { transform: translateX(-5px); }
        .btn.ghost { background: rgba(255, 255, 255, .05); border: 1px solid rgba(255, 255, 255, .14); box-shadow: 0 6px 0 rgba(0, 0, 0, .45); }
        .btn.ghost::after { display: none; }
        .btn.ghost:hover { background: rgba(255, 255, 255, .09); box-shadow: 0 4px 0 rgba(0, 0, 0, .45); }
        .btn.ghost:hover svg { transform: rotate(180deg); }

        @media (max-height: 720px) {
            .desc, .allow { display: none; }
            .panel { padding: 14px 20px; margin-bottom: 2vh; }
            .digits { font-size: min(50vh, 30vw); }
        }
        @media (max-height: 560px) {
            .pill, .req { display: none; }
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

    <div class="rows" aria-hidden="true">
        @for ($i = 0; $i < 4; $i++)
            <div class="row"><span>{{ str_repeat('405 ', 24) }}</span></div>
        @endfor
    </div>

    <div class="tape t1" aria-hidden="true"><span>{{ str_repeat('405  ///  METHOD NOT ALLOWED  ///  ', 14) }}</span></div>
    <div class="tape t2" aria-hidden="true"><span>{{ str_repeat('POST  PUT  PATCH  DELETE  ///  ', 14) }}</span></div>

    <div class="vignette"></div>

    <a href="/" class="branding">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        <div class="branding-text">
            <span class="branding-title">RJ NEXUS</span>
            <span class="branding-sub">Powered by RJ CMS</span>
        </div>
    </a>

    <div class="layout">

        <div class="digits" aria-label="405">
            <div class="p" data-depth="38"><div class="flip f1"><span class="digit d1">4</span></div></div>

            <div class="p" data-depth="-26">
                <div class="flip f2">
                    <div class="ban-stage" id="ban">
                        <span class="orbit"></span>
                        <span class="orbit o2"></span>
                        <div class="ban-wrap">
                            <div class="ban-core" id="core">
                                <svg viewBox="0 0 24 24" fill="none" stroke="url(#banGradient)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <defs>
                                        <linearGradient id="banGradient" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0" stop-color="#f5d0fe"/>
                                            <stop offset=".38" stop-color="#c084fc"/>
                                            <stop offset=".62" stop-color="#a855f7"/>
                                            <stop offset="1" stop-color="#6d28d9"/>
                                        </linearGradient>
                                    </defs>
                                    <circle class="ban-ring" cx="12" cy="12" r="10"></circle>
                                    <line class="ban-slash" x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p" data-depth="52"><div class="flip f3"><span class="digit d3" data-text="5">5</span></div></div>
        </div>

        <div class="panel">
            <span class="pill"><i></i>{{ __('Method Not Allowed') }}</span>
            <h1 class="title">{{ __('Invalid Request Method') }}</h1>
            <p class="desc">{{ __('This page does not accept the request method that was used. Please go back and try again.') }}</p>

            <div class="req" aria-hidden="true">
                <span class="verb">{{ $method }}</span>
                <span class="path">{{ $path }}</span>
                <span class="arrow">&rarr;</span>
                <span class="code">405</span>
            </div>

            @if ($allow)
                <p class="allow">{{ __('Allowed') }}: <b>{{ $allow }}</b></p>
            @endif

            <div class="actions">
                <button class="btn" onclick="goBack()">
                    <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    {{ __('Go Back') }}
                </button>
                <a href="{{ url('/') }}" class="btn ghost">
                    <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    {{ __('Back to Home') }}
                </a>
            </div>
        </div>
    </div>

    <script>
        function goBack() {
            if (history.length > 1) history.back();
            else window.location.href = @json(url('/'));
        }

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

            /* HTTP verbs fly at the sign, hit it and bounce off */
            var ban = document.getElementById('ban');
            var core = document.getElementById('core');
            var verbs = ['POST', 'PUT', 'PATCH', 'DELETE', 'POST', 'PUT'];
            var current = @json($method);
            if (current && verbs.indexOf(current) === -1) verbs.push(current);

            function hit(angle) {
                core.style.setProperty('--kick', (Math.cos(angle) * 5) + 'deg');
                core.classList.remove('hit'); void core.offsetWidth; core.classList.add('hit');
                var s = document.createElement('span');
                s.className = 'shock';
                ban.appendChild(s);
                s.animate([
                    { transform: 'scale(.9)', opacity: .9 },
                    { transform: 'scale(1.7)', opacity: 0 }
                ], { duration: 650, easing: 'ease-out' }).onfinish = function () { s.remove(); };
            }

            function fire(fromX, fromY, verb) {
                var r = ban.getBoundingClientRect();
                var cx = r.left + r.width / 2, cy = r.top + r.height / 2, R = r.width * .5;
                var ang = Math.atan2(fromY - cy, fromX - cx);
                var hx = cx + Math.cos(ang) * R, hy = cy + Math.sin(ang) * R;

                var chip = document.createElement('div');
                chip.className = 'chip' + (verb === 'DELETE' ? ' hot' : '');
                chip.textContent = verb;
                document.body.appendChild(chip);
                var w = chip.offsetWidth / 2, h = chip.offsetHeight / 2;

                var side = Math.random() > .5 ? 1 : -1;
                var ba = ang + side * (.7 + Math.random() * .5);
                var mx = hx + Math.cos(ba) * 130, my = hy + Math.sin(ba) * 130;
                var ex = hx + Math.cos(ba) * 260, ey = hy + Math.sin(ba) * 260 + 110;
                function T(x, y, rot) { return 'translate(' + (x - w) + 'px,' + (y - h) + 'px) rotate(' + rot + 'deg)'; }

                var dur = 1600;
                chip.animate([
                    { transform: T(fromX, fromY, 0), opacity: 0, easing: 'cubic-bezier(.55,0,1,.6)' },
                    { transform: T(fromX + (hx - fromX) * .1, fromY + (hy - fromY) * .1, 0), opacity: 1, offset: .1 },
                    { transform: T(hx, hy, 0), opacity: 1, offset: .5, easing: 'cubic-bezier(.1,.7,.4,1)' },
                    { transform: T(mx, my, side * 140), opacity: 1, offset: .78, easing: 'cubic-bezier(.4,0,1,1)' },
                    { transform: T(ex, ey, side * 300), opacity: 0 }
                ], { duration: dur, fill: 'forwards' }).onfinish = function () { chip.remove(); };

                setTimeout(function () { hit(ang); }, dur * .5);
            }

            function spawn() {
                if (document.hidden) return;
                var r = ban.getBoundingClientRect();
                var cy = r.top + r.height / 2;
                var left = Math.random() > .5;
                var x = left ? -70 : innerWidth + 70;
                var y = Math.max(60, Math.min(innerHeight - 60, cy + (Math.random() - .5) * 360));
                fire(x, y, verbs[(Math.random() * verbs.length) | 0]);
            }
            setTimeout(spawn, 2600);
            setInterval(spawn, 1300);

            /* click anywhere: send a request from the cursor */
            document.addEventListener('click', function (e) {
                if (e.target.closest('a, button')) return;
                fire(e.clientX, e.clientY, verbs[(Math.random() * verbs.length) | 0]);
            });
        })();
    </script>
</body>
</html>