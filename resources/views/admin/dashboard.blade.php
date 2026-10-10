@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded shadow p-4">
            <div class="text-sm text-gray-500">Users</div>
            <div class="text-2xl font-semibold">{{ \App\Models\User::count() }}</div>
        </div>
        <div class="bg-white rounded shadow p-4">
            <div class="text-sm text-gray-500">Posts</div>
            <div class="text-2xl font-semibold">0</div>
        </div>
        <div class="bg-white rounded shadow p-4">
            <div class="text-sm text-gray-500">Pages</div>
            <div class="text-2xl font-semibold">0</div>
        </div>
        <div class="bg-white rounded shadow p-4">
            <div class="text-sm text-gray-500">Themes</div>
            <div class="text-2xl font-semibold">0</div>
        </div>
    </div>
@endsection
