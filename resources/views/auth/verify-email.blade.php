<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ rj_dir() }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Verify Email') }} — {{ rj_get_option('site_name', config('app.name')) }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Figtree', sans-serif; margin: 0; }
        :root { {!! rj_css_variables() !!} }

        .rj-split { display: grid; min-height: 100vh; }
        .rj-aside { display: none; }
        .rj-mobile-logo { display: block; text-align: center; margin-bottom: 2rem; }
        .rj-main { display: flex; align-items: center; justify-content: center; padding: 2.5rem 1.5rem; background: #fff; }
        .rj-wrap { width: 100%; max-width: 26rem; }
        .rj-form > * + * { margin-top: 1.1rem; }
        .rj-back-wrap { margin-top: 1.75rem; text-align: center; }

        @media (min-width: 1024px) {
            .rj-split { grid-template-columns: 1fr 1fr; }
            .rj-aside {
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                position: sticky;
                top: 0;
                height: 100vh;
                padding: 3rem;
                color: #fff;
                overflow: hidden;
            }
            .rj-mobile-logo { display: none; }
        }

        .rj-badge {
            position: relative;
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            margin-bottom: 1.5rem;
        }
        .rj-badge svg { width: 1.6rem; height: 1.6rem; }
        .rj-badge::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            border: 2px solid rgba(168, 85, 247, .55);
            animation: rj-ring 2.4s ease-out infinite;
        }
        @keyframes rj-ring {
            0% { transform: scale(1); opacity: .9; }
            100% { transform: scale(1.6); opacity: 0; }
        }

        .rj-email-chip {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            max-width: 100%;
            padding: .6rem .9rem;
            border-radius: .75rem;
            background: #f5f3ff;
            border: 1px solid #ddd6fe;
            color: #5b21b6;
            font-size: .875rem;
            font-weight: 600;
            word-break: break-all;
        }
        .rj-email-chip svg { width: 1.1rem; height: 1.1rem; flex-shrink: 0; }

        .rj-success {
            display: flex;
            align-items: flex-start;
            gap: .65rem;
            padding: .85rem 1rem;
            border-radius: .75rem;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #15803d;
            font-size: .875rem;
            font-weight: 500;
            line-height: 1.5;
        }
        .rj-success svg { width: 1.25rem; height: 1.25rem; flex-shrink: 0; margin-top: .05rem; }

        .rj-steps { list-style: none; margin: 0; padding: 0; }
        .rj-steps li { display: flex; align-items: center; gap: .75rem; font-size: .875rem; color: #4b5563; }
        .rj-steps li + li { margin-top: .6rem; }
        .rj-step-n {
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .75rem;
            font-weight: 700;
            color: #5b21b6;
            background: #ede9fe;
            flex-shrink: 0;
        }

        .rj-rise { opacity: 0; transform: translateY(18px); animation: rj-rise .65s cubic-bezier(.22, 1, .36, 1) forwards; }
        @keyframes rj-rise { to { opacity: 1; transform: translateY(0); } }

        .rj-bob { animation: rj-bob 5s ease-in-out infinite; }
        .rj-bob-2 { animation-delay: -1.2s; }
        .rj-bob-3 { animation-delay: -2.4s; }
        .rj-bob-4 { animation-delay: -3.6s; }
        .rj-bob-5 { animation-delay: -.6s; animation-duration: 4s; }
        @keyframes rj-bob {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        .rj-svg.is-cheer .rj-bob { animation: rj-jump .7s cubic-bezier(.3, .7, .4, 1) both; animation-delay: 0s; }
        .rj-svg.is-cheer .rj-bob-2 { animation-delay: .08s; }
        .rj-svg.is-cheer .rj-bob-3 { animation-delay: .16s; }
        .rj-svg.is-cheer .rj-bob-4 { animation-delay: .24s; }
        @keyframes rj-jump {
            0% { transform: translateY(0); }
            35% { transform: translateY(-28px); }
            60% { transform: translateY(0); }
            78% { transform: translateY(-9px); }
            100% { transform: translateY(0); }
        }

        .rj-char {
            transform-box: fill-box;
            transform-origin: 50% 100%;
            transition: transform .55s cubic-bezier(.22, 1, .36, 1);
        }
        .rj-svg.is-resend .rj-char-a { transform: scaleY(1.03); }
        .rj-svg.is-resend .rj-char-b { transform: scaleY(1.04); }
        .rj-svg.is-resend .rj-char-c { transform: scale(1.05); }
        .rj-svg.is-resend .rj-char-d { transform: scaleY(1.04); }
        .rj-svg.is-logout .rj-char-a { transform: skewX(-3deg); }
        .rj-svg.is-logout .rj-char-b { transform: skewX(3deg); }
        .rj-svg.is-logout .rj-char-c { transform: translateX(5px); }

        /* Envelope */
        .rj-mail { transform-box: fill-box; transform-origin: center; transition: transform .45s cubic-bezier(.3, 1.5, .5, 1); }
        .rj-svg.is-resend .rj-mail { transform: scale(1.08) rotate(-4deg); }
        .rj-flap {
            transform-box: fill-box;
            transform-origin: 50% 0%;
            transition: transform .5s cubic-bezier(.3, 1.4, .5, 1);
        }
        .rj-svg.is-open .rj-flap { transform: scaleY(-1); }
        .rj-letter { transition: transform .55s cubic-bezier(.3, 1.4, .5, 1) .1s; }
        .rj-svg.is-open .rj-letter { transform: translateY(-24px); }
        .rj-mail-body { transition: filter .3s ease; }
        .rj-svg.is-open .rj-mail-body { filter: drop-shadow(0 0 12px rgba(255, 255, 255, .75)); }
        .rj-stamp { fill: #5b21b6; transition: fill .3s ease; }
        .rj-svg.is-open .rj-stamp { fill: #22c55e; }

        .rj-face { transition: transform .35s cubic-bezier(.22, 1, .36, 1); }
        .rj-pupil { transition: transform .14s ease-out, opacity .12s ease; }
        .rj-white { transition: opacity .12s ease; }
        .rj-lid { opacity: 0; transition: opacity .12s ease; }
        .rj-svg.is-closed .rj-pupil,
        .rj-svg.is-closed .rj-white { opacity: 0; }
        .rj-svg.is-closed .rj-lid { opacity: 1; }

        .rj-btn { position: relative; overflow: hidden; }
        .rj-btn:disabled { cursor: not-allowed; }
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
        .rj-btn:disabled::after { display: none; }
        @keyframes rj-shine {
            0%, 55% { left: -120%; }
            100% { left: 160%; }
        }

        .rj-hidden { display: none; }
        .rj-spin { animation: rj-spin .8s linear infinite; }
        @keyframes rj-spin { to { transform: rotate(360deg); } }

        .rj-logout {
            background: none;
            border: 0;
            cursor: pointer;
            font-family: inherit;
        }
        .rj-arrow { transition: transform .25s ease; }
        .rj-logout:hover .rj-arrow { transform: translateX(-4px); }

        @media (prefers-reduced-motion: reduce) {
            .rj-bob, .rj-btn::after, .rj-spin, .rj-badge::after { animation: none !important; }
            .rj-rise { animation: none; opacity: 1; transform: none; }
            .rj-face, .rj-pupil, .rj-char, .rj-flap, .rj-letter, .rj-mail { transition: none; }
        }
    </style>
</head>
<body class="min-h-full text-gray-900 antialiased">
    @php
        $logoMask = rj_logo_mask_url();
        $linkSent = session('status') == 'verification-link-sent';
    @endphp

    <div class="rj-split">

        <aside class="rj-aside bg-gradient-to-br from-rj-purple to-rj-pink">
            <a href="{{ url('/') }}" class="inline-flex items-center self-start">
                @if ($logoMask)
                    <div class="h-10 w-36 bg-white" style="-webkit-mask-image: url('{{ $logoMask }}'); mask-image: url('{{ $logoMask }}'); -webkit-mask-size: contain; mask-size: contain; -webkit-mask-repeat: no-repeat; mask-repeat: no-repeat; -webkit-mask-position: left center; mask-position: left center;"></div>
                @else
                    <span class="text-2xl font-black tracking-tight" style="color:#fff">{{ rj_get_option('site_name', 'RJ CMS') }}</span>
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
                                <path class="rj-mouth" data-cx="130" data-cy="160" data-w="14" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="color:#fff"/>
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
                                <path class="rj-mouth" data-cx="240" data-cy="250" data-w="11" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="color:#fff"/>
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
                                <path class="rj-mouth" data-cx="105" data-cy="358" data-w="12" fill="none" stroke="#111827" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="color:#111827"/>
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
                                <path class="rj-mouth" data-cx="345" data-cy="310" data-w="10" fill="none" stroke="#111827" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="color:#111827"/>
                            </g>
                        </g>
                    </g>

                    {{-- Envelope --}}
                    <g transform="translate(300 50)">
                        <g class="rj-bob rj-bob-5">
                            <g class="rj-mail">
                                <rect class="rj-letter" x="10" y="28" width="56" height="40" rx="6" fill="#ddd6fe"/>
                                <rect class="rj-letter" x="18" y="36" width="30" height="4" rx="2" fill="#7c3aed" opacity=".55"/>
                                <rect class="rj-letter" x="18" y="45" width="40" height="4" rx="2" fill="#7c3aed" opacity=".35"/>
                                <g class="rj-mail-body">
                                    <rect x="0" y="24" width="76" height="52" rx="12" fill="#fff"/>
                                    <path d="M4 34 L38 58 L72 34" fill="none" stroke="#ddd6fe" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle class="rj-stamp" cx="38" cy="58" r="6"/>
                                </g>
                                <path class="rj-flap" d="M2 26 H74 L38 52 Z" fill="#ede9fe" stroke="#fff" stroke-width="2" stroke-linejoin="round"/>
                            </g>
                        </g>
                    </g>
                </svg>
            </div>

            <p class="text-lg font-semibold max-w-sm leading-snug" style="color:rgba(255,255,255,.92)">{{ __('Everything you need to manage your site, in one place.') }}</p>
        </aside>

        <main class="rj-main">
            <div class="rj-wrap">

                <div class="rj-mobile-logo rj-rise" style="animation-delay:.05s">
                    <a href="{{ url('/') }}" class="inline-flex items-center justify-center">
                        @if ($logoMask)
                            <div class="h-12 w-40 bg-rj-dark-1" style="-webkit-mask-image: url('{{ $logoMask }}'); mask-image: url('{{ $logoMask }}'); -webkit-mask-size: contain; mask-size: contain; -webkit-mask-repeat: no-repeat; mask-repeat: no-repeat; -webkit-mask-position: center; mask-position: center;"></div>
                        @else
                            <span class="text-3xl font-black bg-clip-text text-transparent bg-gradient-to-r from-rj-purple to-rj-pink">{{ rj_get_option('site_name', 'RJ CMS') }}</span>
                        @endif
                    </a>
                </div>

                <div class="rj-rise" style="animation-delay:.1s">
                    <div class="rj-badge bg-gradient-to-br from-rj-purple to-rj-pink">
                        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                    </div>
                    <h1 class="text-3xl font-extrabold text-gray-900" style="margin:0">{{ __('Verify Your Email') }}</h1>
                    <p class="text-sm text-gray-500" style="margin:.75rem 0 1.25rem; line-height:1.6">{{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}</p>
                </div>

                @if (auth()->check())
                    <div class="rj-rise" style="animation-delay:.16s; margin-bottom:1.25rem">
                        <span class="rj-email-chip">
                            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                            {{ auth()->user()->email }}
                        </span>
                    </div>
                @endif

                @if ($linkSent)
                    <div class="rj-success rj-rise" style="animation-delay:.2s; margin-bottom:1.25rem" role="status">
                        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ __('A new verification link has been sent to the email address you provided during registration.') }}</span>
                    </div>
                @endif

                <div class="rj-rise" style="animation-delay:.24s; margin-bottom:1.5rem">
                    <ul class="rj-steps">
                        <li><span class="rj-step-n">1</span>{{ __('Open your inbox') }}</li>
                        <li><span class="rj-step-n">2</span>{{ __('Click the verification link') }}</li>
                        <li><span class="rj-step-n">3</span>{{ __('Check your spam folder if you can\'t find it') }}</li>
                    </ul>
                </div>

                <form method="POST" action="{{ route('verification.send') }}" class="rj-form" id="rj-verify-form">
                    @csrf

                    <div class="rj-rise" style="animation-delay:.32s">
                        <button type="submit" id="rj-submit" class="rj-btn w-full flex justify-center items-center gap-2 py-3.5 px-4 rounded-xl shadow-lg text-sm font-bold text-white bg-gradient-to-r from-rj-purple to-rj-pink hover:opacity-95 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rj-purple transition-all active:scale-[.98]">
                            <svg id="rj-spinner" class="rj-spin rj-hidden" style="width:1rem;height:1rem" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                            <span id="rj-submit-text">{{ __('Resend Verification Email') }}</span>
                        </button>
                    </div>
                </form>

                <div class="rj-back-wrap rj-rise" style="animation-delay:.4s">
                    <form method="POST" action="{{ route('logout') }}" id="rj-logout-form">
                        @csrf
                        <button type="submit" id="rj-logout" class="rj-logout text-sm font-medium text-gray-500 hover:text-gray-900 transition inline-flex items-center gap-1.5">
                            <svg class="rj-arrow" style="width:1rem;height:1rem" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        (function () {
            var svg = document.getElementById('rj-chars');
            var submit = document.getElementById('rj-submit');
            var logout = document.getElementById('rj-logout');
            var form = document.getElementById('rj-verify-form');
            var spinner = document.getElementById('rj-spinner');
            var submitText = document.getElementById('rj-submit-text');
            var linkSent = @json($linkSent);
            var resendLabel = @json(__('Resend Verification Email'));
            var sendingLabel = @json(__('Sending...'));
            var waitLabel = @json(__('Resend in'));
            var COOLDOWN = 60;

            var eyes = svg ? [].slice.call(svg.querySelectorAll('.rj-eye')) : [];
            var faces = svg ? [].slice.call(svg.querySelectorAll('.rj-face')) : [];
            var mouths = svg ? [].slice.call(svg.querySelectorAll('.rj-mouth')) : [];
            var mouse = null;
            var blinking = false;
            var hoverSubmit = false;
            var hoverLogout = false;
            var submitting = false;
            var currentMood = '';

            function pulse(el, cls, ms) {
                if (!el) { return; }
                el.classList.remove(cls);
                void el.getBoundingClientRect();
                el.classList.add(cls);
                setTimeout(function () { el.classList.remove(cls); }, ms);
            }

            function mouthPath(cx, cy, w, kind) {
                var l = cx - w;
                var r = cx + w;
                if (kind === 'flat') { return 'M' + l + ' ' + cy + ' L' + r + ' ' + cy; }
                if (kind === 'sad') { return 'M' + l + ' ' + (cy + 5) + ' Q' + cx + ' ' + (cy - w * 0.55 + 5) + ' ' + r + ' ' + (cy + 5); }
                if (kind === 'smile') { return 'M' + l + ' ' + cy + ' Q' + cx + ' ' + (cy + w * 0.75) + ' ' + r + ' ' + cy; }
                if (kind === 'big') { return 'M' + (l - 2) + ' ' + cy + ' Q' + cx + ' ' + (cy + w * 1.5) + ' ' + (r + 2) + ' ' + cy + ' Z'; }
                return 'M' + l + ' ' + cy + ' Q' + cx + ' ' + (cy + w * 0.4) + ' ' + r + ' ' + cy;
            }

            function setMood(kind) {
                if (kind === currentMood) { return; }
                currentMood = kind;
                mouths.forEach(function (m) {
                    var cx = parseFloat(m.getAttribute('data-cx'));
                    var cy = parseFloat(m.getAttribute('data-cy'));
                    var w = parseFloat(m.getAttribute('data-w'));
                    m.setAttribute('d', mouthPath(cx, cy, w, kind));
                    m.setAttribute('fill', kind === 'big' ? 'currentColor' : 'none');
                });
            }

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

            function centerOf(el) {
                var r = el.getBoundingClientRect();
                return { x: r.left + r.width / 2, y: r.top + r.height / 2 };
            }

            function state() {
                if (submitting) { return 'resend'; }
                if (hoverSubmit || document.activeElement === submit) { return 'resend'; }
                if (hoverLogout || document.activeElement === logout) { return 'logout'; }
                return 'idle';
            }

            function moodFor(s) {
                if (submitting) { return 'big'; }
                if (s === 'logout') { return 'sad'; }
                if (s === 'resend') { return 'smile'; }
                if (linkSent) { return 'smile'; }
                return 'neutral';
            }

            function render() {
                if (!svg) { return; }
                var s = state();

                svg.classList.remove('is-resend', 'is-logout');
                if (s !== 'idle') { svg.classList.add('is-' + s); }
                svg.classList.toggle('is-closed', blinking);
                svg.classList.toggle('is-open', submitting || linkSent || s === 'resend');
                setMood(moodFor(s));

                var box = svg.getBoundingClientRect();
                var sideX = box.left + box.width / 2;

                if (s === 'resend' || s === 'logout') {
                    var t = centerOf(s === 'resend' ? submit : logout);
                    shiftFaces(t.x >= sideX ? 6 : -6, 5);
                    lookAt(t.x, t.y);
                } else {
                    var p = mouse || { x: box.right + 400, y: box.top + box.height / 2 };
                    shiftFaces(Math.max(-6, Math.min(6, (p.x - sideX) / 120)), 0);
                    lookAt(p.x, p.y);
                }
            }

            [submit, logout].forEach(function (el) {
                el.addEventListener('focus', render);
                el.addEventListener('blur', function () { setTimeout(render, 0); });
            });
            submit.addEventListener('mouseenter', function () { hoverSubmit = true; render(); });
            submit.addEventListener('mouseleave', function () { hoverSubmit = false; render(); });
            logout.addEventListener('mouseenter', function () { hoverLogout = true; render(); });
            logout.addEventListener('mouseleave', function () { hoverLogout = false; render(); });

            window.addEventListener('resize', render);
            document.addEventListener('mousemove', function (e) {
                mouse = { x: e.clientX, y: e.clientY };
                if (state() === 'idle') { render(); }
            });

            function blink() {
                blinking = true;
                render();
                setTimeout(function () { blinking = false; render(); }, 150);
                setTimeout(blink, 2600 + Math.random() * 2800);
            }
            setTimeout(blink, 2200);

            // Cooldown after the link was sent
            function startCooldown(seconds) {
                var left = seconds;
                submit.disabled = true;
                submit.classList.add('opacity-60');
                (function tick() {
                    if (left <= 0) {
                        submit.disabled = false;
                        submit.classList.remove('opacity-60');
                        submitText.textContent = resendLabel;
                        return;
                    }
                    submitText.textContent = waitLabel + ' ' + left + 's';
                    left--;
                    setTimeout(tick, 1000);
                })();
            }

            if (linkSent && svg) {
                pulse(svg, 'is-cheer', 1000);
                startCooldown(COOLDOWN);
            }

            form.addEventListener('submit', function (e) {
                if (submitting) { return; }
                e.preventDefault();
                submitting = true;
                spinner.classList.remove('rj-hidden');
                submitText.textContent = sendingLabel;
                submit.disabled = true;
                submit.classList.add('opacity-80', 'cursor-wait');
                pulse(svg, 'is-cheer', 1000);
                render();
                setTimeout(function () { form.submit(); }, 900);
            });

            render();
        })();
    </script>

</body>
</html>
