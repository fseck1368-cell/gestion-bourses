<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RapportAcademique;
use Illuminate\Http\Request;

class RapportAcademiqueController extends Controller
{
    public function index(Request $request)
    {
        $query = RapportAcademique::with(['dossier', 'etudiant', 'convention']);

        if ($request->has('statut_academique')) {
            $query->where('statut_academique', $request->statut_academique);
        }

        return response()->json($query->latest()->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'dossier_id' => 'required|exists:dossiers,id',
            'etudiant_id' => 'required|exists:users,id',
            'convention_id' => 'nullable|exists:conventions,id',
            'semestre' => 'required|string',
            'annee_universitaire' => 'required|string',
            'moyenne' => 'required|numeric|min:0|max:20',
            'credits_valides' => 'required|integer|min:0',
            'credits_total' => 'required|integer|min:0',
            'taux_assiduite' => 'required|numeric|min:0|max:100',
            'statut_academique' => 'required|in:bon,acceptable,insuffisant,exclus',
            'renouvellement_recommande' => 'required|boolean',
            'observations' => 'nullable|string',
        ]);

        $rapport = RapportAcademique::create($validated);

        return response()->json($rapport->load(['dossier', 'etudiant', 'convention']), 201);
    }

    public function show(RapportAcademique $rapport)
    {
        $rapport->load(['dossier.etudiant', 'etudiant', 'convention']);
        return response()->json($rapport);
    }
}
