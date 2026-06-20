<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recours;
use Illuminate\Http\Request;

class RecoursController extends Controller
{
    public function index(Request $request)
    {
        $query = Recours::with(['etudiant', 'dossier', 'traitePar']);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $recours = $query->latest()->paginate(15);

        return view('admin.recours.index', compact('recours'));
    }

    public function show(Recours $recour)
    {
        $recour->load(['etudiant', 'dossier.documents', 'traitePar']);
        return view('admin.recours.show', compact('recour'));
    }

    public function traiter(Request $request, Recours $recour)
    {
        $validated = $request->validate([
            'statut' => 'required|in:accepte,rejete',
            'decision_motif' => 'required|string|max:2000',
        ]);

        $recour->update([
            'statut' => $validated['statut'],
            'decision_motif' => $validated['decision_motif'],
            'traite_par' => auth()->id(),
            'date_traitement' => now(),
        ]);

        if ($validated['statut'] === 'accepte') {
            $recour->dossier->update([
                'statut' => 'en_cours_instruction',
                'commentaire_admin' => 'Dossier réouvert suite au recours accepté.',
            ]);
        }

        return redirect()->route('admin.recours.index')
            ->with('success', 'Recours traité avec succès.');
    }
}
