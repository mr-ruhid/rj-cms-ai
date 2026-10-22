<?php

namespace App\Contracts\Kyc;

use App\Models\User;

interface KycDriverInterface
{
    public function isEnabled(): bool;

    public function submit(User $user, array $data): bool;

    public function handleWebhook(array $payload): bool;
}
