<?php

namespace App\Http\Controllers;

use App\Models\Alerte;
use App\Models\Budget;
use App\Models\Convention;
use App\Models\Dossier;
use App\Models\Paiement;
use App\Models\Recours;
use App\Models\RendezVous;
use App\Models\User;
use App\Services\AlerteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(AlerteService $alerteService)
    {
        $user = auth()->user();

        if ($user->isEtudiant()) {
            $dossiers = Dossier::where('etudiant_id', $user->id)->latest()->get();
            $paiements = Paiement::where('etudiant_id', $user->id)->latest()->limit(5)->get();
            $convention = Convention::where('etudiant_id', $user->id)->where('statut', 'active')->first();
            $recours = Recours::where('etudiant_id', $user->id)->latest()->first();
            $totalVerse = Paiement::where('etudiant_id', $user->id)->where('statut', 'verse')->sum('montant');

            $paiementsParMois = Paiement::where('etudiant_id', $user->id)
                ->where('statut', 'verse')
                ->whereYear('date_versement', now()->year)
                ->selectRaw("LPAD(MONTH(date_versement), 2, '0') as mois, SUM(montant) as total")
                ->groupBy('mois')
                ->orderBy('mois')
                ->pluck('total', 'mois')
                ->toArray();

            $rdvProchains = RendezVous::where('etudiant_id', $user->id)
                ->whereIn('statut', ['demande', 'confirme'])
                ->where('date_heure', '>=', now())
                ->orderBy('date_heure')
                ->limit(3)
                ->get();

            return view('dashboard.etudiant', compact('dossiers', 'paiements', 'convention', 'recours', 'totalVerse', 'paiementsParMois', 'rdvProchains'));
        }

        if ($user->isInstructeur()) {
            $dossiersAssignes = Dossier::where('instructeur_id', $user->id)
                ->where('statut', 'en_cours_instruction')
                ->latest()->get();
            $dossiersTraites = Dossier::where('instructeur_id', $user->id)
                ->whereIn('statut', ['accepte', 'rejete'])
                ->latest()->limit(10)->get();

            $totalTraites = Dossier::where('instructeur_id', $user->id)
                ->whereIn('statut', ['accepte', 'rejete'])->count();
            $totalAssignes = Dossier::where('instructeur_id', $user->id)->count();

            $stats = [
                'assignes' => $dossiersAssignes->count(),
                'total_traites' => $totalTraites,
                'traites_mois' => Dossier::where('instructeur_id', $user->id)
                    ->whereIn('statut', ['accepte', 'rejete'])
                    ->whereMonth('date_decision', now()->month)->count(),
                'traites_semaine' => Dossier::where('instructeur_id', $user->id)
                    ->whereIn('statut', ['accepte', 'rejete'])
                    ->where('date_decision', '>=', now()->startOfWeek())->count(),
                'favorables' => Dossier::where('instructeur_id', $user->id)
                    ->where('avis_instructeur', 'favorable')->count(),
                'defavorables' => Dossier::where('instructeur_id', $user->id)
                    ->where('avis_instructeur', 'defavorable')->count(),
            ];

            $rdvAVenir = RendezVous::where('instructeur_id', $user->id)
                ->whereIn('statut', ['demande', 'confirme'])
                ->where('date_heure', '>=', now())
                ->orderBy('date_heure')
                ->limit(5)
                ->get();

            $dossiersEnRetard = Dossier::where('instructeur_id', $user->id)
                ->where('statut', 'en_cours_instruction')
                ->where('date_instruction', '<=', now()->subDays(7))
                ->count();

            return view('dashboard.instructeur', compact('dossiersAssignes', 'dossiersTraites', 'stats', 'rdvAVenir', 'dossiersEnRetard'));
        }

        if ($user->isAdministrateur()) {
            $alerteService->genererAlertes();

            $stats = [
                'total' => Dossier::count(),
                'soumis' => Dossier::where('statut', 'soumis')->count(),
                'en_cours' => Dossier::where('statut', 'en_cours_instruction')->count(),
                'acceptes' => Dossier::where('statut', 'accepte')->count(),
                'rejetes' => Dossier::where('statut', 'rejete')->count(),
            ];

            $statsFinancieres = [
                'budget_total' => Budget::sum('montant_alloue'),
                'budget_consomme' => Budget::sum('montant_consomme'),
                'paiements_en_attente' => Paiement::where('statut', 'en_attente')->count(),
                'total_verse' => Paiement::where('statut', 'verse')->sum('montant'),
            ];

            $dossiersParMois = Dossier::whereYear('created_at', now()->year)
                ->selectRaw("LPAD(MONTH(created_at), 2, '0') as mois, COUNT(*) as total")
                ->groupBy('mois')
                ->orderBy('mois')
                ->pluck('total', 'mois')
                ->toArray();

            $alertes = Alerte::nonLues()->actives()->latest()->limit(5)->get();
            $derniersDossiers = Dossier::with('etudiant')->latest()->limit(10)->get();
            $recoursEnAttente = Recours::where('statut', 'soumis')->count();

            $statsUtilisateurs = [
                'etudiants' => User::where('role', 'etudiant')->count(),
                'instructeurs' => User::where('role', 'instructeur')->count(),
                'admins' => User::where('role', 'administrateur')->count(),
            ];

            $tauxAcceptation = $stats['total'] > 0
                ? round(($stats['acceptes'] / $stats['total']) * 100, 1)
                : 0;

            $activitesRecentes = \App\Models\Historique::with(['user', 'dossier'])
                ->latest()
                ->limit(10)
                ->get();

            $tempsTraitementMoyen = Dossier::whereNotNull('date_soumission')
                ->whereNotNull('date_decision')
                ->selectRaw('AVG(DATEDIFF(date_decision, date_soumission)) as avg_days')
                ->value('avg_days');
            $tempsTraitementMoyen = $tempsTraitementMoyen ? round($tempsTraitementMoyen, 1) : 0;

            return view('dashboard.administrateur', compact('stats', 'statsFinancieres', 'alertes', 'derniersDossiers', 'recoursEnAttente', 'dossiersParMois', 'statsUtilisateurs', 'tauxAcceptation', 'activitesRecentes', 'tempsTraitementMoyen'));
        }

        return redirect('/');
    }
}
