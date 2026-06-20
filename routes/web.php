<?php

use App\Http\Controllers\Admin\AdminDossierController;
use App\Http\Controllers\Admin\AlerteController;
use App\Http\Controllers\Admin\ApprobationController;
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\BudgetController;
use App\Http\Controllers\Admin\CampagneController;
use App\Http\Controllers\Admin\CommissionController;
use App\Http\Controllers\Admin\ConventionController;
use App\Http\Controllers\Admin\CritereController;
use App\Http\Controllers\Admin\EvaluationController;
use App\Http\Controllers\Admin\PaiementController;
use App\Http\Controllers\Admin\ParametreController;
use App\Http\Controllers\Admin\RapportAcademiqueController;
use App\Http\Controllers\Admin\RecoursController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Etudiant\ComplementController;
use App\Http\Controllers\Etudiant\DossierController;
use App\Http\Controllers\Etudiant\RecoursController as EtudiantRecoursController;
use App\Http\Controllers\Etudiant\RendezVousController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\Instructeur\InstructionController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RechercheController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\LangueController;
use App\Http\Controllers\StatistiqueController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Langue switch
Route::post('/langue/{locale}', [LangueController::class, 'switch'])->name('langue.switch');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Recherche globale
    Route::get('/recherche', [RechercheController::class, 'index'])->name('recherche');

    // Chat temps réel
    Route::get('/chat/{dossier}', [ChatController::class, 'show'])->name('chat.show');
    Route::get('/chat/{dossier}/messages', [ChatController::class, 'messages'])->name('chat.messages');
    Route::post('/chat/{dossier}/send', [ChatController::class, 'send'])->name('chat.send');
    Route::post('/chat/{dossier}/read', [ChatController::class, 'markRead'])->name('chat.read');

    // Messages (accessible par tous les rôles)
    Route::post('/dossiers/{dossier}/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::post('/messages/{message}/lu', [MessageController::class, 'marquerLu'])->name('messages.lu');

    // Notifications
    Route::get('/notifications', function () {
        return view('notifications.index', [
            'notifications' => auth()->user()->notifications()->paginate(20),
        ]);
    })->name('notifications.index');
    Route::post('/notifications/lire-tout', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Notifications marquées comme lues.');
    })->name('notifications.lire-tout');
});

// Routes Etudiant
Route::middleware(['auth', 'role:etudiant'])->prefix('etudiant')->name('etudiant.')->group(function () {
    Route::get('/dossiers/create', [DossierController::class, 'create'])->name('dossiers.create');
    Route::post('/dossiers', [DossierController::class, 'store'])->name('dossiers.store');
    Route::get('/dossiers/{dossier}', [DossierController::class, 'show'])->name('dossiers.show');
    Route::get('/dossiers/{dossier}/edit', [DossierController::class, 'edit'])->name('dossiers.edit');
    Route::put('/dossiers/{dossier}', [DossierController::class, 'update'])->name('dossiers.update');
    Route::delete('/documents/{document}', [DossierController::class, 'destroyDocument'])->name('documents.destroy');
    Route::get('/dossiers/{dossier}/recepisse', [ExportController::class, 'recepisse'])->name('dossiers.recepisse');

    // Recours étudiant
    Route::get('/dossiers/{dossier}/recours', [EtudiantRecoursController::class, 'create'])->name('recours.create');
    Route::post('/dossiers/{dossier}/recours', [EtudiantRecoursController::class, 'store'])->name('recours.store');

    // Complément
    Route::post('/dossiers/{dossier}/complement', [ComplementController::class, 'repondre'])->name('complement.repondre');

    // Rendez-vous étudiant
    Route::post('/dossiers/{dossier}/rendez-vous', [RendezVousController::class, 'store'])->name('rendez-vous.store');

    // Exports étudiant
    Route::get('/releve-paiements', [ExportController::class, 'relevePaiements'])->name('releve-paiements');
});

