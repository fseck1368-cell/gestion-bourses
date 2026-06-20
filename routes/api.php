<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DossierController;
use App\Http\Controllers\Api\CampagneController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CommissionController;
use App\Http\Controllers\Api\PaiementController;
use App\Http\Controllers\Api\BudgetController;
use App\Http\Controllers\Api\CritereController;
use App\Http\Controllers\Api\EvaluationController;
use App\Http\Controllers\Api\ConventionController;
use App\Http\Controllers\Api\RecoursController;
use App\Http\Controllers\Api\RapportAcademiqueController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\RendezVousController;
use App\Http\Controllers\Api\AlerteController;
use App\Http\Controllers\Api\ApprobationController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\StatistiqueController;
use App\Http\Controllers\Api\ParametreController;
use App\Http\Controllers\Api\ScoringController;
use App\Http\Controllers\Api\EligibiliteController;
use App\Http\Controllers\Api\FinanceController;
use App\Http\Controllers\Api\WorkflowController;
use App\Http\Controllers\Api\AcademiqueController;
use App\Http\Controllers\Api\AuditController;
use App\Http\Controllers\Api\LockController;

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

/*
|--------------------------------------------------------------------------
| Protected Routes (auth:sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Dashboard
    Route::get('/dashboard', [StatistiqueController::class, 'dashboard']);

    /*
    |----------------------------------------------------------------------
    | Dossiers
    |----------------------------------------------------------------------
    */
    Route::get('/dossiers', [DossierController::class, 'index']);
    Route::post('/dossiers', [DossierController::class, 'store']);
    Route::get('/dossiers/{dossier}', [DossierController::class, 'show']);
    Route::put('/dossiers/{dossier}', [DossierController::class, 'update']);

    // Dossier - Instructeur actions
    Route::post('/dossiers/{dossier}/avis', [DossierController::class, 'donnerAvis']);
    Route::post('/dossiers/{dossier}/demande-complement', [DossierController::class, 'demanderComplement']);

    // Dossier - Etudiant actions
    Route::post('/dossiers/{dossier}/repondre-complement', [DossierController::class, 'repondreComplement']);

    // Dossier - Admin actions
    Route::post('/dossiers/{dossier}/assigner', [DossierController::class, 'assigner']);
    Route::post('/dossiers/{dossier}/transferer', [DossierController::class, 'transferer']);
    Route::post('/dossiers/{dossier}/decision', [DossierController::class, 'decision']);

    /*
    |----------------------------------------------------------------------
    | Scoring & Éligibilité
    |----------------------------------------------------------------------
    */
    Route::get('/dossiers/{dossier}/scoring', [ScoringController::class, 'calculer']);
    Route::get('/eligibilite/verifier', [EligibiliteController::class, 'verifier']);
    Route::get('/eligibilite/quota', [EligibiliteController::class, 'quotaFiliere']);

    /*
    |----------------------------------------------------------------------
    | Verrouillage (Locks)
    |----------------------------------------------------------------------
    */
    Route::post('/dossiers/{dossier}/lock', [LockController::class, 'verrouiller']);
    Route::post('/dossiers/{dossier}/unlock', [LockController::class, 'deverrouiller']);
    Route::get('/dossiers/{dossier}/lock-status', [LockController::class, 'statut']);

    /*
    |----------------------------------------------------------------------
    | Documents
    |----------------------------------------------------------------------
    */
    Route::get('/dossiers/{dossier}/documents', [DocumentController::class, 'index']);
    Route::post('/dossiers/{dossier}/documents', [DocumentController::class, 'store']);
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy']);

    /*
    |----------------------------------------------------------------------
    | Messages / Chat
    |----------------------------------------------------------------------
    */
    Route::get('/dossiers/{dossier}/messages', [MessageController::class, 'index']);
    Route::post('/dossiers/{dossier}/messages', [MessageController::class, 'store']);
    Route::post('/dossiers/{dossier}/messages/read', [MessageController::class, 'markRead']);

    /*
    |----------------------------------------------------------------------
    | Recours (Appeals)
    |----------------------------------------------------------------------
    */
    Route::get('/recours', [RecoursController::class, 'index']);
    Route::post('/dossiers/{dossier}/recours', [RecoursController::class, 'store']);
    Route::get('/recours/{recour}', [RecoursController::class, 'show']);
    Route::post('/recours/{recour}/traiter', [RecoursController::class, 'traiter']);

    /*
    |----------------------------------------------------------------------
    | Rendez-vous (Appointments)
    |----------------------------------------------------------------------
    */
    Route::get('/rendez-vous', [RendezVousController::class, 'index']);
    Route::post('/dossiers/{dossier}/rendez-vous', [RendezVousController::class, 'store']);
    Route::post('/rendez-vous/{rendezVous}/confirmer', [RendezVousController::class, 'confirmer']);
    Route::post('/rendez-vous/{rendezVous}/refuser', [RendezVousController::class, 'refuser']);
    Route::post('/rendez-vous/{rendezVous}/terminer', [RendezVousController::class, 'terminer']);

    /*
    |----------------------------------------------------------------------
    | Alertes (Notifications)
    |----------------------------------------------------------------------
    */
    Route::get('/alertes', [AlerteController::class, 'index']);
    Route::get('/alertes/non-lues', [AlerteController::class, 'nonLues']);
    Route::post('/alertes/{alerte}/lue', [AlerteController::class, 'marquerLue']);
    Route::post('/alertes/lire-tout', [AlerteController::class, 'marquerToutesLues']);

    /*
    |----------------------------------------------------------------------
    | Académique (progression, fiabilité)
    |----------------------------------------------------------------------
    */
    Route::get('/academique/progression', [AcademiqueController::class, 'progression']);
    Route::get('/academique/fiabilite', [AcademiqueController::class, 'fiabilite']);

    /*
    |----------------------------------------------------------------------
    | Admin Routes
    |----------------------------------------------------------------------
    */
    Route::middleware('role:administrateur')->prefix('admin')->group(function () {

        // Users Management
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::post('/users/{user}/toggle', [UserController::class, 'toggleActif']);

        // Campagnes
        Route::get('/campagnes', [CampagneController::class, 'index']);
        Route::post('/campagnes', [CampagneController::class, 'store']);
        Route::get('/campagnes/{campagne}', [CampagneController::class, 'show']);
        Route::put('/campagnes/{campagne}', [CampagneController::class, 'update']);
        Route::post('/campagnes/{campagne}/toggle', [CampagneController::class, 'toggleActive']);

        // Commissions
        Route::get('/commissions', [CommissionController::class, 'index']);
        Route::post('/commissions', [CommissionController::class, 'store']);
        Route::get('/commissions/{commission}', [CommissionController::class, 'show']);
        Route::post('/commissions/{commission}/demarrer', [CommissionController::class, 'demarrer']);
        Route::post('/commissions/{commission}/terminer', [CommissionController::class, 'terminer']);
        Route::post('/commissions/{commission}/voter', [CommissionController::class, 'voter']);

        // Paiements
        Route::get('/paiements', [PaiementController::class, 'index']);
        Route::post('/paiements', [PaiementController::class, 'store']);
        Route::get('/paiements/{paiement}', [PaiementController::class, 'show']);
        Route::post('/paiements/{paiement}/valider', [PaiementController::class, 'valider']);
        Route::post('/paiements/{paiement}/verser', [PaiementController::class, 'verser']);
        Route::post('/paiements/{paiement}/annuler', [PaiementController::class, 'annuler']);

        // Budgets
        Route::get('/budgets', [BudgetController::class, 'index']);
        Route::post('/budgets', [BudgetController::class, 'store']);
        Route::get('/budgets/{budget}', [BudgetController::class, 'show']);
        Route::put('/budgets/{budget}', [BudgetController::class, 'update']);

        // Critères d'éligibilité
        Route::get('/criteres', [CritereController::class, 'index']);
        Route::post('/criteres', [CritereController::class, 'store']);
        Route::get('/criteres/{critere}', [CritereController::class, 'show']);
        Route::put('/criteres/{critere}', [CritereController::class, 'update']);
        Route::post('/criteres/{critere}/toggle', [CritereController::class, 'toggleActif']);

        // Evaluations
        Route::get('/dossiers/{dossier}/evaluations', [EvaluationController::class, 'index']);
        Route::post('/dossiers/{dossier}/evaluations', [EvaluationController::class, 'store']);

        // Conventions
        Route::get('/conventions', [ConventionController::class, 'index']);
        Route::post('/conventions', [ConventionController::class, 'store']);
        Route::get('/conventions/{convention}', [ConventionController::class, 'show']);
        Route::post('/conventions/{convention}/activer', [ConventionController::class, 'activer']);
        Route::post('/conventions/{convention}/suspendre', [ConventionController::class, 'suspendre']);
        Route::post('/conventions/{convention}/resilier', [ConventionController::class, 'resilier']);
        Route::get('/conventions/{convention}/renouvellement', [AcademiqueController::class, 'evaluerRenouvellement']);

        // Rapports académiques
        Route::get('/rapports', [RapportAcademiqueController::class, 'index']);
        Route::post('/rapports', [RapportAcademiqueController::class, 'store']);
        Route::get('/rapports/{rapport}', [RapportAcademiqueController::class, 'show']);

        // Approbations
        Route::get('/approbations', [ApprobationController::class, 'index']);
        Route::post('/dossiers/{dossier}/approbations', [ApprobationController::class, 'store']);
        Route::post('/approbations/{approbation}/approuver', [ApprobationController::class, 'approuver']);
        Route::post('/approbations/{approbation}/rejeter', [ApprobationController::class, 'rejeter']);

        // Statistiques
        Route::get('/statistiques', [StatistiqueController::class, 'index']);

        // Paramètres
        Route::get('/parametres', [ParametreController::class, 'index']);
        Route::put('/parametres', [ParametreController::class, 'update']);

        // Scoring & Classement
        Route::get('/scoring/classement', [ScoringController::class, 'classement']);

        // Finance
        Route::get('/finance/plafond/{user}', [FinanceController::class, 'plafondCumul']);
        Route::get('/finance/anomalies/{user}', [FinanceController::class, 'anomalies']);
        Route::post('/finance/verifier-montant', [FinanceController::class, 'verifierMontant']);

        // Workflow
        Route::get('/workflow/priorite/{dossier}', [WorkflowController::class, 'priorite']);
        Route::get('/workflow/instructeur-optimal', [WorkflowController::class, 'instructeurOptimal']);
        Route::get('/workflow/escalade', [WorkflowController::class, 'dossiersEscalade']);
        Route::post('/workflow/assigner-auto', [WorkflowController::class, 'assignerAuto']);

        // Académique (admin)
        Route::get('/academique/progression/{user}', [AcademiqueController::class, 'progression']);
        Route::get('/academique/fiabilite/{user}', [AcademiqueController::class, 'fiabilite']);

        // Audit
        Route::get('/audit', [AuditController::class, 'index']);
        Route::get('/audit/dossier/{dossier}', [AuditController::class, 'dossier']);
        Route::get('/audit/utilisateur/{user}', [AuditController::class, 'utilisateur']);
    });
});
