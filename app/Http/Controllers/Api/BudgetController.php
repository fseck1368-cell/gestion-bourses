<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index()
    {
        $budgets = Budget::with('campagne')->latest()->paginate(15);
        return response()->json($budgets);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'campagne_id' => 'required|exists:campagnes,id',
            'libelle' => 'required|string|max:255',
            'montant_alloue' => 'required|numeric|min:0',
            'annee_universitaire' => 'required|string',
            'source_financement' => 'nullable|string',
            'observations' => 'nullable|string',
        ]);

        $validated['montant_consomme'] = 0;

        $budget = Budget::create($validated);

        return response()->json($budget, 201);
    }

    public function show(Budget $budget)
    {
        $budget->load('campagne');
        return response()->json($budget);
    }

    public function update(Request $request, Budget $budget)
    {
        $validated = $request->validate([
            'libelle' => 'sometimes|string|max:255',
            'montant_alloue' => 'sometimes|numeric|min:0',
            'annee_universitaire' => 'sometimes|string',
            'source_financement' => 'nullable|string',
            'observations' => 'nullable|string',
        ]);

        $budget->update($validated);

        return response()->json($budget);
    }
}
