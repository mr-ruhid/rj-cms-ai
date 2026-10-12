<?php

namespace App\Http\Controllers\Admin\Settings;

use Illuminate\Http\Request;
use App\Models\Option;

class MediaSettingsController extends BaseSettingsController
{
    public function index()
    {
        $options = $this->getGroup('media');

        $data = [
            'folderStructures' => $this->folderStructures(),
            'imageQualities' => $this->imageQualities(),
            'watermarkPositions' => $this->watermarkPositions(),
        ];

        return view('admin.settings.media', compact('options', 'data'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'thumbnail_width' => ['required', 'integer', 'min:20', 'max:1000'],
            'thumbnail_height' => ['required', 'integer', 'min:20', 'max:1000'],
            'thumbnail_crop' => ['nullable', 'boolean'],

            'medium_width' => ['required', 'integer', 'min:50', 'max:2000'],
            'medium_height' => ['required', 'integer', 'min:50', 'max:2000'],
            'medium_crop' => ['nullable', 'boolean'],

            'large_width' => ['required', 'integer', 'min:100', 'max:4000'],
            'large_height' => ['required', 'integer', 'min:100', 'max:4000'],
            'large_crop' => ['nullable', 'boolean'],

            'folder_structure' => ['required', 'in:none,yearly,monthly,daily'],
            'organize_uploads' => ['nullable', 'boolean'],
            'image_quality' => ['required', 'integer', 'min:10', 'max:100'],
            'max_upload_size' => ['required', 'integer', 'min:1', 'max:512'],
            'allowed_extensions' => ['nullable', 'string', 'max:500'],

            'convert_webp' => ['nullable', 'boolean'],
            'webp_quality' => ['nullable', 'integer', 'min:10', 'max:100'],
            'lazy_load' => ['nullable', 'boolean'],

            'watermark_enabled' => ['nullable', 'boolean'],
            'watermark_text' => ['nullable', 'string', 'max:100'],
            'watermark_position' => ['nullable', 'in:top-left,top-right,bottom-left,bottom-right,center'],
            'watermark_opacity' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $booleanKeys = [
            'thumbnail_crop',
            'medium_crop',
            'large_crop',
            'organize_uploads',
            'convert_webp',
            'lazy_load',
            'watermark_enabled',
        ];

        foreach ($booleanKeys as $key) {
            $data[$key] = $request->boolean($key) ? '1' : '0';
        }

        $typeMap = [
            'thumbnail_width' => 'number',
            'thumbnail_height' => 'number',
            'medium_width' => 'number',
            'medium_height' => 'number',
            'large_width' => 'number',
            'large_height' => 'number',
            'image_quality' => 'number',
            'max_upload_size' => 'number',
            'webp_quality' => 'number',
            'watermark_opacity' => 'number',
        ];

        foreach ($data as $name => $value) {
            Option::set($name, $value, 'media', $typeMap[$name] ?? 'text');
        }

        return back()->with('success', __('Media settings saved.'));
    }

    protected function folderStructures(): array
    {
        return [
            'none' => __('Do not organize'),
            'yearly' => __('Organize by year (e.g. 2026/)'),
            'monthly' => __('Organize by year/month (e.g. 2026/10/)'),
            'daily' => __('Organize by year/month/day (e.g. 2026/10/12/)'),
        ];
    }

    protected function imageQualities(): array
    {
        return [
            60 => '60% — ' . __('Small file, lower quality'),
            75 => '75% — ' . __('Balanced'),
            85 => '85% — ' . __('Recommended'),
            95 => '95% — ' . __('High quality, larger file'),
            100 => '100% — ' . __('Maximum quality (not recommended)'),
        ];
    }

    protected function watermarkPositions(): array
    {
        return [
            'top-left' => __('Top Left'),
            'top-right' => __('Top Right'),
            'bottom-left' => __('Bottom Left'),
            'bottom-right' => __('Bottom Right'),
            'center' => __('Center'),
        ];
    }
}
