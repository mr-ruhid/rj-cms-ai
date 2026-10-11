@extends('admin.layouts.app')

@section('title', 'Create User')

@section('content')
    <form method="POST" action="{{ route('admin.users.store') }}" class="bg-white rounded shadow p-6 max-w-2xl space-y-4">
        @csrf

        <div>
            <label class="block text-sm mb-1">Name</label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2">
            @error('name') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2">
            @error('email') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Password</label>
            <input type="password" name="password" class="w-full border rounded px-3 py-2">
            @error('password') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Role</label>
            <select name="role" class="w-full border rounded px-3 py-2">
                <option value="user" @selected(old('role') === 'user')>User</option>
                <option value="admin" @selected(old('role') === 'admin')>Admin</option>
            </select>
            @error('role') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Status</label>
            <select name="status" class="w-full border rounded px-3 py-2">
                <option value="active" @selected(old('status') === 'active')>Active</option>
                <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
            </select>
            @error('status') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded">Save</button>
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded border">Cancel</a>
        </div>
    </form>
@endsection
