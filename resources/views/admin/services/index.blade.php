@extends('admin.layouts.app')

@section('title', __('RJ Services'))

@section('content')
<div class="max-w-7xl mx-auto space-y-8 pb-12">

    <div class="text-center max-w-2xl mx-auto py-6">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-rj-purple to-rj-pink text-white shadow-lg mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        </div>
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ __('RJ Services') }}</h1>
        <p class="text-sm text-gray-500 mt-3 leading-relaxed">{{ __('Professional services to upgrade your digital presence. From custom development to SEO and technical support.') }}</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 md:p-8">
        <h3 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wider flex items-center gap-2">
            <svg class="w-4 h-4 text-rj-purple" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            {{ __('Contact Us') }}
        </h3>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            <a href="https://wa.me/994506636031" target="_blank" rel="noopener" class="group flex flex-col items-center justify-center p-4 rounded-xl border border-gray-200 hover:border-green-400 hover:bg-green-50 transition">
                <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-700 group-hover:text-green-700">WhatsApp</span>
                <span class="text-[10px] text-gray-400 mt-0.5">+994 50 663 60 31</span>
            </a>

            <a href="https://t.me/veb_developher_az" target="_blank" rel="noopener" class="group flex flex-col items-center justify-center p-4 rounded-xl border border-gray-200 hover:border-sky-400 hover:bg-sky-50 transition">
                <div class="w-10 h-10 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-700 group-hover:text-sky-700">Telegram</span>
                <span class="text-[10px] text-gray-400 mt-0.5">@veb_developher_az</span>
            </a>

            <a href="mailto:rjnexus@hotmail.com" class="group flex flex-col items-center justify-center p-4 rounded-xl border border-gray-200 hover:border-rose-400 hover:bg-rose-50 transition">
                <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-700 group-hover:text-rose-700">Email</span>
                <span class="text-[10px] text-gray-400 mt-0.5">rjnexus@hotmail.com</span>
            </a>

            <a href="tel:+994506636031" class="group flex flex-col items-center justify-center p-4 rounded-xl border border-gray-200 hover:border-indigo-400 hover:bg-indigo-50 transition">
                <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-700 group-hover:text-indigo-700">Phone</span>
                <span class="text-[10px] text-gray-400 mt-0.5">+994 50 663 60 31</span>
            </a>

            <a href="https://discord.gg/RxDCKz4ZR" target="_blank" rel="noopener" class="group flex flex-col items-center justify-center p-4 rounded-xl border border-gray-200 hover:border-indigo-500 hover:bg-indigo-50 transition">
                <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.317 4.37a19.791 19.791 0 00-4.885-1.515.074.074 0 00-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 00-5.487 0 12.64 12.64 0 00-.617-1.25.077.077 0 00-.079-.037A19.736 19.736 0 003.677 4.37a.07.07 0 00-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 00.031.057 19.9 19.9 0 005.993 3.03.078.078 0 00.084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 00-.041-.106 13.107 13.107 0 01-1.872-.892.077.077 0 01-.008-.128 10.2 10.2 0 00.372-.292.074.074 0 01.077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 01.078.01c.12.098.246.198.373.292a.077.077 0 01-.006.127 12.299 12.299 0 01-1.873.892.077.077 0 00-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 00.084.028 19.839 19.839 0 006.002-3.03.077.077 0 00.032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 00-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-700 group-hover:text-indigo-700">Discord</span>
                <span class="text-[10px] text-gray-400 mt-0.5">Join Server</span>
            </a>

            <a href="https://github.com/orgs/rj-nexus/discussions/categories/custom-order" target="_blank" rel="noopener" class="group flex flex-col items-center justify-center p-4 rounded-xl border border-gray-200 hover:border-gray-800 hover:bg-gray-50 transition">
                <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-700 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 .5C5.4.5 0 5.9 0 12.5c0 5.3 3.4 9.8 8.2 11.4.6.1.8-.3.8-.6v-2c-3.3.7-4-1.6-4-1.6-.5-1.4-1.3-1.8-1.3-1.8-1.1-.7.1-.7.1-.7 1.2.1 1.8 1.2 1.8 1.2 1.1 1.8 2.8 1.3 3.5 1 .1-.8.4-1.3.7-1.6-2.7-.3-5.5-1.3-5.5-5.9 0-1.3.5-2.4 1.2-3.2-.1-.3-.5-1.5.1-3.2 0 0 1-.3 3.3 1.2.9-.3 2-.4 3-.4s2.1.1 3 .4c2.3-1.5 3.3-1.2 3.3-1.2.6 1.7.2 2.9.1 3.2.8.8 1.2 1.9 1.2 3.2 0 4.6-2.8 5.6-5.5 5.9.4.4.8 1.1.8 2.2v3.3c0 .3.2.7.8.6C20.6 22.3 24 17.8 24 12.5 24 5.9 18.6.5 12 .5z"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-700 group-hover:text-gray-900">GitHub</span>
                <span class="text-[10px] text-gray-400 mt-0.5">Custom Orders</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition group flex flex-col">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center mb-4 shadow-sm group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">{{ __('Upgrade RJ CMS') }}</h3>
            <p class="text-xs text-gray-500 leading-relaxed mb-4 flex-1">{{ __('Upgrade your systems to the latest version. Move from RJ CMS 5.1 to RJ Nexus for free.') }}</p>
            <a href="https://wa.me/994506636031" target="_blank" class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1">{{ __('Request Upgrade') }} <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></a>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition group flex flex-col">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center mb-4 shadow-sm group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">{{ __('SMS Service') }}</h3>
            <p class="text-xs text-gray-500 leading-relaxed mb-4 flex-1">{{ __('Send SMS notifications to your customers worldwide with high delivery rates.') }}</p>
            <a href="https://t.me/veb_developher_az" target="_blank" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1">{{ __('Get Quote') }} <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></a>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition group flex flex-col">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-rose-500 to-pink-600 text-white flex items-center justify-center mb-4 shadow-sm group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">{{ __('Email Marketing') }}</h3>
            <p class="text-xs text-gray-500 leading-relaxed mb-4 flex-1">{{ __('Set up email campaigns and newsletters to engage with your audience effectively.') }}</p>
            <a href="mailto:rjnexus@hotmail.com" class="text-xs font-bold text-rose-600 hover:text-rose-700 flex items-center gap-1">{{ __('Get Quote') }} <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></a>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition group flex flex-col">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-white flex items-center justify-center mb-4 shadow-sm group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">{{ __('Order Custom Website') }}</h3>
            <p class="text-xs text-gray-500 leading-relaxed mb-4 flex-1">{{ __('Get a professional website designed and built specifically for your business needs.') }}</p>
            <a href="https://github.com/orgs/rj-nexus/discussions/categories/custom-order" target="_blank" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">{{ __('Order Now') }} <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></a>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition group flex flex-col">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 text-white flex items-center justify-center mb-4 shadow-sm group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">{{ __('360° Digital Services') }}</h3>
            <p class="text-xs text-gray-500 leading-relaxed mb-4 flex-1">{{ __('Full 360-degree digital solutions including branding, design, development, and marketing.') }}</p>
            <a href="https://wa.me/994506636031" target="_blank" class="text-xs font-bold text-purple-600 hover:text-purple-700 flex items-center gap-1">{{ __('Contact Us') }} <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></a>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition group flex flex-col relative overflow-hidden">
            <div class="absolute top-4 right-4 bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-[10px] font-bold px-2 py-1 rounded-full shadow-sm">$250 / {{ __('month') }}</div>
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 text-white flex items-center justify-center mb-4 shadow-sm group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">{{ __('SEO Service') }}</h3>
            <p class="text-xs text-gray-500 leading-relaxed mb-4 flex-1">{{ __('Rank your website on the first page of Google with monthly SEO optimization.') }}</p>
            <a href="mailto:rjnexus@hotmail.com" class="text-xs font-bold text-green-600 hover:text-green-700 flex items-center gap-1">{{ __('Subscribe') }} <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></a>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition group flex flex-col">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600 text-white flex items-center justify-center mb-4 shadow-sm group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">{{ __('Order Software') }}</h3>
            <p class="text-xs text-gray-500 leading-relaxed mb-4 flex-1">{{ __('Custom software solutions and applications developed by our expert team.') }}</p>
            <a href="https://github.com/orgs/rj-nexus/discussions/categories/custom-order" target="_blank" class="text-xs font-bold text-cyan-600 hover:text-cyan-700 flex items-center gap-1">{{ __('Order Now') }} <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></a>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition group flex flex-col">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-fuchsia-500 to-purple-600 text-white flex items-center justify-center mb-4 shadow-sm group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">{{ __('Paid Support') }}</h3>
            <p class="text-xs text-gray-500 leading-relaxed mb-4 flex-1">{{ __('Priority 24/7 support for your business. Direct access to our senior developers.') }}</p>
            <a href="https://discord.gg/RxDCKz4ZR" target="_blank" class="text-xs font-bold text-fuchsia-600 hover:text-fuchsia-700 flex items-center gap-1">{{ __('Contact Us') }} <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></a>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition group flex flex-col relative overflow-hidden">
            <div class="absolute top-4 right-4 bg-gradient-to-r from-slate-700 to-gray-900 text-white text-[10px] font-bold px-2 py-1 rounded-full shadow-sm">$200 / {{ __('month') }}</div>
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-slate-600 to-gray-800 text-white flex items-center justify-center mb-4 shadow-sm group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">{{ __('Technical Support') }}</h3>
            <p class="text-xs text-gray-500 leading-relaxed mb-4 flex-1">{{ __('Monthly technical support package for system maintenance, bug fixes, and updates.') }}</p>
            <a href="tel:+994506636031" class="text-xs font-bold text-slate-700 hover:text-gray-900 flex items-center gap-1">{{ __('Call Now') }} <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></a>
        </div>

        <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl shadow-sm p-6 group flex flex-col text-white relative overflow-hidden">
            <div class="absolute -right-8 -top-8 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
            <div class="w-12 h-12 rounded-xl bg-white/10 text-white flex items-center justify-center mb-4 shadow-sm group-hover:scale-110 transition-transform backdrop-blur-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h.01M12 12h.01M19 12h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="text-base font-bold mb-2">{{ __('Custom Price') }}</h3>
            <p class="text-xs text-gray-300 leading-relaxed mb-4 flex-1">{{ __('Other services available on request. Contact us for a custom quote based on your needs.') }}</p>
            <a href="https://t.me/veb_developher_az" target="_blank" class="text-xs font-bold text-white hover:text-rj-purple-light flex items-center gap-1">{{ __('Contact Us') }} <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></a>
        </div>

    </div>
</div>
@endsection
