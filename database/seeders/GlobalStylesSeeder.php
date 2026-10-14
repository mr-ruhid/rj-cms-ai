<?php

namespace Database\Seeders;

use App\Models\Option;
use Illuminate\Database\Seeder;

class GlobalStylesSeeder extends Seeder
{
    public function run(): void
    {
        if (Option::where('option_name', 'global_colors')->exists()) {
            return;
        }

        $colors = [
            ['id' => 'primary', 'name' => __('Primary'), 'color' => '#7c3aed'],
            ['id' => 'secondary', 'name' => __('Secondary'), 'color' => '#ec4899'],
            ['id' => 'text', 'name' => __('Text'), 'color' => '#111827'],
            ['id' => 'accent', 'name' => __('Accent'), 'color' => '#f59e0b'],
            ['id' => 'light', 'name' => __('Light'), 'color' => '#f3f4f6'],
            ['id' => 'dark', 'name' => __('Dark'), 'color' => '#1f2937'],
        ];

        $fonts = [
            ['id' => 'primary', 'name' => __('Primary'), 'family' => 'Inter, sans-serif', 'weight' => '700'],
            ['id' => 'secondary', 'name' => __('Secondary'), 'family' => 'Inter, sans-serif', 'weight' => '500'],
            ['id' => 'text', 'name' => __('Body Text'), 'family' => 'Inter, sans-serif', 'weight' => '400'],
            ['id' => 'accent', 'name' => __('Accent'), 'family' => 'Inter, sans-serif', 'weight' => '600'],
        ];

        $spacing = [
            ['id' => 'xs', 'name' => __('Extra Small'), 'value' => '4'],
            ['id' => 'sm', 'name' => __('Small'), 'value' => '8'],
            ['id' => 'md', 'name' => __('Medium'), 'value' => '16'],
            ['id' => 'lg', 'name' => __('Large'), 'value' => '24'],
            ['id' => 'xl', 'name' => __('Extra Large'), 'value' => '48'],
        ];

        Option::create([
            'option_name' => 'global_colors',
            'option_value' => json_encode($colors),
            'option_group' => 'design',
            'option_type' => 'json',
            'autoload' => 1,
        ]);

        Option::create([
            'option_name' => 'global_fonts',
            'option_value' => json_encode($fonts),
            'option_group' => 'design',
            'option_type' => 'json',
            'autoload' => 1,
        ]);

        Option::create([
            'option_name' => 'global_spacing',
            'option_value' => json_encode($spacing),
            'option_group' => 'design',
            'option_type' => 'json',
            'autoload' => 1,
        ]);
    }
}