// Routes Instructeur
Route::middleware(['auth', 'role:instructeur'])->prefix('instructeur')->name('instructeur.')->group(function () {
    Route::get('/dossiers', [InstructionController::class, 'index'])->name('dossiers.index');
    Route::get('/dossiers/{dossier}', [InstructionController::class, 'show'])->name('dossiers.show');
    Route::post('/dossiers/{dossier}/avis', [InstructionController::class, 'donnerAvis'])->name('dossiers.avis');
    Route::post('/dossiers/{dossier}/complement', [InstructionController::class, 'demanderComplement'])->name('dossiers.complement');

    // Rendez-vous instructeur
    Route::get('/rendez-vous', [InstructionController::class, 'rendezVous'])->name('rendez-vous.index');
    Route::post('/rendez-vous/{rendezVous}/confirmer', [InstructionController::class, 'confirmerRdv'])->name('rendez-vous.confirmer');
    Route::post('/rendez-vous/{rendezVous}/refuser', [InstructionController::class, 'refuserRdv'])->name('rendez-vous.refuser');
    Route::post('/rendez-vous/{rendezVous}/terminer', [InstructionController::class, 'terminerRdv'])->name('rendez-vous.terminer');
});

// Routes Administrateur
Route::middleware(['auth', 'role:administrateur'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dossiers', [AdminDossierController::class, 'index'])->name('dossiers.index');
    Route::get('/dossiers/{dossier}', [AdminDossierController::class, 'show'])->name('dossiers.show');
    Route::post('/dossiers/{dossier}/assigner', [AdminDossierController::class, 'assigner'])->name('dossiers.assigner');
    Route::post('/dossiers/assigner-masse', [AdminDossierController::class, 'assignerMasse'])->name('dossiers.assigner.masse');
    Route::post('/dossiers/{dossier}/transferer', [AdminDossierController::class, 'transferer'])->name('dossiers.transferer');
    Route::post('/dossiers/{dossier}/decision', [AdminDossierController::class, 'decision'])->name('dossiers.decision');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/toggle', [UserController::class, 'toggleActif'])->name('users.toggle');

    // Campagnes
    Route::get('/campagnes', [CampagneController::class, 'index'])->name('campagnes.index');
    Route::get('/campagnes/create', [CampagneController::class, 'create'])->name('campagnes.create');
    Route::post('/campagnes', [CampagneController::class, 'store'])->name('campagnes.store');
    Route::get('/campagnes/{campagne}/edit', [CampagneController::class, 'edit'])->name('campagnes.edit');
    Route::put('/campagnes/{campagne}', [CampagneController::class, 'update'])->name('campagnes.update');
    Route::post('/campagnes/{campagne}/toggle', [CampagneController::class, 'toggleActive'])->name('campagnes.toggle');

    // Commissions
    Route::get('/commissions', [CommissionController::class, 'index'])->name('commissions.index');
    Route::get('/commissions/create', [CommissionController::class, 'create'])->name('commissions.create');
    Route::post('/commissions', [CommissionController::class, 'store'])->name('commissions.store');
    Route::get('/commissions/{commission}', [CommissionController::class, 'show'])->name('commissions.show');
    Route::post('/commissions/{commission}/demarrer', [CommissionController::class, 'demarrer'])->name('commissions.demarrer');
    Route::post('/commissions/{commission}/terminer', [CommissionController::class, 'terminer'])->name('commissions.terminer');
    Route::post('/commissions/{commission}/voter', [CommissionController::class, 'voter'])->name('commissions.voter');

    // Paiements
    Route::get('/paiements', [PaiementController::class, 'index'])->name('paiements.index');
    Route::get('/paiements/create', [PaiementController::class, 'create'])->name('paiements.create');
    Route::post('/paiements', [PaiementController::class, 'store'])->name('paiements.store');
    Route::get('/paiements/{paiement}', [PaiementController::class, 'show'])->name('paiements.show');
    Route::post('/paiements/{paiement}/valider', [PaiementController::class, 'valider'])->name('paiements.valider');
    Route::post('/paiements/{paiement}/verser', [PaiementController::class, 'verser'])->name('paiements.verser');
    Route::post('/paiements/{paiement}/annuler', [PaiementController::class, 'annuler'])->name('paiements.annuler');

    // Critères d'éligibilité
    Route::get('/criteres', [CritereController::class, 'index'])->name('criteres.index');
    Route::get('/criteres/create', [CritereController::class, 'create'])->name('criteres.create');
    Route::post('/criteres', [CritereController::class, 'store'])->name('criteres.store');
    Route::get('/criteres/{critere}/edit', [CritereController::class, 'edit'])->name('criteres.edit');
    Route::put('/criteres/{critere}', [CritereController::class, 'update'])->name('criteres.update');
    Route::post('/criteres/{critere}/toggle', [CritereController::class, 'toggleActif'])->name('criteres.toggle');

    // Évaluations
    Route::get('/evaluations', [EvaluationController::class, 'index'])->name('evaluations.index');
    Route::get('/evaluations/{dossier}', [EvaluationController::class, 'evaluer'])->name('evaluations.evaluer');
    Route::post('/evaluations/{dossier}', [EvaluationController::class, 'store'])->name('evaluations.store');

    // Budgets
    Route::get('/budgets', [BudgetController::class, 'index'])->name('budgets.index');
    Route::get('/budgets/create', [BudgetController::class, 'create'])->name('budgets.create');
    Route::post('/budgets', [BudgetController::class, 'store'])->name('budgets.store');
    Route::get('/budgets/{budget}/edit', [BudgetController::class, 'edit'])->name('budgets.edit');
    Route::put('/budgets/{budget}', [BudgetController::class, 'update'])->name('budgets.update');

    // Recours
    Route::get('/recours', [RecoursController::class, 'index'])->name('recours.index');
    Route::get('/recours/{recour}', [RecoursController::class, 'show'])->name('recours.show');
    Route::post('/recours/{recour}/traiter', [RecoursController::class, 'traiter'])->name('recours.traiter');

    // Conventions
    Route::get('/conventions', [ConventionController::class, 'index'])->name('conventions.index');
    Route::get('/conventions/create', [ConventionController::class, 'create'])->name('conventions.create');
    Route::post('/conventions', [ConventionController::class, 'store'])->name('conventions.store');
    Route::get('/conventions/{convention}', [ConventionController::class, 'show'])->name('conventions.show');
    Route::post('/conventions/{convention}/activer', [ConventionController::class, 'activer'])->name('conventions.activer');
    Route::post('/conventions/{convention}/suspendre', [ConventionController::class, 'suspendre'])->name('conventions.suspendre');
    Route::post('/conventions/{convention}/resilier', [ConventionController::class, 'resilier'])->name('conventions.resilier');

    // Rapports académiques
    Route::get('/rapports', [RapportAcademiqueController::class, 'index'])->name('rapports.index');
    Route::get('/rapports/create', [RapportAcademiqueController::class, 'create'])->name('rapports.create');
    Route::post('/rapports', [RapportAcademiqueController::class, 'store'])->name('rapports.store');
    Route::get('/rapports/{rapport}', [RapportAcademiqueController::class, 'show'])->name('rapports.show');

    // Alertes
    Route::get('/alertes', [AlerteController::class, 'index'])->name('alertes.index');
    Route::post('/alertes/{alerte}/lue', [AlerteController::class, 'marquerLue'])->name('alertes.lue');
    Route::post('/alertes/lire-tout', [AlerteController::class, 'marquerToutesLues'])->name('alertes.lire-tout');

    // Paramètres
    Route::get('/parametres', [ParametreController::class, 'index'])->name('parametres.index');
    Route::put('/parametres', [ParametreController::class, 'update'])->name('parametres.update');

    // Audit
    Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');

    // Approbations
    Route::get('/approbations', [ApprobationController::class, 'index'])->name('approbations.index');
    Route::get('/approbations/{dossier}/configurer', [ApprobationController::class, 'configurer'])->name('approbations.configurer');
    Route::post('/approbations/{dossier}', [ApprobationController::class, 'store'])->name('approbations.store');
    Route::post('/approbations/{approbation}/approuver', [ApprobationController::class, 'approuver'])->name('approbations.approuver');
    Route::post('/approbations/{approbation}/rejeter', [ApprobationController::class, 'rejeter'])->name('approbations.rejeter');

    // Statistiques
    Route::get('/statistiques', [StatistiqueController::class, 'index'])->name('statistiques');

    // Import
    Route::get('/import', [ImportController::class, 'index'])->name('import.index');
    Route::post('/import', [ImportController::class, 'store'])->name('import.store');
    Route::get('/import/template', [ImportController::class, 'template'])->name('import.template');

    // Exports
    Route::get('/export/rapport', [ExportController::class, 'rapportAdmin'])->name('export.rapport');
    Route::get('/export/csv', [ExportController::class, 'exportCsv'])->name('export.csv');
    Route::get('/export/convention/{convention}', [ExportController::class, 'convention'])->name('export.convention');
    Route::get('/export/attestation/{dossier}', [ExportController::class, 'attestation'])->name('export.attestation');
});

require __DIR__.'/auth.php';
