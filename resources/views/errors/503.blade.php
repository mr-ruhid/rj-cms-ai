<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>503 - {{ __('Maintenance Mode') }}</title>
    <link href="https://fonts.bunny.net/css?family=figtree:400,600,700,900&display=swap" rel="stylesheet" />
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; }
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #09090f;
            background-image:
                linear-gradient(rgba(250, 204, 21, .04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(250, 204, 21, .04) 1px, transparent 1px),
                radial-gradient(ellipse at 50% 40%, rgba(250, 204, 21, .10), transparent 60%);
            background-size: 48px 48px, 48px 48px, 100% 100%;
            color: #fff;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: gridMove 20s linear infinite;
        }
        @keyframes gridMove {
            to { background-position: 48px 48px, 48px 48px, 0 0; }
        }

        /* ===== Xəbərdarlıq lentləri ===== */
        .tape {
            position: fixed; left: -5%; width: 110%;
            background: #facc15; color: #111;
            font-weight: 900; letter-spacing: .25em; text-transform: uppercase;
            font-size: 14px; white-space: nowrap; overflow: hidden;
            padding: 16px 0;
            box-shadow: 0 10px 40px rgba(0, 0, 0, .6);
            z-index: 5;
        }
        .tape::before, .tape::after {
            content: ''; position: absolute; left: 0; right: 0; height: 7px;
            background: repeating-linear-gradient(-45deg, #111 0 12px, #facc15 12px 24px);
        }
        .tape::before { top: 0; }
        .tape::after { bottom: 0; }
        .tape-top { top: 38px; transform: rotate(-2deg); }
        .tape-bottom { bottom: 38px; transform: rotate(2deg); }
        .tape-inner { display: inline-block; animation: marquee 22s linear infinite; }
        .tape-bottom .tape-inner { animation-direction: reverse; }
        @keyframes marquee { to { transform: translateX(-50%); } }

        /* ===== Səhnə ===== */
        .stage { position: relative; z-index: 2; text-align: center; padding: 0 16px; perspective: 900px; }
        .tilt { transition: transform .15s ease-out; transform-style: preserve-3d; }

        .digits {
            display: flex; align-items: center; justify-content: center;
            gap: clamp(.4rem, 2vw, 1.5rem);
            font-size: clamp(7rem, 24vw, 16rem);
            line-height: 1; font-weight: 900;
            padding-top: .6em;
        }
        .digit { position: relative; display: inline-block; }

        .gradient-text {
            background: linear-gradient(180deg, #fde047 0%, #f59e0b 55%, #b45309 100%);
            -webkit-background-clip: text; background-clip: text;
            -webkit-text-fill-color: transparent; color: transparent;
            filter: drop-shadow(0 8px 0 rgba(0, 0, 0, .55)) drop-shadow(0 0 30px rgba(245, 158, 11, .35));
        }

        /* 5: iplərdən asılıb yellənir */
        .swing {
            transform-origin: 50% -0.55em;
            animation: swing 3.4s ease-in-out infinite alternate;
        }
        .swing::before, .swing::after {
            content: ''; position: absolute; top: -.55em; width: 4px; height: .55em;
            background: repeating-linear-gradient(180deg, #a1a1aa 0 6px, #52525b 6px 12px);
            border-radius: 2px;
        }
        .swing::before { left: 22%; }
        .swing::after { right: 22%; }
        @keyframes swing {
            from { transform: rotate(-9deg); }
            to   { transform: rotate(9deg); }
        }

        /* 0: dişli çarx */
        .gear-wrap { width: .86em; height: .86em; position: relative; }
        .gear-wrap svg { width: 100%; height: 100%; display: block; filter: drop-shadow(0 8px 0 rgba(0, 0, 0, .55)) drop-shadow(0 0 30px rgba(245, 158, 11, .35)); }
        .gear { transform-origin: 50% 50%; animation: spin 8s linear infinite; }
        .gear-inner { transform-origin: 50% 50%; animation: spin 5s linear infinite reverse; }
        @keyframes spin { to { transform: rotate(360deg); } }

        .sparks { position: absolute; inset: 0; pointer-events: none; }
        .spark {
            position: absolute; left: 50%; top: 50%; width: 5px; height: 5px;
            border-radius: 9999px; background: #fde047;
            box-shadow: 0 0 10px 2px #f59e0b;
            opacity: 0;
            animation: spark 1.3s ease-out infinite;
        }
        @keyframes spark {
            0%   { transform: translate(0, 0) scale(1); opacity: 1; }
            100% { transform: translate(var(--x), var(--y)) scale(0); opacity: 0; }
        }

        /* 3: glitch + xarab neon */
        .glitch { animation: flicker 4s infinite; }
        .glitch::before, .glitch::after {
            content: attr(data-text);
            position: absolute; left: 0; top: 0; width: 100%;
            -webkit-text-fill-color: currentColor;
            background: none;
            overflow: hidden;
        }
        .glitch::before { color: #22d3ee; transform: translate(-5px, 0); animation: glitchA 2.2s infinite steps(1); }
        .glitch::after  { color: #ec4899; transform: translate(5px, 0);  animation: glitchB 2.8s infinite steps(1); }
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

        /* ===== Yazı ===== */
        .title {
            margin-top: 1.4rem;
            font-size: clamp(1.4rem, 5vw, 2.6rem);
            font-weight: 900; text-transform: uppercase; letter-spacing: .12em;
        }
        .title .ch {
            display: inline-block;
            animation: wave 1.8s ease-in-out infinite;
            text-shadow: 0 0 18px rgba(250, 204, 21, .35);
        }
        .title .sp { display: inline-block; width: .5em; }
        @keyframes wave {
            0%, 100% { transform: translateY(0); color: #fff; }
            30% { transform: translateY(-12px); color: #facc15; }
            60% { transform: translateY(0); color: #fff; }
        }

        .desc { margin: 1rem auto 0; max-width: 34rem; color: #a1a1aa; font-size: .95rem; line-height: 1.7; }

        .terminal {
            margin: 1.4rem auto 0; max-width: 26rem;
            background: rgba(255, 255, 255, .04);
            border: 1px solid rgba(250, 204, 21, .25);
            border-radius: 10px; padding: 12px 16px; text-align: left;
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: 13px; color: #facc15; min-height: 46px;
        }
        .terminal .prompt { color: #71717a; }
        .cursor { display: inline-block; width: 8px; height: 15px; background: #facc15; vertical-align: -2px; margin-left: 2px; animation: blink 1s steps(1) infinite; }
        @keyframes blink { 50% { opacity: 0; } }

        .progress { margin: 1.2rem auto 0; max-width: 26rem; height: 8px; border-radius: 9999px; overflow: hidden; background: repeating-linear-gradient(-45deg, #27272a 0 10px, #18181b 10px 20px); border: 1px solid #3f3f46; }
        .progress i { display: block; height: 100%; width: 0; background: repeating-linear-gradient(-45deg, #facc15 0 10px, #111 10px 20px); background-size: 28px 28px; animation: stripes .6s linear infinite; transition: width 1s linear; }
        @keyframes stripes { to { background-position: 28px 0; } }

        .count { margin-top: .7rem; font-size: .75rem; color: #71717a; }
        .count b { color: #facc15; }

        .btn {
            margin-top: 1.3rem; display: inline-flex; align-items: center; gap: .5rem;
            padding: .8rem 2rem; border: 0; border-radius: 10px; cursor: pointer;
            background: #facc15; color: #111; font-weight: 900; font-size: .95rem;
            text-transform: uppercase; letter-spacing: .08em; font-family: inherit;
            box-shadow: 0 6px 0 #a16207, 0 12px 30px rgba(250, 204, 21, .25);
            transition: transform .12s, box-shadow .12s;
        }
        .btn:hover { transform: translateY(2px); box-shadow: 0 4px 0 #a16207, 0 8px 24px rgba(250, 204, 21, .25); }
        .btn:active { transform: translateY(6px); box-shadow: 0 0 0 #a16207; }
        .btn svg { width: 18px; height: 18px; transition: transform .5s; }
        .btn:hover svg { transform: rotate(180deg); }

        @media (max-height: 760px) {
            .tape-top { top: 10px; }
            .tape-bottom { bottom: 10px; }
            .desc { display: none; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
        }
    </style>
</head>
<body>

    @php
        $terminalLines = [
            __('Updating database...'),
            __('Clearing cache...'),
            __('Optimizing assets...'),
            __('Tightening the bolts...'),
            __('Almost there...'),
        ];
    @endphp

    <div class="tape tape-top">
        <div class="tape-inner" data-repeat="{{ __('Maintenance Mode') }} &nbsp;///&nbsp; {{ __('Under Construction') }} &nbsp;///&nbsp; "></div>
    </div>
    <div class="tape tape-bottom">
        <div class="tape-inner" data-repeat="{{ __('Be Right Back') }} &nbsp;///&nbsp; {{ __('Maintenance Mode') }} &nbsp;///&nbsp; "></div>
    </div>

    <div class="stage">
        <div class="tilt" id="tilt">

            <div class="digits" aria-label="503">
                {{-- 5 --}}
                <div class="digit swing"><span class="gradient-text">5</span></div>

                {{-- 0: dişli çarx --}}
                <div class="digit">
                    <div class="gear-wrap">
                        <svg viewBox="0 0 100 100" fill="none">
                            <defs>
                                <linearGradient id="g" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0" stop-color="#fde047"/>
                                    <stop offset=".55" stop-color="#f59e0b"/>
                                    <stop offset="1" stop-color="#b45309"/>
                                </linearGradient>
                            </defs>
                            <g class="gear">
                                <circle cx="50" cy="50" r="43" stroke="url(#g)" stroke-width="12" stroke-dasharray="9.5 9.5" />
                                <circle cx="50" cy="50" r="33" stroke="url(#g)" stroke-width="14" />
                            </g>
                            <g class="gear-inner">
                                <circle cx="50" cy="50" r="19" stroke="#f59e0b" stroke-opacity=".35" stroke-width="2" stroke-dasharray="4 6" />
                            </g>
                        </svg>
                        <div class="sparks" id="sparks"></div>
                    </div>
                </div>

                {{-- 3 --}}
                <div class="digit glitch" data-text="3"><span class="gradient-text">3</span></div>
            </div>

            <h1 class="title" id="title" data-text="{{ __('Maintenance Mode') }}">{{ __('Maintenance Mode') }}</h1>

            <p class="desc">
                {{ __('We are currently performing scheduled maintenance. We should be back shortly. Thank you for your patience.') }}
            </p>

            <div class="terminal">
                <span class="prompt">&gt;</span> <span id="typed"></span><span class="cursor"></span>
            </div>

            <div class="progress"><i id="bar"></i></div>
            <p class="count">{{ __('Auto refresh in') }} <b id="count">30</b>s</p>

            <button class="btn" onclick="window.location.reload()">
                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                {{ __('Refresh Page') }}
            </button>
        </div>
    </div>

    <script>
        // Lent yazıları
        document.querySelectorAll('.tape-inner').forEach(function (el) {
            var t = el.getAttribute('data-repeat');
            var one = '';
            for (var i = 0; i < 8; i++) one += t;
            el.innerHTML = one + one;
        });

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

        // Qığılcımlar
        (function () {
            var box = document.getElementById('sparks');
            for (var i = 0; i < 14; i++) {
                var s = document.createElement('i');
                s.className = 'spark';
                var a = Math.random() * Math.PI * 2;
                var d = 70 + Math.random() * 90;
                s.style.setProperty('--x', Math.cos(a) * d + 'px');
                s.style.setProperty('--y', Math.sin(a) * d + 'px');
                s.style.animationDelay = (Math.random() * 1.3) + 's';
                s.style.animationDuration = (0.8 + Math.random() * 0.9) + 's';
                box.appendChild(s);
            }
        })();

        // Terminal yazı effekti
        (function () {
            var lines = @json($terminalLines);
            var el = document.getElementById('typed');
            var li = 0, ci = 0, del = false;
            function tick() {
                var cur = lines[li];
                if (!del) {
                    el.textContent = cur.slice(0, ++ci);
                    if (ci === cur.length) { del = true; return setTimeout(tick, 1400); }
                    return setTimeout(tick, 55);
                }
                el.textContent = cur.slice(0, --ci);
                if (ci === 0) { del = false; li = (li + 1) % lines.length; return setTimeout(tick, 300); }
                setTimeout(tick, 25);
            }
            tick();
        })();

        // Mouse ilə 3D əyilmə
        (function () {
            var t = document.getElementById('tilt');
            document.addEventListener('mousemove', function (e) {
                var x = e.clientX / window.innerWidth - 0.5;
                var y = e.clientY / window.innerHeight - 0.5;
                t.style.transform = 'rotateY(' + (x * 14) + 'deg) rotateX(' + (-y * 10) + 'deg)';
            });
            document.addEventListener('mouseleave', function () { t.style.transform = ''; });
        })();

        // Geri sayım + avtomatik yenilənmə
        (function () {
            var total = 30, left = total;
            var c = document.getElementById('count');
            var bar = document.getElementById('bar');
            setInterval(function () {
                left--;
                if (left <= 0) { window.location.reload(); return; }
                c.textContent = left;
                bar.style.width = ((total - left) / total * 100) + '%';
            }, 1000);
        })();
    </script>
</body>
</html>