@extends('layouts.app')

@section('title', 'Gestion des Mensualités')

@section('head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
<div class="min-h-screen bg-gray-50 py-6" x-data="{ activeTab: '{{ $activeTab ?? 'paiements' }}', activeSubTab: 'recus-consultation' }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Messages d'alerte -->
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Succès!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times cursor-pointer"></i>
                </span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Erreur!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times cursor-pointer"></i>
                </span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Erreurs de validation:</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times cursor-pointer"></i>
                </span>
            </div>
        @endif
        
        <!-- En-tête -->
        <div class="mb-8">
            <div class="flex items-center space-x-3 mb-2">
                <div class="bg-green-100 p-2 rounded-lg">
                    <i class="fas fa-money-bill-wave text-green-600 text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Gestion des Mensualités</h1>
                    <p class="text-sm text-gray-600">Enregistrement et suivi des paiements mensuels - Année {{ $anneeActive->nom }}</p>
                </div>
            </div>
        </div>

        <!-- Navigation par onglets -->
        <div class="bg-white rounded-lg shadow-sm border mb-6">
            <nav class="flex space-x-1 p-1">
                <button @click="activeTab = 'paiements'" 
                        :class="activeTab === 'paiements' ? 'bg-green-600 text-white shadow-md' : 'text-gray-600 hover:text-green-600 hover:bg-gray-50'"
                        class="flex items-center px-4 py-3 rounded-md font-medium text-sm transition-all duration-200 flex-1 justify-center">
                    <i class="fas fa-money-bill-wave mr-2"></i>Paiements
                </button>
                <button @click="activeTab = 'historique'" 
                        :class="activeTab === 'historique' ? 'bg-green-600 text-white shadow-md' : 'text-gray-600 hover:text-green-600 hover:bg-gray-50'"
                        class="flex items-center px-4 py-3 rounded-md font-medium text-sm transition-all duration-200 flex-1 justify-center">
                    <i class="fas fa-history mr-2"></i>Historique
                </button>
                <button @click="activeTab = 'tableau-bord'" 
                        :class="activeTab === 'tableau-bord' ? 'bg-green-600 text-white shadow-md' : 'text-gray-600 hover:text-green-600 hover:bg-gray-50'"
                        class="flex items-center px-4 py-3 rounded-md font-medium text-sm transition-all duration-200 flex-1 justify-center">
                    <i class="fas fa-chart-pie mr-2"></i>Tableau de Bord
                </button>
                <button @click="activeTab = 'recus'; activeSubTab = 'recus-consultation'" 
                        :class="activeTab === 'recus' ? 'bg-green-600 text-white shadow-md' : 'text-gray-600 hover:text-green-600 hover:bg-gray-50'"
                        class="flex items-center px-4 py-3 rounded-md font-medium text-sm transition-all duration-200 flex-1 justify-center">
                    <i class="fas fa-receipt mr-2"></i>Reçus & Rapports
                </button>
            </nav>
        </div>

        <!-- Messages de succès/erreur -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                <div class="flex">
                    <i class="fas fa-check-circle mr-2 mt-0.5"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                <div class="flex">
                    <i class="fas fa-exclamation-circle mr-2 mt-0.5"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Onglet PAIEMENTS -->
        <div x-show="activeTab === 'paiements'" x-transition class="space-y-6">
            <!-- Recherche d'élève -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Rechercher un élève</h3>
                </div>
                
                <form method="GET" action="{{ route('mensualites.index') }}" class="space-y-4">
                    <input type="hidden" name="tab" value="paiements">
                    
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" 
                               name="search_eleve" 
                               id="search_eleve"
                               placeholder="Rechercher par nom, prénom ou INE..."
                               class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500"
                               autocomplete="off">
                        
                        <!-- Dropdown des résultats de recherche -->
                        <div id="search_results" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto">
                            <!-- Les résultats seront ajoutés ici via JavaScript -->
                        </div>
                    </div>
                </form>
            </div>

            <!-- Élève sélectionné et ses mensualités -->
            @if($selectedEleve)
                <!-- Informations de l'élève -->
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ strtoupper($selectedEleve->preInscription->nom) }} {{ ucwords(strtolower($selectedEleve->preInscription->prenom)) }}
                            </h3>
                            <div class="flex items-center space-x-4 text-sm text-gray-600 mt-1">
                                <span><strong>INE:</strong> {{ $selectedEleve->preInscription->ine }}</span>
                                <span><strong>Classe:</strong> {{ $selectedEleve->classe->nom }}</span>
                                <span><strong>Niveau:</strong> {{ $selectedEleve->niveau->nom }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-blue-600">
                                @php
                                    $totalPaye = $mensualites->sum('montant_paye');
                                    $totalDu = $mensualites->sum('montant_du');
                                @endphp
                                {{ number_format($totalPaye, 0, ',', ' ') }} FCFA / {{ number_format($totalDu, 0, ',', ' ') }} FCFA
                            </div>
                            <div class="text-sm text-gray-500">Total payé / Total dû</div>
                        </div>
                    </div>
                </div>

                <!-- Tableau des mensualités -->
                <div class="bg-white rounded-lg shadow-sm border">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Mensualités {{ $anneeActive->nom }}</h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mois</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant Dû</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payé</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reste</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($mensualites as $mensualite)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                        {{ ucfirst(strtolower($mensualite->mois_paiement)) }} {{ substr($anneeActive->nom, 0, 4) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ number_format($mensualite->montant_du, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ number_format($mensualite->montant_paye, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        @php $reste = $mensualite->montant_du - $mensualite->montant_paye; @endphp
                                        <span class="{{ $reste > 0 ? 'text-red-600 font-medium' : 'text-green-600' }}">
                                            {{ number_format($reste, 0, ',', ' ') }} FCFA
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        @switch($mensualite->statut)
                                            @case('impaye')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <i class="fas fa-clock mr-1"></i>En attente
                                                </span>
                                                @break
                                            @case('partiel')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                    <i class="fas fa-coins mr-1"></i>Partiel
                                                </span>
                                                @break
                                            @case('complet')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-check mr-1"></i>Payée
                                                </span>
                                                @break
                                            @default
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ ucfirst($mensualite->statut) }}
                                                </span>
                                        @endswitch
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm space-x-2">
                                        @if($mensualite->statut !== 'complet')
                                            <button onclick="openModalPaiement({{ $mensualite->id }}, '{{ $mensualite->mois_paiement }}', {{ $mensualite->montant_du }}, {{ $mensualite->montant_paye }})"
                                                    class="bg-blue-600 text-white px-3 py-1.5 rounded text-xs font-medium hover:bg-blue-700">
                                                <i class="fas fa-money-bill mr-1"></i>Payer
                                            </button>
                                        @endif
                                        
                                        @if($mensualite->numero_recu)
                                            <a href="{{ route('mensualites.voir-recu', $mensualite->id) }}"
                                               class="bg-green-600 text-white px-3 py-1.5 rounded text-xs font-medium hover:bg-green-700" target="_blank">
                                                <i class="fas fa-receipt mr-1"></i>Reçu
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <!-- Message si aucun élève sélectionné -->
                <div class="bg-white rounded-lg shadow-sm border p-8 text-center">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-search text-4xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Recherchez un élève</h3>
                    <p class="text-gray-600">Utilisez la barre de recherche ci-dessus pour trouver un élève et voir ses mensualités.</p>
                </div>
            @endif
        </div>

        <!-- Onglet HISTORIQUE -->
        <div x-show="activeTab === 'historique'" x-transition class="space-y-6">
            <!-- Filtres -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-filter text-blue-600 mr-2"></i>
                    Filtres de recherche
                </h3>
                
                <form method="GET" action="{{ route('mensualites.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <input type="hidden" name="tab" value="historique">
                    
                    <!-- Filtre par année scolaire -->
                    <div>
                        <label for="filter_annee" class="block text-sm font-medium text-gray-700 mb-2">Année scolaire</label>
                        <select name="filter_annee" id="filter_annee" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                            @foreach($anneesScolaires ?? [] as $annee)
                                <option value="{{ $annee->id }}" {{ (request('filter_annee', $anneeActive->id) == $annee->id) ? 'selected' : '' }}>
                                    {{ $annee->libelle }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Filtre par classe -->
                    <div>
                        <label for="filter_classe" class="block text-sm font-medium text-gray-700 mb-2">Classe</label>
                        <select name="filter_classe" id="filter_classe" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Toutes les classes</option>
                            @foreach($classes ?? [] as $classe)
                                <option value="{{ $classe->id }}" {{ request('filter_classe') == $classe->id ? 'selected' : '' }}>
                                    {{ $classe->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtre par mois -->
                    <div>
                        <label for="filter_mois" class="block text-sm font-medium text-gray-700 mb-2">Mois</label>
                        <select name="filter_mois" id="filter_mois" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tous les mois</option>
                            @foreach(\App\Models\Mensualite::getMoisOptions() as $key => $label)
                                <option value="{{ $key }}" {{ request('filter_mois') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtre par statut - CORRIGÉ -->
                    <div>
                        <label for="filter_statut" class="block text-sm font-medium text-gray-700 mb-2">Statut de paiement</label>
                        <select name="filter_statut" id="filter_statut" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tous les statuts</option>
                            <option value="paye" {{ request('filter_statut') == 'paye' ? 'selected' : '' }}>✅ Payés (complet + partiel)</option>
                            <option value="complet" {{ request('filter_statut') == 'complet' ? 'selected' : '' }}>🟢 Payé intégralement</option>
                            <option value="partiel" {{ request('filter_statut') == 'partiel' ? 'selected' : '' }}>🟡 Paiement partiel</option>
                            <option value="impaye" {{ request('filter_statut') == 'impaye' ? 'selected' : '' }}>🔴 Impayés</option>
                        </select>
                    </div>

                    <!-- Période -->
                    <div>
                        <label for="filter_periode" class="block text-sm font-medium text-gray-700 mb-2">Période</label>
                        <select name="filter_periode" id="filter_periode" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Toute la période</option>
                            <option value="7" {{ request('filter_periode') == '7' ? 'selected' : '' }}>7 derniers jours</option>
                            <option value="30" {{ request('filter_periode') == '30' ? 'selected' : '' }}>30 derniers jours</option>
                            <option value="90" {{ request('filter_periode') == '90' ? 'selected' : '' }}>90 derniers jours</option>
                        </select>
                    </div>

                    <!-- Boutons -->
                    <div class="md:col-span-2 lg:col-span-5 flex justify-between items-center pt-4 border-t border-gray-200">
                        <div class="flex space-x-3">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                                <i class="fas fa-search mr-2"></i>Filtrer
                            </button>
                            <a href="{{ route('mensualites.index', ['tab' => 'historique']) }}" 
                               class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                <i class="fas fa-times mr-2"></i>Réinitialiser
                            </a>
                        </div>
                        <button type="button" onclick="exportPaiements()" 
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700">
                            <i class="fas fa-file-export mr-2"></i>Exporter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Historique des paiements -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-history text-blue-600 mr-2"></i>
                            Historique des Paiements
                        </h3>
                        <div class="text-sm text-gray-600">
                            Total: <span class="font-medium">{{ $historiqueCount ?? 0 }}</span> paiements
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Élève</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Classe</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Mois</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28">Montant</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Statut</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Date</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($historiquePaiements ?? [] as $mensualite)
                                @php
                                    // Calculer les informations pour cette mensualité
                                    $reste = $mensualite->montant_du - $mensualite->montant_paye;
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-gray-900 truncate" style="max-width: 160px;">
                                            {{ $mensualite->inscription->preInscription->nom ?? 'N/A' }}
                                            {{ $mensualite->inscription->preInscription->prenom ?? 'N/A' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $mensualite->inscription->preInscription->ine ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-sm text-gray-900">
                                        {{ $mensualite->inscription->classe->nom ?? 'N/A' }}
                                    </td>
                                    <td class="px-3 py-3 text-sm text-gray-900">
                                        {{ ucfirst($mensualite->mois_paiement) }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ number_format($mensualite->montant_paye, 0, ',', ' ') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            / {{ number_format($mensualite->montant_du, 0, ',', ' ') }}
                                        </div>
                                    </td>
                                    <td class="px-3 py-3">
                                        @switch($mensualite->statut)
                                            @case('complet')
                                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                    Complet
                                                </span>
                                                @break
                                            @case('partiel')
                                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                                    Partiel
                                                </span>
                                                @break
                                            @case('impaye')
                                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                                    Impayé
                                                </span>
                                                @break
                                            @default
                                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                                    {{ ucfirst($mensualite->statut) }}
                                                </span>
                                        @endswitch
                                    </td>
                                    <td class="px-3 py-3 text-xs text-gray-900">
                                        {{ $mensualite->date_paiement ? $mensualite->date_paiement->format('d/m/y') : 'N/A' }}
                                    </td>
                                    <td class="px-3 py-3 text-xs space-x-1">
                                        @if($mensualite->numero_recu)
                                            <a href="{{ route('mensualites.voir-recu', $mensualite->id) }}"
                                               class="inline-flex items-center px-2 py-1 text-xs text-white bg-green-600 rounded hover:bg-green-700" 
                                               target="_blank" title="Voir le reçu">
                                                <i class="fas fa-receipt"></i>
                                            </a>
                                        @endif
                                        
                                        <button onclick="modifierPaiement({{ $mensualite->id }})"
                                                class="inline-flex items-center px-2 py-1 text-xs text-white bg-blue-600 rounded hover:bg-blue-700"
                                                title="Modifier le paiement">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        @if($mensualite->created_at->diffInHours(now()) <= 24)
                                            <button onclick="supprimerPaiement({{ $mensualite->id }}, '{{ $mensualite->inscription->preInscription->nom }} {{ $mensualite->inscription->preInscription->prenom }}')"
                                                    class="inline-flex items-center px-2 py-1 text-xs text-white bg-red-600 rounded hover:bg-red-700"
                                                    title="Supprimer le paiement">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-receipt text-4xl text-gray-300 mb-4"></i>
                                            <p class="text-lg font-medium">Aucun paiement trouvé</p>
                                            <p class="text-sm">Les paiements effectués apparaîtront ici</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($historiquePaiements && method_exists($historiquePaiements, 'hasPages') && $historiquePaiements->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $historiquePaiements->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Onglet TABLEAU DE BORD -->
        <div x-show="activeTab === 'tableau-bord'" x-transition class="space-y-6">
            @if($dashboardStats)
                <!-- Statistiques générales - Prévisions et élèves -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <!-- Élèves inscrits -->
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Élèves Inscrits</p>
                                <p class="text-2xl font-bold text-blue-600">{{ $dashboardStats['general']['eleves_inscrits'] }}</p>
                                <p class="text-xs text-gray-500">Pour cette année scolaire</p>
                            </div>
                            <div class="p-3 bg-blue-100 rounded-full">
                                <i class="fas fa-user-graduate text-xl text-blue-600"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Mensualités attendues (prévision) -->
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Mensualités Attendues</p>
                                <p class="text-2xl font-bold text-indigo-600">
                                    {{ number_format($dashboardStats['general']['mensualites_attendues'] ?? ($dashboardStats['general']['eleves_inscrits'] * 10), 0, ',', ' ') }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $dashboardStats['general']['eleves_inscrits'] }} élèves × 10 mois</p>
                            </div>
                            <div class="p-3 bg-indigo-100 rounded-full">
                                <i class="fas fa-calculator text-xl text-indigo-600"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Mensualités créées vs attendues -->
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Mensualités Créées</p>
                                <p class="text-2xl font-bold text-cyan-600">{{ number_format($dashboardStats['general']['total_mensualites'], 0, ',', ' ') }}</p>
                                @php
                                    $attendues = $dashboardStats['general']['mensualites_attendues'] ?? ($dashboardStats['general']['eleves_inscrits'] * 10);
                                    $tauxCouverture = $attendues > 0 ? round(($dashboardStats['general']['total_mensualites'] / $attendues) * 100, 2) : 0;
                                @endphp
                                <p class="text-xs text-gray-500">
                                    {{ $dashboardStats['general']['taux_couverture_mensualites'] ?? $tauxCouverture }}% des attendues
                                </p>
                            </div>
                            <div class="p-3 bg-cyan-100 rounded-full">
                                <i class="fas fa-clipboard-list text-xl text-cyan-600"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Mensualités manquantes -->
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        @php
                            $attendues = $dashboardStats['general']['mensualites_attendues'] ?? ($dashboardStats['general']['eleves_inscrits'] * 10);
                            $manquantes = $dashboardStats['general']['mensualites_manquantes'] ?? max(0, $attendues - $dashboardStats['general']['total_mensualites']);
                            $colorClass = $manquantes > 1000 ? 'text-red-600' : ($manquantes > 500 ? 'text-orange-600' : 'text-green-600');
                            $bgClass = $manquantes > 1000 ? 'bg-red-100' : ($manquantes > 500 ? 'bg-orange-100' : 'bg-green-100');
                        @endphp
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Mensualités Manquantes</p>
                                <p class="text-2xl font-bold {{ $colorClass }}">{{ number_format($manquantes, 0, ',', ' ') }}</p>
                                <p class="text-xs text-gray-500">À créer pour complétude</p>
                            </div>
                            <div class="p-3 {{ $bgClass }} rounded-full">
                                <i class="fas fa-exclamation-triangle text-xl {{ $colorClass }}"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Prévisions Financières -->
                <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-chart-line text-2xl mr-3 text-indigo-600"></i>
                        <h3 class="text-lg font-semibold text-gray-900">Prévisions Financières</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @php
                            $montantMoyen = $dashboardStats['general']['montant_moyen_mensualite'] ?? 
                                ($dashboardStats['general']['total_mensualites'] > 0 ? $dashboardStats['general']['montant_total'] / $dashboardStats['general']['total_mensualites'] : 0);
                            $attendues = $dashboardStats['general']['mensualites_attendues'] ?? ($dashboardStats['general']['eleves_inscrits'] * 10);
                            $manquantes = $dashboardStats['general']['mensualites_manquantes'] ?? max(0, $attendues - $dashboardStats['general']['total_mensualites']);
                            $potentielManquant = $dashboardStats['general']['montant_potentiel_manquant'] ?? ($manquantes * $montantMoyen);
                        @endphp
                        <div class="text-center">
                            <div class="text-2xl font-bold text-indigo-600">{{ number_format($montantMoyen, 0, ',', ' ') }}</div>
                            <div class="text-gray-600">Montant Moyen par Mensualité (FCFA)</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ number_format($potentielManquant, 0, ',', ' ') }}</div>
                            <div class="text-gray-600">Montant Potentiel Manquant (FCFA)</div>
                        </div>
                        <div class="text-center">
                            @php
                                $montantTotalPotentiel = $dashboardStats['general']['montant_total'] + $potentielManquant;
                            @endphp
                            <div class="text-2xl font-bold text-green-600">{{ number_format($montantTotalPotentiel, 0, ',', ' ') }}</div>
                            <div class="text-gray-600">Montant Total Potentiel (FCFA)</div>
                        </div>
                    </div>
                </div>

                <!-- Statistiques des mensualités -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total mensualités -->
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Total Mensualités</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $dashboardStats['general']['total_mensualites'] }}</p>
                            </div>
                            <div class="p-3 bg-blue-100 rounded-full">
                                <i class="fas fa-calendar-alt text-xl text-blue-600"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Mensualités payées (Total) -->
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Mensualités Payées (Total)</p>
                                <p class="text-2xl font-bold text-green-600">{{ $dashboardStats['general']['mensualites_paye'] }}</p>
                                @if($dashboardStats['general']['total_mensualites'] > 0)
                                    <p class="text-xs text-gray-500">
                                        {{ round(($dashboardStats['general']['mensualites_paye'] / $dashboardStats['general']['total_mensualites']) * 100, 1) }}%
                                    </p>
                                @endif
                                <p class="text-xs text-blue-500 mt-1">
                                    Complet: {{ $dashboardStats['general']['mensualites_complet'] ?? 0 }} | 
                                    Partiel: {{ $dashboardStats['general']['mensualites_partiel'] ?? 0 }}
                                </p>
                            </div>
                            <div class="p-3 bg-green-100 rounded-full">
                                <i class="fas fa-check-circle text-xl text-green-600"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Mensualités impayées -->
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Mensualités Impayées</p>
                                <p class="text-2xl font-bold text-red-600">{{ $dashboardStats['general']['mensualites_impaye'] }}</p>
                                @if($dashboardStats['general']['total_mensualites'] > 0)
                                    <p class="text-xs text-gray-500">
                                        {{ round(($dashboardStats['general']['mensualites_impaye'] / $dashboardStats['general']['total_mensualites']) * 100, 1) }}%
                                    </p>
                                @endif
                            </div>
                            <div class="p-3 bg-red-100 rounded-full">
                                <i class="fas fa-times-circle text-xl text-red-600"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Taux de recouvrement -->
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Taux de Recouvrement</p>
                                <p class="text-2xl font-bold text-purple-600">{{ $dashboardStats['general']['taux_recouvrement'] }}%</p>
                                <p class="text-xs text-gray-500">
                                    {{ number_format($dashboardStats['general']['montant_paye'], 0, ',', ' ') }} / 
                                    {{ number_format($dashboardStats['general']['montant_total'], 0, ',', ' ') }} FCFA
                                </p>
                            </div>
                            <div class="p-3 bg-purple-100 rounded-full">
                                <i class="fas fa-percentage text-xl text-purple-600"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Paiements Partiels -->
                @if($dashboardStats['general']['mensualites_partiel'] > 0)
                <div class="bg-gradient-to-r from-orange-50 to-yellow-50 rounded-lg shadow-sm border border-orange-200 p-6 mb-6">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-coins text-2xl mr-3 text-orange-600"></i>
                        <h3 class="text-lg font-semibold text-gray-900">Attention aux Paiements Partiels</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-orange-600">{{ $dashboardStats['general']['mensualites_partiel'] }}</div>
                            <div class="text-gray-600">Paiements Partiels</div>
                        </div>
                        <div class="text-center">
                            @php
                                $montantPartielMoyen = $dashboardStats['general']['mensualites_partiel'] > 0 ? 
                                    ($dashboardStats['general']['montant_paye'] - ($dashboardStats['general']['mensualites_complet'] * ($dashboardStats['general']['montant_total'] / max(1, $dashboardStats['general']['total_mensualites'])))) : 0;
                            @endphp
                            <div class="text-2xl font-bold text-yellow-600">{{ number_format($dashboardStats['general']['montant_restant'], 0, ',', ' ') }}</div>
                            <div class="text-gray-600">Montant Restant (FCFA)</div>
                        </div>
                        <div class="text-center">
                            @php
                                $pourcentagePartiel = $dashboardStats['general']['total_mensualites'] > 0 ? 
                                    round(($dashboardStats['general']['mensualites_partiel'] / $dashboardStats['general']['total_mensualites']) * 100, 1) : 0;
                            @endphp
                            <div class="text-2xl font-bold text-red-600">{{ $pourcentagePartiel }}%</div>
                            <div class="text-gray-600">Taux Paiements Partiels</div>
                        </div>
                    </div>
                    <div class="mt-4 p-3 bg-orange-100 rounded-lg">
                        <p class="text-sm text-orange-800">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            {{ $dashboardStats['general']['mensualites_partiel'] }} mensualités nécessitent un suivi pour compléter les paiements.
                        </p>
                    </div>
                </div>
                @endif

                <!-- Graphiques et statistiques détaillées -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Statistiques par mois -->
                    <div class="bg-white rounded-lg shadow-sm border">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                                Statistiques par Mois
                            </h3>
                        </div>
                        <div class="p-6">
                            @if($dashboardStats['par_mois']->count() > 0)
                                <div class="space-y-4">
                                    @foreach($dashboardStats['par_mois'] as $mois)
                                        @php
                                            $tauxPaiement = $mois->total > 0 ? round(($mois->paye / $mois->total) * 100, 1) : 0;
                                        @endphp
                                        <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                                            <div class="flex justify-between items-center mb-2">
                                                <span class="text-sm font-medium text-gray-900">{{ ucfirst($mois->mois_paiement) }}</span>
                                                <span class="text-sm text-gray-600">{{ $mois->paye }}/{{ $mois->total }} ({{ $tauxPaiement }}%)</span>
                                            </div>
                                            
                                            <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $tauxPaiement }}%"></div>
                                            </div>
                                            
                                            <div class="flex justify-between text-xs text-gray-500 mb-2">
                                                <span>Payé: {{ number_format($mois->montant_paye_total, 0, ',', ' ') }} FCFA</span>
                                                <span>Total: {{ number_format($mois->montant_du_total, 0, ',', ' ') }} FCFA</span>
                                            </div>
                                            
                                            @if(isset($mois->partiel) && $mois->partiel > 0)
                                            <div class="flex justify-between text-xs">
                                                <span class="text-green-600">
                                                    <i class="fas fa-check-circle mr-1"></i>{{ $mois->complet ?? 0 }} complets
                                                </span>
                                                <span class="text-orange-600">
                                                    <i class="fas fa-coins mr-1"></i>{{ $mois->partiel }} partiels
                                                </span>
                                            </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 text-center py-4">Aucune donnée disponible</p>
                            @endif
                        </div>
                    </div>

                    <!-- Statistiques par niveau -->
                    <div class="bg-white rounded-lg shadow-sm border">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <i class="fas fa-graduation-cap text-green-600 mr-2"></i>
                                Statistiques par Niveau
                            </h3>
                        </div>
                        <div class="p-6">
                            @if($dashboardStats['par_niveau']->count() > 0)
                                <div class="space-y-4">
                                    @foreach($dashboardStats['par_niveau'] as $niveau)
                                        @php
                                            $tauxPaiement = $niveau->total > 0 ? round(($niveau->paye / $niveau->total) * 100, 1) : 0;
                                        @endphp
                                        <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                                            <div class="flex justify-between items-center mb-2">
                                                <span class="text-sm font-medium text-gray-900">{{ $niveau->niveau_nom }}</span>
                                                <span class="text-sm text-gray-600">{{ $niveau->paye }}/{{ $niveau->total }} ({{ $tauxPaiement }}%)</span>
                                            </div>
                                            
                                            <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $tauxPaiement }}%"></div>
                                            </div>
                                            
                                            <div class="flex justify-between text-xs text-gray-500">
                                                <span>Payé: {{ number_format($niveau->montant_paye_total, 0, ',', ' ') }} FCFA</span>
                                                <span>Total: {{ number_format($niveau->montant_du_total, 0, ',', ' ') }} FCFA</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 text-center py-4">Aucune donnée disponible</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Top classes et paiements récents -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Top 5 des meilleures classes -->
                    <div class="bg-white rounded-lg shadow-sm border">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <i class="fas fa-trophy text-yellow-600 mr-2"></i>
                                Top 5 des Meilleures Classes
                            </h3>
                        </div>
                        <div class="p-6">
                            @if($dashboardStats['top_classes']->count() > 0)
                                <div class="space-y-4">
                                    @foreach($dashboardStats['top_classes'] as $index => $classe)
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-shrink-0">
                                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                                                    {{ $index === 0 ? 'bg-yellow-100 text-yellow-800' : 
                                                       ($index === 1 ? 'bg-gray-100 text-gray-800' : 
                                                        ($index === 2 ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800')) }}">
                                                    {{ $index + 1 }}
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex justify-between items-center">
                                                    <span class="text-sm font-medium text-gray-900">{{ $classe->classe_nom }}</span>
                                                    <span class="text-sm font-semibold text-green-600">{{ $classe->taux_paiement }}%</span>
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $classe->mensualites_payees }}/{{ $classe->total_mensualites }} mensualités payées
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 text-center py-4">Aucune donnée disponible</p>
                            @endif
                        </div>
                    </div>

                    <!-- Paiements récents -->
                    <div class="bg-white rounded-lg shadow-sm border">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <i class="fas fa-clock text-blue-600 mr-2"></i>
                                Paiements Récents (7 derniers jours)
                            </h3>
                        </div>
                        <div class="p-6">
                            @if($dashboardStats['paiements_recents']->count() > 0)
                                <div class="space-y-4">
                                    @foreach($dashboardStats['paiements_recents'] as $paiement)
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-check text-green-600"></i>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex justify-between items-center">
                                                    <span class="text-sm font-medium text-gray-900">
                                                        {{ $paiement->inscription->preInscription->nom }} {{ $paiement->inscription->preInscription->prenom }}
                                                    </span>
                                                    <span class="text-sm font-semibold text-green-600">
                                                        {{ number_format($paiement->montant_paye, 0, ',', ' ') }} FCFA
                                                    </span>
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $paiement->inscription->classe->nom }} • 
                                                    {{ ucfirst($paiement->mois_paiement) }} • 
                                                    {{ $paiement->date_paiement->format('d/m/Y à H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 text-center py-4">Aucun paiement récent</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Analyse et Recommandations -->
                <div class="bg-white rounded-lg shadow-sm border">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                            Analyse et Recommandations
                        </h3>
                    </div>
                    <div class="p-6">
                        @php
                            $attendues = $dashboardStats['general']['mensualites_attendues'] ?? ($dashboardStats['general']['eleves_inscrits'] * 10);
                            $manquantes = $dashboardStats['general']['mensualites_manquantes'] ?? max(0, $attendues - $dashboardStats['general']['total_mensualites']);
                            $tauxCouverture = $dashboardStats['general']['taux_couverture_mensualites'] ?? 
                                ($attendues > 0 ? round(($dashboardStats['general']['total_mensualites'] / $attendues) * 100, 2) : 0);
                            $tauxRecouvrement = $dashboardStats['general']['taux_recouvrement'];
                            $elevesAvecMensualites = $dashboardStats['general']['eleves_avec_mensualites'];
                            $elevesInscrits = $dashboardStats['general']['eleves_inscrits'];
                            $elevesSansMensualites = $elevesInscrits - $elevesAvecMensualites;
                        @endphp
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Alertes -->
                            <div>
                                <h4 class="font-medium text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                                    Alertes et Points d'Attention
                                </h4>
                                <div class="space-y-3">
                                    @if($elevesSansMensualites > 0)
                                        <div class="flex items-start space-x-3 p-3 bg-red-50 rounded-lg border border-red-200">
                                            <i class="fas fa-user-slash text-red-500 mt-1"></i>
                                            <div>
                                                <p class="text-sm font-medium text-red-800">
                                                    {{ $elevesSansMensualites }} élève(s) sans mensualités
                                                </p>
                                                <p class="text-xs text-red-600">
                                                    Ces élèves n'ont aucune mensualité créée. Action requise.
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($manquantes > 100)
                                        <div class="flex items-start space-x-3 p-3 bg-orange-50 rounded-lg border border-orange-200">
                                            <i class="fas fa-calendar-times text-orange-500 mt-1"></i>
                                            <div>
                                                <p class="text-sm font-medium text-orange-800">
                                                    {{ number_format($manquantes, 0, ',', ' ') }} mensualités manquantes
                                                </p>
                                                <p class="text-xs text-orange-600">
                                                    Retard dans la création des mensualités mensuelles.
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($tauxRecouvrement < 50)
                                        <div class="flex items-start space-x-3 p-3 bg-red-50 rounded-lg border border-red-200">
                                            <i class="fas fa-chart-line text-red-500 mt-1"></i>
                                            <div>
                                                <p class="text-sm font-medium text-red-800">
                                                    Taux de recouvrement faible ({{ $tauxRecouvrement }}%)
                                                </p>
                                                <p class="text-xs text-red-600">
                                                    Intensifier les relances de paiement.
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($manquantes == 0 && $tauxRecouvrement > 80)
                                        <div class="flex items-start space-x-3 p-3 bg-green-50 rounded-lg border border-green-200">
                                            <i class="fas fa-check-circle text-green-500 mt-1"></i>
                                            <div>
                                                <p class="text-sm font-medium text-green-800">
                                                    Excellent suivi des mensualités !
                                                </p>
                                                <p class="text-xs text-green-600">
                                                    Toutes les mensualités sont créées et bien recouvrées.
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Recommandations -->
                            <div>
                                <h4 class="font-medium text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-tasks text-blue-500 mr-2"></i>
                                    Actions Recommandées
                                </h4>
                                <div class="space-y-3">
                                    @if($elevesSansMensualites > 0)
                                        <div class="flex items-start space-x-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                            <i class="fas fa-plus-circle text-blue-500 mt-1"></i>
                                            <div>
                                                <p class="text-sm font-medium text-blue-800">Créer les mensualités manquantes</p>
                                                <p class="text-xs text-blue-600">
                                                    Utiliser la fonction de création automatique pour {{ $elevesSansMensualites }} élève(s).
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($dashboardStats['general']['mensualites_impaye'] > 0)
                                        <div class="flex items-start space-x-3 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                                            <i class="fas fa-bell text-yellow-500 mt-1"></i>
                                            <div>
                                                <p class="text-sm font-medium text-yellow-800">Relancer les impayés</p>
                                                <p class="text-xs text-yellow-600">
                                                    {{ $dashboardStats['general']['mensualites_impaye'] }} mensualité(s) impayée(s) à relancer.
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div class="flex items-start space-x-3 p-3 bg-indigo-50 rounded-lg border border-indigo-200">
                                        <i class="fas fa-chart-bar text-indigo-500 mt-1"></i>
                                        <div>
                                            <p class="text-sm font-medium text-indigo-800">Analyser les performances par classe</p>
                                            <p class="text-xs text-indigo-600">
                                                Identifier les classes avec les meilleurs/moins bons taux de paiement.
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start space-x-3 p-3 bg-purple-50 rounded-lg border border-purple-200">
                                        <i class="fas fa-file-export text-purple-500 mt-1"></i>
                                        <div>
                                            <p class="text-sm font-medium text-purple-800">Générer des rapports</p>
                                            <p class="text-xs text-purple-600">
                                                Créer des rapports pour le suivi administratif et financier.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Résumé financier -->
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg shadow-sm text-white p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold">{{ number_format($dashboardStats['general']['montant_total'], 0, ',', ' ') }}</div>
                            <div class="text-blue-100">Montant Total Attendu (FCFA)</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold">{{ number_format($dashboardStats['general']['montant_paye'], 0, ',', ' ') }}</div>
                            <div class="text-blue-100">Montant Total Perçu (FCFA)</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold">{{ number_format($dashboardStats['general']['montant_restant'], 0, ',', ' ') }}</div>
                            <div class="text-blue-100">Montant Restant à Percevoir (FCFA)</div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm border p-8 text-center">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-chart-pie text-4xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tableau de Bord</h3>
                    <p class="text-gray-600">Chargement des statistiques...</p>
                </div>
            @endif
        </div>

        <!-- Onglet REÇUS & RAPPORTS -->
        <div x-show="activeTab === 'recus'" x-transition class="space-y-6">
            @include('mensualites.partials.reports_mensualites')
        </div>

<!-- Modal de Paiement -->
<div id="modalPaiement" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
            <!-- En-tête de la modale -->
            <div class="sticky top-0 bg-white px-6 py-4 border-b border-gray-200 rounded-t-lg">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-money-bill-wave text-green-600 mr-2"></i>
                        Enregistrer Paiement
                    </h3>
                    <button type="button" onclick="closeModalPaiement()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Corps de la modale -->
            <form method="POST" action="{{ route('mensualites.enregistrer-paiement') }}" id="formPaiement" class="px-6 py-4">
                @csrf
                <input type="hidden" name="mensualite_id" id="paiement_mensualite_id">
                
                <!-- Informations du paiement -->
                <div class="mb-6">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-lg border border-blue-200">
                        <h4 class="font-medium text-gray-900 mb-3 flex items-center">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            Détails du paiement
                        </h4>
                        <div class="grid grid-cols-1 gap-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Élève:</span>
                                <span class="font-medium">{{ $selectedEleve->preInscription->nom ?? '' }} {{ $selectedEleve->preInscription->prenom ?? '' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Mois:</span>
                                <span class="font-medium" id="paiement_mois"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Montant dû:</span>
                                <span class="font-medium text-blue-600" id="paiement_montant_du"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Déjà payé:</span>
                                <span class="font-medium text-green-600" id="paiement_montant_paye"></span>
                            </div>
                            <div class="flex justify-between border-t pt-2 mt-2">
                                <span class="text-gray-600 font-medium">Reste à payer:</span>
                                <span class="font-bold text-red-600" id="paiement_reste"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Montant reçu -->
                <div class="mb-4">
                    <label for="montant_recu" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-coins text-green-600 mr-1"></i>
                        Montant reçu *
                    </label>
                    <div class="relative">
                        <input type="number" name="montant_recu" id="montant_recu" min="1" step="1" required
                               class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Entrez le montant...">
                        <div class="absolute left-2 top-2 text-gray-500">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div class="absolute right-2 top-2 text-gray-500 text-sm">
                            FCFA
                        </div>
                    </div>
                </div>

                <!-- Case paiement complet -->
                <div class="mb-4">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <!-- Hidden input pour s'assurer qu'une valeur est toujours envoyée -->
                        <input type="hidden" name="paiement_complet" value="0">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="paiement_complet" id="paiement_complet" value="1"
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-3 text-sm">
                                <i class="fas fa-check-circle text-yellow-600 mr-1"></i>
                                <span class="text-gray-700">Paiement complet (</span>
                                <span class="font-medium text-yellow-700" id="montant_complet"></span>
                                <span class="text-gray-700"> FCFA)</span>
                            </span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1 ml-7">Cochez cette case pour un paiement intégral du solde restant</p>
                    </div>
                </div>

                <!-- Mode de paiement -->
                <div class="mb-4">
                    <label for="mode_paiement" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-credit-card text-blue-600 mr-1"></i>
                        Mode de paiement *
                    </label>
                    <select name="mode_paiement" id="mode_paiement" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Sélectionnez un mode de paiement...</option>
                        <option value="especes">💰 Espèces</option>
                        <option value="cheque">🏦 Chèque</option>
                        <option value="virement">📱 Virement</option>
                        <option value="orange_money">📱 Orange Money</option>
                        <option value="wave">📱 Wave</option>
                        <option value="free_money">📱 Free Money</option>
                    </select>
                </div>

                <!-- Observations -->
                <div class="mb-6">
                    <label for="observations" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-sticky-note text-gray-600 mr-1"></i>
                        Observations
                    </label>
                    <textarea name="observations" id="observations" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Remarques ou observations particulières..."></textarea>
                </div>
            </form>

            <!-- Pied de la modale avec boutons -->
            <div class="sticky bottom-0 bg-gray-50 px-6 py-4 border-t border-gray-200 rounded-b-lg">
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModalPaiement()" 
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-times mr-2"></i>
                        Annuler
                    </button>
                    <button type="submit" form="formPaiement"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i>
                        Enregistrer Paiement
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour modifier un paiement -->
<div id="modalModifierPaiement" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <!-- En-tête du modal -->
        <div class="sticky top-0 bg-white px-6 py-4 border-b border-gray-200 rounded-t-lg">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-edit text-blue-600 mr-2"></i>
                    Modifier le Paiement
                </h3>
                <button type="button" onclick="fermerModalModifier()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Contenu du modal -->
        <div class="px-6 py-4">
            <form id="formModifierPaiement" class="space-y-4">
                    <input type="hidden" id="editPaiementId">
                    
                    <!-- Informations de l'élève (en lecture seule) -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-full mt-0.5">
                                <i class="fas fa-user-graduate text-blue-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-blue-900 mb-1">Informations de l'élève</h4>
                                <p id="editEleveNom" class="text-sm text-blue-800 font-medium"></p>
                                <p id="editEleveInfo" class="text-xs text-blue-600"></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Montant dû (en lecture seule) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-money-bill-wave text-gray-400 mr-2"></i>Montant dû
                        </label>
                        <div id="editMontantDu" class="px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 font-medium"></div>
                    </div>
                    
                    <!-- Montant payé (modifiable) -->
                    <div>
                        <label for="editMontantPaye" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-coins text-gray-400 mr-2"></i>Montant payé *
                        </label>
                        <input type="number" id="editMontantPaye" name="montant_paye" step="1" min="0" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                               oninput="calculerStatutModification()" required>
                        <div id="editStatutPreview" class="mt-2 text-xs"></div>
                    </div>
                    
                    <!-- Mode de paiement -->
                    <div>
                        <label for="editModePaiement" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-credit-card text-gray-400 mr-2"></i>Mode de paiement *
                        </label>
                        <select id="editModePaiement" name="mode_paiement" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" required>
                            <option value="especes">💵 Espèces</option>
                            <option value="cheque">📝 Chèque</option>
                            <option value="virement">🏦 Virement</option>
                            <option value="carte">💳 Carte bancaire</option>
                            <option value="mobile">📱 Paiement mobile</option>
                        </select>
                    </div>
                    
                    <!-- Date de paiement -->
                    <div>
                        <label for="editDatePaiement" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt text-gray-400 mr-2"></i>Date de paiement *
                        </label>
                        <input type="date" id="editDatePaiement" name="date_paiement" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" required>
                    </div>
                    
                    <!-- Remarques -->
                    <div>
                        <label for="editRemarques" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-comment text-gray-400 mr-2"></i>Remarques
                        </label>
                        <textarea id="editRemarques" name="remarques" rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none"
                                  placeholder="Ajouter des remarques ou commentaires sur ce paiement..."></textarea>
                    </div>
                    
                    <!-- Information sur le reçu -->
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex items-center justify-center w-8 h-8 bg-amber-100 rounded-full mt-0.5">
                                <i class="fas fa-info-circle text-amber-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-amber-900 mb-1">Information importante</h4>
                                <p class="text-xs text-amber-800">Si vous modifiez le montant payé, un nouveau reçu sera automatiquement généré avec un nouveau numéro.</p>
                            </div>
                        </div>
                    </div>
            </form>
        </div>

        <!-- Pied du modal -->
        <div class="sticky bottom-0 bg-gray-50 px-6 py-3 border-t border-gray-200 rounded-b-lg">
            <div class="flex space-x-3">
                <button type="submit" form="formModifierPaiement"
                        class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-save mr-2"></i>
                    Enregistrer
                </button>
                <button type="button" onclick="fermerModalModifier()" 
                        class="inline-flex justify-center items-center px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Annuler
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
let currentMensualiteData = {};

// Recherche d'élèves
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search_eleve');
    const resultsDiv = document.getElementById('search_results');
    let searchTimeout;

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
                resultsDiv.classList.add('hidden');
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(`{{ route('mensualites.search-eleves') }}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        resultsDiv.innerHTML = '';
                        
                        if (data.length > 0) {
                            data.forEach(eleve => {
                                const div = document.createElement('div');
                                div.className = 'px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0';
                                div.innerHTML = `
                                    <div class="font-medium text-gray-900">${eleve.nom} ${eleve.prenom}</div>
                                    <div class="text-sm text-gray-600">${eleve.ine} - ${eleve.classe}</div>
                                `;
                                div.addEventListener('click', () => {
                                    window.location.href = `{{ route('mensualites.index') }}?tab=paiements&eleve_id=${eleve.id}`;
                                });
                                resultsDiv.appendChild(div);
                            });
                            resultsDiv.classList.remove('hidden');
                        } else {
                            resultsDiv.innerHTML = '<div class="px-4 py-3 text-gray-500 text-sm">Aucun élève trouvé</div>';
                            resultsDiv.classList.remove('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur de recherche:', error);
                        resultsDiv.classList.add('hidden');
                    });
            }, 300);
        });

        // Fermer les résultats si on clique ailleurs
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
                resultsDiv.classList.add('hidden');
            }
        });
    }
});

// Modal de paiement
function openModalPaiement(mensualiteId, mois, montantDu, montantPaye) {
    currentMensualiteData = {
        id: mensualiteId,
        mois: mois,
        montantDu: montantDu,
        montantPaye: montantPaye,
        reste: montantDu - montantPaye
    };

    // Remplir les informations
    document.getElementById('paiement_mensualite_id').value = mensualiteId;
    document.getElementById('paiement_mois').textContent = mois;
    document.getElementById('paiement_montant_du').textContent = new Intl.NumberFormat('fr-FR').format(montantDu);
    document.getElementById('paiement_montant_paye').textContent = new Intl.NumberFormat('fr-FR').format(montantPaye);
    document.getElementById('paiement_reste').textContent = new Intl.NumberFormat('fr-FR').format(currentMensualiteData.reste);
    document.getElementById('montant_complet').textContent = new Intl.NumberFormat('fr-FR').format(currentMensualiteData.reste);

    // Réinitialiser le formulaire
    document.getElementById('formPaiement').reset();
    document.getElementById('paiement_mensualite_id').value = mensualiteId;

    // Afficher le modal
    document.getElementById('modalPaiement').classList.remove('hidden');
}

function closeModalPaiement() {
    document.getElementById('modalPaiement').classList.add('hidden');
}

// Gestion de la case "paiement complet" et validation montant
document.addEventListener('DOMContentLoaded', function() {
    const checkboxComplet = document.getElementById('paiement_complet');
    const inputMontant = document.getElementById('montant_recu');

    if (checkboxComplet && inputMontant) {
        checkboxComplet.addEventListener('change', function() {
            if (this.checked) {
                inputMontant.value = currentMensualiteData.reste;
                inputMontant.readOnly = true;
            } else {
                inputMontant.value = '';
                inputMontant.readOnly = false;
            }
        });
        
        // Validation en temps réel du montant saisi
        inputMontant.addEventListener('input', function() {
            const montantSaisi = parseFloat(this.value) || 0;
            const montantMaxAutorise = currentMensualiteData.reste;
            
            if (montantSaisi > montantMaxAutorise) {
                this.setCustomValidity(`Le montant ne peut pas dépasser ${new Intl.NumberFormat('fr-FR').format(montantMaxAutorise)} FCFA`);
                this.style.borderColor = '#ef4444';
                this.style.backgroundColor = '#fef2f2';
                
                // Afficher un message d'erreur
                let errorMsg = this.parentNode.querySelector('.error-message');
                if (!errorMsg) {
                    errorMsg = document.createElement('div');
                    errorMsg.className = 'error-message text-xs text-red-600 mt-1';
                    this.parentNode.appendChild(errorMsg);
                }
                errorMsg.textContent = `❌ Maximum autorisé: ${new Intl.NumberFormat('fr-FR').format(montantMaxAutorise)} FCFA`;
            } else {
                this.setCustomValidity('');
                this.style.borderColor = '';
                this.style.backgroundColor = '';
                
                // Supprimer le message d'erreur
                const errorMsg = this.parentNode.querySelector('.error-message');
                if (errorMsg) {
                    errorMsg.remove();
                }
            }
        });
    }
});

// Fonction d'export des paiements
function exportPaiements() {
    // Récupérer les valeurs des filtres
    const filterClasse = document.getElementById('filter_classe')?.value || '';
    const filterMois = document.getElementById('filter_mois')?.value || '';
    const filterStatut = document.getElementById('filter_statut')?.value || '';
    const filterPeriode = document.getElementById('filter_periode')?.value || '';
    
    // Construire l'URL avec les paramètres
    const params = new URLSearchParams();
    if (filterClasse) params.append('filter_classe', filterClasse);
    if (filterMois) params.append('filter_mois', filterMois);
    if (filterStatut) params.append('filter_statut', filterStatut);
    if (filterPeriode) params.append('filter_periode', filterPeriode);
    
    // Rediriger vers l'export
    const exportUrl = `{{ route('mensualites.export-paiements') }}?${params.toString()}`;
    window.open(exportUrl, '_blank');
}

// === NOUVELLES FONCTIONS POUR L'INTERFACE AMÉLIORÉE ===

// === GESTION DES REÇUS ===
function rechercherRecus() {
    const form = document.getElementById('filtresRecus');
    const formData = new FormData(form);
    
    // Récupérer aussi les dates personnalisées si nécessaire
    const dateDebutInput = document.querySelector('input[name="date_debut_recu"]');
    const dateFinInput = document.querySelector('input[name="date_fin_recu"]');
    
    if (dateDebutInput && dateDebutInput.value) {
        formData.append('date_debut_recu', dateDebutInput.value);
    }
    if (dateFinInput && dateFinInput.value) {
        formData.append('date_fin_recu', dateFinInput.value);
    }
    
    // Pour l'instant, juste recharger la page avec les filtres
    const params = new URLSearchParams();
    for (let [key, value] of formData.entries()) {
        if (value) params.append(key, value);
    }
    
    // Simuler des résultats pour le moment
    alert('Recherche de reçus avec les critères sélectionnés...\nFonctionnalité en cours de développement backend.');
}

function reinitialiserRecherche() {
    document.getElementById('filtresRecus').reset();
    const datesDiv = document.getElementById('datesPersonnalisees');
    if (datesDiv) datesDiv.classList.add('hidden');
    alert('Filtres de recherche réinitialisés');
}

function consulterRecu(id) {
    const consultUrl = `{{ url('mensualites/recu') }}/${id}`;
    window.open(consultUrl, '_blank', 'width=800,height=600');
}

function telechargerRecu(id) {
    const downloadUrl = `{{ url('mensualites/recu') }}/${id}?download=1`;
    window.open(downloadUrl, '_blank');
}

// === GESTION DES RAPPORTS AMÉLIORÉS ===
function previsualiserRapport(type) {
    const previewUrl = `{{ url('mensualites/rapport') }}/${type}?preview=1`;
    window.open(previewUrl, '_blank', 'width=1000,height=700');
}

function previsualiserRapportPersonnalise() {
    const form = document.getElementById('filtresRapportPersonnalise');
    if (!form) {
        alert('Formulaire de rapport personnalisé non trouvé');
        return;
    }
    
    const formData = new FormData(form);
    const params = new URLSearchParams();
    
    for (let [key, value] of formData.entries()) {
        if (value) params.append(key, value);
    }
    
    // Ajouter les types de données sélectionnés
    const typesDonnees = [];
    document.querySelectorAll('input[name="type_donnees[]"]:checked').forEach(checkbox => {
        typesDonnees.push(checkbox.value);
    });
    typesDonnees.forEach(type => params.append('type_donnees[]', type));
    
    params.append('preview', '1');
    
    const previewUrl = `{{ url('mensualites/rapport-personnalise') }}?${params.toString()}`;
    window.open(previewUrl, '_blank', 'width=1000,height=700');
}

function envoyerRapportEmail() {
    const email = prompt('Adresse email de destination :');
    if (email && email.includes('@')) {
        alert(`Rapport sera envoyé à : ${email}\nFonctionnalité en cours de développement.`);
    } else if (email) {
        alert('Adresse email invalide');
    }
}

function reinitialiserFiltresRapport() {
    const form = document.getElementById('filtresRapportPersonnalise');
    if (form) {
        form.reset();
    }
    
    // Masquer les sections optionnelles
    const datesDiv = document.getElementById('datesPersonnaliseesRapport');
    const filtresAvancesDiv = document.getElementById('filtresAvances');
    
    if (datesDiv) datesDiv.classList.add('hidden');
    if (filtresAvancesDiv) filtresAvancesDiv.classList.add('hidden');
    
    // Réinitialiser les cases à cocher
    document.querySelectorAll('input[name="type_donnees[]"]').forEach(checkbox => {
        checkbox.checked = checkbox.value === 'paiements';
    });
}

// === FONCTIONS UTILITAIRES ===
function toggleDatePersonnalisee(periode) {
    const datesDiv = document.getElementById('datesPersonnalisees');
    if (datesDiv) {
        if (periode === 'personnalisee') {
            datesDiv.classList.remove('hidden');
        } else {
            datesDiv.classList.add('hidden');
        }
    }
}

function toggleDatePersonnaliseeRapport(periode) {
    const datesDiv = document.getElementById('datesPersonnaliseesRapport');
    if (datesDiv) {
        if (periode === 'personnalisee') {
            datesDiv.classList.remove('hidden');
        } else {
            datesDiv.classList.add('hidden');
        }
    }
}

function toggleFiltresAvances() {
    const filtresDiv = document.getElementById('filtresAvances');
    if (filtresDiv) {
        filtresDiv.classList.toggle('hidden');
    }
}

function formatNumber(number) {
    return new Intl.NumberFormat('fr-FR').format(number);
}

// === GESTIONNAIRES D'ÉVÉNEMENTS ===
document.addEventListener('DOMContentLoaded', function() {
    // Gérer l'affichage des dates personnalisées pour les reçus
    const periodeSelect = document.querySelector('select[name="periode_recu"]');
    if (periodeSelect) {
        periodeSelect.addEventListener('change', function() {
            toggleDatePersonnalisee(this.value);
        });
    }
    
    // Gérer l'affichage des dates personnalisées pour les rapports
    const periodeRapportSelect = document.querySelector('select[name="periode"]');
    if (periodeRapportSelect) {
        periodeRapportSelect.addEventListener('change', function() {
            toggleDatePersonnaliseeRapport(this.value);
        });
    }
});

// Fonctions pour l'onglet Reçus & Rapports
function imprimerRecu(mensualiteId) {
    // Ouvrir le reçu dans une nouvelle fenêtre pour impression
    const receiptUrl = `{{ route('mensualites.index') }}/recu/${mensualiteId}`;
    window.open(receiptUrl, '_blank', 'width=800,height=600');
}

function voirTousLesRecus() {
    // Afficher tous les reçus dans une modal ou rediriger vers une page dédiée
    alert('Fonctionnalité "Voir tous les reçus" en développement');
}

function genererRapport(type) {
    // Générer un rapport prédéfini selon le type
    const rapportUrl = `{{ url('mensualites/rapport') }}/${type}`;
    window.open(rapportUrl, '_blank');
}

function genererRapportPersonnalise(format = 'pdf') {
    // Récupérer les valeurs du formulaire de filtres
    const form = document.getElementById('filtresRapportPersonnalise');
    const formData = new FormData(form);
    
    // Construire l'URL avec les paramètres
    const params = new URLSearchParams();
    for (let [key, value] of formData.entries()) {
        if (value) params.append(key, value);
    }
    
    // Choisir l'URL selon le format
    if (format === 'excel') {
        const exportUrl = `{{ route('mensualites.export-excel') }}?${params.toString()}`;
        window.open(exportUrl, '_blank');
    } else {
        const rapportUrl = `{{ route('mensualites.rapport-personnalise') }}?${params.toString()}`;
        window.open(rapportUrl, '_blank');
    }
}

function exporterRapportExcel() {
    genererRapportPersonnalise('excel');
}

// Fonctions pour la modification des paiements
function modifierPaiement(id) {
    // Récupérer les données du paiement
    fetch(`{{ url('mensualites/edit') }}/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const mensualite = data.mensualite;
                
                // Remplir le formulaire de modification
                document.getElementById('editPaiementId').value = mensualite.id;
                document.getElementById('editEleveNom').textContent = mensualite.eleve_nom;
                document.getElementById('editEleveInfo').textContent = `INE: ${mensualite.eleve_ine} • ${mensualite.classe} • ${mensualite.mois_libelle}`;
                document.getElementById('editMontantDu').textContent = new Intl.NumberFormat('fr-FR').format(mensualite.montant_du) + ' FCFA';
                document.getElementById('editMontantPaye').value = mensualite.montant_paye;
                document.getElementById('editModePaiement').value = mensualite.mode_paiement;
                document.getElementById('editDatePaiement').value = mensualite.date_paiement;
                document.getElementById('editRemarques').value = mensualite.remarques || '';
                
                // Calculer et afficher le statut initial
                setTimeout(() => calculerStatutModification(), 100);
                
                // Afficher la modal
                document.getElementById('modalModifierPaiement').classList.remove('hidden');
            } else {
                showNotification('Erreur lors du chargement des données du paiement', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur lors du chargement des données', 'error');
        });
}

function fermerModalModifier() {
    document.getElementById('modalModifierPaiement').classList.add('hidden');
    document.getElementById('formModifierPaiement').reset();
    document.getElementById('editStatutPreview').innerHTML = '';
}

function calculerStatutModification() {
    const montantDuText = document.getElementById('editMontantDu').textContent;
    const montantDu = parseInt(montantDuText.replace(/[^0-9]/g, ''));
    const montantPayeInput = document.getElementById('editMontantPaye');
    const montantPaye = parseInt(montantPayeInput.value) || 0;
    
    let statut = '';
    let classe = '';
    let message = '';
    
    // Validation du montant - LOGIQUE CORRIGÉE
    if (montantPaye < 0) {
        statut = '❌ Erreur';
        classe = 'text-red-600 bg-red-100 px-2 py-1 rounded border border-red-300';
        message = 'Le montant ne peut pas être négatif';
        montantPayeInput.setCustomValidity('Le montant ne peut pas être négatif');
    } else if (montantPaye > montantDu) {
        statut = '🚫 INTERDIT : Dépassement';
        classe = 'text-red-600 bg-red-100 px-2 py-1 rounded border-2 border-red-500 animate-pulse';
        message = `❌ MONTANT TROP ÉLEVÉ ! Maximum autorisé: ${new Intl.NumberFormat('fr-FR').format(montantDu)} FCFA`;
        montantPayeInput.setCustomValidity('Le montant ne peut pas dépasser le montant dû');
        // Optionnel: corriger automatiquement
        // montantPayeInput.value = montantDu;
    } else if (montantPaye === 0) {
        statut = '🔴 Impayé';
        classe = 'text-red-600 bg-red-100 px-2 py-1 rounded';
        message = 'Aucun paiement';
        montantPayeInput.setCustomValidity('');
    } else if (montantPaye < montantDu) {
        statut = '🟡 Partiellement payé';
        classe = 'text-yellow-600 bg-yellow-100 px-2 py-1 rounded';
        message = `Reste à payer: ${new Intl.NumberFormat('fr-FR').format(montantDu - montantPaye)} FCFA`;
        montantPayeInput.setCustomValidity('');
    } else {
        statut = '✅ Payé intégralement';
        classe = 'text-green-600 bg-green-100 px-2 py-1 rounded';
        message = 'Paiement complet';
        montantPayeInput.setCustomValidity('');
    }
    
    document.getElementById('editStatutPreview').innerHTML = `<span class="${classe}">${statut}</span><br><small class="text-gray-600">${message}</small>`;
}

function supprimerPaiement(id, eleveNom) {
    if (confirm(`Êtes-vous sûr de vouloir supprimer le paiement de ${eleveNom} ?\n\nCette action est irréversible.`)) {
        fetch(`{{ url('mensualites/delete') }}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                location.reload(); // Recharger la page pour actualiser la liste
            } else {
                showNotification(data.message || 'Erreur lors de la suppression', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur lors de la suppression', 'error');
        });
    }
}

// Gérer la soumission du formulaire de modification
document.getElementById('formModifierPaiement').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const id = document.getElementById('editPaiementId').value;
    const montantPaye = parseFloat(document.getElementById('editMontantPaye').value);
    const montantDuText = document.getElementById('editMontantDu').textContent;
    const montantDu = parseInt(montantDuText.replace(/[^0-9]/g, ''));
    
    // Validation côté client - RENFORCÉE
    if (montantPaye < 0) {
        showNotification('Le montant payé ne peut pas être négatif', 'error');
        return;
    }
    
    // EMPÊCHER le dépassement du montant dû
    if (montantPaye > montantDu) {
        showNotification(`❌ ERREUR : Le montant payé (${new Intl.NumberFormat('fr-FR').format(montantPaye)} FCFA) ne peut pas dépasser le montant dû (${new Intl.NumberFormat('fr-FR').format(montantDu)} FCFA).`, 'error');
        // Remettre le montant au maximum autorisé
        document.getElementById('editMontantPaye').value = montantDu;
        document.getElementById('editMontantPaye').focus();
        return;
    }
    
    const formData = new FormData(this);
    
    // Convertir FormData en objet JSON
    const data = {};
    formData.forEach((value, key) => {
        data[key] = value;
    });
    
    fetch(`{{ url('mensualites/update') }}/${id}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            fermerModalModifier();
            setTimeout(() => {
                location.reload(); // Recharger la page pour actualiser la liste
            }, 1500); // Délai pour permettre de voir la notification
        } else {
            showNotification(data.message || 'Erreur lors de la modification', 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showNotification(`Erreur lors de la modification: ${error.message}`, 'error');
    });
});

function reinitialiserFiltres() {
    // Réinitialiser le formulaire de filtres
    document.getElementById('filtresRapport').reset();
}

// Fonction pour afficher les notifications
function showNotification(message, type = 'info') {
    // Créer l'élément notification
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;
    
    // Définir les couleurs selon le type
    const colors = {
        'success': 'bg-green-100 border border-green-400 text-green-700',
        'error': 'bg-red-100 border border-red-400 text-red-700',
        'warning': 'bg-yellow-100 border border-yellow-400 text-yellow-700',
        'info': 'bg-blue-100 border border-blue-400 text-blue-700'
    };
    
    notification.className += ` ${colors[type] || colors.info}`;
    
    notification.innerHTML = `
        <div class="flex items-start">
            <div class="flex-1">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <button class="ml-3 flex-shrink-0" onclick="this.parentElement.parentElement.remove()">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
    `;
    
    // Ajouter au DOM
    document.body.appendChild(notification);
    
    // Animer l'entrée
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto-suppression après 5 secondes
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 5000);
}

// Initialisation du graphique des paiements par mois
document.addEventListener('DOMContentLoaded', function() {
    const chartCanvas = document.getElementById('chartPaiementsMois');
    if (chartCanvas) {
        const ctx = chartCanvas.getContext('2d');
        
        // Données des paiements par mois (à partir de PHP)
        const paiementsData = @json($rapportsData['paiements_par_mois'] ?? []);
        
        const labels = paiementsData.map(item => {
            const mois = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 
                         'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
            return mois[item.mois - 1] + ' ' + item.annee;
        }).reverse();
        
        const totaux = paiementsData.map(item => item.total).reverse();
        const montants = paiementsData.map(item => item.montant).reverse();
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nombre de paiements',
                    data: totaux,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    yAxisID: 'y'
                }, {
                    label: 'Montant (FCFA)',
                    data: montants,
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Période'
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Nombre de paiements'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Montant (FCFA)'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Évolution des Paiements'
                    },
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });
    }
});
</script>
@endsection