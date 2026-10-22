@extends('admin.layouts.app')

@section('title', __('Products'))

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Products') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('Manage your store products and inventory.') }}</p>
        </div>
        <a href="{{ route('admin.commerce.products.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            {{ __('Add Product') }}
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 w-16">{{ __('Image') }}</th>
                        <th class="px-6 py-4">{{ __('Product') }}</th>
                        <th class="px-6 py-4">{{ __('SKU') }}</th>
                        <th class="px-6 py-4">{{ __('Price') }}</th>
                        <th class="px-6 py-4">{{ __('Stock') }}</th>
                        <th class="px-6 py-4">{{ __('Status') }}</th>
                        <th class="px-6 py-4 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($products as $product)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4">
                                @if($product->featured_image)
                                    <img src="{{ Storage::url($product->featured_image) }}" alt="{{ $product->name }}" class="w-10 h-10 rounded-lg object-cover border border-gray-200">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 border border-gray-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $product->name }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ Str::limit($product->short_description, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-500 font-mono text-xs">{{ $product->sku ?: '—' }}</td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">${{ number_format($product->price, 2) }}</div>
                                @if($product->compare_price)
                                    <div class="text-xs text-gray-400 line-through">${{ number_format($product->compare_price, 2) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($product->manage_stock)
                                    @if($product->stock_quantity > 0)
                                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-green-50 text-green-700 text-xs font-medium border border-green-200">
                                            {{ __('In Stock') }} ({{ $product->stock_quantity }})
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-red-50 text-red-700 text-xs font-medium border border-red-200">
                                            {{ __('Out of Stock') }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-500">{{ __('Always Available') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($product->status === 'published')
                                    <span class="inline-flex items-center px-2 py-1 rounded-md bg-blue-50 text-blue-700 text-xs font-medium border border-blue-200">
                                        {{ __('Published') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-gray-600 text-xs font-medium border border-gray-200">
                                        {{ __('Draft') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.commerce.products.edit', $product) }}" class="p-2 rounded-lg text-gray-400 hover:text-rj-purple hover:bg-rj-purple/10 transition" title="{{ __('Edit') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.commerce.products.destroy', $product) }}" onsubmit="return confirm('{{ __('Are you sure you want to delete this product?') }}');" class="inline">
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
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                <p class="text-base font-medium text-gray-900">{{ __('No products yet') }}</p>
                                <p class="text-sm mt-1">{{ __('Get started by creating your first product.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
