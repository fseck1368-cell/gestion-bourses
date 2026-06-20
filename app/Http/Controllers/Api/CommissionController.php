<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Vote;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function index()
    {
        $commissions = Commission::withCount(['membres', 'dossiers'])
            ->latest()
            ->paginate(15);

        return response()->json($commissions);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'date_deliberation' => 'required|date',
            'membres' => 'required|array|min:1',
            'membres.*' => 'exists:users,id',
            'dossiers' => 'required|array|min:1',
            'dossiers.*' => 'exists:dossiers,id',
        ]);

        $commission = Commission::create([
            'nom' => $validated['nom'],
            'date_deliberation' => $validated['date_deliberation'],
            'statut' => 'planifiee',
        ]);

        $commission->membres()->attach($validated['membres']);
        $commission->dossiers()->attach($validated['dossiers']);

        return response()->json($commission->load(['membres', 'dossiers']), 201);
    }

    public function show(Commission $commission)
    {
        $commission->load(['membres', 'dossiers.etudiant', 'votes.user']);
        return response()->json($commission);
    }

    public function demarrer(Commission $commission)
    {
        $commission->update(['statut' => 'en_cours']);
        return response()->json($commission);
    }

    public function terminer(Request $request, Commission $commission)
    {
        $commission->update(['statut' => 'terminee']);

        foreach ($commission->dossiers as $dossier) {
            $votes = $commission->votes()->where('dossier_id', $dossier->id)->get();
            $pour = $votes->where('vote', 'pour')->count();
            $contre = $votes->where('vote', 'contre')->count();

            $decision = $pour > $contre ? 'accepte' : 'rejete';
            $commission->dossiers()->updateExistingPivot($dossier->id, [
                'decision_finale' => $decision,
            ]);

            $dossier->update([
                'statut' => $decision,
                'date_decision' => now(),
            ]);
        }

        return response()->json($commission->load(['dossiers']));
    }

    public function voter(Request $request, Commission $commission)
    {
        $request->validate([
            'dossier_id' => 'required|exists:dossiers,id',
            'vote' => 'required|in:pour,contre,abstention',
            'commentaire' => 'nullable|string',
        ]);

        $vote = Vote::updateOrCreate(
            [
                'commission_id' => $commission->id,
                'dossier_id' => $request->dossier_id,
                'user_id' => $request->user()->id,
            ],
            [
                'vote' => $request->vote,
                'commentaire' => $request->commentaire,
            ]
        );

        return response()->json($vote);
    }
}
