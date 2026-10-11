@extends('admin.layouts.app')

@section('title', __('General Settings'))

@section('content')
    <form method="POST" action="{{ route('admin.settings.general.update') }}" class="bg-white rounded shadow p-6 max-w-3xl space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm mb-1">{{ __('Site Name') }}</label>
            <input type="text" name="site_name" value="{{ old('site_name', $options['site_name'] ?? '') }}" class="w-full border rounded px-3 py-2">
            @error('site_name') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm mb-1">{{ __('Site Description') }}</label>
            <textarea name="site_description" rows="3" class="w-full border rounded px-3 py-2">{{ old('site_description', $options['site_description'] ?? '') }}</textarea>
            @error('site_description') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm mb-1">{{ __('Site URL') }}</label>
            <input type="text" name="site_url" value="{{ old('site_url', $options['site_url'] ?? '') }}" class="w-full border rounded px-3 py-2">
            @error('site_url') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm mb-1">{{ __('Admin Email') }}</label>
            <input type="email" name="admin_email" value="{{ old('admin_email', $options['admin_email'] ?? '') }}" class="w-full border rounded px-3 py-2">
            @error('admin_email') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1">{{ __('Timezone') }}</label>
                <input type="text" name="timezone" value="{{ old('timezone', $options['timezone'] ?? '') }}" class="w-full border rounded px-3 py-2">
                @error('timezone') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block text-sm mb-1">{{ __('Language') }}</label>
                <input type="text" name="language" value="{{ old('language', $options['language'] ?? '') }}" class="w-full border rounded px-3 py-2">
                @error('language') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block text-sm mb-1">{{ __('Date Format') }}</label>
                <input type="text" name="date_format" value="{{ old('date_format', $options['date_format'] ?? '') }}" class="w-full border rounded px-3 py-2">
                @error('date_format') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block text-sm mb-1">{{ __('Time Format') }}</label>
                <input type="text" name="time_format" value="{{ old('time_format', $options['time_format'] ?? '') }}" class="w-full border rounded px-3 py-2">
                @error('time_format') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block text-sm mb-1">{{ __('Currency') }}</label>
                <input type="text" name="currency" value="{{ old('currency', $options['currency'] ?? '') }}" class="w-full border rounded px-3 py-2">
                @error('currency') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>
        </div>

        <div>
            <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded">{{ __('Save') }}</button>
        </div>
    </form>
@endsection
