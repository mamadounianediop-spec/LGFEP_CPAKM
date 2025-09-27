<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Mensualite;
use App\Models\Frais;

class CorrigerMontantsMensualites extends Command
{
    protected $signature = 'mensualites:corriger-montants';
    protected $description = 'Corriger les montants des mensualités selon les frais configurés par niveau';

    public function handle()
    {
        $this->info('Correction des montants des mensualités...');
        
        $mensualites = Mensualite::with(['inscription.niveau'])->get();
        $corrections = 0;
        
        foreach ($mensualites as $mensualite) {
            $frais = Frais::where('niveau_id', $mensualite->inscription->niveau_id)
                         ->where('type', 'mensualite')
                         ->first();
                         
            if ($frais && $mensualite->montant_du != $frais->montant) {
                $oldAmount = $mensualite->montant_du;
                $mensualite->update(['montant_du' => $frais->montant]);
                
                $this->line("Mensualité ID {$mensualite->id} (Niveau: {$mensualite->inscription->niveau->nom}) - Ancien: {$oldAmount} -> Nouveau: {$frais->montant}");
                $corrections++;
            }
        }
        
        $this->info("✅ {$corrections} mensualités corrigées!");
        
        return 0;
    }
}