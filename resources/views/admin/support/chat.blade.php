@extends('admin.layouts.app')

@section('title', __('Book a Meeting'))

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.support.index') }}" class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-rj-purple hover:border-rj-purple/30 transition shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ __('Book a Meeting') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Schedule a 1-on-1 Google Meet session with our support team.') }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded-r-lg shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-base font-bold text-gray-900 mb-4">{{ __('How it works') }}</h3>
                
                <div class="space-y-6 relative before:absolute before:inset-0 before:ml-4 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-gray-200 before:to-transparent">
                    <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full border-2 border-white bg-rj-purple text-white shadow-sm shrink-0 z-10 font-bold text-xs">1</div>
                        <div class="ml-4">
                            <h4 class="text-sm font-bold text-gray-900">{{ __('Request a Time') }}</h4>
                            <p class="text-xs text-gray-500 mt-1">{{ __('Fill out the form with your preferred date and time.') }}</p>
                        </div>
                    </div>
                    <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full border-2 border-white bg-gray-200 text-gray-600 shadow-sm shrink-0 z-10 font-bold text-xs">2</div>
                        <div class="ml-4">
                            <h4 class="text-sm font-bold text-gray-900">{{ __('Confirmation') }}</h4>
                            <p class="text-xs text-gray-500 mt-1">{{ __('We will review your request and send a confirmation email.') }}</p>
                        </div>
                    </div>
                    <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full border-2 border-white bg-gray-200 text-gray-600 shadow-sm shrink-0 z-10 font-bold text-xs">3</div>
                        <div class="ml-4">
                            <h4 class="text-sm font-bold text-gray-900">{{ __('Join the Meet') }}</h4>
                            <p class="text-xs text-gray-500 mt-1">{{ __('You will receive a Google Meet link to join the session.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-2xl border border-gray-200 p-5">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="text-xs text-gray-600 leading-relaxed">
                        {{ __('Meetings are available in English, Azerbaijani, and Russian. We look forward to speaking with you!') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-base font-bold text-gray-900">{{ __('Meeting Request') }}</h3>
                <p class="text-xs text-gray-500 mt-1">{{ __('All fields are required.') }}</p>
            </div>

            <form method="POST" action="{{ route('admin.support.submit') }}" class="p-6 space-y-5" x-data="{ submitting: false }" @submit="submitting = true">
                @csrf
                <input type="hidden" name="subject" value="Meeting Request">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Your Name') }}</label>
                        <input type="text" name="name" value="{{ auth()->user()->name }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Your Email') }}</label>
                        <input type="email" name="email" value="{{ auth()->user()->email }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Preferred Date') }}</label>
                        <input type="date" name="meeting_date" required min="{{ date('Y-m-d') }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Preferred Time') }}</label>
                        <select name="meeting_time" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm bg-white">
                            <option value="">{{ __('— Select Time —') }}</option>
                            <option value="09:00">09:00 AM</option>
                            <option value="10:00">10:00 AM</option>
                            <option value="11:00">11:00 AM</option>
                            <option value="12:00">12:00 PM</option>
                            <option value="14:00">02:00 PM</option>
                            <option value="15:00">03:00 PM</option>
                            <option value="16:00">04:00 PM</option>
                            <option value="17:00">05:00 PM</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('What would you like to discuss?') }}</label>
                    <textarea name="message" rows="5" required placeholder="{{ __('Briefly describe the topic of the meeting...') }}" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm"></textarea>
                    <input type="hidden" name="meeting_request" value="1">
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit" :disabled="submitting" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-bold hover:opacity-90 transition shadow-sm disabled:opacity-50">
                        <svg x-show="!submitting" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <svg x-show="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                        <span x-text="submitting ? '{{ __('Sending...') }}' : '{{ __('Request Meeting') }}'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection