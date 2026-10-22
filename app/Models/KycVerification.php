<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KycVerification extends Model
{
    protected $table = 'rj_kyc_verifications';

    protected $fillable = [
        'user_id',
        'driver',
        'status',
        'document_type',
        'document_front',
        'document_back',
        'selfie',
        'rejection_reason',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
