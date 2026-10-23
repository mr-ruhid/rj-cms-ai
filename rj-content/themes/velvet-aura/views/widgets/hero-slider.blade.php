@php
    $bgUrl = $settings['bg_image'] ?? '';
    if ($bgUrl && !\Illuminate\Support\Str::startsWith($bgUrl, 'http')) {
        if (\Illuminate\Support\Str::startsWith($bgUrl, 'media/') || \Illuminate\Support\Str::startsWith($bgUrl, '/media/')) {
            $bgUrl = asset(ltrim($bgUrl, '/'));
        } else {
            $bgUrl = \Illuminate\Support\Facades\Storage::url($bgUrl);
        }
    }
@endphp

<section class="relative flex items-center justify-center {{ $settings['height'] ?? 'min-h-[80vh]' }} bg-slate-900 overflow-hidden">
    @if($bgUrl)
        <img src="{{ $bgUrl }}" class="absolute inset-0 w-full h-full object-cover object-center opacity-90" alt="{{ $settings['title'] ?? '' }}">
    @endif

    <div class="absolute inset-0 bg-black/30"></div>

    <div class="relative z-10 text-center px-4 max-w-4xl mx-auto space-y-8">
        <h1 class="text-4xl md:text-6xl lg:text-7xl font-serif font-bold text-white tracking-tight leading-tight">
            {{ $settings['title'] ?? '' }}
        </h1>

        @if(!empty($settings['subtitle']))
            <p class="text-lg md:text-xl text-slate-200 font-light tracking-widest uppercase">
                {{ $settings['subtitle'] }}
            </p>
        @endif

        @if(!empty($settings['button_text']))
            <div class="pt-6">
                <a href="{{ $settings['button_url'] ?? '#' }}" class="inline-block border-b-2 border-white text-white text-xs md:text-sm font-bold tracking-[0.2em] uppercase pb-1 hover:text-[#d4af37] hover:border-[#d4af37] transition-colors duration-300">
                    {{ $settings['button_text'] }}
                </a>
            </div>
        @endif
    </div>
</section>
