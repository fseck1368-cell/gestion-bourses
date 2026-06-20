<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Dossier;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function index()
    {
        $commissions = Commission::withCount(['membres', 'dossiers', 'votes'])->latest()->paginate(15);
        return view('admin.commissions.index', compact('commissions'));
    }

    public function create()
    {
        $instructeurs = User::where('role', 'instructeur')->where('actif', true)->get();
        $dossiers = Dossier::where('statut', 'en_cours_instruction')->with('etudiant')->get();
        return view('admin.commissions.create', compact('instructeurs', 'dossiers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:200',
            'date_deliberation' => 'required|date',
            'membres' => 'required|array|min:2',
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

        return redirect()->route('admin.commissions.show', $commission)
            ->with('success', 'Commission créée avec ' . count($validated['membres']) . ' membres et ' . count($validated['dossiers']) . ' dossiers.');
    }

    public function show(Commission $commission)
    {
        $commission->load(['membres', 'dossiers.etudiant', 'votes.user']);
        return view('admin.commissions.show', compact('commission'));
    }

    public function demarrer(Commission $commission)
    {
        $commission->update(['statut' => 'en_cours']);
        return back()->with('success', 'Délibération démarrée.');
    }

    public function terminer(Commission $commission)
    {
        $commission->update(['statut' => 'terminee']);

        foreach ($commission->dossiers as $dossier) {
            $votes = Vote::where('commission_id', $commission->id)
                ->where('dossier_id', $dossier->id)->get();

            $pour = $votes->where('vote', 'pour')->count();
            $contre = $votes->where('vote', 'contre')->count();

            $decision = $pour > $contre ? 'accepte' : 'rejete';

            $commission->dossiers()->updateExistingPivot($dossier->id, ['decision_finale' => $decision]);

            $dossier->update([
                'statut' => $decision,
                'date_decision' => now(),
                'commentaire_admin' => "Décision par commission : $pour pour, $contre contre.",
            ]);
        }

        return back()->with('success', 'Délibération terminée. Décisions appliquées.');
    }

    public function voter(Request $request, Commission $commission)
    {
        $validated = $request->validate([
            'dossier_id' => 'required|exists:dossiers,id',
            'vote' => 'required|in:pour,contre,abstention',
            'commentaire' => 'nullable|string|max:500',
        ]);

        Vote::updateOrCreate(
            [
                'commission_id' => $commission->id,
                'dossier_id' => $validated['dossier_id'],
                'user_id' => auth()->id(),
            ],
            [
                'vote' => $validated['vote'],
                'commentaire' => $validated['commentaire'],
            ]
        );

        return back()->with('success', 'Vote enregistré.');
    }
}
