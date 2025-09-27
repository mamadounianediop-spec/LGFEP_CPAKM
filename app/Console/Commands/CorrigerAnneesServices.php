<?php

namespace App\Console\Commands;

use App\Models\Service;
use App\Models\DepenseService;
use App\Models\AnneeScolaire;
use Illuminate\Console\Command;

class CorrigerAnneesServices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:corriger-annees';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corriger les années scolaires des services et dépenses existants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $anneeActive = AnneeScolaire::getActive();
        
        if (!$anneeActive) {
            $this->error('Aucune année scolaire active trouvée !');
            return 1;
        }
        
        $this->info("Année scolaire active : {$anneeActive->libelle}");
        
        // Corriger les services sans année scolaire ou avec une mauvaise année
        $servicesACorreger = Service::where(function($query) use ($anneeActive) {
            $query->whereNull('annee_scolaire_id')
                  ->orWhere('annee_scolaire_id', '!=', $anneeActive->id);
        })->get();
        
        if ($servicesACorreger->count() > 0) {
            $this->info("Correction de {$servicesACorreger->count()} services...");
            
            foreach ($servicesACorreger as $service) {
                $ancienneAnnee = $service->anneeScolaire->libelle ?? 'NULL';
                $service->update(['annee_scolaire_id' => $anneeActive->id]);
                $this->line("✓ Service '{$service->nom}' : {$ancienneAnnee} → {$anneeActive->libelle}");
            }
        } else {
            $this->info('Aucun service à corriger.');
        }
        
        // Corriger les dépenses sans année scolaire ou avec une mauvaise année
        $depensesACorreger = DepenseService::where(function($query) use ($anneeActive) {
            $query->whereNull('annee_scolaire_id')
                  ->orWhere('annee_scolaire_id', '!=', $anneeActive->id);
        })->get();
        
        if ($depensesACorreger->count() > 0) {
            $this->info("Correction de {$depensesACorreger->count()} dépenses...");
            
            foreach ($depensesACorreger as $depense) {
                $ancienneAnnee = $depense->anneeScolaire->libelle ?? 'NULL';
                $depense->update(['annee_scolaire_id' => $anneeActive->id]);
                $this->line("✓ Dépense de {$depense->montant} FCFA : {$ancienneAnnee} → {$anneeActive->libelle}");
            }
        } else {
            $this->info('Aucune dépense à corriger.');
        }
        
        $this->info('✅ Correction terminée avec succès !');
        return 0;
    }
}
