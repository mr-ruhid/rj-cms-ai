<?php

namespace App\Services\Kyc\Drivers;

use App\Contracts\Kyc\KycDriverInterface;
use App\Models\KycVerification;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiDriver implements KycDriverInterface
{
    public function isEnabled(): bool
    {
        return !empty(rj_get_option('kyc_api_key')) && !empty(rj_get_option('kyc_api_secret'));
    }

    public function submit(User $user, array $data): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . rj_get_option('kyc_api_key'),
            ])->post(rj_get_option('kyc_api_endpoint', 'https://api.example.com/kyc'), [
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                KycVerification::create([
                    'user_id' => $user->id,
                    'driver' => 'api',
                    'status' => 'pending',
                    'metadata' => $responseData,
                ]);

                $user->update(['kyc_status' => 'pending']);

                return true;
            }

            Log::error('KYC API Submit Failed', ['response' => $response->json()]);
            return false;
        } catch (\Throwable $e) {
            Log::error('KYC API Exception: ' . $e->getMessage());
            return false;
        }
    }

    public function handleWebhook(array $payload): bool
    {
        if (isset($payload['user_id']) && isset($payload['status'])) {
            $user = User::find($payload['user_id']);

            if ($user) {
                $verification = KycVerification::where('user_id', $user->id)->latest()->first();

                if ($verification) {
                    $verification->update(['status' => $payload['status']]);
                    $user->update([
                        'kyc_status' => $payload['status'],
                        'kyc_verified_at' => $payload['status'] === 'approved' ? now() : null
                    ]);
                    return true;
                }
            }
        }

        return false;
    }
}
