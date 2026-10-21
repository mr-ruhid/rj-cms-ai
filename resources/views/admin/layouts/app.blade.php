<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Dashboard')) - {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/admin.css', 'resources/js/app.js'])
    <style>:root { {!! rj_css_variables() !!} }</style>
</head>
<body class="font-sans bg-gray-50 text-gray-900 antialiased">
    @php
        $ic = fn (string $paths) => '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">' . $paths . '</svg>';
        $p = fn (string $d) => '<path stroke-linecap="round" stroke-linejoin="round" d="' . $d . '"/>';

        $coreTop = [
            [
                'label' => __('Dashboard'),
                'icon' => $ic($p('M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10')),
                'url' => route('admin.dashboard'),
                'active' => request()->routeIs('admin.dashboard'),
                'children' => [],
            ],
            [
                'label' => __('Posts'),
                'icon' => $ic($p('M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v8a2 2 0 01-2 2z') . $p('M14 4v6h6M8 13h8M8 17h5')),
                'active' => request()->routeIs('admin.posts.*'),
                'children' => [
                    ['label' => __('All Posts'), 'url' => route('admin.posts.index'), 'route' => 'admin.posts.index'],
                    ['label' => __('Add New'), 'url' => route('admin.posts.create'), 'route' => 'admin.posts.create'],
                    ['label' => __('Categories'), 'url' => route('admin.posts.categories'), 'route' => 'admin.posts.categories'],
                    ['label' => __('Tags'), 'url' => route('admin.posts.tags'), 'route' => 'admin.posts.tags'],
                ],
            ],
            [
                'label' => __('Pages'),
                'icon' => $ic($p('M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z')),
                'active' => request()->routeIs('admin.pages.*'),
                'children' => [
                    ['label' => __('All Pages'), 'url' => route('admin.pages.index'), 'route' => 'admin.pages.index'],
                    ['label' => __('Add New'), 'url' => route('admin.pages.create'), 'route' => 'admin.pages.create'],
                ],
            ],
            [
                'label' => __('Media'),
                'icon' => $ic($p('M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2zm6-10a1 1 0 11-2 0 1 1 0 012 0z')),
                'active' => request()->routeIs('admin.media.*'),
                'children' => [
                    ['label' => __('Library'), 'url' => route('admin.media.index'), 'route' => 'admin.media.index'],
                    ['label' => __('Add New'), 'url' => route('admin.media.create'), 'route' => 'admin.media.create'],
                ],
            ],
            [
                'label' => __('Comments'),
                'icon' => $ic($p('M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z')),
                'active' => request()->routeIs('admin.comments.*'),
                'children' => [
                    ['label' => __('All'), 'url' => route('admin.comments.index'), 'route' => 'admin.comments.index'],
                    ['label' => __('Pending'), 'url' => route('admin.comments.pending'), 'route' => 'admin.comments.pending'],
                    ['label' => __('Approved'), 'url' => route('admin.comments.approved'), 'route' => 'admin.comments.approved'],
                    ['label' => __('Spam'), 'url' => route('admin.comments.spam'), 'route' => 'admin.comments.spam'],
                    ['label' => __('Trash'), 'url' => route('admin.comments.trash'), 'route' => 'admin.comments.trash'],
                ],
            ],
            [
                'label' => __('Appearance'),
                'icon' => $ic($p('M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01')),
                'active' => request()->routeIs('admin.appearance.*'),
                'children' => [
                    ['label' => __('Themes'), 'url' => route('admin.appearance.themes'), 'route' => 'admin.appearance.themes'],
                    ['label' => __('Customize'), 'url' => route('admin.appearance.customize'), 'route' => 'admin.appearance.customize'],
                    ['label' => __('Widgets'), 'url' => route('admin.appearance.widgets'), 'route' => 'admin.appearance.widgets'],
                    ['label' => __('Menus'), 'url' => route('admin.appearance.menus'), 'route' => 'admin.appearance.menus'],
                    ['label' => __('Theme Editor'), 'url' => route('admin.appearance.editor'), 'route' => 'admin.appearance.editor'],
                    ['label' => __('Colors'), 'url' => route('admin.appearance.colors'), 'route' => 'admin.appearance.colors'],
                    ['label' => __('Options'), 'url' => route('admin.appearance.options'), 'route' => 'admin.appearance.options'],
                ],
            ],
            [
                'label' => __('Plugins'),
                'icon' => $ic($p('M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z')),
                'active' => request()->routeIs('admin.plugins.*'),
                'children' => [
                    ['label' => __('Installed'), 'url' => route('admin.plugins.index'), 'route' => 'admin.plugins.index'],
                    ['label' => __('Add New'), 'url' => route('admin.plugins.create'), 'route' => 'admin.plugins.create'],
                    ['label' => __('Editor'), 'url' => route('admin.plugins.editor'), 'route' => 'admin.plugins.editor'],
                ],
            ],
            [
                'label' => __('AI Center'),
                'icon' => $ic($p('M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z')),
                'active' => request()->routeIs('admin.ai.*'),
                'children' => [
                    ['label' => __('Overview'), 'url' => route('admin.ai.index'), 'route' => 'admin.ai.index'],
                    ['label' => __('Models'), 'url' => route('admin.ai.models'), 'route' => 'admin.ai.models'],
                    ['label' => __('Prompts'), 'url' => route('admin.ai.prompts'), 'route' => 'admin.ai.prompts'],
                    ['label' => __('Usage'), 'url' => route('admin.ai.usage'), 'route' => 'admin.ai.usage'],
                    ['label' => __('Tools'), 'url' => route('admin.ai.tools'), 'route' => 'admin.ai.tools'],
                ],
            ],
            [
                'label' => __('RJBuilder'),
                'icon' => $ic($p('M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z')),
                'url' => route('admin.rjbuilder.index'),
                'active' => request()->routeIs('admin.rjbuilder.*'),
                'children' => [],
            ],
        ];

        $pluginMenuItems = rj_apply_filters('admin.menu', []) ?? [];
        if (!is_array($pluginMenuItems)) {
            $pluginMenuItems = [];
        }
        usort($pluginMenuItems, fn($a, $b) => ($a['position'] ?? 100) <=> ($b['position'] ?? 100));
        $pluginMenuRoots = array_filter($pluginMenuItems, fn($i) => empty($i['parent']));
        $pluginMenuChildren = array_filter($pluginMenuItems, fn($i) => !empty($i['parent']));

        $defaultPluginIcon = $ic($p('M4 6h16M4 12h16M4 18h7'));
        $resolveUrl = fn($i) => isset($i['route']) && Route::has($i['route']) ? route($i['route']) : ($i['url'] ?? '#');

        $pluginNav = [];
        foreach ($pluginMenuRoots as $item) {
            $itemChildren = array_filter($pluginMenuChildren, fn($c) => ($c['parent'] ?? '') === ($item['id'] ?? ''));
            $children = [];
            foreach ($itemChildren as $child) {
                $children[] = [
                    'label' => $child['title'] ?? '',
                    'url' => $resolveUrl($child),
                    'route' => $child['route'] ?? null,
                ];
            }
            $pluginNav[] = [
                'label' => $item['title'] ?? '',
                'icon' => !empty($item['icon_svg']) ? $item['icon_svg'] : $defaultPluginIcon,
                'url' => $resolveUrl($item),
                'active' => isset($item['route']) && request()->routeIs($item['route'] . '*'),
                'children' => $children,
            ];
        }

        $coreBottom = [
            [
                'label' => __('Users'),
                'icon' => $ic($p('M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z')),
                'url' => route('admin.users.index'),
                'active' => request()->routeIs('admin.users.*'),
                'children' => [],
            ],
            [
                'label' => __('Support'),
                'icon' => $ic($p('M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.656l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z')),
                'active' => request()->routeIs('admin.support.*'),
                'children' => [
                    ['label' => __('Tickets'), 'url' => route('admin.support.index'), 'route' => 'admin.support.index'],
                    ['label' => __('Live Chat'), 'url' => route('admin.support.chat'), 'route' => 'admin.support.chat'],
                    ['label' => __('FAQ'), 'url' => route('admin.support.faq'), 'route' => 'admin.support.faq'],
                    ['label' => __('Contact'), 'url' => route('admin.support.contact'), 'route' => 'admin.support.contact'],
                    ['label' => __('Documentation'), 'url' => route('admin.support.docs'), 'route' => 'admin.support.docs'],
                    ['label' => __('System Status'), 'url' => route('admin.support.status'), 'route' => 'admin.support.status'],
                ],
            ],
            [
                'label' => __('Tools'),
                'icon' => $ic($p('M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z') . $p('M15 12a3 3 0 11-6 0 3 3 0 016 0z')),
                'active' => request()->routeIs('admin.tools.*'),
                'children' => [
                    ['label' => __('Import'), 'url' => route('admin.tools.import'), 'route' => 'admin.tools.import'],
                    ['label' => __('Export'), 'url' => route('admin.tools.export'), 'route' => 'admin.tools.export'],
                    ['label' => __('Site Health'), 'url' => route('admin.tools.health'), 'route' => 'admin.tools.health'],
                    ['label' => __('Backup'), 'url' => route('admin.tools.backup'), 'route' => 'admin.tools.backup'],
                    ['label' => __('Logs'), 'url' => route('admin.tools.logs'), 'route' => 'admin.tools.logs'],
                    ['label' => __('AI Diagnostics'), 'url' => route('admin.tools.diagnostics'), 'route' => 'admin.tools.diagnostics'],
                ],
            ],
            [
                'label' => __('Updates'),
                'icon' => $ic($p('M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15')),
                'url' => route('admin.updates.index'),
                'active' => request()->routeIs('admin.updates.*'),
                'children' => [],
            ],
            [
                'label' => __('RJ Services'),
                'icon' => $ic($p('M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z')),
                'url' => route('admin.services.index'),
                'active' => request()->routeIs('admin.services.*'),
                'children' => [],
            ],
            [
                'label' => __('Settings'),
                'icon' => $ic($p('M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4')),
                'url' => route('admin.settings.index'),
                'active' => request()->routeIs('admin.settings.*'),
                'children' => [],
            ],
            [
                'label' => __('RJ About'),
                'icon' => $ic($p('M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z')),
                'active' => request()->routeIs('admin.about.*'),
                'children' => [
                    ['label' => __('Info'), 'url' => route('admin.about.index'), 'route' => 'admin.about.index'],
                    ['label' => __('API Document'), 'url' => route('admin.about.api'), 'route' => 'admin.about.api'],
                    ['label' => __('Theme Document'), 'url' => route('admin.about.theme'), 'route' => 'admin.about.theme'],
                    ['label' => __('Admin Document'), 'url' => route('admin.about.admin'), 'route' => 'admin.about.admin'],
                    ['label' => __('Plugin Document'), 'url' => route('admin.about.plugin'), 'route' => 'admin.about.plugin'],
                    ['label' => __('Developer Guide'), 'url' => route('admin.about.developer'), 'route' => 'admin.about.developer'],
                    ['label' => __('Hooks Reference'), 'url' => route('admin.about.hooks'), 'route' => 'admin.about.hooks'],
                    ['label' => __('Other Documents'), 'url' => route('admin.about.other'), 'route' => 'admin.about.other'],
                    ['label' => __('Credits'), 'url' => route('admin.about.credits'), 'route' => 'admin.about.credits'],
                    ['label' => __('License'), 'url' => route('admin.about.license'), 'route' => 'admin.about.license'],
                ],
            ],
        ];

        $navSections = [
            ['title' => null, 'items' => $coreTop],
            ['title' => __('Plugins'), 'items' => $pluginNav],
            ['title' => null, 'items' => $coreBottom],
        ];
    @endphp

    <div class="flex min-h-screen" x-data="{ userMenu: false, langMenu: false }">

        <aside class="w-64 h-screen sticky top-0 self-start z-30 bg-gradient-to-b from-rj-dark-1 via-rj-dark-2 to-rj-dark-1 text-gray-300 flex-shrink-0 flex flex-col">
            <div class="h-20 flex items-center justify-center px-5 border-b border-white/5 flex-shrink-0">
                <a href="{{ route('admin.dashboard') }}" class="hover:opacity-90 transition inline-flex items-center">
                    @php $logoMask = rj_logo_mask_url(); @endphp
                    @if ($logoMask)
                        <div class="rj-logo-mask h-12 w-40" style="-webkit-mask-image: url('{{ $logoMask }}'); mask-image: url('{{ $logoMask }}');"></div>
                    @else
                        <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="h-12 w-auto">
                    @endif
                </a>
            </div>

            <nav class="rj-scrollbar flex-1 overflow-y-auto p-3 space-y-0.5 text-sm">
                @foreach ($navSections as $section)
                    @if (!empty($section['items']))
                        @if ($section['title'])
                            <div class="pt-3 mt-3 border-t border-white/5">
                                <div class="px-3 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    {{ $section['title'] }}
                                </div>
                        @endif

                        @foreach ($section['items'] as $item)
                            @php
                                $hasChildren = !empty($item['children']);
                                $itemUrl = $item['url'] ?? ($hasChildren ? $item['children'][0]['url'] : '#');
                                
                                $hasActiveChild = false;
                                if ($hasChildren) {
                                    foreach ($item['children'] as $child) {
                                        if (!empty($child['route']) && request()->routeIs($child['route'])) {
                                            $hasActiveChild = true;
                                            break;
                                        }
                                    }
                                }
                                $isParentActive = $item['active'] || $hasActiveChild;
                            @endphp

                            <div x-data="{ open: {{ $hasActiveChild ? 'true' : 'false' }} }">
                                @if ($hasChildren)
                                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ $isParentActive ? 'bg-white/10 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                                        <div class="flex items-center gap-3">
                                            <span class="w-5 h-5 flex-shrink-0 flex items-center justify-center text-current">{!! $item['icon'] !!}</span>
                                            <span>{{ $item['label'] }}</span>
                                        </div>
                                        <svg class="w-3.5 h-3.5 opacity-50 transition-transform duration-200" :class="{'rotate-90': open}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                    
                                    <div x-show="open" x-transition.opacity class="mt-1 space-y-0.5 ml-4 border-l border-white/10 pl-2">
                                        @foreach ($item['children'] as $child)
                                            @php $childActive = !empty($child['route']) && request()->routeIs($child['route']); @endphp
                                            <a href="{{ $child['url'] }}"
                                               class="block px-3 py-1.5 rounded-lg text-sm transition {{ $childActive ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                                                {{ $child['label'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <a href="{{ $itemUrl }}"
                                       class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ $item['active'] ? 'bg-gradient-to-r from-rj-purple/30 to-rj-pink/20 text-white border-l-2 border-rj-purple-light' : 'hover:bg-white/5 hover:text-white' }}">
                                        <span class="w-5 h-5 flex-shrink-0 flex items-center justify-center text-current">{!! $item['icon'] !!}</span>
                                        <span>{{ $item['label'] }}</span>
                                    </a>
                                @endif
                            </div>
                        @endforeach

                        @if ($section['title'])
                            </div>
                        @endif
                    @endif
                @endforeach
            </nav>

            <div class="p-3 border-t border-white/5 text-xs text-gray-500 flex items-center justify-between flex-shrink-0">
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
                @if ($errors->any())
                    <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-r-lg rj-fade-in">
                        <ul class="text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

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

    @include('admin.partials.media-picker')

</body>
</html>