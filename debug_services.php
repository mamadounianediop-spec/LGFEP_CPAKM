<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUG MODULE SERVICES ===\n";
echo "Catégories de services: " . App\Models\CategorieService::count() . "\n";
echo "Années scolaires: " . App\Models\AnneeScolaire::count() . "\n";
echo "Services: " . App\Models\Service::count() . "\n";

echo "\n=== CATÉGORIES DE SERVICES ===\n";
$categories = App\Models\CategorieService::all();
foreach ($categories as $cat) {
    echo "- {$cat->nom} (actif: " . ($cat->actif ? 'oui' : 'non') . ")\n";
}

echo "\n=== ANNÉES SCOLAIRES ===\n";
$annees = App\Models\AnneeScolaire::orderBy('date_debut', 'desc')->get();
foreach ($annees as $annee) {
    echo "- {$annee->libelle}\n";
}
