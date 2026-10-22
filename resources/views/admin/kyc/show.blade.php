@extends('admin.layouts.app')

@section('title', __('Review KYC Verification'))

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('admin.kyc.index') }}" class="p-2 rounded-lg hover:bg-gray-100 transition text-gray-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Review Verification') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('User ID: #') }}{{ $verification->user_id }} &middot; {{ $verification->created_at->format('M d, Y H:i') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-900">{{ __('Uploaded Documents') }}</h2>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-gray-200 text-gray-700 text-xs font-medium capitalize">
                        {{ str_replace('_', ' ', $verification->document_type) }}
                    </span>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">{{ __('Document Front') }}</h3>
                        @if($verification->document_front)
                            <a href="{{ Storage::url($verification->document_front) }}" target="_blank" class="block rounded-lg overflow-hidden border border-gray-200 hover:border-rj-purple transition">
                                <img src="{{ Storage::url($verification->document_front) }}" alt="Front" class="w-full h-48 object-cover">
                            </a>
                        @else
                            <div class="w-full h-48 bg-gray-50 rounded-lg border border-gray-200 flex items-center justify-center text-sm text-gray-400">{{ __('Not uploaded') }}</div>
                        @endif
                    </div>

                    <div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">{{ __('Document Back') }}</h3>
                        @if($verification->document_back)
                            <a href="{{ Storage::url($verification->document_back) }}" target="_blank" class="block rounded-lg overflow-hidden border border-gray-200 hover:border-rj-purple transition">
                                <img src="{{ Storage::url($verification->document_back) }}" alt="Back" class="w-full h-48 object-cover">
                            </a>
                        @else
                            <div class="w-full h-48 bg-gray-50 rounded-lg border border-gray-200 flex items-center justify-center text-sm text-gray-400">{{ __('Not uploaded') }}</div>
                        @endif
                    </div>

                    <div class="md:col-span-2">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">{{ __('Selfie with Document') }}</h3>
                        @if($verification->selfie)
                            <a href="{{ Storage::url($verification->selfie) }}" target="_blank" class="block rounded-lg overflow-hidden border border-gray-200 hover:border-rj-purple transition w-full md:w-1/2">
                                <img src="{{ Storage::url($verification->selfie) }}" alt="Selfie" class="w-full h-64 object-cover">
                            </a>
                        @else
                            <div class="w-full h-48 bg-gray-50 rounded-lg border border-gray-200 flex items-center justify-center text-sm text-gray-400">{{ __('Not uploaded') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-sm font-semibold text-gray-900 mb-4">{{ __('User Information') }}</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center text-white font-semibold">
                            {{ strtoupper(substr($verification->user->name ?? '?', 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">{{ $verification->user->name ?? __('Unknown') }}</div>
                            <div class="text-xs text-gray-500">{{ $verification->user->email ?? '' }}</div>
                        </div>
                    </div>
                    <div class="pt-3 border-t border-gray-100 flex justify-between">
                        <span class="text-gray-500">{{ __('Current Status') }}</span>
                        <span class="font-medium text-yellow-600 capitalize">{{ $verification->status }}</span>
                    </div>
                </div>
            </div>

            @if($verification->status === 'pending')
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-sm font-semibold text-gray-900 mb-4">{{ __('Take Action') }}</h2>

                    <form action="{{ route('admin.kyc.approve', $verification) }}" method="POST" class="mb-6">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-green-600 text-white text-sm font-medium hover:bg-green-700 transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            {{ __('Approve Verification') }}
                        </button>
                    </form>

                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-100"></div>
                        </div>
                        <div class="relative flex justify-center text-xs mb-3">
                            <span class="bg-white px-2 text-gray-400 uppercase tracking-wider">{{ __('Or Reject') }}</span>
                        </div>
                    </div>

                    <form action="{{ route('admin.kyc.reject', $verification) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">{{ __('Rejection Reason') }}</label>
                            <textarea name="rejection_reason" rows="3" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm" required placeholder="{{ __('Explain why this was rejected...') }}"></textarea>
                            @error('rejection_reason') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                        </div>
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-red-50 text-red-600 border border-red-200 text-sm font-medium hover:bg-red-100 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            {{ __('Reject Verification') }}
                        </button>
                    </form>
                </div>
            @else
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-6 text-center">
                    <p class="text-sm text-gray-600">{{ __('This verification has already been processed.') }}</p>
                    @if($verification->rejection_reason)
                        <div class="mt-3 text-left bg-white p-3 rounded-lg border border-red-100 text-sm text-red-700">
                            <span class="font-semibold block mb-1 text-xs uppercase tracking-wider text-red-500">{{ __('Reason') }}:</span>
                            {{ $verification->rejection_reason }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
