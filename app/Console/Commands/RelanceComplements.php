<?php

namespace App\Console\Commands;

use App\Models\Alerte;
use App\Models\Dossier;
use App\Models\Rappel;
use App\Notifications\RappelNotification;
use Illuminate\Console\Command;

class RelanceComplements extends Command
{
    protected $signature = 'bourses:relance-complements {--jours=7 : Jours sans réponse avant relance}';
    protected $description = 'Relancer les étudiants qui n\'ont pas répondu aux demandes de complément';

    public function handle(): int
    {
        $jours = (int) $this->option('jours');

        $dossiers = Dossier::where('complement_requis', true)
            ->whereNull('complement_date_reponse')
            ->whereNotNull('complement_date_demande')
            ->where('complement_date_demande', '<=', now()->subDays($jours))
            ->with('etudiant')
            ->get();

        $count = 0;
        foreach ($dossiers as $dossier) {
            $dejaRelance = Rappel::where('dossier_id', $dossier->id)
                ->where('type', 'complement')
                ->where('created_at', '>=', now()->subDays(3))
                ->exists();

            if ($dejaRelance) continue;

            $dossier->etudiant->notify(new RappelNotification(
                'Complément en attente',
                'Votre dossier ' . $dossier->reference . ' attend un complément depuis ' . now()->diffInDays($dossier->complement_date_demande) . ' jours.',
                url('/etudiant/dossiers/' . $dossier->id)
            ));

            Alerte::create([
                'user_id' => $dossier->etudiant_id,
                'type' => 'relance_complement',
                'titre' => 'Rappel : complément en attente',
                'message' => 'Veuillez fournir le complément demandé pour votre dossier ' . $dossier->reference . '.',
                'niveau' => 'warning',
                'lien' => '/etudiant/dossiers/' . $dossier->id,
            ]);

            Rappel::create([
                'dossier_id' => $dossier->id,
                'destinataire_id' => $dossier->etudiant_id,
                'type' => 'complement',
                'message' => 'Relance automatique après ' . now()->diffInDays($dossier->complement_date_demande) . ' jours',
                'envoye' => true,
                'date_envoi' => now(),
            ]);

            $count++;
        }

        $this->info("$count relance(s) envoyée(s).");
        return Command::SUCCESS;
    }
}
