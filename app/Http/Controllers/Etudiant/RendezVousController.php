<?php

namespace App\Http\Controllers\Etudiant;

use App\Http\Controllers\Controller;
use App\Models\Dossier;
use App\Models\RendezVous;
use App\Notifications\RappelNotification;
use Illuminate\Http\Request;

class RendezVousController extends Controller
{
    public function store(Request $request, Dossier $dossier)
    {
        if ($dossier->etudiant_id !== auth()->id()) {
            abort(403);
        }

        if (!$dossier->instructeur_id) {
            return back()->with('error', 'Aucun instructeur assigné à ce dossier.');
        }

        $validated = $request->validate([
            'date_heure' => 'required|date|after:now',
            'motif' => 'required|string|max:500',
        ]);

        RendezVous::create([
            ...$validated,
            'dossier_id' => $dossier->id,
            'etudiant_id' => auth()->id(),
            'instructeur_id' => $dossier->instructeur_id,
        ]);

        $dossier->instructeur->notify(new RappelNotification(
            'Demande de rendez-vous',
            'L\'étudiant ' . auth()->user()->name . ' demande un rendez-vous pour le dossier ' . $dossier->reference,
            url('/instructeur/rendez-vous')
        ));

        return back()->with('success', 'Demande de rendez-vous envoyée.');
    }
}
