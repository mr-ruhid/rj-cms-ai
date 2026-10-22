<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ rj_dir() }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>400 - {{ __('Bad Request') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600,700,900&display=swap" rel="stylesheet" />
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { width: 100%; height: 100%; }
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #09060f;
            color: #fff;
            overflow: hidden;
        }

        .aurora { position: fixed; inset: 0; z-index: 0; overflow: hidden; pointer-events: none; }
        .blob { position: absolute; border-radius: 9999px; filter: blur(100px); }
        .b1 { width: 55vmax; height: 55vmax; left: -15vmax; top: -22vmax; background: #6d28d9; opacity: .5; animation: drift1 20s ease-in-out infinite alternate; }
        .b2 { width: 46vmax; height: 46vmax; right: -14vmax; bottom: -20vmax; background: #be185d; opacity: .4; animation: drift2 24s ease-in-out infinite alternate; }
        @keyframes drift1 { to { transform: translate(10vmax, 8vmax) scale(1.15); } }
        @keyframes drift2 { to { transform: translate(-9vmax, -6vmax) scale(1.2); } }
        .vignette { position: fixed; inset: 0; z-index: 1; pointer-events: none; background: radial-gradient(ellipse at 50% 40%, transparent 35%, rgba(5, 3, 10, .88) 100%); }

        .rows { position: fixed; inset: 0; z-index: 1; display: flex; flex-direction: column; justify-content: space-around; overflow: hidden; pointer-events: none; }
        .row { white-space: nowrap; font-weight: 900; font-size: 24vh; line-height: 1; letter-spacing: -.02em; }
        .row span {
            display: inline-block;
            color: transparent;
            -webkit-text-stroke: 1.5px rgba(168, 85, 247, .22);
            animation: slide 40s linear infinite;
        }
        .row:nth-child(even) span { animation-direction: reverse; animation-duration: 55s; -webkit-text-stroke-color: rgba(236, 72, 153, .18); }
        .row:nth-child(3) span { animation-duration: 32s; }
        @keyframes slide { to { transform: translateX(-50%); } }

        /* Broken request fragments floating in the background */
        .frags { position: fixed; inset: 0; z-index: 2; pointer-events: none; overflow: hidden; }
        .frag {
            position: absolute;
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-weight: 700;
            font-size: clamp(14px, 2.2vmax, 28px);
            color: rgba(216, 180, 254, .22);
            text-shadow: 0 0 14px rgba(168, 85, 247, .35);
            animation: frag 9s ease-in-out infinite;
        }
        .frag.pink { color: rgba(249, 168, 212, .22); text-shadow: 0 0 14px rgba(236, 72, 153, .35); }
        @keyframes frag {
            0%, 100% { transform: translateY(0) rotate(var(--r, 0deg)); opacity: .35; }
            50% { transform: translateY(-22px) rotate(calc(var(--r, 0deg) * -1)); opacity: 1; }
        }

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
        .branding svg { width: 24px; height: 24px; color: #a855f7; }
        .branding-text { display: flex; flex-direction: column; text-align: left; }
        .branding-title { font-weight: 900; font-size: 14px; letter-spacing: .1em; background: linear-gradient(to right, #d8b4fe, #f9a8d4); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .branding-sub { font-size: 10px; color: #71717a; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; }

        .layout { position: fixed; inset: 0; z-index: 5; display: flex; flex-direction: column; }

        .digits {
            position: relative;
            flex: 1; min-height: 0;
            display: flex; align-items: center; justify-content: center;
            gap: min(2vw, 2rem);
            perspective: 1200px;
            font-size: min(54vh, 30vw);
            padding-top: 2vh;
        }

        /* Scan beam: the "validator" sweeping over the digits */
        .scan {
            position: absolute;
            left: 50%;
            width: min(88vw, 1.9em);
            height: 2px;
            transform: translateX(-50%);
            background: linear-gradient(to right, transparent, #f9a8d4, transparent);
            box-shadow: 0 0 18px 3px rgba(236, 72, 153, .55);
            opacity: 0;
            pointer-events: none;
            animation: scan 5s ease-in-out infinite;
            font-size: inherit;
        }
        @keyframes scan {
            0% { top: 14%; opacity: 0; }
            10% { opacity: .9; }
            45% { top: 84%; opacity: .9; }
            55%, 100% { top: 84%; opacity: 0; }
        }

        .p { will-change: transform; transition: transform .25s ease-out; }
        .digit {
            display: block; line-height: .9; font-weight: 900; letter-spacing: -.03em;
            transform-style: preserve-3d;
            background: linear-gradient(180deg, #f5d0fe 0%, #c084fc 38%, #a855f7 62%, #6d28d9 100%);
            -webkit-background-clip: text; background-clip: text;
            -webkit-text-fill-color: transparent; color: transparent;
            filter: drop-shadow(0 0 46px rgba(168, 85, 247, .5));
        }

        .d1 { animation: sway 5s ease-in-out infinite alternate; transform-origin: 50% 100%; }
        @keyframes sway {
            from { transform: rotateY(-22deg) rotateZ(-4deg) translateY(0); }
            to   { transform: rotateY(22deg) rotateZ(4deg) translateY(-3vh); }
        }

        .d2-wrap { position: relative; display: flex; justify-content: center; align-items: center; }
        .warn-icon {
            position: absolute;
            top: 51%;
            left: 50%;
            width: 0.2em;
            height: 0.4em;
            transform: translate(-50%, -50%);
            overflow: visible;
            filter: drop-shadow(0 0 14px rgba(236, 72, 153, .9));
            animation: warn 1.8s ease-in-out infinite;
        }
        @keyframes warn {
            0%, 100% { transform: translate(-50%, -50%) scale(1); filter: drop-shadow(0 0 10px rgba(236, 72, 153, .7)); }
            50% { transform: translate(-50%, -50%) scale(1.12); filter: drop-shadow(0 0 26px rgba(236, 72, 153, 1)); }
        }

        .d3 { position: relative; animation: jitter 4s infinite; }
        .d3::before, .d3::after {
            content: attr(data-text);
            position: absolute; left: 0; top: 0; width: 100%;
            -webkit-text-fill-color: currentColor; background: none; overflow: hidden;
        }
        .d3::before { color: #22d3ee; transform: translate(-.02em, 0); animation: gA 2.4s infinite steps(1); }
        .d3::after  { color: #ec4899; transform: translate(.02em, 0);  animation: gB 3s infinite steps(1); }
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
            animation: shake .6s cubic-bezier(.36, .07, .19, .97) .35s both;
        }
        @keyframes shake {
            10%, 90% { transform: translateX(-2px); }
            20%, 80% { transform: translateX(4px); }
            30%, 50%, 70% { transform: translateX(-7px); }
            40%, 60% { transform: translateX(7px); }
        }
        .pill {
            display: inline-flex; align-items: center; gap: 10px;
            padding: 5px 14px; border-radius: 9999px;
            background: rgba(236, 72, 153, .1); border: 1px solid rgba(236, 72, 153, .3);
            font-size: 11px; font-weight: 700; letter-spacing: .2em; text-transform: uppercase; color: #f9a8d4;
        }
        .pill i { width: 7px; height: 7px; border-radius: 9999px; background: #ec4899; box-shadow: 0 0 10px #ec4899; animation: dot 1.6s ease-in-out infinite; }
        @keyframes dot { 0%, 100% { opacity: .3; } 50% { opacity: 1; } }
        .title { margin-top: .8rem; font-size: clamp(1.2rem, 3vw, 1.9rem); font-weight: 800; letter-spacing: .16em; text-transform: uppercase; }
        .desc { margin: .5rem auto 0; max-width: 30rem; color: #a1a1aa; font-size: .95rem; line-height: 1.7; }

        /* Request parser terminal */
        .term {
            margin: 1rem auto 0;
            max-width: 32rem;
            text-align: left;
            border-radius: 12px;
            background: rgba(0, 0, 0, .38);
            border: 1px solid rgba(255, 255, 255, .08);
            overflow: hidden;
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: .78rem;
            line-height: 1.75;
        }
        .term-bar {
            display: flex; align-items: center; gap: 6px;
            padding: 7px 12px;
            background: rgba(255, 255, 255, .04);
            border-bottom: 1px solid rgba(255, 255, 255, .06);
        }
        .term-bar i { width: 8px; height: 8px; border-radius: 9999px; background: rgba(255, 255, 255, .16); }
        .term-bar i:first-child { background: #ec4899; box-shadow: 0 0 8px #ec4899; }
        .term-bar span { margin-inline-start: auto; font-size: .65rem; letter-spacing: .12em; text-transform: uppercase; color: #71717a; }
        .term-body { padding: 10px 14px; }
        .ln { display: block; color: #a1a1aa; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; animation: ln .45s ease both; }
        .ln:nth-child(1) { animation-delay: .5s; }
        .ln:nth-child(2) { animation-delay: 1.1s; }
        .ln:nth-child(3) { animation-delay: 1.7s; }
        .ln:nth-child(4) { animation-delay: 2.3s; }
        @keyframes ln { from { opacity: 0; transform: translateX(-8px); } }
        .ln b { color: #c084fc; font-weight: 700; }
        .ln .bad { color: #f9a8d4; background: rgba(236, 72, 153, .16); padding: 0 4px; border-radius: 4px; }
        .ln.err { color: #f9a8d4; }
        .ln.res { color: #fff; font-weight: 700; }
        .cursor { display: inline-block; width: .5em; height: 1em; margin-inline-start: 3px; vertical-align: -2px; background: #ec4899; animation: blink 1s steps(1) infinite; }
        @keyframes blink { 50% { opacity: 0; } }

        .actions { margin-top: 1.2rem; display: flex; gap: 12px; flex-wrap: wrap; justify-content: center; }
        .btn {
            display: inline-flex; align-items: center; gap: .55rem;
            padding: .8rem 1.7rem; border: 0; border-radius: 10px; cursor: pointer;
            font-weight: 800; font-size: .82rem; text-transform: uppercase; letter-spacing: .08em;
            font-family: inherit; text-decoration: none; color: #fff;
            background: linear-gradient(to right, #8b5cf6, #db2777);
            box-shadow: 0 6px 0 #4c1d95, 0 12px 30px rgba(139, 92, 246, .4);
            transition: transform .12s, box-shadow .12s;
        }
        .btn:hover { transform: translateY(2px); box-shadow: 0 4px 0 #4c1d95, 0 8px 24px rgba(139, 92, 246, .4); }
        .btn:active { transform: translateY(6px); box-shadow: 0 0 0 #4c1d95; }
        .btn svg { width: 18px; height: 18px; flex-shrink: 0; }
        .btn.ghost { background: rgba(255, 255, 255, .05); border: 1px solid rgba(255, 255, 255, .14); box-shadow: 0 6px 0 rgba(0, 0, 0, .45); }
        .btn.ghost:hover { background: rgba(255, 255, 255, .09); box-shadow: 0 4px 0 rgba(0, 0, 0, .45); }
        .btn.ghost svg { transition: transform .5s; }
        .btn.ghost:hover svg { transform: rotate(180deg); }

        @media (max-height: 820px) {
            .term { display: none; }
        }
        @media (max-height: 720px) {
            .desc { display: none; }
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

    <div class="aurora"><div class="blob b1"></div><div class="blob b2"></div></div>

    <div class="rows" aria-hidden="true">
        @for ($i = 0; $i < 4; $i++)
            <div class="row"><span>{{ str_repeat('400 ', 24) }}</span></div>
        @endfor
    </div>

    <div class="frags" aria-hidden="true">
        @php
            $frags = [
                ['{', 6, 14, 0, 'p'], ['<', 18, 72, 2, 'k'], ['%', 30, 30, 4, 'p'], ['}', 44, 84, 1, 'k'],
                ['\\', 58, 10, 3, 'p'], ['0x00', 70, 66, 5, 'k'], ['">', 82, 22, 2, 'p'], ['??', 92, 78, 4, 'k'],
                ['&', 12, 46, 6, 'k'], [';;', 76, 44, 1, 'p'],
            ];
        @endphp
        @foreach ($frags as $i => $f)
            <span class="frag {{ $f[4] === 'k' ? 'pink' : '' }}" style="left: {{ $f[1] }}%; top: {{ $f[2] }}%; animation-delay: -{{ $f[3] }}s; --r: {{ $i % 2 ? 8 : -8 }}deg">{{ $f[0] }}</span>
        @endforeach
    </div>

    <div class="vignette"></div>

    <a href="/" class="branding">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        <div class="branding-text">
            <span class="branding-title">RJ NEXUS</span>
            <span class="branding-sub">Powered by RJ CMS</span>
        </div>
    </a>

    <div class="layout">

        <div class="digits" aria-label="400">
            <div class="scan"></div>

            <div class="p" data-depth="38"><span class="digit d1">4</span></div>

            <div class="p" data-depth="-26">
                <div class="d2-wrap">
                    <span class="digit">0</span>
                    <svg class="warn-icon" viewBox="0 0 24 48" fill="url(#warnGrad)">
                        <defs>
                            <linearGradient id="warnGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0" stop-color="#f9a8d4"/>
                                <stop offset="1" stop-color="#db2777"/>
                            </linearGradient>
                        </defs>
                        <rect x="6" y="0" width="12" height="31" rx="6"/>
                        <circle cx="12" cy="41" r="6.5"/>
                    </svg>
                </div>
            </div>

            <div class="p" data-depth="52"><span class="digit d3" data-text="0">0</span></div>
        </div>

        <div class="panel">
            <span class="pill"><i></i>{{ __('Bad Request') }}</span>
            <h1 class="title">{{ __('Malformed Request') }}</h1>
            <p class="desc">{{ __('The server cannot process the request due to a client error. Please check your input and try again.') }}</p>

            <div class="term" aria-hidden="true">
                <div class="term-bar">
                    <i></i><i></i><i></i>
                    <span>request.parser</span>
                </div>
                <div class="term-body">
                    <span class="ln"><b>&gt;</b> GET <span id="rj-path">/</span> HTTP/1.1</span>
                    <span class="ln err">✗ header: invalid character <span class="bad">0x1F</span> at byte 42</span>
                    <span class="ln err">✗ parser: unexpected token <span class="bad">%</span></span>
                    <span class="ln res"><b>←</b> 400 Bad Request<span class="cursor"></span></span>
                </div>
            </div>

            <div class="actions">
                <a href="{{ url('/') }}" class="btn">
                    <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    {{ __('Back to Home') }}
                </a>
                <button class="btn ghost" onclick="window.history.back()">
                    <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    {{ __('Go Back') }}
                </button>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var pathEl = document.getElementById('rj-path');
            if (pathEl) { pathEl.textContent = window.location.pathname || '/'; }

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
        })();
    </script>
</body>
</html>
