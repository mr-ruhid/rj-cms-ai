<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeTranslation extends Model
{
    protected $table = 'rj_knowledge_translations';

    protected $fillable = [
        'knowledge_id',
        'locale',
        'title',
        'content',
    ];

    public function knowledge()
    {
        return $this->belongsTo(Knowledge::class, 'knowledge_id');
    }
}