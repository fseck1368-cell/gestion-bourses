<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Convention;
use Illuminate\Http\Request;

class ConventionController extends Controller
{
    public function index(Request $request)
    {
        $query = Convention::with(['dossier', 'etudiant']);

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
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'montant_mensuel' => 'required|numeric|min:0',
            'duree_mois' => 'required|integer|min:1',
            'conditions' => 'nullable|string',
            'obligations_etudiant' => 'nullable|string',
        ]);

        $validated['statut'] = 'brouillon';

        $convention = Convention::create($validated);
        $convention->genererReference();
        $convention->save();

        return response()->json($convention->load(['dossier', 'etudiant']), 201);
    }

    public function show(Convention $convention)
    {
        $convention->load(['dossier.etudiant', 'etudiant', 'rapportsAcademiques']);
        return response()->json($convention);
    }

    public function activer(Convention $convention)
    {
        $convention->update([
            'statut' => 'active',
            'date_signature' => now(),
        ]);

        return response()->json($convention);
    }

    public function suspendre(Convention $convention)
    {
        $convention->update(['statut' => 'suspendue']);
        return response()->json($convention);
    }

    public function resilier(Request $request, Convention $convention)
    {
        $request->validate([
            'motif_resiliation' => 'required|string',
        ]);

        $convention->update([
            'statut' => 'resiliee',
            'motif_resiliation' => $request->motif_resiliation,
        ]);

        return response()->json($convention);
    }
}
