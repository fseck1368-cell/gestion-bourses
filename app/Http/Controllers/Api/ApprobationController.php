<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Approbation;
use App\Models\Dossier;
use App\Models\Historique;
use Illuminate\Http\Request;

class ApprobationController extends Controller
{
    public function index(Request $request)
    {
        $approbations = Approbation::with(['dossier.etudiant', 'approbateur'])
            ->where('approbateur_id', $request->user()->id)
            ->where('statut', 'en_attente')
            ->latest()
            ->paginate(15);

        return response()->json($approbations);
    }

    public function store(Request $request, Dossier $dossier)
    {
        $request->validate([
            'approbateurs' => 'required|array|min:1',
            'approbateurs.*' => 'exists:users,id',
        ]);

        $dossier->approbations()->delete();

        foreach ($request->approbateurs as $index => $approbateurId) {
            Approbation::create([
                'dossier_id' => $dossier->id,
                'approbateur_id' => $approbateurId,
                'ordre' => $index + 1,
                'statut' => 'en_attente',
            ]);
        }

        return response()->json($dossier->approbations()->with('approbateur')->get());
    }

    public function approuver(Request $request, Approbation $approbation)
    {
        $request->validate([
            'commentaire' => 'nullable|string',
        ]);

        $approbation->update([
            'statut' => 'approuve',
            'commentaire' => $request->commentaire,
            'date_decision' => now(),
        ]);

        $next = Approbation::where('dossier_id', $approbation->dossier_id)
            ->where('ordre', $approbation->ordre + 1)
            ->first();

        if (!$next) {
            $approbation->dossier->update([
                'statut' => 'accepte',
                'date_decision' => now(),
            ]);

            Historique::create([
                'dossier_id' => $approbation->dossier_id,
                'user_id' => $request->user()->id,
                'action' => 'Approbation finale - dossier accepté',
                'nouveau_statut' => 'accepte',
            ]);
        }

        return response()->json($approbation);
    }

    public function rejeter(Request $request, Approbation $approbation)
    {
        $request->validate([
            'commentaire' => 'required|string',
        ]);

        $approbation->update([
            'statut' => 'rejete',
            'commentaire' => $request->commentaire,
            'date_decision' => now(),
        ]);

        $approbation->dossier->update([
            'statut' => 'rejete',
            'date_decision' => now(),
        ]);

        Historique::create([
            'dossier_id' => $approbation->dossier_id,
            'user_id' => $request->user()->id,
            'action' => 'Approbation rejetée',
            'nouveau_statut' => 'rejete',
        ]);

        return response()->json($approbation);
    }
}
