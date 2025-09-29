<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class ActivityLogAccess
{
    /**
     * Mot de passe pour accéder aux logs d'activité
     * Changez ce mot de passe selon vos besoins
     */
    public const ACTIVITY_LOG_PASSWORD = 'LogsAccess2025!';

    /**
     * Durée de validité de l'accès en minutes
     */
    private const ACCESS_DURATION = 30;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si l'utilisateur a déjà l'accès valide
        if ($this->hasValidAccess()) {
            return $next($request);
        }

        // Si c'est une requête POST avec le mot de passe
        if ($request->isMethod('post') && $request->has('activity_password')) {
            if ($this->verifyPassword($request->input('activity_password'))) {
                $this->grantAccess();
                
                // Si c'est une requête AJAX, retourner un JSON
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => true, 'message' => 'Accès accordé']);
                }
                
                return $next($request);
            } else {
                // Mot de passe incorrect
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Mot de passe incorrect'], 403);
                }
                
                return redirect()->back()->withErrors(['activity_password' => 'Mot de passe incorrect']);
            }
        }

        // Afficher le formulaire de mot de passe
        return $this->showPasswordForm($request);
    }

    /**
     * Vérifier si l'utilisateur a un accès valide
     */
    private function hasValidAccess(): bool
    {
        $accessTime = Session::get('activity_log_access_time');
        
        if (!$accessTime) {
            return false;
        }

        // Vérifier si l'accès n'a pas expiré
        return now()->diffInMinutes($accessTime) < self::ACCESS_DURATION;
    }

    /**
     * Vérifier le mot de passe
     */
    private function verifyPassword(string $password): bool
    {
        return $password === self::ACTIVITY_LOG_PASSWORD;
    }

    /**
     * Accorder l'accès
     */
    private function grantAccess(): void
    {
        Session::put('activity_log_access_time', now());
        Session::put('activity_log_access_user', auth()->id());
        
        // Logger l'accès aux logs
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'ACCESS',
            'description' => 'Accès aux logs d\'activité accordé',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'method' => 'POST',
            'url' => request()->fullUrl(),
            'level' => 'info'
        ]);
    }

    /**
     * Afficher le formulaire de mot de passe
     */
    private function showPasswordForm(Request $request): Response
    {
        // Si c'est une requête AJAX, retourner un JSON avec l'indication qu'un mot de passe est requis
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false, 
                'requires_password' => true,
                'message' => 'Accès protégé - Mot de passe requis'
            ], 403);
        }

        // Afficher la vue du formulaire de mot de passe
        return response()->view('activity.password-form');
    }
}
