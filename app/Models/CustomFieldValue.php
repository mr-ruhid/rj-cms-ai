<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomFieldValue extends Model
{
    protected $table = 'rj_custom_field_values';

    protected $fillable = [
        'field_id',
        'valuable_type',
        'valuable_id',
        'value',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function field()
    {
        return $this->belongsTo(CustomField::class, 'field_id');
    }

    public function valuable()
    {
        return $this->morphTo();
    }

    public function scopeFor($query, $model)
    {
        return $query->where('valuable_type', get_class($model))
            ->where('valuable_id', $model->id);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function decodedValue()
    {
        if ($this->value === null) {
            return null;
        }

        $decoded = json_decode($this->value, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        return $this->value;
    }

    public function typedValue()
    {
        $raw = $this->decodedValue();

        if (!$this->field) {
            return $raw;
        }

        return match ($this->field->type) {
            'true_false' => (bool) $raw,
            'number', 'range' => is_numeric($raw) ? (int) $raw : 0,
            'checkbox', 'gallery', 'relationship', 'repeater', 'flexible_content', 'group' => is_array($raw) ? $raw : [],
            default => is_scalar($raw) || $raw === null ? (string) ($raw ?? '') : $raw,
        };
    }

    public static function setValue(CustomField $field, Model $model, $value): self
    {
        $encoded = is_scalar($value) || $value === null
            ? (string) $value
            : json_encode($value, JSON_UNESCAPED_UNICODE);

        return static::updateOrCreate(
            [
                'field_id' => $field->id,
                'valuable_type' => get_class($model),
                'valuable_id' => $model->id,
            ],
            [
                'value' => $encoded,
            ]
        );
    }

    public static function getValue(CustomField $field, Model $model, $default = null)
    {
        $row = static::query()
            ->where('field_id', $field->id)
            ->where('valuable_type', get_class($model))
            ->where('valuable_id', $model->id)
            ->first();

        if (!$row) {
            return $default;
        }

        return $row->typedValue();
    }

    public static function deleteValue(CustomField $field, Model $model): void
    {
        static::query()
            ->where('field_id', $field->id)
            ->where('valuable_type', get_class($model))
            ->where('valuable_id', $model->id)
            ->delete();
    }
}