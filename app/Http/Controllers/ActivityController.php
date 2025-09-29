<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActivityController extends Controller
{
    /**
     * Afficher la page principale des logs d'activité
     */
    public function index(Request $request)
    {
        // Récupérer les filtres
        $filters = [
            'user' => $request->get('user'),
            'action' => $request->get('action'),
            'model' => $request->get('model'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'level' => $request->get('level'),
            'search' => $request->get('search')
        ];

        // Query de base
        $query = ActivityLog::with('user')
            ->orderBy('created_at', 'desc');

        // Appliquer les filtres
        if ($filters['user']) {
            $query->where('user_id', $filters['user']);
        }

        if ($filters['action']) {
            $query->where('action', $filters['action']);
        }

        if ($filters['model']) {
            $query->where('model', $filters['model']);
        }

        if ($filters['date_from']) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if ($filters['date_to']) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if ($filters['level']) {
            $query->where('level', $filters['level']);
        }

        if ($filters['search']) {
            $query->where(function($q) use ($filters) {
                $q->where('description', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('ip_address', 'like', '%' . $filters['search'] . '%')
                  ->orWhereHas('user', function($userQuery) use ($filters) {
                      $userQuery->where('name', 'like', '%' . $filters['search'] . '%');
                  });
            });
        }

        // Pagination
        $activities = $query->paginate(50)->withQueryString();

        // Données pour les filtres
        $users = User::orderBy('name')->get(['id', 'name']);
        $actions = ActivityLog::distinct()->pluck('action')->sort();
        $models = ActivityLog::distinct()->whereNotNull('model')->pluck('model')->sort();
        $levels = ['info', 'warning', 'error'];

        // Statistiques du jour
        $todayStats = $this->getTodayStats();

        return view('activity.index', compact(
            'activities', 
            'filters', 
            'users', 
            'actions', 
            'models', 
            'levels',
            'todayStats'
        ));
    }

    /**
     * API pour récupérer les activités (AJAX)
     */
    public function getActivities(Request $request)
    {
        $query = ActivityLog::with('user')
            ->orderBy('created_at', 'desc');

        // Appliquer les filtres
        if ($request->filled('user')) {
            $query->where('user_id', $request->user);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%')
                  ->orWhere('action', 'like', '%' . $request->search . '%')
                  ->orWhere('model', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                $request->date_from . ' 00:00:00',
                $request->date_to . ' 23:59:59'
            ]);
        } elseif ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        } elseif ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $activities = $query->paginate(25);

        // Formatter les données pour la réponse JSON
        $formattedActivities = $activities->map(function ($activity) {
            return [
                'id' => $activity->id,
                'user' => $activity->user ? [
                    'id' => $activity->user->id,
                    'name' => $activity->user->name,
                    'role' => $activity->user->role ?? 'Utilisateur'
                ] : ['name' => 'Système', 'role' => 'System'],
                'action' => [
                    'code' => $activity->action,
                    'label' => $this->getActionLabel($activity->action),
                    'icon' => $this->getActionIcon($activity->action),
                    'color' => $this->getActionColor($activity->action)
                ],
                'description' => $activity->description,
                'target' => [
                    'model' => $activity->model,
                    'model_id' => $activity->model_id,
                    'model_label' => $this->getModelLabel($activity->model),
                    'identifier' => $this->getModelIdentifier($activity->model, $activity->model_id, $activity->details)
                ],
                'context' => [
                    'ip_address' => $activity->ip_address ?? 'Inconnue',
                    'browser' => $activity->user_agent ? $this->getBrowserFromUserAgent($activity->user_agent) : 'Inconnu',
                    'method' => $activity->method ?? 'GET',
                    'url' => $activity->url,
                    'referer' => $activity->referer
                ],
                'level' => $activity->level ?? 'info',
                'created_at' => $activity->created_at ? $activity->created_at->format('Y-m-d H:i:s') : null,
                'created_at_formatted' => $activity->created_at ? $activity->created_at->format('d/m/Y H:i:s') : 'Date inconnue',
                'created_at_human' => $activity->created_at ? $activity->created_at->diffForHumans() : 'Date inconnue',
                'details' => $activity->details,
                'summary' => $this->generateActivitySummary($activity)
            ];
        });

        return response()->json([
            'data' => $formattedActivities,
            'current_page' => $activities->currentPage(),
            'last_page' => $activities->lastPage(),
            'total' => $activities->total(),
            'per_page' => $activities->perPage()
        ]);
    }

    /**
     * Détecter le navigateur depuis le User Agent
     */
    private function getBrowserFromUserAgent($userAgent)
    {
        if (str_contains($userAgent, 'Chrome')) return 'Chrome';
        if (str_contains($userAgent, 'Firefox')) return 'Firefox';
        if (str_contains($userAgent, 'Safari')) return 'Safari';
        if (str_contains($userAgent, 'Edge')) return 'Edge';
        if (str_contains($userAgent, 'Opera')) return 'Opera';
        return 'Autre';
    }

    /**
     * Obtenir le libellé d'une action
     */
    private function getActionLabel($action)
    {
        $labels = [
            'CREATE' => 'Création',
            'UPDATE' => 'Modification',
            'DELETE' => 'Suppression',
            'LOGIN' => 'Connexion',
            'LOGOUT' => 'Déconnexion',
            'DOWNLOAD' => 'Téléchargement',
            'PRINT' => 'Impression',
            'EXPORT' => 'Exportation',
            'SEARCH' => 'Recherche',
            'VIEW' => 'Consultation'
        ];
        return $labels[$action] ?? $action;
    }

    /**
     * Obtenir l'icône d'une action
     */
    private function getActionIcon($action)
    {
        $icons = [
            'CREATE' => 'fas fa-plus-circle',
            'UPDATE' => 'fas fa-edit',
            'DELETE' => 'fas fa-trash',
            'LOGIN' => 'fas fa-sign-in-alt',
            'LOGOUT' => 'fas fa-sign-out-alt',
            'DOWNLOAD' => 'fas fa-download',
            'PRINT' => 'fas fa-print',
            'EXPORT' => 'fas fa-file-export',
            'SEARCH' => 'fas fa-search',
            'VIEW' => 'fas fa-eye'
        ];
        return $icons[$action] ?? 'fas fa-info-circle';
    }

    /**
     * Obtenir la couleur d'une action
     */
    private function getActionColor($action)
    {
        $colors = [
            'CREATE' => 'green',
            'UPDATE' => 'blue',
            'DELETE' => 'red',
            'LOGIN' => 'purple',
            'LOGOUT' => 'gray',
            'DOWNLOAD' => 'yellow',
            'PRINT' => 'indigo',
            'EXPORT' => 'orange',
            'SEARCH' => 'teal',
            'VIEW' => 'gray'
        ];
        return $colors[$action] ?? 'gray';
    }

    /**
     * Obtenir le libellé d'un modèle
     */
    private function getModelLabel($model)
    {
        $labels = [
            'User' => 'Utilisateur',
            'Inscription' => 'Inscription',
            'PreInscription' => 'Pré-inscription',
            'Mensualite' => 'Mensualité',
            'Personnel' => 'Personnel',
            'Service' => 'Service',
            'DepenseService' => 'Dépense',
            'Etablissement' => 'Établissement',
            'Niveau' => 'Niveau',
            'Classe' => 'Classe',
            'Frais' => 'Frais',
            'AnneeScolaire' => 'Année scolaire'
        ];
        return $labels[$model] ?? $model;
    }

    /**
     * Obtenir l'identifiant du modèle pour l'affichage
     */
    private function getModelIdentifier($model, $modelId, $details)
    {
        if (!$model || !$modelId) return null;

        // Essayer d'extraire des informations depuis les détails
        if ($details && is_array($details)) {
            if (isset($details['input']['nom'])) return $details['input']['nom'];
            if (isset($details['input']['name'])) return $details['input']['name'];
            if (isset($details['input']['prenom'])) return $details['input']['prenom'];
            if (isset($details['input']['libelle'])) return $details['input']['libelle'];
        }

        return "#{$modelId}";
    }

    /**
     * Générer un résumé de l'activité
     */
    private function generateActivitySummary($activity)
    {
        $user = $activity->user ? $activity->user->name : 'Système';
        $action = $this->getActionLabel($activity->action);
        $model = $this->getModelLabel($activity->model);
        $identifier = $this->getModelIdentifier($activity->model, $activity->model_id, $activity->details);

        if ($activity->model && $identifier) {
            return "{$user} a effectué une {$action} sur {$model} {$identifier}";
        } elseif ($activity->model) {
            return "{$user} a effectué une {$action} sur {$model}";
        } else {
            return "{$user} a effectué une {$action}";
        }
    }

    /**
     * Statistiques du jour
     */
    private function getTodayStats()
    {
        $today = Carbon::today();
        
        return [
            'total_today' => ActivityLog::whereDate('created_at', $today)->count(),
            'logins_today' => ActivityLog::whereDate('created_at', $today)
                ->where('action', 'LIKE', '%LOGIN%')->count(),
            'creates_today' => ActivityLog::whereDate('created_at', $today)
                ->where('action', 'CREATE')->count(),
            'updates_today' => ActivityLog::whereDate('created_at', $today)
                ->where('action', 'UPDATE')->count(),
            'deletes_today' => ActivityLog::whereDate('created_at', $today)
                ->where('action', 'DELETE')->count(),
            'downloads_today' => ActivityLog::whereDate('created_at', $today)
                ->where('action', 'DOWNLOAD')->count(),
            'unique_users_today' => ActivityLog::whereDate('created_at', $today)
                ->distinct('user_id')->count('user_id'),
            'unique_ips_today' => ActivityLog::whereDate('created_at', $today)
                ->distinct('ip_address')->count('ip_address')
        ];
    }

    /**
     * Statistiques avancées (par période)
     */
    public function getStats(Request $request)
    {
        $days = $request->get('days', 7);
        $startDate = Carbon::now()->subDays($days);

        // Activités par jour
        $activitiesPerDay = ActivityLog::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Activités par action
        $activitiesPerAction = ActivityLog::select('action', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('action')
            ->orderBy('count', 'desc')
            ->get();

        // Top utilisateurs
        $topUsers = ActivityLog::select('user_id', DB::raw('COUNT(*) as count'))
            ->with('user:id,name')
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Top IPs
        $topIPs = ActivityLog::select('ip_address', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('ip_address')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'activities_per_day' => $activitiesPerDay,
            'activities_per_action' => $activitiesPerAction,
            'top_users' => $topUsers,
            'top_ips' => $topIPs
        ]);
    }

    /**
     * Détails d'une activité
     */
    public function show(ActivityLog $activity)
    {
        $activity->load('user');
        
        return response()->json([
            'activity' => $activity,
            'user' => $activity->user,
            'formatted_details' => $this->formatDetails($activity->details)
        ]);
    }

    /**
     * Formater les détails pour l'affichage
     */
    private function formatDetails($details)
    {
        if (!$details) return null;

        $formatted = [];
        foreach ($details as $key => $value) {
            if (is_array($value)) {
                $formatted[$key] = json_encode($value, JSON_PRETTY_PRINT);
            } else {
                $formatted[$key] = $value;
            }
        }

        return $formatted;
    }

    /**
     * Export des logs en CSV
     */
    public function export(Request $request)
    {
        $query = ActivityLog::with('user')
            ->orderBy('created_at', 'desc');

        // Appliquer les mêmes filtres que l'index
        if ($request->user) {
            $query->where('user_id', $request->user);
        }

        if ($request->action) {
            $query->where('action', $request->action);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $activities = $query->get();

        $filename = 'logs_activite_' . now()->format('Y-m-d_H-i') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($activities) {
            $file = fopen('php://output', 'w');
            
            // En-têtes
            fputcsv($file, [
                'Date/Heure',
                'Utilisateur',
                'Action',
                'Description',
                'Modèle',
                'IP',
                'Navigateur',
                'Niveau'
            ]);

            // Données
            foreach ($activities as $activity) {
                fputcsv($file, [
                    $activity->created_at->format('d/m/Y H:i:s'),
                    $activity->user ? $activity->user->name : 'Système',
                    $activity->action,
                    $activity->description,
                    $activity->model ?? '-',
                    $activity->formatted_ip,
                    $activity->browser,
                    strtoupper($activity->level)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Nettoyer les anciens logs
     */
    public function cleanup(Request $request)
    {
        $request->validate([
            'cleanup_type' => 'required|in:basic,smart',
            'days' => 'nullable|integer|min:1|max:365'
        ]);

        $type = $request->get('cleanup_type', 'basic');
        $days = $request->get('days', 30);
        
        if ($type === 'smart') {
            $results = ActivityLog::smartCleanup();
            $totalDeleted = array_sum($results);
            
            return response()->json([
                'success' => true,
                'message' => "Nettoyage intelligent effectué : {$totalDeleted} logs supprimés",
                'deleted_count' => $totalDeleted,
                'details' => $results
            ]);
        } else {
            $deleted = ActivityLog::cleanup($days);
            $date = Carbon::now()->subDays($days);
            
            return response()->json([
                'success' => true,
                'message' => "Suppression de {$deleted} logs antérieurs au " . $date->format('d/m/Y'),
                'deleted_count' => $deleted
            ]);
        }
    }

    /**
     * Obtenir les statistiques de stockage
     */
    public function storageStats()
    {
        $stats = ActivityLog::getStorageStats();
        
        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Authentifier le mot de passe pour l'accès aux logs
     */
    public function authenticatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string'
        ]);

        // Utiliser la même constante que dans le middleware
        $correctPassword = \App\Http\Middleware\ActivityLogAccess::ACTIVITY_LOG_PASSWORD;

        if ($request->password === $correctPassword) {
            // Définir l'accès en session
            session([
                'activity_log_access_time' => now(),
                'activity_log_access_user' => auth()->id()
            ]);

            // Logger l'accès réussi
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'ACCESS_LOGS',
                'description' => 'Accès aux logs d\'activité autorisé',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'method' => 'POST',
                'url' => $request->fullUrl(),
                'level' => 'info'
            ]);

            return redirect()->route('activity.index')
                ->with('success', 'Accès autorisé aux logs d\'activité');
        } else {
            // Logger la tentative d'accès échouée
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'FAILED_LOGS_ACCESS',
                'description' => 'Tentative d\'accès aux logs avec mot de passe incorrect',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'method' => 'POST',
                'url' => $request->fullUrl(),
                'level' => 'warning'
            ]);

            return back()->withErrors([
                'password' => 'Mot de passe incorrect pour l\'accès aux logs d\'activité'
            ])->withInput();
        }
    }
}
