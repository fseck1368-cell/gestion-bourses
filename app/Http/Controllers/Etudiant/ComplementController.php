<?php

namespace App\Http\Controllers\Etudiant;

use App\Http\Controllers\Controller;
use App\Models\Dossier;
use App\Models\Historique;
use App\Models\Message;
use App\Notifications\RappelNotification;
use Illuminate\Http\Request;

class ComplementController extends Controller
{
    public function repondre(Request $request, Dossier $dossier)
    {
        if ($dossier->etudiant_id !== auth()->id()) {
            abort(403);
        }

        if (!$dossier->complement_requis) {
            return back()->with('error', 'Aucun complément demandé.');
        }

        $validated = $request->validate([
            'reponse' => 'required|string|max:5000',
        ]);

        $dossier->update([
            'complement_date_reponse' => now(),
        ]);

        Message::create([
            'dossier_id' => $dossier->id,
            'user_id' => auth()->id(),
            'contenu' => '[Réponse au complément] ' . $validated['reponse'],
            'demande_complement' => false,
        ]);

        Historique::create([
            'dossier_id' => $dossier->id,
            'user_id' => auth()->id(),
            'action' => 'Réponse au complément',
            'ancien_statut' => $dossier->statut,
            'nouveau_statut' => $dossier->statut,
            'commentaire' => 'Complément fourni par l\'étudiant',
        ]);

        if ($dossier->instructeur) {
            $dossier->instructeur->notify(new RappelNotification(
                'Complément reçu - ' . $dossier->reference,
                'L\'étudiant ' . auth()->user()->name . ' a répondu à votre demande de complément.',
                url('/instructeur/dossiers/' . $dossier->id)
            ));
        }

        return back()->with('success', 'Votre réponse a été envoyée.');
    }
}
