<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->boot();

use App\Models\Service;
use App\Models\DepenseService;
use App\Models\AnneeScolaire;

echo "=== ANNÉE SCOLAIRE ACTIVE ===\n";
$anneeActive = AnneeScolaire::getActive();
echo "Année active: " . $anneeActive->libelle . " (ID: " . $anneeActive->id . ")\n\n";

echo "=== SERVICES ===\n";
$services = Service::with('anneeScolaire')->get();
foreach($services as $service) {
    echo "Service: " . $service->nom . " - Année: " . ($service->anneeScolaire->libelle ?? 'NULL') . " (ID: " . $service->annee_scolaire_id . ")\n";
}

echo "\n=== DÉPENSES ===\n";
$depenses = DepenseService::with('anneeScolaire')->get();
foreach($depenses as $depense) {
    echo "Dépense: " . $depense->montant . " FCFA - Année: " . ($depense->anneeScolaire->libelle ?? 'NULL') . " (ID: " . $depense->annee_scolaire_id . ")\n";
}

echo "\n=== FILTRAGE POUR ANNÉE ACTIVE ===\n";
$servicesActifs = Service::where('annee_scolaire_id', $anneeActive->id)->get();
echo "Services pour année active: " . $servicesActifs->count() . "\n";

$depensesActives = DepenseService::where('annee_scolaire_id', $anneeActive->id)->get();
echo "Dépenses pour année active: " . $depensesActives->count() . "\n";