@extends('layouts.app')

@section('title', 'Dashboard - ' . ($etablissement->nom ?? 'LGFP'))

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header avec informations contextuelles -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-tachometer-alt text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                        <p class="text-sm text-gray-600">
                            {{ $etablissement->nom ?? 'Système de Gestion Financière' }} - 
                            Année {{ $anneeActive->libelle }} 
                            @if($donneesTemporelles['mois_scolaire_actuel'] !== 'Vacances')
                                ({{ $donneesTemporelles['mois_scolaire_actuel'] }})
                            @endif
                        </p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Progression de l'année scolaire -->
                    <div class="bg-white rounded-lg shadow-sm border p-4 w-72">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Progression année scolaire</span>
                            <span class="text-sm font-bold text-indigo-600">{{ $donneesTemporelles['progression_annee'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-2 rounded-full transition-all duration-500" 
                                 style="width: {{ $donneesTemporelles['progression_annee'] }}%"></div>
                        </div>
                    </div>

                    <!-- Bouton Rapport Global -->
                    <div class="relative">
                        <button id="rapportGlobalBtn" class="flex items-center px-4 py-2 text-sm font-medium text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-lg transition-all duration-200 group">
                            <i class="fas fa-chart-pie mr-2 group-hover:scale-110 transition-transform"></i>
                            Rapport Global
                            <i class="fas fa-chevron-down ml-2 text-xs group-hover:rotate-180 transition-transform"></i>
                        </button>
                        
                        <!-- Menu déroulant -->
                        <div id="rapportMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                            <div class="py-2">
                                <div class="flex items-center px-4 py-2 text-sm text-gray-500">
                                    <i class="fas fa-info-circle text-gray-400 mr-3"></i>
                                    Module désactivé
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertes importantes -->
        @if(count($alertes) > 0)
        <div class="mb-6">
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700 font-medium">Attention requise</p>
                        <div class="mt-2 text-sm text-yellow-700">
                            @foreach($alertes as $alerte)
                                <div class="flex items-center mb-1">
                                    <i class="{{ $alerte['icon'] }} mr-2"></i>
                                    <a href="{{ $alerte['url'] }}" class="hover:underline">{{ $alerte['message'] }}</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Statistiques principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Inscriptions -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user-graduate text-blue-600 text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Élèves Inscrits
                                </dt>
                                <dd class="text-2xl font-bold text-gray-900">
                                    {{ number_format($stats['inscriptions_actives']) }}
                                </dd>
                                <dt class="text-xs text-gray-400 mt-1">
                                    {{ $stats['total_pre_inscriptions'] }} pré-inscriptions • {{ $stats['taux_conversion'] }}% conversion
                                </dt>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recettes du Mois -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-money-bill-wave text-green-600 text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Recettes {{ $donneesTemporelles['mois_scolaire_actuel'] }}
                                </dt>
                                <dd class="text-2xl font-bold text-gray-900">
                                    {{ number_format($stats['recettes_mois_courant'], 0, ',', ' ') }} F
                                </dd>
                                <dt class="text-xs text-gray-400 mt-1">
                                    {{ number_format($stats['montant_paye'], 0, ',', ' ') }} F total payé
                                </dt>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Impayés -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Montant Impayé
                                </dt>
                                <dd class="text-2xl font-bold text-gray-900">
                                    {{ number_format($stats['montant_impaye'], 0, ',', ' ') }} F
                                </dd>
                                <dt class="text-xs text-gray-400 mt-1">
                                    {{ 100 - $stats['taux_paiement'] }}% du total • {{ $stats['taux_paiement'] }}% payé
                                </dt>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personnel & Services -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-purple-600 text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Personnel & Services
                                </dt>
                                <dd class="text-2xl font-bold text-gray-900">
                                    {{ $stats['total_personnel'] }} • {{ $stats['services_actifs'] }}
                                </dd>
                                <dt class="text-xs text-gray-400 mt-1">
                                    {{ number_format($stats['total_depenses_services'], 0, ',', ' ') }} F dépenses services
                                </dt>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Rapides et Activités -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Actions Rapides -->
            <div class="bg-white shadow-sm rounded-lg border">
                <div class="px-6 py-5">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-bolt text-indigo-600 mr-2"></i>
                        Actions Rapides
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <a href="{{ route('inscriptions.index') }}" class="group flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-all duration-200 hover:shadow-md">
                            <i class="fas fa-user-plus text-blue-600 text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                            <span class="text-sm font-medium text-blue-900 text-center">Nouvelle Inscription</span>
                        </a>
                        <a href="{{ route('mensualites.index') }}" class="group flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-all duration-200 hover:shadow-md">
                            <i class="fas fa-money-check text-green-600 text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                            <span class="text-sm font-medium text-green-900 text-center">Enregistrer Paiement</span>
                        </a>
                        <a href="{{ route('services.index') }}" class="group flex flex-col items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-all duration-200 hover:shadow-md">
                            <i class="fas fa-cogs text-orange-600 text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                            <span class="text-sm font-medium text-orange-900 text-center">Gérer Services</span>
                        </a>
                        @if(auth()->user()->isAdminOrDirector())
                        <a href="{{ route('personnel.index') }}" class="group flex flex-col items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-all duration-200 hover:shadow-md">
                            <i class="fas fa-users text-purple-600 text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                            <span class="text-sm font-medium text-purple-900 text-center">Gérer Personnel</span>
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Activités Récentes -->
            <div class="bg-white shadow-sm rounded-lg border">
                <div class="px-6 py-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-clock text-indigo-600 mr-2"></i>
                            Activités Récentes
                        </h3>
                        <span class="text-xs text-gray-500">{{ $activitesRecentes->count() }} activités</span>
                    </div>
                    <div class="space-y-3 max-h-80 overflow-y-auto">
                        @forelse($activitesRecentes as $activite)
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="w-8 h-8 bg-{{ $activite['color'] }}-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="{{ $activite['icon'] }} text-{{ $activite['color'] }}-600 text-sm"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm text-gray-900 leading-tight">{{ $activite['message'] }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $activite['date']->diffForHumans() }}</p>
                            </div>
                            <a href="{{ $activite['url'] }}" class="text-indigo-600 hover:text-indigo-800 ml-2">
                                <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                        </div>
                        @empty
                        <div class="text-center py-6">
                            <i class="fas fa-inbox text-gray-300 text-3xl mb-3"></i>
                            <p class="text-sm text-gray-500">Aucune activité récente</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Widgets compacts par module -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
            <!-- Widget Inscriptions -->
            <div class="bg-white shadow-sm rounded-lg border">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h4 class="text-md font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-user-graduate text-blue-600 mr-2"></i>
                            Inscriptions
                        </h4>
                        <a href="{{ route('inscriptions.index') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                            Voir tout <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $stats['total_inscriptions'] }}</div>
                            <div class="text-xs text-gray-500">Inscrits</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-600">{{ $stats['total_pre_inscriptions'] - $stats['total_inscriptions'] }}</div>
                            <div class="text-xs text-gray-500">En attente</div>
                        </div>
                    </div>
                    
                    <!-- Dernières inscriptions (3 max) -->
                    <div class="space-y-2">
                        @php
                            $dernieresInscriptions = App\Models\Inscription::with(['preInscription', 'classe'])
                                ->where('annee_scolaire_id', $anneeActive->id)
                                ->orderBy('created_at', 'desc')
                                ->limit(3)
                                ->get();
                        @endphp
                        
                        @forelse($dernieresInscriptions as $inscription)
                        <div class="flex items-center justify-between py-1 border-b border-gray-100 last:border-0">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $inscription->preInscription->nom }} {{ $inscription->preInscription->prenom }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $inscription->classe->nom ?? 'N/A' }}</p>
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ $inscription->created_at->format('d/m') }}
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-gray-500 text-sm">
                            <i class="fas fa-inbox mb-2"></i><br>
                            Aucune inscription récente
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Widget Mensualités -->
            <div class="bg-white shadow-sm rounded-lg border">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h4 class="text-md font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-money-bill-wave text-green-600 mr-2"></i>
                            Mensualités
                        </h4>
                        <a href="{{ route('mensualites.index') }}" class="text-xs text-green-600 hover:text-green-800 font-medium">
                            Voir tout <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $stats['taux_paiement'] }}%</div>
                            <div class="text-xs text-gray-500">Taux paiement</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600">
                                @php
                                    $impayes = App\Models\Mensualite::where('annee_scolaire_id', $anneeActive->id)
                                        ->where('statut', 'impaye')->count();
                                @endphp
                                {{ $impayes }}
                            </div>
                            <div class="text-xs text-gray-500">Impayés</div>
                        </div>
                    </div>
                    
                    <!-- Derniers paiements (3 max) -->
                    <div class="space-y-2">
                        @php
                            $derniersPaiements = App\Models\Mensualite::with('inscription.preInscription')
                                ->where('annee_scolaire_id', $anneeActive->id)
                                ->where('statut', 'complet')
                                ->orderBy('date_paiement', 'desc')
                                ->limit(3)
                                ->get();
                        @endphp
                        
                        @forelse($derniersPaiements as $paiement)
                        <div class="flex items-center justify-between py-1 border-b border-gray-100 last:border-0">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $paiement->inscription->preInscription->nom ?? 'N/A' }}
                                </p>
                                <p class="text-xs text-gray-500">{{ number_format($paiement->montant_paye, 0, ',', ' ') }} F</p>
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ $paiement->date_paiement ? $paiement->date_paiement->format('d/m') : '' }}
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-gray-500 text-sm">
                            <i class="fas fa-inbox mb-2"></i><br>
                            Aucun paiement récent
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Widget Personnel -->
            @if(auth()->user()->isAdminOrDirector())
            <div class="bg-white shadow-sm rounded-lg border">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h4 class="text-md font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-users text-purple-600 mr-2"></i>
                            Personnel
                        </h4>
                        <a href="{{ route('personnel.index') }}" class="text-xs text-purple-600 hover:text-purple-800 font-medium">
                            Voir tout <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ $stats['total_personnel'] }}</div>
                            <div class="text-xs text-gray-500">Personnel actif</div>
                        </div>
                        <div class="text-center">
                            @php
                                $enseignants = $stats['personnel_par_type']['enseignant'] ?? 0;
                            @endphp
                            <div class="text-2xl font-bold text-blue-600">{{ $enseignants }}</div>
                            <div class="text-xs text-gray-500">Enseignants</div>
                        </div>
                    </div>
                    
                    <!-- Répartition par type -->
                    <div class="space-y-2">
                        @foreach(['directeur' => 'Directeur', 'secretaire' => 'Secrétaire', 'surveillant' => 'Surveillant', 'gardien' => 'Gardien'] as $type => $label)
                            @if(isset($stats['personnel_par_type'][$type]) && $stats['personnel_par_type'][$type] > 0)
                            <div class="flex items-center justify-between py-1">
                                <span class="text-sm text-gray-700">{{ $label }}</span>
                                <span class="text-sm font-medium text-gray-900">{{ $stats['personnel_par_type'][$type] }}</span>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Widgets Services et Graphiques -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Widget Services & Dépenses -->
            @if(auth()->user()->isAdminOrDirector())
            <div class="bg-white shadow-sm rounded-lg border">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h4 class="text-md font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-cogs text-orange-600 mr-2"></i>
                            Services & Dépenses
                        </h4>
                        <a href="{{ route('services.index') }}" class="text-xs text-orange-600 hover:text-orange-800 font-medium">
                            Voir tout <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div class="text-center">
                            <div class="text-lg font-bold text-orange-600">{{ $stats['services_actifs'] }}</div>
                            <div class="text-xs text-gray-500">Services actifs</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold text-blue-600">{{ $stats['total_services'] }}</div>
                            <div class="text-xs text-gray-500">Total services</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold text-red-600">{{ number_format($stats['total_depenses_services']/1000000, 1) }}M</div>
                            <div class="text-xs text-gray-500">Dépenses (FCFA)</div>
                        </div>
                    </div>
                    
                    <!-- Dernières dépenses -->
                    <div class="space-y-2">
                        @php
                            $dernieresDepenses = App\Models\DepenseService::with('service')
                                ->where('annee_scolaire_id', $anneeActive->id)
                                ->orderBy('created_at', 'desc')
                                ->limit(4)
                                ->get();
                        @endphp
                        
                        @forelse($dernieresDepenses as $depense)
                        <div class="flex items-center justify-between py-1 border-b border-gray-100 last:border-0">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $depense->service->nom ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500">{{ ucfirst($depense->type_depense) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">{{ number_format($depense->montant/1000, 0) }}k</p>
                                <p class="text-xs text-gray-400">{{ $depense->created_at->format('d/m') }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-gray-500 text-sm">
                            <i class="fas fa-inbox mb-2"></i><br>
                            Aucune dépense récente
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            @endif

            <!-- Graphique des Recettes -->
            <div class="bg-white shadow-sm rounded-lg border">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h4 class="text-md font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-chart-line text-indigo-600 mr-2"></i>
                        Évolution des Recettes
                    </h4>
                </div>
                <div class="p-4">
                    <!-- Canvas pour le graphique Chart.js -->
                    <div class="h-48 relative">
                        <canvas id="recettesChart" class="w-full h-full"></canvas>
                    </div>
                    
                    <!-- Légende rapide -->
                    <div class="mt-4 grid grid-cols-3 gap-4 text-center">
                        <div>
                            <div class="text-sm font-medium text-green-600">{{ number_format($stats['recettes_mois_courant']/1000000, 1) }}M</div>
                            <div class="text-xs text-gray-500">Ce mois</div>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-blue-600">{{ number_format($stats['montant_paye']/1000000, 1) }}M</div>
                            <div class="text-xs text-gray-500">Total payé</div>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-red-600">{{ number_format($stats['montant_impaye']/1000000, 1) }}M</div>
                            <div class="text-xs text-gray-500">Impayé</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer avec informations système -->
        <div class="bg-white shadow-sm rounded-lg border p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <i class="fas fa-calendar-alt text-gray-400 mr-2"></i>
                        <span class="text-sm text-gray-600">Année scolaire {{ $anneeActive->libelle }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-clock text-gray-400 mr-2"></i>
                        <span class="text-sm text-gray-600">Dernière mise à jour: {{ now()->format('d/m/Y à H:i') }}</span>
                    </div>
                </div>
                
                @if(auth()->user()->isAdminOrDirector())
                <div class="flex items-center space-x-3">
                    <a href="{{ route('parametres.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors" title="Paramètres">
                        <i class="fas fa-cog"></i>
                    </a>
                    <a href="{{ route('activity.password-form') }}" class="text-gray-400 hover:text-red-600 transition-colors relative" title="Logs d'Activité (Accès Protégé)">
                        <i class="fas fa-history"></i>
                        <i class="fas fa-shield-alt text-red-500 absolute -top-1 -right-1 text-xs"></i>
                    </a>
                    <div class="h-4 w-px bg-gray-300"></div>
                    <span class="text-xs text-gray-500">{{ auth()->user()->name }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Scripts pour interactions dashboard
document.addEventListener('DOMContentLoaded', function() {
    // Animation d'entrée des widgets
    const widgets = document.querySelectorAll('[class*="shadow-sm"]');
    widgets.forEach((widget, index) => {
        widget.style.opacity = '0';
        widget.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            widget.style.transition = 'all 0.5s ease-out';
            widget.style.opacity = '1';
            widget.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    // Graphique Chart.js - Évolution des Recettes
    const ctx = document.getElementById('recettesChart').getContext('2d');
    
    // Données simulées basées sur les mois scolaires
    const moisScolaires = ['Oct', 'Nov', 'Déc', 'Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul'];
    const recettesData = @json($chartData['recettes_par_mois'] ?? []);
    
    // Préparer les données pour Chart.js
    let labels = [];
    let montants = [];
    
    // Si nous avons des données réelles
    if (recettesData && recettesData.length > 0) {
        recettesData.forEach(item => {
            labels.push(item.mois_paiement ? item.mois_paiement.charAt(0).toUpperCase() + item.mois_paiement.slice(1, 3) : '');
            montants.push(item.montant || 0);
        });
    } else {
        // Données simulées pour démonstration
        labels = moisScolaires;
        montants = [
            {{ $stats['recettes_mois_courant'] > 0 ? $stats['recettes_mois_courant'] : 0 }}, // Mois actuel
            0, 0, 0, 0, 0, 0, 0, 0, 0 // Mois futurs
        ];
    }
    
    const recettesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Recettes (FCFA)',
                data: montants,
                borderColor: 'rgb(99, 102, 241)',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgb(99, 102, 241)',
                pointBorderColor: 'white',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(17, 24, 39, 0.9)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    borderColor: 'rgb(99, 102, 241)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return 'Recettes: ' + new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' FCFA';
                        }
                    }
                }
            },
            scales: {
                x: {
                    display: true,
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6B7280',
                        font: {
                            size: 12
                        }
                    }
                },
                y: {
                    display: true,
                    grid: {
                        color: 'rgba(107, 114, 128, 0.1)'
                    },
                    ticks: {
                        color: '#6B7280',
                        font: {
                            size: 11
                        },
                        callback: function(value) {
                            if (value >= 1000000) {
                                return (value / 1000000).toFixed(1) + 'M';
                            } else if (value >= 1000) {
                                return (value / 1000).toFixed(0) + 'K';
                            }
                            return value;
                        }
                    }
                }
            },
            elements: {
                point: {
                    hoverBackgroundColor: 'rgb(99, 102, 241)',
                    hoverBorderColor: 'white'
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
    
    // Gestion du menu Rapport Global
    const rapportBtn = document.getElementById('rapportGlobalBtn');
    const rapportMenu = document.getElementById('rapportMenu');
    
    if (rapportBtn && rapportMenu) {
        rapportBtn.addEventListener('click', function(e) {
            e.preventDefault();
            rapportMenu.classList.toggle('hidden');
        });
        
        // Fermer le menu si on clique ailleurs
        document.addEventListener('click', function(e) {
            if (!rapportBtn.contains(e.target) && !rapportMenu.contains(e.target)) {
                rapportMenu.classList.add('hidden');
            }
        });
    }
    
    // Auto-refresh des statistiques toutes les 5 minutes
    setInterval(() => {
        // Ici on pourrait faire un appel AJAX pour rafraîchir les stats et le graphique
        console.log('Rafraîchissement automatique des statistiques...');
    }, 300000); // 5 minutes
});
</script>
@endpush
@endsection