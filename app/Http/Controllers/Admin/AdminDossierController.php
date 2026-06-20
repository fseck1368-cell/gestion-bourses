<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dossier;
use App\Models\Historique;
use App\Models\User;
use App\Notifications\DecisionDossierNotification;
use App\Notifications\RappelNotification;
use Illuminate\Http\Request;

class AdminDossierController extends Controller
{
    public function index(Request $request)
    {
        $query = Dossier::with(['etudiant', 'instructeur']);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%$search%")
                  ->orWhereHas('etudiant', function ($q2) use ($search) {
                      $q2->where('nom', 'like', "%$search%")
                         ->orWhere('prenom', 'like', "%$search%")
                         ->orWhere('numero_etudiant', 'like', "%$search%");
                  });
            });
        }

        $dossiers = $query->latest()->paginate(20);

        return view('admin.dossiers.index', compact('dossiers'));
    }

    public function show(Dossier $dossier)
    {
        // Audit d'accès
        app(\App\Services\AuditService::class)->logAcces(
            auth()->id(), 'view', 'dossier', $dossier->id
        );

        // Vérifier verrouillage
        $lockService = app(\App\Services\LockService::class);
        $lockInfo = $lockService->estVerrouille($dossier->id);
        $lockedBy = null;
        if ($lockInfo && $lockInfo['user_id'] !== auth()->id()) {
            $lockedBy = User::find($lockInfo['user_id']);
        }

        $dossier->load(['etudiant', 'instructeur', 'documents', 'historiques.user']);
        $instructeurs = User::where('role', 'instructeur')->where('actif', true)->get();

        $scoring = app(\App\Services\ScoringService::class)->calculerScore($dossier);
        $quotaFiliere = app(\App\Services\EligibiliteService::class)->verifierQuotaFiliere($dossier->filiere, $dossier->campagne_id);

        // Finance checks
        $financeCheck = app(\App\Services\FinanceService::class)->verifierPlafondCumul($dossier->etudiant_id);

        return view('admin.dossiers.show', compact('dossier', 'instructeurs', 'scoring', 'quotaFiliere', 'lockedBy', 'financeCheck'));
    }

    public function assigner(Request $request, Dossier $dossier)
    {
        $validated = $request->validate([
            'instructeur_id' => 'required|exists:users,id',
        ]);

        $instructeur = User::findOrFail($validated['instructeur_id']);
        if (!$instructeur->isInstructeur()) {
            return back()->with('error', 'Cet utilisateur n\'est pas un instructeur.');
        }

        $ancienStatut = $dossier->statut;
        $dossier->update([
            'instructeur_id' => $instructeur->id,
            'statut' => 'en_cours_instruction',
            'date_instruction' => now(),
        ]);

        Historique::create([
            'dossier_id' => $dossier->id,
            'user_id' => auth()->id(),
            'action' => 'Assignation à ' . $instructeur->prenom . ' ' . $instructeur->nom,
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => 'en_cours_instruction',
        ]);

        $instructeur->notify(new RappelNotification(
            'Nouveau dossier assigné',
            'Le dossier ' . $dossier->reference . ' vous a été assigné pour instruction.',
            url('/instructeur/dossiers/' . $dossier->id)
        ));

        return back()->with('success', 'Dossier assigné à ' . $instructeur->prenom . ' ' . $instructeur->nom);
    }

    public function assignerMasse(Request $request)
    {
        $validated = $request->validate([
            'instructeur_id' => 'required|exists:users,id',
            'dossier_ids' => 'required|array|min:1',
            'dossier_ids.*' => 'exists:dossiers,id',
        ]);

        $instructeur = User::findOrFail($validated['instructeur_id']);
        if (!$instructeur->isInstructeur()) {
            return back()->with('error', 'Cet utilisateur n\'est pas un instructeur.');
        }

        $count = 0;
        foreach ($validated['dossier_ids'] as $dossierId) {
            $dossier = Dossier::find($dossierId);
            if ($dossier && $dossier->statut === 'soumis') {
                $dossier->update([
                    'instructeur_id' => $instructeur->id,
                    'statut' => 'en_cours_instruction',
                    'date_instruction' => now(),
                ]);

                Historique::create([
                    'dossier_id' => $dossier->id,
                    'user_id' => auth()->id(),
                    'action' => 'Assignation en masse à ' . $instructeur->prenom . ' ' . $instructeur->nom,
                    'ancien_statut' => 'soumis',
                    'nouveau_statut' => 'en_cours_instruction',
                ]);

                $count++;
            }
        }

        $instructeur->notify(new RappelNotification(
            'Nouveaux dossiers assignés',
            $count . ' dossier(s) vous ont été assignés pour instruction.',
            url('/instructeur/dossiers')
        ));

        return back()->with('success', $count . ' dossier(s) assigné(s) à ' . $instructeur->prenom . ' ' . $instructeur->nom);
    }

    public function transferer(Request $request, Dossier $dossier)
    {
        $validated = $request->validate([
            'instructeur_id' => 'required|exists:users,id',
            'motif_transfert' => 'required|string|max:500',
        ]);

        $instructeur = User::findOrFail($validated['instructeur_id']);
        if (!$instructeur->isInstructeur()) {
            return back()->with('error', 'Cet utilisateur n\'est pas un instructeur.');
        }

        $ancienInstructeur = $dossier->instructeur;
        $dossier->update(['instructeur_id' => $instructeur->id]);

        Historique::create([
            'dossier_id' => $dossier->id,
            'user_id' => auth()->id(),
            'action' => 'Transfert de ' . ($ancienInstructeur?->name ?? 'non assigné') . ' à ' . $instructeur->name,
            'ancien_statut' => $dossier->statut,
            'nouveau_statut' => $dossier->statut,
            'commentaire' => $validated['motif_transfert'],
        ]);

        $instructeur->notify(new RappelNotification(
            'Dossier transféré',
            'Le dossier ' . $dossier->reference . ' vous a été transféré. Motif : ' . $validated['motif_transfert'],
            url('/instructeur/dossiers/' . $dossier->id)
        ));

        if ($ancienInstructeur) {
            $ancienInstructeur->notify(new RappelNotification(
                'Dossier réassigné',
                'Le dossier ' . $dossier->reference . ' a été transféré à ' . $instructeur->name . '.',
                null
            ));
        }

        return back()->with('success', 'Dossier transféré à ' . $instructeur->name);
    }

    public function decision(Request $request, Dossier $dossier)
    {
        $validated = $request->validate([
            'decision' => 'required|in:accepte,rejete',
            'commentaire' => 'nullable|string|max:2000',
        ]);

        $ancienStatut = $dossier->statut;
        $dossier->update([
            'statut' => $validated['decision'],
            'commentaire_admin' => $validated['commentaire'],
            'date_decision' => now(),
        ]);

        Historique::create([
            'dossier_id' => $dossier->id,
            'user_id' => auth()->id(),
            'action' => 'Décision administrative : ' . $validated['decision'],
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => $validated['decision'],
            'commentaire' => $validated['commentaire'],
        ]);

        $dossier->etudiant->notify(new DecisionDossierNotification($dossier));

        return back()->with('success', 'Décision enregistrée : ' . $validated['decision']);
    }
}
