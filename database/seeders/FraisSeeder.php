<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Frais;
use App\Models\Niveau;
use App\Models\AnneeScolaire;

class FraisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer l'année scolaire active
        $anneeActive = AnneeScolaire::getActive();
        if (!$anneeActive) {
            $this->command->error('Aucune année scolaire active trouvée. Veuillez d\'abord créer et activer une année scolaire.');
            return;
        }

        // Récupérer les niveaux existants
        $niveaux = Niveau::all();
        if ($niveaux->isEmpty()) {
            $this->command->error('Veuillez d\'abord créer des niveaux avant d\'exécuter ce seeder.');
            return;
        }

        // Frais généraux (sans niveau spécifique)
        $fraisGeneraux = [
            [
                'type' => 'inscription',
                'nom' => 'Frais d\'inscription',
                'montant' => 25000,
                'niveau_id' => null,
                'actif' => true,
                'annee_scolaire_id' => $anneeActive->id,
            ],
            [
                'type' => 'transport',
                'nom' => 'Frais de transport',
                'montant' => 15000,
                'niveau_id' => null,
                'actif' => true,
                'annee_scolaire_id' => $anneeActive->id,
            ],
            [
                'type' => 'cantine',
                'nom' => 'Frais de cantine',
                'montant' => 20000,
                'niveau_id' => null,
                'actif' => true,
                'annee_scolaire_id' => $anneeActive->id,
            ],
        ];

        // Créer les frais généraux
        foreach ($fraisGeneraux as $frais) {
            Frais::create($frais);
        }

        // Frais de mensualité par niveau
        $montantsMensualites = [
            'CI' => 45000,
            'CP' => 45000,
            'CE1' => 50000,
            'CE2' => 50000,
            'CM1' => 55000,
            'CM2' => 55000,
            '6EME' => 60000,
            '5EME' => 65000,
            '4EME' => 65000,
            '3EME' => 70000,
            '2NDE' => 75000,
            '1ERE' => 80000,
            'TERM' => 85000,
        ];

        foreach ($niveaux as $niveau) {
            $montant = $montantsMensualites[$niveau->code] ?? 50000; // Montant par défaut
            
            Frais::create([
                'type' => 'mensualite',
                'nom' => 'Mensualité ' . $niveau->nom,
                'montant' => $montant,
                'niveau_id' => $niveau->id,
                'actif' => true,
                'annee_scolaire_id' => $anneeActive->id,
            ]);

            // Frais d'examen pour les niveaux d'examen
            if (in_array($niveau->code, ['CM2', '3EME', 'TERM'])) {
                Frais::create([
                    'type' => 'examen',
                    'nom' => 'Frais d\'examen ' . $niveau->nom,
                    'montant' => 10000,
                    'niveau_id' => $niveau->id,
                    'actif' => true,
                    'annee_scolaire_id' => $anneeActive->id,
                ]);
            }
        }

        $this->command->info('Frais scolaires créés avec succès pour l\'année ' . $anneeActive->libelle . ' !');
        $this->command->info('- ' . count($fraisGeneraux) . ' frais généraux créés');
        $this->command->info('- ' . $niveaux->count() . ' frais de mensualité créés');
        $this->command->info('- 3 frais d\'examen créés');
    }
}
