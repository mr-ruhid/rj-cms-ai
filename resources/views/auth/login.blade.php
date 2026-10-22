<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ rj_dir() }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Log in') }} — {{ rj_get_option('site_name', config('app.name')) }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Figtree', sans-serif; }
        :root { {!! rj_css_variables() !!} }

        .rj-rise { opacity: 0; transform: translateY(18px); animation: rj-rise .65s cubic-bezier(.22, 1, .36, 1) forwards; }
        @keyframes rj-rise { to { opacity: 1; transform: translateY(0); } }

        .rj-bob { animation: rj-bob 5s ease-in-out infinite; }
        .rj-bob-2 { animation-delay: -1.2s; }
        .rj-bob-3 { animation-delay: -2.4s; }
        .rj-bob-4 { animation-delay: -3.6s; }
        @keyframes rj-bob {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        .rj-svg.is-error .rj-bob { animation: rj-nope .55s cubic-bezier(.36, .07, .19, .97) both; }
        @keyframes rj-nope {
            10%, 90% { transform: translateX(-2px); }
            20%, 80% { transform: translateX(4px); }
            30%, 50%, 70% { transform: translateX(-7px); }
            40%, 60% { transform: translateX(7px); }
        }

        .rj-char {
            transform-box: fill-box;
            transform-origin: 50% 100%;
            transition: transform .55s cubic-bezier(.22, 1, .36, 1);
        }
        .rj-svg.is-email .rj-char-a { transform: skewX(-3deg); }
        .rj-svg.is-email .rj-char-b { transform: skewX(2deg); }
        .rj-svg.is-pass .rj-char-a { transform: skewX(3deg); }
        .rj-svg.is-pass .rj-char-b { transform: skewX(-2deg); }

        .rj-face { transition: transform .35s cubic-bezier(.22, 1, .36, 1); }
        .rj-pupil { transition: transform .14s ease-out, opacity .12s ease; }
        .rj-white { transition: opacity .12s ease; }
        .rj-lid { opacity: 0; transition: opacity .12s ease; }
        .rj-svg.is-closed .rj-pupil,
        .rj-svg.is-closed .rj-white { opacity: 0; }
        .rj-svg.is-closed .rj-lid { opacity: 1; }

        .rj-field-icon, .rj-field-toggle {
            position: absolute;
            top: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            color: #9ca3af;
        }
        .rj-field-icon { inset-inline-start: 0; padding-inline-start: 1rem; pointer-events: none; }
        .rj-field-toggle { inset-inline-end: 0; padding-inline-end: 1rem; background: none; border: 0; cursor: pointer; transition: color .2s ease; }
        .rj-field-toggle:hover { color: var(--rj-purple, #8b5cf6); }
        .rj-field-icon svg, .rj-field-toggle svg { width: 1.25rem; height: 1.25rem; flex-shrink: 0; }
        input.rj-has-icon { padding-inline-start: 3rem; padding-inline-end: 1rem; }
        input.rj-has-icon.rj-has-toggle { padding-inline-end: 3rem; }
        .rj-hidden { display: none; }

        .rj-input { transition: box-shadow .25s ease, border-color .25s ease; }
        .rj-input:focus { box-shadow: 0 10px 24px -12px rgba(168, 85, 247, .5); }

        .rj-btn { position: relative; overflow: hidden; }
        .rj-btn::after {
            content: '';
            position: absolute;
            top: 0;
            left: -120%;
            width: 60%;
            height: 100%;
            background: linear-gradient(120deg, transparent, rgba(255, 255, 255, .4), transparent);
            transform: skewX(-20deg);
            animation: rj-shine 3.6s ease-in-out infinite;
        }
        @keyframes rj-shine {
            0%, 55% { left: -120%; }
            100% { left: 160%; }
        }

        .rj-shake { animation: rj-nope .5s cubic-bezier(.36, .07, .19, .97) both; }
        .rj-spin { animation: rj-spin .8s linear infinite; }
        @keyframes rj-spin { to { transform: rotate(360deg); } }

        .rj-arrow { transition: transform .25s ease; }
        .rj-back:hover .rj-arrow { transform: translateX(-4px); }

        @media (prefers-reduced-motion: reduce) {
            .rj-bob, .rj-btn::after, .rj-spin { animation: none; }
            .rj-rise { animation: none; opacity: 1; transform: none; }
            .rj-face, .rj-pupil, .rj-char { transition: none; }
        }
    </style>
</head>
<body class="min-h-full text-gray-900 antialiased">
    @php $logoMask = rj_logo_mask_url(); @endphp

    <div class="min-h-screen grid lg:grid-cols-2">

        <aside class="hidden lg:flex relative flex-col justify-between p-12 text-white bg-gradient-to-br from-rj-purple to-rj-pink overflow-hidden">
            <a href="{{ url('/') }}" class="inline-flex items-center self-start">
                @if ($logoMask)
                    <div class="h-10 w-36 bg-white" style="-webkit-mask-image: url('{{ $logoMask }}'); mask-image: url('{{ $logoMask }}'); -webkit-mask-size: contain; mask-size: contain; -webkit-mask-repeat: no-repeat; mask-repeat: no-repeat; -webkit-mask-position: left center; mask-position: left center;"></div>
                @else
                    <span class="text-2xl font-black tracking-tight">{{ rj_get_option('site_name', 'RJ CMS') }}</span>
                @endif
            </a>

            <div class="flex items-center justify-center">
                <svg id="rj-chars" class="rj-svg w-full max-w-lg" viewBox="0 0 440 400" aria-hidden="true">
                    <ellipse cx="220" cy="386" rx="205" ry="9" fill="rgba(0,0,0,.2)"/>

                    <g class="rj-bob">
                        <g class="rj-char rj-char-a">
                            <rect x="60" y="50" width="140" height="334" rx="20" fill="#5b21b6"/>
                            <g class="rj-face" data-mult="1.3">
                                <g class="rj-eye" data-max="4.5" transform="translate(105 120)">
                                    <circle class="rj-white" r="11" fill="#fff"/>
                                    <circle class="rj-pupil" r="5.5" fill="#111827"/>
                                    <path class="rj-lid" d="M-10 0 Q0 8 10 0" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round"/>
                                </g>
                                <g class="rj-eye" data-max="4.5" transform="translate(155 120)">
                                    <circle class="rj-white" r="11" fill="#fff"/>
                                    <circle class="rj-pupil" r="5.5" fill="#111827"/>
                                    <path class="rj-lid" d="M-10 0 Q0 8 10 0" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round"/>
                                </g>
                            </g>
                        </g>
                    </g>

                    <g class="rj-bob rj-bob-2">
                        <g class="rj-char rj-char-b">
                            <rect x="190" y="165" width="100" height="219" rx="16" fill="#111827"/>
                            <g class="rj-face" data-mult="1">
                                <g class="rj-eye" data-max="4" transform="translate(222 215)">
                                    <circle class="rj-white" r="10" fill="#fff"/>
                                    <circle class="rj-pupil" r="5" fill="#111827"/>
                                    <path class="rj-lid" d="M-9 0 Q0 7 9 0" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round"/>
                                </g>
                                <g class="rj-eye" data-max="4" transform="translate(258 215)">
                                    <circle class="rj-white" r="10" fill="#fff"/>
                                    <circle class="rj-pupil" r="5" fill="#111827"/>
                                    <path class="rj-lid" d="M-9 0 Q0 7 9 0" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round"/>
                                </g>
                            </g>
                        </g>
                    </g>

                    <g class="rj-bob rj-bob-3">
                        <g class="rj-char rj-char-c">
                            <path d="M10 384 a95 95 0 0 1 190 0 Z" fill="#fb923c"/>
                            <g class="rj-face" data-mult="1.6">
                                <g class="rj-eye" data-max="5" transform="translate(78 336)">
                                    <circle class="rj-pupil" r="7" fill="#111827"/>
                                    <path class="rj-lid" d="M-9 0 Q0 8 9 0" fill="none" stroke="#111827" stroke-width="3" stroke-linecap="round"/>
                                </g>
                                <g class="rj-eye" data-max="5" transform="translate(132 336)">
                                    <circle class="rj-pupil" r="7" fill="#111827"/>
                                    <path class="rj-lid" d="M-9 0 Q0 8 9 0" fill="none" stroke="#111827" stroke-width="3" stroke-linecap="round"/>
                                </g>
                                <path d="M93 358 Q105 370 117 358" fill="none" stroke="#111827" stroke-width="3" stroke-linecap="round"/>
                            </g>
                        </g>
                    </g>

                    <g class="rj-bob rj-bob-4">
                        <g class="rj-char rj-char-d">
                            <path d="M285 384 V285 a60 60 0 0 1 120 0 V384 Z" fill="#fde047"/>
                            <g class="rj-face" data-mult="1.5">
                                <g class="rj-eye" data-max="5" transform="translate(320 285)">
                                    <circle class="rj-pupil" r="7" fill="#111827"/>
                                    <path class="rj-lid" d="M-9 0 Q0 8 9 0" fill="none" stroke="#111827" stroke-width="3" stroke-linecap="round"/>
                                </g>
                                <g class="rj-eye" data-max="5" transform="translate(370 285)">
                                    <circle class="rj-pupil" r="7" fill="#111827"/>
                                    <path class="rj-lid" d="M-9 0 Q0 8 9 0" fill="none" stroke="#111827" stroke-width="3" stroke-linecap="round"/>
                                </g>
                                <path d="M335 310 Q345 320 355 310" fill="none" stroke="#111827" stroke-width="3" stroke-linecap="round"/>
                            </g>
                        </g>
                    </g>
                </svg>
            </div>

            <p class="text-lg font-semibold text-white/90 max-w-sm leading-snug">{{ __('Everything you need to manage your site, in one place.') }}</p>
        </aside>

        <main class="flex items-center justify-center p-6 sm:p-12 bg-white">
            <div class="w-full max-w-sm">

                <div class="lg:hidden text-center mb-8 rj-rise" style="animation-delay:.05s">
                    <a href="{{ url('/') }}" class="inline-flex items-center justify-center">
                        @if ($logoMask)
                            <div class="h-12 w-40 bg-rj-dark-1" style="-webkit-mask-image: url('{{ $logoMask }}'); mask-image: url('{{ $logoMask }}'); -webkit-mask-size: contain; mask-size: contain; -webkit-mask-repeat: no-repeat; mask-repeat: no-repeat; -webkit-mask-position: center; mask-position: center;"></div>
                        @else
                            <span class="text-3xl font-black bg-clip-text text-transparent bg-gradient-to-r from-rj-purple to-rj-pink">{{ rj_get_option('site_name', 'RJ CMS') }}</span>
                        @endif
                    </a>
                </div>

                <div class="mb-8 rj-rise" style="animation-delay:.1s">
                    <h1 class="text-3xl font-extrabold text-gray-900">{{ __('Welcome back') }}</h1>
                    <p class="text-sm text-gray-500 mt-2">{{ __('Sign in to your account to continue') }}</p>
                </div>

                @if (session('status'))
                    <div class="mb-6 text-sm font-medium text-green-700 bg-green-50 p-3 rounded-xl border border-green-200 text-center rj-rise" style="animation-delay:.15s">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="rj-shake mb-6 bg-red-50 text-red-600 p-4 rounded-xl border border-red-100 text-sm">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5" id="rj-login-form">
                    @csrf

                    <div class="rj-rise" style="animation-delay:.2s">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Email Address') }}</label>
                        <div class="relative">
                            <span class="rj-field-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                            </span>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                   class="rj-input w-full rounded-xl border-gray-300 focus:border-rj-purple focus:ring-rj-purple rj-has-icon py-3"
                                   placeholder="name@example.com">
                        </div>
                    </div>

                    <div class="rj-rise" style="animation-delay:.3s">
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-sm font-medium text-gray-700">{{ __('Password') }}</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-rj-purple hover:text-rj-pink transition">
                                    {{ __('Forgot password?') }}
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <span class="rj-field-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                            </span>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                   class="rj-input w-full rounded-xl border-gray-300 focus:border-rj-purple focus:ring-rj-purple rj-has-icon rj-has-toggle py-3"
                                   placeholder="••••••••">
                            <button type="button" id="rj-toggle-pass" aria-label="{{ __('Show password') }}"
                                    class="rj-field-toggle">
                                <svg id="rj-eye-on" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <svg id="rj-eye-off" class="rj-hidden" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1 rj-rise" style="animation-delay:.4s">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer">
                            <input id="remember_me" type="checkbox" name="remember"
                                   class="rounded border-gray-300 text-rj-purple shadow-sm focus:ring-rj-purple">
                            <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                        </label>
                    </div>

                    <div class="rj-rise" style="animation-delay:.5s">
                        <button type="submit" id="rj-submit" class="rj-btn w-full flex justify-center items-center gap-2 py-3.5 px-4 rounded-xl shadow-lg shadow-rj-purple/30 text-sm font-bold text-white bg-gradient-to-r from-rj-purple to-rj-pink hover:opacity-95 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rj-purple transition-all active:scale-[.98]">
                            <svg id="rj-spinner" class="rj-spin rj-hidden" style="width:1rem;height:1rem" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                            <span id="rj-submit-text">{{ __('Sign In') }}</span>
                        </button>
                    </div>
                </form>

                @if (Route::has('register'))
                    <div class="mt-8 text-center pt-6 border-t border-gray-100 rj-rise" style="animation-delay:.6s">
                        <p class="text-sm text-gray-600">
                            {{ __("Don't have an account?") }}
                            <a href="{{ route('register') }}" class="font-bold text-rj-purple hover:text-rj-pink transition ml-1">{{ __('Create one now') }}</a>
                        </p>
                    </div>
                @endif

                <div class="text-center mt-8 rj-rise" style="animation-delay:.7s">
                    <a href="{{ url('/') }}" class="rj-back text-sm font-medium text-gray-500 hover:text-gray-900 transition inline-flex items-center gap-1.5">
                        <svg class="rj-arrow w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        {{ __('Back to website') }}
                    </a>
                </div>
            </div>
        </main>
    </div>

    <script>
        (function () {
            var svg = document.getElementById('rj-chars');
            var email = document.getElementById('email');
            var pass = document.getElementById('password');
            var toggle = document.getElementById('rj-toggle-pass');
            var eyeOn = document.getElementById('rj-eye-on');
            var eyeOff = document.getElementById('rj-eye-off');
            var form = document.getElementById('rj-login-form');
            var btn = document.getElementById('rj-submit');
            var spinner = document.getElementById('rj-spinner');
            var text = document.getElementById('rj-submit-text');
            var hasErrors = @json($errors->any());

            var eyes = svg ? [].slice.call(svg.querySelectorAll('.rj-eye')) : [];
            var faces = svg ? [].slice.call(svg.querySelectorAll('.rj-face')) : [];
            var mouse = null;
            var blinking = false;

            function isRtl() { return document.documentElement.dir === 'rtl'; }

            function lookAt(tx, ty) {
                eyes.forEach(function (eye) {
                    var pupil = eye.querySelector('.rj-pupil');
                    var r = eye.getBoundingClientRect();
                    var cx = r.left + r.width / 2;
                    var cy = r.top + r.height / 2;
                    var dx = tx - cx;
                    var dy = ty - cy;
                    var d = Math.sqrt(dx * dx + dy * dy) || 1;
                    var max = parseFloat(eye.getAttribute('data-max'));
                    var k = Math.min(1, d / 140);
                    pupil.style.transform = 'translate(' + (dx / d * max * k) + 'px,' + (dy / d * max * k) + 'px)';
                });
            }

            function shiftFaces(x, y) {
                faces.forEach(function (face) {
                    var m = parseFloat(face.getAttribute('data-mult'));
                    face.style.transform = 'translate(' + (x * m) + 'px,' + (y * m) + 'px)';
                });
            }

            function caretTarget() {
                var r = email.getBoundingClientRect();
                var travel = Math.min(email.value.length * 8.5, Math.max(r.width - 80, 0));
                var x = isRtl() ? r.right - 48 - travel : r.left + 48 + travel;
                return { x: x, y: r.top + r.height / 2 };
            }

            function state() {
                if (document.activeElement === pass || document.activeElement === toggle) { return 'pass'; }
                if (document.activeElement === email) { return 'email'; }
                return 'idle';
            }

            function render() {
                if (!svg) { return; }
                var s = state();
                var peek = s === 'pass' && pass.type === 'text';
                svg.classList.toggle('is-email', s === 'email');
                svg.classList.toggle('is-pass', s === 'pass');
                svg.classList.toggle('is-closed', (s === 'pass' && !peek) || blinking);

                var box = svg.getBoundingClientRect();
                var sideX = box.left + box.width / 2;

                if (s === 'email') {
                    var t = caretTarget();
                    var dir = t.x >= sideX ? 1 : -1;
                    var ratio = Math.min(email.value.length / 28, 1);
                    shiftFaces(dir * (5 + ratio * 3), 2 + ratio * 3);
                    lookAt(t.x, t.y + 14 + ratio * 26);
                } else if (s === 'pass') {
                    if (peek) {
                        shiftFaces(isRtl() ? 6 : -6, 3);
                        lookAt(box.left + (isRtl() ? box.width + 300 : -300), box.bottom);
                    } else {
                        shiftFaces(0, 2);
                    }
                } else {
                    var p = mouse || { x: box.right + 400, y: box.top + box.height / 2 };
                    var dx = p.x - sideX;
                    shiftFaces(Math.max(-6, Math.min(6, dx / 120)), 0);
                    lookAt(p.x, p.y);
                }
            }

            email.addEventListener('focus', render);
            email.addEventListener('blur', function () { setTimeout(render, 0); });
            email.addEventListener('input', render);
            email.addEventListener('keyup', render);
            email.addEventListener('click', render);
            pass.addEventListener('focus', render);
            pass.addEventListener('blur', function () { setTimeout(render, 0); });
            window.addEventListener('resize', render);
            document.addEventListener('mousemove', function (e) {
                mouse = { x: e.clientX, y: e.clientY };
                if (state() === 'idle') { render(); }
            });

            toggle.addEventListener('mousedown', function (e) { e.preventDefault(); });
            toggle.addEventListener('click', function () {
                var show = pass.type === 'password';
                pass.type = show ? 'text' : 'password';
                eyeOn.classList.toggle('rj-hidden', show);
                eyeOff.classList.toggle('rj-hidden', !show);
                pass.focus();
                render();
            });

            function blink() {
                if (state() !== 'pass') {
                    blinking = true;
                    render();
                    setTimeout(function () { blinking = false; render(); }, 150);
                }
                setTimeout(blink, 2600 + Math.random() * 2800);
            }
            setTimeout(blink, 2200);

            if (hasErrors && svg) {
                svg.classList.add('is-error');
                setTimeout(function () { svg.classList.remove('is-error'); }, 900);
            }

            form.addEventListener('submit', function () {
                spinner.classList.remove('rj-hidden');
                text.textContent = @json(__('Signing in...'));
                setTimeout(function () { btn.disabled = true; btn.classList.add('opacity-80', 'cursor-wait'); }, 0);
            });

            render();
        })();
    </script>

</body>
</html>
