<?php

namespace Database\Seeders;

use App\Models\AnneeScolaire;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnneeScolaireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Année scolaire 2024-2025 (active)
        AnneeScolaire::create([
            'libelle' => '2024-2025',
            'date_debut' => '2024-10-01',
            'date_fin' => '2025-07-31',
            'actif' => true,
            'description' => 'Année scolaire 2024-2025 - Année active'
        ]);

        // Année scolaire 2025-2026 (future)
        AnneeScolaire::create([
            'libelle' => '2025-2026',
            'date_debut' => '2025-10-01',
            'date_fin' => '2026-07-31',
            'actif' => false,
            'description' => 'Année scolaire 2025-2026 - Année future'
        ]);

        // Année scolaire 2023-2024 (passée)
        AnneeScolaire::create([
            'libelle' => '2023-2024',
            'date_debut' => '2023-10-01',
            'date_fin' => '2024-07-31',
            'actif' => false,
            'description' => 'Année scolaire 2023-2024 - Année passée'
        ]);
    }
}
