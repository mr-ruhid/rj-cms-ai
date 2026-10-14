<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'rj_comments';

    protected $fillable = [
        'post_id',
        'parent_id',
        'user_id',
        'author_name',
        'author_email',
        'author_url',
        'author_ip',
        'content',
        'status',
        'like_count',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'like_count' => 'integer',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Comment::class, 'parent_id')->orderBy('created_at');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->orderBy('created_at');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSpam($query)
    {
        return $query->where('status', 'spam');
    }

    public function scopeTrash($query)
    {
        return $query->where('status', 'trash');
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isSpam(): bool
    {
        return $this->status === 'spam';
    }

    public function isReply(): bool
    {
        return !is_null($this->parent_id);
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->user && $this->user->avatar) {
            return $this->user->avatar;
        }

        $hash = md5(strtolower(trim((string) $this->author_email)));

        return 'https://www.gravatar.com/avatar/' . $hash . '?d=mp&s=80';
    }

    public static function statuses(): array
    {
        return [
            'approved' => ['label' => __('Approved'), 'color' => '#10b981'],
            'pending' => ['label' => __('Pending'), 'color' => '#f59e0b'],
            'spam' => ['label' => __('Spam'), 'color' => '#ef4444'],
            'trash' => ['label' => __('Trash'), 'color' => '#6b7280'],
        ];
    }

    public function statusLabel(): string
    {
        return static::statuses()[$this->status]['label'] ?? $this->status;
    }
}