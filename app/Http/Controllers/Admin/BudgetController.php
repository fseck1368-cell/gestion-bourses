<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\Campagne;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index()
    {
        $budgets = Budget::with('campagne')->latest()->paginate(15);
        $totalAlloue = Budget::sum('montant_alloue');
        $totalConsomme = Budget::sum('montant_consomme');

        return view('admin.budgets.index', compact('budgets', 'totalAlloue', 'totalConsomme'));
    }

    public function create()
    {
        $campagnes = Campagne::where('active', true)->get();
        return view('admin.budgets.create', compact('campagnes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'campagne_id' => 'required|exists:campagnes,id',
            'libelle' => 'required|string|max:200',
            'montant_alloue' => 'required|numeric|min:0',
            'annee_universitaire' => 'required|string|max:20',
            'source_financement' => 'nullable|string|max:200',
            'observations' => 'nullable|string|max:2000',
        ]);

        Budget::create($validated);

        return redirect()->route('admin.budgets.index')
            ->with('success', 'Budget créé avec succès.');
    }

    public function edit(Budget $budget)
    {
        $campagnes = Campagne::where('active', true)->get();
        return view('admin.budgets.edit', compact('budget', 'campagnes'));
    }

    public function update(Request $request, Budget $budget)
    {
        $validated = $request->validate([
            'campagne_id' => 'required|exists:campagnes,id',
            'libelle' => 'required|string|max:200',
            'montant_alloue' => 'required|numeric|min:0',
            'annee_universitaire' => 'required|string|max:20',
            'source_financement' => 'nullable|string|max:200',
            'observations' => 'nullable|string|max:2000',
        ]);

        $budget->update($validated);

        return redirect()->route('admin.budgets.index')
            ->with('success', 'Budget mis à jour.');
    }
}
