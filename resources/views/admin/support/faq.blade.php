@extends('admin.layouts.app')

@section('title', __('Frequently Asked Questions'))

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ openItem: null, search: '' }">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.support.index') }}" class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-rj-purple hover:border-rj-purple/30 transition shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ __('Frequently Asked Questions') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Find quick answers to common questions about RJ CMS.') }}</p>
        </div>
    </div>

    <div class="relative">
        <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="text" x-model="search" placeholder="{{ __('Search questions...') }}" class="w-full pl-12 pr-4 py-4 rounded-2xl border border-gray-200 bg-white focus:border-rj-purple focus:ring-4 focus:ring-rj-purple/10 text-sm shadow-sm transition-all">
    </div>

    <div class="space-y-3">
        
        <div x-show="search === '' || 'how do i update my theme'.includes(search.toLowerCase())" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden transition-all duration-200">
            <button @click="openItem = openItem === 1 ? null : 1" class="w-full px-6 py-4 flex items-center justify-between gap-4 text-left hover:bg-gray-50/50 transition">
                <span class="text-sm font-bold text-gray-900">{{ __('How do I update my theme?') }}</span>
                <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="openItem === 1 ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="openItem === 1" x-collapse x-cloak>
                <div class="px-6 pb-4 text-sm text-gray-600 leading-relaxed border-t border-gray-100 pt-3">
                    {{ __('Go to Appearance > Themes, click "Check for Updates", and if an update is available, click the "Update" button. Always backup your site before updating.') }}
                </div>
            </div>
        </div>

        <div x-show="search === '' || 'how can i add a new language'.includes(search.toLowerCase())" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden transition-all duration-200">
            <button @click="openItem = openItem === 2 ? null : 2" class="w-full px-6 py-4 flex items-center justify-between gap-4 text-left hover:bg-gray-50/50 transition">
                <span class="text-sm font-bold text-gray-900">{{ __('How can I add a new language?') }}</span>
                <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="openItem === 2 ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="openItem === 2" x-collapse x-cloak>
                <div class="px-6 pb-4 text-sm text-gray-600 leading-relaxed border-t border-gray-100 pt-3">
                    {{ __('Go to Settings > Language. You can add a new language, and our AI system can automatically translate the entire site for you.') }}
                </div>
            </div>
        </div>

        <div x-show="search === '' || 'how do i backup my website'.includes(search.toLowerCase())" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden transition-all duration-200">
            <button @click="openItem = openItem === 3 ? null : 3" class="w-full px-6 py-4 flex items-center justify-between gap-4 text-left hover:bg-gray-50/50 transition">
                <span class="text-sm font-bold text-gray-900">{{ __('How do I backup my website?') }}</span>
                <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="openItem === 3 ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="openItem === 3" x-collapse x-cloak>
                <div class="px-6 pb-4 text-sm text-gray-600 leading-relaxed border-t border-gray-100 pt-3">
                    {{ __('Go to Tools > Backup. Click "Create Backup Now". The system will create a ZIP file containing your database and media files. You can download it or keep it on the server.') }}
                </div>
            </div>
        </div>

        <div x-show="search === '' || 'where can i generate api keys'.includes(search.toLowerCase())" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden transition-all duration-200">
            <button @click="openItem = openItem === 4 ? null : 4" class="w-full px-6 py-4 flex items-center justify-between gap-4 text-left hover:bg-gray-50/50 transition">
                <span class="text-sm font-bold text-gray-900">{{ __('Where can I generate API keys?') }}</span>
                <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="openItem === 4 ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="openItem === 4" x-collapse x-cloak>
                <div class="px-6 pb-4 text-sm text-gray-600 leading-relaxed border-t border-gray-100 pt-3">
                    {{ __('Go to Settings > API & Integrations. Enter an application name and click "Generate Key". This will give you a Public and Private key to access your site externally.') }}
                </div>
            </div>
        </div>

        <div x-show="search === '' || 'the site is running slow'.includes(search.toLowerCase())" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden transition-all duration-200">
            <button @click="openItem = openItem === 5 ? null : 5" class="w-full px-6 py-4 flex items-center justify-between gap-4 text-left hover:bg-gray-50/50 transition">
                <span class="text-sm font-bold text-gray-900">{{ __('The site is running slow. What should I do?') }}</span>
                <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="openItem === 5 ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="openItem === 5" x-collapse x-cloak>
                <div class="px-6 pb-4 text-sm text-gray-600 leading-relaxed border-t border-gray-100 pt-3">
                    {{ __('First, go to Settings > Cache and clear all caches. Then, go to Tools > Health to run a diagnostic check. Our AI will analyze your site and suggest performance improvements.') }}
                </div>
            </div>
        </div>

        <div x-show="search !== '' && !['how do i update my theme', 'how can i add a new language', 'how do i backup my website', 'where can i generate api keys', 'the site is running slow'].some(t => t.includes(search.toLowerCase()))" class="text-center py-12">
            <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900">{{ __('No results found') }}</h3>
            <p class="text-sm text-gray-500 mt-1">{{ __('Try a different keyword or submit a support ticket.') }}</p>
        </div>

    </div>

    <div class="bg-gradient-to-r from-rj-purple to-rj-pink rounded-2xl shadow-sm p-8 text-center text-white relative overflow-hidden">
        <div class="absolute -right-8 -top-8 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute -left-8 -bottom-8 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        <h3 class="text-lg font-bold mb-2 relative z-10">{{ __('Still need help?') }}</h3>
        <p class="text-sm text-white/80 mb-5 relative z-10">{{ __('Our support team is always ready to assist you.') }}</p>
        <a href="{{ route('admin.support.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-white text-rj-purple text-sm font-bold hover:bg-gray-50 transition shadow-sm relative z-10">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            {{ __('Submit a Ticket') }}
        </a>
    </div>

</div>
@endsection