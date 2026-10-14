<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'rj_activity_log';

    protected $fillable = [
        'log_name',
        'description',
        'subject_type',
        'subject_id',
        'causer_type',
        'causer_id',
        'properties',
        'event',
        'batch_uuid',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function subject()
    {
        return $this->morphTo();
    }

    public function causer()
    {
        return $this->morphTo();
    }

    public function scopeName($query, string $name)
    {
        return $query->where('log_name', $name);
    }

    public function scopeEvent($query, string $event)
    {
        return $query->where('event', $event);
    }

    public function scopeForSubject($query, $model)
    {
        return $query->where('subject_type', get_class($model))
            ->where('subject_id', $model->id);
    }

    public function scopeForCauser($query, $model)
    {
        return $query->where('causer_type', get_class($model))
            ->where('causer_id', $model->id);
    }

    public function scopeBatch($query, string $uuid)
    {
        return $query->where('batch_uuid', $uuid);
    }

    public function scopeRecent($query, int $limit = 50)
    {
        return $query->orderByDesc('created_at')->limit($limit);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('description', 'like', '%' . $term . '%')
              ->orWhere('event', 'like', '%' . $term . '%')
              ->orWhere('log_name', 'like', '%' . $term . '%');
        });
    }

    public function getProperty(string $key, $default = null)
    {
        if (!is_array($this->properties)) {
            return $default;
        }

        return $this->properties[$key] ?? $default;
    }

    public function causerName(): string
    {
        if ($this->causer && isset($this->causer->name)) {
            return (string) $this->causer->name;
        }

        return __('System');
    }

    public function subjectLabel(): string
    {
        if (!$this->subject) {
            return '';
        }

        foreach (['name', 'title'] as $field) {
            if (!empty($this->subject->{$field})) {
                return (string) $this->subject->{$field};
            }
        }

        return class_basename($this->subject_type) . ' #' . $this->subject_id;
    }

    public function ago(): string
    {
        return $this->created_at ? $this->created_at->diffForHumans() : '';
    }

    public function eventLabel(): string
    {
        return static::eventLabels()[$this->event] ?? ucfirst((string) $this->event);
    }

    public function logNameLabel(): string
    {
        return static::logNameLabels()[$this->log_name] ?? ucfirst((string) $this->log_name);
    }

    public static function eventLabels(): array
    {
        return [
            'created' => __('Created'),
            'updated' => __('Updated'),
            'deleted' => __('Deleted'),
            'restored' => __('Restored'),
            'login' => __('Login'),
            'logout' => __('Logout'),
            'login_failed' => __('Failed Login'),
            'published' => __('Published'),
            'unpublished' => __('Unpublished'),
            'activated' => __('Activated'),
            'deactivated' => __('Deactivated'),
            'uploaded' => __('Uploaded'),
            'exported' => __('Exported'),
            'imported' => __('Imported'),
        ];
    }

    public static function logNameLabels(): array
    {
        return [
            'auth' => __('Authentication'),
            'posts' => __('Posts'),
            'pages' => __('Pages'),
            'comments' => __('Comments'),
            'media' => __('Media'),
            'users' => __('Users'),
            'roles' => __('Roles'),
            'settings' => __('Settings'),
            'plugins' => __('Plugins'),
            'themes' => __('Themes'),
            'builder' => __('Builder'),
            'backup' => __('Backup'),
            'updates' => __('Updates'),
            'system' => __('System'),
        ];
    }

    public static function record(array $data): self
    {
        return static::create([
            'log_name' => $data['log_name'] ?? 'system',
            'description' => $data['description'] ?? '',
            'subject_type' => $data['subject_type'] ?? null,
            'subject_id' => $data['subject_id'] ?? null,
            'causer_type' => $data['causer_type'] ?? (auth()->check() ? get_class(auth()->user()) : null),
            'causer_id' => $data['causer_id'] ?? auth()->id(),
            'properties' => $data['properties'] ?? null,
            'event' => $data['event'] ?? 'created',
            'batch_uuid' => $data['batch_uuid'] ?? null,
            'ip_address' => $data['ip_address'] ?? (request() ? request()->ip() : null),
            'user_agent' => $data['user_agent'] ?? (request() ? (string) request()->userAgent() : null),
        ]);
    }

    public static function forModel(Model $model, string $event, ?string $logName = null, ?string $description = null): self
    {
        $basename = strtolower(class_basename($model));

        return static::record([
            'log_name' => $logName ?? $basename . 's',
            'description' => $description ?? __(ucfirst($event) . ' :name', ['name' => $basename]),
            'subject_type' => get_class($model),
            'subject_id' => $model->id,
            'event' => $event,
        ]);
    }

    public static function prune(int $days = 90): int
    {
        return static::where('created_at', '<', now()->subDays($days))->delete();
    }
}