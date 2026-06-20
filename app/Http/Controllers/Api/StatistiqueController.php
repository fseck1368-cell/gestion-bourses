<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dossier;
use App\Models\Paiement;
use App\Models\User;
use App\Models\Budget;
use App\Models\Recours;
use Illuminate\Http\Request;

class StatistiqueController extends Controller
{
    public function index()
    {
        $stats = [
            'dossiers' => [
                'total' => Dossier::count(),
                'soumis' => Dossier::where('statut', 'soumis')->count(),
                'en_cours' => Dossier::where('statut', 'en_cours_instruction')->count(),
                'acceptes' => Dossier::where('statut', 'accepte')->count(),
                'rejetes' => Dossier::where('statut', 'rejete')->count(),
            ],
            'utilisateurs' => [
                'total' => User::count(),
                'etudiants' => User::where('role', 'etudiant')->count(),
                'instructeurs' => User::where('role', 'instructeur')->count(),
                'administrateurs' => User::where('role', 'administrateur')->count(),
            ],
            'financier' => [
                'budget_total' => Budget::sum('montant_alloue'),
                'budget_consomme' => Budget::sum('montant_consomme'),
                'paiements_en_attente' => Paiement::where('statut', 'en_attente')->sum('montant'),
                'paiements_verses' => Paiement::where('statut', 'verse')->sum('montant'),
            ],
            'recours' => [
                'total' => Recours::count(),
                'en_attente' => Recours::where('statut', 'soumis')->count(),
                'acceptes' => Recours::where('statut', 'accepte')->count(),
                'rejetes' => Recours::where('statut', 'rejete')->count(),
            ],
        ];

        return response()->json($stats);
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();

        if ($user->isEtudiant()) {
            return response()->json([
                'mes_dossiers' => Dossier::where('etudiant_id', $user->id)->count(),
                'dossiers_acceptes' => Dossier::where('etudiant_id', $user->id)->where('statut', 'accepte')->count(),
                'paiements_recus' => Paiement::where('etudiant_id', $user->id)->where('statut', 'verse')->sum('montant'),
                'recours_en_cours' => Recours::where('etudiant_id', $user->id)->whereIn('statut', ['soumis', 'en_examen'])->count(),
            ]);
        }

        if ($user->isInstructeur()) {
            return response()->json([
                'dossiers_assignes' => Dossier::where('instructeur_id', $user->id)->count(),
                'dossiers_en_cours' => Dossier::where('instructeur_id', $user->id)->where('statut', 'en_cours_instruction')->count(),
                'avis_donnes' => Dossier::where('instructeur_id', $user->id)->whereNotNull('avis_instructeur')->count(),
            ]);
        }

        return $this->index();
    }
}
