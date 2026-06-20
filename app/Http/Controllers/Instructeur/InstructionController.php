<?php

namespace App\Http\Controllers\Instructeur;

use App\Http\Controllers\Controller;
use App\Models\Dossier;
use App\Models\Historique;
use App\Models\RendezVous;
use App\Notifications\RappelNotification;
use Illuminate\Http\Request;

class InstructionController extends Controller
{
    public function index()
    {
        $dossiers = Dossier::where('instructeur_id', auth()->id())
            ->with('etudiant')
            ->latest()
            ->paginate(20);

        $stats = [
            'en_cours' => Dossier::where('instructeur_id', auth()->id())->where('statut', 'en_cours_instruction')->count(),
            'traites_mois' => Dossier::where('instructeur_id', auth()->id())
                ->whereNotNull('avis_instructeur')
                ->whereMonth('date_avis_instructeur', now()->month)->count(),
            'complement_en_attente' => Dossier::where('instructeur_id', auth()->id())
                ->where('complement_requis', true)
                ->whereNull('complement_date_reponse')->count(),
            'rdv_a_venir' => RendezVous::where('instructeur_id', auth()->id())
                ->where('statut', 'confirme')
                ->where('date_heure', '>=', now())->count(),
        ];

        return view('instructeur.dossiers.index', compact('dossiers', 'stats'));
    }

    public function show(Dossier $dossier)
    {
        if ($dossier->instructeur_id !== auth()->id()) {
            abort(403);
        }

        // Verrouillage du dossier
        $lockService = app(\App\Services\LockService::class);
        $locked = $lockService->verrouiller($dossier->id, auth()->id());
        $lockedBy = null;
        if (!$locked) {
            $lockInfo = $lockService->estVerrouille($dossier->id);
            $lockedBy = \App\Models\User::find($lockInfo['user_id']);
        }

        // Audit d'accès
        app(\App\Services\AuditService::class)->logAcces(
            auth()->id(), 'view', 'dossier', $dossier->id
        );

        $dossier->load(['etudiant', 'documents', 'historiques.user', 'messages.user', 'rendezVous']);

        // Calcul du score pour recommandation
        $scoring = app(\App\Services\ScoringService::class)->calculerScore($dossier);

        return view('instructeur.dossiers.show', compact('dossier', 'scoring', 'locked', 'lockedBy'));
    }

    public function donnerAvis(Request $request, Dossier $dossier)
    {
        if ($dossier->instructeur_id !== auth()->id()) {
            abort(403);
        }

        if ($dossier->statut !== 'en_cours_instruction') {
            return back()->with('error', 'Ce dossier ne peut plus être instruit.');
        }

        $validated = $request->validate([
            'avis' => 'required|in:favorable,defavorable,reserve',
            'commentaire' => 'required|string|max:2000',
        ]);

        $dossier->update([
            'commentaire_instructeur' => $validated['commentaire'],
            'avis_instructeur' => $validated['avis'],
            'date_avis_instructeur' => now(),
            'avis_transmis_admin' => true,
        ]);

        Historique::create([
            'dossier_id' => $dossier->id,
            'user_id' => auth()->id(),
            'action' => 'Avis ' . $validated['avis'] . ' émis',
            'ancien_statut' => $dossier->statut,
            'nouveau_statut' => $dossier->statut,
            'commentaire' => $validated['commentaire'],
        ]);

        return redirect()->route('instructeur.dossiers.index')
            ->with('success', 'Avis formel transmis à l\'administration pour le dossier ' . $dossier->reference);
    }

    public function demanderComplement(Request $request, Dossier $dossier)
    {
        if ($dossier->instructeur_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'complement_description' => 'required|string|max:2000',
        ]);

        $dossier->update([
            'complement_requis' => true,
            'complement_description' => $validated['complement_description'],
            'complement_date_demande' => now(),
            'complement_date_reponse' => null,
        ]);

        Historique::create([
            'dossier_id' => $dossier->id,
            'user_id' => auth()->id(),
            'action' => 'Demande de complément',
            'ancien_statut' => $dossier->statut,
            'nouveau_statut' => $dossier->statut,
            'commentaire' => $validated['complement_description'],
        ]);

        $dossier->etudiant->notify(new RappelNotification(
            'Complément requis pour votre dossier ' . $dossier->reference,
            $validated['complement_description'],
            url('/etudiant/dossiers/' . $dossier->id)
        ));

        return back()->with('success', 'Demande de complément envoyée à l\'étudiant.');
    }

    public function rendezVous()
    {
        $rdvs = RendezVous::where('instructeur_id', auth()->id())
            ->with(['etudiant', 'dossier'])
            ->orderBy('date_heure', 'desc')
            ->paginate(15);

        return view('instructeur.rendez-vous.index', compact('rdvs'));
    }

    public function confirmerRdv(RendezVous $rendezVous)
    {
        if ($rendezVous->instructeur_id !== auth()->id()) {
            abort(403);
        }

        $rendezVous->update(['statut' => 'confirme']);

        $rendezVous->etudiant->notify(new RappelNotification(
            'Rendez-vous confirmé',
            'Votre rendez-vous du ' . $rendezVous->date_heure->format('d/m/Y à H:i') . ' a été confirmé.',
            url('/etudiant/dossiers/' . $rendezVous->dossier_id)
        ));

        return back()->with('success', 'Rendez-vous confirmé.');
    }

    public function refuserRdv(Request $request, RendezVous $rendezVous)
    {
        if ($rendezVous->instructeur_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'commentaire_refus' => 'required|string|max:500',
        ]);

        $rendezVous->update([
            'statut' => 'refuse',
            'commentaire_refus' => $validated['commentaire_refus'],
        ]);

        $rendezVous->etudiant->notify(new RappelNotification(
            'Rendez-vous refusé',
            'Votre demande de rendez-vous a été refusée : ' . $validated['commentaire_refus'],
            url('/etudiant/dossiers/' . $rendezVous->dossier_id)
        ));

        return back()->with('success', 'Rendez-vous refusé.');
    }

    public function terminerRdv(Request $request, RendezVous $rendezVous)
    {
        if ($rendezVous->instructeur_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'note_instructeur' => 'nullable|string|max:2000',
        ]);

        $rendezVous->update([
            'statut' => 'termine',
            'note_instructeur' => $validated['note_instructeur'],
        ]);

        return back()->with('success', 'Rendez-vous marqué comme terminé.');
    }
}
