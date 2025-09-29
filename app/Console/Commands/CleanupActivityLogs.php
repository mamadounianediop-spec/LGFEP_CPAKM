<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Illuminate\Console\Command;

class CleanupActivityLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:cleanup 
                            {--days=30 : Nombre de jours à conserver pour les logs généraux}
                            {--smart : Utiliser le nettoyage intelligent par type d\'action}
                            {--stats : Afficher les statistiques avant nettoyage}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nettoie les logs d\'activité anciens pour optimiser l\'espace disque';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Nettoyage des logs d\'activité...');
        
        // Afficher les statistiques avant nettoyage
        if ($this->option('stats')) {
            $this->showStats();
        }
        
        if ($this->option('smart')) {
            $this->smartCleanup();
        } else {
            $this->basicCleanup();
        }
        
        $this->info('✅ Nettoyage terminé !');
    }
    
    /**
     * Afficher les statistiques des logs
     */
    protected function showStats()
    {
        $this->info('📊 Statistiques actuelles des logs :');
        
        $stats = ActivityLog::getStorageStats();
        
        $this->table(
            ['Métrique', 'Valeur'],
            [
                ['Total des logs', number_format($stats['total_logs'])],
                ['Taille en base', $stats['size_mb'] . ' MB'],
                ['Plus ancien log', $stats['oldest_log']?->format('d/m/Y H:i') ?? 'Aucun'],
                ['Plus récent log', $stats['newest_log']?->format('d/m/Y H:i') ?? 'Aucun']
            ]
        );
        
        if (!empty($stats['logs_by_action'])) {
            $this->info('📈 Répartition par action :');
            $actionData = [];
            foreach ($stats['logs_by_action'] as $action => $count) {
                $actionData[] = [$action, number_format($count)];
            }
            $this->table(['Action', 'Nombre'], $actionData);
        }
        
        $this->newLine();
    }
    
    /**
     * Nettoyage intelligent par type d'action
     */
    protected function smartCleanup()
    {
        $this->info('🤖 Nettoyage intelligent en cours...');
        
        if (!$this->confirm('Voulez-vous continuer avec le nettoyage intelligent ?')) {
            $this->info('Nettoyage annulé.');
            return;
        }
        
        $results = ActivityLog::smartCleanup();
        
        $this->info('📋 Résultats du nettoyage intelligent :');
        $this->table(
            ['Type de logs', 'Supprimés'],
            [
                ['Logs de consultation (>7j)', number_format($results['view_logs'])],
                ['Logs de recherche (>3j)', number_format($results['search_logs'])],
                ['Autres logs (>30j)', number_format($results['other_logs'])],
                ['Logs critiques (>90j)', number_format($results['critical_logs'])]
            ]
        );
        
        $totalDeleted = array_sum($results);
        $this->info("🗑️  Total supprimé : " . number_format($totalDeleted) . " logs");
    }
    
    /**
     * Nettoyage de base par nombre de jours
     */
    protected function basicCleanup()
    {
        $days = (int) $this->option('days');
        
        $this->info("🗓️  Nettoyage des logs de plus de {$days} jours...");
        
        if (!$this->confirm("Supprimer tous les logs de plus de {$days} jours ?")) {
            $this->info('Nettoyage annulé.');
            return;
        }
        
        $deletedCount = ActivityLog::cleanup($days);
        
        $this->info("🗑️  {$deletedCount} logs supprimés (plus de {$days} jours)");
    }
}
