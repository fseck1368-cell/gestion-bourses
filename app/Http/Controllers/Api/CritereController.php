<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CritereEligibilite;
use Illuminate\Http\Request;

class CritereController extends Controller
{
    public function index(Request $request)
    {
        $query = CritereEligibilite::with('campagne');

        if ($request->has('campagne_id')) {
            $query->where('campagne_id', $request->campagne_id);
        }

        return response()->json($query->latest()->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'campagne_id' => 'required|exists:campagnes,id',
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:numerique,booleen,selection',
            'valeur_min' => 'nullable|numeric',
            'valeur_max' => 'nullable|numeric',
            'valeurs_acceptees' => 'nullable|array',
            'poids' => 'integer|min:1',
            'obligatoire' => 'boolean',
            'actif' => 'boolean',
        ]);

        $critere = CritereEligibilite::create($validated);

        return response()->json($critere, 201);
    }

    public function show(CritereEligibilite $critere)
    {
        $critere->load('campagne');
        return response()->json($critere);
    }

    public function update(Request $request, CritereEligibilite $critere)
    {
        $validated = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'type' => 'sometimes|in:numerique,booleen,selection',
            'valeur_min' => 'nullable|numeric',
            'valeur_max' => 'nullable|numeric',
            'valeurs_acceptees' => 'nullable|array',
            'poids' => 'integer|min:1',
            'obligatoire' => 'boolean',
            'actif' => 'boolean',
        ]);

        $critere->update($validated);

        return response()->json($critere);
    }

    public function toggleActif(CritereEligibilite $critere)
    {
        $critere->update(['actif' => !$critere->actif]);
        return response()->json($critere);
    }
}
