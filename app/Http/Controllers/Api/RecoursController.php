<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recours;
use App\Models\Dossier;
use App\Models\Historique;
use Illuminate\Http\Request;

class RecoursController extends Controller
{
    public function index(Request $request)
    {
        $query = Recours::with(['dossier', 'etudiant', 'traitePar']);

        if ($request->user()->isEtudiant()) {
            $query->where('etudiant_id', $request->user()->id);
        }

        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        return response()->json($query->latest()->paginate(15));
    }

    public function store(Request $request, Dossier $dossier)
    {
        if ($dossier->statut !== 'rejete') {
            return response()->json(['message' => 'Le recours ne peut être soumis que pour un dossier rejeté.'], 422);
        }

        $validated = $request->validate([
            'motif' => 'required|string',
            'justification' => 'required|string',
        ]);

        $recours = Recours::create([
            'dossier_id' => $dossier->id,
            'etudiant_id' => $request->user()->id,
            'motif' => $validated['motif'],
            'justification' => $validated['justification'],
            'statut' => 'soumis',
            'date_soumission' => now(),
        ]);

        $recours->genererReference();
        $recours->save();

        return response()->json($recours->load(['dossier', 'etudiant']), 201);
    }

    public function show(Recours $recour)
    {
        $recour->load(['dossier.etudiant', 'dossier.documents', 'etudiant', 'traitePar']);
        return response()->json($recour);
    }

    public function traiter(Request $request, Recours $recour)
    {
        $request->validate([
            'statut' => 'required|in:accepte,rejete',
            'decision_motif' => 'required|string',
        ]);

        $recour->update([
            'statut' => $request->statut,
            'decision_motif' => $request->decision_motif,
            'traite_par' => $request->user()->id,
            'date_traitement' => now(),
        ]);

        if ($request->statut === 'accepte') {
            $recour->dossier->update([
                'statut' => 'soumis',
                'date_decision' => null,
                'commentaire_admin' => null,
            ]);

            Historique::create([
                'dossier_id' => $recour->dossier_id,
                'user_id' => $request->user()->id,
                'action' => 'Recours accepté - dossier réouvert',
                'ancien_statut' => 'rejete',
                'nouveau_statut' => 'soumis',
            ]);
        }

        return response()->json($recour->load(['dossier', 'etudiant']));
    }
}
