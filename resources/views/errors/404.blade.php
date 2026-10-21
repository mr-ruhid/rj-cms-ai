<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - {{ __('Page Not Found') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600,700,900&display=swap" rel="stylesheet" />
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; }
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #0f0a18;
            background-image:
                linear-gradient(rgba(139, 92, 246, .05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(139, 92, 246, .05) 1px, transparent 1px),
                radial-gradient(ellipse at 50% 40%, rgba(139, 92, 246, .15), transparent 60%);
            background-size: 48px 48px, 48px 48px, 100% 100%;
            color: #fff;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: gridMove 20s linear infinite;
            cursor: none;
            user-select: none;
            -webkit-user-select: none;
        }
        @keyframes gridMove {
            to { background-position: 48px 48px, 48px 48px, 0 0; }
        }
        a, .btn { cursor: pointer; }

        /* ===== Brend ===== */
        .branding {
            position: fixed; top: 24px; right: 24px; z-index: 100;
            display: flex; align-items: center; gap: 10px;
            padding: 10px 16px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px; backdrop-filter: blur(8px);
            text-decoration: none; color: #fff; transition: all 0.3s ease;
        }
        .branding:hover { background: rgba(255, 255, 255, 0.06); border-color: rgba(139, 92, 246, 0.4); }
        .branding svg { width: 24px; height: 24px; color: #a855f7; }
        .branding-text { display: flex; flex-direction: column; text-align: left; }
        .branding-title { font-weight: 900; font-size: 14px; letter-spacing: 0.1em; background: linear-gradient(to right, #d8b4fe, #f9a8d4); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .branding-sub { font-size: 10px; color: #71717a; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }

        /* ===== Skor ===== */
        .score {
            position: fixed; top: 24px; left: 24px; z-index: 100;
            padding: 10px 16px; border-radius: 12px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(8px);
            font-size: 12px; font-weight: 600; color: #a1a1aa;
            text-transform: uppercase; letter-spacing: .06em;
        }
        .score b { display: inline-block; margin-left: 6px; font-size: 18px; font-weight: 900; color: #d8b4fe; }
        .score b.pop { animation: pop .35s ease-out; }
        @keyframes pop { 0% { transform: scale(1); } 40% { transform: scale(1.6); color: #f9a8d4; } 100% { transform: scale(1); } }

        /* ===== Səhnə ===== */
        .stage { position: relative; z-index: 2; text-align: center; padding: 0 16px; perspective: 900px; display: flex; flex-direction: column; align-items: center; }
        .tilt { transition: transform .15s ease-out; transform-style: preserve-3d; display: flex; flex-direction: column; align-items: center; }

        .digits {
            display: flex; align-items: center; justify-content: center;
            gap: clamp(.4rem, 2vw, 1.5rem);
            font-size: clamp(7rem, 24vw, 16rem);
            line-height: 1; font-weight: 900;
            padding-top: .3em;
        }
        .digit { position: relative; display: inline-block; }

        .gradient-text {
            background: linear-gradient(180deg, #d8b4fe 0%, #a855f7 55%, #6d28d9 100%);
            -webkit-background-clip: text; background-clip: text;
            -webkit-text-fill-color: transparent; color: transparent;
            filter: drop-shadow(0 8px 0 rgba(0, 0, 0, .6)) drop-shadow(0 0 30px rgba(168, 85, 247, .4));
        }

        .float { animation: float 4s ease-in-out infinite; }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        /* Glitch (ikinci 4) */
        .glitch { animation: flicker 4s infinite; }
        .glitch::before, .glitch::after {
            content: attr(data-text);
            position: absolute; left: 0; top: 0; width: 100%;
            -webkit-text-fill-color: currentColor; background: none; overflow: hidden;
        }
        .glitch::before { color: #22d3ee; transform: translate(-5px, 0); animation: glitchA 2.2s infinite steps(1); }
        .glitch::after  { color: #db2777; transform: translate(5px, 0);  animation: glitchB 2.8s infinite steps(1); }
        @keyframes glitchA {
            0%   { clip-path: inset(0 0 100% 0); }
            10%  { clip-path: inset(12% 0 70% 0); }
            20%  { clip-path: inset(60% 0 12% 0); }
            30%  { clip-path: inset(0 0 100% 0); }
            55%  { clip-path: inset(35% 0 45% 0); }
            65%  { clip-path: inset(80% 0 4% 0); }
            75%, 100% { clip-path: inset(0 0 100% 0); }
        }
        @keyframes glitchB {
            0%   { clip-path: inset(0 0 100% 0); }
            15%  { clip-path: inset(70% 0 10% 0); }
            25%  { clip-path: inset(20% 0 60% 0); }
            35%  { clip-path: inset(0 0 100% 0); }
            60%  { clip-path: inset(50% 0 30% 0); }
            70%  { clip-path: inset(5% 0 85% 0); }
            80%, 100% { clip-path: inset(0 0 100% 0); }
        }
        @keyframes flicker {
            0%, 18%, 22%, 60%, 64%, 100% { opacity: 1; transform: translate(0, 0); }
            20% { opacity: .35; transform: translate(-3px, 1px); }
            62% { opacity: .5; transform: translate(3px, -1px); }
            63% { opacity: 1; transform: translate(-2px, 0); }
        }

        /* 0: böyüdücü şüşə axtarır */
        .lens-wrap { width: .9em; height: .9em; position: relative; }
        .lens-wrap svg { width: 100%; height: 100%; display: block; overflow: visible; filter: drop-shadow(0 8px 0 rgba(0, 0, 0, .6)) drop-shadow(0 0 30px rgba(168, 85, 247, .4)); }
        .lens { transform-origin: 40% 40%; animation: search 6s ease-in-out infinite; }
        @keyframes search {
            0%   { transform: translate(0, 0) rotate(0deg); }
            15%  { transform: translate(-14px, 6px) rotate(-12deg); }
            30%  { transform: translate(16px, -8px) rotate(10deg); }
            45%  { transform: translate(-8px, -12px) rotate(-6deg) scale(1.08); }
            60%  { transform: translate(12px, 10px) rotate(14deg); }
            80%  { transform: translate(-16px, 2px) rotate(-10deg) scale(1.05); }
            100% { transform: translate(0, 0) rotate(0deg); }
        }
        .q { font-family: 'Figtree', sans-serif; font-weight: 900; animation: qblink 1.2s ease-in-out infinite; transform-origin: 38px 40px; }
        @keyframes qblink { 0%, 100% { opacity: .35; } 50% { opacity: 1; } }

        /* ===== Yazı ===== */
        .title {
            margin-top: 1.4rem;
            font-size: clamp(1.4rem, 5vw, 2.6rem);
            font-weight: 900; text-transform: uppercase; letter-spacing: .12em;
        }
        .title .ch { display: inline-block; animation: wave 1.8s ease-in-out infinite; text-shadow: 0 0 18px rgba(168, 85, 247, .5); }
        .title .sp { display: inline-block; width: .5em; }
        @keyframes wave {
            0%, 100% { transform: translateY(0); color: #fff; }
            30% { transform: translateY(-12px); color: #d8b4fe; }
            60% { transform: translateY(0); color: #fff; }
        }

        .desc { margin: 1rem auto 0; max-width: 34rem; color: #a1a1aa; font-size: .95rem; line-height: 1.7; }
        .hint { margin-top: .9rem; font-size: .8rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: #f9a8d4; animation: hint 1.6s ease-in-out infinite; }
        @keyframes hint { 0%, 100% { opacity: .45; } 50% { opacity: 1; } }

        .btn {
            margin-top: 1.6rem; display: inline-flex; align-items: center; gap: .5rem;
            padding: .8rem 2rem; border: 0; border-radius: 10px;
            background: linear-gradient(to right, #8b5cf6, #db2777); color: #fff; font-weight: 900; font-size: .95rem;
            text-transform: uppercase; letter-spacing: .08em; font-family: inherit; text-decoration: none;
            box-shadow: 0 6px 0 #4c1d95, 0 12px 30px rgba(139, 92, 246, .4);
            transition: transform .12s, box-shadow .12s;
        }
        .btn:hover { transform: translateY(2px); box-shadow: 0 4px 0 #4c1d95, 0 8px 24px rgba(139, 92, 246, .4); }
        .btn:active { transform: translateY(6px); box-shadow: 0 0 0 #4c1d95; }
        .btn svg { width: 18px; height: 18px; transition: transform .5s; }
        .btn:hover svg { transform: translateX(-4px); }

        /* ===== İtmiş səhifələr ===== */
        .lost {
            position: fixed; left: 0; top: 0; z-index: 4;
            width: 54px; height: 66px;
            will-change: transform;
            filter: drop-shadow(0 0 14px rgba(168, 85, 247, .6));
        }
        .lost svg { width: 100%; height: 100%; display: block; pointer-events: none; }
        .lost .eye { animation: look 2.4s ease-in-out infinite; }
        @keyframes look { 0%, 100% { transform: translateX(-1.5px); } 50% { transform: translateX(1.5px); } }

        /* ===== Nişangah ===== */
        .crosshair {
            position: fixed; left: 0; top: 0; z-index: 9999;
            width: 44px; height: 44px; margin: -22px 0 0 -22px;
            pointer-events: none; opacity: 0;
            transition: opacity .2s, scale .08s;
            will-change: transform;
        }
        .crosshair.on { opacity: 1; }
        .crosshair.shoot { scale: .75; }
        .crosshair svg { width: 100%; height: 100%; display: block; filter: drop-shadow(0 0 8px rgba(219, 39, 119, .8)); }

        /* Güllə izi */
        .hole {
            position: fixed; z-index: 3; width: 18px; height: 18px; margin: -9px 0 0 -9px;
            border-radius: 9999px; pointer-events: none;
            background: radial-gradient(circle, #000 0 30%, rgba(168, 85, 247, .55) 45%, transparent 70%);
            animation: holeFade 4s ease-out forwards;
        }
        @keyframes holeFade { 0% { transform: scale(1.8); opacity: 1; } 10% { transform: scale(1); } 80% { opacity: .8; } 100% { opacity: 0; } }

        /* Partlayış hissəcikləri */
        .particle {
            position: fixed; z-index: 6; width: 8px; height: 8px; margin: -4px 0 0 -4px;
            border-radius: 2px; pointer-events: none;
            animation: burst .7s ease-out forwards;
        }
        @keyframes burst {
            0%   { transform: translate(0, 0) rotate(0) scale(1); opacity: 1; }
            100% { transform: translate(var(--x), var(--y)) rotate(var(--r)) scale(0); opacity: 0; }
        }
        .plus {
            position: fixed; z-index: 7; pointer-events: none; font-weight: 900; font-size: 22px;
            color: #f9a8d4; text-shadow: 0 0 12px rgba(219, 39, 119, .8);
            animation: plus .9s ease-out forwards;
        }
        @keyframes plus { 0% { transform: translateY(0); opacity: 1; } 100% { transform: translateY(-50px); opacity: 0; } }

        @media (max-height: 760px) {
            .desc { display: none; }
            .branding { top: 10px; right: 10px; padding: 6px 10px; }
            .branding svg { width: 16px; height: 16px; }
            .branding-title { font-size: 12px; }
            .branding-sub { font-size: 8px; }
            .score { top: 10px; left: 10px; padding: 6px 10px; }
        }
        @media (hover: none) { body { cursor: auto; } }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
        }
    </style>
</head>
<body>

    <a href="/" class="branding">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        <div class="branding-text">
            <span class="branding-title">RJ NEXUS</span>
            <span class="branding-sub">Powered by RJ CMS</span>
        </div>
    </a>

    <div class="score">{{ __('Lost pages found') }}<b id="score">0</b></div>

    <div class="stage">
        <div class="tilt" id="tilt">

            <div class="digits" aria-label="404">
                <div class="digit float" style="animation-delay: 0s;"><span class="gradient-text">4</span></div>

                {{-- 0: böyüdücü şüşə --}}
                <div class="digit float" style="animation-delay: 0.2s;">
                    <div class="lens-wrap">
                        <svg viewBox="0 0 100 100" fill="none">
                            <defs>
                                <linearGradient id="lg" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0" stop-color="#d8b4fe"/>
                                    <stop offset=".55" stop-color="#a855f7"/>
                                    <stop offset="1" stop-color="#6d28d9"/>
                                </linearGradient>
                            </defs>
                            <g class="lens">
                                <circle cx="40" cy="40" r="28" fill="rgba(168,85,247,.12)" stroke="url(#lg)" stroke-width="10"/>
                                <path d="M24 28 A20 20 0 0 1 36 19" stroke="#fff" stroke-opacity=".6" stroke-width="4" stroke-linecap="round"/>
                                <text class="q" x="40" y="54" text-anchor="middle" font-size="40" fill="#f9a8d4">?</text>
                                <line x1="61" y1="61" x2="88" y2="88" stroke="url(#lg)" stroke-width="12" stroke-linecap="round"/>
                            </g>
                        </svg>
                    </div>
                </div>

                <div class="digit float glitch" data-text="4" style="animation-delay: 0.4s;"><span class="gradient-text">4</span></div>
            </div>

            <h1 class="title" id="title" data-text="{{ __('Page Not Found') }}">{{ __('Page Not Found') }}</h1>

            <p class="desc">
                {{ __('Sorry, the page you are looking for could not be found. It might have been removed, renamed, or is temporarily unavailable.') }}
            </p>

            <p class="hint">{{ __('Shoot the lost pages!') }}</p>

            <a href="{{ url('/') }}" class="btn">
                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('Back to Home') }}
            </a>
        </div>
    </div>

    {{-- Nişangah --}}
    <div class="crosshair" id="cross">
        <svg viewBox="0 0 44 44" fill="none" stroke="#f9a8d4" stroke-width="2" stroke-linecap="round">
            <circle cx="22" cy="22" r="12"/>
            <line x1="22" y1="2" x2="22" y2="12"/>
            <line x1="22" y1="32" x2="22" y2="42"/>
            <line x1="2" y1="22" x2="12" y2="22"/>
            <line x1="32" y1="22" x2="42" y2="22"/>
            <circle cx="22" cy="22" r="1.5" fill="#f9a8d4"/>
        </svg>
    </div>

    <script>
        // Başlıq hərfləri dalğalansın
        (function () {
            var el = document.getElementById('title');
            var text = el.dataset.text;
            el.textContent = '';
            Array.from(text).forEach(function (c, i) {
                var s = document.createElement('span');
                if (c === ' ') { s.className = 'sp'; }
                else {
                    s.className = 'ch';
                    s.textContent = c;
                    s.style.animationDelay = (i * 0.09) + 's';
                }
                el.appendChild(s);
            });
        })();

        // 3D əyilmə + nişangah
        (function () {
            var t = document.getElementById('tilt');
            var cross = document.getElementById('cross');
            document.addEventListener('mousemove', function (e) {
                var x = e.clientX / window.innerWidth - 0.5;
                var y = e.clientY / window.innerHeight - 0.5;
                t.style.transform = 'rotateY(' + (x * 14) + 'deg) rotateX(' + (-y * 10) + 'deg)';
                cross.classList.add('on');
                cross.style.transform = 'translate(' + e.clientX + 'px,' + e.clientY + 'px)';
            });
            document.addEventListener('mouseleave', function () {
                t.style.transform = '';
                cross.classList.remove('on');
            });
        })();

        // Oyun: itmiş səhifələri vur
        (function () {
            var scoreEl = document.getElementById('score');
            var cross = document.getElementById('cross');
            var score = 0;
            var colors = ['#d8b4fe', '#a855f7', '#f9a8d4', '#db2777', '#22d3ee'];
            var lost = [];

            var pageSvg =
                '<svg viewBox="0 0 54 66" fill="none">' +
                '<path d="M6 4 H34 L48 18 V62 H6 Z" fill="#1c1030" stroke="#c084fc" stroke-width="3" stroke-linejoin="round"/>' +
                '<path d="M34 4 V18 H48" stroke="#c084fc" stroke-width="3" stroke-linejoin="round"/>' +
                '<g class="eye"><circle cx="20" cy="36" r="5" fill="#fff"/><circle cx="36" cy="36" r="5" fill="#fff"/>' +
                '<circle cx="21" cy="36" r="2.2" fill="#6d28d9"/><circle cx="37" cy="36" r="2.2" fill="#6d28d9"/></g>' +
                '<path d="M20 48 Q28 54 36 48" stroke="#f9a8d4" stroke-width="3" stroke-linecap="round"/>' +
                '</svg>';

            function spawn() {
                if (document.hidden || lost.length > 6) return;
                var el = document.createElement('div');
                el.className = 'lost';
                el.innerHTML = pageSvg;

                var fromLeft = Math.random() > 0.5;
                var w = window.innerWidth, h = window.innerHeight;
                var y0 = 80 + Math.random() * (h - 200);
                var speed = (w + 200) / (6 + Math.random() * 5) / 60;
                var o = {
                    el: el,
                    x: fromLeft ? -80 : w + 30,
                    y: y0,
                    base: y0,
                    vx: fromLeft ? speed : -speed,
                    amp: 20 + Math.random() * 50,
                    ph: Math.random() * 6,
                    rot: 0,
                    vr: (Math.random() - 0.5) * 2
                };
                lost.push(o);
                document.body.appendChild(el);
            }

            function frame() {
                for (var i = lost.length - 1; i >= 0; i--) {
                    var o = lost[i];
                    o.x += o.vx;
                    o.ph += 0.04;
                    o.y = o.base + Math.sin(o.ph) * o.amp;
                    o.rot += o.vr;
                    o.el.style.transform = 'translate(' + o.x + 'px,' + o.y + 'px) rotate(' + o.rot + 'deg)';
                    if (o.x < -120 || o.x > window.innerWidth + 120) {
                        o.el.remove();
                        lost.splice(i, 1);
                    }
                }
                requestAnimationFrame(frame);
            }

            function burst(x, y) {
                for (var i = 0; i < 14; i++) {
                    var p = document.createElement('i');
                    p.className = 'particle';
                    var a = Math.random() * Math.PI * 2;
                    var d = 40 + Math.random() * 80;
                    p.style.left = x + 'px';
                    p.style.top = y + 'px';
                    p.style.background = colors[i % colors.length];
                    p.style.setProperty('--x', Math.cos(a) * d + 'px');
                    p.style.setProperty('--y', Math.sin(a) * d + 'px');
                    p.style.setProperty('--r', (Math.random() * 540 - 270) + 'deg');
                    document.body.appendChild(p);
                    setTimeout(function (n) { n.remove(); }, 750, p);
                }
                var plus = document.createElement('div');
                plus.className = 'plus';
                plus.textContent = '+1';
                plus.style.left = x + 'px';
                plus.style.top = y + 'px';
                document.body.appendChild(plus);
                setTimeout(function () { plus.remove(); }, 950);
            }

            document.addEventListener('mousedown', function (e) {
                if (e.target.closest('a, .btn')) return;

                cross.classList.add('shoot');
                setTimeout(function () { cross.classList.remove('shoot'); }, 90);

                var hole = document.createElement('div');
                hole.className = 'hole';
                hole.style.left = e.clientX + 'px';
                hole.style.top = e.clientY + 'px';
                document.body.appendChild(hole);
                setTimeout(function () { hole.remove(); }, 4100);

                var hit = e.target.closest('.lost');
                if (hit) {
                    for (var i = 0; i < lost.length; i++) {
                        if (lost[i].el === hit) {
                            lost.splice(i, 1);
                            break;
                        }
                    }
                    hit.remove();
                    burst(e.clientX, e.clientY);
                    score++;
                    scoreEl.textContent = score;
                    scoreEl.classList.remove('pop');
                    void scoreEl.offsetWidth;
                    scoreEl.classList.add('pop');
                }
            });

            setInterval(spawn, 1600);
            spawn();
            requestAnimationFrame(frame);
        })();
    </script>
</body>
</html>