<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - {{ __('Access Denied') }}</title>
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
            user-select: none;
            -webkit-user-select: none;
        }
        @keyframes gridMove { to { background-position: 48px 48px, 48px 48px, 0 0; } }

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

        /* ===== Səhnə ===== */
        .stage { position: relative; z-index: 2; text-align: center; padding: 0 16px; display: flex; flex-direction: column; align-items: center; }

        .digits {
            display: flex; align-items: center; justify-content: center;
            gap: clamp(.6rem, 3vw, 2.5rem);
            font-size: clamp(7rem, 22vw, 15rem);
            line-height: 1; font-weight: 900;
        }
        .digit { position: relative; display: inline-block; transition: transform .2s ease-out; }
        .gradient-text {
            background: linear-gradient(180deg, #d8b4fe 0%, #a855f7 55%, #6d28d9 100%);
            -webkit-background-clip: text; background-clip: text;
            -webkit-text-fill-color: transparent; color: transparent;
            filter: drop-shadow(0 8px 0 rgba(0, 0, 0, .6)) drop-shadow(0 0 30px rgba(168, 85, 247, .4));
        }

        /* ===== Böyük qıfıl ===== */
        .lock-box { position: relative; width: .95em; height: .95em; cursor: pointer; }
        .lock-box svg { position: relative; z-index: 2; width: 100%; height: 100%; overflow: visible; display: block; filter: drop-shadow(0 8px 0 rgba(0, 0, 0, .6)); }
        .lock { transform-origin: 50% 90%; animation: rattle 2.6s ease-in-out infinite; }
        .lock-box.hit .lock { animation: slam .6s ease-out; }
        .lock-box.hit .body { fill: #be123c; }

        @keyframes rattle {
            0%, 55%, 100% { transform: rotate(0); }
            58% { transform: rotate(-9deg); }
            62% { transform: rotate(9deg); }
            66% { transform: rotate(-7deg); }
            70% { transform: rotate(7deg); }
            74% { transform: rotate(-4deg); }
            78% { transform: rotate(3deg); }
            82% { transform: rotate(0); }
        }
        @keyframes slam {
            0%, 100% { transform: rotate(0) scale(1); }
            15% { transform: rotate(-16deg) scale(1.12); }
            30% { transform: rotate(14deg) scale(1.12); }
            45% { transform: rotate(-12deg) scale(1.08); }
            60% { transform: rotate(9deg); }
            80% { transform: rotate(-4deg); }
        }
        .shackle { transform-origin: 50% 100%; animation: jiggle 2.6s ease-in-out infinite; }
        @keyframes jiggle {
            0%, 55%, 100% { transform: translateY(0); }
            60% { transform: translateY(-5px); }
            66% { transform: translateY(0); }
            72% { transform: translateY(-3px); }
            78% { transform: translateY(0); }
        }
        .keyhole-pulse { animation: kp 1.2s ease-in-out infinite; transform-origin: 50px 66px; }
        @keyframes kp { 0%, 100% { opacity: .5; transform: scale(1); } 50% { opacity: 1; transform: scale(1.25); } }

        /* Radar dalğaları */
        .pulse {
            position: absolute; left: 50%; top: 50%; z-index: 1;
            width: 100%; height: 100%; margin: -50% 0 0 -50%;
            border-radius: 9999px; border: 3px solid #db2777;
            opacity: 0; pointer-events: none;
            animation: radar 2.8s ease-out infinite;
        }
        .pulse:nth-child(2) { animation-delay: .93s; }
        .pulse:nth-child(3) { animation-delay: 1.86s; }
        @keyframes radar {
            0% { transform: scale(.8); opacity: .9; }
            100% { transform: scale(3.2); opacity: 0; }
        }

        /* ===== Yazı ===== */
        .title {
            margin-top: 2rem;
            font-size: clamp(1.6rem, 5.5vw, 3rem);
            font-weight: 900; text-transform: uppercase; letter-spacing: .12em;
            color: #fff; text-shadow: 0 0 24px rgba(219, 39, 119, .55);
            transition: color .15s;
        }
        .title.flash { animation: flash .6s steps(1); }
        @keyframes flash { 0%, 40%, 80% { color: #db2777; } 20%, 60%, 100% { color: #fff; } }

        .desc { margin: 1rem auto 0; max-width: 32rem; color: #a1a1aa; font-size: 1rem; line-height: 1.7; }
        .hint { margin-top: 1rem; font-size: .8rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: #f9a8d4; animation: hint 1.6s ease-in-out infinite; }
        @keyframes hint { 0%, 100% { opacity: .4; } 50% { opacity: 1; } }

        .btn {
            margin-top: 1.8rem; display: inline-flex; align-items: center; gap: .5rem;
            padding: .8rem 2rem; border: 0; border-radius: 10px; cursor: pointer;
            background: linear-gradient(to right, #8b5cf6, #db2777); color: #fff; font-weight: 900; font-size: .95rem;
            text-transform: uppercase; letter-spacing: .08em; font-family: inherit; text-decoration: none;
            box-shadow: 0 6px 0 #4c1d95, 0 12px 30px rgba(139, 92, 246, .4);
            transition: transform .12s, box-shadow .12s;
        }
        .btn:hover { transform: translateY(2px); box-shadow: 0 4px 0 #4c1d95, 0 8px 24px rgba(139, 92, 246, .4); }
        .btn:active { transform: translateY(6px); box-shadow: 0 0 0 #4c1d95; }
        .btn svg { width: 18px; height: 18px; transition: transform .5s; }
        .btn:hover svg { transform: translateX(-4px); }

        @media (max-height: 700px) {
            .desc { display: none; }
            .hint { margin-top: .6rem; }
            .title { margin-top: 1.2rem; }
            .btn { margin-top: 1rem; padding: .6rem 1.6rem; }
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

    <a href="/" class="branding">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        <div class="branding-text">
            <span class="branding-title">RJ NEXUS</span>
            <span class="branding-sub">Powered by RJ CMS</span>
        </div>
    </a>

    <div class="stage">

        <div class="digits" aria-label="403">
            <div class="digit" id="d1"><span class="gradient-text">4</span></div>

            <div class="digit">
                <div class="lock-box" id="lock">
                    <span class="pulse"></span>
                    <span class="pulse"></span>
                    <span class="pulse"></span>
                    <svg viewBox="0 0 100 100" fill="none">
                        <defs>
                            <linearGradient id="lg" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0" stop-color="#d8b4fe"/>
                                <stop offset=".55" stop-color="#a855f7"/>
                                <stop offset="1" stop-color="#6d28d9"/>
                            </linearGradient>
                            <filter id="glow"><feDropShadow dx="0" dy="0" stdDeviation="4" flood-color="#a855f7" flood-opacity=".6"/></filter>
                        </defs>
                        <g class="lock" filter="url(#glow)">
                            <path class="shackle" d="M30 46 V32 a20 20 0 0 1 40 0 V46" stroke="url(#lg)" stroke-width="10" stroke-linecap="round"/>
                            <rect class="body" x="16" y="44" width="68" height="48" rx="10" fill="url(#lg)"/>
                            <circle class="keyhole-pulse" cx="50" cy="64" r="7" fill="#0f0a18"/>
                            <rect x="47" y="66" width="6" height="14" rx="2" fill="#0f0a18"/>
                        </g>
                    </svg>
                </div>
            </div>

            <div class="digit" id="d3"><span class="gradient-text">3</span></div>
        </div>

        <h1 class="title" id="title">{{ __('Access Denied') }}</h1>

        <p class="desc">
            {{ __('You do not have the necessary permissions to view this page. Please contact the administrator if you believe this is a mistake.') }}
        </p>

        <p class="hint">{{ __('Try to open the lock') }}</p>

        <a href="{{ url('/') }}" class="btn">
            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            {{ __('Back to Home') }}
        </a>
    </div>

    <script>
        (function () {
            var lock = document.getElementById('lock');
            var title = document.getElementById('title');
            var d1 = document.getElementById('d1');
            var d3 = document.getElementById('d3');

            // 4 və 3 qıfıla baxır kimi əyilir
            document.addEventListener('mousemove', function (e) {
                var r = lock.getBoundingClientRect();
                var cx = r.left + r.width / 2;
                var cy = r.top + r.height / 2;
                var dx = (e.clientX - cx) / window.innerWidth;
                var dy = (e.clientY - cy) / window.innerHeight;
                d1.style.transform = 'rotate(' + (dx * 14) + 'deg) translate(' + (dx * 14) + 'px,' + (dy * 14) + 'px)';
                d3.style.transform = 'rotate(' + (dx * 14) + 'deg) translate(' + (dx * 14) + 'px,' + (dy * 14) + 'px)';
            });

            // Klik: qıfıl bərk silkələnir
            lock.addEventListener('click', function () {
                lock.classList.remove('hit');
                title.classList.remove('flash');
                void lock.offsetWidth;
                lock.classList.add('hit');
                title.classList.add('flash');
            });
            lock.addEventListener('animationend', function (e) {
                if (e.animationName === 'slam') lock.classList.remove('hit');
            });
        })();
    </script>
</body>
</html>