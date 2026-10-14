<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{
    protected $table = 'rj_form_fields';

    protected $fillable = [
        'form_id',
        'name',
        'label',
        'type',
        'placeholder',
        'help_text',
        'default_value',
        'options',
        'validation',
        'required',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
        'validation' => 'array',
        'required' => 'boolean',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function options(): array
    {
        if (!is_array($this->options)) {
            return [];
        }

        return $this->options;
    }

    public function validation(): array
    {
        if (!is_array($this->validation)) {
            return [];
        }

        return $this->validation;
    }

    public function validationRules(): array
    {
        $rules = [];

        if ($this->required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        $config = $this->validation();
        $type = $this->type;

        switch ($type) {
            case 'email':
                $rules[] = 'email';
                break;
            case 'url':
                $rules[] = 'url';
                break;
            case 'number':
            case 'range':
                $rules[] = 'numeric';
                break;
            case 'date':
            case 'time':
                $rules[] = 'string';
                break;
            case 'file':
                $rules[] = 'file';
                break;
            default:
                $rules[] = 'string';
        }

        if (!empty($config['min'])) {
            if (in_array($type, ['number', 'range'], true)) {
                $rules[] = 'min:' . (int) $config['min'];
            } else {
                $rules[] = 'min:' . (int) $config['min'];
            }
        }

        if (!empty($config['max'])) {
            if (in_array($type, ['number', 'range'], true)) {
                $rules[] = 'max:' . (int) $config['max'];
            } else {
                $rules[] = 'max:' . (int) $config['max'];
            }
        }

        if (!empty($config['pattern'])) {
            $rules[] = 'regex:/' . $config['pattern'] . '/';
        }

        if ($type === 'select' || $type === 'radio') {
            $values = array_column($this->options(), 'value');
            if (!empty($values)) {
                $rules[] = 'in:' . implode(',', $values);
            }
        }

        return $rules;
    }

    public static function types(): array
    {
        return [
            'text' => ['label' => __('Text'), 'icon' => 'type'],
            'email' => ['label' => __('Email'), 'icon' => 'mail'],
            'tel' => ['label' => __('Phone'), 'icon' => 'phone'],
            'url' => ['label' => __('URL'), 'icon' => 'link'],
            'number' => ['label' => __('Number'), 'icon' => 'hash'],
            'textarea' => ['label' => __('Textarea'), 'icon' => 'align-left'],
            'select' => ['label' => __('Select'), 'icon' => 'chevron-down'],
            'radio' => ['label' => __('Radio'), 'icon' => 'circle'],
            'checkbox' => ['label' => __('Checkbox'), 'icon' => 'check-square'],
            'date' => ['label' => __('Date'), 'icon' => 'calendar'],
            'time' => ['label' => __('Time'), 'icon' => 'clock'],
            'file' => ['label' => __('File'), 'icon' => 'upload'],
            'hidden' => ['label' => __('Hidden'), 'icon' => 'eye-off'],
        ];
    }

    public function typeLabel(): string
    {
        return static::types()[$this->type]['label'] ?? $this->type;
    }
}