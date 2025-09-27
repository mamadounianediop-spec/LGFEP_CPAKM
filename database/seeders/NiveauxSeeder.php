<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NiveauxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $niveaux = [
            ['nom' => 'Cours d\'Initiation', 'code' => 'CI', 'ordre' => 1],
            ['nom' => 'Cours Préparatoire', 'code' => 'CP', 'ordre' => 2],
            ['nom' => 'Cours Élémentaire 1ère année', 'code' => 'CE1', 'ordre' => 3],
            ['nom' => 'Cours Élémentaire 2ème année', 'code' => 'CE2', 'ordre' => 4],
            ['nom' => 'Cours Moyen 1ère année', 'code' => 'CM1', 'ordre' => 5],
            ['nom' => 'Cours Moyen 2ème année', 'code' => 'CM2', 'ordre' => 6],
            ['nom' => 'Sixième', 'code' => '6EME', 'ordre' => 7],
            ['nom' => 'Cinquième', 'code' => '5EME', 'ordre' => 8],
            ['nom' => 'Quatrième', 'code' => '4EME', 'ordre' => 9],
            ['nom' => 'Troisième', 'code' => '3EME', 'ordre' => 10],
            ['nom' => 'Seconde', 'code' => '2NDE', 'ordre' => 11],
            ['nom' => 'Première', 'code' => '1ERE', 'ordre' => 12],
            ['nom' => 'Terminale', 'code' => 'TERM', 'ordre' => 13],
        ];

        foreach ($niveaux as $niveau) {
            \App\Models\Niveau::create([
                'nom' => $niveau['nom'],
                'code' => $niveau['code'],
                'ordre' => $niveau['ordre'],
                'description' => 'Niveau ' . $niveau['nom'],
                'actif' => true,
            ]);
        }
    }
}
