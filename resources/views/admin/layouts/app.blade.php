<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Dashboard')) - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/admin.css', 'resources/js/app.js'])
    <style>:root { {!! rj_css_variables() !!} }</style>
</head>
<body class="font-sans bg-gray-50 text-gray-900 antialiased">
    <div class="flex min-h-screen" x-data="{ openMenu: null, userMenu: false, langMenu: false }">

        <aside class="w-64 bg-gradient-to-b from-rj-dark-1 via-rj-dark-2 to-rj-dark-1 text-gray-300 flex-shrink-0 flex flex-col">
            <div class="h-20 flex items-center justify-center px-5 border-b border-white/5">
                <a href="{{ route('admin.dashboard') }}" class="text-rj-purple-light hover:opacity-90 transition inline-flex items-center">
                    {!! rj_logo_svg('h-12 w-auto') !!}
                </a>
            </div>

            <nav class="rj-scrollbar flex-1 overflow-y-auto p-3 space-y-0.5 text-sm">

                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-rj-purple/30 to-rj-pink/20 text-white border-l-2 border-rj-purple-light' : 'hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10"/></svg>
                    <span>{{ __('Dashboard') }}</span>
                </a>

                <div>
                    <button @click="openMenu = openMenu === 'posts' ? null : 'posts'" class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.posts.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                        <span class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v8a2 2 0 01-2 2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M14 4v6h6M8 13h8M8 17h5"/></svg>
                            {{ __('Posts') }}
                        </span>
                        <svg class="w-3.5 h-3.5 opacity-60 transition" :class="openMenu === 'posts' ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openMenu === 'posts'" x-cloak class="pl-11 mt-1 space-y-0.5">
                        <a href="{{ route('admin.posts.index') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('All Posts') }}</a>
                        <a href="{{ route('admin.posts.create') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Add New') }}</a>
                        <a href="{{ route('admin.posts.categories') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Categories') }}</a>
                        <a href="{{ route('admin.posts.tags') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Tags') }}</a>
                    </div>
                </div>

                <div>
                    <button @click="openMenu = openMenu === 'pages' ? null : 'pages'" class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.pages.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                        <span class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            {{ __('Pages') }}
                        </span>
                        <svg class="w-3.5 h-3.5 opacity-60 transition" :class="openMenu === 'pages' ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openMenu === 'pages'" x-cloak class="pl-11 mt-1 space-y-0.5">
                        <a href="{{ route('admin.pages.index') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('All Pages') }}</a>
                        <a href="{{ route('admin.pages.create') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Add New') }}</a>
                    </div>
                </div>

                <div>
                    <button @click="openMenu = openMenu === 'media' ? null : 'media'" class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.media.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                        <span class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2zm6-10a1 1 0 11-2 0 1 1 0 012 0z"/></svg>
                            {{ __('Media') }}
                        </span>
                        <svg class="w-3.5 h-3.5 opacity-60 transition" :class="openMenu === 'media' ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openMenu === 'media'" x-cloak class="pl-11 mt-1 space-y-0.5">
                        <a href="{{ route('admin.media.index') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Library') }}</a>
                        <a href="{{ route('admin.media.create') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Add New') }}</a>
                    </div>
                </div>

                <div>
                    <button @click="openMenu = openMenu === 'comments' ? null : 'comments'" class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.comments.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                        <span class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            {{ __('Comments') }}
                        </span>
                        <svg class="w-3.5 h-3.5 opacity-60 transition" :class="openMenu === 'comments' ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openMenu === 'comments'" x-cloak class="pl-11 mt-1 space-y-0.5">
                        <a href="{{ route('admin.comments.index') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('All') }}</a>
                        <a href="{{ route('admin.comments.pending') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Pending') }}</a>
                        <a href="{{ route('admin.comments.approved') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Approved') }}</a>
                        <a href="{{ route('admin.comments.spam') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Spam') }}</a>
                        <a href="{{ route('admin.comments.trash') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Trash') }}</a>
                    </div>
                </div>

                <div>
                    <button @click="openMenu = openMenu === 'appearance' ? null : 'appearance'" class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.appearance.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                        <span class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                            {{ __('Appearance') }}
                        </span>
                        <svg class="w-3.5 h-3.5 opacity-60 transition" :class="openMenu === 'appearance' ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openMenu === 'appearance'" x-cloak class="pl-11 mt-1 space-y-0.5">
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
                        <span class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
                            {{ __('Plugins') }}
                        </span>
                        <svg class="w-3.5 h-3.5 opacity-60 transition" :class="openMenu === 'plugins' ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openMenu === 'plugins'" x-cloak class="pl-11 mt-1 space-y-0.5">
                        <a href="{{ route('admin.plugins.index') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Installed') }}</a>
                        <a href="{{ route('admin.plugins.create') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Add New') }}</a>
                        <a href="{{ route('admin.plugins.editor') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Editor') }}</a>
                    </div>
                </div>

                <div>
                    <button @click="openMenu = openMenu === 'ai' ? null : 'ai'" class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.ai.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                        <span class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                            {{ __('AI Center') }}
                        </span>
                        <svg class="w-3.5 h-3.5 opacity-60 transition" :class="openMenu === 'ai' ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openMenu === 'ai'" x-cloak class="pl-11 mt-1 space-y-0.5">
                        <a href="{{ route('admin.ai.index') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Overview') }}</a>
                        <a href="{{ route('admin.ai.models') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Models') }}</a>
                        <a href="{{ route('admin.ai.prompts') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Prompts') }}</a>
                        <a href="{{ route('admin.ai.usage') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Usage') }}</a>
                        <a href="{{ route('admin.ai.tools') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Tools') }}</a>
                    </div>
                </div>

                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.users.*') ? 'bg-gradient-to-r from-rj-purple/30 to-rj-pink/20 text-white border-l-2 border-rj-purple-light' : 'hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>{{ __('Users') }}</span>
                </a>

                <div>
                    <button @click="openMenu = openMenu === 'support' ? null : 'support'" class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.support.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                        <span class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            {{ __('Support') }}
                        </span>
                        <svg class="w-3.5 h-3.5 opacity-60 transition" :class="openMenu === 'support' ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openMenu === 'support'" x-cloak class="pl-11 mt-1 space-y-0.5">
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
                        <span class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ __('Tools') }}
                        </span>
                        <svg class="w-3.5 h-3.5 opacity-60 transition" :class="openMenu === 'tools' ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openMenu === 'tools'" x-cloak class="pl-11 mt-1 space-y-0.5">
                        <a href="{{ route('admin.tools.import') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Import') }}</a>
                        <a href="{{ route('admin.tools.export') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Export') }}</a>
                        <a href="{{ route('admin.tools.health') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Site Health') }}</a>
                        <a href="{{ route('admin.tools.backup') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Backup') }}</a>
                        <a href="{{ route('admin.tools.logs') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('Logs') }}</a>
                        <a href="{{ route('admin.tools.diagnostics') }}" class="block px-3 py-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/5">{{ __('AI Diagnostics') }}</a>
                    </div>
                </div>

                <a href="{{ route('admin.updates.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.updates.*') ? 'bg-gradient-to-r from-rj-purple/30 to-rj-pink/20 text-white border-l-2 border-rj-purple-light' : 'hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>{{ __('Updates') }}</span>
                </a>

                <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.settings.*') ? 'bg-gradient-to-r from-rj-purple/30 to-rj-pink/20 text-white border-l-2 border-rj-purple-light' : 'hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    <span>{{ __('Settings') }}</span>
                </a>

                <div>
                    <button @click="openMenu = openMenu === 'about' ? null : 'about'" class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.about.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                        <span class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ __('RJ About') }}
                        </span>
                        <svg class="w-3.5 h-3.5 opacity-60 transition" :class="openMenu === 'about' ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openMenu === 'about'" x-cloak class="pl-11 mt-1 space-y-0.5">
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

            <div class="p-3 border-t border-white/5 text-xs text-gray-500 flex items-center justify-between">
                <span>v{{ rj_get_option('rj_version', '1.0.0') }}</span>
                <a href="{{ route('home') }}" target="_blank" class="hover:text-white transition flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    {{ __('View Site') }}
                </a>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0">
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 sticky top-0 z-10">
                <div>
                    <h1 class="text-lg font-semibold text-gray-900">@yield('title', __('Dashboard'))</h1>
                </div>

                <div class="flex items-center gap-2">

                    <div class="relative" @click.away="langMenu = false">
                        <button @click="langMenu = !langMenu" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition text-sm text-gray-600">
                            <span class="uppercase text-xs font-medium">{{ app()->getLocale() }}</span>
                            <svg class="w-3.5 h-3.5 opacity-60" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="langMenu" x-cloak x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-1 rj-fade-in z-20">
                            @foreach (rj_available_locales() as $locale)
                                <a href="{{ route('admin.lang.switch', $locale) }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 {{ app()->getLocale() === $locale ? 'bg-gray-50 font-medium' : '' }}">
                                    @if (file_exists(public_path("images/flags/{$locale}.svg")))
                                        <img src="{{ asset("images/flags/{$locale}.svg") }}" alt="{{ strtoupper($locale) }}" class="w-5 h-4 object-cover rounded-sm">
                                    @endif
                                    <span class="uppercase">{{ $locale }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="relative" @click.away="userMenu = false">
                        <button @click="userMenu = !userMenu" class="flex items-center gap-3 px-2 py-1.5 rounded-lg hover:bg-gray-100 transition">
                            <div class="h-9 w-9 rounded-full bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center text-white text-sm font-semibold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="hidden sm:block text-left">
                                <div class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</div>
                                <div class="text-xs text-gray-500">{{ ucfirst(auth()->user()->role) }}</div>
                            </div>
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="userMenu" x-cloak x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-100 py-1 rj-fade-in z-20">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <div class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</div>
                                <div class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</div>
                            </div>

                            <a href="{{ route('admin.profile.edit') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ __('Profile') }}
                            </a>

                            <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                {{ __('View Site') }}
                            </a>

                            <div class="border-t border-gray-100 mt-1 pt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        {{ __('Logout') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </header>

            <main class="p-6 flex-1">
                @if (session('success'))
                    <div class="mb-4 bg-green-50 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded-r-lg rj-fade-in">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-r-lg rj-fade-in">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
