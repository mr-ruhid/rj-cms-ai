@extends('theme::layout')

@section('title', ($product->translation()?->name ?? $product->name) . ' — ' . rj_get_option('site_name', config('app.name')))

@push('styles')
<script src="https://cdn.tailwindcss.com"></script>
@endpush

@section('content')
<div class="bg-slate-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-8 flex items-center justify-between">
            <a href="{{ route('commerce.shop.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                {{ __('Back to Shop') }}
            </a>
            <a href="{{ route('commerce.cart.index') }}" class="relative inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 text-white rounded-xl text-sm font-semibold hover:bg-slate-800 transition shadow-lg shadow-slate-900/20 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                {{ __('Cart') }} ({{ array_sum(array_column(session()->get('rj_cart', []), 'quantity')) }})
            </a>
        </div>

        @php
            $imgUrl = null;
            if ($product->featured_image) {
                if (Str::startsWith($product->featured_image, 'http')) {
                    $imgUrl = $product->featured_image;
                } elseif (Str::startsWith($product->featured_image, 'media/') || Str::startsWith($product->featured_image, '/media/')) {
                    $imgUrl = asset(ltrim($product->featured_image, '/'));
                } else {
                    $imgUrl = Storage::url($product->featured_image);
                }
            }
        @endphp

        <div class="lg:grid lg:grid-cols-2 lg:gap-x-12 xl:gap-x-16 items-start">
            <div>
                <div class="aspect-square rounded-3xl bg-white overflow-hidden shadow-xl shadow-slate-200/60 ring-1 ring-slate-900/5">
                    @if($imgUrl)
                        <img src="{{ $imgUrl }}" alt="{{ $product->translation()?->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                            <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-10 px-4 sm:px-0 lg:mt-0">
                <div class="text-xs font-bold tracking-wider text-slate-500 uppercase mb-2">{{ $product->category?->translation()?->name ?? __('Uncategorized') }}</div>
                <h1 class="text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight mb-4">{{ $product->translation()?->name ?? $product->name }}</h1>

                <div class="flex items-end gap-3 mb-8">
                    <span class="text-4xl font-bold text-slate-900">${{ number_format($product->price, 2) }}</span>
                    @if($product->compare_price)
                        <span class="text-xl text-slate-400 line-through mb-1">${{ number_format($product->compare_price, 2) }}</span>
                    @endif
                </div>

                <div class="prose prose-slate prose-sm text-slate-600 mb-8 max-w-none">
                    {!! $product->translation()?->description ?? $product->translation()?->short_description ?? __('No description available.') !!}
                </div>

                <form action="{{ route('commerce.cart.add') }}" method="POST" id="add-to-cart-form">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    @if($product->variations->isNotEmpty())
                        <div class="mb-8">
                            <h3 class="text-sm font-semibold text-slate-900 mb-3">{{ __('Available Options') }}</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($product->variations as $index => $variation)
                                    <label class="relative flex cursor-pointer rounded-2xl border bg-white p-4 shadow-sm hover:border-slate-400 transition-all has-[:checked]:border-slate-900 has-[:checked]:ring-2 has-[:checked]:ring-slate-900 has-[:checked]:bg-slate-50">
                                        <input type="radio" name="variation_id" value="{{ $variation->id }}" class="sr-only" required {{ $loop->first ? 'checked' : '' }}>
                                        <span class="flex flex-1 items-center justify-between">
                                            <span class="flex flex-col">
                                                <span class="block text-sm font-semibold text-slate-900">{{ $variation->sku ?: __('Option') . ' ' . ($index + 1) }}</span>
                                                @if($variation->price && $variation->price != $product->price)
                                                    <span class="mt-1 text-xs font-medium text-emerald-600">+${{ number_format($variation->price - $product->price, 2) }}</span>
                                                @endif
                                            </span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="flex flex-col sm:flex-row gap-4 mb-8">
                        <div class="flex items-center rounded-xl border border-slate-300 bg-white shadow-sm">
                            <button type="button" onclick="let i = document.getElementById('qty'); i.stepDown();" class="px-4 py-4 text-slate-500 hover:text-slate-900 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                            </button>
                            <input type="number" id="qty" name="quantity" value="1" min="1" class="w-16 text-center border-0 focus:ring-0 text-slate-900 font-semibold text-lg bg-transparent">
                            <button type="button" onclick="let i = document.getElementById('qty'); i.stepUp();" class="px-4 py-4 text-slate-500 hover:text-slate-900 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </button>
                        </div>

                        <button type="submit" id="add-btn" class="flex-1 flex items-center justify-center gap-3 bg-slate-900 text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-slate-800 transition-all duration-200 shadow-lg shadow-slate-900/20 active:scale-[0.98] disabled:opacity-70">
                            <svg id="btn-spin" class="hidden w-5 h-5 animate-spin text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                            <svg id="btn-cart" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span id="btn-text">{{ __('Add to Cart') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('add-to-cart-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const btn = document.getElementById('add-btn');
        const spinner = document.getElementById('btn-spin');
        const icon = document.getElementById('btn-cart');
        const text = document.getElementById('btn-text');
        const form = this;

        btn.disabled = true;
        spinner.classList.remove('hidden');
        icon.classList.add('hidden');
        text.textContent = '{{ __("Adding...") }}';

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new FormData(form)
        })
        .then(res => {
            if (res.redirected) {
                window.location.href = res.url;
                return;
            }
            return res.json().then(data => ({ status: res.status, data }));
        })
        .then(result => {
            if (!result) return;
            if (result.status === 422) {
                const errors = Object.values(result.data.errors || {}).flat().join('\n');
                alert(errors || '{{ __("Please check the form.") }}');
                resetBtn();
            } else if (result.data.success) {
                window.location.href = '{{ route('commerce.cart.index') }}';
            } else {
                alert(result.data.message || '{{ __("Error") }}');
                resetBtn();
            }
        })
        .catch(err => {
            console.error(err);
            alert('{{ __("Network error. Please try again.") }}');
            resetBtn();
        });

        function resetBtn() {
            btn.disabled = false;
            spinner.classList.add('hidden');
            icon.classList.remove('hidden');
            text.textContent = '{{ __("Add to Cart") }}';
        }
    });
</script>
@endsection
