<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use App\Models\Budget;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    public function index(Request $request)
    {
        $query = Paiement::with(['dossier', 'etudiant']);

        if ($request->user()->isEtudiant()) {
            $query->where('etudiant_id', $request->user()->id);
        }

        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        return response()->json($query->latest()->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'dossier_id' => 'required|exists:dossiers,id',
            'etudiant_id' => 'required|exists:users,id',
            'montant' => 'required|numeric|min:0',
            'mode_paiement' => 'required|in:virement,cheque,especes',
            'reference_bancaire' => 'nullable|string',
            'banque' => 'nullable|string',
            'numero_compte' => 'nullable|string',
            'date_prevue' => 'required|date',
            'periode' => 'nullable|string',
            'commentaire' => 'nullable|string',
        ]);

        $validated['statut'] = 'en_attente';

        $paiement = Paiement::create($validated);
        $paiement->genererReference();
        $paiement->save();

        return response()->json($paiement->load(['dossier', 'etudiant']), 201);
    }

    public function show(Paiement $paiement)
    {
        $paiement->load(['dossier.etudiant', 'etudiant', 'validePar']);
        return response()->json($paiement);
    }

    public function valider(Request $request, Paiement $paiement)
    {
        $paiement->update([
            'statut' => 'valide',
            'valide_par' => $request->user()->id,
        ]);

        return response()->json($paiement);
    }

    public function verser(Request $request, Paiement $paiement)
    {
        $paiement->update([
            'statut' => 'verse',
            'date_versement' => now(),
        ]);

        $budget = Budget::where('campagne_id', $paiement->dossier->campagne_id)->first();
        if ($budget) {
            $budget->increment('montant_consomme', $paiement->montant);
        }

        return response()->json($paiement);
    }

    public function annuler(Paiement $paiement)
    {
        $paiement->update(['statut' => 'annule']);
        return response()->json($paiement);
    }
}
