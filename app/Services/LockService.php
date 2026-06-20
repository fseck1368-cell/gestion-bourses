<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class LockService
{
    private const LOCK_DURATION = 300; // 5 minutes

    public function verrouiller(int $dossierId, int $userId): bool
    {
        $key = 'dossier_lock_' . $dossierId;
        $lock = Cache::get($key);

        if ($lock && $lock['user_id'] !== $userId) {
            return false; // Already locked by someone else
        }

        Cache::put($key, [
            'user_id' => $userId,
            'locked_at' => now()->toDateTimeString(),
        ], self::LOCK_DURATION);

        return true;
    }

    public function deverrouiller(int $dossierId, int $userId): void
    {
        $key = 'dossier_lock_' . $dossierId;
        $lock = Cache::get($key);

        if ($lock && $lock['user_id'] === $userId) {
            Cache::forget($key);
        }
    }

    public function estVerrouille(int $dossierId): ?array
    {
        return Cache::get('dossier_lock_' . $dossierId);
    }

    public function verrouillerPar(int $dossierId): ?int
    {
        $lock = $this->estVerrouille($dossierId);
        return $lock ? $lock['user_id'] : null;
    }
}
