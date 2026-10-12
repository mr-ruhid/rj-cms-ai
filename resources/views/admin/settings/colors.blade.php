@extends('admin.settings.layout')

@section('settings')
<form method="POST" action="{{ route('admin.settings.colors.update') }}" class="space-y-6" x-data="{ selected: '{{ $data['activePreset'] }}' }">
    @csrf
    @method('PUT')

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Admin Color Preset') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Choose a color palette for the admin panel. Changes apply instantly.') }}</p>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                @foreach ($data['presets'] as $key => $preset)
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="admin_color_preset" value="{{ $key }}"
                               x-model="selected"
                               {{ $data['activePreset'] === $key ? 'checked' : '' }}
                               class="sr-only">

                        <div class="rounded-xl border-2 transition overflow-hidden"
                             :class="selected === '{{ $key }}' ? 'border-rj-purple shadow-md' : 'border-gray-200 group-hover:border-gray-300'">

                            <div class="h-24 bg-gradient-to-br relative"
                                 style="background: linear-gradient(135deg, rgb({{ $preset['dark-1'] }}) 0%, rgb({{ $preset['dark-2'] }}) 50%, rgb({{ $preset['dark-3'] }}) 100%);">

                                <div class="absolute top-3 left-3 flex items-center gap-1.5">
                                    <span class="w-5 h-5 rounded-full shadow-sm border border-white/20"
                                          style="background: rgb({{ $preset['purple'] }});"></span>
                                    <span class="w-5 h-5 rounded-full shadow-sm border border-white/20"
                                          style="background: rgb({{ $preset['purple-light'] }});"></span>
                                    <span class="w-5 h-5 rounded-full shadow-sm border border-white/20"
                                          style="background: rgb({{ $preset['pink'] }});"></span>
                                </div>

                                <div class="absolute bottom-3 right-3 flex items-end gap-0.5 h-8">
                                    <span class="w-1.5 rounded-sm bg-white/30" style="height: 40%;"></span>
                                    <span class="w-1.5 rounded-sm bg-white/50" style="height: 65%;"></span>
                                    <span class="w-1.5 rounded-sm bg-white/70" style="height: 85%;"></span>
                                    <span class="w-1.5 rounded-sm bg-white/40" style="height: 55%;"></span>
                                </div>

                                <div x-show="selected === '{{ $key }}'" x-cloak class="absolute top-3 right-3">
                                    <div class="w-6 h-6 rounded-full bg-white flex items-center justify-center shadow-sm">
                                        <svg class="w-4 h-4 text-rj-purple" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                </div>
                            </div>

                            <div class="px-4 py-3 bg-white">
                                <div class="text-sm font-medium text-gray-900">{{ __($preset['label']) }}</div>
                                <div class="flex items-center gap-1 mt-2">
                                    <span class="w-3 h-3 rounded-full" style="background: rgb({{ $preset['purple'] }});"></span>
                                    <span class="w-3 h-3 rounded-full" style="background: rgb({{ $preset['purple-light'] }});"></span>
                                    <span class="w-3 h-3 rounded-full" style="background: rgb({{ $preset['pink'] }});"></span>
                                    <span class="w-3 h-3 rounded-full" style="background: rgb({{ $preset['dark-2'] }});"></span>
                                </div>
                            </div>
                        </div>
                    </label>
                @endforeach

            </div>
        </div>
    </div>

    <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div class="text-sm text-blue-800">
            <p class="font-medium">{{ __('These colors apply to the admin panel only.') }}</p>
            <p class="mt-1 text-blue-700">
                {{ __('Frontend (website) colors are controlled by your active theme.') }}
                {{ __('To customize the frontend appearance, go to') }}
                <a href="{{ route('admin.appearance.customize') }}" class="font-medium underline hover:text-blue-900">{{ __('Appearance → Customize') }}</a>.
            </p>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
            {{ __('Save Changes') }}
        </button>
    </div>

</form>
@endsection
