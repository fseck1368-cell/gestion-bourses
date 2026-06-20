<?php

namespace Database\Seeders;

use App\Models\Alerte;
use App\Models\Budget;
use App\Models\Campagne;
use App\Models\Convention;
use App\Models\CritereEligibilite;
use App\Models\Dossier;
use App\Models\Historique;
use App\Models\Paiement;
use App\Models\RapportAcademique;
use App\Models\Recours;
use App\Models\RendezVous;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (Campagne::where('nom', 'Bourse Excellence 2024-2025')->exists()) {
            echo "Demo data already exists. Skipping.\n";
            return;
        }

        // =============================================
        // INSTRUCTEURS
        // =============================================
        $inst2 = User::firstOrCreate(
            ['email' => 'amadou.ba@bourses.sn'],
            ['nom' => 'Ba', 'prenom' => 'Amadou', 'password' => Hash::make('password'), 'role' => 'instructeur', 'telephone' => '77 123 45 67', 'actif' => true]
        );

        $inst3 = User::firstOrCreate(
            ['email' => 'awa.ndiaye@bourses.sn'],
            ['nom' => 'Ndiaye', 'prenom' => 'Awa', 'password' => Hash::make('password'), 'role' => 'instructeur', 'telephone' => '76 987 65 43', 'actif' => true]
        );

        // =============================================
        // ÉTUDIANTS
        // =============================================
        $etu1 = User::firstOrCreate(
            ['email' => 'ibrahima.fall@univ.sn'],
            ['nom' => 'Fall', 'prenom' => 'Ibrahima', 'password' => Hash::make('password'), 'role' => 'etudiant', 'telephone' => '78 111 22 33', 'numero_etudiant' => 'ETU-2025-010', 'actif' => true]
        );

        $etu2 = User::firstOrCreate(
            ['email' => 'mariama.diallo@univ.sn'],
            ['nom' => 'Diallo', 'prenom' => 'Mariama', 'password' => Hash::make('password'), 'role' => 'etudiant', 'telephone' => '77 444 55 66', 'numero_etudiant' => 'ETU-2025-011', 'actif' => true]
        );

        $etu3 = User::firstOrCreate(
            ['email' => 'ousmane.sy@univ.sn'],
            ['nom' => 'Sy', 'prenom' => 'Ousmane', 'password' => Hash::make('password'), 'role' => 'etudiant', 'telephone' => '76 777 88 99', 'numero_etudiant' => 'ETU-2025-012', 'actif' => true]
        );

        // =============================================
        // CAMPAGNES
        // =============================================
        $campagne1 = Campagne::create([
            'nom' => 'Bourse Excellence 2024-2025',
            'description' => 'Programme de bourses d\'excellence pour les étudiants méritants.',
            'annee_universitaire' => '2024-2025',
            'date_ouverture' => '2024-09-01',
            'date_cloture' => '2025-01-31',
            'active' => false,
        ]);

        $campagne2 = Campagne::create([
            'nom' => 'Bourse Sociale 2025-2026',
            'description' => 'Aide financière pour étudiants en situation précaire.',
            'annee_universitaire' => '2025-2026',
            'date_ouverture' => '2025-09-01',
            'date_cloture' => '2026-01-31',
            'active' => true,
        ]);

        $campagne3 = Campagne::create([
            'nom' => 'Bourse Recherche Master/Doctorat',
            'description' => 'Soutien aux étudiants en cycle de recherche.',
            'annee_universitaire' => '2025-2026',
            'date_ouverture' => '2025-10-01',
            'date_cloture' => '2026-03-31',
            'active' => true,
        ]);

        $campagne4 = Campagne::create([
            'nom' => 'Aide d\'urgence 2026',
            'description' => 'Fonds d\'aide d\'urgence pour situations exceptionnelles.',
            'annee_universitaire' => '2025-2026',
            'date_ouverture' => '2026-01-01',
            'date_cloture' => '2026-12-31',
            'active' => true,
        ]);

        // =============================================
        // BUDGETS
        // =============================================
        Budget::create([
            'campagne_id' => $campagne1->id,
            'libelle' => 'Budget Excellence 2024-2025',
            'montant_alloue' => 500000,
            'montant_consomme' => 320000,
            'annee_universitaire' => '2024-2025',
            'source_financement' => 'Ministère de l\'Enseignement Supérieur',
        ]);

        Budget::create([
            'campagne_id' => $campagne2->id,
            'libelle' => 'Budget Social 2025-2026',
            'montant_alloue' => 800000,
            'montant_consomme' => 150000,
            'annee_universitaire' => '2025-2026',
            'source_financement' => 'Fonds National des Bourses',
        ]);

        Budget::create([
            'campagne_id' => $campagne3->id,
            'libelle' => 'Budget Recherche 2025-2026',
            'montant_alloue' => 300000,
            'montant_consomme' => 45000,
            'annee_universitaire' => '2025-2026',
            'source_financement' => 'Partenariat Universités',
        ]);

        // =============================================
        // CRITÈRES D'ÉLIGIBILITÉ
        // =============================================
        CritereEligibilite::create([
            'campagne_id' => $campagne2->id,
            'nom' => 'Moyenne minimale',
            'description' => 'L\'étudiant doit avoir une moyenne >= 10/20',
            'type' => 'numerique',
            'valeur_min' => 10,
            'valeur_max' => 20,
            'poids' => 3,
            'obligatoire' => true,
            'actif' => true,
        ]);

        CritereEligibilite::create([
            'campagne_id' => $campagne2->id,
            'nom' => 'Revenu familial',
            'description' => 'Revenu mensuel inférieur à 8000 DH',
            'type' => 'numerique',
            'valeur_min' => 0,
            'valeur_max' => 8000,
            'poids' => 4,
            'obligatoire' => true,
            'actif' => true,
        ]);

        CritereEligibilite::create([
            'campagne_id' => $campagne3->id,
            'nom' => 'Niveau Master ou Doctorat',
            'description' => 'Inscription en Master ou Doctorat obligatoire',
            'type' => 'selection',
            'valeurs_acceptees' => ['Master 1', 'Master 2', 'Doctorat'],
            'poids' => 5,
            'obligatoire' => true,
            'actif' => true,
        ]);

        // =============================================
        // DOSSIERS
        // =============================================
        $instructeur1 = User::find(2); // Moussa Diop

        // Dossier 1 - Accepté
        $dossier1 = Dossier::create([
            'reference' => 'BRS-2025-00002',
            'campagne_id' => $campagne1->id,
            'etudiant_id' => $etu1->id,
            'instructeur_id' => $instructeur1->id,
            'statut' => 'accepte',
            'annee_universitaire' => '2024-2025',
            'niveau_etude' => 'Master 1',
            'filiere' => 'Informatique',
            'etablissement' => 'Université Cheikh Anta Diop',
            'moyenne_generale' => 15.5,
            'situation_sociale' => 'modeste',
            'revenu_familial' => 3500,
            'nombre_freres_soeurs' => 4,
            'motif_demande' => 'Je sollicite une bourse pour poursuivre mes études en Master Informatique. Ma famille a des revenus limités.',
            'score_global' => 15.8,
            'avis_instructeur' => 'favorable',
            'commentaire_instructeur' => 'Excellent dossier académique, situation sociale justifiée.',
            'commentaire_admin' => 'Dossier approuvé conformément aux critères.',
            'date_soumission' => '2024-10-05',
            'date_instruction' => '2024-10-12',
            'date_avis_instructeur' => '2024-10-15',
            'avis_transmis_admin' => true,
            'date_decision' => '2024-10-20',
        ]);

        // Dossier 2 - En cours d'instruction
        $dossier2 = Dossier::create([
            'reference' => 'BRS-2025-00003',
            'campagne_id' => $campagne2->id,
            'etudiant_id' => $etu2->id,
            'instructeur_id' => $inst2->id,
            'statut' => 'en_cours_instruction',
            'annee_universitaire' => '2025-2026',
            'niveau_etude' => 'Licence 3',
            'filiere' => 'Droit',
            'etablissement' => 'Université Gaston Berger',
            'moyenne_generale' => 13.2,
            'situation_sociale' => 'famille_nombreuse',
            'revenu_familial' => 4500,
            'nombre_freres_soeurs' => 6,
            'motif_demande' => 'Issue d\'une famille nombreuse, j\'ai besoin d\'un soutien financier pour mes études de droit.',
            'score_global' => 14.2,
            'date_soumission' => '2025-11-15',
            'date_instruction' => '2025-11-20',
        ]);

        // Dossier 3 - Soumis (en attente)
        $dossier3 = Dossier::create([
            'reference' => 'BRS-2025-00004',
            'campagne_id' => $campagne2->id,
            'etudiant_id' => $etu3->id,
            'instructeur_id' => null,
            'statut' => 'soumis',
            'annee_universitaire' => '2025-2026',
            'niveau_etude' => 'Master 2',
            'filiere' => 'Économie',
            'etablissement' => 'FASEG - UCAD',
            'moyenne_generale' => 14.8,
            'situation_sociale' => 'orphelin',
            'revenu_familial' => 2000,
            'nombre_freres_soeurs' => 3,
            'motif_demande' => 'Orphelin de père, je vis avec ma mère qui a un revenu très limité. La bourse me permettrait de finir mon Master.',
            'score_global' => 17.4,
            'date_soumission' => '2026-01-10',
        ]);

        // Dossier 4 - Rejeté
        $dossier4 = Dossier::create([
            'reference' => 'BRS-2025-00005',
            'campagne_id' => $campagne2->id,
            'etudiant_id' => $etu1->id,
            'instructeur_id' => $inst3->id,
            'statut' => 'rejete',
            'annee_universitaire' => '2025-2026',
            'niveau_etude' => 'Licence 2',
            'filiere' => 'Biologie',
            'etablissement' => 'Université de Thiès',
            'moyenne_generale' => 9.5,
            'situation_sociale' => 'modeste',
            'revenu_familial' => 7000,
            'nombre_freres_soeurs' => 2,
            'motif_demande' => 'Demande de soutien pour mes frais de scolarité.',
            'score_global' => 8.6,
            'avis_instructeur' => 'defavorable',
            'commentaire_instructeur' => 'Moyenne insuffisante, revenu familial au-dessus du seuil.',
            'commentaire_admin' => 'Dossier ne répondant pas aux critères minimaux.',
            'date_soumission' => '2025-12-01',
            'date_instruction' => '2025-12-05',
            'date_avis_instructeur' => '2025-12-08',
            'avis_transmis_admin' => true,
            'date_decision' => '2025-12-12',
        ]);

        // Dossier 5 - Soumis
        $dossier5 = Dossier::create([
            'reference' => 'BRS-2026-00006',
            'campagne_id' => $campagne3->id,
            'etudiant_id' => $etu2->id,
            'instructeur_id' => null,
            'statut' => 'soumis',
            'annee_universitaire' => '2025-2026',
            'niveau_etude' => 'Doctorat',
            'filiere' => 'Physique',
            'etablissement' => 'ESP - UCAD',
            'moyenne_generale' => 16.2,
            'situation_sociale' => 'precaire',
            'revenu_familial' => 1500,
            'nombre_freres_soeurs' => 5,
            'motif_demande' => 'Doctorante en physique, je recherche un financement pour ma thèse sur les énergies renouvelables.',
            'score_global' => 18.2,
            'date_soumission' => '2026-02-20',
        ]);

        // =============================================
        // HISTORIQUES
        // =============================================
        foreach ([$dossier1, $dossier2, $dossier3, $dossier4, $dossier5] as $d) {
            Historique::create([
                'dossier_id' => $d->id,
                'user_id' => $d->etudiant_id,
                'action' => 'Soumission du dossier',
                'nouveau_statut' => 'soumis',
                'created_at' => $d->date_soumission,
            ]);
        }

        Historique::create(['dossier_id' => $dossier1->id, 'user_id' => 1, 'action' => 'Assignation à Moussa Diop', 'ancien_statut' => 'soumis', 'nouveau_statut' => 'en_cours_instruction', 'created_at' => '2024-10-12']);
        Historique::create(['dossier_id' => $dossier1->id, 'user_id' => $instructeur1->id, 'action' => 'Avis favorable émis', 'commentaire' => 'Excellent dossier.', 'created_at' => '2024-10-15']);
        Historique::create(['dossier_id' => $dossier1->id, 'user_id' => 1, 'action' => 'Décision administrative : accepté', 'ancien_statut' => 'en_cours_instruction', 'nouveau_statut' => 'accepte', 'created_at' => '2024-10-20']);
        Historique::create(['dossier_id' => $dossier2->id, 'user_id' => 1, 'action' => 'Assignation à Amadou Ba', 'ancien_statut' => 'soumis', 'nouveau_statut' => 'en_cours_instruction', 'created_at' => '2025-11-20']);
        Historique::create(['dossier_id' => $dossier4->id, 'user_id' => 1, 'action' => 'Décision administrative : rejeté', 'ancien_statut' => 'en_cours_instruction', 'nouveau_statut' => 'rejete', 'commentaire' => 'Critères non remplis.', 'created_at' => '2025-12-12']);

        // =============================================
        // CONVENTION
        // =============================================
        $convention = Convention::create([
            'reference' => 'CONV-2025-00001',
            'dossier_id' => $dossier1->id,
            'etudiant_id' => $etu1->id,
            'date_debut' => '2024-11-01',
            'date_fin' => '2025-06-30',
            'montant_mensuel' => 3000,
            'duree_mois' => 8,
            'conditions' => 'Maintenir une moyenne >= 12/20 et un taux d\'assiduité >= 80%.',
            'obligations_etudiant' => 'Fournir un rapport académique chaque semestre.',
            'statut' => 'active',
            'date_signature' => '2024-10-25',
        ]);

        // =============================================
        // PAIEMENTS
        // =============================================
        $mois = ['11/2024', '12/2024', '01/2025', '02/2025', '03/2025', '04/2025'];
        foreach ($mois as $i => $periode) {
            Paiement::create([
                'dossier_id' => $dossier1->id,
                'etudiant_id' => $etu1->id,
                'reference' => 'PAY-2025-' . str_pad($i + 2, 5, '0', STR_PAD_LEFT),
                'montant' => 3000,
                'statut' => 'verse',
                'mode_paiement' => 'virement',
                'banque' => 'CBAO',
                'numero_compte' => 'SN08 1234 5678 9012',
                'date_prevue' => '2024-' . ($i < 2 ? (11 + $i) : '01') . '-05',
                'date_versement' => '2024-' . ($i < 2 ? (11 + $i) : '01') . '-' . rand(5, 10),
                'periode' => $periode,
                'commentaire' => 'Paiement mensuel convention CONV-2025-00001',
            ]);
        }

        // Paiements en attente
        Paiement::create([
            'dossier_id' => $dossier1->id,
            'etudiant_id' => $etu1->id,
            'reference' => 'PAY-2025-00008',
            'montant' => 3000,
            'statut' => 'en_attente',
            'mode_paiement' => 'virement',
            'banque' => 'CBAO',
            'date_prevue' => '2025-05-05',
            'periode' => '05/2025',
        ]);

        Paiement::create([
            'dossier_id' => $dossier1->id,
            'etudiant_id' => $etu1->id,
            'reference' => 'PAY-2025-00009',
            'montant' => 3000,
            'statut' => 'en_attente',
            'mode_paiement' => 'virement',
            'banque' => 'CBAO',
            'date_prevue' => '2025-06-05',
            'periode' => '06/2025',
        ]);

        // =============================================
        // RAPPORTS ACADÉMIQUES
        // =============================================
        RapportAcademique::create([
            'dossier_id' => $dossier1->id,
            'etudiant_id' => $etu1->id,
            'convention_id' => $convention->id,
            'semestre' => 'S1',
            'annee_universitaire' => '2024-2025',
            'moyenne' => 15.2,
            'credits_valides' => 30,
            'credits_total' => 30,
            'taux_assiduite' => 95,
            'statut_academique' => 'bon',
            'renouvellement_recommande' => true,
            'observations' => 'Excellent semestre, tous les modules validés.',
        ]);

        RapportAcademique::create([
            'dossier_id' => $dossier1->id,
            'etudiant_id' => $etu1->id,
            'convention_id' => $convention->id,
            'semestre' => 'S2',
            'annee_universitaire' => '2024-2025',
            'moyenne' => 14.8,
            'credits_valides' => 28,
            'credits_total' => 30,
            'taux_assiduite' => 88,
            'statut_academique' => 'bon',
            'renouvellement_recommande' => true,
            'observations' => 'Bon semestre, 2 modules à rattraper en session 2.',
        ]);

        // =============================================
        // RECOURS
        // =============================================
        Recours::create([
            'reference' => 'REC-2025-00001',
            'dossier_id' => $dossier4->id,
            'etudiant_id' => $etu1->id,
            'motif' => 'Contestation du rejet',
            'justification' => 'Ma situation familiale a changé depuis la soumission. Mon père a perdu son emploi et notre revenu a considérablement baissé. Je joins les justificatifs.',
            'statut' => 'soumis',
            'date_soumission' => '2025-12-20',
        ]);

        // =============================================
        // RENDEZ-VOUS
        // =============================================
        RendezVous::create([
            'dossier_id' => $dossier2->id,
            'etudiant_id' => $etu2->id,
            'instructeur_id' => $inst2->id,
            'date_heure' => now()->addDays(3)->setHour(10)->setMinute(0),
            'lieu' => 'Bureau 204, Bâtiment A',
            'motif' => 'Discussion sur les pièces justificatives du dossier',
            'statut' => 'confirme',
        ]);

        RendezVous::create([
            'dossier_id' => $dossier2->id,
            'etudiant_id' => $etu2->id,
            'instructeur_id' => $inst2->id,
            'date_heure' => now()->addDays(7)->setHour(14)->setMinute(30),
            'lieu' => 'Salle de réunion, RDC',
            'motif' => 'Entretien complémentaire',
            'statut' => 'demande',
        ]);

        // =============================================
        // ALERTES
        // =============================================
        Alerte::create([
            'user_id' => 1,
            'type' => 'info',
            'titre' => 'Nouvelle campagne ouverte',
            'message' => 'La campagne "Bourse Sociale 2025-2026" est maintenant ouverte aux candidatures.',
            'niveau' => 'info',
            'lue' => false,
        ]);

        Alerte::create([
            'user_id' => 1,
            'type' => 'warning',
            'titre' => 'Dossiers en attente',
            'message' => '2 dossiers sont en attente d\'assignation depuis plus de 5 jours.',
            'niveau' => 'warning',
            'lien' => '/admin/dossiers?statut=soumis',
            'lue' => false,
        ]);

        Alerte::create([
            'user_id' => $etu1->id,
            'type' => 'success',
            'titre' => 'Paiement reçu',
            'message' => 'Votre paiement de 3 000 DH pour la période 04/2025 a été versé.',
            'niveau' => 'success',
            'lue' => false,
        ]);
    }
}
