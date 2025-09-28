<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ParametresController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\PersonnelController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Route temporaire pour tester les notifications
Route::get('/test-notifications', function () {
    return view('mensualites.test-notifications');
});



// Route pour tester les nouvelles fonctionnalités mensualités
Route::get('/test-mensualites', function () {
    return view('test-mensualites');
});

// Route pour tester la recherche d'élèves
Route::get('/test-recherche', function () {
    return view('test-recherche');
});

// Route simple pour test rapide
Route::get('/test-simple', function () {
    return view('test-simple');
});



Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
        // Routes pour les paramètres
    Route::middleware(['auth'])->prefix('parametres')->name('parametres.')->group(function () {
        Route::get('/', [ParametresController::class, 'index'])->name('index');
        
        // Gestion des établissements
        Route::post('/etablissement', [ParametresController::class, 'storeEtablissement'])->name('etablissement.store');
        Route::put('/etablissement/{etablissement}', [ParametresController::class, 'updateEtablissement'])->name('etablissement.update');
        
        // Gestion des niveaux
        Route::post('/niveaux', [ParametresController::class, 'storeNiveau'])->name('niveaux.store');
        Route::put('/niveaux/{niveau}', [ParametresController::class, 'updateNiveau'])->name('niveaux.update');
        Route::delete('/niveaux/{niveau}', [ParametresController::class, 'destroyNiveau'])->name('niveaux.destroy');
        
        // Gestion des classes
        Route::post('/classes', [ParametresController::class, 'storeClasse'])->name('classes.store');
        Route::put('/classes/{classe}', [ParametresController::class, 'updateClasse'])->name('classes.update');
        Route::delete('/classes/{classe}', [ParametresController::class, 'destroyClasse'])->name('classes.destroy');
        
        // Gestion des frais
        Route::post('/frais', [ParametresController::class, 'storeFrais'])->name('frais.store');
        Route::put('/frais/{frais}', [ParametresController::class, 'updateFrais'])->name('frais.update');
        Route::delete('/frais/{frais}', [ParametresController::class, 'destroyFrais'])->name('frais.destroy');
        
        // Route de debug
        Route::get('/debug-frais', function() {
            $anneeActive = \App\Models\AnneeScolaire::getActive();
            echo "Année active: " . $anneeActive->nom . " (ID: " . $anneeActive->id . ")<br><br>";
            
            echo "Frais de mensualité configurés:<br>";
            $frais = \App\Models\Frais::where('type', 'mensualite')->where('actif', true)->get();
            foreach($frais as $f) {
                $niveau = \App\Models\Niveau::find($f->niveau_id);
                echo "- Niveau: " . ($niveau ? $niveau->nom : 'N/A') . " | Montant: " . $f->montant . " | Année: " . $f->annee_scolaire_id . "<br>";
            }
            
            echo "<br>Test pour élève ID 4:<br>";
            $inscription = \App\Models\Inscription::find(4);
            if($inscription) {
                echo "- Niveau de l'élève: " . $inscription->niveau_id . "<br>";
                $fraisTrouve = \App\Models\Frais::where('niveau_id', $inscription->niveau_id)
                    ->where('type', 'mensualite')
                    ->where('annee_scolaire_id', $anneeActive->id)
                    ->where('actif', true)
                    ->first();
                echo "- Frais trouvé: " . ($fraisTrouve ? $fraisTrouve->montant : 'AUCUN') . "<br>";
            }
        });
        
        // Gestion des utilisateurs
        Route::post('/users', [ParametresController::class, 'storeUser'])->name('users.store');
        Route::put('/users/{user}', [ParametresController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [ParametresController::class, 'destroyUser'])->name('users.destroy');
        
        // Gestion des années scolaires
        Route::post('/annees-scolaires', [ParametresController::class, 'storeAnneeScolaire'])->name('annees-scolaires.store');
        Route::put('/annees-scolaires/{anneeScolaire}', [ParametresController::class, 'updateAnneeScolaire'])->name('annees-scolaires.update');
        Route::post('/annees-scolaires/{anneeScolaire}/activer', [ParametresController::class, 'activerAnneeScolaire'])->name('annees-scolaires.activer');
        Route::delete('/annees-scolaires/{anneeScolaire}', [ParametresController::class, 'destroyAnneeScolaire'])->name('annees-scolaires.destroy');
    });
    
    // Routes pour le module inscriptions
    Route::middleware(['auth'])->prefix('inscriptions')->name('inscriptions.')->group(function () {
        Route::get('/', [InscriptionController::class, 'index'])->name('index');
        
        // Routes pour pré-inscriptions
        Route::post('/pre-inscription', [InscriptionController::class, 'storePreInscription'])->name('pre-inscription.store');
        Route::put('/pre-inscription/{preInscription}', [InscriptionController::class, 'updatePreInscription'])->name('pre-inscription.update');
        Route::delete('/pre-inscription/{preInscription}', [InscriptionController::class, 'destroyPreInscription'])->name('destroy-pre');
        
        // Routes pour finaliser inscriptions
        Route::post('/finaliser', [InscriptionController::class, 'finaliserInscription'])->name('finaliser');
        
        // Routes pour édition des inscriptions
        Route::put('/inscription/{inscription}', [InscriptionController::class, 'updateInscription'])->name('inscription.update');
        Route::delete('/inscription/{inscription}', [InscriptionController::class, 'deleteInscription'])->name('inscription.delete');
        Route::post('/inscription/{inscription}/annuler', [InscriptionController::class, 'annulerInscription'])->name('inscription.annuler');
        
        // Routes AJAX
        Route::get('/search', [InscriptionController::class, 'searchPreInscriptions'])->name('search');
        Route::get('/frais-niveau/{niveau}', [InscriptionController::class, 'getFraisNiveau'])->name('frais-niveau');
        
        // Route pour reçu
        Route::get('/recu/{inscription}', [InscriptionController::class, 'recu'])->name('recu');
        
        // Route pour la liste administrative PDF
        Route::get('/liste-administrative-pdf', [InscriptionController::class, 'listeAdministrativePdf'])->name('liste-administrative.pdf');
        
        // Route pour l'aperçu de la liste d'appel
        Route::get('/liste-appel', [InscriptionController::class, 'listeAppel'])->name('liste-appel');
        
        // Routes pour les rapports des inscriptions
        Route::get('/rapports', [InscriptionController::class, 'rapports'])->name('rapports');
        Route::get('/rapports-data', [InscriptionController::class, 'getRapportsInscriptions'])->name('rapports.data');
        Route::get('/rapports/apercu', [InscriptionController::class, 'rapportsApercu'])->name('rapports.apercu.inscriptions');
        
        // Routes AJAX pour les filtres
        Route::get('/annees-scolaires', [InscriptionController::class, 'anneesScolaires'])->name('annees-scolaires');
        Route::get('/niveaux', [InscriptionController::class, 'niveaux'])->name('niveaux');
        
        // Route pour l'export des inscriptions
        Route::get('/export-inscriptions', [InscriptionController::class, 'exportInscriptions'])->name('export-inscriptions');
        
        // Route pour la fiche élève
        Route::get('/fiche-eleve/{inscription}', [InscriptionController::class, 'ficheEleve'])->name('fiche-eleve');
    });

    // Routes pour le module mensualités
    Route::middleware(['auth'])->prefix('mensualites')->name('mensualites.')->group(function () {
        Route::get('/', [App\Http\Controllers\MensualiteController::class, 'index'])->name('index');
        
        // Routes AJAX
        Route::get('/search-eleves', [App\Http\Controllers\MensualiteController::class, 'searchEleves'])->name('search-eleves');
        
        // Routes pour paiements
        Route::post('/enregistrer-paiement', [App\Http\Controllers\MensualiteController::class, 'enregistrerPaiement'])->name('enregistrer-paiement');
        Route::get('/edit/{id}', [App\Http\Controllers\MensualiteController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [App\Http\Controllers\MensualiteController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [App\Http\Controllers\MensualiteController::class, 'destroy'])->name('destroy');
        
        // Route pour corriger les montants
        Route::post('/corriger-montants', [App\Http\Controllers\MensualiteController::class, 'corrigerMontants'])->name('corriger-montants');
        
        // Routes pour reçus
        Route::get('/recu/{id}', [App\Http\Controllers\MensualiteController::class, 'voirRecu'])->name('voir-recu');
        
        // Routes pour export
        Route::get('/export-paiements', [App\Http\Controllers\MensualiteController::class, 'exportPaiements'])->name('export-paiements');
        Route::get('/download-export-pdf', [App\Http\Controllers\MensualiteController::class, 'downloadExportPDF'])->name('download-export-pdf');
        
        // Route pour corriger les montants
        Route::post('/corriger-montants', [App\Http\Controllers\MensualiteController::class, 'corrigerMontants'])->name('corriger-montants');
        
        // Routes pour les rapports dynamiques
        Route::get('/rapports-data', [App\Http\Controllers\MensualiteController::class, 'getRapportsMensualites'])->name('rapports.data');
        Route::get('/rapports-apercu', [App\Http\Controllers\MensualiteController::class, 'apercuRapportsMensualites'])->name('rapports.apercu');
        Route::get('/annees-scolaires', [App\Http\Controllers\MensualiteController::class, 'getAnneesScolaires'])->name('annees.scolaires');
        
        // Routes pour rapports statiques (anciennes)
        Route::get('/rapport/{type}', [App\Http\Controllers\MensualiteController::class, 'genererRapport'])->name('rapport');
        Route::get('/rapport-personnalise', [App\Http\Controllers\MensualiteController::class, 'rapportPersonnalise'])->name('rapport-personnalise');
        Route::get('/export-excel', [App\Http\Controllers\MensualiteController::class, 'exportExcel'])->name('export-excel');
    });

    // Routes pour le module personnel
    Route::middleware(['auth'])->prefix('personnel')->name('personnel.')->group(function () {
        Route::get('/', [App\Http\Controllers\PersonnelController::class, 'index'])->name('index');
        
        // Routes CRUD personnel
        Route::post('/', [App\Http\Controllers\PersonnelController::class, 'store'])->name('store');
        Route::get('/{personnel}/edit', [App\Http\Controllers\PersonnelController::class, 'edit'])->name('edit');
        Route::put('/{personnel}', [App\Http\Controllers\PersonnelController::class, 'update'])->name('update');
        Route::delete('/{personnel}', [App\Http\Controllers\PersonnelController::class, 'destroy'])->name('destroy');
        
        // Route pour changer le statut
        Route::post('/{personnel}/toggle-statut', [App\Http\Controllers\PersonnelController::class, 'toggleStatut'])->name('toggle-statut');
        
        // Routes pour les États de Paiement
        Route::get('/etats-paiement/{annee}/{mois}', [App\Http\Controllers\PersonnelController::class, 'getEtatsPaiement'])->name('etats-paiement.get');
        Route::post('/etats-paiement/sauvegarder', [App\Http\Controllers\PersonnelController::class, 'sauvegarderEtatPaiement'])->name('etats-paiement.save');
        Route::post('/etats-paiement/{etat}/toggle-visibilite', [App\Http\Controllers\PersonnelController::class, 'toggleVisibiliteEtat'])->name('etats-paiement.toggle-visibilite');
        Route::post('/etats-paiement/archiver/{annee}/{mois}', [App\Http\Controllers\PersonnelController::class, 'archiverEtatsPaiement'])->name('etats-paiement.archiver');
        
        // Routes pour les Archives
        Route::get('/annees-scolaires', [App\Http\Controllers\PersonnelController::class, 'getAnneesScolaires'])->name('annees.scolaires');
        Route::get('/archives/{annee}', [App\Http\Controllers\PersonnelController::class, 'getArchives'])->name('archives.get');
        Route::get('/archives/{annee}/{mois}/details', [App\Http\Controllers\PersonnelController::class, 'getDetailArchive'])->name('archives.details');
        Route::get('/archives/{annee}/{mois}/print', [App\Http\Controllers\PersonnelController::class, 'printArchive'])->name('archives.print');
        
        // Route pour l'aperçu des états actuels
        Route::get('/etats-paiement/{annee}/{mois}/apercu', [App\Http\Controllers\PersonnelController::class, 'apercuEtatsPaiement'])->name('etats.apercu');
        
        // Route temporaire pour recalculer les montants
        Route::get('/etats-paiement/{annee}/{mois}/recalculer', [App\Http\Controllers\PersonnelController::class, 'recalculerEtatsPaiement'])->name('etats.recalculer');

        // Routes pour les Rapports
        Route::get('/rapports-data', [App\Http\Controllers\PersonnelController::class, 'getRapportsData'])->name('rapports.data');
        Route::get('/rapports-apercu', [App\Http\Controllers\PersonnelController::class, 'apercuRapports'])->name('rapports.apercu');
        Route::get('/reports/data', [App\Http\Controllers\PersonnelController::class, 'getReportsData'])->name('reports.data');
        Route::post('/etats/{etat}/validate', [App\Http\Controllers\PersonnelController::class, 'validateEtat'])->name('etats.validate');
        Route::get('/reports/generate', [App\Http\Controllers\PersonnelController::class, 'generateReport'])->name('reports.generate');
        Route::get('/reports/export', [App\Http\Controllers\PersonnelController::class, 'exportData'])->name('reports.export');

    });

    // Routes pour le module services
    Route::middleware(['auth'])->prefix('services')->name('services.')->group(function () {
        Route::get('/', [App\Http\Controllers\ServiceController::class, 'index'])->name('index');
        
        // Routes pour les services
        Route::get('/data', [App\Http\Controllers\ServiceController::class, 'getServices'])->name('data');
        Route::post('/store', [App\Http\Controllers\ServiceController::class, 'storeService'])->name('store');
        Route::put('/{service}', [App\Http\Controllers\ServiceController::class, 'updateService'])->name('update');
        Route::delete('/{service}', [App\Http\Controllers\ServiceController::class, 'destroyService'])->name('destroy');
        
        // Routes pour les dépenses
        Route::get('/depenses/data', [App\Http\Controllers\ServiceController::class, 'getDepenses'])->name('depenses.data');
        Route::post('/depenses/store', [App\Http\Controllers\ServiceController::class, 'storeDepense'])->name('depenses.store');
        Route::put('/depenses/{depense}', [App\Http\Controllers\ServiceController::class, 'updateDepense'])->name('depenses.update');
        Route::delete('/depenses/{depense}', [App\Http\Controllers\ServiceController::class, 'destroyDepense'])->name('depenses.destroy');
        Route::get('/depenses/{depense}/fiche', [App\Http\Controllers\ServiceController::class, 'ficheDepense'])->name('depenses.fiche');
        
        // Routes utilitaires
        Route::get('/services-select', [App\Http\Controllers\ServiceController::class, 'getServicesForSelect'])->name('services.select');
        
        // Routes pour les filtres et rapports
        Route::post('/depenses/filtrer', [App\Http\Controllers\ServiceController::class, 'filtrerDepenses'])->name('depenses.filtrer');
        Route::post('/rapports/generer', [App\Http\Controllers\ServiceController::class, 'genererRapport'])->name('rapports.generer');
        
        // Routes pour les exports
        Route::post('/depenses/exporter', [App\Http\Controllers\ServiceController::class, 'exporterDepenses'])->name('depenses.exporter');
        Route::post('/depenses/apercu', [App\Http\Controllers\ServiceController::class, 'apercuDepenses'])->name('depenses.apercu');
        Route::post('/rapports/exporter-pdf', [App\Http\Controllers\ServiceController::class, 'exporterRapportPDF'])->name('rapports.exporter-pdf');
    });
});

require __DIR__.'/auth.php';