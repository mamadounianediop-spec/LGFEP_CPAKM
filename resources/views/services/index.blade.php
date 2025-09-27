@extends('layouts.app')

@section('title', 'Gestion des Services')

@section('content')
<div x-data="{
        activeTab: 'services',
        modals: {
            service: false,
            depense: false,
            editService: false,
            editDepense: false
        }
    }" class="min-h-screen bg-gray-50 p-6">
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
        
        <!-- En-tête -->
        <div class="mb-8">
            <div class="flex items-center space-x-3 mb-2">
                <div class="bg-orange-100 p-2 rounded-lg">
                    <i class="fas fa-cogs text-orange-600 text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Gestion des Services</h1>
                    <p class="text-sm text-gray-600">Gérez les services et matériels de l'établissement - Année {{ $anneeActive->libelle ?? '2025-2026' }}</p>
                </div>
            </div>
        </div>

        <!-- Navigation par onglets -->
        <div class="bg-white rounded-lg shadow-sm border mb-6">
            <nav class="flex space-x-1 p-1">
                <button @click="activeTab = 'services'" 
                        :class="activeTab === 'services' ? 'bg-orange-600 text-white shadow-md' : 'text-gray-600 hover:text-orange-600 hover:bg-gray-50'"
                        class="flex items-center px-4 py-3 rounded-md font-medium text-sm transition-all duration-200 flex-1 justify-center">
                    <i class="fas fa-cogs mr-2"></i>Services & Matériels
                </button>
                <button @click="activeTab = 'depenses'" 
                        :class="activeTab === 'depenses' ? 'bg-orange-600 text-white shadow-md' : 'text-gray-600 hover:text-orange-600 hover:bg-gray-50'"
                        class="flex items-center px-4 py-3 rounded-md font-medium text-sm transition-all duration-200 flex-1 justify-center">
                    <i class="fas fa-receipt mr-2"></i>Dépenses
                </button>
                <button @click="activeTab = 'rapports'" 
                        :class="activeTab === 'rapports' ? 'bg-orange-600 text-white shadow-md' : 'text-gray-600 hover:text-orange-600 hover:bg-gray-50'"
                        class="flex items-center px-4 py-3 rounded-md font-medium text-sm transition-all duration-200 flex-1 justify-center">
                    <i class="fas fa-chart-bar mr-2"></i>Rapports
                </button>
            </nav>
        </div>

        <!-- ONGLET SERVICES -->
        <div x-show="activeTab === 'services'" x-transition class="space-y-6">
            <!-- Titre de l'onglet -->
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Services & Matériels</h3>
                    <p class="mt-1 text-sm text-gray-600">Gérez l'inventaire des services et équipements de l'établissement</p>
                </div>
                <button @click="modals.service = true" class="bg-orange-600 text-white px-4 py-2 rounded-md text-sm hover:bg-orange-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Ajouter un service
                </button>
                <!-- Boutons cachés pour ouvrir les modales d'édition -->
                <button @click="modals.editService = true" id="openEditServiceModal" style="display: none;"></button>
            </div>
            
            <!-- Boutons cachés pour ouvrir les modales d'édition -->
            <button @click="modals.editService = true" id="openEditServiceModal" style="display: none;"></button>

            <!-- Liste des services -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Liste des services</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fournisseur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($services ?? [] as $service)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $service->nom }}</div>
                                    <div class="text-sm text-gray-500">{{ $service->description }}</div>
                                    @if($service->date_acquisition)
                                        <div class="text-xs text-gray-400">Acquis le {{ \Carbon\Carbon::parse($service->date_acquisition)->format('d/m/Y') }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                        <i class="{{ $service->categorieService->icone ?? 'fas fa-cog' }} mr-1"></i>
                                        {{ $service->categorieService->nom ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $service->fournisseur ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($service->statut === 'actif')
                                        <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>Actif
                                        </span>
                                    @elseif($service->statut === 'inactif')
                                        <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i>Inactif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-wrench mr-1"></i>En maintenance
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button class="text-orange-600 hover:text-orange-900 btn-edit-service" 
                                                data-service='@json($service)'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="text-red-600 hover:text-red-900 btn-delete-service" 
                                                data-url="{{ route('services.destroy', $service) }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <i class="fas fa-cogs text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-gray-500">Aucun service enregistré</p>
                                    <p class="text-sm text-gray-400">Commencez par ajouter un service ci-dessus</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ONGLET DEPENSES -->
        <div x-show="activeTab === 'depenses'" x-transition class="space-y-6">
            <!-- Titre de l'onglet -->
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Dépenses des Services</h3>
                    <p class="mt-1 text-sm text-gray-600">Enregistrez et suivez toutes les dépenses liées aux services et matériels</p>
                </div>
                <button @click="modals.depense = true" class="bg-orange-600 text-white px-4 py-2 rounded-md text-sm hover:bg-orange-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Enregistrer une dépense
                </button>
                <!-- Bouton caché pour ouvrir la modale d'édition -->
                <button @click="modals.editDepense = true" id="openEditDepenseModal" style="display: none;"></button>
            </div>

            <!-- Filtres avancés pour les dépenses -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex flex-wrap items-end gap-4">
                    <!-- Année scolaire -->
                    <div class="flex-1 min-w-40">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Année scolaire</label>
                        <select id="filter_depenses_annee" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                            <option value="">Toutes les années</option>
                            @foreach($anneeScolaires as $annee)
                                <option value="{{ $annee->id }}" {{ $annee->id == $anneeActive->id ? 'selected' : '' }}>
                                    {{ $annee->libelle }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Mois -->
                    <div class="flex-1 min-w-32">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mois</label>
                        <select id="filter_depenses_mois" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                            <option value="">Tous les mois</option>
                            <option value="01">Janvier</option>
                            <option value="02">Février</option>
                            <option value="03">Mars</option>
                            <option value="04">Avril</option>
                            <option value="05">Mai</option>
                            <option value="06">Juin</option>
                            <option value="07">Juillet</option>
                            <option value="08">Août</option>
                            <option value="09">Septembre</option>
                            <option value="10">Octobre</option>
                            <option value="11">Novembre</option>
                            <option value="12">Décembre</option>
                        </select>
                    </div>

                    <!-- Catégorie -->
                    <div class="flex-1 min-w-40">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                        <select id="filter_depenses_categorie" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                            <option value="">Toutes les catégories</option>
                            @foreach($categoriesServices as $categorie)
                                <option value="{{ $categorie->id }}">{{ $categorie->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Service -->
                    <div class="flex-1 min-w-40">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Service</label>
                        <select id="filter_depenses_service" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                            <option value="">Tous les services</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}">{{ $service->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Recherche -->
                    <div class="flex-1 min-w-48">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                        <input type="text" id="filter_depenses_search" placeholder="Rechercher dans les dépenses..." 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                    </div>

                    <!-- Boutons d'action -->
                    <div class="flex gap-2">
                        <button onclick="filtrerDepenses()" class="bg-orange-600 text-white px-4 py-2 rounded-md text-sm hover:bg-orange-700 transition-colors">
                            <i class="fas fa-filter mr-2"></i>Filtrer
                        </button>
                        <button onclick="exporterDepensesFiltrées()" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700 transition-colors">
                            <i class="fas fa-file-export mr-2"></i>Exporter
                        </button>
                        <button onclick="apercuDepensesFiltrées()" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700 transition-colors">
                            <i class="fas fa-eye mr-2"></i>Aperçu
                        </button>
                        <button onclick="reinitialiserFiltresDepenses()" class="bg-gray-500 text-white px-4 py-2 rounded-md text-sm hover:bg-gray-600 transition-colors">
                            <i class="fas fa-undo mr-2"></i>Réinitialiser
                        </button>
                    </div>
                </div>
            </div>

            <!-- Liste des dépenses -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Historique des dépenses</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table id="tableauDepenses" class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Facture</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($depenses ?? [] as $depense)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $depense->service->nom ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $depense->description }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @switch($depense->type_depense)
                                        @case('achat')
                                            <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                                <i class="fas fa-shopping-cart mr-1"></i>Achat
                                            </span>
                                            @break
                                        @case('maintenance')
                                            <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-wrench mr-1"></i>Maintenance
                                            </span>
                                            @break
                                        @case('reparation')
                                            <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                                <i class="fas fa-tools mr-1"></i>Réparation
                                            </span>
                                            @break
                                        @default
                                            <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                                {{ ucfirst($depense->type_depense) }}
                                            </span>
                                    @endswitch
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ number_format($depense->montant, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($depense->date_depense)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $depense->numero_facture ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button class="text-blue-600 hover:text-blue-900 btn-print-depense" 
                                                data-depense='@json($depense)'
                                                title="Imprimer la fiche de dépense">
                                            <i class="fas fa-print"></i>
                                        </button>
                                        <button class="text-orange-600 hover:text-orange-900 btn-edit-depense" 
                                                data-depense='@json($depense)'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="text-red-600 hover:text-red-900 btn-delete-depense" 
                                                data-url="{{ route('services.depenses.destroy', $depense) }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <i class="fas fa-receipt text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-gray-500">Aucune dépense enregistrée</p>
                                    <p class="text-sm text-gray-400">Ajoutez une dépense ci-dessus</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ONGLET RAPPORTS -->
        <div x-show="activeTab === 'rapports'" x-transition class="space-y-6">
            <!-- Titre de l'onglet -->
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Rapports & Analyses</h3>
                    <p class="mt-1 text-sm text-gray-600">Générez des rapports détaillés et analysez les dépenses des services</p>
                </div>
                <div class="flex space-x-2">
                    <button onclick="genererRapport()" class="bg-orange-600 text-white px-4 py-2 rounded-md text-sm hover:bg-orange-700 transition-colors">
                        <i class="fas fa-chart-bar mr-2"></i>Générer rapport
                    </button>
                    <button onclick="exporterRapport()" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700 transition-colors">
                        <i class="fas fa-download mr-2"></i>Exporter PDF
                    </button>
                </div>
            </div>
            
            <!-- Filtres avancés pour les rapports -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex flex-wrap items-end gap-4">
                    <!-- Année scolaire -->
                    <div class="flex-1 min-w-40">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Année scolaire</label>
                        <select id="filter_rapports_annee" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                            <option value="">Toutes les années</option>
                            @foreach($anneeScolaires as $annee)
                                <option value="{{ $annee->id }}" {{ $annee->id == $anneeActive->id ? 'selected' : '' }}>
                                    {{ $annee->libelle }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Mois -->
                    <div class="flex-1 min-w-32">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mois</label>
                        <select id="filter_rapports_mois" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                            <option value="">Tous les mois</option>
                            <option value="01">Janvier</option>
                            <option value="02">Février</option>
                            <option value="03">Mars</option>
                            <option value="04">Avril</option>
                            <option value="05">Mai</option>
                            <option value="06">Juin</option>
                            <option value="07">Juillet</option>
                            <option value="08">Août</option>
                            <option value="09">Septembre</option>
                            <option value="10">Octobre</option>
                            <option value="11">Novembre</option>
                            <option value="12">Décembre</option>
                        </select>
                    </div>

                    <!-- Catégorie -->
                    <div class="flex-1 min-w-40">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                        <select id="filter_rapports_categorie" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                            <option value="">Toutes les catégories</option>
                            @foreach($categoriesServices as $categorie)
                                <option value="{{ $categorie->id }}">{{ $categorie->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Service -->
                    <div class="flex-1 min-w-40">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Service</label>
                        <select id="filter_rapports_service" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                            <option value="">Tous les services</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}">{{ $service->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Recherche -->
                    <div class="flex-1 min-w-48">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                        <input type="text" id="filter_rapports_search" placeholder="Rechercher..." 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                    </div>

                    <!-- Boutons d'action -->
                    <div class="flex gap-2">
                        <button onclick="reinitialiserFiltresRapports()" class="bg-gray-500 text-white px-4 py-2 rounded-md text-sm hover:bg-gray-600 transition-colors">
                            <i class="fas fa-undo mr-2"></i>Réinitialiser
                        </button>
                    </div>
                </div>
            </div>

            <!-- Statistiques générales -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-orange-100">
                            <i class="fas fa-cogs text-2xl text-orange-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Services</p>
                            <p class="text-2xl font-bold text-gray-900">{{ count($services ?? []) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100">
                            <i class="fas fa-check-circle text-2xl text-green-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Services Actifs</p>
                            <p class="text-2xl font-bold text-gray-900">{{ collect($services ?? [])->where('statut', 'actif')->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100">
                            <i class="fas fa-receipt text-2xl text-blue-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Dépenses</p>
                            <p class="text-2xl font-bold text-gray-900 stat-total-depenses">{{ count($depenses ?? []) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100">
                            <i class="fas fa-money-bill-wave text-2xl text-purple-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Montant Total</p>
                            <p class="text-2xl font-bold text-gray-900 stat-montant-total">
                                {{ number_format(collect($depenses ?? [])->sum('montant'), 0, ',', ' ') }} F
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Répartition par catégories -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Services par Catégorie</h4>
                    <div class="space-y-3">
                        @foreach($categoriesServices as $categorie)
                            @php
                                $servicesCount = collect($services ?? [])->where('categorie_service_id', $categorie->id)->count();
                            @endphp
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <i class="{{ $categorie->icone ?? 'fas fa-cog' }} text-orange-600 mr-3"></i>
                                    <span class="font-medium text-gray-900">{{ $categorie->nom }}</span>
                                </div>
                                <span class="text-gray-600 font-semibold">{{ $servicesCount }} services</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Types de Dépenses</h4>
                    <div class="space-y-3">
                        @php
                            $typesDepenses = [
                                'achat' => ['icon' => 'fas fa-shopping-cart', 'color' => 'blue', 'label' => 'Achats'],
                                'maintenance' => ['icon' => 'fas fa-wrench', 'color' => 'yellow', 'label' => 'Maintenance'],
                                'reparation' => ['icon' => 'fas fa-tools', 'color' => 'red', 'label' => 'Réparations'],
                                'location' => ['icon' => 'fas fa-home', 'color' => 'purple', 'label' => 'Location']
                            ];
                        @endphp
                        @foreach($typesDepenses as $type => $config)
                            @php
                                $count = collect($depenses ?? [])->where('type_depense', $type)->count();
                            @endphp
                            <div class="flex items-center justify-between p-3 bg-{{ $config['color'] }}-50 rounded-lg">
                                <div class="flex items-center">
                                    <i class="{{ $config['icon'] }} text-{{ $config['color'] }}-600 mr-3"></i>
                                    <span class="font-medium text-gray-900">{{ $config['label'] }}</span>
                                </div>
                                <span class="text-gray-600 font-semibold">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Actions disponibles -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Actions disponibles</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <button onclick="window.print()" class="flex items-center justify-center px-4 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                        <i class="fas fa-print mr-2"></i>
                        Imprimer le Rapport
                    </button>
                    <button onclick="alert('Fonctionnalité d\'export à venir')" class="flex items-center justify-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-file-excel mr-2"></i>
                        Export Services
                    </button>
                    <button onclick="alert('Fonctionnalité d\'export à venir')" class="flex items-center justify-center px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-file-csv mr-2"></i>
                        Export Dépenses
                    </button>
                </div>
        </div>
    </div>

    <!-- MODALE AJOUTER SERVICE -->
    <div x-show="modals.service" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto modal-service" style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="modals.service" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
            
            <div x-show="modals.service" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <form action="{{ route('services.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Ajouter un service</h3>
                            <button type="button" @click="modals.service = false" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom du service</label>
                                    <input type="text" name="nom" required 
                                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500"
                                           placeholder="Ex: Climatisation Bureau">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                                    <select name="categorie_service_id" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                                        <option value="">Sélectionner une catégorie</option>
                                        @foreach($categoriesServices ?? [] as $categorie)
                                            <option value="{{ $categorie->id }}">{{ $categorie->nom }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Fournisseur</label>
                                    <input type="text" name="fournisseur" 
                                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500"
                                           placeholder="Nom du fournisseur">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date d'acquisition</label>
                                    <input type="date" name="date_acquisition" 
                                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                                <select name="statut" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                                    <option value="actif">Actif</option>
                                    <option value="inactif">Inactif</option>
                                    <option value="en_maintenance">En maintenance</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea name="description" rows="3" 
                                          class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500"
                                          placeholder="Description du service..."></textarea>
                            </div>
                            
                            <input type="hidden" name="annee_scolaire_id" value="{{ $anneeActive->id ?? '' }}">
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm">
                            <i class="fas fa-plus mr-2"></i>Ajouter le service
                        </button>
                        <button type="button" @click="modals.service = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODALE AJOUTER DEPENSE -->
    <div x-show="modals.depense" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto modal-depense" style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="modals.depense" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
            
            <div x-show="modals.depense" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <form action="{{ route('services.depenses.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Enregistrer une dépense</h3>
                            <button type="button" @click="modals.depense = false" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Service concerné</label>
                                    <select name="service_id" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                                        <option value="">Sélectionner un service</option>
                                        @foreach($services ?? [] as $service)
                                            <option value="{{ $service->id }}">{{ $service->nom }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Type de dépense</label>
                                    <select name="type_depense" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                                        <option value="">Sélectionner le type</option>
                                        <option value="achat">Achat</option>
                                        <option value="maintenance">Maintenance</option>
                                        <option value="location">Location</option>
                                        <option value="reparation">Réparation</option>
                                        <option value="consommation">Consommation</option>
                                        <option value="autre">Autre</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Montant (FCFA)</label>
                                    <input type="number" name="montant" required min="0" 
                                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500"
                                           placeholder="0">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de la dépense</label>
                                    <input type="date" name="date_depense" required value="{{ date('Y-m-d') }}"
                                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Numéro de facture</label>
                                <input type="text" name="numero_facture" 
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500"
                                       placeholder="Ex: FAC-2025-001">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description de la dépense</label>
                                <textarea name="description" rows="3" 
                                          class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500"
                                          placeholder="Détails de la dépense..."></textarea>
                            </div>
                            
                            <input type="hidden" name="annee_scolaire_id" value="{{ $anneeActive->id ?? '' }}">
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm">
                            <i class="fas fa-save mr-2"></i>Enregistrer la dépense
                        </button>
                        <button type="button" @click="modals.depense = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODALE ÉDITER SERVICE -->
    <div x-show="modals.editService" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto modal-edit-service" style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="modals.editService" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
            
            <div x-show="modals.editService" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <form id="editServiceForm">
                    @csrf
                    @method('PUT')
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Modifier le service</h3>
                            <button type="button" @click="modals.editService = false" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom du service</label>
                                    <input type="text" id="edit_service_nom" name="nom" required 
                                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                                    <select id="edit_service_categorie" name="categorie_service_id" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                                        <option value="">Sélectionner une catégorie</option>
                                        @foreach($categoriesServices ?? [] as $categorie)
                                            <option value="{{ $categorie->id }}">{{ $categorie->nom }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Fournisseur</label>
                                    <input type="text" id="edit_service_fournisseur" name="fournisseur" 
                                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date d'acquisition</label>
                                    <input type="date" id="edit_service_date" name="date_acquisition" 
                                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                                <select id="edit_service_statut" name="statut" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                                    <option value="actif">Actif</option>
                                    <option value="inactif">Inactif</option>
                                    <option value="en_maintenance">En maintenance</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea id="edit_service_description" name="description" rows="3" 
                                          class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500"></textarea>
                            </div>
                            
                            <input type="hidden" id="edit_service_annee_scolaire_id" name="annee_scolaire_id" value="{{ $anneeActive->id ?? '' }}">
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm">
                            <i class="fas fa-save mr-2"></i>Modifier le service
                        </button>
                        <button type="button" @click="modals.editService = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODALE ÉDITER DÉPENSE -->
    <div x-show="modals.editDepense" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto modal-edit-depense" style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="modals.editDepense" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
            
            <div x-show="modals.editDepense" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <form id="editDepenseForm">
                    @csrf
                    @method('PUT')
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Modifier la dépense</h3>
                            <button type="button" @click="modals.editDepense = false" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Service concerné</label>
                                    <select id="edit_depense_service" name="service_id" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                                        <option value="">Sélectionner un service</option>
                                        @foreach($services ?? [] as $service)
                                            <option value="{{ $service->id }}">{{ $service->nom }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Type de dépense</label>
                                    <select id="edit_depense_type" name="type_depense" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                                        <option value="">Sélectionner le type</option>
                                        <option value="achat">Achat</option>
                                        <option value="maintenance">Maintenance</option>
                                        <option value="location">Location</option>
                                        <option value="reparation">Réparation</option>
                                        <option value="consommation">Consommation</option>
                                        <option value="autre">Autre</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Montant (FCFA)</label>
                                    <input type="number" id="edit_depense_montant" name="montant" required min="0" 
                                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de la dépense</label>
                                    <input type="date" id="edit_depense_date" name="date_depense" required
                                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Numéro de facture</label>
                                <input type="text" id="edit_depense_facture" name="numero_facture" 
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description de la dépense</label>
                                <textarea id="edit_depense_description" name="description" rows="3" 
                                          class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500"></textarea>
                            </div>
                            
                            <input type="hidden" id="edit_depense_annee_scolaire_id" name="annee_scolaire_id" value="{{ $anneeActive->id ?? '' }}">
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm">
                            <i class="fas fa-save mr-2"></i>Modifier la dépense
                        </button>
                        <button type="button" @click="modals.editDepense = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fonction pour afficher les modales de notification
    function showNotificationModal(message, type = 'success', title = null) {
        // Supprimer toute modale existante
        const existingModal = document.getElementById('notificationModal');
        if (existingModal) {
            existingModal.remove();
        }

        const isSuccess = type === 'success';
        const bgColor = isSuccess ? 'bg-green-100' : 'bg-red-100';
        const textColor = isSuccess ? 'text-green-800' : 'text-red-800';
        const iconColor = isSuccess ? 'text-green-600' : 'text-red-600';
        const borderColor = isSuccess ? 'border-green-200' : 'border-red-200';
        const buttonColor = isSuccess ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700';
        const icon = isSuccess ? 
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>' :
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
        
        const defaultTitle = isSuccess ? 'Succès' : 'Erreur';
        const modalTitle = title || defaultTitle;

        const modal = document.createElement('div');
        modal.id = 'notificationModal';
        modal.className = 'fixed inset-0 z-50 overflow-y-auto';
        modal.innerHTML = `
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm sm:w-full sm:p-6">
                    <div>
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full ${bgColor} ${borderColor} border-2">
                            <svg class="h-6 w-6 ${iconColor}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                ${icon}
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">${modalTitle}</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">${message}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6">
                        <button onclick="closeNotificationModal()" class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 ${buttonColor} text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:text-sm">
                            OK
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        
        // Animation d'entrée
        setTimeout(() => {
            modal.classList.add('opacity-100');
        }, 50);
    }

    // Fonction pour fermer la modale de notification
    window.closeNotificationModal = function() {
        const modal = document.getElementById('notificationModal');
        if (modal) {
            modal.classList.add('opacity-0');
            setTimeout(() => {
                modal.remove();
            }, 150);
        }
    };

    // Fermer la modale en cliquant sur l'overlay
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('notificationModal');
        if (modal && e.target === modal.querySelector('.bg-gray-500')) {
            closeNotificationModal();
        }
    });

    // Fermer avec la touche Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeNotificationModal();
        }
    });

    // Gérer les formulaires de services
    const serviceForm = document.querySelector('form[action*="services/store"]');
    if (serviceForm) {
        serviceForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Désactiver le bouton et montrer un loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Ajout en cours...';
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Afficher le message de succès et recharger après fermeture
                    showNotificationModal(data.message, 'success', 'Service ajouté');
                    
                    // Observer la fermeture de la modale de notification pour recharger
                    const checkForReload = () => {
                        if (!document.getElementById('notificationModal')) {
                            window.location.reload();
                        } else {
                            setTimeout(checkForReload, 200);
                        }
                    };
                    setTimeout(checkForReload, 500);
                } else {
                    showNotificationModal(data.message || 'Erreur lors de l\'ajout du service', 'error', 'Erreur');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotificationModal('Erreur lors de l\'ajout du service', 'error', 'Erreur');
            })
            .finally(() => {
                // Réactiver le bouton
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }

    // Gérer les formulaires de dépenses
    const depenseForm = document.querySelector('form[action*="depenses/store"]');
    if (depenseForm) {
        depenseForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Désactiver le bouton et montrer un loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Enregistrement...';
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Afficher le message de succès et recharger après fermeture
                    showNotificationModal(data.message, 'success', 'Dépense enregistrée');
                    
                    // Observer la fermeture de la modale de notification pour recharger
                    const checkForReload = () => {
                        if (!document.getElementById('notificationModal')) {
                            window.location.reload();
                        } else {
                            setTimeout(checkForReload, 200);
                        }
                    };
                    setTimeout(checkForReload, 500);
                } else {
                    showNotificationModal(data.message || 'Erreur lors de l\'enregistrement de la dépense', 'error', 'Erreur');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotificationModal('Erreur lors de l\'enregistrement de la dépense', 'error', 'Erreur');
            })
            .finally(() => {
                // Réactiver le bouton
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }

    // Fonction pour afficher la modale de confirmation
    function showConfirmationModal(message, onConfirm, title = 'Confirmation') {
        // Supprimer toute modale existante
        const existingModal = document.getElementById('confirmationModal');
        if (existingModal) {
            existingModal.remove();
        }

        const modal = document.createElement('div');
        modal.id = 'confirmationModal';
        modal.className = 'fixed inset-0 z-50 overflow-y-auto';
        modal.innerHTML = `
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm sm:w-full sm:p-6">
                    <div>
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 border-2 border-red-200">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">${title}</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">${message}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button onclick="confirmAction()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:col-start-2 sm:text-sm">
                            Supprimer
                        </button>
                        <button onclick="closeConfirmationModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                            Annuler
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        
        // Stocker la fonction de confirmation
        window.confirmAction = onConfirm;
        
        // Animation d'entrée
        setTimeout(() => {
            modal.classList.add('opacity-100');
        }, 50);
    }

    // Fonction pour fermer la modale de confirmation
    window.closeConfirmationModal = function() {
        const modal = document.getElementById('confirmationModal');
        if (modal) {
            modal.classList.add('opacity-0');
            setTimeout(() => {
                modal.remove();
                delete window.confirmAction;
            }, 150);
        }
    };

    // Gérer les boutons de suppression
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-delete-service') || e.target.closest('.btn-delete-depense')) {
            e.preventDefault();
            
            const button = e.target.closest('.btn-delete-service') || e.target.closest('.btn-delete-depense');
            const url = button.getAttribute('data-url');
            const type = button.classList.contains('btn-delete-service') ? 'service' : 'dépense';
            
            showConfirmationModal(
                `Êtes-vous sûr de vouloir supprimer ce ${type} ? Cette action est irréversible.`,
                function() {
                    // Fermer la modale de confirmation
                    closeConfirmationModal();
                    
                    // Effectuer la suppression
                    fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotificationModal(data.message, 'success', `${type.charAt(0).toUpperCase() + type.slice(1)} supprimé${type === 'service' ? '' : 'e'}`);
                        
                        // Observer la fermeture de la modale de notification pour recharger
                        const checkForReload = () => {
                            if (!document.getElementById('notificationModal')) {
                                window.location.reload();
                            } else {
                                setTimeout(checkForReload, 200);
                            }
                        };
                        setTimeout(checkForReload, 500);
                    } else {
                        showNotificationModal(data.message || `Erreur lors de la suppression du ${type}`, 'error', 'Erreur');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotificationModal(`Erreur lors de la suppression du ${type}`, 'error', 'Erreur');
                });
                },
                'Confirmer la suppression'
            );
        }
    });

    // Gérer les boutons d'édition de services
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-edit-service')) {
            e.preventDefault();
            
            const button = e.target.closest('.btn-edit-service');
            const serviceData = JSON.parse(button.getAttribute('data-service'));
            
            // Pré-remplir le formulaire d'édition
            document.getElementById('edit_service_nom').value = serviceData.nom || '';
            document.getElementById('edit_service_categorie').value = serviceData.categorie_service_id || '';
            document.getElementById('edit_service_fournisseur').value = serviceData.fournisseur || '';
            document.getElementById('edit_service_date').value = serviceData.date_acquisition || '';
            document.getElementById('edit_service_statut').value = serviceData.statut || 'actif';
            document.getElementById('edit_service_description').value = serviceData.description || '';
            document.getElementById('edit_service_annee_scolaire_id').value = serviceData.annee_scolaire_id || '';
            
            // Définir l'URL du formulaire
            document.getElementById('editServiceForm').action = `/services/${serviceData.id}`;
            
            // Ouvrir la modale en cliquant sur le bouton caché
            document.getElementById('openEditServiceModal').click();
        }
    });

    // Gérer les boutons d'édition de dépenses
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-edit-depense')) {
            e.preventDefault();
            
            const button = e.target.closest('.btn-edit-depense');
            const depenseData = JSON.parse(button.getAttribute('data-depense'));
            
            // Pré-remplir le formulaire d'édition
            document.getElementById('edit_depense_service').value = depenseData.service_id || '';
            document.getElementById('edit_depense_type').value = depenseData.type_depense || '';
            document.getElementById('edit_depense_montant').value = depenseData.montant || '';
            document.getElementById('edit_depense_date').value = depenseData.date_depense || '';
            document.getElementById('edit_depense_facture').value = depenseData.numero_facture || '';
            document.getElementById('edit_depense_description').value = depenseData.description || '';
            document.getElementById('edit_depense_annee_scolaire_id').value = depenseData.annee_scolaire_id || '';
            
            // Définir l'URL du formulaire
            document.getElementById('editDepenseForm').action = `/services/depenses/${depenseData.id}`;
            
            // Ouvrir la modale en cliquant sur le bouton caché
            document.getElementById('openEditDepenseModal').click();
        }
    });

    // Gérer la soumission du formulaire d'édition de service
    document.getElementById('editServiceForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Désactiver le bouton et montrer un loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Modification...';
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => {
            if (response.status === 422) {
                // Erreur de validation
                return response.json().then(errorData => {
                    let errorMessage = 'Erreurs de validation:\n';
                    if (errorData.errors) {
                        for (let field in errorData.errors) {
                            errorMessage += `- ${errorData.errors[field].join(', ')}\n`;
                        }
                    }
                    showNotificationModal(errorMessage, 'error', 'Erreur de validation');
                    throw new Error('Validation failed');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotificationModal(data.message, 'success', 'Service modifié');
                
                // Observer la fermeture de la modale de notification pour recharger
                const checkForReload = () => {
                    if (!document.getElementById('notificationModal')) {
                        window.location.reload();
                    } else {
                        setTimeout(checkForReload, 200);
                    }
                };
                setTimeout(checkForReload, 500);
            } else {
                showNotificationModal(data.message || 'Erreur lors de la modification du service', 'error', 'Erreur');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotificationModal('Erreur lors de la modification du service', 'error', 'Erreur');
        })
        .finally(() => {
            // Réactiver le bouton
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });

    // Gérer la soumission du formulaire d'édition de dépense
    document.getElementById('editDepenseForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Désactiver le bouton et montrer un loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Modification...';
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => {
            if (response.status === 422) {
                // Erreur de validation
                return response.json().then(errorData => {
                    let errorMessage = 'Erreurs de validation:\n';
                    if (errorData.errors) {
                        for (let field in errorData.errors) {
                            errorMessage += `- ${errorData.errors[field].join(', ')}\n`;
                        }
                    }
                    showNotificationModal(errorMessage, 'error', 'Erreur de validation');
                    throw new Error('Validation failed');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotificationModal(data.message, 'success', 'Dépense modifiée');
                
                // Observer la fermeture de la modale de notification pour recharger
                const checkForReload = () => {
                    if (!document.getElementById('notificationModal')) {
                        window.location.reload();
                    } else {
                        setTimeout(checkForReload, 200);
                    }
                };
                setTimeout(checkForReload, 500);
            } else {
                showNotificationModal(data.message || 'Erreur lors de la modification de la dépense', 'error', 'Erreur');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotificationModal('Erreur lors de la modification de la dépense', 'error', 'Erreur');
        })
        .finally(() => {
            // Réactiver le bouton
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });

    // Gérer les boutons d'impression de fiche de dépense
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-print-depense')) {
            e.preventDefault();
            
            const button = e.target.closest('.btn-print-depense');
            const depenseData = JSON.parse(button.getAttribute('data-depense'));
            
            // Ouvrir la fiche de dépense dans un nouvel onglet
            const url = `/services/depenses/${depenseData.id}/fiche`;
            window.open(url, '_blank');
        }
    });
});

// Fonctions pour les filtres des dépenses
function filtrerDepenses() {
    const formData = new FormData();
    
    // Récupérer les valeurs des filtres
    const annee = document.getElementById('filter_depenses_annee').value;
    const mois = document.getElementById('filter_depenses_mois').value;
    const categorie = document.getElementById('filter_depenses_categorie').value;
    const service = document.getElementById('filter_depenses_service').value;
    const search = document.getElementById('filter_depenses_search').value;
    
    // Ajouter au FormData
    if (annee) formData.append('annee_scolaire_id', annee);
    if (mois) formData.append('mois', mois);
    if (categorie) formData.append('categorie_id', categorie);
    if (service) formData.append('service_id', service);
    if (search) formData.append('search', search);
    
    // Désactiver le bouton de filtrage
    const filterBtn = event.target;
    const originalText = filterBtn.innerHTML;
    filterBtn.disabled = true;
    filterBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Filtrage...';
    
    fetch('/services/depenses/filtrer', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mettre à jour le tableau des dépenses
            mettreAJourTableauDepenses(data.depenses);
            
            // Afficher un résumé
            showNotificationModal(
                `Filtrage terminé: ${data.total} dépense(s) trouvée(s) pour un montant total de ${formatMontant(data.montant_total)} FCFA`,
                'success',
                'Filtrage réussi'
            );
        } else {
            showNotificationModal('Erreur lors du filtrage', 'error', 'Erreur');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotificationModal('Erreur lors du filtrage', 'error', 'Erreur');
    })
    .finally(() => {
        filterBtn.disabled = false;
        filterBtn.innerHTML = originalText;
    });
}

function reinitialiserFiltresDepenses() {
    document.getElementById('filter_depenses_annee').value = '{{ $anneeActive->id }}';
    document.getElementById('filter_depenses_mois').value = '';
    document.getElementById('filter_depenses_categorie').value = '';
    document.getElementById('filter_depenses_service').value = '';
    document.getElementById('filter_depenses_search').value = '';
    
    // Appliquer le filtre avec année scolaire active seulement
    filtrerDepenses();
}

// Fonctions pour les rapports
function genererRapport() {
    const formData = new FormData();
    
    // Récupérer les valeurs des filtres
    const annee = document.getElementById('filter_rapports_annee').value;
    const mois = document.getElementById('filter_rapports_mois').value;
    const categorie = document.getElementById('filter_rapports_categorie').value;
    const service = document.getElementById('filter_rapports_service').value;
    const search = document.getElementById('filter_rapports_search').value;
    
    // Ajouter au FormData
    if (annee) formData.append('annee_scolaire_id', annee);
    if (mois) formData.append('mois', mois);
    if (categorie) formData.append('categorie_id', categorie);
    if (service) formData.append('service_id', service);
    if (search) formData.append('search', search);
    
    // Désactiver le bouton
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Génération...';
    
    fetch('/services/rapports/generer', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mettre à jour les statistiques
            mettreAJourStatistiques(data.statistiques);
            
            showNotificationModal(
                `Rapport généré avec succès: ${data.statistiques.total_depenses} dépenses analysées`,
                'success',
                'Rapport généré'
            );
        } else {
            showNotificationModal('Erreur lors de la génération du rapport', 'error', 'Erreur');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotificationModal('Erreur lors de la génération du rapport', 'error', 'Erreur');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

function exporterRapport() {
    const formData = new FormData();
    
    // Récupérer les valeurs des filtres
    const annee = document.getElementById('filter_rapports_annee').value;
    const mois = document.getElementById('filter_rapports_mois').value;
    const categorie = document.getElementById('filter_rapports_categorie').value;
    const service = document.getElementById('filter_rapports_service').value;
    const search = document.getElementById('filter_rapports_search').value;
    
    // Ajouter au FormData
    if (annee) formData.append('annee_scolaire_id', annee);
    if (mois) formData.append('mois', mois);
    if (categorie) formData.append('categorie_id', categorie);
    if (service) formData.append('service_id', service);
    if (search) formData.append('search', search);
    
    // Créer un formulaire temporaire pour soumettre en POST
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/services/rapports/exporter-pdf';
    form.target = '_blank';
    
    // Ajouter le token CSRF
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    form.appendChild(csrfToken);
    
    // Ajouter tous les champs
    for (let pair of formData.entries()) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = pair[0];
        input.value = pair[1];
        form.appendChild(input);
    }
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    
    showNotificationModal('Rapport en cours de génération dans un nouvel onglet', 'success', 'Export en cours');
}

function reinitialiserFiltresRapports() {
    document.getElementById('filter_rapports_annee').value = '{{ $anneeActive->id }}';
    document.getElementById('filter_rapports_mois').value = '';
    document.getElementById('filter_rapports_categorie').value = '';
    document.getElementById('filter_rapports_service').value = '';
    document.getElementById('filter_rapports_search').value = '';
    
    // Générer un nouveau rapport avec les filtres réinitialisés
    genererRapport();
}

// Fonctions utilitaires
function mettreAJourTableauDepenses(depenses) {
    const tbody = document.querySelector('#tableauDepenses tbody');
    if (!tbody) return;
    
    let html = '';
    
    if (depenses.length === 0) {
        html = `
            <tr>
                <td colspan="6" class="px-6 py-12 text-center">
                    <i class="fas fa-receipt text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">Aucune dépense trouvée avec ces critères</p>
                    <p class="text-sm text-gray-400">Modifiez vos filtres ou réinitialisez-les</p>
                </td>
            </tr>
        `;
    } else {
        depenses.forEach(depense => {
            const service = depense.service || {};
            const typeDepenseIcons = {
                'achat': 'fas fa-shopping-cart',
                'maintenance': 'fas fa-wrench',
                'reparation': 'fas fa-tools',
                'location': 'fas fa-home',
                'consommation': 'fas fa-bolt',
                'autre': 'fas fa-question'
            };
            
            const typeDepenseColors = {
                'achat': 'bg-blue-100 text-blue-800',
                'maintenance': 'bg-yellow-100 text-yellow-800',
                'reparation': 'bg-red-100 text-red-800',
                'location': 'bg-purple-100 text-purple-800',
                'consommation': 'bg-green-100 text-green-800',
                'autre': 'bg-gray-100 text-gray-800'
            };
            
            const dateDepense = new Date(depense.date_depense).toLocaleDateString('fr-FR');
            const montant = new Intl.NumberFormat('fr-FR').format(depense.montant);
            
            html += `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">${service.nom || 'N/A'}</div>
                        <div class="text-sm text-gray-500">${depense.description || ''}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2 py-1 text-xs rounded-full ${typeDepenseColors[depense.type_depense] || 'bg-gray-100 text-gray-800'}">
                            <i class="${typeDepenseIcons[depense.type_depense] || 'fas fa-question'} mr-1"></i>
                            ${depense.type_depense.charAt(0).toUpperCase() + depense.type_depense.slice(1)}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">${montant} FCFA</td>
                    <td class="px-6 py-4 text-sm text-gray-500">${dateDepense}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">${depense.numero_facture || '-'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-2">
                            <button class="text-blue-600 hover:text-blue-900 btn-print-depense" 
                                    data-depense='${JSON.stringify(depense)}'
                                    title="Imprimer la fiche de dépense">
                                <i class="fas fa-print"></i>
                            </button>
                            <button class="text-orange-600 hover:text-orange-900 btn-edit-depense" 
                                    data-depense='${JSON.stringify(depense)}'>
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="text-red-600 hover:text-red-900 btn-delete-depense" 
                                    data-url="/services/depenses/${depense.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    tbody.innerHTML = html;
}

function mettreAJourStatistiques(stats) {
    // Mettre à jour le total des dépenses
    const totalDepensesEl = document.querySelector('.stat-total-depenses');
    if (totalDepensesEl) {
        totalDepensesEl.textContent = stats.total_depenses;
    }
    
    // Mettre à jour le montant total
    const montantTotalEl = document.querySelector('.stat-montant-total');
    if (montantTotalEl) {
        montantTotalEl.textContent = formatMontant(stats.montant_total) + ' FCFA';
    }
    
    // Afficher un résumé des statistiques dans une notification
    let resumeStats = `
        📊 Résumé du rapport:
        • ${stats.total_depenses} dépenses analysées
        • Montant total: ${formatMontant(stats.montant_total)} FCFA
        • Moyenne par dépense: ${formatMontant(stats.moyenne_depense)} FCFA
    `;
    
    if (stats.par_type && Object.keys(stats.par_type).length > 0) {
        resumeStats += '\n\n📋 Par type de dépense:';
        Object.entries(stats.par_type).forEach(([type, data]) => {
            resumeStats += `\n• ${type}: ${data.count} (${formatMontant(data.montant)} FCFA)`;
        });
    }
    
    console.log('Statistiques complètes:', stats);
    
    // Afficher le résumé dans la console pour le développement
    console.log(resumeStats);
}

function formatMontant(montant) {
    return new Intl.NumberFormat('fr-FR').format(montant);
}

// Fonctions pour l'export des dépenses
function exporterDepensesFiltrées() {
    const formData = new FormData();
    
    // Récupérer les valeurs des filtres
    const annee = document.getElementById('filter_depenses_annee').value;
    const mois = document.getElementById('filter_depenses_mois').value;
    const categorie = document.getElementById('filter_depenses_categorie').value;
    const service = document.getElementById('filter_depenses_service').value;
    const search = document.getElementById('filter_depenses_search').value;
    
    // Ajouter au FormData
    if (annee) formData.append('annee_scolaire_id', annee);
    if (mois) formData.append('mois', mois);
    if (categorie) formData.append('categorie_id', categorie);
    if (service) formData.append('service_id', service);
    if (search) formData.append('search', search);
    
    // Créer un formulaire temporaire pour soumettre en POST
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/services/depenses/exporter';
    form.target = '_blank';
    
    // Ajouter le token CSRF
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    form.appendChild(csrfToken);
    
    // Ajouter tous les champs
    for (let pair of formData.entries()) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = pair[0];
        input.value = pair[1];
        form.appendChild(input);
    }
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    
    showNotificationModal('Export des dépenses en cours de génération dans un nouvel onglet', 'success', 'Export en cours');
}

function apercuDepensesFiltrées() {
    const formData = new FormData();
    
    // Récupérer les valeurs des filtres
    const annee = document.getElementById('filter_depenses_annee').value;
    const mois = document.getElementById('filter_depenses_mois').value;
    const categorie = document.getElementById('filter_depenses_categorie').value;
    const service = document.getElementById('filter_depenses_service').value;
    const search = document.getElementById('filter_depenses_search').value;
    
    // Ajouter au FormData
    if (annee) formData.append('annee_scolaire_id', annee);
    if (mois) formData.append('mois', mois);
    if (categorie) formData.append('categorie_id', categorie);
    if (service) formData.append('service_id', service);
    if (search) formData.append('search', search);
    
    // Créer un formulaire temporaire pour soumettre en POST
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/services/depenses/apercu';
    form.target = '_blank';
    
    // Ajouter le token CSRF
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    form.appendChild(csrfToken);
    
    // Ajouter tous les champs
    for (let pair of formData.entries()) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = pair[0];
        input.value = pair[1];
        form.appendChild(input);
    }
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    
    showNotificationModal('Aperçu des dépenses en cours de génération dans un nouvel onglet', 'success', 'Aperçu en cours');
}
</script>
@endpush

@endsection