@extends('admin.layouts.app')

@section('title', __('API Documentation'))

@section('content')
<div class="max-w-7xl mx-auto" x-data="{ activeSection: 'introduction' }">
    <div class="flex flex-col lg:flex-row gap-8">

        <!-- Sol Naviqasiya (Sticky) -->
        <div class="w-full lg:w-64 flex-shrink-0">
            <div class="sticky top-24 space-y-1 bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <div class="px-3 py-2 mb-2">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ __('Contents') }}</h3>
                </div>
                <a href="#introduction" @click="activeSection = 'introduction'" :class="activeSection === 'introduction' ? 'bg-rj-purple/10 text-rj-purple font-semibold' : 'text-gray-600 hover:bg-gray-50'" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ __('Introduction') }}
                </a>
                <a href="#authentication" @click="activeSection = 'authentication'" :class="activeSection === 'authentication' ? 'bg-rj-purple/10 text-rj-purple font-semibold' : 'text-gray-600 hover:bg-gray-50'" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    {{ __('Authentication') }}
                </a>
                <a href="#products" @click="activeSection = 'products'" :class="activeSection === 'products' ? 'bg-rj-purple/10 text-rj-purple font-semibold' : 'text-gray-600 hover:bg-gray-50'" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    {{ __('Products') }}
                </a>
                <a href="#orders" @click="activeSection = 'orders'" :class="activeSection === 'orders' ? 'bg-rj-purple/10 text-rj-purple font-semibold' : 'text-gray-600 hover:bg-gray-50'" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    {{ __('Orders') }}
                </a>
                <a href="#resources" @click="activeSection = 'resources'" :class="activeSection === 'resources' ? 'bg-rj-purple/10 text-rj-purple font-semibold' : 'text-gray-600 hover:bg-gray-50'" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    {{ __('Resources') }}
                </a>
            </div>
        </div>

        <!-- Əsas Məzmun -->
        <div class="flex-1 min-w-0 space-y-8">

            <!-- Səhifə Başlığı -->
            <div class="bg-gradient-to-br from-rj-dark-1 via-rj-dark-2 to-rj-dark-3 rounded-3xl p-8 text-white relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full bg-rj-purple/20 blur-3xl"></div>
                <div class="absolute -bottom-10 -left-10 w-40 h-40 rounded-full bg-rj-pink/20 blur-3xl"></div>
                <div class="relative">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-xs font-bold uppercase tracking-widest mb-4">
                        <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                        RJ Commerce API v1.0
                    </div>
                    <h1 class="text-3xl font-extrabold mb-3">{{ __('API Documentation') }}</h1>
                    <p class="text-gray-300 text-sm max-w-lg leading-relaxed">{{ __('Connect your external applications, mobile apps, or ERP systems to manage orders and sync products in real-time.') }}</p>
                </div>
            </div>

            <!-- 1. Introduction -->
            <section id="introduction" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 scroll-mt-24">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-rj-purple/10 text-rj-purple flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">{{ __('Introduction') }}</h2>
                </div>
                <p class="text-sm text-gray-600 leading-relaxed mb-4">{{ __('The RJ Commerce API allows you to interact with your store data programmatically. You can fetch products, manage orders, and update shipping details from any external application.') }}</p>
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 font-mono text-sm text-gray-700">
                    <span class="text-rj-purple font-bold">Base URL:</span> {{ url('/api/commerce/v1') }}
                </div>
            </section>

            <!-- 2. Authentication -->
            <section id="authentication" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 scroll-mt-24">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">{{ __('Authentication') }}</h2>
                </div>
                <p class="text-sm text-gray-600 leading-relaxed mb-4">{{ __('All API requests must be authenticated using your Public Key and Secret Key. You can find these keys in the Commerce Settings under the API tab.') }}</p>
                <div class="space-y-3">
                    <div>
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Header') }}</span>
                        <div class="mt-1 bg-gray-900 rounded-xl p-4 font-mono text-sm text-green-400 overflow-x-auto">
                            X-Public-Key: your_public_key_here<br>
                            Authorization: Bearer your_secret_key_here
                        </div>
                    </div>
                </div>
            </section>

            <!-- 3. Products -->
            <section id="products" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 scroll-mt-24">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">{{ __('Products') }}</h2>
                </div>

                <div class="space-y-6">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-700 text-xs font-bold uppercase">GET</span>
                            <code class="text-sm font-mono text-gray-700">/products</code>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">{{ __('Retrieve a paginated list of published products. You can filter by category, brand, or search term.') }}</p>
                        <details class="group">
                            <summary class="text-xs font-medium text-rj-purple cursor-pointer hover:underline">{{ __('View Example Response') }}</summary>
                            <div class="mt-2 bg-gray-900 rounded-xl p-4 font-mono text-xs text-gray-300 overflow-x-auto">
                                <pre>{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "slug": "black-phantom",
        "price": "150.00",
        "type": "physical",
        "translations": [...],
        "variations": [...]
      }
    ]
  }
}</pre>
                            </div>
                        </details>
                    </div>

                    <div class="border-t border-gray-100 pt-6">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-700 text-xs font-bold uppercase">GET</span>
                            <code class="text-sm font-mono text-gray-700">/products/{slug}</code>
                        </div>
                        <p class="text-sm text-gray-600">{{ __('Retrieve full details of a single product, including all variations, attributes, and SEO data.') }}</p>
                    </div>
                </div>
            </section>

            <!-- 4. Orders -->
            <section id="orders" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 scroll-mt-24">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">{{ __('Orders') }}</h2>
                </div>

                <div class="space-y-6">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-700 text-xs font-bold uppercase">GET</span>
                            <code class="text-sm font-mono text-gray-700">/orders</code>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">{{ __('Retrieve a list of all orders. You can filter by status using the query parameter.') }}</p>
                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-100 text-sm text-gray-700 mb-2">
                            <span class="font-mono text-rj-purple">?status=pending</span> — {{ __('Filter by status (pending, processing, completed, cancelled)') }}
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-6">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-0.5 rounded-md bg-blue-100 text-blue-700 text-xs font-bold uppercase">PUT</span>
                            <code class="text-sm font-mono text-gray-700">/orders/{id}/status</code>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">{{ __('Update the status of a specific order.') }}</p>
                        <div class="bg-gray-900 rounded-xl p-4 font-mono text-xs text-gray-300 overflow-x-auto">
                            <pre>{
  "status": "completed"
}</pre>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-6">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-0.5 rounded-md bg-blue-100 text-blue-700 text-xs font-bold uppercase">PUT</span>
                            <code class="text-sm font-mono text-gray-700">/orders/{id}/shipping</code>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">{{ __('Update shipping and tracking information for an order.') }}</p>
                        <div class="bg-gray-900 rounded-xl p-4 font-mono text-xs text-gray-300 overflow-x-auto">
                            <pre>{
  "tracking_number": "TRK123456789",
  "tracking_url": "https://track.carrier.com/TRK123456789",
  "shipping_carrier": "DHL Express"
}</pre>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 5. Resources -->
            <section id="resources" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 scroll-mt-24">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-pink-50 text-pink-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">{{ __('Resources & Guides') }}</h2>
                </div>
                <p class="text-sm text-gray-600 leading-relaxed mb-6">{{ __('Explore our official resources to help you integrate faster.') }}</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="https://www.youtube.com/playlist?list=PLUgbQDz1GR-A" target="_blank" rel="noopener" class="group flex items-start gap-4 p-5 rounded-2xl border border-gray-200 hover:border-rj-purple/50 hover:bg-rj-purple/5 transition">
                        <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-900 group-hover:text-rj-purple transition">{{ __('Video Tutorials') }}</h4>
                            <p class="text-xs text-gray-500 mt-1">{{ __('Step-by-step YouTube playlist for setup and usage.') }}</p>
                        </div>
                    </a>

                    <a href="https://ruhidjavadoff.blogspot.com/2026/10/rj-commerce-rj-nexus-plugins.html" target="_blank" rel="noopener" class="group flex items-start gap-4 p-5 rounded-2xl border border-gray-200 hover:border-rj-purple/50 hover:bg-rj-purple/5 transition">
                        <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v8a2 2 0 01-2 2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M14 4v6h6"/></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-900 group-hover:text-rj-purple transition">{{ __('Blog Article') }}</h4>
                            <p class="text-xs text-gray-500 mt-1">{{ __('Detailed written guide and release notes.') }}</p>
                        </div>
                    </a>

                    <a href="https://github.com/rj-nexus/rj-commerce" target="_blank" rel="noopener" class="group flex items-start gap-4 p-5 rounded-2xl border border-gray-200 hover:border-rj-purple/50 hover:bg-rj-purple/5 transition md:col-span-2">
                        <div class="w-12 h-12 rounded-xl bg-gray-900 text-white flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 .5C5.65.5.5 5.65.5 12c0 5.08 3.29 9.39 7.86 10.91.58.1.79-.25.79-.56v-2c-3.2.7-3.87-1.54-3.87-1.54-.52-1.33-1.28-1.69-1.28-1.69-1.04-.71.08-.7.08-.7 1.15.08 1.76 1.18 1.76 1.18 1.03 1.76 2.69 1.25 3.35.96.1-.74.4-1.25.73-1.54-2.55-.29-5.24-1.28-5.24-5.69 0-1.26.45-2.28 1.18-3.09-.12-.29-.51-1.46.11-3.05 0 0 .97-.31 3.17 1.18a11 11 0 0 1 5.77 0c2.2-1.49 3.17-1.18 3.17-1.18.62 1.59.23 2.76.11 3.05.74.81 1.18 1.83 1.18 3.09 0 4.42-2.69 5.39-5.25 5.68.41.36.78 1.06.78 2.14v3.17c0 .31.21.67.8.56A11.51 11.51 0 0 0 23.5 12C23.5 5.65 18.35.5 12 .5z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-900 group-hover:text-rj-purple transition">{{ __('GitHub Repository') }}</h4>
                            <p class="text-xs text-gray-500 mt-1">{{ __('View source code, report issues, and contribute.') }}</p>
                        </div>
                    </a>
                </div>
            </section>

        </div>
    </div>
</div>
@endsection
