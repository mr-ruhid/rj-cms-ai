<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Option extends Model
{
    protected $table = 'rj_options';

    protected $fillable = [
        'option_name',
        'option_value',
        'option_group',
        'option_type',
        'autoload',
    ];

    protected $casts = [
        'autoload' => 'boolean',
    ];

    public static function get(string $name, $default = null)
    {
        $options = Cache::rememberForever('rj_options', function () {
            return static::where('autoload', true)->pluck('option_value', 'option_name')->toArray();
        });

        return $options[$name] ?? $default;
    }

    public static function set(string $name, $value, string $group = 'general', string $type = 'text', bool $autoload = true): void
    {
        static::updateOrCreate(
            ['option_name' => $name],
            [
                'option_value' => is_array($value) ? json_encode($value) : $value,
                'option_group' => $group,
                'option_type' => $type,
                'autoload' => $autoload,
            ]
        );

        Cache::forget('rj_options');
    }

    public static function forget(string $name): void
    {
        static::where('option_name', $name)->delete();
        Cache::forget('rj_options');
    }

    public static function flush(): void
    {
        Cache::forget('rj_options');
    }
}
