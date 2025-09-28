<?php
// Script pour corriger automatiquement les vues pour grands écrans

$viewsDirectory = __DIR__ . '/resources/views';

// Patterns à remplacer pour les grands écrans
$patterns = [
    // Max width containers
    'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8' => 'max-w-none xl:max-w-7xl 2xl:max-w-none mx-auto px-4 sm:px-6 lg:px-8 xl:px-12 2xl:px-20',
    'max-w-6xl mx-auto px-4 sm:px-6 lg:px-8' => 'max-w-none xl:max-w-6xl 2xl:max-w-none mx-auto px-4 sm:px-6 lg:px-8 xl:px-12 2xl:px-20',
    'max-w-5xl mx-auto px-4 sm:px-6 lg:px-8' => 'max-w-none xl:max-w-5xl 2xl:max-w-none mx-auto px-4 sm:px-6 lg:px-8 xl:px-12 2xl:px-20',
    
    // Headers
    'text-2xl font-bold' => 'text-2xl xl:text-4xl 2xl:text-5xl font-bold',
    'text-xl font-bold' => 'text-xl xl:text-3xl 2xl:text-4xl font-bold',
    'text-lg font-bold' => 'text-lg xl:text-2xl 2xl:text-3xl font-bold',
    
    // Spacing
    'py-6' => 'py-6 xl:py-8 2xl:py-12',
    'mb-8' => 'mb-8 xl:mb-12 2xl:mb-16',
    'space-x-6' => 'space-x-6 xl:space-x-8 2xl:space-x-12',
    'space-y-6' => 'space-y-6 xl:space-y-8 2xl:space-y-12',
    
    // Buttons
    'px-4 py-2 text-sm' => 'px-4 xl:px-6 2xl:px-8 py-2 xl:py-3 2xl:py-4 text-sm xl:text-base 2xl:text-lg',
    
    // Cards
    'p-6' => 'p-6 xl:p-8 2xl:p-12',
    'p-4' => 'p-4 xl:p-6 2xl:p-8',
];

function correctView($filePath, $patterns) {
    $content = file_get_contents($filePath);
    
    foreach ($patterns as $search => $replace) {
        $content = str_replace($search, $replace, $content);
    }
    
    file_put_contents($filePath, $content);
    return $filePath;
}

function correctViewsRecursively($directory, $patterns) {
    $correctedFiles = [];
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory)
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $filePath = $file->getPathname();
            
            // Skip certain files
            if (strpos($filePath, 'layouts') !== false && strpos($filePath, 'app.blade.php') !== false) {
                continue; // Skip app.blade.php as we already corrected it
            }
            
            correctView($filePath, $patterns);
            $correctedFiles[] = $filePath;
        }
    }
    
    return $correctedFiles;
}

echo "=== CORRECTION DES VUES POUR GRANDS ÉCRANS ===\n\n";

try {
    $correctedFiles = correctViewsRecursively($viewsDirectory, $patterns);
    
    echo "Fichiers corrigés : " . count($correctedFiles) . "\n\n";
    
    foreach ($correctedFiles as $file) {
        echo "✓ " . basename($file) . "\n";
    }
    
    echo "\n=== CORRECTION TERMINÉE ===\n";
    echo "Toutes les vues ont été mises à jour pour les grands écrans !\n";
    
} catch (Exception $e) {
    echo "ERREUR : " . $e->getMessage() . "\n";
}