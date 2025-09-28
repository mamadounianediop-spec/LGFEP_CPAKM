<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RapportController extends Controller
{
    /**
     * Génère le rapport global (à implémenter)
     */
    public function rapportGlobal(Request $request)
    {
        // TODO: Implémenter le système de rapport global
        return redirect()->route('dashboard')
            ->with('info', 'Système de rapport global en cours de développement.');
    }
}