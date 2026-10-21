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

        /* ===== Siqnal işıqları ===== */
        .alarm {
            position: fixed; top: -140px; width: 320px; height: 320px; border-radius: 9999px;
            background: radial-gradient(circle, rgba(219, 39, 119, .55), transparent 65%);
            filter: blur(10px); pointer-events: none; z-index: 1;
            animation: alarm 2.4s ease-in-out infinite;
        }
        .alarm-l { left: -120px; }
        .alarm-r { right: -120px; animation-delay: 1.2s; }
        @keyframes alarm { 0%, 100% { opacity: .15; } 50% { opacity: .9; } }
        body.panic .alarm { animation-duration: .5s; }
        body.panic::after {
            content: ''; position: fixed; inset: 0; z-index: 50; pointer-events: none;
            background: rgba(219, 39, 119, .18);
            animation: panicFlash .5s steps(1) infinite;
        }
        @keyframes panicFlash { 0% { opacity: 1; } 50% { opacity: 0; } }

        /* ===== Lazer şəbəkəsi ===== */
        .laser {
            position: fixed; left: 0; width: 100%; height: 2px; z-index: 1; pointer-events: none;
            background: linear-gradient(90deg, transparent, #db2777 20%, #f9a8d4 50%, #db2777 80%, transparent);
            box-shadow: 0 0 14px 2px rgba(219, 39, 119, .7);
            opacity: .55;
            animation: laser 7s ease-in-out infinite alternate;
        }
        .laser:nth-of-type(2) { animation-duration: 9s; animation-delay: -3s; }
        .laser:nth-of-type(3) { animation-duration: 11s; animation-delay: -6s; opacity: .35; }
        @keyframes laser { from { top: 8%; } to { top: 92%; } }

        /* ===== Səhnə ===== */
        .stage { position: relative; z-index: 2; text-align: center; padding: 0 16px; display: flex; flex-direction: column; align-items: center; }

        .digits {
            display: flex; align-items: center; justify-content: center;
            gap: clamp(.4rem, 2vw, 1.5rem);
            font-size: clamp(6rem, 20vw, 13rem);
            line-height: 1; font-weight: 900;
        }
        .digit { position: relative; display: inline-block; }
        .gradient-text {
            background: linear-gradient(180deg, #d8b4fe 0%, #a855f7 55%, #6d28d9 100%);
            -webkit-background-clip: text; background-clip: text;
            -webkit-text-fill-color: transparent; color: transparent;
            filter: drop-shadow(0 8px 0 rgba(0, 0, 0, .6)) drop-shadow(0 0 30px rgba(168, 85, 247, .4));
        }

        /* 0: barmaq izi skaneri */
        .scanner {
            position: relative; width: .86em; height: .86em;
            border-radius: .22em; overflow: hidden;
            border: .035em solid #a855f7;
            background: rgba(168, 85, 247, .08);
            box-shadow: 0 0 40px rgba(168, 85, 247, .35), inset 0 0 30px rgba(168, 85, 247, .2);
            animation: ring 4s ease-in-out infinite;
        }
        .scanner svg.fp { position: absolute; inset: 14%; width: 72%; height: 72%; }
        .scan-line {
            position: absolute; left: 0; right: 0; top: 0; height: 4px;
            background: #f9a8d4; box-shadow: 0 0 18px 6px rgba(249, 168, 212, .8);
            animation: sweep 4s linear infinite;
        }
        .scan-x {
            position: absolute; inset: 18%; width: 64%; height: 64%;
            opacity: 0; animation: xshow 4s ease-in-out infinite;
        }
        @keyframes sweep {
            0% { top: 0; opacity: 1; }
            30% { top: calc(100% - 4px); }
            60% { top: 0; opacity: 1; }
            64%, 100% { top: 0; opacity: 0; }
        }
        @keyframes xshow {
            0%, 64% { opacity: 0; transform: scale(2); }
            70% { opacity: 1; transform: scale(1); }
            92% { opacity: 1; }
            100% { opacity: 0; }
        }
        @keyframes ring {
            0%, 62% { border-color: #a855f7; box-shadow: 0 0 40px rgba(168, 85, 247, .35), inset 0 0 30px rgba(168, 85, 247, .2); }
            68%, 92% { border-color: #db2777; box-shadow: 0 0 60px rgba(219, 39, 119, .7), inset 0 0 40px rgba(219, 39, 119, .4); }
            100% { border-color: #a855f7; box-shadow: 0 0 40px rgba(168, 85, 247, .35), inset 0 0 30px rgba(168, 85, 247, .2); }
        }

        /* ===== Möhür ===== */
        .stamp-wrap { margin-top: 1.6rem; height: 4.2rem; display: flex; align-items: center; justify-content: center; }
        .stamp {
            display: inline-block;
            padding: .35rem 1.2rem;
            border: 4px solid #db2777; border-radius: 8px;
            color: #db2777; font-weight: 900; text-transform: uppercase; letter-spacing: .16em;
            font-size: clamp(1.1rem, 4.5vw, 2rem);
            text-shadow: 0 0 18px rgba(219, 39, 119, .5);
            box-shadow: 0 0 24px rgba(219, 39, 119, .3), inset 0 0 12px rgba(219, 39, 119, .2);
            transform: rotate(-5deg);
            animation: slam 5s cubic-bezier(.2, .8, .2, 1) infinite;
        }
        @keyframes slam {
            0% { transform: rotate(-5deg) scale(3.2); opacity: 0; }
            8% { transform: rotate(-5deg) scale(1); opacity: 1; }
            10% { transform: rotate(-5deg) scale(1.06) translateY(3px); }
            12%, 85% { transform: rotate(-5deg) scale(1); opacity: 1; }
            95%, 100% { transform: rotate(-5deg) scale(1); opacity: 0; }
        }

        .desc { margin: 1rem auto 0; max-width: 34rem; color: #a1a1aa; font-size: .95rem; line-height: 1.7; }

        /* ===== Klaviatura ===== */
        .pad {
            margin-top: 1.4rem; width: 220px;
            padding: 14px; border-radius: 16px;
            background: rgba(255, 255, 255, .04);
            border: 1px solid rgba(255, 255, 255, .1);
            backdrop-filter: blur(8px);
        }
        .pad-display {
            height: 44px; border-radius: 10px; margin-bottom: 10px;
            background: #0a0612; border: 1px solid rgba(168, 85, 247, .35);
            display: flex; align-items: center; justify-content: center;
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: 18px; letter-spacing: .4em; color: #d8b4fe; text-transform: uppercase;
            transition: color .15s, border-color .15s;
        }
        .pad-display.bad { color: #db2777; border-color: #db2777; letter-spacing: .1em; font-size: 14px; font-weight: 700; animation: shake .4s; }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-8px); }
            40% { transform: translateX(8px); }
            60% { transform: translateX(-6px); }
            80% { transform: translateX(6px); }
        }
        .keys { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
        .key {
            height: 38px; border: 0; border-radius: 9px; cursor: pointer;
            background: rgba(255, 255, 255, .07); color: #fff;
            font: 700 16px 'Figtree', sans-serif;
            box-shadow: 0 3px 0 rgba(0, 0, 0, .5);
            transition: transform .08s, box-shadow .08s, background .15s;
        }
        .key:hover { background: rgba(168, 85, 247, .35); }
        .key:active { transform: translateY(3px); box-shadow: 0 0 0 rgba(0, 0, 0, .5); }
        .key.clr { color: #f9a8d4; }
        .tries { margin-top: 8px; font-size: 11px; font-weight: 600; color: #71717a; text-transform: uppercase; letter-spacing: .08em; }
        .tries b { color: #f9a8d4; }

        .btn {
            margin-top: 1.4rem; display: inline-flex; align-items: center; gap: .5rem;
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

        @media (max-height: 820px) {
            .desc { display: none; }
            .stamp-wrap { margin-top: 1rem; height: 3.4rem; }
            .pad { margin-top: .8rem; padding: 10px; }
            .key { height: 32px; }
            .btn { margin-top: .9rem; padding: .6rem 1.6rem; }
        }
        @media (max-height: 680px) {
            .pad { display: none; }
            .branding { top: 10px; right: 10px; padding: 6px 10px; }
            .branding svg { width: 16px; height: 16px; }
            .branding-title { font-size: 12px; }
            .branding-sub { font-size: 8px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
            .scan-x { opacity: 1; }
        }
    </style>
</head>
<body>

    @php
        $msgs = [
            'denied' => __('Access Denied'),
            'nice' => __('Nice try!'),
            'alarm' => __('Alarm triggered'),
        ];
    @endphp

    <a href="/" class="branding">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        <div class="branding-text">
            <span class="branding-title">RJ NEXUS</span>
            <span class="branding-sub">Powered by RJ CMS</span>
        </div>
    </a>

    <div class="alarm alarm-l"></div>
    <div class="alarm alarm-r"></div>
    <div class="laser"></div>
    <div class="laser"></div>
    <div class="laser"></div>

    <div class="stage">

        <div class="digits" aria-label="403">
            <div class="digit"><span class="gradient-text">4</span></div>

            {{-- 0: barmaq izi skaneri --}}
            <div class="digit">
                <div class="scanner">
                    <svg class="fp" viewBox="0 0 100 100" fill="none" stroke="#c084fc" stroke-width="3.5" stroke-linecap="round">
                        <circle cx="50" cy="50" r="7" stroke-dasharray="30 14" transform="rotate(20 50 50)"/>
                        <circle cx="50" cy="50" r="15" stroke-dasharray="60 16" transform="rotate(110 50 50)"/>
                        <circle cx="50" cy="50" r="23" stroke-dasharray="90 20" transform="rotate(200 50 50)"/>
                        <circle cx="50" cy="50" r="31" stroke-dasharray="120 24" transform="rotate(300 50 50)"/>
                        <circle cx="50" cy="50" r="39" stroke-dasharray="150 30" transform="rotate(60 50 50)"/>
                    </svg>
                    <svg class="scan-x" viewBox="0 0 100 100" fill="none" stroke="#db2777" stroke-width="12" stroke-linecap="round">
                        <line x1="18" y1="18" x2="82" y2="82"/>
                        <line x1="82" y1="18" x2="18" y2="82"/>
                    </svg>
                    <div class="scan-line"></div>
                </div>
            </div>

            <div class="digit"><span class="gradient-text">3</span></div>
        </div>

        <div class="stamp-wrap">
            <div class="stamp">{{ __('Access Denied') }}</div>
        </div>

        <p class="desc">
            {{ __('You do not have the necessary permissions to view this page. Please contact the administrator if you believe this is a mistake.') }}
        </p>

        <div class="pad">
            <div class="pad-display" id="display"></div>
            <div class="keys" id="keys">
                <button class="key" data-k="1">1</button>
                <button class="key" data-k="2">2</button>
                <button class="key" data-k="3">3</button>
                <button class="key" data-k="4">4</button>
                <button class="key" data-k="5">5</button>
                <button class="key" data-k="6">6</button>
                <button class="key" data-k="7">7</button>
                <button class="key" data-k="8">8</button>
                <button class="key" data-k="9">9</button>
                <button class="key clr" data-k="C">C</button>
                <button class="key" data-k="0">0</button>
                <button class="key clr" data-k="B">&larr;</button>
            </div>
            <p class="tries">{{ __('Attempts') }}: <b id="tries">0</b></p>
        </div>

        <a href="{{ url('/') }}" class="btn">
            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            {{ __('Back to Home') }}
        </a>
    </div>

    <script>
        (function () {
            var msgs =