<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'kyc_status',
        'kyc_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_secret' => 'encrypted',
            'two_factor_recovery_codes' => 'encrypted:array',
            'two_factor_confirmed_at' => 'datetime',
            'kyc_verified_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function kycVerifications()
    {
        return $this->hasMany(KycVerification::class);
    }

    public function latestKyc()
    {
        return $this->hasOne(KycVerification::class)->latestOfMany();
    }

    public function hasEnabledTwoFactorAuthentication(): bool
    {
        return !is_null($this->two_factor_secret) && !is_null($this->two_factor_confirmed_at);
    }

    public function generateTwoFactorSecret(): void
    {
        if ($this->two_factor_secret) {
            return;
        }

        $this->forceFill([
            'two_factor_secret' => app(Google2FA::class)->generateSecretKey(),
        ])->save();
    }

    public function generateRecoveryCodes(): void
    {
        if (! $this->hasEnabledTwoFactorAuthentication()) {
            return;
        }

        $this->forceFill([
            'two_factor_recovery_codes' => collect(range(1, 8))->map(function () {
                return Str::random(10) . '-' . Str::random(10);
            })->all(),
        ])->save();
    }

    public function confirmTwoFactorAuthentication(): void
    {
        $this->forceFill([
            'two_factor_confirmed_at' => now(),
        ])->save();

        $this->generateRecoveryCodes();
    }

    public function disableTwoFactorAuthentication(): void
    {
        $this->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();
    }
}
