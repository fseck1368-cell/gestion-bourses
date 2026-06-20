<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dossier;
use App\Models\Evaluation;
use App\Models\CritereEligibilite;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    public function index(Dossier $dossier)
    {
        $evaluations = $dossier->evaluations()->with(['critere', 'evaluateur'])->get();
        return response()->json($evaluations);
    }

    public function store(Request $request, Dossier $dossier)
    {
        $request->validate([
            'evaluations' => 'required|array',
            'evaluations.*.critere_id' => 'required|exists:criteres_eligibilite,id',
            'evaluations.*.note' => 'required|numeric|min:0',
            'evaluations.*.critere_rempli' => 'required|boolean',
            'evaluations.*.commentaire' => 'nullable|string',
        ]);

        $totalScore = 0;
        $totalPoids = 0;

        foreach ($request->evaluations as $eval) {
            Evaluation::updateOrCreate(
                [
                    'dossier_id' => $dossier->id,
                    'critere_id' => $eval['critere_id'],
                ],
                [
                    'evaluateur_id' => $request->user()->id,
                    'note' => $eval['note'],
                    'critere_rempli' => $eval['critere_rempli'],
                    'commentaire' => $eval['commentaire'] ?? null,
                ]
            );

            $critere = CritereEligibilite::find($eval['critere_id']);
            $totalScore += $eval['note'] * $critere->poids;
            $totalPoids += $critere->poids;
        }

        if ($totalPoids > 0) {
            $dossier->update(['score_global' => round($totalScore / $totalPoids, 2)]);
        }

        return response()->json([
            'evaluations' => $dossier->evaluations()->with('critere')->get(),
            'score_global' => $dossier->score_global,
        ]);
    }
}
