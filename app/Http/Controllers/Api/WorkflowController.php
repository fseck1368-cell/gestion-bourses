<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dossier;
use App\Models\Historique;
use App\Services\WorkflowService;
use Illuminate\Http\Request;

class WorkflowController extends Controller
{
    public function priorite(Dossier $dossier)
    {
        $priorite = app(WorkflowService::class)->calculerPriorite($dossier);

        return response()->json([
            'dossier_id' => $dossier->id,
            'reference' => $dossier->reference,
            'priorite' => $priorite,
            'label' => match ($priorite) {
                1 => 'Très haute',
                2 => 'Haute',
                3 => 'Moyenne',
                4 => 'Basse',
                5 => 'Très basse',
            },
        ]);
    }

    public function instructeurOptimal()
    {
        $instructeur = app(WorkflowService::class)->instructeurOptimal();

        if (!$instructeur) {
            return response()->json(['message' => 'Aucun instructeur disponible.'], 404);
        }

        return response()->json([
            'instructeur' => $instructeur,
            'dossiers_en_cours' => $instructeur->dossiersAssignes()
                ->where('statut', 'en_cours_instruction')->count(),
        ]);
    }

    public function dossiersEscalade(Request $request)
    {
        $jours = $request->get('jours', 15);
        $dossiers = app(WorkflowService::class)->dossiersAEscalader($jours);

        return response()->json([
            'count' => $dossiers->count(),
            'jours_seuil' => $jours,
            'dossiers' => $dossiers->load('etudiant', 'instructeur'),
        ]);
    }

    public function assignerAuto(Request $request)
    {
        $workflowService = app(WorkflowService::class);

        $dossiersSoumis = Dossier::where('statut', 'soumis')
            ->whereNull('instructeur_id')
            ->get();

        $count = 0;
        foreach ($dossiersSoumis as $dossier) {
            $instructeur = $workflowService->instructeurOptimal();
            if (!$instructeur) break;

            $dossier->update([
                'instructeur_id' => $instructeur->id,
                'statut' => 'en_cours_instruction',
                'date_instruction' => now(),
            ]);

            Historique::create([
                'dossier_id' => $dossier->id,
                'user_id' => $request->user()->id,
                'action' => 'Assignation automatique à ' . $instructeur->prenom . ' ' . $instructeur->nom,
                'ancien_statut' => 'soumis',
                'nouveau_statut' => 'en_cours_instruction',
            ]);

            $count++;
        }

        return response()->json([
            'message' => $count . ' dossier(s) assigné(s) automatiquement.',
            'assignes' => $count,
        ]);
    }
}
