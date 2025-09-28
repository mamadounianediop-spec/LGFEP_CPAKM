<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== EXPORT DES DONNÉES LOCALES VERS PRODUCTION ===\n\n";

// Tables à exporter (sans les données de test)
$tables = [
    'etablissements',
    'niveaux', 
    'classes',
    'annee_scolaires',
    'frais',
    'pre_inscriptions',
    'inscriptions',
    'mensualites',
    'personnels',
    'users'
];

$exportData = [];

foreach ($tables as $table) {
    try {
        $count = DB::table($table)->count();
        if ($count > 0) {
            echo "Exportation de {$table}: {$count} enregistrements\n";
            $data = DB::table($table)->get()->toArray();
            $exportData[$table] = $data;
        } else {
            echo "Table {$table}: aucune donnée\n";
        }
    } catch (Exception $e) {
        echo "Erreur avec la table {$table}: " . $e->getMessage() . "\n";
    }
}

// Sauvegarder dans un fichier JSON
$jsonData = json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
file_put_contents('data_export.json', $jsonData);

echo "\n=== EXPORT TERMINÉ ===\n";
echo "Fichier créé: data_export.json\n";
echo "Taille: " . round(filesize('data_export.json') / 1024, 2) . " KB\n";

// Afficher un résumé
echo "\n=== RÉSUMÉ ===\n";
foreach ($exportData as $table => $data) {
    echo "{$table}: " . count($data) . " enregistrements\n";
}