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

        .rj-bg-grid {
            background-image:
                linear-gradient(to right, rgba(100, 116, 139, .07) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(100, 116, 139, .07) 1px, transparent 1px);
            background-size: 44px 44px;
            -webkit-mask-image: radial-gradient(ellipse at center, #000 30%, transparent 75%);
            mask-image: radial-gradient(ellipse at center, #000 30%, transparent 75%);
        }

        .rj-blob { animation: rj-blob 18s ease-in-out infinite; will-change: transform; }
        .rj-blob-2 { animation-duration: 22s; animation-delay: -6s; }
        .rj-blob-3 { animation-duration: 26s; animation-delay: -12s; }

        @keyframes rj-blob {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(60px, -40px) scale(1.12); }
            50% { transform: translate(-40px, 50px) scale(.94); }
            75% { transform: translate(30px, 30px) scale(1.06); }
        }

        .rj-rise { opacity: 0; transform: translateY(22px); animation: rj-rise .7s cubic-bezier(.22, 1, .36, 1) forwards; }
        @keyframes rj-rise { to { opacity: 1; transform: translateY(0); } }

        .rj-card { animation: rj-card .9s cubic-bezier(.22, 1, .36, 1) both; }
        @keyframes rj-card {
            from { opacity: 0; transform: translateY(40px) scale(.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .rj-float { animation: rj-float 4.5s ease-in-out infinite; }
        @keyframes rj-float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }

        .rj-border-glow { position: relative; }
        .rj-border-glow::before {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: inherit;
            background: linear-gradient(120deg, transparent 20%, rgba(168, 85, 247, .55), rgba(236, 72, 153, .55), transparent 80%);
            background-size: 250% 250%;
            animation: rj-glow 6s linear infinite;
            z-index: -1;
            filter: blur(10px);
            opacity: .65;
        }
        @keyframes rj-glow {
            0% { background-position: 0% 50%; }
            100% { background-position: 250% 50%; }
        }

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

        .rj-input { transition: box-shadow .25s ease, border-color .25s ease, transform .25s ease; }
        .rj-input:focus { transform: translateY(-1px); box-shadow: 0 10px 24px -12px rgba(168, 85, 247, .45); }

        .rj-shake { animation: rj-shake .5s cubic-bezier(.36, .07, .19, .97) both; }
        @keyframes rj-shake {
            10%, 90% { transform: translateX(-2px); }
            20%, 80% { transform: translateX(4px); }
            30%, 50%, 70% { transform: translateX(-6px); }
            40%, 60% { transform: translateX(6px); }
        }

        .rj-spin { animation: rj-spin .8s linear infinite; }
        @keyframes rj-spin { to { transform: rotate(360deg); } }

        .rj-arrow { transition: transform .25s ease; }
        .rj-back:hover .rj-arrow { transform: translateX(-4px); }

        @media (prefers-reduced-motion: reduce) {
            .rj-blob, .rj-float, .rj-border-glow::before, .rj-btn::after, .rj-spin { animation: none; }
            .rj-rise, .rj-card { animation: none; opacity: 1; transform: none; }
        }
    </style>
</head>
<body class="h-full text-gray-900 antialiased flex flex-col justify-center items-center p-4 relative overflow-x-hidden">

    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="rj-bg-grid absolute inset-0"></div>
        <div class="rj-blob absolute -top-32 -left-24 w-96 h-96 rounded-full bg-rj-purple opacity-20 blur-3xl"></div>
        <div class="rj-blob rj-blob-2 absolute top-1/3 -right-32 w-[28rem] h-[28rem] rounded-full bg-rj-pink opacity-20 blur-3xl"></div>
        <div class="rj-blob rj-blob-3 absolute -bottom-40 left-1/4 w-96 h-96 rounded-full bg-rj-purple opacity-15 blur-3xl"></div>
    </div>

    <div class="w-full max-w-md">

        <div class="text-center mb-8 rj-rise" style="animation-delay:.05s">
            <a href="{{ url('/') }}" class="rj-float inline-flex items-center justify-center gap-2">
                @php $logoMask = rj_logo_mask_url(); @endphp
                @if ($logoMask)
                    <div class="h-12 w-40 bg-rj-dark-1" style="-webkit-mask-image: url('{{ $logoMask }}'); mask-image: url('{{ $logoMask }}'); -webkit-mask-size: contain; mask-size: contain; -webkit-mask-repeat: no-repeat; mask-repeat: no-repeat; -webkit-mask-position: center; mask-position: center;"></div>
                @else
                    <span class="text-3xl font-black bg-clip-text text-transparent bg-gradient-to-r from-rj-purple to-rj-pink">{{ rj_get_option('site_name', 'RJ CMS') }}</span>
                @endif
            </a>
        </div>

        <div class="rj-card rj-border-glow rounded-3xl">
            <div class="bg-white/85 backdrop-blur-xl rounded-3xl shadow-xl border border-white overflow-hidden p-8">

                <div class="mb-8 rj-rise" style="animation-delay:.2s">
                    <h1 class="text-2xl font-extrabold text-gray-900 text-center">{{ __('Welcome back') }}</h1>
                    <p class="text-sm text-gray-500 mt-2 text-center">{{ __('Sign in to your account to continue') }}</p>
                </div>

                @if (session('status'))
                    <div class="mb-6 text-sm font-medium text-green-700 bg-green-50 p-3 rounded-xl border border-green-200 text-center rj-rise" style="animation-delay:.25s">
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

                    <div class="rj-rise" style="animation-delay:.3s">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Email Address') }}</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 start-0 flex items-center ps-4 text-gray-400 pointer-events-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                            </span>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                   class="rj-input w-full rounded-xl border-gray-300 focus:border-rj-purple focus:ring-rj-purple ps-12 pe-4 py-3"
                                   placeholder="name@example.com">
                        </div>
                    </div>

                    <div class="rj-rise" style="animation-delay:.4s">
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-sm font-medium text-gray-700">{{ __('Password') }}</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-rj-purple hover:text-rj-pink transition">
                                    {{ __('Forgot password?') }}
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 start-0 flex items-center ps-4 text-gray-400 pointer-events-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                            </span>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                   class="rj-input w-full rounded-xl border-gray-300 focus:border-rj-purple focus:ring-rj-purple ps-12 pe-12 py-3"
                                   placeholder="••••••••">
                            <button type="button" id="rj-toggle-pass" aria-label="{{ __('Show password') }}"
                                    class="absolute inset-y-0 end-0 flex items-center pe-4 text-gray-400 hover:text-rj-purple transition">
                                <svg id="rj-eye-on" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <svg id="rj-eye-off" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1 rj-rise" style="animation-delay:.5s">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer">
                            <input id="remember_me" type="checkbox" name="remember"
                                   class="rounded border-gray-300 text-rj-purple shadow-sm focus:ring-rj-purple">
                            <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                        </label>
                    </div>

                    <div class="rj-rise" style="animation-delay:.6s">
                        <button type="submit" id="rj-submit" class="rj-btn w-full flex justify-center items-center gap-2 py-3.5 px-4 rounded-xl shadow-lg shadow-rj-purple/30 text-sm font-bold text-white bg-gradient-to-r from-rj-purple to-rj-pink hover:opacity-95 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rj-purple transition-all active:scale-[.98]">
                            <svg id="rj-spinner" class="rj-spin w-4 h-4 hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                            <span id="rj-submit-text">{{ __('Sign In') }}</span>
                        </button>
                    </div>
                </form>

                @if (Route::has('register'))
                    <div class="mt-8 text-center pt-6 border-t border-gray-100 rj-rise" style="animation-delay:.7s">
                        <p class="text-sm text-gray-600">
                            {{ __("Don't have an account?") }}
                            <a href="{{ route('register') }}" class="font-bold text-rj-purple hover:text-rj-pink transition ml-1">{{ __('Create one now') }}</a>
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <div class="text-center mt-8 rj-rise" style="animation-delay:.8s">
            <a href="{{ url('/') }}" class="rj-back text-sm font-medium text-gray-500 hover:text-gray-900 transition inline-flex items-center gap-1.5">
                <svg class="rj-arrow w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                {{ __('Back to website') }}
            </a>
        </div>
    </div>

    <script>
        (function () {
            var pass = document.getElementById('password');
            var toggle = document.getElementById('rj-toggle-pass');
            var eyeOn = document.getElementById('rj-eye-on');
            var eyeOff = document.getElementById('rj-eye-off');
            var form = document.getElementById('rj-login-form');
            var btn = document.getElementById('rj-submit');
            var spinner = document.getElementById('rj-spinner');
            var text = document.getElementById('rj-submit-text');

            toggle.addEventListener('click', function () {
                var show = pass.type === 'password';
                pass.type = show ? 'text' : 'password';
                eyeOn.classList.toggle('hidden', show);
                eyeOff.classList.toggle('hidden', !show);
            });

            form.addEventListener('submit', function () {
                spinner.classList.remove('hidden');
                text.textContent = @json(__('Signing in...'));
                setTimeout(function () { btn.disabled = true; btn.classList.add('opacity-80', 'cursor-wait'); }, 0);
            });
        })();
    </script>

</body>
</html>
