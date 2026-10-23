@extends('theme::layout')

@section('title', rj_get_option('site_name', config('app.name')))

@section('content')
<div class="bg-white min-h-[60vh] flex items-center justify-center py-24 px-4">
    <div class="text-center max-w-2xl mx-auto">
        <h1 class="text-4xl md:text-5xl font-serif text-slate-900 tracking-tight mb-6">
            {{ __('Welcome to') }} {{ rj_get_option('site_name', 'VelvetAura') }}
        </h1>
        <p class="text-lg text-slate-500 mb-8 leading-relaxed">
            {{ __('This is the default home page of the VelvetAura theme. You can replace this content by creating a new page and building it with RJBuilder, then setting it as your front page in the settings.') }}
        </p>
        <div class="flex items-center justify-center gap-4">
            @if(function_exists('rj_commerce_url'))
                <a href="{{ rj_commerce_url('shop') }}" class="inline-flex items-center justify-center px-8 py-4 border border-transparent text-sm font-bold tracking-widest uppercase text-white bg-slate-900 hover:bg-[#d4af37] transition-colors duration-300">
                    {{ __('Explore Shop') }}
                </a>
            @endif
            <a href="{{ url('/admin') }}" class="inline-flex items-center justify-center px-8 py-4 border border-slate-300 text-sm font-bold tracking-widest uppercase text-slate-700 hover:bg-slate-50 transition-colors duration-300">
                {{ __('Go to Admin') }}
            </a>
        </div>
    </div>
</div>
@endsection
