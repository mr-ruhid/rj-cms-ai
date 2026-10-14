<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Revision extends Model
{
    protected $table = 'rj_revisions';

    protected $fillable = [
        'revisionable_type',
        'revisionable_id',
        'user_id',
        'content',
        'title',
        'note',
        'version',
    ];

    protected $casts = [
        'content' => 'array',
        'version' => 'integer',
    ];

    public function revisionable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeFor($query, $model)
    {
        return $query->where('revisionable_type', get_class($model))
            ->where('revisionable_id', $model->id);
    }

    public function scopeLatest($query)
    {
        return $query->orderByDesc('version');
    }

    public static function createFor(Model $model, array $content, array $options = []): Revision
    {
        $nextVersion = static::query()
            ->where('revisionable_type', get_class($model))
            ->where('revisionable_id', $model->id)
            ->max('version') + 1;

        return static::create([
            'revisionable_type' => get_class($model),
            'revisionable_id' => $model->id,
            'user_id' => auth()->id(),
            'content' => $content,
            'title' => $options['title'] ?? ($model->name ?? $model->title ?? ''),
            'note' => $options['note'] ?? null,
            'version' => $nextVersion,
        ]);
    }

    public static function pruneFor(Model $model, int $keep = 20): int
    {
        $ids = static::query()
            ->where('revisionable_type', get_class($model))
            ->where('revisionable_id', $model->id)
            ->orderByDesc('version')
            ->skip($keep)
            ->take(PHP_INT_MAX)
            ->pluck('id');

        if ($ids->isEmpty()) {
            return 0;
        }

        return static::whereIn('id', $ids)->delete();
    }

    public function ago(): string
    {
        return $this->created_at ? $this->created_at->diffForHumans() : '';
    }
}