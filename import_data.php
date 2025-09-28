<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== IMPORT DES DONNÉES VERS PRODUCTION ===\n\n";

// Vérifier que le fichier d'export existe
if (!file_exists('data_export.json')) {
    echo "ERREUR: Le fichier data_export.json n'existe pas!\n";
    exit(1);
}

// Charger les données
$jsonData = file_get_contents('data_export.json');
$importData = json_decode($jsonData, true);

if (!$importData) {
    echo "ERREUR: Impossible de lire le fichier JSON!\n";
    exit(1);
}

echo "Fichier JSON chargé avec succès\n\n";

// Ordre d'import (pour respecter les contraintes de clés étrangères)
$importOrder = [
    'users',
    'etablissements',
    'niveaux',
    'annee_scolaires',
    'classes',
    'frais',
    'personnels',
    'pre_inscriptions',
    'inscriptions',
    'mensualites'
];

// Nettoyer les tables (sauf les migrations)
echo "=== NETTOYAGE DES TABLES ===\n";
foreach ($importOrder as $table) {
    if (isset($importData[$table])) {
        try {
            $count = DB::table($table)->count();
            if ($count > 0) {
                DB::table($table)->truncate();
                echo "Table {$table}: {$count} enregistrements supprimés\n";
            }
        } catch (Exception $e) {
            echo "Erreur lors du nettoyage de {$table}: " . $e->getMessage() . "\n";
        }
    }
}

echo "\n=== IMPORT DES DONNÉES ===\n";

// Importer les données dans l'ordre
foreach ($importOrder as $table) {
    if (isset($importData[$table]) && !empty($importData[$table])) {
        try {
            $data = $importData[$table];
            
            // Convertir les objets stdClass en arrays si nécessaire
            $data = json_decode(json_encode($data), true);
            
            // Insérer par petits lots pour éviter les erreurs de mémoire
            $chunks = array_chunk($data, 50);
            $totalInserted = 0;
            
            foreach ($chunks as $chunk) {
                DB::table($table)->insert($chunk);
                $totalInserted += count($chunk);
            }
            
            echo "Table {$table}: {$totalInserted} enregistrements importés\n";
            
        } catch (Exception $e) {
            echo "ERREUR lors de l'import de {$table}: " . $e->getMessage() . "\n";
        }
    } else {
        echo "Table {$table}: aucune donnée à importer\n";
    }
}

echo "\n=== IMPORT TERMINÉ ===\n";

// Vérification finale
echo "\n=== VÉRIFICATION ===\n";
foreach ($importOrder as $table) {
    if (isset($importData[$table])) {
        try {
            $count = DB::table($table)->count();
            echo "{$table}: {$count} enregistrements en base\n";
        } catch (Exception $e) {
            echo "{$table}: erreur de vérification\n";
        }
    }
}