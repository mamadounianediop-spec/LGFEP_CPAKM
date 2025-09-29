<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model',
        'model_id',
        'description',
        'details',
        'ip_address',
        'user_agent',
        'method',
        'url',
        'referer',
        'level'
    ];

    protected $casts = [
        'details' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Créer un log d'activité
     */
    public static function log(string $action, string $description, array $options = []): self
    {
        return self::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model' => $options['model'] ?? null,
            'model_id' => $options['model_id'] ?? null,
            'description' => $description,
            'details' => $options['details'] ?? null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'method' => request()->method(),
            'url' => request()->fullUrl(),
            'referer' => request()->header('referer'),
            'level' => $options['level'] ?? 'info'
        ]);
    }

    /**
     * Scope pour filtrer par utilisateur
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope pour filtrer par action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope pour filtrer par modèle
     */
    public function scopeByModel($query, $model)
    {
        return $query->where('model', $model);
    }

    /**
     * Scope pour filtrer par date
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('created_at', $date);
    }

    /**
     * Scope pour filtrer par période
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope pour les activités récentes
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Formatage de l'IP pour affichage
     */
    public function getFormattedIpAttribute(): string
    {
        if ($this->ip_address === '127.0.0.1' || $this->ip_address === '::1') {
            return 'Local';
        }
        return $this->ip_address;
    }

    /**
     * Formatage du navigateur
     */
    public function getBrowserAttribute(): string
    {
        $userAgent = $this->user_agent;
        
        if (str_contains($userAgent, 'Chrome')) return 'Chrome';
        if (str_contains($userAgent, 'Firefox')) return 'Firefox';
        if (str_contains($userAgent, 'Safari')) return 'Safari';
        if (str_contains($userAgent, 'Edge')) return 'Edge';
        if (str_contains($userAgent, 'Opera')) return 'Opera';
        
        return 'Autre';
    }

    /**
     * Nettoyer les logs anciens (garder seulement les X derniers jours)
     */
    public static function cleanup(int $daysToKeep = 30): int
    {
        $cutoffDate = now()->subDays($daysToKeep);
        
        $deletedCount = self::where('created_at', '<', $cutoffDate)->delete();
        
        return $deletedCount;
    }

    /**
     * Nettoyer les logs par type d'action (garder plus longtemps les actions critiques)
     */
    public static function smartCleanup(): array
    {
        $results = [];
        
        // Supprimer les logs de consultation (VIEW) après 7 jours
        $results['view_logs'] = self::where('action', 'VIEW')
            ->where('created_at', '<', now()->subDays(7))
            ->delete();
            
        // Supprimer les logs de recherche (SEARCH) après 3 jours  
        $results['search_logs'] = self::where('action', 'SEARCH')
            ->where('created_at', '<', now()->subDays(3))
            ->delete();
            
        // Supprimer les autres logs après 90 jours (garder plus longtemps les actions importantes)
        $criticalActions = ['CREATE', 'UPDATE', 'DELETE', 'LOGIN', 'LOGOUT', 'DOWNLOAD'];
        $results['other_logs'] = self::whereNotIn('action', array_merge($criticalActions, ['VIEW', 'SEARCH']))
            ->where('created_at', '<', now()->subDays(30))
            ->delete();
            
        // Supprimer les logs critiques après 90 jours
        $results['critical_logs'] = self::whereIn('action', $criticalActions)
            ->where('created_at', '<', now()->subDays(90))
            ->delete();
            
        return $results;
    }

    /**
     * Obtenir les statistiques d'utilisation d'espace
     */
    public static function getStorageStats(): array
    {
        $totalLogs = self::count();
        $logsSize = \DB::select('SELECT 
            ROUND((data_length + index_length) / 1024 / 1024, 2) AS size_mb 
            FROM information_schema.tables 
            WHERE table_schema = DATABASE() AND table_name = "activity_logs"')[0]->size_mb ?? 0;
            
        return [
            'total_logs' => $totalLogs,
            'size_mb' => $logsSize,
            'oldest_log' => self::oldest('created_at')->first()?->created_at,
            'newest_log' => self::latest('created_at')->first()?->created_at,
            'logs_by_action' => self::select('action', \DB::raw('COUNT(*) as count'))
                ->groupBy('action')
                ->pluck('count', 'action')
                ->toArray()
        ];
    }
}
