<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Convention;
use App\Models\Dossier;
use Illuminate\Http\Request;

class ConventionController extends Controller
{
    public function index(Request $request)
    {
        $query = Convention::with(['etudiant', 'dossier']);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $conventions = $query->latest()->paginate(15);

        return view('admin.conventions.index', compact('conventions'));
    }

    public function create()
    {
        $dossiers = Dossier::where('statut', 'accepte')
            ->doesntHave('convention')
            ->with('etudiant')->get();

        return view('admin.conventions.create', compact('dossiers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'dossier_id' => 'required|exists:dossiers,id',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'montant_mensuel' => 'required|numeric|min:0',
            'duree_mois' => 'required|integer|min:1|max:36',
            'conditions' => 'nullable|string|max:5000',
            'obligations_etudiant' => 'nullable|string|max:5000',
        ]);

        $dossier = Dossier::findOrFail($validated['dossier_id']);

        Convention::create([
            ...$validated,
            'etudiant_id' => $dossier->etudiant_id,
            'reference' => Convention::genererReference(),
            'statut' => 'brouillon',
        ]);

        return redirect()->route('admin.conventions.index')
            ->with('success', 'Convention créée avec succès.');
    }

    public function show(Convention $convention)
    {
        $convention->load(['etudiant', 'dossier', 'rapportsAcademiques']);
        return view('admin.conventions.show', compact('convention'));
    }

    public function activer(Convention $convention)
    {
        $convention->update([
            'statut' => 'active',
            'date_signature' => now(),
        ]);

        // Générer automatiquement l'échéancier de paiements
        $paiements = app(\App\Services\ConventionService::class)->genererEcheancier($convention);

        $msg = 'Convention activée.';
        if (count($paiements) > 0) {
            $msg .= ' ' . count($paiements) . ' paiement(s) mensuel(s) créé(s) automatiquement.';
        }

        return back()->with('success', $msg);
    }

    public function suspendre(Request $request, Convention $convention)
    {
        $convention->update(['statut' => 'suspendue']);
        return back()->with('success', 'Convention suspendue.');
    }

    public function resilier(Request $request, Convention $convention)
    {
        $validated = $request->validate([
            'motif_resiliation' => 'required|string|max:2000',
        ]);

        $convention->update([
            'statut' => 'resiliee',
            'motif_resiliation' => $validated['motif_resiliation'],
        ]);

        return back()->with('success', 'Convention résiliée.');
    }
}
