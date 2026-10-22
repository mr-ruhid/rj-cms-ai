<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ rj_dir() }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Create your account') }} — {{ rj_get_option('site_name', config('app.name')) }}</title>

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
        .rj-head { margin-bottom: 1.75rem; }
        .rj-form > * + * { margin-top: 1.1rem; }
        .rj-foot { margin-top: 1.75rem; text-align: center; font-size: .875rem; color: #4b5563; }
        .rj-back-wrap { margin-top: 1.5rem; text-align: center; }

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
        .rj-field-toggle:hover { color: #4b5563; }
        .rj-field-icon svg, .rj-field-toggle svg { width: 1.25rem; height: 1.25rem; flex-shrink: 0; }
        input.rj-has-icon { padding-inline-start: 3rem; padding-inline-end: 1rem; }
        input.rj-has-icon.rj-has-toggle { padding-inline-end: 3rem; }
        .rj-hidden { display: none; }

        .rj-err { margin: .5rem 0 0; font-size: .8125rem; color: #dc2626; }

        .rj-meter { display: flex; align-items: center; gap: .375rem; margin-top: .6rem; opacity: 0; transition: opacity .25s ease; }
        .rj-meter.on { opacity: 1; }
        .rj-bar { flex: 1; height: .3rem; border-radius: 9999px; background: #e5e7eb; transition: background .3s ease; }
        .rj-meter-label { font-size: .75rem; font-weight: 700; min-width: 3.5rem; text-align: end; transition: color .3s ease; }

        .rj-check { display: flex; align-items: flex-start; gap: .5rem; font-size: .875rem; color: #4b5563; cursor: pointer; }
        .rj-check input { margin-top: .2rem; flex-shrink: 0; }

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

        .rj-svg .rj-bob.is-hop { animation: rj-hop .32s ease both; animation-delay: 0s; }
        @keyframes rj-hop {
            0% { transform: translateY(0); }
            45% { transform: translateY(-12px); }
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
        .rj-svg.is-name .rj-char-a { transform: skewX(-4deg); }
        .rj-svg.is-name .rj-char-d { transform: translateX(5px) rotate(3deg); }
        .rj-svg.is-email .rj-char-a { transform: skewX(-3deg); }
        .rj-svg.is-email .rj-char-b { transform: skewX(3deg); }
        .rj-svg.is-email .rj-char-c { transform: translateX(5px); }
        .rj-svg.is-pass .rj-char-a { transform: skewX(4deg); }
        .rj-svg.is-pass .rj-char-b { transform: skewX(-3deg); }
        .rj-svg.is-pass .rj-char-d { transform: translateX(-4px) rotate(-3deg); }
        .rj-svg.is-conf .rj-char-a { transform: skewX(5deg); }
        .rj-svg.is-conf .rj-char-b { transform: skewX(-4deg); }
        .rj-svg.is-conf .rj-char-c { transform: translateX(-5px); }
        .rj-svg.is-conf .rj-char-d { transform: translateX(-5px) rotate(-3deg); }
        .rj-svg.is-terms .rj-char-a { transform: rotate(-2deg); }
        .rj-svg.is-terms .rj-char-b { transform: rotate(2deg); }
        .rj-svg.is-terms .rj-char-c { transform: scale(1.04); }
        .rj-svg.is-submit .rj-char-a { transform: scaleY(1.03); }
        .rj-svg.is-submit .rj-char-b { transform: scaleY(1.04); }
        .rj-svg.is-submit .rj-char-c { transform: scale(1.05); }
        .rj-svg.is-submit .rj-char-d { transform: scaleY(1.04); }

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
            .rj-bob, .rj-btn::after, .rj-spin { animation: none !important; }
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

                <div class="rj-head rj-rise" style="animation-delay:.1s">
                    <h1 class="text-3xl font-extrabold text-gray-900" style="margin:0">{{ __('Create your account') }}</h1>
                    <p class="text-sm text-gray-500" style="margin:.5rem 0 0">{{ __('Join us and start building today') }}</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="rj-form" id="rj-register-form">
                    @csrf

                    <div class="rj-rise" style="animation-delay:.15s">
                        <label for="name" class="rj-label">{{ __('Name') }}</label>
                        <div class="rj-input-wrap">
                            <span class="rj-field-icon">
                                <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                            </span>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                                   class="rj-input rj-has-icon focus:border-rj-purple focus:ring-rj-purple" placeholder="John Doe">
                        </div>
                        @error('name')<p class="rj-err">{{ $message }}</p>@enderror
                    </div>

                    <div class="rj-rise" style="animation-delay:.22s">
                        <label for="email" class="rj-label">{{ __('Email') }}</label>
                        <div class="rj-input-wrap">
                            <span class="rj-field-icon">
                                <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                            </span>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                                   class="rj-input rj-has-icon focus:border-rj-purple focus:ring-rj-purple" placeholder="you@example.com">
                        </div>
                        @error('email')<p class="rj-err">{{ $message }}</p>@enderror
                    </div>

                    <div class="rj-rise" style="animation-delay:.29s">
                        <label for="password" class="rj-label">{{ __('Password') }}</label>
                        <div class="rj-input-wrap">
                            <span class="rj-field-icon">
                                <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                            </span>
                            <input id="password" type="password" name="password" required autocomplete="new-password"
                                   class="rj-input rj-has-icon rj-has-toggle focus:border-rj-purple focus:ring-rj-purple" placeholder="••••••••">
                            <button type="button" class="rj-field-toggle" data-toggle data-for="password" aria-label="{{ __('Show password') }}">
                                <svg class="rj-eye-on" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <svg class="rj-eye-off rj-hidden" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                            </button>
                        </div>
                        <div class="rj-meter" id="rj-meter">
                            <span class="rj-bar"></span><span class="rj-bar"></span><span class="rj-bar"></span><span class="rj-bar"></span>
                            <span class="rj-meter-label" id="rj-meter-label"></span>
                        </div>
                        @error('password')<p class="rj-err">{{ $message }}</p>@enderror
                    </div>

                    <div class="rj-rise" style="animation-delay:.36s">
                        <label for="password_confirmation" class="rj-label">{{ __('Confirm Password') }}</label>
                        <div class="rj-input-wrap">
                            <span class="rj-field-icon">
                                <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                            </span>
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                                   class="rj-input rj-has-icon rj-has-toggle focus:border-rj-purple focus:ring-rj-purple" placeholder="••••••••">
                            <button type="button" class="rj-field-toggle" data-toggle data-for="password_confirmation" aria-label="{{ __('Show password') }}">
                                <svg class="rj-eye-on" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <svg class="rj-eye-off rj-hidden" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                            </button>
                        </div>
                        @error('password_confirmation')<p class="rj-err">{{ $message }}</p>@enderror
                    </div>

                    <div class="rj-rise" style="animation-delay:.43s">
                        <label for="terms" class="rj-check">
                            <input id="terms" type="checkbox" class="rounded border-gray-300 text-rj-purple shadow-sm focus:ring-rj-purple" required>
                            <span>{{ __('I agree to the') }} <a href="#" class="font-semibold text-rj-purple hover:text-rj-pink transition">{{ __('Terms') }}</a> {{ __('and') }} <a href="#" class="font-semibold text-rj-purple hover:text-rj-pink transition">{{ __('Privacy Policy') }}</a></span>
                        </label>
                    </div>

                    <div class="rj-rise" style="animation-delay:.5s">
                        <button type="submit" id="rj-submit" class="rj-btn w-full flex justify-center items-center gap-2 py-3.5 px-4 rounded-xl shadow-lg text-sm font-bold text-white bg-gradient-to-r from-rj-purple to-rj-pink hover:opacity-95 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rj-purple transition-all active:scale-[.98]">
                            <svg id="rj-spinner" class="rj-spin rj-hidden" style="width:1rem;height:1rem" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                            <span id="rj-submit-text">{{ __('Create account') }}</span>
                        </button>
                    </div>
                </form>

                <p class="rj-foot rj-rise" style="animation-delay:.58s">
                    {{ __('Already have an account?') }}
                    <a href="{{ route('login') }}" class="font-bold text-rj-purple hover:text-rj-pink transition" style="margin-inline-start:.25rem">{{ __('Sign in') }}</a>
                </p>

                <div class="rj-back-wrap rj-rise" style="animation-delay:.64s">
                    <a href="{{ url('/') }}" class="rj-back text-sm font-medium text-gray-500 hover:text-gray-900 transition inline-flex items-center gap-1.5">
                        <svg class="rj-arrow" style="width:1rem;height:1rem" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        {{ __('Back to website') }}
                    </a>
                </div>
            </div>
        </main>
    </div>

    <script>
        (function () {
            var svg = document.getElementById('rj-chars');
            var nameI = document.getElementById('name');
            var email = document.getElementById('email');
            var pass = document.getElementById('password');
            var conf = document.getElementById('password_confirmation');
            var terms = document.getElementById('terms');
            var submit = document.getElementById('rj-submit');
            var form = document.getElementById('rj-register-form');
            var spinner = document.getElementById('rj-spinner');
            var submitText = document.getElementById('rj-submit-text');
            var meter = document.getElementById('rj-meter');
            var meterLabel = document.getElementById('rj-meter-label');
            var bars = [].slice.call(meter.querySelectorAll('.rj-bar'));
            var toggles = [].slice.call(document.querySelectorAll('[data-toggle]'));
            var hasErrors = @json($errors->any());
            var labels = @json([__('Weak'), __('Fair'), __('Good'), __('Strong')]);
            var colors = ['#ef4444', '#f97316', '#eab308', '#22c55e'];
            var stateClasses = ['is-name', 'is-email', 'is-pass', 'is-conf', 'is-terms', 'is-submit'];

            var eyes = svg ? [].slice.call(svg.querySelectorAll('.rj-eye')) : [];
            var faces = svg ? [].slice.call(svg.querySelectorAll('.rj-face')) : [];
            var mouths = svg ? [].slice.call(svg.querySelectorAll('.rj-mouth')) : [];
            var hopper = svg ? svg.querySelector('.rj-bob-4') : null;
            var mouse = null;
            var blinking = false;
            var hoverSubmit = false;
            var touched = false;
            var currentMood = '';
            var prevLevel = 0;
            var prevMatch = false;
            var prevMismatch = false;

            function isRtl() { return document.documentElement.dir === 'rtl'; }

            function pulse(el, cls, ms) {
                if (!el) { return; }
                el.classList.remove(cls);
                void el.getBoundingClientRect();
                el.classList.add(cls);
                setTimeout(function () { el.classList.remove(cls); }, ms);
            }

            function level(v) {
                if (!v) { return 0; }
                if (v.length < 8) { return 1; }
                var s = 1;
                if (/[a-z]/.test(v) && /[A-Z]/.test(v)) { s++; }
                if (/\d/.test(v)) { s++; }
                if (/[^A-Za-z0-9]/.test(v)) { s++; }
                return s;
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
                var a = document.activeElement;
                if (a === pass) { return 'pass'; }
                if (a === conf) { return 'conf'; }
                if (hoverSubmit || a === submit) { return 'submit'; }
                if (a === nameI) { return 'name'; }
                if (a === email) { return 'email'; }
                if (a === terms) { return 'terms'; }
                return 'idle';
            }

            function moodFor(s) {
                if (hasErrors && !touched) { return 'sad'; }
                if (s === 'name') { return nameI.value ? 'smile' : 'neutral'; }
                if (s === 'email') { return email.value && email.validity.valid ? 'smile' : 'neutral'; }
                if (s === 'pass') {
                    var l = level(pass.value);
                    if (l >= 4) { return 'big'; }
                    if (l === 3) { return 'smile'; }
                    if (l === 1) { return 'flat'; }
                    return 'neutral';
                }
                if (s === 'conf') {
                    if (!conf.value) { return 'neutral'; }
                    if (conf.value === pass.value) { return 'big'; }
                    return conf.value.length >= pass.value.length ? 'sad' : 'flat';
                }
                if (s === 'terms') { return terms.checked ? 'big' : 'neutral'; }
                if (s === 'submit') { return 'smile'; }
                return 'neutral';
            }

            function updateMeter() {
                var l = level(pass.value);
                meter.classList.toggle('on', l > 0);
                bars.forEach(function (b, i) { b.style.background = i < l ? colors[l - 1] : '#e5e7eb'; });
                meterLabel.textContent = l > 0 ? labels[l - 1] : '';
                meterLabel.style.color = l > 0 ? colors[l - 1] : '';
            }

            function render() {
                if (!svg) { return; }
                var s = state();
                var peek = (s === 'pass' || s === 'conf') && pass.type === 'text';
                var closed = ((s === 'pass' || s === 'conf') && !peek) || blinking;

                stateClasses.forEach(function (c) { svg.classList.remove(c); });
                if (s !== 'idle') { svg.classList.add('is-' + s); }
                svg.classList.toggle('is-closed', closed);
                setMood(moodFor(s));

                var box = svg.getBoundingClientRect();
                var sideX = box.left + box.width / 2;
                var target = null;

                if (s === 'name') {
                    target = caretTarget(nameI);
                    var rn = Math.min(nameI.value.length / 24, 1);
                    shiftFaces((target.x >= sideX ? 1 : -1) * (5 + rn * 3), 2 + rn * 3);
                    lookAt(target.x, target.y + 12 + rn * 24);
                } else if (s === 'email') {
                    target = caretTarget(email);
                    var re = Math.min(email.value.length / 28, 1);
                    shiftFaces((target.x >= sideX ? 1 : -1) * (5 + re * 3), 2 + re * 3);
                    lookAt(target.x, target.y + 12 + re * 24);
                } else if (s === 'terms') {
                    target = centerOf(terms);
                    shiftFaces(target.x >= sideX ? 6 : -6, 4);
                    lookAt(target.x, target.y);
                } else if (s === 'submit') {
                    target = centerOf(submit);
                    shiftFaces(target.x >= sideX ? 6 : -6, 5);
                    lookAt(target.x, target.y);
                } else if (s === 'pass' || s === 'conf') {
                    if (peek) {
                        shiftFaces(isRtl() ? 6 : -6, 3);
                        lookAt(box.left + (isRtl() ? box.width + 300 : -300), box.bottom);
                    } else {
                        shiftFaces(0, 2);
                    }
                } else {
                    var p = mouse || { x: box.right + 400, y: box.top + box.height / 2 };
                    shiftFaces(Math.max(-6, Math.min(6, (p.x - sideX) / 120)), 0);
                    lookAt(p.x, p.y);
                }
            }

            function checkConfirm() {
                var l = level(pass.value);
                if (l === 4 && prevLevel < 4) { pulse(svg, 'is-cheer', 1000); }
                prevLevel = l;

                var match = !!conf.value && conf.value === pass.value;
                var mismatch = !!conf.value && !match && conf.value.length >= pass.value.length && document.activeElement === conf;
                if (match && !prevMatch) { pulse(svg, 'is-cheer', 1000); }
                if (mismatch && !prevMismatch) { pulse(svg, 'is-error', 700); }
                prevMatch = match;
                prevMismatch = mismatch;
            }

            [nameI, email, pass, conf, terms, submit].forEach(function (el) {
                el.addEventListener('focus', render);
                el.addEventListener('blur', function () { setTimeout(render, 0); });
            });
            [nameI, email].forEach(function (el) {
                el.addEventListener('keyup', render);
                el.addEventListener('click', render);
            });

            nameI.addEventListener('input', function () { touched = true; pulse(hopper, 'is-hop', 340); render(); });
            email.addEventListener('input', function () { touched = true; render(); });
            pass.addEventListener('input', function () { touched = true; updateMeter(); checkConfirm(); render(); });
            conf.addEventListener('input', function () { touched = true; checkConfirm(); render(); });
            terms.addEventListener('change', function () {
                touched = true;
                if (terms.checked) { pulse(svg, 'is-cheer', 1000); }
                render();
            });

            submit.addEventListener('mouseenter', function () { hoverSubmit = true; render(); });
            submit.addEventListener('mouseleave', function () { hoverSubmit = false; render(); });
            submit.addEventListener('click', function () { pulse(svg, 'is-cheer', 1000); });

            window.addEventListener('resize', render);
            document.addEventListener('mousemove', function (e) {
                mouse = { x: e.clientX, y: e.clientY };
                if (state() === 'idle') { render(); }
            });

            toggles.forEach(function (t) {
                t.addEventListener('mousedown', function (e) { e.preventDefault(); });
                t.addEventListener('click', function () {
                    var show = pass.type === 'password';
                    pass.type = show ? 'text' : 'password';
                    conf.type = show ? 'text' : 'password';
                    toggles.forEach(function (b) {
                        b.querySelector('.rj-eye-on').classList.toggle('rj-hidden', show);
                        b.querySelector('.rj-eye-off').classList.toggle('rj-hidden', !show);
                    });
                    document.getElementById(t.getAttribute('data-for')).focus();
                    render();
                });
            });

            function blink() {
                var s = state();
                if (!(s === 'pass' || s === 'conf')) {
                    blinking = true;
                    render();
                    setTimeout(function () { blinking = false; render(); }, 150);
                }
                setTimeout(blink, 2600 + Math.random() * 2800);
            }
            setTimeout(blink, 2200);

            if (hasErrors && svg) { pulse(svg, 'is-error', 900); }

            form.addEventListener('submit', function () {
                spinner.classList.remove('rj-hidden');
                submitText.textContent = @json(__('Creating account...'));
                setTimeout(function () { submit.disabled = true; submit.classList.add('opacity-80', 'cursor-wait'); }, 0);
            });

            updateMeter();
            prevLevel = level(pass.value);
            render();
        })();
    </script>

</body>
</html>
