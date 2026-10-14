<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TermTranslation extends Model
{
    protected $table = 'rj_term_translations';

    protected $fillable = [
        'term_id',
        'locale',
        'name',
        'description',
        'seo_title',
        'seo_description',
    ];

    public function term()
    {
        return $this->belongsTo(Term::class, 'term_id');
    }

    public function scopeLocale($query, string $locale)
    {
        return $query->where('locale', $locale);
    }
}