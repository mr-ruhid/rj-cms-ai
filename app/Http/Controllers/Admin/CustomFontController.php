<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomFont;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomFontController extends Controller
{
    public function index()
    {
        $fonts = CustomFont::with('author')
            ->orderByDesc('updated_at')
            ->get();

        return view('admin.fonts.index', compact('fonts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'family' => 'nullable|string|max:255',
            'files' => 'required|array|min:1',
            'files.*.file' => 'required|file|mimes:woff,woff2,ttf,otf,eot,svg|max:5120',
            'files.*.weight' => 'nullable|string|max:10',
            'files.*.style' => 'nullable|in:normal,italic',
        ]);

        $family = $validated['family'] ?? \Illuminate\Support\Str::slug($validated['name']);
        $family = CustomFont::uniqueSlug($family);

        $storedFiles = [];

        foreach ($validated['files'] as $item) {
            if (empty($item['file'])) {
                continue;
            }

            $uploaded = $item['file'];
            $ext = strtolower($uploaded->getClientOriginalExtension());
            $filename = $family . '-' . ($item['weight'] ?? '400') . '-' . ($item['style'] ?? 'normal') . '.' . $ext;
            $path = 'uploads/fonts/' . $filename;

            Storage::disk('public_uploads')->putFileAs('fonts', $uploaded, $filename);

            $storedFiles[] = [
                'url' => url('uploads/fonts/' . $filename),
                'path' => 'uploads/fonts/' . $filename,
                'weight' => $item['weight'] ?? '400',
                'style' => $item['style'] ?? 'normal',
                'format' => $this->detectFormat($ext),
            ];
        }

        if (empty($storedFiles)) {
            return back()->with('error', __('No files uploaded.'));
        }

        $font = CustomFont::create([
            'name' => $validated['name'],
            'family' => $family,
            'source' => 'upload',
            'files' => $storedFiles,
            'variants' => $this->summarizeVariants($storedFiles),
            'status' => 'active',
            'author_id' => auth()->id(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'font' => [
                    'id' => $font->id,
                    'name' => $font->name,
                    'family' => $font->family,
                ],
            ]);
        }

        return redirect()
            ->route('admin.custom-fonts.index')
            ->with('success', __('Font uploaded.'));
    }

    public function update(Request $request, CustomFont $customFont)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'nullable|in:active,inactive',
        ]);

        $customFont->update([
            'name' => $validated['name'],
            'status' => $validated['status'] ?? $customFont->status,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', __('Font updated.'));
    }

    public function destroy(CustomFont $customFont)
    {
        foreach ($customFont->fonts() as $file) {
            $path = $file['path'] ?? '';
            if ($path && Storage::disk('public_uploads')->exists($path)) {
                Storage::disk('public_uploads')->delete($path);
            }
        }

        $customFont->delete();

        return redirect()
            ->route('admin.custom-fonts.index')
            ->with('success', __('Font deleted.'));
    }

    public function css()
    {
        $css = '';

        foreach (CustomFont::active()->get() as $font) {
            $css .= $font->cssRule();
        }

        return response($css, 200, [
            'Content-Type' => 'text/css; charset=UTF-8',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    protected function detectFormat(string $ext): string
    {
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

    protected function summarizeVariants(array $files): array
    {
        $variants = [];

        foreach ($files as $f) {
            $key = ($f['weight'] ?? '400') . '-' . ($f['style'] ?? 'normal');

            if (!isset($variants[$key])) {
                $variants[$key] = [
                    'weight' => $f['weight'] ?? '400',
                    'style' => $f['style'] ?? 'normal',
                ];
            }
        }

        return array_values($variants);
    }
}