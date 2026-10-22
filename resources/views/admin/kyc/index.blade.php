@extends('admin.layouts.app')

@section('title', __('KYC Management'))

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('KYC Verifications') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('Review and manage identity verification requests from users.') }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">{{ __('User') }}</th>
                        <th class="px-6 py-4">{{ __('Document Type') }}</th>
                        <th class="px-6 py-4">{{ __('Submitted At') }}</th>
                        <th class="px-6 py-4 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($verifications as $verification)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center text-white text-sm font-semibold flex-shrink-0">
                                        {{ strtoupper(substr($verification->user->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $verification->user->name ?? __('Unknown User') }}</div>
                                        <div class="text-xs text-gray-500">{{ $verification->user->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-gray-100 text-gray-700 text-xs font-medium capitalize">
                                    {{ str_replace('_', ' ', $verification->document_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ $verification->created_at->format('M d, Y H:i') }}
                                <div class="text-xs text-gray-400 mt-0.5">{{ $verification->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.kyc.show', $verification) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rj-purple/10 text-rj-purple hover:bg-rj-purple/20 transition text-xs font-semibold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    {{ __('Review') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-base font-medium text-gray-900">{{ __('No pending verifications') }}</p>
                                <p class="text-sm mt-1">{{ __('All KYC requests have been processed.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($verifications->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $verifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
