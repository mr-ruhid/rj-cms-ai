<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentService
{
    public function create(Post $post, array $data, Request $request): Comment
    {
        $parentId = isset($data['parent_id']) ? (int) $data['parent_id'] : null;

        if ($parentId && !Comment::where('id', $parentId)->where('post_id', $post->id)->exists()) {
            $parentId = null;
        }

        $status = $this->resolveStatus();

        return Comment::create([
            'post_id' => $post->id,
            'parent_id' => $parentId,
            'user_id' => auth()->id(),
            'author_name' => $data['author_name'] ?? (auth()->user()->name ?? 'Guest'),
            'author_email' => $data['author_email'] ?? (auth()->user()->email ?? ''),
            'author_url' => $data['author_url'] ?? null,
            'author_ip' => $request->ip(),
            'content' => trim((string) ($data['content'] ?? '')),
            'status' => $status,
            'meta' => [
                'user_agent' => (string) $request->userAgent(),
                'referrer' => (string) $request->headers->get('referer', ''),
            ],
        ]);
    }

    public function approve(Comment $comment): void
    {
        $comment->update(['status' => 'approved']);
    }

    public function unapprove(Comment $comment): void
    {
        $comment->update(['status' => 'pending']);
    }

    public function markAsSpam(Comment $comment): void
    {
        $comment->update(['status' => 'spam']);
    }

    public function trash(Comment $comment): void
    {
        $comment->update(['status' => 'trash']);
    }

    public function restore(Comment $comment): void
    {
        $comment->update(['status' => 'pending']);
    }

    public function delete(Comment $comment): void
    {
        $comment->children()->delete();
        $comment->delete();
    }

    public function bulkAction(array $ids, string $action): int
    {
        $query = Comment::whereIn('id', $ids);

        return match ($action) {
            'approve' => $query->update(['status' => 'approved']),
            'unapprove' => $query->update(['status' => 'pending']),
            'spam' => $query->update(['status' => 'spam']),
            'trash' => $query->update(['status' => 'trash']),
            'delete' => Comment::whereIn('id', $ids)->delete(),
            default => 0,
        };
    }

    public function isSpam(string $content, string $authorName, string $authorEmail): bool
    {
        $spamPatterns = [
            '/\b(viagra|cialis|casino|porn|xxx)\b/i',
            '/\b(bit\.ly|tinyurl|t\.co|goo\.gl)\b/i',
            '/https?:\/\/[^\s]+/i',
            '/\b(seo services|rank higher|backlinks for sale)\b/i',
        ];

        foreach ($spamPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        $linkCount = preg_match_all('/<a\s|https?:\/\//i', $content);

        if ($linkCount > 2) {
            return true;
        }

        if (strlen($content) < 3) {
            return true;
        }

        return false;
    }

    public function buildTree($comments, ?int $parentId = null): array
    {
        $tree = [];

        foreach ($comments as $comment) {
            if ($comment->parent_id === $parentId) {
                $children = $this->buildTree($comments, $comment->id);

                $tree[] = [
                    'comment' => $comment,
                    'children' => $children,
                ];
            }
        }

        return $tree;
    }

    protected function resolveStatus(): string
    {
        if (auth()->check() && auth()->user()->can('comments.moderate')) {
            return 'approved';
        }

        $moderation = rj_get_option('comment_moderation', true);

        return $moderation ? 'pending' : 'approved';
    }
}