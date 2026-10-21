@extends('admin.layouts.app')

@section('title', __('Support Center'))

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <div>
        <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
            <svg class="w-6 h-6 text-rj-purple" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            {{ __('Support Center') }}
        </h2>
        <p class="text-sm text-gray-500 mt-1">{{ __('Need help? Submit a ticket below or explore our resources.') }}</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded-r-lg shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-r-lg shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-base font-bold text-gray-900">{{ __('Submit a Ticket') }}</h3>
                <p class="text-xs text-gray-500 mt-1">{{ __('Describe your issue and we will get back to you via email.') }}</p>
            </div>
            
            <form method="POST" action="{{ route('admin.support.submit') }}" class="p-6 space-y-4" x-data="{ submitting: false }" @submit="submitting = true">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Your Name') }}</label>
                        <input type="text" name="name" value="{{ auth()->user()->name }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Your Email') }}</label>
                        <input type="email" name="email" value="{{ auth()->user()->email }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Subject') }}</label>
                    <input type="text" name="subject" required placeholder="{{ __('Brief summary of the issue') }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Message') }}</label>
                    <textarea name="message" rows="6" required placeholder="{{ __('Please provide as much detail as possible...') }}" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm"></textarea>
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit" :disabled="submitting" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-bold hover:opacity-90 transition shadow-sm disabled:opacity-50">
                        <svg x-show="!submitting" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        <svg x-show="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                        <span x-text="submitting ? '{{ __('Sending...') }}' : '{{ __('Submit Ticket') }}'"></span>
                    </button>
                </div>
            </form>
        </div>

        <div class="space-y-4">
            <a href="{{ route('admin.support.chat') }}" class="group block bg-white rounded-2xl border border-gray-200 shadow-sm p-5 hover:border-rj-purple/40 hover:shadow-md transition">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-900 group-hover:text-rj-purple transition">{{ __('Book a Meeting') }}</h4>
                        <p class="text-xs text-gray-500 mt-0.5">{{ __('Schedule a Google Meet with our team.') }}</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.support.faq') }}" class="group block bg-white rounded-2xl border border-gray-200 shadow-sm p-5 hover:border-rj-purple/40 hover:shadow-md transition">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-900 group-hover:text-rj-purple transition">{{ __('FAQ') }}</h4>
                        <p class="text-xs text-gray-500 mt-0.5">{{ __('Frequently asked questions.') }}</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.services.index') }}" class="group block bg-white rounded-2xl border border-gray-200 shadow-sm p-5 hover:border-rj-purple/40 hover:shadow-md transition">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-900 group-hover:text-rj-purple transition">{{ __('RJ Services') }}</h4>
                        <p class="text-xs text-gray-500 mt-0.5">{{ __('Order custom services.') }}</p>
                    </div>
                </div>
            </a>

            <div class="bg-gray-900 rounded-2xl shadow-sm p-5 text-white relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-white/10 rounded-full blur-xl"></div>
                <div class="flex items-center gap-4 relative z-10">
                    <div class="w-12 h-12 rounded-xl bg-white/20 text-white flex items-center justify-center flex-shrink-0 backdrop-blur-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold">{{ __('Direct Contact') }}</h4>
                        <p class="text-xs text-gray-300 mt-0.5">+994 50 663 60 31</p>
                        <p class="text-xs text-gray-300">rjnexus@hotmail.com</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection