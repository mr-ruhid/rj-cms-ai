<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Revision;
use Illuminate\Http\Request;

class RevisionController extends Controller
{
    public function index(Request $request)
    {
        $query = Revision::with('user')->orderByDesc('created_at');

        if ($type = $request->input('type')) {
            $query->where('revisionable_type', $type);
        }

        if ($userId = $request->input('user_id')) {
            $query->where('user_id', (int) $userId);
        }

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('note', 'like', '%' . $search . '%');
            });
        }

        $revisions = $query->paginate(30)->withQueryString();

        $types = Revision::query()
            ->select('revisionable_type')
            ->distinct()
            ->pluck('revisionable_type')
            ->filter()
            ->map(fn($t) => [
                'value' => $t,
                'label' => class_basename($t),
            ])
            ->values();

        $counts = [
            'all' => Revision::count(),
            'today' => Revision::whereDate('created_at', today())->count(),
            'week' => Revision::where('created_at', '>=', now()->subWeek())->count(),
        ];

        return view('admin.revisions.index', compact('revisions', 'types', 'counts'));
    }

    public function show(Revision $revision)
    {
        $revision->load(['user', 'revisionable']);

        $previous = Revision::query()
            ->where('revisionable_type', $revision->revisionable_type)
            ->where('revisionable_id', $revision->revisionable_id)
            ->where('version', '<', $revision->version)
            ->orderByDesc('version')
            ->first();

        return view('admin.revisions.show', compact('revision', 'previous'));
    }

    public function restore(Request $request, Revision $revision)
    {
        $model = $revision->revisionable;

        if (!$model) {
            $message = __('Original item no longer exists.');

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 404);
            }

            return back()->with('error', $message);
        }

        if (!method_exists($model, 'restoreFromRevision')) {
            $message = __('This item does not support revision restoration.');

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        $backup = $this->backupCurrentState($model);

        $model->restoreFromRevision($revision->content);

        $model->save();

        Revision::createFor($model, $backup, [
            'note' => __('Before restoring version :v', ['v' => $revision->version]),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', __('Revision restored.'));
    }

    public function destroy(Request $request, Revision $revision)
    {
        $revision->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', __('Revision deleted.'));
    }

    public function prune(Request $request)
    {
        $validated = $request->validate([
            'days' => 'nullable|integer|min:1|max:365',
        ]);

        $days = $validated['days'] ?? 90;

        $count = Revision::where('created_at', '<', now()->subDays($days))->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'count' => $count,
                'message' => __(':n revision(s) deleted.', ['n' => $count]),
            ]);
        }

        return back()->with('success', __(':n revision(s) deleted.', ['n' => $count]));
    }

    protected function backupCurrentState($model): array
    {
        $fields = ['content', 'settings', 'conditions', 'title', 'name'];

        $content = [];

        foreach ($fields as $field) {
            if (isset($model->{$field})) {
                $content[$field] = $model->{$field};
            }
        }

        return $content;
    }
}