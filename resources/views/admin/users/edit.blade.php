@extends('admin.layouts.app')

@section('title', __('Edit User'))

@section('content')
    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="bg-white rounded shadow p-6 max-w-2xl space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm mb-1">{{ __('Name') }}</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border rounded px-3 py-2">
            @error('name') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm mb-1">{{ __('Email') }}</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border rounded px-3 py-2">
            @error('email') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm mb-1">{{ __('Password') }}</label>
            <input type="password" name="password" class="w-full border rounded px-3 py-2">
            <div class="text-xs text-gray-500 mt-1">{{ __('Leave empty to keep current password') }}</div>
            @error('password') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm mb-1">{{ __('Role') }}</label>
            <select name="role" class="w-full border rounded px-3 py-2">
                <option value="user" @selected(old('role', $user->role) === 'user')>{{ __('User') }}</option>
                <option value="admin" @selected(old('role', $user->role) === 'admin')>{{ __('Admin') }}</option>
            </select>
            @error('role') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm mb-1">{{ __('Status') }}</label>
            <select name="status" class="w-full border rounded px-3 py-2">
                <option value="active" @selected(old('status', $user->status) === 'active')>{{ __('Active') }}</option>
                <option value="inactive" @selected(old('status', $user->status) === 'inactive')>{{ __('Inactive') }}</option>
            </select>
            @error('status') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded">{{ __('Save') }}</button>
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded border">{{ __('Cancel') }}</a>
        </div>
    </form>
@endsection
