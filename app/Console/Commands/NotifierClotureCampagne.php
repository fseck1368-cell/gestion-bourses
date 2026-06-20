<?php

namespace App\Console\Commands;

use App\Models\Alerte;
use App\Models\Campagne;
use App\Models\User;
use App\Notifications\RappelNotification;
use Illuminate\Console\Command;

class NotifierClotureCampagne extends Command
{
    protected $signature = 'bourses:notifier-cloture {--jours=3 : Jours avant clôture}';
    protected $description = 'Notifier les étudiants des campagnes qui ferment bientôt';

    public function handle(): int
    {
        $jours = (int) $this->option('jours');

        $campagnes = Campagne::where('active', true)
            ->where('date_cloture', '>=', now())
            ->where('date_cloture', '<=', now()->addDays($jours))
            ->get();

        if ($campagnes->isEmpty()) {
            $this->info('Aucune campagne ne ferme dans les ' . $jours . ' prochains jours.');
            return Command::SUCCESS;
        }

        $etudiants = User::where('role', 'etudiant')->where('actif', true)->get();
        $count = 0;

        foreach ($campagnes as $campagne) {
            $joursRestants = now()->diffInDays($campagne->date_cloture);

            foreach ($etudiants as $etudiant) {
                $dejaNotifie = Alerte::where('user_id', $etudiant->id)
                    ->where('type', 'campagne_cloture_etudiant')
                    ->where('titre', 'like', '%' . $campagne->nom . '%')
                    ->where('created_at', '>=', now()->subDays(2))
                    ->exists();

                if ($dejaNotifie) continue;

                Alerte::create([
                    'user_id' => $etudiant->id,
                    'type' => 'campagne_cloture_etudiant',
                    'titre' => 'Campagne ferme bientôt : ' . $campagne->nom,
                    'message' => 'La campagne "' . $campagne->nom . '" ferme dans ' . $joursRestants . ' jour(s). Soumettez votre dossier avant le ' . $campagne->date_cloture->format('d/m/Y') . '.',
                    'niveau' => $joursRestants <= 1 ? 'danger' : 'warning',
                    'lien' => '/etudiant/dossiers/create',
                ]);

                $count++;
            }
        }

        $this->info("$count notification(s) envoyée(s) pour " . $campagnes->count() . " campagne(s).");
        return Command::SUCCESS;
    }
}
