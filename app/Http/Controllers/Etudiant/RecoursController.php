<?php

namespace App\Http\Controllers\Etudiant;

use App\Http\Controllers\Controller;
use App\Models\Dossier;
use App\Models\Recours;
use Illuminate\Http\Request;

class RecoursController extends Controller
{
    public function create(Dossier $dossier)
    {
        if ($dossier->etudiant_id !== auth()->id() || $dossier->statut !== 'rejete') {
            abort(403);
        }

        $existant = Recours::where('dossier_id', $dossier->id)
            ->whereIn('statut', ['soumis', 'en_examen'])->exists();

        if ($existant) {
            return back()->with('error', 'Un recours est déjà en cours pour ce dossier.');
        }

        return view('etudiant.recours.create', compact('dossier'));
    }

    public function store(Request $request, Dossier $dossier)
    {
        if ($dossier->etudiant_id !== auth()->id() || $dossier->statut !== 'rejete') {
            abort(403);
        }

        $validated = $request->validate([
            'motif' => 'required|string|max:5000',
            'justification' => 'nullable|string|max:5000',
        ]);

        Recours::create([
            ...$validated,
            'reference' => Recours::genererReference(),
            'dossier_id' => $dossier->id,
            'etudiant_id' => auth()->id(),
            'date_soumission' => now(),
        ]);

        return redirect()->route('etudiant.dossiers.show', $dossier)
            ->with('success', 'Votre recours a été soumis avec succès.');
    }
}
