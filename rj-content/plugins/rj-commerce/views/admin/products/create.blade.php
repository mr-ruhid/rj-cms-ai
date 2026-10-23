@extends('admin.layouts.app')

@section('title', __('Add Product'))

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="productForm()">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.commerce.products.index') }}" class="p-2 rounded-lg hover:bg-gray-100 transition text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('Add New Product') }}</h1>
                <p class="text-sm text-gray-500 mt-1">{{ __('Create a new physical, digital or external product.') }}</p>
            </div>
        </div>
        <div class="flex gap-3">
            <button type="submit" form="product-form" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-bold hover:opacity-90 transition shadow-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ __('Save Product') }}
            </button>
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

    <form id="product-form" method="POST" action="{{ route('admin.commerce.products.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        @csrf

        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="flex border-b border-gray-100 bg-gray-50 overflow-x-auto">
                    @foreach($locales as $locale)
                        <button type="button" @click="activeTab = '{{ $locale }}'" :class="activeTab === '{{ $locale }}' ? 'border-rj-purple text-rj-purple bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100'" class="px-6 py-4 text-sm font-bold uppercase tracking-wider border-b-2 transition">
                            {{ $locale }}
                        </button>
                    @endforeach
                </div>

                <div class="p-6 space-y-5">
                    @foreach($locales as $locale)
                        <div x-show="activeTab === '{{ $locale }}'" x-cloak class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Product Name') }} ({{ strtoupper($locale) }}) <span class="text-red-500">*</span></label>
                                <input type="text" name="translations[{{ $locale }}][name]" value="{{ old("translations.$locale.name") }}" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm" placeholder="{{ __('e.g. Black Phantom Perfume') }}">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Short Description') }} ({{ strtoupper($locale) }})</label>
                                <textarea name="translations[{{ $locale }}][short_description]" rows="2" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm" placeholder="{{ __('Brief summary...') }}">{{ old("translations.$locale.short_description") }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Full Description') }} ({{ strtoupper($locale) }})</label>
                                <textarea name="translations[{{ $locale }}][description]" rows="6" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm" placeholder="{{ __('Detailed description...') }}">{{ old("translations.$locale.description") }}</textarea>
                            </div>

                            <div class="pt-4 border-t border-gray-100 space-y-4">
                                <h4 class="text-sm font-semibold text-gray-900">{{ __('SEO Settings') }} ({{ strtoupper($locale) }})</h4>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('SEO Title') }}</label>
                                    <input type="text" name="translations[{{ $locale }}][seo_title]" value="{{ old("translations.$locale.seo_title") }}" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('SEO Description') }}</label>
                                    <textarea name="translations[{{ $locale }}][seo_description]" rows="2" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">{{ old("translations.$locale.seo_description") }}</textarea>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-3">{{ __('Pricing & Inventory') }}</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Price') }} ($) <span class="text-red-500">*</span></label>
                        <input type="number" name="price" x-model="form.price" step="0.01" min="0" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Compare Price') }} ($)</label>
                        <input type="number" name="compare_price" step="0.01" min="0" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('SKU') }}</label>
                        <input type="text" name="sku" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Barcode / ISBN') }}</label>
                        <input type="text" name="barcode" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Stock Status') }}</label>
                        <select name="stock_status" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                            <option value="in_stock">{{ __('In Stock') }}</option>
                            <option value="out_of_stock">{{ __('Out of Stock') }}</option>
                            <option value="on_backorder">{{ __('On Backorder') }}</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-6 pt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="manage_stock" value="0">
                        <input type="checkbox" name="manage_stock" value="1" @checked(true) class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                        <span class="text-sm text-gray-700">{{ __('Manage Stock') }}</span>
                    </label>
                    <div class="flex-1 max-w-[200px]">
                        <input type="number" name="stock_quantity" value="0" min="0" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm" placeholder="{{ __('Quantity') }}">
                    </div>
                </div>
            </div>

            <div x-show="form.type === 'physical'" x-cloak class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-3">{{ __('Shipping & Dimensions') }}</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Weight (kg)') }}</label>
                        <input type="number" name="weight" step="0.01" min="0" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Length (cm)') }}</label>
                        <input type="number" name="length" step="0.01" min="0" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Width (cm)') }}</label>
                        <input type="number" name="width" step="0.01" min="0" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Height (cm)') }}</label>
                        <input type="number" name="height" step="0.01" min="0" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    </div>
                </div>
            </div>

            <div x-show="form.type === 'digital'" x-cloak class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-3">{{ __('Digital Assets & Licensing') }}</h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Upload File (ZIP, PDF, MP3)') }}</label>
                    <input type="file" name="downloadable_file" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-rj-purple/10 file:text-rj-purple hover:file:bg-rj-purple/20">
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Download Limit (0 for unlimited)') }}</label>
                        <input type="number" name="download_limit" min="0" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Expiry (days, 0 for never)') }}</label>
                        <input type="number" name="download_expiry" min="0" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('License Keys Pool (One per line)') }}</label>
                    <textarea name="license_keys_pool" rows="3" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono" placeholder="XXXX-XXXX-XXXX-XXXX"></textarea>
                    <label class="flex items-center gap-2 mt-2 cursor-pointer">
                        <input type="hidden" name="auto_generate_license" value="0">
                        <input type="checkbox" name="auto_generate_license" value="1" class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                        <span class="text-sm text-gray-700">{{ __('Auto-generate license keys if pool is empty') }}</span>
                    </label>
                </div>
            </div>

            <div x-show="form.type === 'external'" x-cloak class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-3">{{ __('External Product Details') }}</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Product URL') }}</label>
                    <input type="url" name="external_url" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm" placeholder="https://amazon.com/dp/...">
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h2 class="text-lg font-semibold text-gray-900">{{ __('Product Variations') }}</h2>
                    <button type="button" @click="addVariation()" class="text-sm font-medium text-rj-purple hover:text-rj-pink transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        {{ __('Add Variation') }}
                    </button>
                </div>

                <div class="space-y-4">
                    <template x-for="(variation, index) in variations" :key="index">
                        <div class="p-4 rounded-xl border border-gray-200 bg-gray-50 relative">
                            <button type="button" @click="removeVariation(index)" class="absolute top-3 right-3 p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                            </button>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 pr-10">
                                <template x-for="attribute in attributes" :key="attribute.id">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1" x-text="attribute.translations[0]?.name || 'Attribute'"></label>
                                        <select :name="'variations[' + index + '][attributes][' + attribute.id + ']'" x-model="variation.attributes[attribute.id]" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                                            <option value="">{{ __('Select...') }}</option>
                                            <template x-for="val in attribute.values" :key="val.id">
                                                <option :value="val.id" x-text="val.translations[0]?.name"></option>
                                            </template>
                                        </select>
                                    </div>
                                </template>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('SKU') }}</label>
                                    <input type="text" :name="'variations[' + index + '][sku]'" x-model="variation.sku" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('Price') }}</label>
                                    <input type="number" :name="'variations[' + index + '][price]'" x-model="variation.price" step="0.01" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm" placeholder="Default">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('Stock') }}</label>
                                    <input type="number" :name="'variations[' + index + '][stock_quantity]'" x-model="variation.stock_quantity" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('Image') }}</label>
                                    <input type="file" :name="'variations[' + index + '][image]'" accept="image/*" class="w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-gray-200 file:text-gray-700">
                                </div>
                            </div>
                        </div>
                    </template>

                    <div x-show="variations.length === 0" class="text-center py-6 border-2 border-dashed border-gray-200 rounded-xl">
                        <p class="text-sm text-gray-500">{{ __('No variations added yet. Products without variations will use the default price and stock.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-3">{{ __('Publish') }}</h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Product Type') }}</label>
                    <select name="type" x-model="form.type" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        <option value="physical">{{ __('Physical Product') }}</option>
                        <option value="digital">{{ __('Digital Product') }}</option>
                        <option value="external">{{ __('External / Affiliate') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Status') }}</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        <option value="draft">{{ __('Draft') }}</option>
                        <option value="published">{{ __('Published') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Slug') }}</label>
                    <input type="text" name="slug" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono" placeholder="{{ __('Auto-generate') }}">
                </div>

                <label class="flex items-center gap-2 cursor-pointer pt-2">
                    <input type="hidden" name="is_featured" value="0">
                    <input type="checkbox" name="is_featured" value="1" class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <span class="text-sm text-gray-700">{{ __('Featured Product') }}</span>
                </label>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-3">{{ __('Organization') }}</h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Category') }}</label>
                    <select name="category_id" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        <option value="">{{ __('Uncategorized') }}</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->translation()?->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Brand') }}</label>
                    <select name="brand_id" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        <option value="">{{ __('No Brand') }}</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->translation()?->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Tags') }}</label>
                    <div class="max-h-40 overflow-y-auto border border-gray-300 rounded-lg p-2 space-y-1 bg-white">
                        @foreach($tags as $tag)
                            <label class="flex items-center gap-2 p-1 hover:bg-gray-50 rounded cursor-pointer">
                                <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                                <span class="text-sm text-gray-700">{{ $tag->translation()?->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-3">{{ __('Featured Image') }}</h2>
                <div>
                    <input type="file" name="featured_image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-rj-purple/10 file:text-rj-purple hover:file:bg-rj-purple/20">
                </div>
                <h2 class="text-sm font-semibold text-gray-900 border-b border-gray-100 pb-2 mt-4">{{ __('Gallery Images') }}</h2>
                <div>
                    <input type="file" name="gallery[]" accept="image/*" multiple class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function productForm() {
    return {
        activeTab: '{{ config('app.locale') }}',
        form: {
            type: 'physical',
            price: ''
        },
        attributes: @json($attributes),
        variations: [],
        addVariation() {
            this.variations.push({
                sku: '',
                price: '',
                stock_quantity: 0,
                attributes: {}
            });
        },
        removeVariation(index) {
            this.variations.splice(index, 1);
        }
    }
}
</script>
@endsection
