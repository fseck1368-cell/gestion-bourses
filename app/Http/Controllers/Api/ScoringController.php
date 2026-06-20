<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dossier;
use App\Services\ScoringService;
use Illuminate\Http\Request;

class ScoringController extends Controller
{
    public function calculer(Dossier $dossier)
    {
        $scoring = app(ScoringService::class)->calculerScore($dossier);
        return response()->json($scoring);
    }

    public function classement(Request $request)
    {
        $query = Dossier::whereNotNull('score_global')
            ->with('etudiant')
            ->orderByDesc('score_global');

        if ($request->has('campagne_id')) {
            $query->where('campagne_id', $request->campagne_id);
        }

        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        return response()->json($query->paginate(20));
    }
}
