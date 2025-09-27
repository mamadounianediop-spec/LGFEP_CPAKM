<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classe;
use App\Models\Niveau;

class ClasseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $niveaux = Niveau::all();

        foreach ($niveaux as $niveau) {
            // Créer 2-3 classes par niveau
            for ($i = 1; $i <= 2; $i++) {
                Classe::create([
                    'nom' => $niveau->nom . ' - Classe ' . $i,
                    'code' => $niveau->code . 'C' . $i,
                    'niveau_id' => $niveau->id,
                    'effectif_max' => 35,
                    'actif' => true
                ]);
            }
        }

        $this->command->info('Classes créées avec succès !');
    }
}
