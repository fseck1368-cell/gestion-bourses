<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campagne;
use Illuminate\Http\Request;

class CampagneController extends Controller
{
    public function index()
    {
        $campagnes = Campagne::latest()->paginate(15);
        return view('admin.campagnes.index', compact('campagnes'));
    }

    public function create()
    {
        return view('admin.campagnes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:200',
            'description' => 'nullable|string|max:2000',
            'annee_universitaire' => 'required|string|max:20',
            'date_ouverture' => 'required|date',
            'date_cloture' => 'required|date|after:date_ouverture',
        ]);

        Campagne::create($validated);

        return redirect()->route('admin.campagnes.index')
            ->with('success', 'Campagne créée avec succès.');
    }

    public function edit(Campagne $campagne)
    {
        return view('admin.campagnes.edit', compact('campagne'));
    }

    public function update(Request $request, Campagne $campagne)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:200',
            'description' => 'nullable|string|max:2000',
            'annee_universitaire' => 'required|string|max:20',
            'date_ouverture' => 'required|date',
            'date_cloture' => 'required|date|after:date_ouverture',
            'active' => 'boolean',
        ]);

        $validated['active'] = $request->boolean('active');
        $campagne->update($validated);

        return redirect()->route('admin.campagnes.index')
            ->with('success', 'Campagne mise à jour.');
    }

    public function toggleActive(Campagne $campagne)
    {
        $campagne->update(['active' => !$campagne->active]);
        return back()->with('success', 'Statut de la campagne modifié.');
    }
}
