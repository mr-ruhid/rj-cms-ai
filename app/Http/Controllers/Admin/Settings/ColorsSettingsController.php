<?php

namespace App\Http\Controllers\Admin\Settings;

use Illuminate\Http\Request;
use App\Models\Option;

class ColorsSettingsController extends BaseSettingsController
{
    public function index()
    {
        $options = $this->getGroup('colors');

        $data = [
            'presets' => rj_color_presets(),
            'activePreset' => rj_active_preset_key(),
        ];

        return view('admin.settings.colors', compact('options', 'data'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'admin_color_preset' => ['required', 'string', 'max:50'],
        ]);

        $presets = rj_color_presets();

        if (!isset($presets[$data['admin_color_preset']])) {
            return back()->with('error', __('Invalid preset selected.'));
        }

        Option::set('admin_color_preset', $data['admin_color_preset'], 'colors');

        return back()->with('success', __('Color preset saved.'));
    }
}
