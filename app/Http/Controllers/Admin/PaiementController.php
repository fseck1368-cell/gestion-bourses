<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\Dossier;
use App\Models\Paiement;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    public function index(Request $request)
    {
        $query = Paiement::with(['etudiant', 'dossier']);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $paiements = $query->latest()->paginate(15);
        $stats = [
            'total' => Paiement::sum('montant'),
            'verse' => Paiement::where('statut', 'verse')->sum('montant'),
            'en_attente' => Paiement::where('statut', 'en_attente')->sum('montant'),
        ];

        return view('admin.paiements.index', compact('paiements', 'stats'));
    }

    public function create()
    {
        $dossiers = Dossier::where('statut', 'accepte')->with('etudiant')->get();
        return view('admin.paiements.create', compact('dossiers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'dossier_id' => 'required|exists:dossiers,id',
            'montant' => 'required|numeric|min:0',
            'mode_paiement' => 'required|in:virement,cheque,especes',
            'reference_bancaire' => 'nullable|string|max:100',
            'banque' => 'nullable|string|max:100',
            'numero_compte' => 'nullable|string|max:50',
            'date_prevue' => 'nullable|date',
            'periode' => 'nullable|string|max:50',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        $dossier = Dossier::findOrFail($validated['dossier_id']);

        // Check plafond
        $financeService = app(\App\Services\FinanceService::class);
        $plafondCheck = $financeService->verifierPlafondCumul($dossier->etudiant_id);
        if ($plafondCheck['depasse']) {
            return back()->withInput()->with('error', 'Plafond de cumul dépassé. L\'étudiant a déjà reçu ' . number_format($plafondCheck['total_recu'], 0, ',', ' ') . ' DH sur un plafond de ' . number_format($plafondCheck['plafond'], 0, ',', ' ') . ' DH.');
        }

        // Check double validation
        if ($financeService->necessiteDoubleValidation($validated['montant'])) {
            $validated['commentaire'] = ($validated['commentaire'] ?? '') . ' [DOUBLE VALIDATION REQUISE]';
        }

        Paiement::create([
            ...$validated,
            'etudiant_id' => $dossier->etudiant_id,
            'reference' => Paiement::genererReference(),
        ]);

        return redirect()->route('admin.paiements.index')
            ->with('success', 'Paiement créé avec succès.');
    }

    public function show(Paiement $paiement)
    {
        $paiement->load(['etudiant', 'dossier', 'validePar']);
        return view('admin.paiements.show', compact('paiement'));
    }

    public function valider(Paiement $paiement)
    {
        // Check if double validation is needed
        if (app(\App\Services\FinanceService::class)->necessiteDoubleValidation($paiement->montant)) {
            if (!str_contains($paiement->commentaire ?? '', '[DOUBLE VALIDATION OK]')) {
                $paiement->update([
                    'commentaire' => ($paiement->commentaire ?? '') . ' [Première validation par ' . auth()->user()->name . ']',
                ]);
                return back()->with('success', 'Première validation enregistrée. Une deuxième validation est nécessaire pour ce montant.');
            }
        }

        $paiement->update([
            'statut' => 'valide',
            'valide_par' => auth()->id(),
        ]);

        return back()->with('success', 'Paiement validé.');
    }

    public function verser(Paiement $paiement)
    {
        $paiement->update([
            'statut' => 'verse',
            'date_versement' => now(),
        ]);

        $budget = Budget::where('campagne_id', $paiement->dossier->campagne_id)->first();
        if ($budget) {
            $budget->increment('montant_consomme', $paiement->montant);
        }

        return back()->with('success', 'Paiement marqué comme versé.');
    }

    public function annuler(Paiement $paiement)
    {
        $paiement->update(['statut' => 'annule']);
        return back()->with('success', 'Paiement annulé.');
    }
}
