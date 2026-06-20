<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CritereEligibilite;
use App\Models\Dossier;
use App\Models\Evaluation;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    public function index()
    {
        $dossiers = Dossier::where('statut', 'en_cours_instruction')
            ->with(['etudiant', 'evaluations.critere', 'campagne'])
            ->paginate(15);

        return view('admin.evaluations.index', compact('dossiers'));
    }

    public function evaluer(Dossier $dossier)
    {
        $criteres = CritereEligibilite::where('campagne_id', $dossier->campagne_id)
            ->where('actif', true)->get();

        $evaluations = Evaluation::where('dossier_id', $dossier->id)
            ->pluck('note', 'critere_id')->toArray();

        $dossier->load('etudiant');

        return view('admin.evaluations.evaluer', compact('dossier', 'criteres', 'evaluations'));
    }

    public function store(Request $request, Dossier $dossier)
    {
        $validated = $request->validate([
            'notes' => 'required|array',
            'notes.*' => 'nullable|numeric|min:0|max:20',
            'commentaires' => 'nullable|array',
            'commentaires.*' => 'nullable|string|max:500',
        ]);

        $scoreTotal = 0;
        $poidsTotal = 0;

        $criteres = CritereEligibilite::where('campagne_id', $dossier->campagne_id)
            ->where('actif', true)->get();

        foreach ($criteres as $critere) {
            $note = $validated['notes'][$critere->id] ?? null;
            $commentaire = $validated['commentaires'][$critere->id] ?? null;

            Evaluation::updateOrCreate(
                ['dossier_id' => $dossier->id, 'critere_id' => $critere->id],
                [
                    'evaluateur_id' => auth()->id(),
                    'note' => $note,
                    'critere_rempli' => $note !== null && $note >= ($critere->valeur_min ?? 0),
                    'commentaire' => $commentaire,
                ]
            );

            if ($note !== null) {
                $scoreTotal += $note * $critere->poids;
                $poidsTotal += $critere->poids;
            }
        }

        $scoreGlobal = $poidsTotal > 0 ? round($scoreTotal / $poidsTotal, 2) : null;
        $dossier->update(['score_global' => $scoreGlobal]);

        return redirect()->route('admin.evaluations.index')
            ->with('success', "Évaluation enregistrée. Score global : $scoreGlobal/20");
    }
}
