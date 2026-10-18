<?php

namespace RjPlugins\FormBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Form extends Model
{
    protected $table = 'rj_forms';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'settings',
        'status',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function fields()
    {
        return $this->hasMany(FormField::class, 'form_id')->orderBy('sort_order');
    }

    public function submissions()
    {
        return $this->hasMany(FormSubmission::class, 'form_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (
            static::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }
}
