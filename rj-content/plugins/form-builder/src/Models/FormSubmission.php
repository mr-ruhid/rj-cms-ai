<?php

namespace RjPlugins\FormBuilder\Models;

use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
    protected $table = 'rj_form_submissions';

    protected $fillable = [
        'form_id',
        'data',
        'ip_address',
        'user_agent',
        'status',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id');
    }
}
