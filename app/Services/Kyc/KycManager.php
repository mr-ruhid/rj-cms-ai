<?php

namespace App\Services\Kyc;

use App\Contracts\Kyc\KycDriverInterface;
use App\Models\User;
use App\Services\Kyc\Drivers\ManualDriver;

class KycManager implements KycDriverInterface
{
    protected ?KycDriverInterface $driver = null;

    public function __construct()
    {
        $driverName = rj_get_option('kyc_driver', 'manual');

        if ($driverName === 'api') {
            $this->driver = new \App\Services\Kyc\Drivers\ApiDriver();
        } elseif ($driverName === 'manual') {
            $this->driver = new ManualDriver();
        } else {
            $pluginDriver = rj_apply_filters('rj_kyc_driver', null, $driverName);

            if ($pluginDriver instanceof KycDriverInterface) {
                $this->driver = $pluginDriver;
            } else {
                $this->driver = new ManualDriver();
            }
        }
    }

    public function isEnabled(): bool
    {
        return $this->driver ? $this->driver->isEnabled() : false;
    }

    public function submit(User $user, array $data): bool
    {
        if (!$this->driver) {
            return false;
        }
        return $this->driver->submit($user, $data);
    }

    public function handleWebhook(array $payload): bool
    {
        if (!$this->driver) {
            return false;
        }
        return $this->driver->handleWebhook($payload);
    }
}
