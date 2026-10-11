<?php

namespace Database\Seeders;

use App\Models\Option;
use Illuminate\Database\Seeder;

class OptionSeeder extends Seeder
{
    public function run(): void
    {
        $options = [
            ['site_name', 'RJ CMS', 'general', 'text'],
            ['site_description', 'A modern CMS built with Laravel', 'general', 'text'],
            ['site_url', 'http://127.0.0.1:8000', 'general', 'text'],
            ['admin_email', 'admin@example.com', 'general', 'email'],
            ['timezone', 'Asia/Baku', 'general', 'text'],
            ['date_format', 'Y-m-d', 'general', 'text'],
            ['time_format', 'H:i', 'general', 'text'],
            ['language', 'en', 'general', 'text'],
            ['currency', 'USD', 'general', 'text'],

            ['posts_per_page', '10', 'reading', 'number'],
            ['homepage_type', 'latest', 'reading', 'text'],
            ['feed_enabled', '1', 'reading', 'boolean'],

            ['comments_enabled', '1', 'discussion', 'boolean'],
            ['comments_moderation', '1', 'discussion', 'boolean'],
            ['ai_moderation', '0', 'discussion', 'boolean'],

            ['permalink_structure', '/%postname%/', 'permalinks', 'text'],

            ['admin_theme', 'light', 'colors', 'text'],
            ['admin_color', '#111827', 'colors', 'text'],
            ['frontend_theme', 'default', 'colors', 'text'],

            ['seo_enabled', '1', 'seo', 'boolean'],
            ['seo_ai_enabled', '0', 'seo', 'boolean'],

            ['ai_enabled', '0', 'ai', 'boolean'],
            ['ai_provider', 'openai', 'ai', 'text'],
            ['ai_model', 'gpt-4o-mini', 'ai', 'text'],
            ['ai_temperature', '0.7', 'ai', 'text'],
            ['ai_max_tokens', '2000', 'ai', 'number'],
            ['ai_monthly_limit', '100', 'ai', 'number'],

            ['security_2fa', '0', 'security', 'boolean'],
            ['security_login_limit', '5', 'security', 'number'],

            ['backup_auto', '0', 'backup', 'boolean'],
            ['backup_frequency', 'daily', 'backup', 'text'],

            ['cache_enabled', '0', 'cache', 'boolean'],
            ['cache_ttl', '3600', 'cache', 'number'],

            ['updates_core_auto', '0', 'updates', 'boolean'],
            ['updates_plugin_auto', '0', 'updates', 'boolean'],
            ['updates_theme_auto', '0', 'updates', 'boolean'],

            ['rj_version', '1.0.0', 'system', 'text'],
            ['rj_installed_at', now()->toDateTimeString(), 'system', 'text'],
        ];

        foreach ($options as [$name, $value, $group, $type]) {
            Option::updateOrCreate(
                ['option_name' => $name],
                [
                    'option_value' => $value,
                    'option_group' => $group,
                    'option_type' => $type,
                    'autoload' => true,
                ]
            );
        }
    }
}
