<?php

namespace App\Console\Commands;

use App\Models\Campagne;
use App\Models\Dossier;
use Illuminate\Console\Command;

class ArchiverDossiers extends Command
{
    protected $signature = 'bourses:archiver {--force : Archiver sans confirmation}';
    protected $description = 'Archiver les dossiers des campagnes terminées (clôturées depuis plus de 6 mois)';

    public function handle(): int
    {
        $campagnesTerminees = Campagne::where('active', false)
            ->where('date_cloture', '<=', now()->subMonths(6))
            ->get();

        if ($campagnesTerminees->isEmpty()) {
            $this->info('Aucune campagne à archiver.');
            return Command::SUCCESS;
        }

        $this->info($campagnesTerminees->count() . ' campagne(s) terminée(s) trouvée(s).');

        $totalArchives = 0;

        foreach ($campagnesTerminees as $campagne) {
            $dossiersSoumis = Dossier::where('campagne_id', $campagne->id)
                ->where('statut', 'soumis')
                ->get();

            foreach ($dossiersSoumis as $dossier) {
                $dossier->update([
                    'statut' => 'rejete',
                    'commentaire_admin' => 'Rejeté automatiquement - Campagne terminée sans instruction.',
                    'date_decision' => now(),
                ]);
                $totalArchives++;
            }

            $this->line(" - {$campagne->nom}: " . $dossiersSoumis->count() . " dossier(s) non traités archivés.");
        }

        $this->info("Total: $totalArchives dossier(s) archivé(s).");
        return Command::SUCCESS;
    }
}
