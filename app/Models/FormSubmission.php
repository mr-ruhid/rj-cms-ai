<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
    protected $table = 'rj_form_submissions';

    protected $fillable = [
        'form_id',
        'data',
        'status',
        'ip_address',
        'user_agent',
        'referrer',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeRead($query)
    {
        return $query->where('status', 'read');
    }

    public function scopeSpam($query)
    {
        return $query->where('status', 'spam');
    }

    public function getField(string $name, $default = null)
    {
        if (!is_array($this->data)) {
            return $default;
        }

        return $this->data[$name] ?? $default;
    }

    public function fieldValue(string $name): string
    {
        $value = $this->getField($name);

        if (is_array($value)) {
            return implode(', ', $value);
        }

        return (string) $value;
    }

    public static function statuses(): array
    {
        return [
            'new' => ['label' => __('New'), 'color' => '#7c3aed'],
            'read' => ['label' => __('Read'), 'color' => '#3b82f6'],
            'replied' => ['label' => __('Replied'), 'color' => '#10b981'],
            'spam' => ['label' => __('Spam'), 'color' => '#ef4444'],
            'archived' => ['label' => __('Archived'), 'color' => '#6b7280'],
        ];
    }

    public function statusLabel(): string
    {
        return static::statuses()[$this->status]['label'] ?? $this->status;
    }
}