<?php

namespace App\Http\Controllers;

use App\Models\Dossier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatistiqueController extends Controller
{
    public function index()
    {
        $dossiersParMois = Dossier::selectRaw('MONTH(created_at) as mois, COUNT(*) as total')
            ->whereYear('created_at', date('Y'))
            ->groupBy('mois')
            ->orderBy('mois')
            ->pluck('total', 'mois')
            ->toArray();

        $parStatut = Dossier::selectRaw('statut, COUNT(*) as total')
            ->groupBy('statut')
            ->pluck('total', 'statut')
            ->toArray();

        $parFiliere = Dossier::selectRaw('filiere, COUNT(*) as total')
            ->groupBy('filiere')
            ->orderByDesc('total')
            ->limit(10)
            ->pluck('total', 'filiere')
            ->toArray();

        $parNiveau = Dossier::selectRaw('niveau_etude, COUNT(*) as total')
            ->groupBy('niveau_etude')
            ->pluck('total', 'niveau_etude')
            ->toArray();

        $tauxAcceptation = Dossier::whereIn('statut', ['accepte', 'rejete'])->count() > 0
            ? round(Dossier::where('statut', 'accepte')->count() / Dossier::whereIn('statut', ['accepte', 'rejete'])->count() * 100, 1)
            : 0;

        $moyenneTraitement = Dossier::whereNotNull('date_decision')
            ->whereNotNull('date_soumission')
            ->selectRaw('AVG(DATEDIFF(date_decision, date_soumission)) as moyenne')
            ->value('moyenne');

        return view('admin.statistiques', compact(
            'dossiersParMois', 'parStatut', 'parFiliere', 'parNiveau',
            'tauxAcceptation', 'moyenneTraitement'
        ));
    }
}
