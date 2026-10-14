<?php

namespace App\Shortcodes;

class ShortcodeRegistry
{
    protected static array $shortcodes = [];

    public static function add(string $tag, $callback): void
    {
        static::$shortcodes[$tag] = $callback;
    }

    public static function remove(string $tag): bool
    {
        if (!isset(static::$shortcodes[$tag])) {
            return false;
        }

        unset(static::$shortcodes[$tag]);

        return true;
    }

    public static function has(string $tag): bool
    {
        return isset(static::$shortcodes[$tag]);
    }

    public static function all(): array
    {
        return static::$shortcodes;
    }

    public static function render(string $content): string
    {
        if ($content === '' || !str_contains($content, '[')) {
            return $content;
        }

        $pattern = '/\[([a-zA-Z0-9_\-]+)((?:\s+[a-zA-Z0-9_\-]+=(?:"[^"]*"|\'[^\']*\'|[^\s\]]+))*)\s*\](?:(.*?)\[\/\1\])?/s';

        return preg_replace_callback($pattern, function ($matches) {
            $tag = $matches[1] ?? '';
            $attrString = $matches[2] ?? '';
            $innerContent = $matches[3] ?? null;

            if (!static::has($tag)) {
                return $matches[0];
            }

            $attrs = static::parseAttributes($attrString);

            try {
                $result = call_user_func(static::$shortcodes[$tag], $attrs, $innerContent, $tag);

                return is_string($result) ? $result : '';
            } catch (\Throwable $e) {
                logger()->error("Shortcode [{$tag}] failed: " . $e->getMessage());

                return '';
            }
        }, $content);
    }

    public static function strip(string $content): string
    {
        if ($content === '' || !str_contains($content, '[')) {
            return $content;
        }

        $pattern = '/\[[a-zA-Z0-9_\-]+(?:\s+[^\]]*)?\](?:.*?\[\/[a-zA-Z0-9_\-]+\])?/s';

        return preg_replace($pattern, '', $content);
    }

    public static function exists(string $content): bool
    {
        if ($content === '' || !str_contains($content, '[')) {
            return false;
        }

        return (bool) preg_match('/\[[a-zA-Z0-9_\-]+/', $content);
    }

    public static function parseAttributes(string $string): array
    {
        $attrs = [];

        $string = trim($string);

        if ($string === '') {
            return $attrs;
        }

        $pattern = '/([a-zA-Z0-9_\-]+)=("[^"]*"|\'[^\']*\'|[^\s\]]+)/';

        if (preg_match_all($pattern, $string, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $key = $match[1];
                $value = $match[2];

                if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                    (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                    $value = substr($value, 1, -1);
                }

                $attrs[$key] = $value;
            }
        }

        return $attrs;
    }

    public static function clear(): void
    {
        static::$shortcodes = [];
    }

    public static function registerDefaults(): void
    {
        static::add('year', fn() => date('Y'));

        static::add('site_name', fn() => rj_get_option('site_name', config('app.name')));

        static::add('site_url', fn() => url('/'));

        static::add('current_date', function ($attrs) {
            $format = $attrs['format'] ?? 'F j, Y';

            return date($format);
        });

        static::add('current_user', function ($attrs) {
            if (!auth()->check()) {
                return '';
            }

            $field = $attrs['field'] ?? 'name';

            return match ($field) {
                'email' => auth()->user()->email ?? '',
                'id' => (string) auth()->id(),
                default => auth()->user()->name ?? '',
            };
        });

        static::add('button', function ($attrs, $content) {
            $url = $attrs['url'] ?? '#';
            $target = $attrs['target'] ?? '_self';
            $style = $attrs['style'] ?? 'primary';

            $classes = [
                'primary' => 'rj-btn-primary',
                'secondary' => 'rj-btn-secondary',
                'ghost' => 'rj-btn-ghost',
            ];

            $class = $classes[$style] ?? $classes['primary'];

            $text = is_string($content) ? trim($content) : '';

            return '<a href="' . e($url) . '" target="' . e($target) . '" class="rj-shortcode-btn ' . $class . '">' . e($text) . '</a>';
        });

        static::add('divider', function ($attrs) {
            $style = $attrs['style'] ?? 'solid';

            return '<hr class="rj-shortcode-divider rj-shortcode-divider-' . e($style) . '">';
        });

        static::add('spacer', function ($attrs) {
            $height = $attrs['height'] ?? '32px';

            return '<div style="height:' . e($height) . ';"></div>';
        });

        static::add('highlight', function ($attrs, $content) {
            $color = $attrs['color'] ?? '#fbbf24';
            $text = is_string($content) ? $content : '';

            return '<span style="background:' . e($color) . '; padding:2px 6px; border-radius:4px;">' . e($text) . '</span>';
        });

        static::add('icon', function ($attrs) {
            $name = $attrs['name'] ?? '';
            $size = (int) ($attrs['size'] ?? 20);

            if ($name === '') {
                return '';
            }

            $paths = [
                'check' => '<path d="M5 13l4 4L19 7"/>',
                'arrow-right' => '<path d="M5 12h14M13 5l7 7-7 7"/>',
                'star' => '<path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>',
                'heart' => '<path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>',
                'mail' => '<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><path d="M22 6l-10 7L2 6"/>',
                'phone' => '<path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/>',
            ];

            $path = $paths[$name] ?? '';

            if ($path === '') {
                return '';
            }

            return '<svg width="' . $size . '" height="' . $size . '" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">' . $path . '</svg>';
        });
    }
}