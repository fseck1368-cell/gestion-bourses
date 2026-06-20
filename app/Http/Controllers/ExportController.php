<?php

namespace App\Http\Controllers;

use App\Models\Convention;
use App\Models\Dossier;
use App\Models\Paiement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    public function recepisse(Dossier $dossier)
    {
        $dossier->load(['etudiant', 'documents']);
        $pdf = Pdf::loadView('exports.recepisse', compact('dossier'));
        return $pdf->download('recepisse_' . $dossier->reference . '.pdf');
    }

    public function rapportAdmin(Request $request)
    {
        $query = Dossier::with(['etudiant', 'instructeur']);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('campagne_id')) {
            $query->where('campagne_id', $request->campagne_id);
        }

        $dossiers = $query->latest()->get();
        $stats = [
            'total' => $dossiers->count(),
            'acceptes' => $dossiers->where('statut', 'accepte')->count(),
            'rejetes' => $dossiers->where('statut', 'rejete')->count(),
            'en_cours' => $dossiers->where('statut', 'en_cours_instruction')->count(),
            'soumis' => $dossiers->where('statut', 'soumis')->count(),
        ];

        $pdf = Pdf::loadView('exports.rapport', compact('dossiers', 'stats'));
        return $pdf->download('rapport_bourses_' . date('Y-m-d') . '.pdf');
    }

    public function exportCsv(Request $request)
    {
        $query = Dossier::with(['etudiant', 'instructeur']);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $dossiers = $query->latest()->get();

        $csv = "Reference;Etudiant;Email;Filiere;Niveau;Etablissement;Moyenne;Statut;Date Soumission;Instructeur\n";

        foreach ($dossiers as $d) {
            $csv .= implode(';', [
                $d->reference,
                $d->etudiant->prenom . ' ' . $d->etudiant->nom,
                $d->etudiant->email,
                $d->filiere,
                $d->niveau_etude,
                $d->etablissement,
                $d->moyenne_generale ?? '',
                $d->statut,
                $d->date_soumission?->format('d/m/Y') ?? '',
                $d->instructeur ? $d->instructeur->prenom . ' ' . $d->instructeur->nom : '',
            ]) . "\n";
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="dossiers_' . date('Y-m-d') . '.csv"',
        ]);
    }

    public function convention(Convention $convention)
    {
        $convention->load(['etudiant', 'dossier']);
        $pdf = Pdf::loadView('exports.convention', compact('convention'));
        return $pdf->download('convention_' . $convention->reference . '.pdf');
    }

    public function attestation(Dossier $dossier)
    {
        $dossier->load(['etudiant', 'campagne']);
        $pdf = Pdf::loadView('exports.attestation', compact('dossier'));
        return $pdf->download('attestation_' . $dossier->reference . '.pdf');
    }

    public function relevePaiements()
    {
        $user = auth()->user();
        $paiements = Paiement::where('etudiant_id', $user->id)
            ->where('statut', 'verse')
            ->orderBy('date_versement')
            ->get();

        $pdf = Pdf::loadView('exports.releve_paiements', compact('paiements', 'user'));
        return $pdf->download('releve_paiements_' . $user->numero_etudiant . '.pdf');
    }
}
