<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            OptionSeeder::class,
            GlobalStylesSeeder::class,
            BreakpointSettingsSeeder::class,
            PermissionSeeder::class,
            PostTypeSeeder::class,
            TaxonomySeeder::class,
            MenuSeeder::class,
            PageSeeder::class,
            PostSeeder::class,
            TemplateSeeder::class,
        ]);
    }
}