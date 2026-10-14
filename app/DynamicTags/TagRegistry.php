<?php

namespace App\DynamicTags;

use App\DynamicTags\Builtin\ArchiveDescriptionTag;
use App\DynamicTags\Builtin\ArchiveTitleTag;
use App\DynamicTags\Builtin\CurrentDateTag;
use App\DynamicTags\Builtin\CurrentUserTag;
use App\DynamicTags\Builtin\CustomFieldTag;
use App\DynamicTags\Builtin\PageTitleTag;
use App\DynamicTags\Builtin\PostAuthorTag;
use App\DynamicTags\Builtin\PostContentTag;
use App\DynamicTags\Builtin\PostDateTag;
use App\DynamicTags\Builtin\PostExcerptTag;
use App\DynamicTags\Builtin\PostFeaturedImageTag;
use App\DynamicTags\Builtin\PostTermsTag;
use App\DynamicTags\Builtin\PostTitleTag;
use App\DynamicTags\Builtin\ShortcodeTag;
use App\DynamicTags\Builtin\SiteLogoTag;
use App\DynamicTags\Builtin\SiteTaglineTag;
use App\DynamicTags\Builtin\SiteTitleTag;
use App\DynamicTags\Builtin\SiteUrlTag;
use App\DynamicTags\Builtin\UrlParamTag;

class TagRegistry
{
    protected static ?array $cache = null;

    public static function discover(): array
    {
        if (static::$cache !== null) {
            return static::$cache;
        }

        static::$cache = [
            new SiteTitleTag(),
            new SiteUrlTag(),
            new SiteLogoTag(),
            new SiteTaglineTag(),
            new CurrentDateTag(),
            new CurrentUserTag(),
            new UrlParamTag(),
            new ShortcodeTag(),
            new PageTitleTag(),
            new ArchiveTitleTag(),
            new ArchiveDescriptionTag(),
            new PostTitleTag(),
            new PostContentTag(),
            new PostExcerptTag(),
            new PostFeaturedImageTag(),
            new PostDateTag(),
            new PostAuthorTag(),
            new PostTermsTag(),
            new CustomFieldTag(),
        ];

        return static::$cache;
    }

    public static function all(): array
    {
        return static::discover();
    }

    public static function get(string $id): ?BaseTag
    {
        foreach (static::discover() as $tag) {
            if ($tag->id() === $id) {
                return $tag;
            }
        }

        return null;
    }

    public static function has(string $id): bool
    {
        return static::get($id) !== null;
    }

    public static function grouped(): array
    {
        $groups = [];

        foreach (static::discover() as $tag) {
            $groups[$tag->category()][] = $tag;
        }

        return $groups;
    }

    public static function categories(): array
    {
        return [
            'site' => ['label' => __('Site'), 'icon' => '🌐'],
            'user' => ['label' => __('User'), 'icon' => '👤'],
            'post' => ['label' => __('Post'), 'icon' => '📝'],
            'archive' => ['label' => __('Archive'), 'icon' => '📚'],
        ];
    }

    public static function resolve(?string $tagId, array $options = []): string
    {
        if (!$tagId) {
            return '';
        }

        $tag = static::get($tagId);

        if (!$tag) {
            return '';
        }

        try {
            return $tag->resolve($options);
        } catch (\Throwable $e) {
            return '';
        }
    }

    public static function toArray(): array
    {
        $out = [];

        foreach (static::discover() as $tag) {
            $out[] = [
                'id' => $tag->id(),
                'name' => $tag->name(),
                'category' => $tag->category(),
                'icon' => $tag->icon(),
                'description' => $tag->description(),
                'options' => $tag->options(),
            ];
        }

        return $out;
    }

    public static function reset(): void
    {
        static::$cache = null;
    }
}