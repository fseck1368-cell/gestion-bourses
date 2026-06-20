<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dossier;
use App\Models\Document;
use App\Models\Historique;
use Illuminate\Http\Request;

class DossierController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Dossier::with(['etudiant', 'instructeur', 'campagne']);

        if ($user->isEtudiant()) {
            $query->where('etudiant_id', $user->id);
        } elseif ($user->isInstructeur()) {
            $query->where('instructeur_id', $user->id);
        }

        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('etudiant', function ($q) use ($search) {
                      $q->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%");
                  });
            });
        }

        return response()->json($query->latest()->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'annee_universitaire' => 'required|string',
            'niveau_etude' => 'required|string',
            'filiere' => 'required|string',
            'etablissement' => 'required|string',
            'moyenne_generale' => 'required|numeric|min:0|max:20',
            'situation_sociale' => 'required|string',
            'revenu_familial' => 'required|numeric|min:0',
            'nombre_freres_soeurs' => 'required|integer|min:0',
            'motif_demande' => 'required|string',
            'campagne_id' => 'nullable|exists:campagnes,id',
        ]);

        $validated['etudiant_id'] = $request->user()->id;
        $validated['statut'] = 'soumis';
        $validated['date_soumission'] = now();

        $dossier = Dossier::create($validated);
        $dossier->genererReference();
        $dossier->save();

        Historique::create([
            'dossier_id' => $dossier->id,
            'user_id' => $request->user()->id,
            'action' => 'Création du dossier',
            'nouveau_statut' => 'soumis',
        ]);

        return response()->json($dossier->load(['etudiant', 'documents']), 201);
    }

    public function show(Request $request, Dossier $dossier)
    {
        $user = $request->user();

        if ($user->isEtudiant() && $dossier->etudiant_id !== $user->id) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }
        if ($user->isInstructeur() && $dossier->instructeur_id !== $user->id) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $dossier->load(['etudiant', 'instructeur', 'documents', 'historiques.user', 'messages.user', 'paiements', 'evaluations.critere', 'recours', 'convention', 'rendezVous']);

        return response()->json($dossier);
    }

    public function update(Request $request, Dossier $dossier)
    {
        $user = $request->user();

        if ($user->isEtudiant() && $dossier->etudiant_id !== $user->id) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        if (!$dossier->estModifiable()) {
            return response()->json(['message' => 'Ce dossier ne peut plus être modifié.'], 422);
        }

        $validated = $request->validate([
            'annee_universitaire' => 'sometimes|string',
            'niveau_etude' => 'sometimes|string',
            'filiere' => 'sometimes|string',
            'etablissement' => 'sometimes|string',
            'moyenne_generale' => 'sometimes|numeric|min:0|max:20',
            'situation_sociale' => 'sometimes|string',
            'revenu_familial' => 'sometimes|numeric|min:0',
            'nombre_freres_soeurs' => 'sometimes|integer|min:0',
            'motif_demande' => 'sometimes|string',
        ]);

        $dossier->update($validated);

        return response()->json($dossier->load(['etudiant', 'documents']));
    }

    public function assigner(Request $request, Dossier $dossier)
    {
        $request->validate([
            'instructeur_id' => 'required|exists:users,id',
        ]);

        $dossier->update([
            'instructeur_id' => $request->instructeur_id,
            'statut' => 'en_cours_instruction',
            'date_instruction' => now(),
        ]);

        Historique::create([
            'dossier_id' => $dossier->id,
            'user_id' => $request->user()->id,
            'action' => 'Assignation à un instructeur',
            'ancien_statut' => 'soumis',
            'nouveau_statut' => 'en_cours_instruction',
        ]);

        return response()->json($dossier->load(['etudiant', 'instructeur']));
    }

    public function transferer(Request $request, Dossier $dossier)
    {
        $request->validate([
            'instructeur_id' => 'required|exists:users,id',
            'motif' => 'nullable|string',
        ]);

        $ancienInstructeur = $dossier->instructeur_id;
        $dossier->update(['instructeur_id' => $request->instructeur_id]);

        Historique::create([
            'dossier_id' => $dossier->id,
            'user_id' => $request->user()->id,
            'action' => 'Transfert de dossier',
            'commentaire' => $request->motif,
        ]);

        return response()->json($dossier->load(['etudiant', 'instructeur']));
    }

    public function decision(Request $request, Dossier $dossier)
    {
        $request->validate([
            'statut' => 'required|in:accepte,rejete',
            'commentaire_admin' => 'nullable|string',
        ]);

        $ancienStatut = $dossier->statut;
        $dossier->update([
            'statut' => $request->statut,
            'commentaire_admin' => $request->commentaire_admin,
            'date_decision' => now(),
        ]);

        Historique::create([
            'dossier_id' => $dossier->id,
            'user_id' => $request->user()->id,
            'action' => 'Décision administrative',
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => $request->statut,
            'commentaire' => $request->commentaire_admin,
        ]);

        return response()->json($dossier->load(['etudiant', 'instructeur']));
    }

    public function donnerAvis(Request $request, Dossier $dossier)
    {
        $user = $request->user();

        if ($dossier->instructeur_id !== $user->id) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $request->validate([
            'avis_instructeur' => 'required|in:favorable,defavorable,reserve',
            'commentaire_instructeur' => 'nullable|string',
        ]);

        $dossier->update([
            'avis_instructeur' => $request->avis_instructeur,
            'commentaire_instructeur' => $request->commentaire_instructeur,
            'date_avis_instructeur' => now(),
            'avis_transmis_admin' => true,
        ]);

        Historique::create([
            'dossier_id' => $dossier->id,
            'user_id' => $user->id,
            'action' => 'Avis de l\'instructeur: ' . $request->avis_instructeur,
            'commentaire' => $request->commentaire_instructeur,
        ]);

        return response()->json($dossier);
    }

    public function demanderComplement(Request $request, Dossier $dossier)
    {
        $user = $request->user();

        if ($dossier->instructeur_id !== $user->id) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $request->validate([
            'complement_description' => 'required|string',
        ]);

        $dossier->update([
            'complement_requis' => true,
            'complement_description' => $request->complement_description,
            'complement_date_demande' => now(),
        ]);

        Historique::create([
            'dossier_id' => $dossier->id,
            'user_id' => $user->id,
            'action' => 'Demande de complément',
            'commentaire' => $request->complement_description,
        ]);

        return response()->json($dossier);
    }

    public function repondreComplement(Request $request, Dossier $dossier)
    {
        $user = $request->user();

        if ($dossier->etudiant_id !== $user->id) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $request->validate([
            'reponse' => 'required|string',
        ]);

        $dossier->update([
            'complement_requis' => false,
            'complement_date_reponse' => now(),
        ]);

        Historique::create([
            'dossier_id' => $dossier->id,
            'user_id' => $user->id,
            'action' => 'Réponse au complément',
            'commentaire' => $request->reponse,
        ]);

        return response()->json($dossier);
    }
}
