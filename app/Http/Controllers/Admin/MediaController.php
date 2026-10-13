<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $query = Media::with('uploader')->latest();

        if ($type = $request->input('type')) {
            if ($type === 'image') {
                $query->where('mime_type', 'like', 'image/%');
            } elseif ($type === 'video') {
                $query->where('mime_type', 'like', 'video/%');
            } elseif ($type === 'audio') {
                $query->where('mime_type', 'like', 'audio/%');
            } elseif ($type === 'document') {
                $query->whereIn('mime_type', [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-powerpoint',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'text/plain',
                ]);
            }
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('original_name', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('alt_text', 'like', "%{$search}%");
            });
        }

        $media = $query->paginate(24)->withQueryString();

        $stats = [
            'total' => Media::count(),
            'images' => Media::where('mime_type', 'like', 'image/%')->count(),
            'videos' => Media::where('mime_type', 'like', 'video/%')->count(),
            'documents' => Media::where('mime_type', 'not like', 'image/%')
                ->where('mime_type', 'not like', 'video/%')
                ->where('mime_type', 'not like', 'audio/%')
                ->count(),
            'size' => Media::sum('size'),
        ];

        return view('admin.media.index', compact('media', 'stats'));
    }

    public function create()
    {
        return view('admin.media.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'files' => ['required', 'array'],
            'files.*' => ['file', 'max:10240'],
        ]);

        $uploaded = [];
        $errors = [];

        foreach ($request->file('files') as $file) {
            try {
                $uploaded[] = $this->saveFile($file, $request->user()->id);
            } catch (\Throwable $e) {
                $errors[] = $file->getClientOriginalName() . ': ' . $e->getMessage();
            }
        }

        if (empty($uploaded)) {
            return back()->with('error', __('No files were uploaded. :errors', ['errors' => implode(', ', $errors)]));
        }

        $message = __('Uploaded :count file(s).', ['count' => count($uploaded)]);

        if (!empty($errors)) {
            $message .= ' ' . __(':count failed.', ['count' => count($errors)]);
        }

        return redirect()->route('admin.media.index')->with('success', $message);
    }

    public function edit(Media $medium)
    {
        return view('admin.media.edit', ['media' => $medium]);
    }

    public function update(Request $request, Media $medium)
    {
        $data = $request->validate([
            'alt_text' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $medium->update($data);

        return back()->with('success', __('Media updated.'));
    }

    public function destroy(Media $medium)
    {
        $this->deleteFile($medium);

        return back()->with('success', __('Media deleted.'));
    }

    public function bulkDestroy(Request $request)
    {
        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:rj_media,id'],
        ]);

        $items = Media::whereIn('id', $data['ids'])->get();
        $count = 0;

        foreach ($items as $item) {
            $this->deleteFile($item);
            $count++;
        }

        return back()->with('success', __(':count item(s) deleted.', ['count' => $count]));
    }

    protected function saveFile($file, ?int $userId): Media
    {
        $original = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());
        $mime = $file->getMimeType();

        $folder = 'uploads/' . date('Y/m');
        $basename = Str::slug(pathinfo($original, PATHINFO_FILENAME));
        $basename = $basename !== '' ? $basename : 'file';
        $basename = substr($basename, 0, 80);

        $filename = $basename . '-' . Str::random(8) . '.' . $extension;

        $destPath = public_path($folder);

        if (!is_dir($destPath)) {
            mkdir($destPath, 0755, true);
        }

        $file->move($destPath, $filename);

        $fullPath = $destPath . '/' . $filename;
        $dimensions = @getimagesize($fullPath);

        return Media::create([
            'filename' => $filename,
            'original_name' => $original,
            'path' => $folder . '/' . $filename,
            'url' => asset($folder . '/' . $filename),
            'mime_type' => $mime,
            'size' => filesize($fullPath),
            'width' => $dimensions[0] ?? null,
            'height' => $dimensions[1] ?? null,
            'title' => pathinfo($original, PATHINFO_FILENAME),
            'uploaded_by' => $userId,
        ]);
    }

    protected function deleteFile(Media $media): void
    {
        $path = public_path($media->path);

        if (file_exists($path)) {
            @unlink($path);
        }

        $media->delete();
    }
}