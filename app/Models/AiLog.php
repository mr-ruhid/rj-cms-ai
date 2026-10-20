<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiLog extends Model
{
    protected $table = 'rj_ai_logs';

    protected $fillable = [
        'user_id',
        'model',
        'task',
        'prompt_tokens',
        'completion_tokens',
        'total_tokens',
        'cost',
        'status',
        'error_message',
    ];

    protected $casts = [
        'cost' => 'decimal:6',
        'prompt_tokens' => 'integer',
        'completion_tokens' => 'integer',
        'total_tokens' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}