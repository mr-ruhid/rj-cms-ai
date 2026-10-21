<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>503 - {{ __('Under Maintenance') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,900&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Figtree', sans-serif; }

        .bg-main {
            background-color: #0b1020;
            background-image:
                radial-gradient(at 0% 0%, hsla(253,40%,14%,1) 0, transparent 55%),
                radial-gradient(at 50% 0%, hsla(225,55%,26%,1) 0, transparent 55%),
                radial-gradient(at 100% 0%, hsla(339,55%,26%,1) 0, transparent 55%);
        }

        .glass {
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        
        .orb { position: absolute; border-radius: 9999px; filter: blur(70px); opacity: .35; transition: transform .4s ease-out; }
        .orb-1 { width: 380px; height: 380px; background: #7c3aed; top: -120px; left: -100px; animation: drift 14s ease-in-out infinite; }
        .orb-2 { width: 320px; height: 320px; background: #ec4899; bottom: 10%; right: -80px; animation: drift 17s ease-in-out infinite reverse; }
        .orb-3 { width: 260px; height: 260px; background: #06b6d4; top: 40%; left: 30%; opacity: .18; animation: drift 20s ease-in-out infinite; }
        @keyframes drift {
            0%, 100% { margin: 0 0 0 0; }
            50% { margin: 40px 0 0 50px; }
        }

        
        .wave-wrap { position: absolute; left: 0; bottom: 0; width: 100%; height: 220px; overflow: hidden; pointer-events: none; }
        .wave { position: absolute; left: 0; bottom: 0; width: 200%; height: 100%; }
        .wave svg { width: 100%; height: 100%; display: block; }
        .wave-1 { animation: waveMove 14s linear infinite; opacity: .35; }
        .wave-2 { animation: waveMove 10s linear infinite reverse; opacity: .45; height: 85%; }
        .wave-3 { animation: waveMove 7s linear infinite; opacity: .7; height: 65%; }
        @keyframes waveMove {
            from { transform: translateX(0); }
            to   { transform: translateX(-50%); }
        }
        .wave-bob { animation: bob 6s ease-in-out infinite; }
        @keyframes bob {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(10px); }
        }

       
        .bubble {
            position: absolute; bottom: -40px; border-radius: 9999px;
            background: radial-gradient(circle at 30% 30%, rgba(255,255,255,.55), rgba(255,255,255,.08));
            border: 1px solid rgba(255,255,255,.25);
            animation: rise linear infinite;
        }
        @keyframes rise {
            0%   { transform: translateY(0) translateX(0); opacity: 0; }
            10%  { opacity: .9; }
            50%  { transform: translateY(-50vh) translateX(20px); }
            100% { transform: translateY(-105vh) translateX(-20px); opacity: 0; }
        }

        
        .spin-slow { animation: spin 9s linear infinite; }
        .spin-rev  { animation: spin 6s linear infinite reverse; }
        @keyframes spin { to { transform: rotate(360deg); } }

       
        .text-shimmer {
            background: linear-gradient(90deg, #a78bfa, #f472b6, #22d3ee, #a78bfa);
            background-size: 300% 100%;
            -webkit-background-clip: text; background-clip: text; color: transparent;
            animation: shimmer 5s linear infinite;
            filter: drop-shadow(0 0 22px rgba(168, 85, 247, .45));
        }
        @keyframes shimmer { to { background-position: 300% 0; } }

        .float { animation: float 5s ease-in-out infinite; }
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(3deg); }
            50% { transform: translateY(-10px) rotate(-2deg); }
        }

        .glow-ring { animation: pulseRing 2.4s ease-out infinite; }
        @keyframes pulseRing {
            0% { box-shadow: 0 0 0 0 rgba(236, 72, 153, .55); }
            100% { box-shadow: 0 0 0 28px rgba(236, 72, 153, 0); }
        }

        
        .bar { position: relative; height: 6px; border-radius: 9999px; background: rgba(255,255,255,.1); overflow: hidden; }
        .bar::before {
            content: ''; position: absolute; top: 0; bottom: 0; width: 40%;
            border-radius: 9999px;
            background: linear-gradient(90deg, #a855f7, #ec4899, #22d3ee);
            animation: slide 1.6s ease-in-out infinite;
        }
        @keyframes slide {
            0% { left: -40%; }
            100% { left: 100%; }
        }

        .card-in { animation: cardIn .9s cubic-bezier(.2,.8,.2,1) both; }
        @keyframes cardIn {
            from { opacity: 0; transform: translateY(30px) scale(.96); }
            to   { opacity: 1; transform: none; }
        }

        .dots span { animation: blink 1.4s infinite both; }
        .dots span:nth-child(2) { animation-delay: .2s; }
        .dots span:nth-child(3) { animation-delay: .4s; }
        @keyframes blink { 0%, 80%, 100% { opacity: .15; } 40% { opacity: 1; } }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
        }
    </style>
</head>
<body class="h-full bg-main text-white antialiased overflow-hidden">

 
    <div class="orb orb-1" data-depth="20"></div>
    <div class="orb orb-2" data-depth="-30"></div>
    <div class="orb orb-3" data-depth="40"></div>

 
    <div id="bubbles" class="absolute inset-0 pointer-events-none"></div>

   
    <div class="wave-wrap">
        <div class="wave wave-1 wave-bob">
            <svg viewBox="0 0 2400 200" preserveAspectRatio="none">
                <path fill="#6d28d9" d="M0,100 C150,40 450,40 600,100 C750,160 1050,160 1200,100 C1350,40 1650,40 1800,100 C1950,160 2250,160 2400,100 L2400,200 L0,200 Z"/>
            </svg>
        </div>
        <div class="wave wave-2">
            <svg viewBox="0 0 2400 200" preserveAspectRatio="none">
                <path fill="#be185d" d="M0,110 C200,50 400,50 600,110 C800,170 1000,170 1200,110 C1400,50 1600,50 1800,110 C2000,170 2200,170 2400,110 L2400,200 L0,200 Z"/>
            </svg>
        </div>
        <div class="wave wave-3">
            <svg viewBox="0 0 2400 200" preserveAspectRatio="none">
                <path fill="#0e7490" d="M0,120 C100,80 500,80 600,120 C700,160 1100,160 1200,120 C1300,80 1700,80 1800,120 C1900,160 2300,160 2400,120 L2400,200 L0,200 Z"/>
            </svg>
        </div>
    </div>

    
    <div class="relative z-10 min-h-full flex items-center justify-center p-4">
        <div class="glass card-in max-w-lg w-full p-8 md:p-12 rounded-3xl shadow-2xl text-center relative overflow-hidden">

            
            <svg class="absolute -top-8 -right-8 w-32 h-32 text-white/5 spin-slow" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <svg class="absolute -bottom-10 -left-10 w-40 h-40 text-white/5 spin-rev" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>

            <div class="relative z-10">
                <div class="float glow-ring w-20 h-20 mx-auto bg-gradient-to-tr from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center shadow-lg mb-6">
                    <svg class="w-10 h-10 text-white spin-slow" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>

                <h1 class="text-7xl md:text-8xl font-black text-shimmer mb-3 tracking-tight">503</h1>
                <h2 class="text-2xl font-bold mb-3">
                    {{ __('Under Maintenance') }}<span class="dots"><span>.</span><span>.</span><span>.</span></span>
                </h2>
                <p class="text-gray-300 text-sm md:text-base mb-6 leading-relaxed">
                    {{ __('We are currently performing scheduled maintenance. We should be back shortly. Thank you for your patience.') }}
                </p>

                <div class="bar mb-3"></div>
                <p class="text-xs text-gray-400 mb-7">
                    {{ __('Auto refresh in') }} <span id="count" class="font-semibold text-white">30</span>s
                </p>

                <button onclick="window.location.reload()" class="group inline-flex items-center justify-center gap-2 w-full md:w-auto px-8 py-3 bg-white text-gray-900 rounded-xl font-semibold hover:bg-gray-100 hover:scale-105 active:scale-95 transition duration-200 shadow-lg shadow-pink-500/20">
                    <svg class="w-5 h-5 transition-transform duration-500 group-hover:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    {{ __('Refresh Page') }}
                </button>
            </div>
        </div>
    </div>

    <script>
        
        (function () {
            const box = document.getElementById('bubbles');
            for (let i = 0; i < 22; i++) {
                const b = document.createElement('span');
                const size = 8 + Math.random() * 30;
                b.className = 'bubble';
                b.style.width = b.style.height = size + 'px';
                b.style.left = Math.random() * 100 + '%';
                b.style.animationDuration = (9 + Math.random() * 14) + 's';
                b.style.animationDelay = (-Math.random() * 20) + 's';
                box.appendChild(b);
            }
        })();

        
        document.addEventListener('mousemove', function (e) {
            const x = (e.clientX / window.innerWidth - 0.5);
            const y = (e.clientY / window.innerHeight - 0.5);
            document.querySelectorAll('.orb').forEach(function (el) {
                const d = parseFloat(el.dataset.depth || 20);
                el.style.transform = 'translate(' + (x * d * 2) + 'px,' + (y * d * 2) + 'px)';
            });
        });

        
        (function () {
            let left = 30;
            const el = document.getElementById('count');
            setInterval(function () {
                left--;
                if (left <= 0) { window.location.reload(); return; }
                el.textContent = left;
            }, 1000);
        })();
    </script>
</body>
</html>