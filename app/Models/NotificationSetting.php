<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    protected $table = 'rj_notification_settings';

    protected $fillable = [
        'user_id',
        'event',
        'channel',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeFor($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeEvent($query, string $event)
    {
        return $query->where('event', $event);
    }

    public function scopeChannel($query, string $channel)
    {
        return $query->where('channel', $channel);
    }

    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    public static function events(): array
    {
        return [
            'new_comment' => __('New Comment'),
            'comment_approved' => __('Comment Approved'),
            'comment_reply' => __('Comment Reply'),
            'new_submission' => __('New Form Submission'),
            'new_user' => __('New User Registration'),
            'user_login' => __('User Login'),
            'password_changed' => __('Password Changed'),
            'post_published' => __('Post Published'),
            'post_scheduled' => __('Post Scheduled'),
            'backup_completed' => __('Backup Completed'),
            'backup_failed' => __('Backup Failed'),
            'update_available' => __('Update Available'),
            'system_error' => __('System Error'),
            'login_failed' => __('Login Failed'),
        ];
    }

    public static function channels(): array
    {
        return [
            'database' => ['label' => __('In-app'), 'icon' => 'bell'],
            'email' => ['label' => __('Email'), 'icon' => 'mail'],
            'browser' => ['label' => __('Browser Push'), 'icon' => 'globe'],
        ];
    }

    public static function defaults(): array
    {
        return [
            'new_comment' => ['database' => true, 'email' => true, 'browser' => false],
            'comment_approved' => ['database' => true, 'email' => false, 'browser' => false],
            'comment_reply' => ['database' => true, 'email' => true, 'browser' => false],
            'new_submission' => ['database' => true, 'email' => true, 'browser' => false],
            'new_user' => ['database' => true, 'email' => true, 'browser' => false],
            'user_login' => ['database' => false, 'email' => false, 'browser' => false],
            'password_changed' => ['database' => true, 'email' => true, 'browser' => false],
            'post_published' => ['database' => true, 'email' => false, 'browser' => false],
            'post_scheduled' => ['database' => true, 'email' => false, 'browser' => false],
            'backup_completed' => ['database' => true, 'email' => true, 'browser' => false],
            'backup_failed' => ['database' => true, 'email' => true, 'browser' => true],
            'update_available' => ['database' => true, 'email' => true, 'browser' => false],
            'system_error' => ['database' => true, 'email' => true, 'browser' => true],
            'login_failed' => ['database' => true, 'email' => true, 'browser' => false],
        ];
    }

    public static function isEnabled(User $user, string $event, string $channel): bool
    {
        $row = static::query()
            ->where('user_id', $user->id)
            ->where('event', $event)
            ->where('channel', $channel)
            ->first();

        if ($row) {
            return $row->enabled;
        }

        $defaults = static::defaults();

        return $defaults[$event][$channel] ?? false;
    }

    public static function setEnabled(User $user, string $event, string $channel, bool $enabled): self
    {
        return static::updateOrCreate(
            [
                'user_id' => $user->id,
                'event' => $event,
                'channel' => $channel,
            ],
            [
                'enabled' => $enabled,
            ]
        );
    }

    public static function forUser(User $user): array
    {
        $existing = static::for($user)->get()->keyBy(fn($s) => $s->event . ':' . $s->channel);

        $out = [];

        foreach (static::events() as $event => $eventLabel) {
            $out[$event] = [
                'label' => $eventLabel,
                'channels' => [],
            ];

            foreach (static::channels() as $channel => $channelData) {
                $key = $event . ':' . $channel;
                $row = $existing->get($key);

                $out[$event]['channels'][$channel] = [
                    'label' => $channelData['label'],
                    'icon' => $channelData['icon'],
                    'enabled' => $row ? $row->enabled : (static::defaults()[$event][$channel] ?? false),
                ];
            }
        }

        return $out;
    }

    public static function seedDefaults(User $user): void
    {
        foreach (static::defaults() as $event => $channels) {
            foreach ($channels as $channel => $enabled) {
                static::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'event' => $event,
                        'channel' => $channel,
                    ],
                    [
                        'enabled' => $enabled,
                    ]
                );
            }
        }
    }
}