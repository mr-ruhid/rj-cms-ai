<?php

namespace App\Http\Controllers\Admin\Settings;

use Illuminate\Http\Request;
use App\Models\Option;

class SocialSettingsController extends BaseSettingsController
{
    public function index()
    {
        $options = $this->getGroup('social');

        $data = [
            'providers' => rj_social_providers(),
            'regions' => rj_social_regions(),
        ];

        return view('admin.settings.social', compact('options', 'data'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'social_enabled' => ['nullable', 'boolean'],
            'social_auto_register' => ['nullable', 'boolean'],
            'social_auto_link' => ['nullable', 'boolean'],
            'social_default_role' => ['required', 'in:user,editor,admin'],
            'providers' => ['nullable', 'array'],
            'providers.*' => ['nullable', 'array'],
        ]);

        $data['social_enabled'] = $request->boolean('social_enabled') ? '1' : '0';
        $data['social_auto_register'] = $request->boolean('social_auto_register') ? '1' : '0';
        $data['social_auto_link'] = $request->boolean('social_auto_link') ? '1' : '0';

        Option::set('social_enabled', $data['social_enabled'], 'social');
        Option::set('social_auto_register', $data['social_auto_register'], 'social');
        Option::set('social_auto_link', $data['social_auto_link'], 'social');
        Option::set('social_default_role', $data['social_default_role'], 'social');

        $providers = rj_social_providers();
        $input = $request->input('providers', []);

        foreach ($providers as $key => $provider) {
            $enabled = isset($input[$key]['enabled']) ? '1' : '0';
            Option::set('social_' . $key . '_enabled', $enabled, 'social');

            foreach ($provider['fields'] as $field => $meta) {
                $value = $input[$key][$field] ?? '';

                if ($meta['type'] === 'password' && $value === '') {
                    continue;
                }

                Option::set('social_' . $key . '_' . $field, $value, 'social');
            }
        }

        return back()->with('success', __('Social login settings saved.'));
    }
}