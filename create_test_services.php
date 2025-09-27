<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Récupérer les données nécessaires
$etablissement = App\Models\Etablissement::first();
$anneeScolaire = App\Models\AnneeScolaire::first();
$categories = App\Models\CategorieService::all();

if (!$etablissement || !$anneeScolaire || $categories->isEmpty()) {
    echo "❌ Données manquantes: établissement, année scolaire ou catégories\n";
    exit(1);
}

echo "🔧 Création de services de test...\n";

// Créer des services de test
$servicesTest = [
    [
        'nom' => 'Climatisation Bureau Principal',
        'categorie' => 'Infrastructures & Bâtiments',
        'description' => 'Système de climatisation pour le bureau principal',
        'fournisseur' => 'Climatech SARL',
        'date_acquisition' => '2025-01-15',
        'statut' => 'actif'
    ],
    [
        'nom' => 'Projecteur Salle de Classe A',
        'categorie' => 'Matériel Pédagogique',
        'description' => 'Projecteur multimédia pour salle de classe',
        'fournisseur' => 'Techno Éduc',
        'date_acquisition' => '2025-02-10',
        'statut' => 'actif'
    ],
    [
        'nom' => 'Connexion Internet Fibre',
        'categorie' => 'Services Publics',
        'description' => 'Abonnement internet fibre optique',
        'fournisseur' => 'Orange Sénégal',
        'date_acquisition' => '2025-01-01',
        'statut' => 'actif'
    ]
];

foreach ($servicesTest as $serviceData) {
    $categorie = $categories->where('nom', $serviceData['categorie'])->first();
    
    if ($categorie) {
        $service = App\Models\Service::create([
            'nom' => $serviceData['nom'],
            'categorie_service_id' => $categorie->id,
            'description' => $serviceData['description'],
            'fournisseur' => $serviceData['fournisseur'],
            'date_acquisition' => $serviceData['date_acquisition'],
            'statut' => $serviceData['statut'],
            'etablissement_id' => $etablissement->id,
            'annee_scolaire_id' => $anneeScolaire->id
        ]);
        
        echo "✅ Service créé: {$service->nom}\n";
        
        // Créer une dépense pour ce service
        App\Models\DepenseService::create([
            'service_id' => $service->id,
            'montant' => rand(50000, 500000),
            'date_depense' => '2025-03-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
            'type_depense' => ['achat', 'maintenance', 'location'][rand(0, 2)],
            'numero_facture' => 'FAC-' . rand(1000, 9999),
            'description' => 'Dépense test pour ' . $service->nom,
            'etablissement_id' => $etablissement->id,
            'annee_scolaire_id' => $anneeScolaire->id
        ]);
        
        echo "✅ Dépense créée pour: {$service->nom}\n";
    }
}

echo "\n🎉 Services de test créés avec succès!\n";
echo "Services total: " . App\Models\Service::count() . "\n";
echo "Dépenses total: " . App\Models\DepenseService::count() . "\n";