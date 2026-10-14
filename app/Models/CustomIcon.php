<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CustomIcon extends Model
{
    protected $table = 'rj_custom_icons';

    protected $fillable = [
        'name',
        'slug',
        'source',
        'library',
        'svg',
        'path',
        'status',
        'author_id',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeLibrary($query, string $library)
    {
        return $query->where('library', $library);
    }

    public function svgContent(): string
    {
        if ($this->source === 'upload' && $this->path) {
            $full = public_path($this->path);

            if (file_exists($full)) {
                $svg = file_get_contents($full);
                if (is_string($svg) && trim($svg) !== '') {
                    return $svg;
                }
            }
        }

        return (string) $this->svg;
    }

    public function inlineSvg(int $size = 20, ?string $class = null): string
    {
        $svg = $this->svgContent();

        if ($svg === '') {
            return '';
        }

        $classAttr = $class ? ' class="' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . '"' : '';

        if (preg_match('/<svg[^>]*>/i', $svg, $m)) {
            $originalTag = $m[0];
            $newTag = preg_replace('/\swidth="[^"]*"/i', '', $originalTag);
            $newTag = preg_replace('/\sheight="[^"]*"/i', '', $newTag);
            $newTag = preg_replace('/<svg/i', '<svg width="' . $size . '" height="' . $size . '"', $newTag, 1);

            if ($classAttr && !preg_match('/\sclass="[^"]*"/i', $newTag)) {
                $newTag = preg_replace('/<svg/i', '<svg' . $classAttr, $newTag, 1);
            }

            $svg = preg_replace('/<svg[^>]*>/i', $newTag, $svg, 1);
        }

        return $svg;
    }

    public function toIconArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'library' => $this->library,
            'source' => $this->source,
            'svg' => $this->svgContent(),
        ];
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