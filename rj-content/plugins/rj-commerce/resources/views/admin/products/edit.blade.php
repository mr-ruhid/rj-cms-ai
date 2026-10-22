@extends('admin.layouts.app')

@section('title', __('Edit Product'))

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('admin.commerce.products.index') }}" class="p-2 rounded-lg hover:bg-gray-100 transition text-gray-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Edit Product') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('Update product information for') }}: <span class="font-semibold text-gray-900">{{ $product->name }}</span></p>
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border-s-4 border-red-500 text-red-800 px-4 py-3 rounded-e-lg">
            <ul class="list-disc pl-5 text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.commerce.products.update', $product) }}" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        @csrf
        @method('PUT')

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-3">{{ __('Basic Information') }}</h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Product Name') }}</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Short Description') }}</label>
                    <textarea name="short_description" rows="2" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">{{ old('short_description', $product->short_description) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Full Description') }}</label>
                    <textarea name="description" rows="6" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">{{ old('description', $product->description) }}</textarea>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-3">{{ __('Pricing & Inventory') }}</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Price') }} ($)</label>
                        <input type="number" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Compare Price') }} ($)</label>
                        <input type="number" name="compare_price" value="{{ old('compare_price', $product->compare_price) }}" step="0.01" min="0" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('SKU') }}</label>
                        <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Stock Quantity') }}</label>
                        <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    </div>
                </div>

                <div class="flex items-center gap-6 pt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="manage_stock" value="0">
                        <input type="checkbox" name="manage_stock" value="1" @checked(old('manage_stock', $product->manage_stock)) class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                        <span class="text-sm text-gray-700">{{ __('Manage Stock') }}</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-3">{{ __('Status') }}</h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Visibility') }}</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        <option value="draft" @selected(old('status', $product->status) === 'draft')>{{ __('Draft') }}</option>
                        <option value="published" @selected(old('status', $product->status) === 'published')>{{ __('Published') }}</option>
                    </select>
                </div>

                <label class="flex items-center gap-2 cursor-pointer mt-4">
                    <input type="hidden" name="is_featured" value="0">
                    <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured)) class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <span class="text-sm text-gray-700">{{ __('Featured Product') }}</span>
                </label>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-3">{{ __('Images') }}</h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Featured Image') }}</label>

                    @if($product->featured_image)
                        <div class="mb-3 relative inline-block">
                            <img src="{{ Storage::url($product->featured_image) }}" class="w-24 h-24 object-cover rounded-lg border border-gray-200">
                        </div>
                    @endif

                    <input type="file" name="featured_image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-rj-purple/10 file:text-rj-purple hover:file:bg-rj-purple/20">
                    <p class="text-xs text-gray-500 mt-1">{{ __('Upload new to replace the current image.') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Gallery Images') }}</label>

                    @if(!empty($product->gallery) && is_array($product->gallery))
                        <div class="flex flex-wrap gap-2 mb-3">
                            @foreach($product->gallery as $galleryImage)
                                <img src="{{ Storage::url($galleryImage) }}" class="w-16 h-16 object-cover rounded-lg border border-gray-200">
                            @endforeach
                        </div>
                    @endif

                    <input type="file" name="gallery[]" accept="image/*" multiple class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.commerce.products.index') }}" class="px-6 py-3 rounded-xl bg-gray-100 text-gray-700 font-bold text-sm hover:bg-gray-200 transition">
                    {{ __('Cancel') }}
                </a>
                <button type="submit" class="flex-1 inline-flex justify-center items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-rj-purple to-rj-pink text-white font-bold text-sm hover:opacity-90 transition shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    {{ __('Update Product') }}
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
