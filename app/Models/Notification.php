<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'rj_notifications';

    protected $fillable = [
        'type',
        'title',
        'message',
        'icon',
        'color',
        'action_url',
        'action_label',
        'user_id',
        'notifiable_type',
        'notifiable_id',
        'read_at',
        'meta',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'meta' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeFor($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeRecent($query, int $limit = 20)
    {
        return $query->orderByDesc('created_at')->limit($limit);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function markAsRead(): void
    {
        if ($this->read_at === null) {
            $this->update(['read_at' => now()]);
        }
    }

    public function markAsUnread(): void
    {
        $this->update(['read_at' => null]);
    }

    public function ago(): string
    {
        return $this->created_at ? $this->created_at->diffForHumans() : '';
    }

    public static function send(User $user, array $data): self
    {
        return static::create([
            'user_id' => $user->id,
            'type' => $data['type'] ?? 'general',
            'title' => $data['title'] ?? '',
            'message' => $data['message'] ?? null,
            'icon' => $data['icon'] ?? null,
            'color' => $data['color'] ?? '#7c3aed',
            'action_url' => $data['action_url'] ?? null,
            'action_label' => $data['action_label'] ?? null,
            'notifiable_type' => $data['notifiable_type'] ?? null,
            'notifiable_id' => $data['notifiable_id'] ?? null,
            'meta' => $data['meta'] ?? null,
        ]);
    }

    public static function sendToAdmins(array $data): int
    {
        $count = 0;

        $admins = User::query()
            ->where(function ($q) {
                $q->where('role', 'admin')
                  ->orWhere('role', 'super-admin');
            })
            ->get();

        foreach ($admins as $admin) {
            static::send($admin, $data);
            $count++;
        }

        return $count;
    }

    public static function markAllRead(User $user): int
    {
        return static::for($user)->unread()->update(['read_at' => now()]);
    }

    public static function unreadCount(User $user): int
    {
        return static::for($user)->unread()->count();
    }

    public static function types(): array
    {
        return [
            'general' => ['label' => __('General'), 'color' => '#7c3aed', 'icon' => 'bell'],
            'success' => ['label' => __('Success'), 'color' => '#10b981', 'icon' => 'check'],
            'warning' => ['label' => __('Warning'), 'color' => '#f59e0b', 'icon' => 'alert-triangle'],
            'error' => ['label' => __('Error'), 'color' => '#ef4444', 'icon' => 'alert-circle'],
            'info' => ['label' => __('Info'), 'color' => '#3b82f6', 'icon' => 'info'],
            'comment' => ['label' => __('New Comment'), 'color' => '#8b5cf6', 'icon' => 'message'],
            'form' => ['label' => __('Form Submission'), 'color' => '#ec4899', 'icon' => 'inbox'],
            'system' => ['label' => __('System'), 'color' => '#6b7280', 'icon' => 'settings'],
        ];
    }

    public function typeLabel(): string
    {
        return static::types()[$this->type]['label'] ?? $this->type;
    }
}