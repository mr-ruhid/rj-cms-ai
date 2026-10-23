@extends('admin.layouts.app')

@section('title', __('Coupons'))

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Coupons') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('Create and manage discount codes for your customers.') }}</p>
        </div>
        <a href="{{ route('admin.commerce.coupons.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            {{ __('Add Coupon') }}
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">{{ __('Code') }}</th>
                        <th class="px-6 py-4">{{ __('Type') }}</th>
                        <th class="px-6 py-4">{{ __('Amount') }}</th>
                        <th class="px-6 py-4">{{ __('Usage') }}</th>
                        <th class="px-6 py-4">{{ __('Expiry') }}</th>
                        <th class="px-6 py-4">{{ __('Status') }}</th>
                        <th class="px-6 py-4 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($coupons as $coupon)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="inline-flex items-center gap-2 px-3 py-1 bg-gray-100 border border-gray-200 rounded-lg font-mono font-bold text-gray-900">
                                    {{ $coupon->code }}
                                </div>
                                @if($coupon->description)
                                    <div class="text-xs text-gray-500 mt-1">{{ Str::limit($coupon->description, 40) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($coupon->type === 'percentage')
                                    <span class="text-sm text-gray-700">{{ __('Percentage') }}</span>
                                @else
                                    <span class="text-sm text-gray-700">{{ __('Fixed Cart') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                @if($coupon->type === 'percentage')
                                    {{ $coupon->amount }}%
                                @else
                                    ${{ number_format($coupon->amount, 2) }}
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $coupon->used_count }} / {{ $coupon->usage_limit ?? '∞' }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                @if($coupon->expires_at)
                                    {{ $coupon->expires_at->format('M d, Y') }}
                                    @if($coupon->expires_at->isPast())
                                        <span class="inline-block ml-1 text-red-500 text-xs font-bold uppercase">{{ __('Expired') }}</span>
                                    @endif
                                @else
                                    {{ __('Never') }}
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($coupon->is_active && $coupon->isValid())
                                    <span class="inline-flex items-center px-2 py-1 rounded-md bg-green-50 text-green-700 text-xs font-medium border border-green-200">
                                        {{ __('Active') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-gray-600 text-xs font-medium border border-gray-200">
                                        {{ __('Inactive') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.commerce.coupons.edit', $coupon) }}" class="p-2 rounded-lg text-gray-400 hover:text-rj-purple hover:bg-rj-purple/10 transition" title="{{ __('Edit') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.commerce.coupons.destroy', $coupon) }}" onsubmit="return confirm('{{ __('Are you sure you want to delete this coupon?') }}');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="{{ __('Delete') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="w-16 h-16 mx-auto bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                                </div>
                                <h3 class="text-base font-semibold text-gray-900">{{ __('No coupons yet') }}</h3>
                                <p class="text-sm text-gray-500 mt-1 max-w-sm mx-auto">{{ __('Create your first discount code to boost sales and attract customers.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($coupons->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $coupons->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
