<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Services\CommentService;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    protected CommentService $service;

    public function __construct(CommentService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $defaultStatus = $request->route() ? ($request->route()->defaults['status_filter'] ?? null) : null;

        $query = Comment::with(['post', 'user'])->orderByDesc('created_at');

        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        } elseif ($defaultStatus) {
            $query->where('status', $defaultStatus);
        } else {
            $query->where('status', '!=', 'trash');
        }

        if ($postId = $request->input('post_id')) {
            $query->where('post_id', (int) $postId);
        }

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('content', 'like', '%' . $search . '%')
                  ->orWhere('author_name', 'like', '%' . $search . '%')
                  ->orWhere('author_email', 'like', '%' . $search . '%');
            });
        }

        $comments = $query->paginate(20)->withQueryString();

        $counts = [
            'all' => Comment::count(),
            'pending' => Comment::where('status', 'pending')->count(),
            'approved' => Comment::where('status', 'approved')->count(),
            'spam' => Comment::where('status', 'spam')->count(),
            'trash' => Comment::where('status', 'trash')->count(),
        ];

        return view('admin.comments.index', compact('comments', 'counts'));
    }

    public function show(Comment $comment)
    {
        $comment->load(['post', 'user', 'parent', 'children']);

        return view('admin.comments.show', compact('comment'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'required|integer|exists:rj_posts,id',
            'parent_id' => 'nullable|integer|exists:rj_comments,id',
            'content' => 'required|string|max:5000',
            'author_name' => 'nullable|string|max:255',
            'author_email' => 'nullable|email|max:255',
            'status' => 'nullable|in:pending,approved,spam',
        ]);

        $post = Post::findOrFail($validated['post_id']);

        $comment = Comment::create([
            'post_id' => $post->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'user_id' => auth()->id(),
            'author_name' => $validated['author_name'] ?? auth()->user()->name,
            'author_email' => $validated['author_email'] ?? auth()->user()->email,
            'author_ip' => $request->ip(),
            'content' => $validated['content'],
            'status' => $validated['status'] ?? 'approved',
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'comment' => $comment]);
        }

        return back()->with('success', __('Comment added.'));
    }

    public function update(Request $request, Comment $comment)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:5000',
            'author_name' => 'required|string|max:255',
            'author_email' => 'required|email|max:255',
            'author_url' => 'nullable|url|max:255',
        ]);

        $comment->update($validated);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'comment' => $comment]);
        }

        return back()->with('success', __('Comment updated.'));
    }

    public function approve(Comment $comment)
    {
        $this->service->approve($comment);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'status' => 'approved']);
        }

        return back()->with('success', __('Comment approved.'));
    }

    public function unapprove(Comment $comment)
    {
        $this->service->unapprove($comment);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'status' => 'pending']);
        }

        return back()->with('success', __('Comment unapproved.'));
    }

    public function spam(Comment $comment)
    {
        $this->service->markAsSpam($comment);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'status' => 'spam']);
        }

        return back()->with('success', __('Comment marked as spam.'));
    }

    public function trash(Comment $comment)
    {
        $this->service->trash($comment);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'status' => 'trash']);
        }

        return back()->with('success', __('Comment moved to trash.'));
    }

    public function restore(Comment $comment)
    {
        $this->service->restore($comment);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'status' => 'pending']);
        }

        return back()->with('success', __('Comment restored.'));
    }

    public function destroy(Comment $comment)
    {
        $this->service->delete($comment);

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', __('Comment deleted.'));
    }

    public function bulk(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:rj_comments,id',
            'action' => 'required|in:approve,unapprove,spam,trash,delete',
        ]);

        $count = $this->service->bulkAction($validated['ids'], $validated['action']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'count' => $count,
                'message' => __(':n comment(s) updated.', ['n' => $count]),
            ]);
        }

        return back()->with('success', __(':n comment(s) updated.', ['n' => $count]));
    }
}