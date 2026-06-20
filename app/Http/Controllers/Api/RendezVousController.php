<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RendezVous;
use App\Models\Dossier;
use Illuminate\Http\Request;

class RendezVousController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = RendezVous::with(['dossier', 'etudiant', 'instructeur']);

        if ($user->isEtudiant()) {
            $query->where('etudiant_id', $user->id);
        } elseif ($user->isInstructeur()) {
            $query->where('instructeur_id', $user->id);
        }

        return response()->json($query->latest()->paginate(15));
    }

    public function store(Request $request, Dossier $dossier)
    {
        $request->validate([
            'date_heure' => 'required|date|after:now',
            'lieu' => 'nullable|string',
            'motif' => 'required|string',
        ]);

        $rendezVous = RendezVous::create([
            'dossier_id' => $dossier->id,
            'etudiant_id' => $request->user()->id,
            'instructeur_id' => $dossier->instructeur_id,
            'date_heure' => $request->date_heure,
            'lieu' => $request->lieu,
            'motif' => $request->motif,
            'statut' => 'demande',
        ]);

        return response()->json($rendezVous->load(['dossier', 'etudiant', 'instructeur']), 201);
    }

    public function confirmer(RendezVous $rendezVous)
    {
        $rendezVous->update(['statut' => 'confirme']);
        return response()->json($rendezVous);
    }

    public function refuser(Request $request, RendezVous $rendezVous)
    {
        $request->validate([
            'commentaire_refus' => 'required|string',
        ]);

        $rendezVous->update([
            'statut' => 'refuse',
            'commentaire_refus' => $request->commentaire_refus,
        ]);

        return response()->json($rendezVous);
    }

    public function terminer(Request $request, RendezVous $rendezVous)
    {
        $request->validate([
            'note_instructeur' => 'nullable|string',
        ]);

        $rendezVous->update([
            'statut' => 'termine',
            'note_instructeur' => $request->note_instructeur,
        ]);

        return response()->json($rendezVous);
    }
}
