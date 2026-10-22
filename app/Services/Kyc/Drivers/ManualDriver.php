<?php

namespace App\Services\Kyc\Drivers;

use App\Contracts\Kyc\KycDriverInterface;
use App\Models\KycVerification;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ManualDriver implements KycDriverInterface
{
    public function isEnabled(): bool
    {
        return true;
    }

    public function submit(User $user, array $data): bool
    {
        $paths = [];

        foreach (['document_front', 'document_back', 'selfie'] as $field) {
            if (isset($data[$field]) && $data[$field]->isValid()) {
                $filename = Str::random(20) . '.' . $data[$field]->getClientOriginalExtension();
                $paths[$field] = $data[$field]->storeAs('kyc/' . $user->id, $filename, 'public');
            }
        }

        KycVerification::create([
            'user_id' => $user->id,
            'driver' => 'manual',
            'status' => 'pending',
            'document_type' => $data['document_type'] ?? 'id_card',
            'document_front' => $paths['document_front'] ?? null,
            'document_back' => $paths['document_back'] ?? null,
            'selfie' => $paths['selfie'] ?? null,
        ]);

        $user->update(['kyc_status' => 'pending']);

        return true;
    }

    public function handleWebhook(array $payload): bool
    {
        return false;
    }
}
