<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campagne;
use App\Models\CritereEligibilite;
use Illuminate\Http\Request;

class CritereController extends Controller
{
    public function index(Request $request)
    {
        $query = CritereEligibilite::with('campagne');

        if ($request->filled('campagne_id')) {
            $query->where('campagne_id', $request->campagne_id);
        }

        $criteres = $query->latest()->paginate(15);
        $campagnes = Campagne::where('active', true)->get();

        return view('admin.criteres.index', compact('criteres', 'campagnes'));
    }

    public function create()
    {
        $campagnes = Campagne::where('active', true)->get();
        return view('admin.criteres.create', compact('campagnes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'campagne_id' => 'required|exists:campagnes,id',
            'nom' => 'required|string|max:200',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:numerique,booleen,selection',
            'valeur_min' => 'nullable|numeric',
            'valeur_max' => 'nullable|numeric',
            'valeurs_acceptees' => 'nullable|string',
            'poids' => 'required|integer|min:1|max:10',
            'obligatoire' => 'boolean',
        ]);

        if (!empty($validated['valeurs_acceptees'])) {
            $validated['valeurs_acceptees'] = array_map('trim', explode(',', $validated['valeurs_acceptees']));
        }

        $validated['obligatoire'] = $request->boolean('obligatoire');

        CritereEligibilite::create($validated);

        return redirect()->route('admin.criteres.index')
            ->with('success', 'Critère créé avec succès.');
    }

    public function edit(CritereEligibilite $critere)
    {
        $campagnes = Campagne::where('active', true)->get();
        return view('admin.criteres.edit', compact('critere', 'campagnes'));
    }

    public function update(Request $request, CritereEligibilite $critere)
    {
        $validated = $request->validate([
            'campagne_id' => 'required|exists:campagnes,id',
            'nom' => 'required|string|max:200',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:numerique,booleen,selection',
            'valeur_min' => 'nullable|numeric',
            'valeur_max' => 'nullable|numeric',
            'valeurs_acceptees' => 'nullable|string',
            'poids' => 'required|integer|min:1|max:10',
            'obligatoire' => 'boolean',
        ]);

        if (!empty($validated['valeurs_acceptees'])) {
            $validated['valeurs_acceptees'] = array_map('trim', explode(',', $validated['valeurs_acceptees']));
        }

        $validated['obligatoire'] = $request->boolean('obligatoire');

        $critere->update($validated);

        return redirect()->route('admin.criteres.index')
            ->with('success', 'Critère mis à jour.');
    }

    public function toggleActif(CritereEligibilite $critere)
    {
        $critere->update(['actif' => !$critere->actif]);
        return back()->with('success', 'Statut du critère modifié.');
    }
}
