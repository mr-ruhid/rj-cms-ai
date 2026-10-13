<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Models\TagTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $query = Tag::with('translations');

        if ($search = $request->input('search')) {
            $query->whereHas('translations', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhere('slug', 'like', "%{$search}%");
        }

        $tags = $query->ordered()->get();
        $locales = rj_available_locales();

        $stats = [
            'total' => Tag::count(),
            'used' => Tag::where('post_count', '>', 0)->count(),
            'unused' => Tag::where('post_count', 0)->count(),
        ];

        return view('admin.tags.index', compact('tags', 'locales', 'stats'));
    }

    public function store(Request $request)
    {
        $baseLocale = config('app.locale');
        $locales = rj_available_locales();

        $rules = [
            'translations' => ['required', 'array'],
        ];

        foreach ($locales as $locale) {
            $rules["translations.{$locale}.name"] = $locale === $baseLocale
                ? ['required', 'string', 'max:100']
                : ['nullable', 'string', 'max:100'];
        }

        $data = $request->validate($rules);

        $baseName = trim($data['translations'][$baseLocale]['name']);
        $slug = $this->uniqueSlug(Str::slug($baseName));

        if (empty($slug)) {
            $slug = 'tag-' . Str::random(6);
        }

        $tag = Tag::create(['slug' => $slug]);

        foreach ($locales as $locale) {
            $name = trim($data['translations'][$locale]['name'] ?? '');

            if (empty($name)) {
                continue;
            }

            TagTranslation::create([
                'tag_id' => $tag->id,
                'locale' => $locale,
                'name' => $name,
            ]);
        }

        return back()->with('success', __('Tag created.'));
    }

    public function update(Request $request, Tag $tag)
    {
        $baseLocale = config('app.locale');
        $locales = rj_available_locales();

        $rules = [
            'translations' => ['required', 'array'],
        ];

        foreach ($locales as $locale) {
            $rules["translations.{$locale}.name"] = $locale === $baseLocale
                ? ['required', 'string', 'max:100']
                : ['nullable', 'string', 'max:100'];
        }

        $data = $request->validate($rules);

        foreach ($locales as $locale) {
            $name = trim($data['translations'][$locale]['name'] ?? '');

            $existing = TagTranslation::where('tag_id', $tag->id)
                ->where('locale', $locale)
                ->first();

            if (empty($name)) {
                if ($existing && $locale !== $baseLocale) {
                    $existing->delete();
                }
                continue;
            }

            if ($existing) {
                $existing->update(['name' => $name]);
            } else {
                TagTranslation::create([
                    'tag_id' => $tag->id,
                    'locale' => $locale,
                    'name' => $name,
                ]);
            }
        }

        return back()->with('success', __('Tag updated.'));
    }

    public function destroy(Tag $tag)
    {
        if ($tag->post_count > 0) {
            return back()->with('error', __('Cannot delete — tag is used by :count post(s).', ['count' => $tag->post_count]));
        }

        $tag->delete();

        return back()->with('success', __('Tag deleted.'));
    }

    protected function uniqueSlug(string $slug): string
    {
        $base = $slug;
        $i = 1;

        while (Tag::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;

            if ($i > 100) {
                return $base . '-' . Str::random(6);
            }
        }

        return $slug;
    }
}