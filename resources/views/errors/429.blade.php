<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>429 - {{ __('Too Many Requests') }}</title>
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

        /* the screen briefly flares when the shield is overwhelmed */
        .flare { position: fixed; inset: 0; z-index: 1; pointer-events: none; background: radial-gradient(circle at 50% 40%, rgba(236, 72, 153, .35), transparent 60%); opacity: 0; }
        .flare.on { animation: flare .7s ease-out; }
        @keyframes flare { 0% { opacity: 0; } 20% { opacity: 1; } 100% { opacity: 0; } }

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

        #fx { position: fixed; inset: 0; z-index: 2; width: 100%; height: 100%; pointer-events: none; }

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

        /* entrance: 4 and 9 slam in from the sides, shield pops in the middle */
        .slam-l { animation: slamL .8s cubic-bezier(.2, 1.2, .35, 1) .1s both; }
        .slam-r { animation: slamR .8s cubic-bezier(.2, 1.2, .35, 1) .1s both; }
        .pop    { animation: pop .9s cubic-bezier(.2, 1.5, .4, 1) .5s both; }
        @keyframes slamL { from { transform: translateX(-70vw) skewX(30deg); opacity: 0; filter: blur(16px); } to { transform: none; opacity: 1; filter: blur(0); } }
        @keyframes slamR { from { transform: translateX(70vw) skewX(-30deg); opacity: 0; filter: blur(16px); } to { transform: none; opacity: 1; filter: blur(0); } }
        @keyframes pop   { from { transform: scale(0) rotate(-200deg); opacity: 0; } to { transform: none; opacity: 1; } }

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

        /* ---------- Shield: the one memorable thing ---------- */
        .shield-wrap {
            width: .8em; height: .8em; position: relative; margin: 0 auto;
            filter: drop-shadow(0 0 46px rgba(168, 85, 247, .5));
            animation: float 4s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-15px) scale(1.05); }
        }
        .shield-core { width: 100%; height: 100%; }
        .shield-core svg { width: 100%; height: 100%; display: block; overflow: visible; }
        .shield-core.hit { animation: hit .22s ease-out; }
        @keyframes hit {
            0% { transform: scale(1); filter: brightness(1); }
            35% { transform: scale(.94) rotate(var(--tilt, 2deg)); filter: brightness(1.7) drop-shadow(0 0 20px #ec4899); }
            100% { transform: scale(1); filter: brightness(1); }
        }
        .shield-core.overload { animation: overload .7s ease-out; }
        @keyframes overload {
            0%, 100% { transform: translate(0, 0); filter: brightness(1); }
            10%, 50%, 90% { transform: translate(-2.5%, 1%) rotate(-3deg); filter: brightness(2) drop-shadow(0 0 30px #ec4899); }
            30%, 70% { transform: translate(2.5%, -1%) rotate(3deg); }
        }

        /* the "load" inside the shield keeps rising, then drains */
        .level { animation: level 9s ease-in-out infinite; }
        @keyframes level {
            0% { transform: translateY(21px); }
            75% { transform: translateY(3px); }
            82% { transform: translateY(3px); }
            100% { transform: translateY(21px); }
        }
        .wave { animation: wave 1.6s linear infinite; }
        @keyframes wave { to { transform: translateX(-12px); } }

        /* hexagonal energy rings */
        .halo { position: absolute; inset: -18%; border-radius: 9999px; border: 2px solid rgba(168, 85, 247, .3); pointer-events: none; animation: halo 3.2s ease-out infinite; opacity: 0; }
        .halo.h2 { animation-delay: 1.07s; border-color: rgba(236, 72, 153, .3); }
        .halo.h3 { animation-delay: 2.14s; }
        @keyframes halo { 0% { transform: scale(.7); opacity: .9; } 100% { transform: scale(1.9); opacity: 0; } }

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
            position: relative; overflow: hidden;
            align-self: center;
            width: min(760px, calc(100% - 32px));
            margin-bottom: 3vh;
            padding: 22px 28px 26px;
            text-align: center;
            background: rgba(255, 255, 255, .045);
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: 20px;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, .45);
            animation: reveal 1s 1s cubic-bezier(.2, .9, .3, 1) both;
        }
        @keyframes reveal { from { clip-path: inset(0 50% 0 50% round 20px); opacity: 0; } to { clip-path: inset(0 0 0 0 round 20px); opacity: 1; } }

        /* cooldown line along the bottom edge */
        .cool { position: absolute; left: 0; bottom: 0; height: 3px; width: 100%; background: linear-gradient(to right, var(--purple), var(--pink)); transform-origin: left; transform: scaleX(0); box-shadow: 0 0 14px var(--pink); }

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
        }
        .desc { margin: .5rem auto 0; max-width: 30rem; color: #a1a1aa; font-size: .95rem; line-height: 1.7; }

        .stats { margin-top: .9rem; display: flex; justify-content: center; gap: 22px; font-size: .78rem; font-weight: 600; color: #71717a; font-variant-numeric: tabular-nums; }
        .stats b { color: #d8b4fe; font-weight: 700; }
        .stats .warn b { color: #f9a8d4; }

        .actions { margin-top: 1.2rem; display: flex; gap: 12px; flex-wrap: wrap; justify-content: center; }
        .btn {
            position: relative; overflow: hidden;
            display: inline-flex; align-items: center; gap: .55rem;
            padding: .8rem 1.7rem; border: 0; border-radius: 10px; cursor: pointer;
            font-weight: 800; font-size: .82rem; text-transform: uppercase; letter-spacing: .08em;
            font-family: inherit; text-decoration: none; color: #fff;
            background: linear-gradient(to right, #8b5cf6, #db2777);
            box-shadow: 0 6px 0 #4c1d95, 0 12px 30px rgba(139, 92, 246, .4);
            transition: transform .12s, box-shadow .12s, opacity .3s;
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
        .btn[disabled] { opacity: .45; cursor: not-allowed; transform: none; box-shadow: 0 6px 0 #4c1d95; }
        .btn[disabled]::after { display: none; }
        .btn.ready { animation: ready .7s ease-out; }
        @keyframes ready { 0% { transform: scale(.9); } 50% { transform: scale(1.08); } 100% { transform: scale(1); } }
        .btn.ghost { background: rgba(255, 255, 255, .05); border: 1px solid rgba(255, 255, 255, .14); box-shadow: 0 6px 0 rgba(0, 0, 0, .45); }
        .btn.ghost::after { display: none; }
        .btn.ghost:hover { background: rgba(255, 255, 255, .09); box-shadow: 0 4px 0 rgba(0, 0, 0, .45); }
        .btn.ghost svg { transition: transform .5s; }
        .btn.ghost:hover svg { transform: rotate(180deg); }

        @media (max-height: 720px) {
            .desc, .stats { display: none; }
            .panel { padding: 14px 20px 18px; margin-bottom: 2vh; }
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
    <div class="flare" id="flare"></div>

    <div class="rows" aria-hidden="true">
        @for ($i = 0; $i < 4; $i++)
            <div class="row"><span>{{ str_repeat('429 ', 24) }}</span></div>
        @endfor
    </div>

    <canvas id="fx" aria-hidden="true"></canvas>
    <div class="vignette"></div>

    <a href="/" class="branding">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        <div class="branding-text">
            <span class="branding-title">RJ NEXUS</span>
            <span class="branding-sub">Powered by RJ CMS</span>
        </div>
    </a>

    <div class="layout">

        <div class="digits" aria-label="429">
            <div class="p" data-depth="38"><div class="slam-l"><span class="digit d1">4</span></div></div>

            <div class="p" data-depth="-26">
                <div class="pop">
                    <div class="shield-wrap" id="shield">
                        <span class="halo"></span><span class="halo h2"></span><span class="halo h3"></span>
                        <div class="shield-core" id="core">
                            <svg viewBox="0 0 24 24" fill="none" stroke="url(#shieldGradient)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <defs>
                                    <linearGradient id="shieldGradient" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0" stop-color="#f5d0fe"/>
                                        <stop offset=".38" stop-color="#c084fc"/>
                                        <stop offset=".62" stop-color="#a855f7"/>
                                        <stop offset="1" stop-color="#6d28d9"/>
                                    </linearGradient>
                                    <linearGradient id="loadGradient" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0" stop-color="#f9a8d4" stop-opacity=".75"/>
                                        <stop offset="1" stop-color="#be185d" stop-opacity=".5"/>
                                    </linearGradient>
                                    <clipPath id="shieldClip"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></clipPath>
                                </defs>
                                <g clip-path="url(#shieldClip)" stroke="none">
                                    <g class="level">
                                        <path class="wave" d="M0 2 Q3 0 6 2 T12 2 T18 2 T24 2 T30 2 T36 2 V30 H0Z" fill="url(#loadGradient)"/>
                                    </g>
                                </g>
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p" data-depth="52"><div class="slam-r"><span class="digit d3" data-text="9">9</span></div></div>
        </div>

        <div class="panel">
            <span class="pill"><i></i>{{ __('Too Many Requests') }}</span>
            <h1 class="title">{{ __('Rate Limit Exceeded') }}</h1>
            <p class="desc">{{ __('You have made too many requests recently. Please wait a moment before trying again.') }}</p>

            <div class="stats" aria-hidden="true">
                <span>{{ __('Blocked') }}: <b id="blocked">0</b></span>
                <span class="warn">{{ __('Cooldown') }}: <b id="cd">15</b>s</span>
            </div>

            <div class="actions">
                <button class="btn" id="retry" disabled onclick="window.location.reload()">
                    <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span id="retryText">{{ __('Try Again') }}</span>
                </button>
                <a href="{{ url('/') }}" class="btn ghost">
                    <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    {{ __('Back to Home') }}
                </a>
            </div>
            <div class="cool" id="coolbar"></div>
        </div>
    </div>

    <script>
        (function () {
            var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            /* ---- cooldown: Try Again unlocks after COOLDOWN seconds ---- */
            var COOLDOWN = 15;
            var retry = document.getElementById('retry');
            var retryText = document.getElementById('retryText');
            var label = retryText.textContent;
            var cd = document.getElementById('cd');
            var bar = document.getElementById('coolbar');
            var t0 = Date.now(), done = false;

            var timer = setInterval(function () {
                var elapsed = (Date.now() - t0) / 1000;
                var left = Math.max(0, Math.ceil(COOLDOWN - elapsed));
                bar.style.transform = 'scaleX(' + Math.min(1, elapsed / COOLDOWN) + ')';
                cd.textContent = left;
                if (!done) retryText.textContent = label + ' (' + left + 's)';
                if (left <= 0 && !done) {
                    done = true;
                    clearInterval(timer);
                    retry.disabled = false;
                    retryText.textContent = label;
                    retry.classList.add('ready');
                }
            }, 100);

            /* ---- parallax digits ---- */
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

            /* ---- request storm: packets fly in and crash into the shield ---- */
            var cv = document.getElementById('fx'), ctx = cv.getContext('2d');
            var shield = document.getElementById('shield');
            var core = document.getElementById('core');
            var flare = document.getElementById('flare');
            var blockedEl = document.getElementById('blocked');
            var W, H, dpr = Math.min(window.devicePixelRatio || 1, 2);
            var packets = [], rings = [], bits = [], blocked = 0, hits = 0;
            var colors = ['192,132,252', '244,114,182', '168,85,247', '249,168,212'];

            function resize() { W = cv.width = innerWidth * dpr; H = cv.height = innerHeight * dpr; }
            resize(); addEventListener('resize', resize);

            function center() {
                var r = shield.getBoundingClientRect();
                return { x: (r.left + r.width / 2) * dpr, y: (r.top + r.height / 2) * dpr, R: r.width * .42 * dpr };
            }

            function launch(sx, sy, speed) {
                var c = center();
                var ang = Math.atan2(sy * dpr - c.y, sx * dpr - c.x);
                packets.push({ sx: sx * dpr, sy: sy * dpr, ang: ang, t: 0, v: speed || (.008 + Math.random() * .012), trail: [], c: colors[(Math.random() * colors.length) | 0] });
            }
            function edgeSpawn(speed) {
                var side = (Math.random() * 4) | 0, w = innerWidth, h = innerHeight, x, y;
                if (side === 0) { x = Math.random() * w; y = -20; }
                else if (side === 1) { x = w + 20; y = Math.random() * h; }
                else if (side === 2) { x = Math.random() * w; y = h + 20; }
                else { x = -20; y = Math.random() * h; }
                launch(x, y, speed);
            }

            function impact(p, x, y) {
                blocked++; hits++;
                blockedEl.textContent = blocked;
                rings.push({ x: x, y: y, r: 4 * dpr, a: 1, c: p.c });
                for (var i = 0; i < 6; i++) {
                    var a = p.ang + Math.PI + (Math.random() - .5) * 2.2, s = (1.5 + Math.random() * 3) * dpr;
                    bits.push({ x: x, y: y, vx: Math.cos(a) * s, vy: Math.sin(a) * s, life: 1, c: p.c });
                }
                core.style.setProperty('--tilt', (Math.cos(p.ang) * 4) + 'deg');
                core.classList.remove('hit', 'overload'); void core.offsetWidth; core.classList.add('hit');
            }

            /* ambient trickle + a flood every few seconds */
            setInterval(function () { edgeSpawn(); }, 260);
            function flood() {
                for (var i = 0; i < 16; i++) setTimeout(edgeSpawn, i * 45);
                setTimeout(function () {
                    core.classList.remove('hit'); void core.offsetWidth; core.classList.add('overload');
                    flare.classList.remove('on'); void flare.offsetWidth; flare.classList.add('on');
                }, 800);
            }
            setTimeout(flood, 2500);
            setInterval(flood, 9000);

            /* click anywhere = fire a request at the shield */
            document.addEventListener('click', function (e) {
                if (e.target.closest('a, button')) return;
                for (var i = 0; i < 5; i++) setTimeout(function () { launch(e.clientX, e.clientY, .02); }, i * 50);
            });

            function frame() {
                ctx.clearRect(0, 0, W, H);
                var c = center();
                ctx.globalCompositeOperation = 'lighter';
                ctx.lineCap = 'round';

                for (var i = packets.length - 1; i >= 0; i--) {
                    var p = packets[i];
                    p.t += p.v * (0.5 + p.t * 1.6);           // accelerates toward the shield
                    var e = Math.min(p.t, 1);
                    var tx = c.x + Math.cos(p.ang) * c.R, ty = c.y + Math.sin(p.ang) * c.R;
                    var x = p.sx + (tx - p.sx) * e, y = p.sy + (ty - p.sy) * e;
                    p.trail.push({ x: x, y: y });
                    if (p.trail.length > 14) p.trail.shift();

                    for (var k = 1; k < p.trail.length; k++) {
                        ctx.strokeStyle = 'rgba(' + p.c + ',' + (k / p.trail.length) * .8 + ')';
                        ctx.lineWidth = (k / p.trail.length) * 3 * dpr;
                        ctx.beginPath(); ctx.moveTo(p.trail[k - 1].x, p.trail[k - 1].y); ctx.lineTo(p.trail[k].x, p.trail[k].y); ctx.stroke();
                    }
                    ctx.fillStyle = 'rgba(255,255,255,.95)';
                    ctx.beginPath(); ctx.arc(x, y, 2.2 * dpr, 0, 6.283); ctx.fill();

                    if (p.t >= 1) { impact(p, x, y); packets.splice(i, 1); }
                }

                for (var r = rings.length - 1; r >= 0; r--) {
                    var g = rings[r];
                    g.r += 3.2 * dpr; g.a -= .035;
                    if (g.a <= 0) { rings.splice(r, 1); continue; }
                    ctx.strokeStyle = 'rgba(' + g.c + ',' + g.a + ')';
                    ctx.lineWidth = 2 * dpr;
                    ctx.beginPath(); ctx.arc(g.x, g.y, g.r, 0, 6.283); ctx.stroke();
                }

                for (var b = bits.length - 1; b >= 0; b--) {
                    var s = bits[b];
                    s.x += s.vx; s.y += s.vy; s.vx *= .96; s.vy *= .96; s.life -= .03;
                    if (s.life <= 0) { bits.splice(b, 1); continue; }
                    ctx.fillStyle = 'rgba(' + s.c + ',' + s.life + ')';
                    ctx.beginPath(); ctx.arc(s.x, s.y, 2 * dpr * s.life, 0, 6.283); ctx.fill();
                }

                ctx.globalCompositeOperation = 'source-over';
                requestAnimationFrame(frame);
            }
            frame();
        })();
    </script>
</body>
</html>