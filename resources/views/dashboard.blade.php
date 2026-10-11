@extends('admin.layouts.app')

@section('title', __('Dashboard'))

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded shadow p-4">
            <div class="text-sm text-gray-500">{{ __('Users') }}</div>
            <div class="text-2xl font-semibold">{{ \App\Models\User::count() }}</div>
        </div>
        <div class="bg-white rounded shadow p-4">
            <div class="text-sm text-gray-500">{{ __('Posts') }}</div>
            <div class="text-2xl font-semibold">0</div>
        </div>
        <div class="bg-white rounded shadow p-4">
            <div class="text-sm text-gray-500">{{ __('Pages') }}</div>
            <div class="text-2xl font-semibold">0</div>
        </div>
        <div class="bg-white rounded shadow p-4">
            <div class="text-sm text-gray-500">{{ __('Themes') }}</div>
            <div class="text-2xl font-semibold">0</div>
        </div>
    </div>
@endsection
