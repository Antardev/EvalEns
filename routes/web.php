<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DirectorOnboardingController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\AdminUniversityController;
use App\Http\Controllers\GestionnaireController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionnairePublicController;
use App\Http\Middleware\EnsureIsGestionnaire;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('welcome'))->name('home');

/*
|--------------------------------------------------------------------------
| QuestionnairePublicController — accès anonyme (pas d'auth requise)
|--------------------------------------------------------------------------
*/
Route::controller(QuestionnairePublicController::class)->prefix('q')->name('questionnaire.')->group(function () {
    Route::get('/{token}',  'show')->name('show');
    Route::post('/{token}', 'submit')->name('submit');
});

Route::middleware('guest')->get('/register/form', function () {
    $universities = \App\Models\University::where('statut', 'active')
        ->orderBy('nom')->get(['id', 'nom', 'acronyme']);
    $annexes = \App\Models\Annexe::orderBy('nom')
        ->get(['id', 'university_id', 'nom', 'ville', 'pays']);
    return view('auth.register', compact('universities', 'annexes'));
})->name('register.form');

/*
|--------------------------------------------------------------------------
| ProfileController — partagé tous rôles
|--------------------------------------------------------------------------
*/
Route::middleware('auth')
    ->prefix('profile')->name('profile.')
    ->controller(ProfileController::class)
    ->group(function () {
        Route::get('/',                'show')->name('show');
        Route::put('/info',            'updateInfo')->name('update-info');
        Route::put('/password',        'updatePassword')->name('update-password');
        Route::post('/avatar',         'updateAvatar')->name('update-avatar');
    });

/*
|--------------------------------------------------------------------------
| DirectorOnboardingController
|--------------------------------------------------------------------------
*/
Route::middleware('auth')
    ->prefix('director')->name('director.')
    ->controller(DirectorOnboardingController::class)
    ->group(function () {
        Route::get('/register-university',  'showRegisterUniversity')->name('register-university');
        Route::post('/register-university', 'storeUniversity')->name('store-university');
        Route::get('/pending',              'pending')->name('pending');
    });

/*
|--------------------------------------------------------------------------
| SuperAdminController
|--------------------------------------------------------------------------
*/
Route::middleware('auth')
    ->prefix('superadmin')->name('superadmin.')
    ->controller(SuperAdminController::class)
    ->group(function () {
        Route::get('/', 'dashboard')->name('dashboard');

        Route::get('/inscriptions',                 'inscriptions')->name('inscriptions');
        Route::get('/inscriptions/historique',      'inscriptionsHistorique')->name('inscriptions.historique');
        Route::post('/inscriptions/{id}/approuver', 'approuverInscription')->name('inscriptions.approuver');
        Route::post('/inscriptions/{id}/rejeter',   'rejeterInscription')->name('inscriptions.rejeter');

        Route::get('/universites',          'universites')->name('universites');
        Route::post('/universites',         'creerUniversite')->name('universites.creer');
        Route::put('/universites/{id}',     'modifierUniversite')->name('universites.modifier');
        Route::delete('/universites/{id}',  'supprimerUniversite')->name('universites.supprimer');

        Route::get('/utilisateurs', 'utilisateurs')->name('utilisateurs');

        Route::get('/criteres',  'criteres')->name('criteres');
        Route::post('/criteres', 'saveCriteres')->name('criteres.save');

        Route::get('/statistiques', 'statistiques')->name('statistiques');

        Route::get('/rapports',           'rapports')->name('rapports');
        Route::post('/rapports/exporter', 'exporterRapport')->name('rapports.exporter');

        Route::get('/logs', 'logs')->name('logs');
    });

