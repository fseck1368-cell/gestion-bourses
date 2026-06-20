<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Rappels quotidiens aux instructeurs (dossiers en retard)
Schedule::command('bourses:rappels')->dailyAt('09:00');

// Relance étudiants pour compléments non répondus
Schedule::command('bourses:relance-complements')->dailyAt('10:00');

// Notification campagne qui ferme bientôt (3 jours avant)
Schedule::command('bourses:notifier-cloture --jours=3')->dailyAt('08:00');

// Archivage mensuel des campagnes terminées
Schedule::command('bourses:archiver --force')->monthlyOn(1, '02:00');

// Rotation automatique des dossiers soumis vers les instructeurs
Schedule::command('bourses:rotation')->dailyAt('07:00');

// Escalade des dossiers en instruction depuis trop longtemps
Schedule::command('bourses:escalade')->dailyAt('11:00');
