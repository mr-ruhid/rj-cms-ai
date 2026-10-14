<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomIcon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomIconController extends Controller
{
    public function index(Request $request)
    {
        $query = CustomIcon::with('author')->orderByDesc('updated_at');

        if ($library = $request->input('library')) {
            $query->where('library', $library);
        }

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('slug', 'like', '%' . $search . '%');
            });
        }

        $icons = $query->paginate(48)->withQueryString();

        $libraries = CustomIcon::query()
            ->whereNotNull('library')
            ->select('library')
            ->distinct()
            ->pluck('library')
            ->toArray();

        return view('admin.icons.index', compact('icons', 'libraries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'library' => 'nullable|string|max:100',
            'source' => 'required|in:svg,upload',
            'svg' => 'nullable|string',
            'file' => 'nullable|file|mimes:svg|max:512',
        ]);

        if ($validated['source'] === 'svg') {
            if (empty($validated['svg'])) {
                return back()->with('error', __('SVG content is required.'));
            }

            $svg = $validated['svg'];
        } else {
            if (!$request->hasFile('file')) {
                return back()->with('error', __('SVG file is required.'));
            }

            $svg = file_get_contents($request->file('file')->getRealPath());
        }

        if (!$this->looksLikeSvg($svg)) {
            return back()->with('error', __('Invalid SVG content.'));
        }

        $svg = $this->sanitizeSvg($svg);

        $icon = CustomIcon::create([
            'name' => $validated['name'],
            'slug' => CustomIcon::uniqueSlug($validated['name']),
            'source' => 'svg',
            'library' => $validated['library'] ?? null,
            'svg' => $svg,
            'path' => null,
            'status' => 'active',
            'author_id' => auth()->id(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'icon' => $icon->toIconArray(),
            ]);
        }

        return redirect()
            ->route('admin.custom-icons.index')
            ->with('success', __('Icon added.'));
    }

    public function storeBulk(Request $request)
    {
        $validated = $request->validate([
            'library' => 'nullable|string|max:100',
            'svgs' => 'required|array|min:1',
            'svgs.*.name' => 'required|string|max:255',
            'svgs.*.svg' => 'required|string',
        ]);

        $created = 0;

        foreach ($validated['svgs'] as $item) {
            $svg = trim((string) $item['svg']);

            if (!$this->looksLikeSvg($svg)) {
                continue;
            }

            $svg = $this->sanitizeSvg($svg);

            CustomIcon::create([
                'name' => $item['name'],
                'slug' => CustomIcon::uniqueSlug($item['name']),
                'source' => 'svg',
                'library' => $validated['library'] ?? null,
                'svg' => $svg,
                'path' => null,
                'status' => 'active',
                'author_id' => auth()->id(),
            ]);

            $created++;
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'created' => $created]);
        }

        return back()->with('success', __(':n icons added.', ['n' => $created]));
    }

    public function update(Request $request, CustomIcon $customIcon)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'library' => 'nullable|string|max:100',
            'status' => 'nullable|in:active,inactive',
        ]);

        $customIcon->update([
            'name' => $validated['name'],
            'library' => $validated['library'] ?? $customIcon->library,
            'status' => $validated['status'] ?? $customIcon->status,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', __('Icon updated.'));
    }

    public function destroy(CustomIcon $customIcon)
    {
        if ($customIcon->path && Storage::disk('public_uploads')->exists($customIcon->path)) {
            Storage::disk('public_uploads')->delete($customIcon->path);
        }

        $customIcon->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()
            ->route('admin.custom-icons.index')
            ->with('success', __('Icon deleted.'));
    }

    public function options(Request $request)
    {
        $query = CustomIcon::active();

        if ($library = $request->input('library')) {
            $query->where('library', $library);
        }

        $icons = $query->orderBy('name')->get();

        return response()->json([
            'icons' => $icons->map(fn($i) => $i->toIconArray()),
            'libraries' => CustomIcon::query()
                ->whereNotNull('library')
                ->select('library')
                ->distinct()
                ->pluck('library')
                ->toArray(),
        ]);
    }

    protected function looksLikeSvg(string $svg): bool
    {
        return (bool) preg_match('/<svg[\s\S]*<\/svg>/i', $svg);
    }

    protected function sanitizeSvg(string $svg): string
    {
        $svg = preg_replace('/<script[\s\S]*?<\/script>/i', '', $svg);
        $svg = preg_replace('/\son\w+="[^"]*"/i', '', $svg);
        $svg = preg_replace("/\son\w+='[^']*'/i", '', $svg);
        $svg = preg_replace('/javascript:/i', '', $svg);

        return trim($svg);
    }
}