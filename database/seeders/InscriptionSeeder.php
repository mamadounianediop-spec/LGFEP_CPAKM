<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PreInscription;
use App\Models\Inscription;
use App\Models\Niveau;
use App\Models\Classe;
use App\Models\AnneeScolaire;
use Carbon\Carbon;

class InscriptionSeeder extends Seeder
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

        // Récupérer les niveaux et classes existants
        $niveaux = Niveau::all();
        $classes = Classe::all();

        if ($niveaux->isEmpty() || $classes->isEmpty()) {
            $this->command->error('Veuillez d\'abord créer des niveaux et des classes avant d\'exécuter ce seeder.');
            return;
        }

        // Créer quelques pré-inscriptions
        $preInscriptions = [
            [
                'nom' => 'DIOP',
                'prenom' => 'Amadou',
                'sexe' => 'M',
                'ine' => 'INE2026001',
                'date_naissance' => '2006-03-15',
                'lieu_naissance' => 'Dakar',
                'adresse' => 'Parcelles Assainies, Villa n°123',
                'contact' => '77 123 45 67',
                'tuteur' => 'Mamadou DIOP (Père)',
                'etablissement_origine' => 'Collège Moderne de Dakar',
                'statut' => 'en_attente'
            ],
            [
                'nom' => 'FALL',
                'prenom' => 'Fatou',
                'sexe' => 'F',
                'ine' => 'INE2026002',
                'date_naissance' => '2005-07-22',
                'lieu_naissance' => 'Thiès',
                'adresse' => 'Médina, Rue 15',
                'contact' => '78 234 56 78',
                'tuteur' => 'Ousmane FALL (Père)',
                'etablissement_origine' => 'CEM de Thiès',
                'statut' => 'en_attente'
            ],
            [
                'nom' => 'NDIAYE',
                'prenom' => 'Moussa',
                'sexe' => 'M',
                'ine' => 'INE2026003',
                'date_naissance' => '2004-11-08',
                'lieu_naissance' => 'Saint-Louis',
                'adresse' => 'Grand Yoff, Cité Millionnaire',
                'contact' => '76 345 67 89',
                'tuteur' => 'Ibrahima NDIAYE (Père)',
                'etablissement_origine' => 'Lycée de Saint-Louis',
                'statut' => 'en_attente'
            ],
            [
                'nom' => 'SARR',
                'prenom' => 'Awa',
                'sexe' => 'F',
                'ine' => 'INE2026004',
                'date_naissance' => '2006-01-12',
                'lieu_naissance' => 'Kaolack',
                'adresse' => 'Liberté 6, Extension',
                'contact' => '77 456 78 90',
                'tuteur' => 'Alioune SARR (Père)',
                'etablissement_origine' => 'CEM Aline Sitoé Diatta',
                'statut' => 'en_attente'
            ],
            [
                'nom' => 'WADE',
                'prenom' => 'Cheikh',
                'sexe' => 'M',
                'ine' => 'INE2026005',
                'date_naissance' => '2005-09-30',
                'lieu_naissance' => 'Ziguinchor',
                'adresse' => 'HLM, Grand Médine',
                'contact' => '78 567 89 01',
                'tuteur' => 'Abdou WADE (Père)',
                'etablissement_origine' => 'Lycée Djignabo',
                'statut' => 'en_attente'
            ]
        ];

        $createdPreInscriptions = [];
        foreach ($preInscriptions as $preInscriptionData) {
            $preInscriptionData['annee_scolaire_id'] = $anneeActive->id;
            $preInscription = PreInscription::create($preInscriptionData);
            $createdPreInscriptions[] = $preInscription;
        }

        // Créer des inscriptions pour certaines pré-inscriptions
        $modesPaiement = ['orange_money', 'wave', 'free_money', 'billetage'];

        // Inscrire les 3 premiers élèves
        for ($i = 0; $i < 3; $i++) {
            $preInscription = $createdPreInscriptions[$i];
            $niveau = $niveaux->random();
            $classe = $classes->where('niveau_id', $niveau->id)->first() ?? $classes->random();
            
            $montantTotal = rand(50000, 150000); // Entre 50k et 150k FCFA
            $montantPaye = rand(25000, $montantTotal); // Paiement partiel ou complet
            
            Inscription::create([
                'pre_inscription_id' => $preInscription->id,
                'niveau_id' => $niveau->id,
                'classe_id' => $classe->id,
                'montant_total' => $montantTotal,
                'montant_paye' => $montantPaye,
                'mode_paiement' => $modesPaiement[array_rand($modesPaiement)],
                'statut_paiement' => $montantPaye >= $montantTotal ? 'complet' : 'partiel',
                'numero_recu' => Inscription::generateNumeroRecu(),
                'statut' => 'actif',
                'date_inscription' => Carbon::now()->subDays(rand(1, 30))->format('Y-m-d'),
                'annee_scolaire_id' => $anneeActive->id,
            ]);

            // Mettre à jour le statut de la pré-inscription
            $preInscription->update(['statut' => 'inscrit']);
        }

        $this->command->info('Données de test créées avec succès !');
        $this->command->info('- ' . count($preInscriptions) . ' pré-inscriptions créées');
        $this->command->info('- 3 inscriptions finalisées');
        $this->command->info('- 2 pré-inscriptions en attente');
    }
}
