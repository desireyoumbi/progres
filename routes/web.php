<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BeneficiaryController;
use App\Http\Controllers\CollectiveContributionController;
use App\Http\Controllers\IndividualContributionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RotatingContributionController;
use App\Http\Controllers\TontineController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordController;

/*
|--------------------------------------------------------------------------
| Routes Web - PROGRÈS (Plateforme de Tontine & Réunion)
|--------------------------------------------------------------------------
*/

// Redirection de la racine vers la page de connexion
Route::get('/', function () {
    return redirect()->route('login');
});

// Routes d'authentification (Invités uniquement)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});


// modifier le mot de passe 
Route::middleware('auth')->group(function () {
    // Changement obligatoire du mot de passe
    Route::get('/change-password', [PasswordController::class, 'showChangeForm'])
        ->name('password.change');

    Route::put('/change-password', [PasswordController::class, 'updatePassword'])
        ->name('password.update');
});

// Routes protégées par l'authentification
Route::middleware('auth', 'force.password.change')->group(function () {


    // Déconnexion
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Tableau de bord unifié
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Routes réservées au bureau exécutif (Président, Secrétaire, Trésorier, Admin)
    Route::prefix('admin')->name('admin.')->group(function () {

        // Gestion des membres
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Gestion des Tontines
        Route::get('/tontines', [TontineController::class, 'index'])->name('tontines.index');
        Route::post('/tontines', [TontineController::class, 'store'])->name('tontines.store');
        Route::put('/tontines/{tontine}', [TontineController::class, 'update'])->name('tontines.update');
        Route::patch('/tontines/{tontine}/close', [TontineController::class, 'close'])->name('tontines.close');
        Route::get('/tontines/{tontine}/edit', [TontineController::class, 'edit'])->name('tontines.edit');

        // Caisse collective
        Route::resource('collective-contributions', CollectiveContributionController::class)->except(['create', 'edit', 'show']);
        Route::patch('collective-contributions/{collectiveContribution}/approve', [CollectiveContributionController::class, 'approve'])->name('collective-contributions.approve');
        Route::patch('collective-contributions/{collectiveContribution}/reject', [CollectiveContributionController::class, 'reject'])->name('collective-contributions.reject');
        Route::get('/collective-contributions/{collectiveContribution}/edit',[CollectiveContributionController::class, 'edit'])->name('collective-contributions.edit');

        // Caisse individuelle 
        Route::resource('individual-contributions', IndividualContributionController::class)->except(['create', 'show']);
        Route::patch('individual-contributions/{individualContribution}/approve', [IndividualContributionController::class, 'approve'])->name('individual-contributions.approve');
        Route::patch('individual-contributions/{individualContribution}/reject', [IndividualContributionController::class, 'reject'])->name('individual-contributions.reject');

        // Cotisation rotative 
        Route::resource('rotating-contributions', RotatingContributionController::class)->except(['create', 'show']);
        Route::patch('rotating-contributions/{rotatingContribution}/approve', [RotatingContributionController::class, 'approve'])->name('rotating-contributions.approve');
        Route::patch('rotating-contributions/{rotatingContribution}/reject', [RotatingContributionController::class, 'reject'])->name('rotating-contributions.reject');

        // Rapports
        Route::resource('reports', ReportController::class);

// beneficier 
Route::get('/beneficiaries',[BeneficiaryController::class, 'index'])->name('beneficiaries.index');

Route::post('/beneficiaries',[BeneficiaryController::class, 'store'])->name('beneficiaries.store');

Route::patch('/beneficiaries/{beneficiary}/confirm-payment',[BeneficiaryController::class, 'confirmPayment'])->name('beneficiaries.confirm-payment');

Route::patch('/beneficiaries/{beneficiary}/cancel',[BeneficiaryController::class, 'cancel'])->name('beneficiaries.cancel');
    });
});

// Tests - Création de comptes de test
Route::get('/install-test-users', function () {
    User::firstOrCreate(
        ['phone' => '690000001'],
        [
            'name' => 'Désiré Youmbi (Président)',
            'email' => 'president@progres.test',
            'password' => Hash::make('password123'),
            'role' => 'president',
        ]
    );

    User::firstOrCreate(
        ['phone' => '690000003'],
        [
            'name' => 'Jean Calvin (Secrétaire)',
            'email' => 'secretaire@progres.test',
            'password' => Hash::make('password123'),
            'role' => 'secretary',
        ]
    );

    User::firstOrCreate(
        ['phone' => '690000002'],
        [
            'name' => 'Membre Test',
            'email' => 'membre@progres.test',
            'password' => Hash::make('password123'),
            'role' => 'member',
        ]
    );

    return response()->json([
        'message' => 'Comptes de test créés avec succès !',
        'comptes' => [
            ['role' => 'Président', 'telephone' => '690000001', 'password' => 'password123'],
            ['role' => 'Secrétaire', 'telephone' => '690000003', 'password' => 'password123'],
            ['role' => 'Membre', 'telephone' => '690000002', 'password' => 'password123']
        ]
    ]);
});
