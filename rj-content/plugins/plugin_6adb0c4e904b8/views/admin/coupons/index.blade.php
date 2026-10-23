@extends('admin.layouts.app')

@section('title', __('Coupons'))

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="couponManager()">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Coupons') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('Create and manage discount codes for your customers.') }}</p>
        </div>
        <button @click="openCreate()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            {{ __('Add Coupon') }}
        </button>
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
                                <span class="text-sm text-gray-700">{{ $coupon->type === 'percentage' ? __('Percentage') : __('Fixed Cart') }}</span>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $coupon->type === 'percentage' ? $coupon->amount . '%' : '$' . number_format($coupon->amount, 2) }}
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
                                    <button @click="openEdit({{ $coupon->toJson() }})" class="p-2 rounded-lg text-gray-400 hover:text-rj-purple hover:bg-rj-purple/10 transition" title="{{ __('Edit') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
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

    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
        <div x-show="showModal" x-transition.opacity class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showModal = false"></div>

        <div x-show="showModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] flex flex-col overflow-hidden">

            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50">
                <h3 class="text-lg font-bold text-gray-900" x-text="isEditing ? '{{ __('Edit Coupon') }}' : '{{ __('Add New Coupon') }}'"></h3>
                <button @click="showModal = false" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="isEditing ? '/admin/commerce/coupons/' + form.id : '{{ route('admin.commerce.coupons.store') }}'" method="POST" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <template x-if="isEditing">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="flex-1 overflow-y-auto p-6 space-y-6">

                    @if ($errors->any())
                        <div class="bg-red-50 border-s-4 border-red-500 text-red-800 px-4 py-3 rounded-e-lg">
                            <ul class="list-disc pl-5 text-sm space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Coupon Code') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="code" x-model="form.code" required class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono uppercase" placeholder="e.g. SUMMER2026">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Discount Type') }} <span class="text-red-500">*</span></label>
                            <select name="type" x-model="form.type" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                                <option value="percentage">{{ __('Percentage Discount') }}</option>
                                <option value="fixed_cart">{{ __('Fixed Cart Discount') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Amount') }} <span class="text-red-500">*</span></label>
                            <input type="number" name="amount" x-model="form.amount" step="0.01" min="0" required class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Usage Limit') }}</label>
                            <input type="number" name="usage_limit" x-model="form.usage_limit" min="1" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm" placeholder="{{ __('Unlimited') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Min Spend') }}</label>
                            <input type="number" name="minimum_amount" x-model="form.minimum_amount" step="0.01" min="0" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm" placeholder="{{ __('None') }}">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Start Date') }}</label>
                            <input type="date" name="starts_at" x-model="form.starts_at" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Expiry Date') }}</label>
                            <input type="date" name="expires_at" x-model="form.expires_at" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Description') }}</label>
                        <textarea name="description" x-model="form.description" rows="2" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm" placeholder="{{ __('Optional note...') }}"></textarea>
                    </div>

                    <label class="flex items-center gap-3 cursor-pointer p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" x-model="form.is_active" class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple w-5 h-5">
                        <div>
                            <span class="block text-sm font-bold text-gray-900">{{ __('Enable this coupon') }}</span>
                            <span class="block text-xs text-gray-500">{{ __('If unchecked, customers will not be able to use this code.') }}</span>
                        </div>
                    </label>

                </div>

                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-end gap-3">
                    <button type="button" @click="showModal = false" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-200 transition">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-bold hover:opacity-90 transition shadow-md">
                        <span x-text="isEditing ? '{{ __('Update Coupon') }}' : '{{ __('Create Coupon') }}'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function couponManager() {
    return {
        showModal: {{ $errors->any() ? 'true' : 'false' }},
        isEditing: false,
        form: {
            id: null,
            code: '',
            type: 'percentage',
            amount: '',
            description: '',
            usage_limit: '',
            minimum_amount: '',
            starts_at: '',
            expires_at: '',
            is_active: true
        },
        openCreate() {
            this.isEditing = false;
            this.resetForm();
            this.showModal = true;
        },
        openEdit(coupon) {
            this.isEditing = true;
            this.form = {
                id: coupon.id,
                code: coupon.code,
                type: coupon.type,
                amount: coupon.amount,
                description: coupon.description || '',
                usage_limit: coupon.usage_limit || '',
                minimum_amount: coupon.minimum_amount || '',
                starts_at: coupon.starts_at ? coupon.starts_at.substring(0, 10) : '',
                expires_at: coupon.expires_at ? coupon.expires_at.substring(0, 10) : '',
                is_active: !!coupon.is_active
            };
            this.showModal = true;
        },
        resetForm() {
            this.form = {
                id: null,
                code: '',
                type: 'percentage',
                amount: '',
                description: '',
                usage_limit: '',
                minimum_amount: '',
                starts_at: '',
                expires_at: '',
                is_active: true
            };
        }
    }
}
</script>
@endsection