/*
|--------------------------------------------------------------------------
| AdminUniversityController
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'director.onboarding'])
    ->prefix('adminuniversity')->name('adminuniversity.')
    ->controller(AdminUniversityController::class)
    ->group(function () {
        Route::get('/', 'dashboard')->name('dashboard');

        Route::get('/enseignants',                    'enseignants')->name('enseignants');
        Route::get('/enseignants/{id}/statistiques', 'enseignantStatistiques')->name('enseignants.statistiques');
        Route::post('/enseignants',        'creerEnseignant')->name('enseignants.creer');
        Route::put('/enseignants/{id}',    'modifierEnseignant')->name('enseignants.modifier');
        Route::delete('/enseignants/{id}', 'supprimerEnseignant')->name('enseignants.supprimer');

        Route::get('/periodes',         'periodes')->name('periodes');
        Route::post('/periodes',        'creerPeriode')->name('periodes.creer');
        Route::put('/periodes/{id}',    'modifierPeriode')->name('periodes.modifier');
        Route::delete('/periodes/{id}', 'supprimerPeriode')->name('periodes.supprimer');

        Route::get('/formations',         'formations')->name('formations');
        Route::post('/formations',        'creerFormation')->name('formations.creer');
        Route::put('/formations/{id}',    'modifierFormation')->name('formations.modifier');
        Route::delete('/formations/{id}', 'supprimerFormation')->name('formations.supprimer');

        Route::get('/annexes',                      'annexes')->name('annexes');
        Route::post('/annexes',                     'creerAnnexe')->name('annexes.creer');
        Route::put('/annexes/{id}',                 'modifierAnnexe')->name('annexes.modifier');
        Route::delete('/annexes/{id}',              'supprimerAnnexe')->name('annexes.supprimer');
        Route::post('/annexes/{id}/gestionnaire',   'creerGestionnaire')->name('annexes.gestionnaire.creer');
        Route::delete('/annexes/{id}/gestionnaire', 'supprimerGestionnaire')->name('annexes.gestionnaire.supprimer');

        Route::get('/questionnaires',  'questionnaires')->name('questionnaires');
        Route::post('/questionnaires', 'saveQuestionnaire')->name('questionnaires.save');

        Route::get('/rapports',           'rapports')->name('rapports');
        Route::post('/rapports/exporter', 'exporterRapport')->name('rapports.exporter');
    });

/*
|--------------------------------------------------------------------------
| GestionnaireController
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', EnsureIsGestionnaire::class])
    ->prefix('gestionnaire')->name('gestionnaire.')
    ->controller(GestionnaireController::class)
    ->group(function () {
        Route::get('/',            'dashboard')->name('dashboard');
        Route::get('/enseignants', 'enseignants')->name('enseignants');

        // Liens questionnaires
        Route::get('/liens',                    'liens')->name('liens');
        Route::post('/liens',                   'creerLien')->name('liens.creer');
        Route::post('/liens/{id}/fermer',       'fermerLien')->name('liens.fermer');
        Route::post('/liens/{id}/rafraichir',   'rafraichirLien')->name('liens.rafraichir');
        Route::delete('/liens/{id}',            'supprimerLien')->name('liens.supprimer');
        Route::get('/liens/{id}/reponses',      'voirReponses')->name('liens.reponses');

        Route::get('/questionnaires',  'questionnaires')->name('questionnaires');
        Route::post('/questionnaires', 'saveQuestionnaire')->name('questionnaires.save');
    });

/*
|--------------------------------------------------------------------------
| TeacherController
|--------------------------------------------------------------------------
*/
Route::middleware('auth')
    ->prefix('teacher')->name('teacher.')
    ->controller(TeacherController::class)
    ->group(function () {
        Route::get('/',             'dashboard')->name('dashboard');
        Route::get('/resultats',    'resultats')->name('resultats');
        Route::get('/evolution',    'evolution')->name('evolution');
        Route::get('/commentaires', 'commentaires')->name('commentaires');
        Route::get('/rapport',      'rapport')->name('rapport');
        Route::post('/rapport/exporter', 'exporterRapport')->name('rapport.exporter');
    });

