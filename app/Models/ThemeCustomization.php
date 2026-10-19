<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThemeCustomization extends Model
{
    protected $table = 'rj_theme_customizations';

    protected $fillable = [
        'theme_slug',
        'section',
        'key',
        'value',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class, 'theme_slug', 'slug');
    }

    public function decodedValue()
    {
        if ($this->value === null) {
            return null;
        }

        $decoded = json_decode($this->value, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        return $this->value;
    }
}
