@extends('admin.settings.layout')

@section('settings')
<form method="POST" action="{{ route('admin.settings.media.update') }}" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Image Sizes') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Thumbnail, medium and large image dimensions.') }}</p>
        </div>

        <div class="p-6 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Thumbnail Size') }}</label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="thumbnail_width" min="20" max="1000"
                               value="{{ $options['thumbnail_width'] ?? '150' }}"
                               class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        <span class="text-gray-400 text-sm">×</span>
                        <input type="number" name="thumbnail_height" min="20" max="1000"
                               value="{{ $options['thumbnail_height'] ?? '150' }}"
                               class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    </div>
                    <label class="flex items-center gap-2 mt-2 cursor-pointer">
                        <input type="checkbox" name="thumbnail_crop" value="1"
                               {{ ($options['thumbnail_crop'] ?? '1') === '1' ? 'checked' : '' }}
                               class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                        <span class="text-xs text-gray-600">{{ __('Crop to exact dimensions') }}</span>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Medium Size') }}</label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="medium_width" min="50" max="2000"
                               value="{{ $options['medium_width'] ?? '300' }}"
                               class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        <span class="text-gray-400 text-sm">×</span>
                        <input type="number" name="medium_height" min="50" max="2000"
                               value="{{ $options['medium_height'] ?? '300' }}"
                               class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    </div>
                    <label class="flex items-center gap-2 mt-2 cursor-pointer">
                        <input type="checkbox" name="medium_crop" value="1"
                               {{ ($options['medium_crop'] ?? '0') === '1' ? 'checked' : '' }}
                               class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                        <span class="text-xs text-gray-600">{{ __('Crop to exact dimensions') }}</span>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Large Size') }}</label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="large_width" min="100" max="4000"
                               value="{{ $options['large_width'] ?? '1024' }}"
                               class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        <span class="text-gray-400 text-sm">×</span>
                        <input type="number" name="large_height" min="100" max="4000"
                               value="{{ $options['large_height'] ?? '1024' }}"
                               class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    </div>
                    <label class="flex items-center gap-2 mt-2 cursor-pointer">
                        <input type="checkbox" name="large_crop" value="1"
                               {{ ($options['large_crop'] ?? '0') === '1' ? 'checked' : '' }}
                               class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                        <span class="text-xs text-gray-600">{{ __('Crop to exact dimensions') }}</span>
                    </label>
                </div>
            </div>

        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Uploads') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('File storage, folder structure and limits.') }}</p>
        </div>

        <div class="p-6 space-y-5">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Folder Structure') }}</label>
                    <select name="folder_structure" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        @foreach ($data['folderStructures'] as $value => $label)
                            <option value="{{ $value }}" {{ ($options['folder_structure'] ?? 'monthly') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Max Upload Size (MB)') }}</label>
                    <input type="number" name="max_upload_size" min="1" max="512"
                           value="{{ $options['max_upload_size'] ?? '10' }}"
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                </div>
            </div>

            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="organize_uploads" value="1"
                       {{ ($options['organize_uploads'] ?? '1') === '1' ? 'checked' : '' }}
                       class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                <span>
                    <span class="block text-sm font-medium text-gray-700">{{ __('Organize uploads into folders') }}</span>
                    <span class="block text-xs text-gray-500 mt-0.5">{{ __('Use the folder structure setting above.') }}</span>
                </span>
            </label>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Allowed File Extensions') }}</label>
                <input type="text" name="allowed_extensions"
                       value="{{ $options['allowed_extensions'] ?? 'jpg,jpeg,png,gif,webp,svg,pdf,doc,docx,mp4,mp3' }}"
                       class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                <p class="text-xs text-gray-500 mt-1">{{ __('Comma-separated list.') }}</p>
            </div>

        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Optimization') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Image quality and performance.') }}</p>
        </div>

        <div class="p-6 space-y-5">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Image Quality') }}</label>
                    <select name="image_quality" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        @foreach ($data['imageQualities'] as $value => $label)
                            <option value="{{ $value }}" {{ ($options['image_quality'] ?? 85) == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('WebP Quality') }}</label>
                    <input type="number" name="webp_quality" min="10" max="100"
                           value="{{ $options['webp_quality'] ?? '80' }}"
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                </div>
            </div>

            <label class="flex items-start gap-3 cursor-pointer border-t border-gray-100 pt-5">
                <input type="checkbox" name="convert_webp" value="1"
                       {{ ($options['convert_webp'] ?? '0') === '1' ? 'checked' : '' }}
                       class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                <span>
                    <span class="block text-sm font-medium text-gray-700">{{ __('Auto-convert to WebP') }}</span>
                    <span class="block text-xs text-gray-500 mt-0.5">{{ __('Convert uploaded JPEG/PNG to WebP for smaller file sizes.') }}</span>
                </span>
            </label>

            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="lazy_load" value="1"
                       {{ ($options['lazy_load'] ?? '1') === '1' ? 'checked' : '' }}
                       class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                <span>
                    <span class="block text-sm font-medium text-gray-700">{{ __('Enable lazy loading') }}</span>
                    <span class="block text-xs text-gray-500 mt-0.5">{{ __('Load images only when they enter the viewport.') }}</span>
                </span>
            </label>

        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Watermark') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Add a text watermark to uploaded images.') }}</p>
        </div>

        <div class="p-6 space-y-5">

            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="watermark_enabled" value="1"
                       {{ ($options['watermark_enabled'] ?? '0') === '1' ? 'checked' : '' }}
                       class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                <span>
                    <span class="block text-sm font-medium text-gray-700">{{ __('Enable watermark') }}</span>
                </span>
            </label>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 border-t border-gray-100 pt-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Watermark Text') }}</label>
                    <input type="text" name="watermark_text"
                           value="{{ $options['watermark_text'] ?? '' }}"
                           placeholder="{{ __('© Your Site') }}"
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Position') }}</label>
                    <select name="watermark_position" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        @foreach ($data['watermarkPositions'] as $value => $label)
                            <option value="{{ $value }}" {{ ($options['watermark_position'] ?? 'bottom-right') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Opacity (%)') }}</label>
                    <input type="number" name="watermark_opacity" min="0" max="100"
                           value="{{ $options['watermark_opacity'] ?? '50' }}"
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                </div>
            </div>

        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
            {{ __('Save Changes') }}
        </button>
    </div>

</form>
@endsection
