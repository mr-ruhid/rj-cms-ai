@extends('admin.layouts.app')

@section('title', __('Settings'))

@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">

        <a href="{{ route('admin.settings.general') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-rj-purple/30 transition group">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
            </div>
            <h3 class="font-semibold text-gray-900 group-hover:text-rj-purple transition">{{ __('General') }}</h3>
            <p class="text-xs text-gray-500 mt-1">{{ __('Site name, URL, timezone, language') }}</p>
        </a>

        <a href="{{ route('admin.settings.writing') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-rj-purple/30 transition group">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            </div>
            <h3 class="font-semibold text-gray-900 group-hover:text-rj-purple transition">{{ __('Writing') }}</h3>
            <p class="text-xs text-gray-500 mt-1">{{ __('Default category, post format, editor') }}</p>
        </a>

        <a href="{{ route('admin.settings.reading') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-rj-purple/30 transition group">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <h3 class="font-semibold text-gray-900 group-hover:text-rj-purple transition">{{ __('Reading') }}</h3>
            <p class="text-xs text-gray-500 mt-1">{{ __('Homepage, posts per page, feed') }}</p>
        </a>

        <a href="{{ route('admin.settings.discussion') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-rj-purple/30 transition group">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </div>
            <h3 class="font-semibold text-gray-900 group-hover:text-rj-purple transition">{{ __('Discussion') }}</h3>
            <p class="text-xs text-gray-500 mt-1">{{ __('Comments, moderation, avatars') }}</p>
        </a>

        <a href="{{ route('admin.settings.media') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-rj-purple/30 transition group">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2zm6-10a1 1 0 11-2 0 1 1 0 012 0z"/></svg>
            </div>
            <h3 class="font-semibold text-gray-900 group-hover:text-rj-purple transition">{{ __('Media') }}</h3>
            <p class="text-xs text-gray-500 mt-1">{{ __('Image sizes, upload folder, optimization') }}</p>
        </a>

        <a href="{{ route('admin.settings.permalinks') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-rj-purple/30 transition group">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            </div>
            <h3 class="font-semibold text-gray-900 group-hover:text-rj-purple transition">{{ __('Permalinks') }}</h3>
            <p class="text-xs text-gray-500 mt-1">{{ __('URL structure, custom patterns') }}</p>
        </a>

        <a href="{{ route('admin.settings.language') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-rj-purple/30 transition group">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
            </div>
            <h3 class="font-semibold text-gray-900 group-hover:text-rj-purple transition">{{ __('Language') }}</h3>
            <p class="text-xs text-gray-500 mt-1">{{ __('Locale, RTL, translation files, AI') }}</p>
        </a>

        <a href="{{ route('admin.settings.colors') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-rj-purple/30 transition group">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
            </div>
            <h3 class="font-semibold text-gray-900 group-hover:text-rj-purple transition">{{ __('Colors') }}</h3>
            <p class="text-xs text-gray-500 mt-1">{{ __('Admin theme, frontend palette, dark mode') }}</p>
        </a>

        <a href="{{ route('admin.settings.seo') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-rj-purple/30 transition group">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <h3 class="font-semibold text-gray-900 group-hover:text-rj-purple transition">{{ __('SEO') }}</h3>
            <p class="text-xs text-gray-500 mt-1">{{ __('Meta, sitemap, robots, schema, AI') }}</p>
        </a>

        <a href="{{ route('admin.settings.email') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-rj-purple/30 transition group">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="font-semibold text-gray-900 group-hover:text-rj-purple transition">{{ __('Email / SMTP') }}</h3>
            <p class="text-xs text-gray-500 mt-1">{{ __('Driver, host, templates, test, AI') }}</p>
        </a>

        <a href="{{ route('admin.settings.payments') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-rj-purple/30 transition group">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
            <h3 class="font-semibold text-gray-900 group-hover:text-rj-purple transition">{{ __('Payments') }}</h3>
            <p class="text-xs text-gray-500 mt-1">{{ __('Stripe, PayPal, currency, tax, invoice') }}</p>
        </a>

        <a href="{{ route('admin.settings.security') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-rj-purple/30 transition group">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <h3 class="font-semibold text-gray-900 group-hover:text-rj-purple transition">{{ __('Security') }}</h3>
            <p class="text-xs text-gray-500 mt-1">{{ __('2FA, login limit, captcha, firewall') }}</p>
        </a>

        <a href="{{ route('admin.settings.backup') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-rj-purple/30 transition group">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
            </div>
            <h3 class="font-semibold text-gray-900 group-hover:text-rj-purple transition">{{ __('Backup') }}</h3>
            <p class="text-xs text-gray-500 mt-1">{{ __('Auto, manual, restore, cloud') }}</p>
        </a>

        <a href="{{ route('admin.settings.cache') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-rj-purple/30 transition group">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <h3 class="font-semibold text-gray-900 group-hover:text-rj-purple transition">{{ __('Cache') }}</h3>
            <p class="text-xs text-gray-500 mt-1">{{ __('Enable, TTL, clear, CDN') }}</p>
        </a>

        <a href="{{ route('admin.settings.social') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-rj-purple/30 transition group">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <h3 class="font-semibold text-gray-900 group-hover:text-rj-purple transition">{{ __('Social Login') }}</h3>
            <p class="text-xs text-gray-500 mt-1">{{ __('Google, Facebook, GitHub, Apple') }}</p>
        </a>

        <a href="{{ route('admin.settings.notifications') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-rj-purple/30 transition group">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            <h3 class="font-semibold text-gray-900 group-hover:text-rj-purple transition">{{ __('Notifications') }}</h3>
            <p class="text-xs text-gray-500 mt-1">{{ __('Email, push, SMS, webhook') }}</p>
        </a>

        <a href="{{ route('admin.settings.api') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-rj-purple/30 transition group">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="font-semibold text-gray-900 group-hover:text-rj-purple transition">{{ __('API & Integrations') }}</h3>
            <p class="text-xs text-gray-500 mt-1">{{ __('API keys, webhooks, OAuth') }}</p>
        </a>

        <a href="{{ route('admin.settings.ai') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-rj-purple/30 transition group">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            </div>
            <h3 class="font-semibold text-gray-900 group-hover:text-rj-purple transition">{{ __('AI') }}</h3>
            <p class="text-xs text-gray-500 mt-1">{{ __('Providers, models, limits, cost') }}</p>
        </a>

        <a href="{{ route('admin.settings.advanced') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-rj-purple/30 transition group">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <h3 class="font-semibold text-gray-900 group-hover:text-rj-purple transition">{{ __('Advanced') }}</h3>
            <p class="text-xs text-gray-500 mt-1">{{ __('Custom code, debug mode, maintenance') }}</p>
        </a>

        <a href="{{ route('admin.settings.roles') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-rj-purple/30 transition group">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <h3 class="font-semibold text-gray-900 group-hover:text-rj-purple transition">{{ __('Roles') }}</h3>
            <p class="text-xs text-gray-500 mt-1">{{ __('Create roles, permission matrix') }}</p>
        </a>

    </div>
@endsection
