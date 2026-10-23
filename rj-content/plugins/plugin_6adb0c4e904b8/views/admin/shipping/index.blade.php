@extends('admin.layouts.app')

@section('title', __('Shipping Zones'))

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="{
    showZoneModal: false,
    showMethodModal: false,
    currentZone: null,
    zoneName: '',
    zoneRegions: '',
    methodName: '',
    methodType: 'flat_rate',
    methodCost: 0,
    methodMinAmount: ''
}">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Shipping Zones') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('Define where you ship and how much it costs.') }}</p>
        </div>
        <button @click="showZoneModal = true; zoneName = ''; zoneRegions = ''" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            {{ __('Add Zone') }}
        </button>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="space-y-6">
        @forelse($zones as $zone)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">{{ $zone->name }}</h2>
                        @if(!empty($zone->regions))
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach($zone->regions as $region)
                                    <span class="text-xs bg-white border border-gray-200 text-gray-600 px-2 py-0.5 rounded">{{ $region }}</span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-gray-500">{{ __('All regions') }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="showMethodModal = true; currentZone = {{ $zone->id }}; methodName = ''; methodType = 'flat_rate'; methodCost = 0; methodMinAmount = ''" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rj-purple/10 text-rj-purple text-xs font-semibold hover:bg-rj-purple/20 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            {{ __('Add Method') }}
                        </button>
                        <form method="POST" action="{{ route('admin.commerce.shipping.zones.destroy', $zone) }}" onsubmit="return confirm('{{ __('Delete this zone and all its methods?') }}');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="{{ __('Delete') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse($zone->methods as $method)
                        <div class="px-6 py-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg {{ $method->type === 'free_shipping' ? 'bg-green-50 text-green-600' : 'bg-blue-50 text-blue-600' }} flex items-center justify-center">
                                    @if($method->type === 'free_shipping')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $method->name }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        @if($method->type === 'free_shipping')
                                            {{ __('Free Shipping') }}
                                        @else
                                            {{ __('Flat Rate') }} — ${{ number_format($method->cost, 2) }}
                                        @endif
                                        @if($method->min_amount)
                                            · {{ __('Min:') }} ${{ number_format($method->min_amount, 2) }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('admin.commerce.shipping.methods.destroy', $method) }}" onsubmit="return confirm('{{ __('Delete this shipping method?') }}');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="{{ __('Delete') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="px-6 py-6 text-center text-sm text-gray-500 italic">{{ __('No shipping methods yet. Add one above.') }}</div>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-16 h-16 mx-auto bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900">{{ __('No shipping zones defined') }}</h3>
                <p class="text-sm text-gray-500 mt-1 max-w-sm mx-auto">{{ __('Create your first shipping zone to start calculating shipping costs.') }}</p>
            </div>
        @endforelse
    </div>

    <!-- Add Zone Modal -->
    <div x-show="showZoneModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div x-show="showZoneModal" x-transition.opacity class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showZoneModal = false"></div>
        <div x-show="showZoneModal" x-transition class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 space-y-5">
            <h3 class="text-lg font-bold text-gray-900">{{ __('Add Shipping Zone') }}</h3>
            <form method="POST" action="{{ route('admin.commerce.shipping.zones.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Zone Name') }}</label>
                    <input type="text" name="name" x-model="zoneName" required class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm" placeholder="e.g. Baku, Azerbaijan">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Regions (comma separated)') }}</label>
                    <input type="text" x-model="zoneRegions" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm" placeholder="Baku, Sumgait, Ganja">
                    <input type="hidden" name="regions" :value="JSON.stringify(zoneRegions.split(',').map(s => s.trim()).filter(s => s))">
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showZoneModal = false" class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100 text-sm font-medium">{{ __('Cancel') }}</button>
                    <button type="submit" class="px-5 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-bold hover:opacity-90">{{ __('Create Zone') }}</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Method Modal -->
    <div x-show="showMethodModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div x-show="showMethodModal" x-transition.opacity class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showMethodModal = false"></div>
        <div x-show="showMethodModal" x-transition class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 space-y-5">
            <h3 class="text-lg font-bold text-gray-900">{{ __('Add Shipping Method') }}</h3>
            <form :action="'{{ route('admin.commerce.shipping.zones.index') }}/' + currentZone + '/methods'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Method Name') }}</label>
                    <input type="text" name="name" x-model="methodName" required class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm" placeholder="e.g. Standard Delivery">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Type') }}</label>
                    <select name="type" x-model="methodType" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        <option value="flat_rate">{{ __('Flat Rate') }}</option>
                        <option value="free_shipping">{{ __('Free Shipping') }}</option>
                    </select>
                </div>
                <div x-show="methodType === 'flat_rate'" class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Cost ($)') }}</label>
                        <input type="number" name="cost" x-model="methodCost" step="0.01" min="0" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Min Amount') }}</label>
                        <input type="number" name="min_amount" x-model="methodMinAmount" step="0.01" min="0" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm" placeholder="Optional">
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showMethodModal = false" class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100 text-sm font-medium">{{ __('Cancel') }}</button>
                    <button type="submit" class="px-5 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-bold hover:opacity-90">{{ __('Add Method') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
