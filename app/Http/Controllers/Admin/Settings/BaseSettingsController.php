<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Option;

abstract class BaseSettingsController extends Controller
{
    protected function getGroup(string $group): array
    {
        return Option::where('option_group', $group)
            ->pluck('option_value', 'option_name')
            ->toArray();
    }
}
