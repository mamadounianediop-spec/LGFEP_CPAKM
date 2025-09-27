<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Personnel;
use Carbon\Carbon;

class PersonnelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $personnels = [
            // Directeurs
            [
                'nom' => 'DIALLO',
                'prenom' => 'Mamadou',
                'telephone' => '77 123 45 67',
                'adresse' => 'Dakar, Plateau',
                'cni' => '1234567890123',
                'type_personnel' => 'directeur',
                'statut' => 'actif',
                'date_embauche' => Carbon::create(2020, 1, 15),
                'mode_paiement' => 'fixe',
                'montant_fixe' => 800000,
            ],
            
            // Surveillants
            [
                'nom' => 'FALL',
                'prenom' => 'Aminata',
                'telephone' => '78 234 56 78',
                'adresse' => 'Pikine, Zone B',
                'cni' => '2345678901234',
                'type_personnel' => 'surveillant',
                'statut' => 'actif',
                'date_embauche' => Carbon::create(2021, 3, 10),
                'mode_paiement' => 'heure',
                'tarif_heure' => 5000,
            ],
            [
                'nom' => 'SARR',
                'prenom' => 'Ibrahima',
                'telephone' => '77 345 67 89',
                'adresse' => 'Guédiawaye',
                'cni' => '3456789012345',
                'type_personnel' => 'surveillant',
                'statut' => 'actif',
                'date_embauche' => Carbon::create(2022, 9, 5),
            ],
            
            // Secrétaires
            [
                'nom' => 'NDIAYE',
                'prenom' => 'Fatou',
                'telephone' => '78 456 78 90',
                'adresse' => 'Parcelles Assainies, Unité 15',
                'cni' => '4567890123456',
                'type_personnel' => 'secretaire',
                'statut' => 'actif',
                'date_embauche' => Carbon::create(2021, 6, 1),
            ],
            [
                'nom' => 'BA',
                'prenom' => 'Aïssatou',
                'telephone' => '77 567 89 01',
                'adresse' => 'Médina, Rue 15',
                'cni' => '5678901234567',
                'type_personnel' => 'secretaire',
                'statut' => 'actif',
                'date_embauche' => Carbon::create(2023, 2, 15),
            ],
            
            // Enseignants
            [
                'nom' => 'SECK',
                'prenom' => 'Ousmane',
                'telephone' => '78 678 90 12',
                'adresse' => 'Liberté 6',
                'cni' => '6789012345678',
                'type_personnel' => 'enseignant',
                'discipline' => 'Mathématiques',
                'statut' => 'actif',
                'date_embauche' => Carbon::create(2019, 10, 1),
            ],
            [
                'nom' => 'KONE',
                'prenom' => 'Mariama',
                'telephone' => '77 789 01 23',
                'adresse' => 'Grand Yoff',
                'cni' => '7890123456789',
                'type_personnel' => 'enseignant',
                'discipline' => 'Français',
                'statut' => 'actif',
                'date_embauche' => Carbon::create(2020, 9, 15),
            ],
            [
                'nom' => 'DIOUF',
                'prenom' => 'Cheikh',
                'telephone' => '78 890 12 34',
                'adresse' => 'HLM Grand Yoff',
                'cni' => '8901234567890',
                'type_personnel' => 'enseignant',
                'discipline' => 'Sciences Physiques',
                'statut' => 'actif',
                'date_embauche' => Carbon::create(2021, 1, 4),
            ],
            [
                'nom' => 'THIAM',
                'prenom' => 'Bineta',
                'telephone' => '77 901 23 45',
                'adresse' => 'Almadies',
                'cni' => '9012345678901',
                'type_personnel' => 'enseignant',
                'discipline' => 'Anglais',
                'statut' => 'actif',
                'date_embauche' => Carbon::create(2022, 2, 14),
            ],
            [
                'nom' => 'CAMARA',
                'prenom' => 'Abdoulaye',
                'telephone' => '78 012 34 56',
                'adresse' => 'Sacré-Cœur',
                'cni' => '0123456789012',
                'type_personnel' => 'enseignant',
                'discipline' => 'Histoire-Géographie',
                'statut' => 'actif',
                'date_embauche' => Carbon::create(2023, 9, 1),
            ],
            [
                'nom' => 'GUEYE',
                'prenom' => 'Ndeye Awa',
                'telephone' => '77 123 45 78',
                'adresse' => 'Point E',
                'cni' => '1234567890124',
                'type_personnel' => 'enseignant',
                'discipline' => 'Sciences de la Vie et de la Terre',
                'statut' => 'conge',
                'date_embauche' => Carbon::create(2021, 11, 8),
            ],
            
            // Gardiens
            [
                'nom' => 'TOURE',
                'prenom' => 'Mamadou',
                'telephone' => '78 234 56 79',
                'adresse' => 'Biscuiterie',
                'cni' => '2345678901235',
                'type_personnel' => 'gardien',
                'statut' => 'actif',
                'date_embauche' => Carbon::create(2022, 5, 20),
            ],
            [
                'nom' => 'CISSE',
                'prenom' => 'Moussa',
                'telephone' => '77 345 67 80',
                'adresse' => 'Colobane',
                'cni' => '3456789012346',
                'type_personnel' => 'gardien',
                'statut' => 'actif',
                'date_embauche' => Carbon::create(2023, 1, 10),
            ],
        ];

        foreach ($personnels as $personnel) {
            Personnel::create($personnel);
        }
        
        $this->command->info('Personnel seeders créés avec succès !');
        $this->command->info('- 1 Directeur');
        $this->command->info('- 2 Surveillants');
        $this->command->info('- 2 Secrétaires');
        $this->command->info('- 6 Enseignants (5 actifs, 1 en congé)');
        $this->command->info('- 2 Gardiens');
    }
}
