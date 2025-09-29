<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class LogActivity
{
    /**
     * Routes à ignorer pour éviter le spam de logs
     */
    private $excludedRoutes = [
        'activity.*',
        'dashboard',
        '_debugbar/*',
        'api/*',
        'livewire/*',
        '*/search*',
        '*/data*',
        '*/get-*',
        'test-*'
    ];

    /**
     * Actions à ignorer
     */
    private $excludedMethods = ['HEAD', 'OPTIONS'];

    /**
     * Actions importantes à logger
     */
    private $importantActions = [
        'login', 'logout', 'store', 'update', 'destroy', 'delete',
        'export', 'download', 'print', 'finaliser', 'annuler',
        'enregistrer-paiement', 'corriger-montants', 'archiver',
        'validate', 'toggle-statut', 'activer', 'cleanup'
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Log seulement après la réponse pour éviter les erreurs
        if ($this->shouldLog($request, $response)) {
            $this->logActivity($request, $response);
        }

        return $response;
    }

    /**
     * Déterminer si l'activité doit être loggée
     */
    private function shouldLog(Request $request, Response $response): bool
    {
        // Ignorer certaines méthodes HTTP
        if (in_array($request->method(), $this->excludedMethods)) {
            return false;
        }

        // Ignorer les routes exclues
        foreach ($this->excludedRoutes as $pattern) {
            if ($request->is($pattern)) {
                return false;
            }
        }

        // Ignorer les erreurs 4xx et 5xx
        if ($response->getStatusCode() >= 400) {
            return false;
        }

        // Ne logger que les actions importantes
        $route = $request->route();
        $routeName = $route ? $route->getName() : '';
        
        // Si GET, vérifier si c'est une action importante
        if ($request->method() === 'GET') {
            $isImportant = false;
            foreach ($this->importantActions as $action) {
                if (str_contains($routeName, $action)) {
                    $isImportant = true;
                    break;
                }
            }
            if (!$isImportant) {
                return false;
            }
        }

        return true;
    }

    /**
     * Logger l'activité
     */
    private function logActivity(Request $request, Response $response): void
    {
        try {
            $action = $this->determineAction($request);
            $description = $this->generateDescription($request, $action);
            $details = $this->collectDetails($request, $response);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'model' => $this->extractModel($request),
                'model_id' => $this->extractModelId($request),
                'description' => $description,
                'details' => $details,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'referer' => $request->header('referer'),
                'level' => $this->determineLevel($action, $response)
            ]);
        } catch (\Exception $e) {
            // En cas d'erreur, ne pas interrompre la requête
            logger('Erreur lors du logging d\'activité: ' . $e->getMessage());
        }
    }

    /**
     * Déterminer l'action basée sur la requête
     */
    private function determineAction(Request $request): string
    {
        $method = $request->method();
        $route = $request->route();
        $routeName = $route ? $route->getName() : '';

        // Actions spécifiques basées sur les noms de routes
        if (str_contains($routeName, 'login')) return 'LOGIN';
        if (str_contains($routeName, 'logout')) return 'LOGOUT';
        if (str_contains($routeName, 'store')) return 'CREATE';
        if (str_contains($routeName, 'update')) return 'UPDATE';
        if (str_contains($routeName, 'destroy') || str_contains($routeName, 'delete')) return 'DELETE';
        if (str_contains($routeName, 'export') || str_contains($routeName, 'download')) return 'DOWNLOAD';
        if (str_contains($routeName, 'recu') || str_contains($routeName, 'pdf')) return 'DOWNLOAD';
        if (str_contains($routeName, 'print')) return 'PRINT';
        if (str_contains($routeName, 'search')) return 'SEARCH';
        if (str_contains($routeName, 'edit') || str_contains($routeName, 'show')) return 'VIEW';

        // Actions basées sur les méthodes HTTP
        switch ($method) {
            case 'POST': return 'CREATE';
            case 'PUT':
            case 'PATCH': return 'UPDATE';
            case 'DELETE': return 'DELETE';
            case 'GET':
            default: return 'VIEW';
        }
    }

    /**
     * Générer une description lisible
     */
    private function generateDescription(Request $request, string $action): string
    {
        $route = $request->route();
        $routeName = $route ? $route->getName() : '';
        $model = $this->extractModel($request);

        $descriptions = [
            'LOGIN' => 'Connexion utilisateur',
            'LOGOUT' => 'Déconnexion utilisateur',
            'CREATE' => 'Création d\'un enregistrement' . ($model ? " ($model)" : ''),
            'UPDATE' => 'Modification d\'un enregistrement' . ($model ? " ($model)" : ''),
            'DELETE' => 'Suppression d\'un enregistrement' . ($model ? " ($model)" : ''),
            'DOWNLOAD' => 'Téléchargement de fichier',
            'PRINT' => 'Impression de document',
            'SEARCH' => 'Recherche effectuée',
            'VIEW' => 'Consultation de page'
        ];

        // Descriptions spécifiques pour certaines actions importantes
        if (str_contains($routeName, 'finaliser')) return 'Finalisation d\'inscription';
        if (str_contains($routeName, 'annuler')) return 'Annulation d\'inscription';
        if (str_contains($routeName, 'enregistrer-paiement')) return 'Enregistrement de paiement';
        if (str_contains($routeName, 'corriger-montants')) return 'Correction des montants';
        if (str_contains($routeName, 'archiver')) return 'Archivage des données';
        if (str_contains($routeName, 'toggle-statut')) return 'Changement de statut';
        if (str_contains($routeName, 'activer')) return 'Activation';
        if (str_contains($routeName, 'cleanup')) return 'Nettoyage des données';

        return $descriptions[$action] ?? 'Action inconnue';
    }

    /**
     * Collecter les détails de la requête
     */
    private function collectDetails(Request $request, Response $response): ?array
    {
        $details = [];

        // Paramètres de la requête (sans mots de passe)
        $input = $request->except(['password', 'password_confirmation', '_token', '_method']);
        if (!empty($input)) {
            $details['input'] = $input;
        }

        // Code de statut de la réponse
        $details['status_code'] = $response->getStatusCode();

        // Taille de la réponse
        $details['response_size'] = strlen($response->getContent());

        return !empty($details) ? $details : null;
    }

    /**
     * Extraire le nom du modèle depuis la route
     */
    private function extractModel(Request $request): ?string
    {
        $route = $request->route();
        if (!$route) return null;

        $routeName = $route->getName();
        if (!$routeName) return null;

        // Mapper les noms de routes aux modèles
        $modelMapping = [
            'inscriptions' => 'Inscription',
            'pre-inscription' => 'PreInscription',
            'mensualites' => 'Mensualite',
            'personnel' => 'Personnel',
            'services' => 'Service',
            'depenses' => 'DepenseService',
            'users' => 'User',
            'etablissement' => 'Etablissement',
            'niveaux' => 'Niveau',
            'classes' => 'Classe',
            'frais' => 'Frais',
            'annees-scolaires' => 'AnneeScolaire'
        ];

        foreach ($modelMapping as $routeKey => $model) {
            if (str_contains($routeName, $routeKey)) {
                return $model;
            }
        }

        return null;
    }

    /**
     * Extraire l'ID du modèle depuis les paramètres de route
     */
    private function extractModelId(Request $request): ?int
    {
        $route = $request->route();
        if (!$route) return null;

        $parameters = $route->parameters();
        
        // Chercher un paramètre qui ressemble à un ID
        foreach ($parameters as $key => $value) {
            if (is_numeric($value) || (is_object($value) && method_exists($value, 'getKey'))) {
                return is_object($value) ? $value->getKey() : (int)$value;
            }
        }

        return null;
    }

    /**
     * Déterminer le niveau de gravité
     */
    private function determineLevel(string $action, Response $response): string
    {
        if (in_array($action, ['DELETE'])) {
            return 'warning';
        }

        if ($response->getStatusCode() >= 400) {
            return 'error';
        }

        return 'info';
    }
}
