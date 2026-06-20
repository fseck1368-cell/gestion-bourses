<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Convention;
use App\Models\RapportAcademique;
use Illuminate\Http\Request;

class RapportAcademiqueController extends Controller
{
    public function index(Request $request)
    {
        $query = RapportAcademique::with(['etudiant', 'convention']);

        if ($request->filled('statut_academique')) {
            $query->where('statut_academique', $request->statut_academique);
        }

        $rapports = $query->latest()->paginate(15);

        return view('admin.rapports.index', compact('rapports'));
    }

    public function create()
    {
        $conventions = Convention::where('statut', 'active')->with('etudiant')->get();
        return view('admin.rapports.create', compact('conventions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'convention_id' => 'required|exists:conventions,id',
            'semestre' => 'required|string|max:20',
            'annee_universitaire' => 'required|string|max:20',
            'moyenne' => 'nullable|numeric|min:0|max:20',
            'credits_valides' => 'nullable|integer|min:0',
            'credits_total' => 'nullable|integer|min:0',
            'taux_assiduite' => 'nullable|numeric|min:0|max:100',
            'statut_academique' => 'required|in:bon,acceptable,insuffisant,exclus',
            'renouvellement_recommande' => 'boolean',
            'observations' => 'nullable|string|max:2000',
        ]);

        $convention = Convention::findOrFail($validated['convention_id']);

        $validated['renouvellement_recommande'] = $request->boolean('renouvellement_recommande');

        RapportAcademique::create([
            ...$validated,
            'dossier_id' => $convention->dossier_id,
            'etudiant_id' => $convention->etudiant_id,
        ]);

        // Vérifier suspension automatique si résultats insuffisants
        $msg = 'Rapport académique enregistré.';
        if (in_array($validated['statut_academique'], ['insuffisant', 'exclus'])) {
            $suspendu = app(\App\Services\ConventionService::class)->verifierSuspensionAutomatique($convention);
            if ($suspendu) {
                $msg .= ' La convention a été automatiquement suspendue en raison des résultats insuffisants.';
            }
        }

        return redirect()->route('admin.rapports.index')
            ->with('success', $msg);
    }

    public function show(RapportAcademique $rapport)
    {
        $rapport->load(['etudiant', 'convention', 'dossier']);
        return view('admin.rapports.show', compact('rapport'));
    }
}
