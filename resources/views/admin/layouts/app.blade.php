<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Dashboard')) - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 antialiased">
    <div class="flex min-h-screen" x-data="{ openMenu: null }">
        <aside class="w-64 bg-gray-900 text-gray-100 flex-shrink-0">
            <div class="h-16 flex items-center px-6 border-b border-gray-800">
                <a href="{{ route('admin.dashboard') }}" class="text-lg font-semibold">{{ rj_get_option('site_name', 'RJ CMS') }}</a>
            </div>
            <nav class="p-4 space-y-1 text-sm">

                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800' : '' }}">{{ __('Dashboard') }}</a>

                <div>
                    <button @click="openMenu = openMenu === 'posts' ? null : 'posts'" class="w-full flex justify-between items-center px-3 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('admin.posts.*') ? 'bg-gray-800' : '' }}">
                        <span>{{ __('Posts') }}</span>
                        <span x-text="openMenu === 'posts' ? '−' : '+'"></span>
                    </button>
                    <div x-show="openMenu === 'posts'" class="pl-4 space-y-1 mt-1">
                        <a href="{{ route('admin.posts.index') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('All Posts') }}</a>
                        <a href="{{ route('admin.posts.create') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Add New') }}</a>
                        <a href="{{ route('admin.posts.categories') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Categories') }}</a>
                        <a href="{{ route('admin.posts.tags') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Tags') }}</a>
                    </div>
                </div>

                <div>
                    <button @click="openMenu = openMenu === 'pages' ? null : 'pages'" class="w-full flex justify-between items-center px-3 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('admin.pages.*') ? 'bg-gray-800' : '' }}">
                        <span>{{ __('Pages') }}</span>
                        <span x-text="openMenu === 'pages' ? '−' : '+'"></span>
                    </button>
                    <div x-show="openMenu === 'pages'" class="pl-4 space-y-1 mt-1">
                        <a href="{{ route('admin.pages.index') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('All Pages') }}</a>
                        <a href="{{ route('admin.pages.create') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Add New') }}</a>
                    </div>
                </div>

                <div>
                    <button @click="openMenu = openMenu === 'media' ? null : 'media'" class="w-full flex justify-between items-center px-3 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('admin.media.*') ? 'bg-gray-800' : '' }}">
                        <span>{{ __('Media') }}</span>
                        <span x-text="openMenu === 'media' ? '−' : '+'"></span>
                    </button>
                    <div x-show="openMenu === 'media'" class="pl-4 space-y-1 mt-1">
                        <a href="{{ route('admin.media.index') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Library') }}</a>
                        <a href="{{ route('admin.media.create') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Add New') }}</a>
                    </div>
                </div>

                <div>
                    <button @click="openMenu = openMenu === 'comments' ? null : 'comments'" class="w-full flex justify-between items-center px-3 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('admin.comments.*') ? 'bg-gray-800' : '' }}">
                        <span>{{ __('Comments') }}</span>
                        <span x-text="openMenu === 'comments' ? '−' : '+'"></span>
                    </button>
                    <div x-show="openMenu === 'comments'" class="pl-4 space-y-1 mt-1">
                        <a href="{{ route('admin.comments.index') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('All') }}</a>
                        <a href="{{ route('admin.comments.pending') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Pending') }}</a>
                        <a href="{{ route('admin.comments.approved') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Approved') }}</a>
                        <a href="{{ route('admin.comments.spam') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Spam') }}</a>
                        <a href="{{ route('admin.comments.trash') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Trash') }}</a>
                    </div>
                </div>

                <div>
                    <button @click="openMenu = openMenu === 'appearance' ? null : 'appearance'" class="w-full flex justify-between items-center px-3 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('admin.appearance.*') ? 'bg-gray-800' : '' }}">
                        <span>{{ __('Appearance') }}</span>
                        <span x-text="openMenu === 'appearance' ? '−' : '+'"></span>
                    </button>
                    <div x-show="openMenu === 'appearance'" class="pl-4 space-y-1 mt-1">
                        <a href="{{ route('admin.appearance.themes') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Themes') }}</a>
                        <a href="{{ route('admin.appearance.customize') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Customize') }}</a>
                        <a href="{{ route('admin.appearance.widgets') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Widgets') }}</a>
                        <a href="{{ route('admin.appearance.menus') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Menus') }}</a>
                        <a href="{{ route('admin.appearance.editor') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Theme Editor') }}</a>
                        <a href="{{ route('admin.appearance.colors') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Colors') }}</a>
                        <a href="{{ route('admin.appearance.options') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Options') }}</a>
                    </div>
                </div>

                <div>
                    <button @click="openMenu = openMenu === 'plugins' ? null : 'plugins'" class="w-full flex justify-between items-center px-3 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('admin.plugins.*') ? 'bg-gray-800' : '' }}">
                        <span>{{ __('Plugins') }}</span>
                        <span x-text="openMenu === 'plugins' ? '−' : '+'"></span>
                    </button>
                    <div x-show="openMenu === 'plugins'" class="pl-4 space-y-1 mt-1">
                        <a href="{{ route('admin.plugins.index') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Installed') }}</a>
                        <a href="{{ route('admin.plugins.create') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Add New') }}</a>
                        <a href="{{ route('admin.plugins.editor') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Editor') }}</a>
                    </div>
                </div>

                <div>
                    <button @click="openMenu = openMenu === 'ai' ? null : 'ai'" class="w-full flex justify-between items-center px-3 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('admin.ai.*') ? 'bg-gray-800' : '' }}">
                        <span>{{ __('AI Center') }}</span>
                        <span x-text="openMenu === 'ai' ? '−' : '+'"></span>
                    </button>
                    <div x-show="openMenu === 'ai'" class="pl-4 space-y-1 mt-1">
                        <a href="{{ route('admin.ai.index') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Overview') }}</a>
                        <a href="{{ route('admin.ai.settings') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Settings') }}</a>
                        <a href="{{ route('admin.ai.models') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Models') }}</a>
                        <a href="{{ route('admin.ai.prompts') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Prompts') }}</a>
                        <a href="{{ route('admin.ai.usage') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Usage') }}</a>
                        <a href="{{ route('admin.ai.tools') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Tools') }}</a>
                    </div>
                </div>

                <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('admin.users.*') ? 'bg-gray-800' : '' }}">{{ __('Users') }}</a>

                <div>
                    <button @click="openMenu = openMenu === 'support' ? null : 'support'" class="w-full flex justify-between items-center px-3 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('admin.support.*') ? 'bg-gray-800' : '' }}">
                        <span>{{ __('Support') }}</span>
                        <span x-text="openMenu === 'support' ? '−' : '+'"></span>
                    </button>
                    <div x-show="openMenu === 'support'" class="pl-4 space-y-1 mt-1">
                        <a href="{{ route('admin.support.tickets') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Tickets') }}</a>
                        <a href="{{ route('admin.support.chat') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Live Chat') }}</a>
                        <a href="{{ route('admin.support.faq') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('FAQ') }}</a>
                        <a href="{{ route('admin.support.contact') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Contact') }}</a>
                        <a href="{{ route('admin.support.docs') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Documentation') }}</a>
                        <a href="{{ route('admin.support.status') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('System Status') }}</a>
                    </div>
                </div>

                <div>
                    <button @click="openMenu = openMenu === 'tools' ? null : 'tools'" class="w-full flex justify-between items-center px-3 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('admin.tools.*') ? 'bg-gray-800' : '' }}">
                        <span>{{ __('Tools') }}</span>
                        <span x-text="openMenu === 'tools' ? '−' : '+'"></span>
                    </button>
                    <div x-show="openMenu === 'tools'" class="pl-4 space-y-1 mt-1">
                        <a href="{{ route('admin.tools.import') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Import') }}</a>
                        <a href="{{ route('admin.tools.export') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Export') }}</a>
                        <a href="{{ route('admin.tools.health') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Site Health') }}</a>
                        <a href="{{ route('admin.tools.backup') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Backup') }}</a>
                        <a href="{{ route('admin.tools.logs') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Logs') }}</a>
                        <a href="{{ route('admin.tools.diagnostics') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('AI Diagnostics') }}</a>
                    </div>
                </div>

                <a href="{{ route('admin.updates.index') }}" class="block px-3 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('admin.updates.*') ? 'bg-gray-800' : '' }}">{{ __('Updates') }}</a>

                <a href="{{ route('admin.settings.index') }}" class="block px-3 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('admin.settings.*') ? 'bg-gray-800' : '' }}">{{ __('Settings') }}</a>

                <div>
                    <button @click="openMenu = openMenu === 'about' ? null : 'about'" class="w-full flex justify-between items-center px-3 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('admin.about.*') ? 'bg-gray-800' : '' }}">
                        <span>{{ __('RJ About') }}</span>
                        <span x-text="openMenu === 'about' ? '−' : '+'"></span>
                    </button>
                    <div x-show="openMenu === 'about'" class="pl-4 space-y-1 mt-1">
                        <a href="{{ route('admin.about.index') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Info') }}</a>
                        <a href="{{ route('admin.about.api') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('API Document') }}</a>
                        <a href="{{ route('admin.about.theme') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Theme Document') }}</a>
                        <a href="{{ route('admin.about.admin') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Admin Document') }}</a>
                        <a href="{{ route('admin.about.plugin') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Plugin Document') }}</a>
                        <a href="{{ route('admin.about.developer') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Developer Guide') }}</a>
                        <a href="{{ route('admin.about.hooks') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Hooks Reference') }}</a>
                        <a href="{{ route('admin.about.other') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Other Documents') }}</a>
                        <a href="{{ route('admin.about.credits') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('Credits') }}</a>
                        <a href="{{ route('admin.about.license') }}" class="block px-3 py-1.5 rounded hover:bg-gray-800">{{ __('License') }}</a>
                    </div>
                </div>

            </nav>
        </aside>

        <div class="flex-1 flex flex-col">
            <header class="h-16 bg-white border-b flex items-center justify-between px-6">
                <div class="font-medium">@yield('title', __('Dashboard'))</div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:underline">{{ __('Logout') }}</button>
                    </form>
                </div>
            </header>

            <main class="p-6 flex-1">
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
