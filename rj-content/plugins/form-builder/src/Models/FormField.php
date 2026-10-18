<?php

namespace RjPlugins\FormBuilder\Models;

use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{
    protected $table = 'rj_form_fields';

    protected $fillable = [
        'form_id',
        'type',
        'name',
        'label',
        'placeholder',
        'help_text',
        'is_required',
        'options',
        'sort_order',
        'settings',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'options' => 'array',
        'settings' => 'array',
        'sort_order' => 'integer',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
