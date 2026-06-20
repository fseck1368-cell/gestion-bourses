<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\LockService;
use Illuminate\Http\Request;

class LockController extends Controller
{
    public function verrouiller(Request $request, int $dossierId)
    {
        $lockService = app(LockService::class);
        $locked = $lockService->verrouiller($dossierId, $request->user()->id);

        if (!$locked) {
            $lockInfo = $lockService->estVerrouille($dossierId);
            $lockedBy = User::find($lockInfo['user_id']);

            return response()->json([
                'locked' => false,
                'message' => 'Dossier verrouillé par ' . $lockedBy->prenom . ' ' . $lockedBy->nom,
                'locked_by' => $lockedBy,
                'locked_at' => $lockInfo['locked_at'],
            ], 409);
        }

        return response()->json([
            'locked' => true,
            'message' => 'Dossier verrouillé avec succès.',
        ]);
    }

    public function deverrouiller(Request $request, int $dossierId)
    {
        app(LockService::class)->deverrouiller($dossierId, $request->user()->id);

        return response()->json(['message' => 'Dossier déverrouillé.']);
    }

    public function statut(int $dossierId)
    {
        $lockInfo = app(LockService::class)->estVerrouille($dossierId);

        if (!$lockInfo) {
            return response()->json(['verrouille' => false]);
        }

        $lockedBy = User::find($lockInfo['user_id']);

        return response()->json([
            'verrouille' => true,
            'locked_by' => $lockedBy,
            'locked_at' => $lockInfo['locked_at'],
        ]);
    }
}
