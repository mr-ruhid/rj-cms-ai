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
        }
        @keyframes gridMove {
            to { background-position: 48px 48px, 48px 48px, 0 0; }
        }

        .branding {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 100;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            backdrop-filter: blur(8px);
            text-decoration: none;
            color: #fff;
            transition: all 0.3s ease;
        }
        .branding:hover {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(139, 92, 246, 0.4);
        }
        .branding svg { width: 24px; height: 24px; color: #a855f7; }
        .branding-text { display: flex; flex-direction: column; text-align: left; }
        .branding-title { font-weight: 900; font-size: 14px; letter-spacing: 0.1em; background: linear-gradient(to right, #d8b4fe, #f9a8d4); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .branding-sub { font-size: 10px; color: #71717a; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }

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

        .float {
            animation: float 4s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        .lock-wrap {
            width: .8em; height: .8em; position: relative;
            filter: drop-shadow(0 8px 0 rgba(0, 0, 0, .6)) drop-shadow(0 0 30px rgba(168, 85, 247, .4));
            animation: float 4s ease-in-out infinite; animation-delay: 0.2s;
        }
        .lock-wrap svg { width: 100%; height: 100%; display: block; }

        .glitch { animation: flicker 4s infinite; }
        .glitch::before, .glitch::after {
            content: attr(data-text);
            position: absolute; left: 0; top: 0; width: 100%;
            -webkit-text-fill-color: currentColor;
            background: none;
            overflow: hidden;
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

        .title {
            margin-top: 1.4rem;
            font-size: clamp(1.4rem, 5vw, 2.6rem);
            font-weight: 900; text-transform: uppercase; letter-spacing: .12em;
        }
        .title .ch {
            display: inline-block;
            animation: wave 1.8s ease-in-out infinite;
            text-shadow: 0 0 18px rgba(168, 85, 247, .5);
        }
        .title .sp { display: inline-block; width: .5em; }
        @keyframes wave {
            0%, 100% { transform: translateY(0); color: #fff; }
            30% { transform: translateY(-12px); color: #d8b4fe; }
            60% { transform: translateY(0); color: #fff; }
        }

        .desc { margin: 1rem auto 0; max-width: 34rem; color: #a1a1aa; font-size: .95rem; line-height: 1.7; }

        .btn {
            margin-top: 2rem; display: inline-flex; align-items: center; gap: .5rem;
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

        @media (max-height: 760px) {
            .desc { display: none; }
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
        <div class="tilt" id="tilt">

            <div class="digits" aria-label="403">
                <div class="digit float" style="animation-delay: 0s;"><span class="gradient-text">4</span></div>
                
                <div class="digit">
                    <div class="lock-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="url(#lockGradient)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <defs>
                                <linearGradient id="lockGradient" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0" stop-color="#d8b4fe"/>
                                    <stop offset=".55" stop-color="#a855f7"/>
                                    <stop offset="1" stop-color="#4c1d95"/>
                                </linearGradient>
                            </defs>
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            <circle cx="12" cy="16" r="1.5" fill="url(#lockGradient)"></circle>
                        </svg>
                    </div>
                </div>

                <div class="digit float glitch" data-text="3" style="animation-delay: 0.4s;"><span class="gradient-text">3</span></div>
            </div>

            <h1 class="title" id="title" data-text="{{ __('Access Denied') }}">{{ __('Access Denied') }}</h1>

            <p class="desc">
                {{ __('You do not have the necessary permissions to view this page. Please contact the administrator if you believe this is a mistake.') }}
            </p>

            <a href="{{ url('/') }}" class="btn">
                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('Back to Home') }}
            </a>
        </div>
    </div>

    <script>
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

        (function () {
            var t = document.getElementById('tilt');
            document.addEventListener('mousemove', function (e) {
                var x = e.clientX / window.innerWidth - 0.5;
                var y = e.clientY / window.innerHeight - 0.5;
                t.style.transform = 'rotateY(' + (x * 14) + 'deg) rotateX(' + (-y * 10) + 'deg)';
            });
            document.addEventListener('mouseleave', function () { t.style.transform = ''; });
        })();
    </script>
</body>
</html>