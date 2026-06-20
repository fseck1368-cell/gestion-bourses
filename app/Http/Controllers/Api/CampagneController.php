<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campagne;
use Illuminate\Http\Request;

class CampagneController extends Controller
{
    public function index()
    {
        $campagnes = Campagne::withCount('dossiers')->latest()->paginate(15);
        return response()->json($campagnes);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'annee_universitaire' => 'required|string',
            'date_ouverture' => 'required|date',
            'date_cloture' => 'required|date|after:date_ouverture',
            'active' => 'boolean',
        ]);

        $campagne = Campagne::create($validated);

        return response()->json($campagne, 201);
    }

    public function show(Campagne $campagne)
    {
        $campagne->load(['dossiers', 'criteres', 'budgets']);
        return response()->json($campagne);
    }

    public function update(Request $request, Campagne $campagne)
    {
        $validated = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'annee_universitaire' => 'sometimes|string',
            'date_ouverture' => 'sometimes|date',
            'date_cloture' => 'sometimes|date|after:date_ouverture',
            'active' => 'boolean',
        ]);

        $campagne->update($validated);

        return response()->json($campagne);
    }

    public function toggleActive(Campagne $campagne)
    {
        $campagne->update(['active' => !$campagne->active]);
        return response()->json($campagne);
    }
}
