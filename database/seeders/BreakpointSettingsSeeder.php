<?php

namespace Database\Seeders;

use App\Models\Option;
use Illuminate\Database\Seeder;

class BreakpointSettingsSeeder extends Seeder
{
    public function run(): void
    {
        if (Option::where('option_name', 'rj_breakpoints')->exists()) {
            return;
        }

        $breakpoints = [
            'mobile' => [
                'label' => 'Mobile',
                'max' => 560,
                'icon' => 'mobile',
            ],
            'tablet' => [
                'label' => 'Tablet',
                'max' => 860,
                'icon' => 'tablet',
            ],
            'desktop' => [
                'label' => 'Desktop',
                'min' => 861,
                'icon' => 'desktop',
            ],
        ];

        Option::create([
            'option_name' => 'rj_breakpoints',
            'option_value' => json_encode($breakpoints),
            'option_group' => 'design',
            'option_type' => 'json',
            'autoload' => 1,
        ]);
    }
}