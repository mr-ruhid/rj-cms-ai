<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CustomFont extends Model
{
    protected $table = 'rj_custom_fonts';

    protected $fillable = [
        'name',
        'family',
        'source',
        'files',
        'variants',
        'status',
        'author_id',
    ];

    protected $casts = [
        'files' => 'array',
        'variants' => 'array',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function fonts(): array
    {
        if (!is_array($this->files)) {
            return [];
        }

        return $this->files;
    }

    public function variants(): array
    {
        if (!is_array($this->variants)) {
            return [];
        }

        return $this->variants;
    }

    public function cssRule(): string
    {
        $rules = [];

        foreach ($this->fonts() as $font) {
            $weight = $font['weight'] ?? '400';
            $style = $font['style'] ?? 'normal';
            $url = $font['url'] ?? '';
            $format = $font['format'] ?? $this->detectFormat($url);

            if ($url === '') {
                continue;
            }

            $rules[] = "@font-face{font-family:'{$this->family}';font-style:{$style};font-weight:{$weight};font-display:swap;src:url('{$url}') format('{$format}');}";
        }

        return implode('', $rules);
    }

    protected function detectFormat(string $url): string
    {
        $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));

        return match ($ext) {
            'woff2' => 'woff2',
            'woff' => 'woff',
            'ttf' => 'truetype',
            'otf' => 'opentype',
            'eot' => 'embedded-opentype',
            'svg' => 'svg',
            default => 'woff2',
        };
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (
            static::query()
                ->where('family', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }
}