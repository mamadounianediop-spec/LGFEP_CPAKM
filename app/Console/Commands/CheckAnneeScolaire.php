<?php

namespace App\Console\Commands;

use App\Models\AnneeScolaire;
use Illuminate\Console\Command;

class CheckAnneeScolaire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'annee:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifier les années scolaires';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $annees = AnneeScolaire::all();
        
        $this->info('Années scolaires dans la base de données :');
        $this->table(
            ['ID', 'Libellé', 'Date début', 'Date fin', 'Active'],
            $annees->map(function($annee) {
                return [
                    $annee->id,
                    $annee->libelle,
                    $annee->date_debut,
                    $annee->date_fin,
                    $annee->actif ? 'Oui' : 'Non'
                ];
            })
        );

        $active = AnneeScolaire::getActive();
        if ($active) {
            $this->info("Année scolaire active : {$active->libelle}");
        } else {
            $this->warn("Aucune année scolaire active trouvée !");
        }
    }
}
