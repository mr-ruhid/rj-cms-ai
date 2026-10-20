@extends('admin.layouts.app')

@section('content')
    <div class="flex gap-6">
        <aside class="w-56 flex-shrink-0">
            <nav class="bg-white rounded shadow p-2 space-y-1 text-sm">
                <a href="{{ route('admin.settings.general') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('admin.settings.general') ? 'bg-gray-100 font-medium' : '' }}">{{ __('General') }}</a>
                <a href="{{ route('admin.settings.writing') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('admin.settings.writing') ? 'bg-gray-100 font-medium' : '' }}">{{ __('Writing') }}</a>
                <a href="{{ route('admin.settings.reading') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('admin.settings.reading') ? 'bg-gray-100 font-medium' : '' }}">{{ __('Reading') }}</a>
                <a href="{{ route('admin.settings.discussion') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('admin.settings.discussion') ? 'bg-gray-100 font-medium' : '' }}">{{ __('Discussion') }}</a>
                <a href="{{ route('admin.settings.media') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('admin.settings.media') ? 'bg-gray-100 font-medium' : '' }}">{{ __('Media') }}</a>
                <a href="{{ route('admin.settings.permalinks') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('admin.settings.permalinks') ? 'bg-gray-100 font-medium' : '' }}">{{ __('Permalinks') }}</a>
                <a href="{{ route('admin.settings.language') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('admin.settings.language') ? 'bg-gray-100 font-medium' : '' }}">{{ __('Language') }}</a>
                <a href="{{ route('admin.settings.colors') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('admin.settings.colors') ? 'bg-gray-100 font-medium' : '' }}">{{ __('Colors') }}</a>
                <a href="{{ route('admin.settings.seo') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('admin.settings.seo') ? 'bg-gray-100 font-medium' : '' }}">{{ __('SEO') }}</a>
                <a href="{{ route('admin.settings.email') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('admin.settings.email') ? 'bg-gray-100 font-medium' : '' }}">{{ __('Email / SMTP') }}</a>
                <a href="{{ route('admin.settings.security') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('admin.settings.security') ? 'bg-gray-100 font-medium' : '' }}">{{ __('Security') }}</a>
                <a href="{{ route('admin.settings.backup') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('admin.settings.backup') ? 'bg-gray-100 font-medium' : '' }}">{{ __('Backup') }}</a>
                <a href="{{ route('admin.settings.cache') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('admin.settings.cache') ? 'bg-gray-100 font-medium' : '' }}">{{ __('Cache') }}</a>
                <a href="{{ route('admin.settings.social') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('admin.settings.social') ? 'bg-gray-100 font-medium' : '' }}">{{ __('Social Login') }}</a>
                <a href="{{ route('admin.settings.notifications') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('admin.settings.notifications') ? 'bg-gray-100 font-medium' : '' }}">{{ __('Notifications') }}</a>
                <a href="{{ route('admin.settings.api') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('admin.settings.api') ? 'bg-gray-100 font-medium' : '' }}">{{ __('API & Integrations') }}</a>
                <a href="{{ route('admin.settings.ai') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('admin.settings.ai') ? 'bg-gray-100 font-medium' : '' }}">{{ __('AI') }}</a>
                <a href="{{ route('admin.settings.advanced') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('admin.settings.advanced') ? 'bg-gray-100 font-medium' : '' }}">{{ __('Advanced') }}</a>
                <a href="{{ route('admin.settings.roles') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('admin.settings.roles') ? 'bg-gray-100 font-medium' : '' }}">{{ __('Roles') }}</a>
            </nav>
        </aside>

        <div class="flex-1">
            @yield('settings')
        </div>
    </div>
@endsection
