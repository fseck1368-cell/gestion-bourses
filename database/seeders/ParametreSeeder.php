<?php

namespace Database\Seeders;

use App\Models\Parametre;
use Illuminate\Database\Seeder;

class ParametreSeeder extends Seeder
{
    public function run(): void
    {
        $parametres = [
            ['cle' => 'annee_universitaire', 'valeur' => '2025-2026', 'type' => 'text', 'groupe' => 'general', 'label' => 'Année universitaire en cours', 'description' => 'Année universitaire active par défaut'],
            ['cle' => 'montant_bourse_defaut', 'valeur' => '2000', 'type' => 'number', 'groupe' => 'financier', 'label' => 'Montant mensuel par défaut (DH)', 'description' => 'Montant mensuel de bourse par défaut'],
            ['cle' => 'duree_bourse_defaut', 'valeur' => '10', 'type' => 'number', 'groupe' => 'financier', 'label' => 'Durée par défaut (mois)', 'description' => 'Durée standard d\'attribution de bourse'],
            ['cle' => 'delai_instruction', 'valeur' => '15', 'type' => 'number', 'groupe' => 'delais', 'label' => 'Délai d\'instruction (jours)', 'description' => 'Délai maximum pour instruire un dossier'],
            ['cle' => 'delai_recours', 'valeur' => '30', 'type' => 'number', 'groupe' => 'delais', 'label' => 'Délai de recours (jours)', 'description' => 'Délai accordé pour soumettre un recours après rejet'],
            ['cle' => 'moyenne_min_defaut', 'valeur' => '10', 'type' => 'number', 'groupe' => 'criteres', 'label' => 'Moyenne minimale par défaut', 'description' => 'Moyenne minimale requise par défaut'],
            ['cle' => 'revenu_max_defaut', 'valeur' => '5000', 'type' => 'number', 'groupe' => 'criteres', 'label' => 'Revenu familial max (DH)', 'description' => 'Plafond de revenu familial par défaut'],
            ['cle' => 'email_expediteur', 'valeur' => 'bourses@universite.ma', 'type' => 'email', 'groupe' => 'emails', 'label' => 'Email expéditeur', 'description' => 'Adresse email d\'envoi des notifications'],
            ['cle' => 'nom_etablissement', 'valeur' => 'Université Mohammed V', 'type' => 'text', 'groupe' => 'general', 'label' => 'Nom de l\'établissement', 'description' => 'Nom affiché dans les documents officiels'],
            ['cle' => 'seuil_alerte_budget', 'valeur' => '80', 'type' => 'number', 'groupe' => 'alertes', 'label' => 'Seuil alerte budget (%)', 'description' => 'Pourcentage de consommation déclenchant une alerte'],
            ['cle' => 'taux_assiduite_min', 'valeur' => '75', 'type' => 'number', 'groupe' => 'criteres', 'label' => 'Taux d\'assiduité minimum (%)', 'description' => 'Taux minimum pour le renouvellement'],
        ];

        foreach ($parametres as $p) {
            Parametre::updateOrCreate(['cle' => $p['cle']], $p);
        }
    }
}
