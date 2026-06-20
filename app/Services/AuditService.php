<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class AuditService
{
    public function logAcces(int $userId, string $action, string $entite, int $entiteId, ?string $details = null): void
    {
        DB::table('audit_logs')->insert([
            'user_id' => $userId,
            'action' => $action,
            'entite' => $entite,
            'entite_id' => $entiteId,
            'details' => $details,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }

    public function historiqueAcces(string $entite, int $entiteId, int $limit = 20): array
    {
        return DB::table('audit_logs')
            ->where('entite', $entite)
            ->where('entite_id', $entiteId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function activiteUtilisateur(int $userId, int $limit = 50): array
    {
        return DB::table('audit_logs')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
