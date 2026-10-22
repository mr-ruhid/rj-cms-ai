<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ rj_dir() }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Forgot password?') }} — {{ rj_get_option('site_name', config('app.name')) }}</title>

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
        .rj-form > * + * { margin-top: 1.25rem; }
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

        .rj-status {
            display: flex;
            align-items: flex-start;
            gap: .6rem;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #15803d;
            border-radius: .75rem;
            padding: .8rem 1rem;
            font-size: .875rem;
            line-height: 1.5;
            margin-bottom: 1.25rem;
        }
        .rj-status svg { width: 1.25rem; height: 1.25rem; flex-shrink: 0; margin-top: .05rem; }

        .rj-label { display: block; font-size: .875rem; font-weight: 500; color: #374151; margin-bottom: .375rem; }
        .rj-input-wrap { position: relative; }
        input.rj-input {
            width: 100%;
            box-sizing: border-box;
            border-radius: .75rem;
            border: 1px solid #d1d5db;
            padding-top: .75rem;
            padding-bottom: .75rem;
            font-size: 1rem;
            background: #fff;
            color: #111827;
            transition: box-shadow .25s ease, border-color .25s ease;
        }
        input.rj-input:focus { box-shadow: 0 10px 24px -12px rgba(168, 85, 247, .5); }

        .rj-field-icon {
            position: absolute;
            top: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            color: #9ca3af;
            inset-inline-start: 0;
            padding-inline-start: 1rem;
            pointer-events: none;
        }
        .rj-field-icon svg { width: 1.25rem; height: 1.25rem; flex-shrink: 0; }
        input.rj-has-icon { padding-inline-start: 3rem; padding-inline-end: 1rem; }
        .rj-hidden { display: none; }

        .rj-err { margin: .5rem 0 0; font-size: .8125rem; color: #dc2626; }

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

        .rj-svg.is-error .rj-bob { animation: rj-nope .55s cubic-bezier(.36, .07, .19, .97) both; animation-delay: 0s; }
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
        .rj-svg.is-confused .rj-char-a { transform: skewX(2deg) rotate(-1.5deg); }
        .rj-svg.is-confused .rj-char-b { transform: skewX(-2deg) rotate(1.5deg); }
        .rj-svg.is-confused .rj-char-d { transform: rotate(-3deg); }
        .rj-svg.is-email .rj-char-a { transform: skewX(-3deg); }
        .rj-svg.is-email .rj-char-b { transform: skewX(3deg); }
        .rj-svg.is-email .rj-char-c { transform: translateX(5px); }
        .rj-svg.is-submit .rj-char-a { transform: scaleY(1.03); }
        .rj-svg.is-submit .rj-char-b { transform: scaleY(1.04); }
        .rj-svg.is-submit .rj-char-c { transform: scale(1.05); }
        .rj-svg.is-submit .rj-char-d { transform: scaleY(1.04); }

        .rj-q {
            font-family: 'Figtree', sans-serif;
            font-weight: 800;
            fill: #fff;
            opacity: 0;
            transform-box: fill-box;
            transform-origin: center;
            transition: opacity .35s ease;
            animation: rj-wob 3s ease-in-out infinite;
        }
        .rj-svg.is-confused .rj-q { opacity: .92; }
        @keyframes rj-wob {
            0%, 100% { transform: translateY(0) rotate(-8deg); }
            50% { transform: translateY(-7px) rotate(8deg); }
        }

        .rj-mail { transform-box: fill-box; transform-origin: center; transition: filter .3s ease; }
        .rj-svg.is-valid .rj-mail { filter: drop-shadow(0 0 10px rgba(255, 255, 255, .7)); }
        .rj-svg.is-sending .rj-mail { animation: rj-fly 1s cubic-bezier(.5, 0, .8, .4) forwards; }
        @keyframes rj-fly {
            0% { transform: translate(0, 0) scale(1) rotate(0); opacity: 1; }
            20% { transform: translate(-6px, 6px) scale(.95) rotate(-4deg); opacity: 1; }
            100% { transform: translate(150px, -120px) scale(.35) rotate(18deg); opacity: 0; }
        }
        .rj-svg.is-sent .rj-mail { animation: rj-pop .6s cubic-bezier(.3, 1.6, .5, 1) both; }
        @keyframes rj-pop {
            0% { transform: scale(.6); }
            100% { transform: scale(1); }
        }

        .rj-tag { opacity: 0; transform-box: fill-box; transform-origin: center; transform: scale(0); transition: transform .4s cubic-bezier(.3, 1.6, .5, 1), opacity .2s ease; }
        .rj-svg.is-sent .rj-tag-ok { opacity: 1; transform: scale(1); }
        .rj-svg.is-denied .rj-tag-no { opacity: 1; transform: scale(1); }

        .rj-face { transition: transform .35s cubic-bezier(.22, 1, .36, 1); }
        .rj-pupil { transition: transform .14s ease-out, opacity .12s ease; }
        .rj-white { transition: opacity .12s ease; }
        .rj-lid { opacity: 0; transition: opacity .12s ease; }
        .rj-svg.is-closed .rj-pupil,
        .rj-svg.is-closed .rj-white { opacity: 0; }
        .rj-svg.is-closed .rj-lid { opacity: 1; }

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

        .rj-spin { animation: rj-spin .8s linear infinite; }
        @keyframes rj-spin { to { transform: rotate(360deg); } }

        .rj-arrow { transition: transform .25s ease; }
        .rj-back:hover .rj-arrow { transform: translateX(-4px); }

        @media (prefers-reduced-motion: reduce) {
            .rj-bob, .rj-btn::after, .rj-spin, .rj-badge::after, .rj-q { animation: none !important; }
            .rj-rise { animation: none; opacity: 1; transform: none; }
            .rj-face, .rj-pupil, .rj-char { transition: none; }
        }
    </style>
</head>
<body class="min-h-full text-gray-900 antialiased">
    @php $logoMask = rj_logo_mask_url(); @endphp

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

                    <text class="rj-q" x="92" y="38" font-size="40" style="animation-delay:-.4s">?</text>
                    <text class="rj-q" x="168" y="30" font-size="30" style="animation-delay:-1.6s">?</text>
                    <text class="rj-q" x="232" y="148" font-size="34" style="animation-delay:-2.2s">?</text>
                    <text class="rj-q" x="392" y="214" font-size="34" style="animation-delay:-1s">?</text>

                    <g transform="translate(285 62)">
                        <g class="rj-bob rj-bob-5">
                            <g class="rj-mail">
                                <rect width="92" height="64" rx="10" fill="#fff"/>
                                <path d="M5 8 L46 38 L87 8" fill="none" stroke="#c4b5fd" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M5 58 L34 31 M87 58 L58 31" fill="none" stroke="#e5e7eb" stroke-width="3" stroke-linecap="round"/>
                                <g class="rj-tag rj-tag-ok">
                                    <circle cx="88" cy="60" r="16" fill="#22c55e"/>
                                    <path d="M80 60 l6 6 l11 -12" fill="none" stroke="#fff" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </g>
                                <g class="rj-tag rj-tag-no">
                                    <circle cx="88" cy="60" r="16" fill="#ef4444"/>
                                    <path d="M81 53 l14 14 M95 53 l-14 14" fill="none" stroke="#fff" stroke-width="3.5" stroke-linecap="round"/>
                                </g>
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
                        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 9v.906a2.25 2.25 0 01-1.183 1.981l-6.478 3.488a2.25 2.25 0 01-2.18 0l-6.478-3.488A2.25 2.25 0 012.25 9.906V9m19.5 0a2.25 2.25 0 00-1.183-1.981l-6.478-3.488a2.25 2.25 0 00-2.18 0l-6.478 3.488A2.25 2.25 0 002.25 9m19.5 0v8.25A2.25 2.25 0 0119.5 19.5h-15a2.25 2.25 0 01-2.25-2.25V9"/></svg>
                    </div>
                    <h1 class="text-3xl font-extrabold text-gray-900" style="margin:0">{{ __('Forgot password?') }}</h1>
                    <p class="text-sm text-gray-500" style="margin:.75rem 0 1.75rem; line-height:1.6">{{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}</p>
                </div>

                @if (session('status'))
                    <div class="rj-status rj-rise" style="animation-delay:.15s">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="rj-form" id="rj-forgot-form">
                    @csrf

                    <div class="rj-rise" style="animation-delay:.2s">
                        <label for="email" class="rj-label">{{ __('Email') }}</label>
                        <div class="rj-input-wrap">
                            <span class="rj-field-icon">
                                <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                            </span>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                   class="rj-input rj-has-icon focus:border-rj-purple focus:ring-rj-purple" placeholder="name@example.com">
                        </div>
                        @error('email')<p class="rj-err">{{ $message }}</p>@enderror
                    </div>

                    <div class="rj-rise" style="animation-delay:.3s">
                        <button type="submit" id="rj-submit" class="rj-btn w-full flex justify-center items-center gap-2 py-3.5 px-4 rounded-xl shadow-lg text-sm font-bold text-white bg-gradient-to-r from-rj-purple to-rj-pink hover:opacity-95 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rj-purple transition-all active:scale-[.98]">
                            <svg id="rj-spinner" class="rj-spin rj-hidden" style="width:1rem;height:1rem" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                            <span id="rj-submit-text">{{ __('Email Password Reset Link') }}</span>
                        </button>
                    </div>
                </form>

                <div class="rj-back-wrap rj-rise" style="animation-delay:.4s">
                    <a href="{{ route('login') }}" class="rj-back text-sm font-medium text-gray-500 hover:text-gray-900 transition inline-flex items-center gap-1.5">
                        <svg class="rj-arrow" style="width:1rem;height:1rem" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        {{ __('Back to sign in') }}
                    </a>
                </div>
            </div>
        </main>
    </div>

    <script>
        (function () {
            var svg = document.getElementById('rj-chars');
            var email = document.getElementById('email');
            var submit = document.getElementById('rj-submit');
            var form = document.getElementById('rj-forgot-form');
            var spinner = document.getElementById('rj-spinner');
            var submitText = document.getElementById('rj-submit-text');
            var hasErrors = @json($errors->has('email'));
            var wasSent = @json(session('status') !== null);

            var eyes = svg ? [].slice.call(svg.querySelectorAll('.rj-eye')) : [];
            var faces = svg ? [].slice.call(svg.querySelectorAll('.rj-face')) : [];
            var mouths = svg ? [].slice.call(svg.querySelectorAll('.rj-mouth')) : [];
            var mouse = null;
            var blinking = false;
            var hoverSubmit = false;
            var sending = false;
            var touched = false;
            var currentMood = '';

            function isRtl() { return document.documentElement.dir === 'rtl'; }

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

            function caretTarget(input) {
                var r = input.getBoundingClientRect();
                var travel = Math.min(input.value.length * 8.5, Math.max(r.width - 80, 0));
                var x = isRtl() ? r.right - 48 - travel : r.left + 48 + travel;
                return { x: x, y: r.top + r.height / 2 };
            }

            function centerOf(el) {
                var r = el.getBoundingClientRect();
                return { x: r.left + r.width / 2, y: r.top + r.height / 2 };
            }

            function state() {
                if (sending) { return 'sending'; }
                if (hoverSubmit || document.activeElement === submit) { return 'submit'; }
                if (document.activeElement === email) { return 'email'; }
                return 'idle';
            }

            function moodFor(s) {
                if (sending) { return 'big'; }
                if (!touched && wasSent) { return 'big'; }
                if (!touched && hasErrors) { return 'sad'; }
                if (s === 'submit') { return 'smile'; }
                if (email.value) { return email.validity.valid ? 'smile' : 'neutral'; }
                return 'flat';
            }

            function render() {
                if (!svg) { return; }
                var s = state();
                var empty = !email.value;
                var valid = !empty && email.validity.valid;

                svg.classList.toggle('is-email', s === 'email');
                svg.classList.toggle('is-submit', s === 'submit' || s === 'sending');
                svg.classList.toggle('is-confused', empty && !sending && !(wasSent && !touched));
                svg.classList.toggle('is-valid', valid && !sending);
                svg.classList.toggle('is-closed', blinking);
                setMood(moodFor(s));

                var box = svg.getBoundingClientRect();
                var sideX = box.left + box.width / 2;

                if (s === 'sending') {
                    shiftFaces(6, -3);
                    lookAt(box.right + 200, box.top - 120);
                } else if (s === 'submit') {
                    var t = centerOf(submit);
                    shiftFaces(t.x >= sideX ? 6 : -6, 5);
                    lookAt(t.x, t.y);
                } else if (s === 'email') {
                    var c = caretTarget(email);
                    var ratio = Math.min(email.value.length / 28, 1);
                    shiftFaces((c.x >= sideX ? 1 : -1) * (5 + ratio * 3), 2 + ratio * 3);
                    lookAt(c.x, c.y + 12 + ratio * 24);
                } else {
                    var p = mouse || { x: box.right + 400, y: box.top + box.height / 2 };
                    shiftFaces(Math.max(-6, Math.min(6, (p.x - sideX) / 120)), 0);
                    lookAt(p.x, p.y);
                }
            }

            [email, submit].forEach(function (el) {
                el.addEventListener('focus', render);
                el.addEventListener('blur', function () { setTimeout(render, 0); });
            });
            email.addEventListener('keyup', render);
            email.addEventListener('click', render);
            email.addEventListener('input', function () {
                touched = true;
                svg.classList.remove('is-sent', 'is-denied');
                render();
            });

            submit.addEventListener('mouseenter', function () { hoverSubmit = true; render(); });
            submit.addEventListener('mouseleave', function () { hoverSubmit = false; render(); });

            window.addEventListener('resize', render);
            document.addEventListener('mousemove', function (e) {
                mouse = { x: e.clientX, y: e.clientY };
                if (state() === 'idle') { render(); }
            });

            function blink() {
                if (!sending) {
                    blinking = true;
                    render();
                    setTimeout(function () { blinking = false; render(); }, 150);
                }
                setTimeout(blink, 2600 + Math.random() * 2800);
            }
            setTimeout(blink, 2200);

            if (svg && wasSent) {
                svg.classList.add('is-sent');
                pulse(svg, 'is-cheer', 1000);
            } else if (svg && hasErrors) {
                svg.classList.add('is-denied');
                pulse(svg, 'is-error', 900);
            }

            form.addEventListener('submit', function (e) {
                if (sending) { return; }
                if (!form.checkValidity()) { return; }
                e.preventDefault();
                sending = true;
                spinner.classList.remove('rj-hidden');
                submitText.textContent = @json(__('Sending...'));
                submit.disabled = true;
                submit.classList.add('opacity-80', 'cursor-wait');
                svg.classList.remove('is-sent', 'is-denied');
                svg.classList.add('is-sending');
                setTimeout(function () { pulse(svg, 'is-cheer', 1000); }, 500);
                render();
                setTimeout(function () { form.submit(); }, 1000);
            });

            render();
        })();
    </script>

</body>
</html>
