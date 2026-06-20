<?php

namespace App\Http\Controllers;

use App\Models\Convention;
use App\Models\Dossier;
use App\Models\Paiement;
use App\Models\User;
use Illuminate\Http\Request;

class RechercheController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');

        if (!$q || strlen($q) < 2) {
            return view('recherche.index', ['resultats' => [], 'q' => $q]);
        }

        $resultats = [];

        $dossiers = Dossier::where('reference', 'like', "%{$q}%")
            ->orWhereHas('etudiant', function ($query) use ($q) {
                $query->where('nom', 'like', "%{$q}%")
                    ->orWhere('prenom', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('numero_etudiant', 'like', "%{$q}%");
            })
            ->with('etudiant')
            ->limit(10)->get();

        foreach ($dossiers as $d) {
            $resultats[] = [
                'type' => 'Dossier',
                'titre' => $d->reference . ' - ' . $d->etudiant->name,
                'description' => $d->filiere . ' | ' . $d->statut_label,
                'lien' => auth()->user()->isAdministrateur()
                    ? route('admin.dossiers.show', $d)
                    : (auth()->user()->isInstructeur() ? route('instructeur.dossiers.show', $d) : route('etudiant.dossiers.show', $d)),
                'statut' => $d->statut_label,
                'color' => $d->statut_color,
            ];
        }

        $users = User::where('nom', 'like', "%{$q}%")
            ->orWhere('prenom', 'like', "%{$q}%")
            ->orWhere('email', 'like', "%{$q}%")
            ->orWhere('numero_etudiant', 'like', "%{$q}%")
            ->limit(10)->get();

        foreach ($users as $u) {
            $resultats[] = [
                'type' => 'Utilisateur',
                'titre' => $u->name . ' (' . $u->role . ')',
                'description' => $u->email . ($u->numero_etudiant ? ' | N° ' . $u->numero_etudiant : ''),
                'lien' => auth()->user()->isAdministrateur() ? route('admin.users.edit', $u) : '#',
                'statut' => ucfirst($u->role),
                'color' => 'blue',
            ];
        }

        $conventions = Convention::where('reference', 'like', "%{$q}%")
            ->with('etudiant')
            ->limit(5)->get();

        foreach ($conventions as $c) {
            $resultats[] = [
                'type' => 'Convention',
                'titre' => $c->reference . ' - ' . $c->etudiant->name,
                'description' => $c->statut_label . ' | ' . number_format($c->montant_mensuel, 0, ',', ' ') . ' DH/mois',
                'lien' => route('admin.conventions.show', $c),
                'statut' => $c->statut_label,
                'color' => $c->statut_color,
            ];
        }

        $paiements = Paiement::where('reference', 'like', "%{$q}%")
            ->with('etudiant')
            ->limit(5)->get();

        foreach ($paiements as $p) {
            $resultats[] = [
                'type' => 'Paiement',
                'titre' => $p->reference . ' - ' . $p->etudiant->name,
                'description' => number_format($p->montant, 2, ',', ' ') . ' DH | ' . $p->statut_label,
                'lien' => route('admin.paiements.show', $p),
                'statut' => $p->statut_label,
                'color' => $p->statut_color,
            ];
        }

        return view('recherche.index', compact('resultats', 'q'));
    }
}
