<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    protected $table = 'rj_forms';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'settings',
        'notifications',
        'actions',
        'status',
        'author_id',
    ];

    protected $casts = [
        'settings' => 'array',
        'notifications' => 'array',
        'actions' => 'array',
    ];

    public function fields()
    {
        return $this->hasMany(FormField::class, 'form_id')->orderBy('sort_order');
    }

    public function submissions()
    {
        return $this->hasMany(FormSubmission::class, 'form_id')->orderByDesc('created_at');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function settings(): array
    {
        $defaults = [
            'submit_label' => __('Submit'),
            'success_message' => __('Thank you! Your submission has been received.'),
            'error_message' => __('Something went wrong. Please try again.'),
            'redirect_url' => '',
            'reset_after_submit' => true,
            'store_submissions' => true,
            'enable_captcha' => false,
        ];

        if (!is_array($this->settings)) {
            return $defaults;
        }

        return array_merge($defaults, $this->settings);
    }

    public function notifications(): array
    {
        $defaults = [
            'admin_email' => rj_get_option('admin_email', config('mail.from.address', '')),
            'admin_subject' => __('New form submission'),
            'user_email_field' => '',
            'user_subject' => __('Thank you for your message'),
            'user_body' => '',
        ];

        if (!is_array($this->notifications)) {
            return $defaults;
        }

        return array_merge($defaults, $this->notifications);
    }

    public function actions(): array
    {
        $defaults = [
            'webhook_url' => '',
            'webhook_method' => 'POST',
        ];

        if (!is_array($this->actions)) {
            return $defaults;
        }

        return array_merge($defaults, $this->actions);
    }

    public function newSubmissionsCount(): int
    {
        return $this->submissions()->where('status', 'new')->count();
    }

    public function typeLabel(): string
    {
        return __('Form');
    }
}