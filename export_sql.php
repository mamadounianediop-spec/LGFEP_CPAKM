<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== EXPORT COMPLET DES DONNÉES LOCALES VERS SQL ===\n\n";

// Tables à exporter dans l'ordre (pour respecter les contraintes)
$tables = [
    'users',
    'etablissements', 
    'annee_scolaires',
    'niveaux',
    'classes',
    'frais',
    'personnels',
    'pre_inscriptions',
    'inscriptions',
    'mensualites',
    'etats_paiement_mensuels',
    'categories_services',
    'services',
    'depenses_services'
];

$sqlContent = "-- Export complet des données locales vers MySQL\n";
$sqlContent .= "-- Généré le " . date('Y-m-d H:i:s') . "\n\n";
$sqlContent .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

$totalRecords = 0;

foreach ($tables as $table) {
    try {
        $records = DB::table($table)->get();
        $count = $records->count();
        
        if ($count > 0) {
            echo "Exportation de {$table}: {$count} enregistrements\n";
            
            // Vider la table d'abord
            $sqlContent .= "-- Table: {$table}\n";
            $sqlContent .= "TRUNCATE TABLE `{$table}`;\n";
            
            // Construire les INSERT
            if ($count > 0) {
                $columns = array_keys((array)$records->first());
                $columnsList = '`' . implode('`, `', $columns) . '`';
                
                $sqlContent .= "INSERT INTO `{$table}` ({$columnsList}) VALUES\n";
                
                $values = [];
                foreach ($records as $record) {
                    $recordArray = (array)$record;
                    $valuesList = [];
                    
                    foreach ($recordArray as $value) {
                        if ($value === null) {
                            $valuesList[] = 'NULL';
                        } elseif (is_string($value)) {
                            $valuesList[] = "'" . addslashes($value) . "'";
                        } elseif (is_bool($value)) {
                            $valuesList[] = $value ? '1' : '0';
                        } else {
                            $valuesList[] = $value;
                        }
                    }
                    
                    $values[] = '(' . implode(', ', $valuesList) . ')';
                }
                
                $sqlContent .= implode(",\n", $values) . ";\n\n";
                $totalRecords += $count;
            }
        } else {
            echo "Table {$table}: aucune donnée\n";
        }
        
    } catch (Exception $e) {
        echo "ERREUR avec la table {$table}: " . $e->getMessage() . "\n";
    }
}

$sqlContent .= "SET FOREIGN_KEY_CHECKS = 1;\n";

// Sauvegarder le fichier SQL
file_put_contents('data_export.sql', $sqlContent);

echo "\n=== EXPORT SQL TERMINÉ ===\n";
echo "Fichier créé: data_export.sql\n";
echo "Taille: " . round(filesize('data_export.sql') / 1024, 2) . " KB\n";
echo "Total d'enregistrements: {$totalRecords}\n";

echo "\n=== RÉSUMÉ ===\n";
foreach ($tables as $table) {
    try {
        $count = DB::table($table)->count();
        if ($count > 0) {
            echo "{$table}: {$count} enregistrements\n";
        }
    } catch (Exception $e) {
        // Table n'existe pas ou erreur
    }
}