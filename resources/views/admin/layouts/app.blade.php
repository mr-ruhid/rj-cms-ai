<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Dashboard')) - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-gray-50 text-gray-900 antialiased">
    <div class="flex min-h-screen" x-data="{ openMenu: null, sidebarOpen: true }">

        <aside class="w-64 bg-gradient-to-b from-[#1a1030] via-[#231243] to-[#1a1030] text-gray-300 flex-shrink-0 flex flex-col">
            <div class="h-16 flex items-center px-5 border-b border-white/5">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                    <div class="h-9 w-9 rounded-lg bg-gradient-to-br from-[#7c3aed] to-[#ec4899] flex items-center justify-center">
                        <span class="text-white font-bold text-sm">RJ</span>
                    </div>
                    <span class="text-white font-semibold">{{ rj_get_option('site_name', 'RJ CMS') }}</span>
                </a>
            </div>

            <nav class="flex-1 overflow-y-auto p-3 space-y-0.5 text-sm">

                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-[#7c3aed]/30 to-[#ec4899]/20 text-white border-l-2 border-[#a855f7]' : 'hover:bg-white/5 hover:text-white' }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>
                    <span>{{ __('Dashboard') }}</span>
                </a>

                <div>
                    <button @click="openMenu = openMenu === 'posts' ? null : 'posts'" class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.posts.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                        <span class="flex items-center gap-3"><span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>{{ __('Posts') }}</span>
                        <span class="text-xs opacity-60" x-text="openMenu === 'posts' ? '−' : '+'"></span>
                    </button>
                    <div x-show="openMenu === 'posts'" x-cloak class="pl-7 mt-1 space-y-0.5">
                        <a href="{{ route('admin.posts.index') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('All Posts') }}</a>
                        <a href="{{ route('admin.posts.create') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Add New') }}</a>
                        <a href="{{ route('admin.posts.categories') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Categories') }}</a>
                        <a href="{{ route('admin.posts.tags') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Tags') }}</a>
                    </div>
                </div>

                <div>
                    <button @click="openMenu = openMenu === 'pages' ? null : 'pages'" class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.pages.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                        <span class="flex items-center gap-3"><span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>{{ __('Pages') }}</span>
                        <span class="text-xs opacity-60" x-text="openMenu === 'pages' ? '−' : '+'"></span>
                    </button>
                    <div x-show="openMenu === 'pages'" x-cloak class="pl-7 mt-1 space-y-0.5">
                        <a href="{{ route('admin.pages.index') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('All Pages') }}</a>
                        <a href="{{ route('admin.pages.create') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Add New') }}</a>
                    </div>
                </div>

                <div>
                    <button @click="openMenu = openMenu === 'media' ? null : 'media'" class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.media.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                        <span class="flex items-center gap-3"><span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>{{ __('Media') }}</span>
                        <span class="text-xs opacity-60" x-text="openMenu === 'media' ? '−' : '+'"></span>
                    </button>
                    <div x-show="openMenu === 'media'" x-cloak class="pl-7 mt-1 space-y-0.5">
                        <a href="{{ route('admin.media.index') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Library') }}</a>
                        <a href="{{ route('admin.media.create') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Add New') }}</a>
                    </div>
                </div>

                <div>
                    <button @click="openMenu = openMenu === 'comments' ? null : 'comments'" class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.comments.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                        <span class="flex items-center gap-3"><span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>{{ __('Comments') }}</span>
                        <span class="text-xs opacity-60" x-text="openMenu === 'comments' ? '−' : '+'"></span>
                    </button>
                    <div x-show="openMenu === 'comments'" x-cloak class="pl-7 mt-1 space-y-0.5">
                        <a href="{{ route('admin.comments.index') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('All') }}</a>
                        <a href="{{ route('admin.comments.pending') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Pending') }}</a>
                        <a href="{{ route('admin.comments.approved') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Approved') }}</a>
                        <a href="{{ route('admin.comments.spam') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Spam') }}</a>
                        <a href="{{ route('admin.comments.trash') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Trash') }}</a>
                    </div>
                </div>

                <div>
                    <button @click="openMenu = openMenu === 'appearance' ? null : 'appearance'" class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.appearance.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                        <span class="flex items-center gap-3"><span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>{{ __('Appearance') }}</span>
                        <span class="text-xs opacity-60" x-text="openMenu === 'appearance' ? '−' : '+'"></span>
                    </button>
                    <div x-show="openMenu === 'appearance'" x-cloak class="pl-7 mt-1 space-y-0.5">
                        <a href="{{ route('admin.appearance.themes') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Themes') }}</a>
                        <a href="{{ route('admin.appearance.customize') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Customize') }}</a>
                        <a href="{{ route('admin.appearance.widgets') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Widgets') }}</a>
                        <a href="{{ route('admin.appearance.menus') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Menus') }}</a>
                        <a href="{{ route('admin.appearance.editor') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Theme Editor') }}</a>
                        <a href="{{ route('admin.appearance.colors') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Colors') }}</a>
                        <a href="{{ route('admin.appearance.options') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Options') }}</a>
                    </div>
                </div>

                <div>
                    <button @click="openMenu = openMenu === 'plugins' ? null : 'plugins'" class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.plugins.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                        <span class="flex items-center gap-3"><span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>{{ __('Plugins') }}</span>
                        <span class="text-xs opacity-60" x-text="openMenu === 'plugins' ? '−' : '+'"></span>
                    </button>
                    <div x-show="openMenu === 'plugins'" x-cloak class="pl-7 mt-1 space-y-0.5">
                        <a href="{{ route('admin.plugins.index') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Installed') }}</a>
                        <a href="{{ route('admin.plugins.create') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Add New') }}</a>
                        <a href="{{ route('admin.plugins.editor') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Editor') }}</a>
                    </div>
                </div>

                <div>
                    <button @click="openMenu = openMenu === 'ai' ? null : 'ai'" class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.ai.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                        <span class="flex items-center gap-3">
                            <span class="w-1.5 h-1.5 rounded-full bg-gradient-to-r from-[#7c3aed] to-[#ec4899]"></span>
                            {{ __('AI Center') }}
                        </span>
                        <span class="text-xs opacity-60" x-text="openMenu === 'ai' ? '−' : '+'"></span>
                    </button>
                    <div x-show="openMenu === 'ai'" x-cloak class="pl-7 mt-1 space-y-0.5">
                        <a href="{{ route('admin.ai.index') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Overview') }}</a>
                        <a href="{{ route('admin.ai.settings') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Settings') }}</a>
                        <a href="{{ route('admin.ai.models') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Models') }}</a>
                        <a href="{{ route('admin.ai.prompts') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Prompts') }}</a>
                        <a href="{{ route('admin.ai.usage') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Usage') }}</a>
                        <a href="{{ route('admin.ai.tools') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Tools') }}</a>
                    </div>
                </div>

                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.users.*') ? 'bg-gradient-to-r from-[#7c3aed]/30 to-[#ec4899]/20 text-white border-l-2 border-[#a855f7]' : 'hover:bg-white/5 hover:text-white' }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>
                    <span>{{ __('Users') }}</span>
                </a>

                <div>
                    <button @click="openMenu = openMenu === 'support' ? null : 'support'" class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.support.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                        <span class="flex items-center gap-3"><span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>{{ __('Support') }}</span>
                        <span class="text-xs opacity-60" x-text="openMenu === 'support' ? '−' : '+'"></span>
                    </button>
                    <div x-show="openMenu === 'support'" x-cloak class="pl-7 mt-1 space-y-0.5">
                        <a href="{{ route('admin.support.tickets') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Tickets') }}</a>
                        <a href="{{ route('admin.support.chat') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Live Chat') }}</a>
                        <a href="{{ route('admin.support.faq') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('FAQ') }}</a>
                        <a href="{{ route('admin.support.contact') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Contact') }}</a>
                        <a href="{{ route('admin.support.docs') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Documentation') }}</a>
                        <a href="{{ route('admin.support.status') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('System Status') }}</a>
                    </div>
                </div>

                <div>
                    <button @click="openMenu = openMenu === 'tools' ? null : 'tools'" class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.tools.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                        <span class="flex items-center gap-3"><span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>{{ __('Tools') }}</span>
                        <span class="text-xs opacity-60" x-text="openMenu === 'tools' ? '−' : '+'"></span>
                    </button>
                    <div x-show="openMenu === 'tools'" x-cloak class="pl-7 mt-1 space-y-0.5">
                        <a href="{{ route('admin.tools.import') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Import') }}</a>
                        <a href="{{ route('admin.tools.export') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Export') }}</a>
                        <a href="{{ route('admin.tools.health') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Site Health') }}</a>
                        <a href="{{ route('admin.tools.backup') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Backup') }}</a>
                        <a href="{{ route('admin.tools.logs') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Logs') }}</a>
                        <a href="{{ route('admin.tools.diagnostics') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('AI Diagnostics') }}</a>
                    </div>
                </div>

                <a href="{{ route('admin.updates.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.updates.*') ? 'bg-gradient-to-r from-[#7c3aed]/30 to-[#ec4899]/20 text-white border-l-2 border-[#a855f7]' : 'hover:bg-white/5 hover:text-white' }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>
                    <span>{{ __('Updates') }}</span>
                </a>

                <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.settings.*') ? 'bg-gradient-to-r from-[#7c3aed]/30 to-[#ec4899]/20 text-white border-l-2 border-[#a855f7]' : 'hover:bg-white/5 hover:text-white' }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>
                    <span>{{ __('Settings') }}</span>
                </a>

                <div>
                    <button @click="openMenu = openMenu === 'about' ? null : 'about'" class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.about.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                        <span class="flex items-center gap-3"><span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>{{ __('RJ About') }}</span>
                        <span class="text-xs opacity-60" x-text="openMenu === 'about' ? '−' : '+'"></span>
                    </button>
                    <div x-show="openMenu === 'about'" x-cloak class="pl-7 mt-1 space-y-0.5">
                        <a href="{{ route('admin.about.index') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Info') }}</a>
                        <a href="{{ route('admin.about.api') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('API Document') }}</a>
                        <a href="{{ route('admin.about.theme') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Theme Document') }}</a>
                        <a href="{{ route('admin.about.admin') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Admin Document') }}</a>
                        <a href="{{ route('admin.about.plugin') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Plugin Document') }}</a>
                        <a href="{{ route('admin.about.developer') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Developer Guide') }}</a>
                        <a href="{{ route('admin.about.hooks') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Hooks Reference') }}</a>
                        <a href="{{ route('admin.about.other') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Other Documents') }}</a>
                        <a href="{{ route('admin.about.credits') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Credits') }}</a>
                        <a href="{{ route('admin.about.license') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('License') }}</a>
                    </div>
                </div>

            </nav>

            <div class="p-3 border-t border-white/5 text-xs text-gray-500">
                v{{ rj_get_option('rj_version', '1.0.0') }}
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0">
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 sticky top-0 z-10">
                <div>
                    <h1 class="text-lg font-semibold text-gray-900">@yield('title', __('Dashboard'))</h1>
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3 pl-4 border-l border-gray-200">
                        <div class="h-9 w-9 rounded-full bg-gradient-to-br from-[#7c3aed] to-[#ec4899] flex items-center justify-center text-white text-sm font-semibold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden sm:block">
                            <div class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</div>
                            <div class="text-xs text-gray-500">{{ ucfirst(auth()->user()->role) }}</div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm text-gray-500 hover:text-red-600 transition">{{ __('Logout') }}</button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="p-6 flex-1">
                @if (session('success'))
                    <div class="mb-4 bg-green-50 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded-r-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-r-lg">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
