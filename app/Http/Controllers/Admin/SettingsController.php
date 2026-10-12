<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Option;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function general()
    {
        $options = $this->getGroup('general');
        return view('admin.settings.general', compact('options'));
    }

    public function updateGeneral(Request $request)
    {
        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'site_description' => ['nullable', 'string', 'max:500'],
            'site_url' => ['required', 'url'],
            'admin_email' => ['required', 'email'],
            'timezone' => ['required', 'string'],
            'date_format' => ['required', 'string'],
            'time_format' => ['required', 'string'],
            'language' => ['required', 'string'],
            'currency' => ['required', 'string'],
        ]);

        foreach ($data as $name => $value) {
            $type = in_array($name, ['admin_email']) ? 'email' : 'text';
            Option::set($name, $value, 'general', $type);
        }

        return back()->with('success', __('Settings saved.'));
    }

    protected function getGroup(string $group): array
    {
        return Option::where('option_group', $group)
            ->pluck('option_value', 'option_name')
            ->toArray();
    }
}
