@extends('layouts.app')

@section('title', 'Gestion des Inscriptions')

@section('content')
<div class="min-h-screen bg-gray-50 py-6" x-data="{ activeTab: '{{ $activeTab ?? 'pre-inscription' }}' }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="mb-8">
            <div class="flex items-center space-x-3 mb-2">
                <div class="bg-blue-100 p-2 rounded-lg">
                    <i class="fas fa-user-graduate text-blue-600 text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Gestion des Inscriptions</h1>
                    <p class="text-sm text-gray-600">Gérez les pré-inscriptions et inscriptions des élèves</p>
                </div>
            </div>
        </div>

        <!-- Navigation par onglets -->
        <div class="bg-white rounded-lg shadow-sm border mb-6">
            <nav class="flex space-x-1 p-1">
                <button @click="activeTab = 'pre-inscription'" 
                        :class="activeTab === 'pre-inscription' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-gray-50'"
                        class="flex items-center px-4 py-3 rounded-md font-medium text-sm transition-all duration-200 flex-1 justify-center">
                    <i class="fas fa-user-plus mr-2"></i>Pré-inscription
                </button>
                <button @click="activeTab = 'inscription'" 
                        :class="activeTab === 'inscription' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-gray-50'"
                        class="flex items-center px-4 py-3 rounded-md font-medium text-sm transition-all duration-200 flex-1 justify-center">
                    <i class="fas fa-user-check mr-2"></i>Inscription
                </button>
                <button @click="activeTab = 'liste-eleves'" 
                        :class="activeTab === 'liste-eleves' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-gray-50'"
                        class="flex items-center px-4 py-3 rounded-md font-medium text-sm transition-all duration-200 flex-1 justify-center">
                    <i class="fas fa-users mr-2"></i>Liste Élèves
                </button>
                <button @click="activeTab = 'liste-administrative'" 
                        :class="activeTab === 'liste-administrative' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-gray-50'"
                        class="flex items-center px-4 py-3 rounded-md font-medium text-sm transition-all duration-200 flex-1 justify-center">
                    <i class="fas fa-file-alt mr-2"></i>Liste d'Appel
                </button>
                </button>
                <button @click="activeTab = 'rapports'" 
                        :class="activeTab === 'rapports' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-gray-50'"
                        class="flex items-center px-4 py-3 rounded-md font-medium text-sm transition-all duration-200 flex-1 justify-center">
                    <i class="fas fa-chart-bar mr-2"></i>Rapports
                </button>
            </nav>
        </div>

        <!-- Onglet Pré-inscription -->
        <div x-show="activeTab === 'pre-inscription'" x-transition class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Pré-inscriptions</h3>
                    <p class="mt-1 text-sm text-gray-600">Enregistrez les candidatures des nouveaux élèves</p>
                </div>
                <button onclick="openModal('addPreInscriptionModal')" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>Nouvelle pré-inscription
                </button>
            </div>

            <!-- Avertissement de validation -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-blue-900">Informations importantes</h4>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Tous les champs marqués d'un <span class="text-red-600 font-bold">*</span> sont obligatoires</li>
                                <li>Chaque élève doit avoir un <strong>INE unique</strong> (généré automatiquement si non fourni)</li>
                                <li>Les doublons d'INE sont automatiquement détectés et bloqués</li>
                                <li>La validation des inscriptions se base sur l'INE pour éviter les doublons</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtres -->
            <div class="bg-white p-4 rounded-lg shadow-sm border">
                <form method="GET" class="flex items-center space-x-4">
                    <input type="hidden" name="tab" value="pre-inscription">
                    <div class="flex-1">
                        <input type="text" name="search" placeholder="Rechercher par nom, prénom ou INE..." 
                               value="{{ request('search') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-md text-sm hover:bg-gray-700">
                        <i class="fas fa-search mr-2"></i>Rechercher
                    </button>
                    @if(request('search'))
                        <a href="{{ route('inscriptions.index', ['tab' => 'pre-inscription']) }}" 
                           class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm hover:bg-gray-400">
                            <i class="fas fa-times mr-2"></i>Effacer
                        </a>
                    @endif
                </form>
            </div>

            <!-- Tableau des pré-inscriptions -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">INE</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Élève</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @if(isset($preInscriptions))
                                @forelse($preInscriptions as $preInscription)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-mono text-gray-900">{{ $preInscription->ine }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $preInscription->nom }} {{ $preInscription->prenom }}</p>
                                            @if($preInscription->date_naissance)
                                                <p class="text-gray-500">Né(e) le {{ $preInscription->date_naissance->format('d/m/Y') }}</p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500">
                                        @if($preInscription->contact)
                                            <p>{{ $preInscription->contact }}</p>
                                        @endif
                                        @if($preInscription->tuteur)
                                            <p class="text-xs">Tuteur: {{ $preInscription->tuteur }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        @switch($preInscription->statut)
                                            @case('en_attente')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    En attente
                                                </span>
                                                @break
                                            @case('inscrit')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Inscrit
                                                </span>
                                                @break
                                            @case('rejete')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Rejeté
                                                </span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm font-medium">
                                        <button onclick="editPreInscription({{ json_encode([
                                            'id' => $preInscription->id,
                                            'nom' => $preInscription->nom,
                                            'prenom' => $preInscription->prenom,
                                            'ine' => $preInscription->ine,
                                            'date_naissance' => $preInscription->date_naissance ? $preInscription->date_naissance->format('Y-m-d') : '',
                                            'lieu_naissance' => $preInscription->lieu_naissance,
                                            'adresse' => $preInscription->adresse,
                                            'contact' => $preInscription->contact,
                                            'tuteur' => $preInscription->tuteur,
                                            'etablissement_origine' => $preInscription->etablissement_origine
                                        ]) }})" 
                                                class="text-blue-600 hover:text-blue-900 mr-2">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @if(!$preInscription->inscription)
                                            <form method="POST" action="{{ route('inscriptions.destroy-pre', $preInscription) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" 
                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette pré-inscription ?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                        <i class="fas fa-user-plus text-4xl text-gray-300 mb-4"></i>
                                        <p>Aucune pré-inscription trouvée</p>
                                    </td>
                                </tr>
                                @endforelse
                            @else
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                        <i class="fas fa-user-plus text-4xl text-gray-300 mb-4"></i>
                                        <p>Aucune pré-inscription trouvée</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                @if(isset($preInscriptions) && $preInscriptions->hasPages())
                    <div class="px-4 py-3 border-t border-gray-200">
                        {{ $preInscriptions->appends(['tab' => 'pre-inscription'])->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Onglet Inscription -->
        <div x-show="activeTab === 'inscription'" x-transition class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Finaliser les inscriptions</h3>
                    <p class="mt-1 text-sm text-gray-600">Compléter l'inscription des élèves pré-inscrits</p>
                </div>
            </div>

            <!-- Formulaire d'inscription -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <form method="POST" action="{{ route('inscriptions.finaliser') }}" x-data="inscriptionForm()">
                    @csrf
                    
                    <!-- Recherche élève -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="md:col-span-2">
                            <label for="search_eleve" class="block text-sm font-medium text-gray-700 mb-2">
                                Rechercher un élève pré-inscrit
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       x-model="searchTerm"
                                       @input.debounce.200ms="searchEleves()"
                                       @focus="showResults = results.length > 0"
                                       @blur.away="showResults = false"
                                       placeholder="Tapez le nom, prénom ou INE (minimum 1 caractère)..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                                
                                <!-- Indicateur de recherche -->
                                <div x-show="searching" class="absolute right-3 top-3">
                                    <i class="fas fa-spinner fa-spin text-gray-400"></i>
                                </div>
                                
                                <!-- Résultats de recherche -->
                                <div x-show="showResults && results.length > 0" 
                                     class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
                                    <template x-for="result in results" :key="result.id">
                                        <div @click="selectEleve(result)"
                                             class="px-4 py-2 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0">
                                            <div class="flex justify-between items-start">
                                                <div class="flex-1">
                                                    <p class="font-medium text-gray-900" x-text="result.data.nom + ' ' + result.data.prenom"></p>
                                                    <p class="text-sm text-gray-500" x-text="'INE: ' + result.data.ine"></p>
                                                    <p class="text-xs text-gray-400" x-text="result.data.contact || 'Pas de contact'"></p>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <span :class="result.couleur || 'text-blue-600'" 
                                                          class="text-xs font-medium px-2 py-1 rounded-full bg-gray-100"
                                                          x-text="result.statut || 'Non inscrit'"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                
                                <!-- Message quand pas de résultats -->
                                <div x-show="showResults && results.length === 0 && searchTerm.length >= 1" 
                                     class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg p-4 text-center text-gray-500">
                                    <i class="fas fa-search text-gray-300 mb-2"></i>
                                    <p class="text-sm">Aucun élève trouvé pour "<span x-text="searchTerm"></span>"</p>
                                </div>
                            </div>
                            <input type="hidden" name="pre_inscription_id" x-model="selectedEleveId">
                        </div>
                    </div>

                    <!-- Informations de l'élève sélectionné -->
                    <div x-show="selectedEleve" class="bg-blue-50 p-4 rounded-lg mb-6">
                        <h4 class="font-medium text-blue-900 mb-2">Élève sélectionné</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-blue-700">Nom complet:</span>
                                <span class="ml-2 font-medium" x-text="selectedEleve ? selectedEleve.nom + ' ' + selectedEleve.prenom : ''"></span>
                            </div>
                            <div>
                                <span class="text-blue-700">INE:</span>
                                <span class="ml-2 font-mono" x-text="selectedEleve ? selectedEleve.ine : ''"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Informations d'inscription -->
                    <div x-show="selectedEleve" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="niveau_id" class="block text-sm font-medium text-gray-700">Niveau *</label>
                            <select name="niveau_id" x-model="selectedNiveau" @change="loadClasses(); loadFrais()"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Sélectionner un niveau</option>
                                @if(isset($niveaux))
                                    @foreach($niveaux as $niveau)
                                        <option value="{{ $niveau->id }}">{{ $niveau->nom }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div>
                            <label for="classe_id" class="block text-sm font-medium text-gray-700">Classe *</label>
                            <select name="classe_id" x-model="selectedClasse"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Sélectionner une classe</option>
                                <template x-for="classe in availableClasses" :key="classe.id">
                                    <option :value="classe.id" x-text="classe.nom"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label for="montant_total" class="block text-sm font-medium text-gray-700">
                                Montant total (frais d'inscription) *
                                <span class="text-xs text-gray-500">Automatique selon le niveau</span>
                            </label>
                            <input type="number" name="montant_total" x-model="montantTotal" step="0.01" min="0" readonly
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm bg-gray-50 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="montant_paye" class="block text-sm font-medium text-gray-700">Montant payé *</label>
                            <input type="number" name="montant_paye" x-model="montantPaye" step="0.01" min="0" 
                                   :max="montantTotal" @input="validateMontantPaye()"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                            <p x-show="montantPaye > montantTotal" class="text-red-500 text-xs mt-1">
                                Le montant payé ne peut pas dépasser le montant total (<span x-text="montantTotal"></span> FCFA)
                            </p>
                        </div>

                        <div class="md:col-span-2">
                            <label for="mode_paiement" class="block text-sm font-medium text-gray-700">Mode de paiement *</label>
                            <select name="mode_paiement"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Sélectionner un mode</option>
                                <option value="orange_money">Orange Money</option>
                                <option value="wave">Wave</option>
                                <option value="free_money">Free Money</option>
                                <option value="billetage">Billetage</option>
                            </select>
                        </div>

                        <div class="md:col-span-2 pt-4">
                            <button type="button" x-show="selectedEleve" @click="confirmerInscription()"
                                    class="bg-blue-600 text-white px-6 py-2 rounded-md text-sm hover:bg-blue-700">
                                <i class="fas fa-check mr-2"></i>Finaliser l'inscription
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Onglet Liste d'Appel -->
        <div x-show="activeTab === 'liste-administrative'" x-transition class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Liste d'Appel des Élèves</h3>
                    <p class="mt-1 text-sm text-gray-600">Liste officielle pour l'administration scolaire ({{ $anneeActive->libelle ?? 'Année en cours' }})</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button type="button" onclick="showListeAppelPreview()" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-eye mr-2"></i>
                        Aperçu
                    </button>
                </div>
            </div>

            <!-- Filtres pour liste d'appel -->
            <div class="bg-white p-4 rounded-lg shadow-sm border">
                <form method="GET" class="flex items-center space-x-4" id="filterAdministrativeForm">
                    <input type="hidden" name="tab" value="liste-administrative">
                    <div class="flex-1">
                        <label for="search_admin" class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                        <input type="text" id="search_admin" name="search_admin" 
                               value="{{ request('search_admin') }}" 
                               placeholder="Nom, prénom, numéro d'inscription..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="w-48">
                        <label for="classe_admin" class="block text-sm font-medium text-gray-700 mb-1">Classe</label>
                        <select name="classe_admin" id="classe_admin" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Toutes les classes</option>
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}" {{ request('classe_admin') == $classe->id ? 'selected' : '' }}>
                                    {{ $classe->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex space-x-2 pt-6">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            <i class="fas fa-search mr-1"></i>Filtrer
                        </button>
                        <a href="{{ route('inscriptions.index', ['tab' => 'liste-administrative']) }}" 
                           class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                            <i class="fas fa-times mr-1"></i>Réinitialiser
                        </a>
                    </div>
                </form>
            </div>

            <!-- Tableau liste d'appel -->
            <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N°</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Élève</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sexe</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date de naissance</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lieu de naissance</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Classe</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parent/Tuteur</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                $administrativeQuery = App\Models\Inscription::with(['preInscription', 'classe'])
                                    ->where('statut', 'actif'); // Le statut correct est 'actif', pas 'active'
                                
                                if ($anneeActive) {
                                    $administrativeQuery->where('annee_scolaire_id', $anneeActive->id);
                                }
                                
                                if (request('search_admin')) {
                                    $searchAdmin = request('search_admin');
                                    $administrativeQuery->whereHas('preInscription', function($q) use ($searchAdmin) {
                                        $q->where('nom', 'like', "%{$searchAdmin}%")
                                          ->orWhere('prenom', 'like', "%{$searchAdmin}%");
                                    })->orWhere('numero_recu', 'like', "%{$searchAdmin}%");
                                }
                                
                                if (request('classe_admin')) {
                                    $administrativeQuery->where('classe_id', request('classe_admin'));
                                }
                                
                                $administrativeInscriptions = $administrativeQuery->orderBy('numero_recu')->paginate(20, ['*'], 'admin_page');
                                $counter = ($administrativeInscriptions->currentPage() - 1) * $administrativeInscriptions->perPage() + 1;
                            @endphp
                            
                            @forelse($administrativeInscriptions as $inscription)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                        {{ $counter++ }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $inscription->preInscription->nom }} {{ $inscription->preInscription->prenom }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            N° {{ $inscription->numero_recu }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $inscription->preInscription->sexe === 'M' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                            {{ $inscription->preInscription->sexe === 'M' ? 'Masculin' : 'Féminin' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($inscription->preInscription->date_naissance)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ $inscription->preInscription->lieu_naissance }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $inscription->classe->nom ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ $inscription->preInscription->tuteur }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ $inscription->preInscription->contact }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($inscription->statut == 'actif')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Inscrit
                                            </span>
                                        @elseif($inscription->statut == 'suspendu')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-pause-circle mr-1"></i>
                                                Suspendu
                                            </span>
                                        @elseif($inscription->statut == 'transfere')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <i class="fas fa-exchange-alt mr-1"></i>
                                                Transféré
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <i class="fas fa-question-circle mr-1"></i>
                                                {{ ucfirst($inscription->statut) }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                        <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                                        <p>Aucun élève inscrit trouvé pour les critères sélectionnés</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if(isset($administrativeInscriptions) && $administrativeInscriptions->hasPages())
                    <div class="px-4 py-3 border-t border-gray-200">
                        {{ $administrativeInscriptions->appends(request()->except('admin_page'))->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Onglet Liste des élèves -->
        <div x-show="activeTab === 'liste-eleves'" x-transition class="space-y-6">
            <!-- Filtres simplifiés -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-filter text-blue-600 mr-2"></i>
                    Filtres de recherche
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    
                    <!-- Recherche -->
                    <div>
                        <label for="search_eleves" class="block text-sm font-medium text-gray-700 mb-2">Recherche rapide</label>
                        <div class="relative">
                            <input type="text" id="search_eleves" 
                                   value="{{ request('search_eleves') }}"
                                   placeholder="Nom, prénom, INE..."
                                   class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                            <div class="absolute left-3 top-2.5">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Année scolaire -->
                    <div>
                        <label for="filter_annee" class="block text-sm font-medium text-gray-700 mb-2">Année scolaire</label>
                        <select id="filter_annee" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                            @foreach($anneesScolaires ?? [] as $annee)
                                <option value="{{ $annee->id }}" {{ (request('filter_annee', $anneeActive->id) == $annee->id) ? 'selected' : '' }}>
                                    {{ $annee->libelle }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Niveau -->
                    <div>
                        <label for="filter_niveau" class="block text-sm font-medium text-gray-700 mb-2">Niveau</label>
                        <select id="filter_niveau" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tous les niveaux</option>
                            @foreach($niveaux ?? [] as $niveau)
                                <option value="{{ $niveau->id }}" {{ request('filter_niveau') == $niveau->id ? 'selected' : '' }}>
                                    {{ $niveau->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Classe -->
                    <div>
                        <label for="filter_classe" class="block text-sm font-medium text-gray-700 mb-2">Classe</label>
                        <select id="filter_classe" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Toutes les classes</option>
                            @foreach($classes ?? [] as $classe)
                                <option value="{{ $classe->id }}" {{ request('filter_classe') == $classe->id ? 'selected' : '' }}>
                                    {{ $classe->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Statut -->
                    <div>
                        <label for="filter_statut" class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                        <select id="filter_statut" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tous les statuts</option>
                            <option value="inscrits" {{ request('filter_statut') == 'inscrits' ? 'selected' : '' }}>✅ Inscrits (finalisés)</option>
                            <option value="non_inscrits" {{ request('filter_statut') == 'non_inscrits' ? 'selected' : '' }}>⚠️ Non inscrits (pré-inscrits seulement)</option>
                        </select>
                    </div>

                    <!-- Boutons -->
                    <div class="md:col-span-2 lg:col-span-5 flex justify-between items-center pt-4 border-t border-gray-200">
                        <div class="flex space-x-3">
                            <button type="button" onclick="reinitialiserFiltres()" 
                               class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                <i class="fas fa-times mr-2"></i>Réinitialiser
                            </button>
                        </div>
                        <button type="button" onclick="exportInscriptions()" 
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700">
                            <i class="fas fa-file-export mr-2"></i>Exporter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Liste des élèves -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-users text-blue-600 mr-2"></i>
                            Liste des Élèves Inscrits
                        </h3>
                        <div class="text-sm text-gray-600">
                            Total: <span class="font-medium">{{ $inscriptions->total() ?? 0 }}</span> élèves
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Élève</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Classe</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28">Montant</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Statut</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Date</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($inscriptions ?? [] as $inscription)
                                @php
                                    // Détecter si c'est une PreInscription (non inscrit) ou une Inscription (inscrit)
                                    $isPreInscription = !isset($inscription->preInscription);
                                    $eleve = $isPreInscription ? $inscription : $inscription->preInscription;
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <div class="flex items-start space-x-3">
                                            <div class="flex-shrink-0">
                                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                    <span class="text-sm font-medium text-blue-600">
                                                        {{ substr($eleve->nom, 0, 1) }}{{ substr($eleve->prenom, 0, 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="font-medium text-gray-900 truncate" style="max-width: 160px;">
                                                    {{ $eleve->nom }} {{ $eleve->prenom }}
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    {{ $eleve->ine }}
                                                    @if($eleve->date_naissance)
                                                        • {{ $eleve->date_naissance->format('d/m/Y') }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-sm text-gray-900">
                                        <div>
                                            @if($isPreInscription)
                                                <p class="font-medium">{{ $inscription->classe->nom ?? 'N/A' }}</p>
                                                <p class="text-xs text-gray-500">{{ $inscription->niveau->nom ?? 'N/A' }}</p>
                                            @else 
                                                <p class="font-medium">{{ $inscription->classe->nom }}</p>
                                                <p class="text-xs text-gray-500">{{ $inscription->niveau->nom }}</p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm">
                                            @if($isPreInscription)
                                                <div class="flex items-center space-x-2">
                                                    <span class="font-medium text-gray-400">0</span>
                                                    <span class="text-gray-400">/</span>
                                                    <span class="text-gray-600">{{ number_format($inscription->frais_inscription ?? 0, 0, ',', ' ') }}</span>
                                                </div>
                                                <div class="mt-1">
                                                    <div class="w-24 bg-gray-200 rounded-full h-2">
                                                        <div class="bg-gray-400 h-2 rounded-full" style="width: 0%"></div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="flex items-center space-x-2">
                                                    <span class="font-medium">{{ number_format($inscription->montant_paye, 0, ',', ' ') }}</span>
                                                    <span class="text-gray-400">/</span>
                                                    <span class="text-gray-600">{{ number_format($inscription->montant_total, 0, ',', ' ') }}</span>
                                                </div>
                                                <div class="mt-1">
                                                    @php
                                                        $pourcentage = $inscription->montant_total > 0 ? ($inscription->montant_paye / $inscription->montant_total) * 100 : 0;
                                                    @endphp
                                                    <div class="w-24 bg-gray-200 rounded-full h-2">
                                                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                                             style="width: {{ min(100, $pourcentage) }}%"></div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="flex flex-col space-y-1">
                                            @if($isPreInscription)
                                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                                    Non inscrit
                                                </span>
                                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                                    En attente
                                                </span>
                                            @else
                                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                    Inscrit
                                                </span>
                                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full 
                                                    {{ $inscription->statut_paiement === 'complet' ? 'bg-green-100 text-green-800' : 
                                                       ($inscription->statut_paiement === 'partiel' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                    {{ $inscription->statut_paiement === 'complet' ? 'Payé' : 
                                                       ($inscription->statut_paiement === 'partiel' ? 'Partiel' : 'Impayé') }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-xs text-gray-900">
                                        {{ $inscription->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-3 py-3 text-xs space-x-1">
                                        @if($isPreInscription)
                                            <!-- Actions pour les pré-inscriptions (non inscrits) -->
                                            <button onclick="finaliserInscription({{ $inscription->id }})"
                                                    class="inline-flex items-center px-2 py-1 text-xs text-white bg-green-600 rounded hover:bg-green-700"
                                                    title="Finaliser l'inscription">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            
                                            <button onclick="editPreInscription({{ json_encode([
                                                'id' => $inscription->id,
                                                'nom' => $inscription->nom,
                                                'prenom' => $inscription->prenom,
                                                'ine' => $inscription->ine,
                                                'contact' => $inscription->contact ?? '',
                                                'date_naissance' => $inscription->date_naissance ? $inscription->date_naissance->format('Y-m-d') : ''
                                            ]) }})"
                                                    class="inline-flex items-center px-2 py-1 text-xs text-white bg-blue-600 rounded hover:bg-blue-700"
                                                    title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            @if($inscription->contact)
                                                <a href="tel:{{ $inscription->contact }}" 
                                                   class="inline-flex items-center px-2 py-1 text-xs text-white bg-orange-600 rounded hover:bg-orange-700" 
                                                   title="Appeler">
                                                    <i class="fas fa-phone"></i>
                                                </a>
                                            @endif
                                        @else
                                            <!-- Actions pour les inscriptions finalisées -->
                                            <a href="{{ route('inscriptions.recu', $inscription) }}"
                                               class="inline-flex items-center px-2 py-1 text-xs text-white bg-green-600 rounded hover:bg-green-700" 
                                               target="_blank" title="Voir le reçu">
                                                <i class="fas fa-receipt"></i>
                                            </a>
                                            
                                            <button onclick="editInscription({{ json_encode([
                                                'id' => $inscription->id,
                                                'niveau_id' => $inscription->niveau_id,
                                                'classe_id' => $inscription->classe_id,
                                                'montant_total' => $inscription->montant_total,
                                                'montant_paye' => $inscription->montant_paye,
                                                'mode_paiement' => $inscription->mode_paiement,
                                                'statut' => $inscription->statut,
                                                'remarques' => $inscription->remarques ?? '',
                                                'eleve' => [
                                                    'nom_complet' => $eleve->nom . ' ' . $eleve->prenom,
                                                    'ine' => $eleve->ine,
                                                    'contact' => $eleve->contact,
                                                    'date_naissance' => $eleve->date_naissance ? $eleve->date_naissance->format('d/m/Y') : ''
                                                ]
                                            ]) }})"
                                                    class="inline-flex items-center px-2 py-1 text-xs text-white bg-blue-600 rounded hover:bg-blue-700"
                                                    title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            @if($eleve->contact)
                                                <a href="tel:{{ $eleve->contact }}" 
                                                   class="inline-flex items-center px-2 py-1 text-xs text-white bg-orange-600 rounded hover:bg-orange-700" 
                                                   title="Appeler">
                                                    <i class="fas fa-phone"></i>
                                                </a>
                                            @endif
                                            
                                            <button onclick="annulerInscription({{ $inscription->id }}, '{{ $eleve->nom }} {{ $eleve->prenom }}')"
                                                    class="inline-flex items-center px-2 py-1 text-xs text-white bg-red-600 rounded hover:bg-red-700"
                                                    title="Annuler l'inscription">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                                            <p class="text-lg font-medium">Aucun élève inscrit trouvé</p>
                                            <p class="text-sm">Les élèves inscrits apparaîtront ici</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($inscriptions && method_exists($inscriptions, 'hasPages') && $inscriptions->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $inscriptions->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Onglet Rapports -->
        <div x-show="activeTab === 'rapports'" x-transition class="space-y-6" x-data="{ activeSubTab: 'dashboard' }">
            <!-- Navigation sous-onglets -->
            <div class="bg-white rounded-lg shadow-sm border">
                <nav class="flex space-x-1 p-1">
                    <button @click="activeSubTab = 'dashboard'" 
                            :class="activeSubTab === 'dashboard' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-gray-50'"
                            class="flex items-center px-4 py-3 rounded-md font-medium text-sm transition-all duration-200 flex-1 justify-center">
                        <i class="fas fa-chart-pie mr-2"></i>Tableau de Bord
                    </button>
                    <button @click="activeSubTab = 'rapports'" 
                            :class="activeSubTab === 'rapports' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-gray-50'"
                            class="flex items-center px-4 py-3 rounded-md font-medium text-sm transition-all duration-200 flex-1 justify-center">
                        <i class="fas fa-chart-bar mr-2"></i>Rapports & Analyses
                    </button>
                </nav>
            </div>

            <!-- Sous-onglet Tableau de Bord -->
            <div x-show="activeSubTab === 'dashboard'" x-transition class="space-y-6">
                <!-- En-tête dashboard -->
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-pie text-blue-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Tableau de Bord des Inscriptions</h3>
                            <p class="text-sm text-gray-600">Vue d'ensemble et indicateurs clés - Année {{ $anneeActive->libelle ?? 'en cours' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Statistiques principales -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total pré-inscriptions -->
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Pré-inscriptions</p>
                                <p class="text-2xl font-bold text-blue-600">{{ $statistiques['total_pre_inscriptions'] ?? 0 }}</p>
                                <p class="text-xs text-gray-500">Demandes d'inscription</p>
                            </div>
                            <div class="p-3 bg-blue-100 rounded-full">
                                <i class="fas fa-user-plus text-xl text-blue-600"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Inscriptions validées -->
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Inscriptions Validées</p>
                                <p class="text-2xl font-bold text-green-600">{{ $statistiques['inscriptions_validees'] ?? 0 }}</p>
                                @php
                                    $tauxValidation = ($statistiques['total_pre_inscriptions'] ?? 0) > 0 ? 
                                        round((($statistiques['inscriptions_validees'] ?? 0) / $statistiques['total_pre_inscriptions']) * 100, 1) : 0;
                                @endphp
                                <p class="text-xs text-gray-500">{{ $tauxValidation }}% de conversion</p>
                            </div>
                            <div class="p-3 bg-green-100 rounded-full">
                                <i class="fas fa-user-check text-xl text-green-600"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total recettes -->
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Total Recettes</p>
                                <p class="text-2xl font-bold text-indigo-600">
                                    {{ number_format($statistiques['total_recettes'] ?? 0, 0, ',', ' ') }}
                                </p>
                                <p class="text-xs text-gray-500">FCFA encaissés</p>
                            </div>
                            <div class="p-3 bg-indigo-100 rounded-full">
                                <i class="fas fa-coins text-xl text-indigo-600"></i>
                            </div>
                        </div>
                    </div>

                    <!-- En attente -->
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">En Attente</p>
                                <p class="text-2xl font-bold text-orange-600">
                                    {{ ($statistiques['total_pre_inscriptions'] ?? 0) - ($statistiques['inscriptions_validees'] ?? 0) }}
                                </p>
                                <p class="text-xs text-gray-500">Pré-inscriptions non validées</p>
                            </div>
                            <div class="p-3 bg-orange-100 rounded-full">
                                <i class="fas fa-clock text-xl text-orange-600"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section paiements -->
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-credit-card text-2xl mr-3 text-green-600"></i>
                        <h3 class="text-lg font-semibold text-gray-900">Analyse des Paiements</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $statistiques['paiements_complets'] ?? 0 }}</div>
                            <div class="text-gray-600">Paiements Complets</div>
                            @php
                                $tauxComplet = ($statistiques['inscriptions_validees'] ?? 0) > 0 ? 
                                    round((($statistiques['paiements_complets'] ?? 0) / $statistiques['inscriptions_validees']) * 100, 1) : 0;
                            @endphp
                            <div class="text-xs text-gray-500">{{ $tauxComplet }}% des inscriptions</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-600">{{ $statistiques['paiements_partiels'] ?? 0 }}</div>
                            <div class="text-gray-600">Paiements Partiels</div>
                            @php
                                $montantMoyen = ($statistiques['inscriptions_validees'] ?? 0) > 0 ? 
                                    ($statistiques['total_recettes'] ?? 0) / $statistiques['inscriptions_validees'] : 0;
                            @endphp
                            <div class="text-xs text-gray-500">{{ number_format($montantMoyen, 0, ',', ' ') }} FCFA/inscription</div>
                        </div>
                        <div class="text-center">
                            @php
                                $tauxRecouvrement = ($statistiques['total_recettes'] ?? 0) > 0 && ($statistiques['inscriptions_validees'] ?? 0) > 0 ? 
                                    round((($statistiques['paiements_complets'] ?? 0) / $statistiques['inscriptions_validees']) * 100, 1) : 0;
                            @endphp
                            <div class="text-2xl font-bold text-purple-600">{{ $tauxRecouvrement }}%</div>
                            <div class="text-gray-600">Taux de Recouvrement</div>
                            <div class="text-xs text-gray-500">Objectif: 90%</div>
                        </div>
                    </div>
                </div>

                <!-- Répartition par niveau -->
                <div class="bg-white rounded-lg shadow-sm border">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-layer-group text-blue-600 mr-2"></i>
                            Répartition par Niveau
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Niveau</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pré-inscriptions</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inscriptions</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conversion</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recettes</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($niveaux as $niveau)
                                @php
                                    $preInscriptionsNiveau = $niveau->preInscriptions()->where('annee_scolaire_id', $anneeActive->id ?? null)->count();
                                    $inscriptionsNiveau = $niveau->inscriptions()->where('annee_scolaire_id', $anneeActive->id ?? null)->count();
                                    $recettesNiveau = $niveau->inscriptions()->where('annee_scolaire_id', $anneeActive->id ?? null)->sum('montant_paye');
                                    $tauxConversion = $preInscriptionsNiveau > 0 ? round(($inscriptionsNiveau / $preInscriptionsNiveau) * 100, 1) : 0;
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $niveau->nom }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $preInscriptionsNiveau }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                        {{ $inscriptionsNiveau }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $tauxConversion >= 70 ? 'bg-green-100 text-green-800' : ($tauxConversion >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $tauxConversion }}%
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ number_format($recettesNiveau, 0, ',', ' ') }} FCFA
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sous-onglet Rapports & Analyses -->
            <div x-show="activeSubTab === 'rapports'" x-transition class="space-y-6">
                @include('inscriptions.partials.reports_inscriptions')
            </div>
        </div>
    </div>

    @include('inscriptions.modals')
</div>

<script>

</script>

<script>
function inscriptionForm() {
    return {
        searchTerm: '',
        showResults: false,
        results: [],
        searching: false,
        selectedEleve: null,
        selectedEleveId: '',
        selectedNiveau: '',
        selectedClasse: '',
        availableClasses: [],
        montantTotal: 0,

        async searchEleves() {
            // Recherche dès le premier caractère
            if (this.searchTerm.length < 1) {
                this.showResults = false;
                this.results = [];
                return;
            }

            this.searching = true;

            try {
                const response = await fetch(`/inscriptions/search?q=${encodeURIComponent(this.searchTerm)}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    this.results = await response.json();
                    this.showResults = true;
                } else {
                    console.error('Erreur de réponse:', response.status);
                    this.results = [];
                    this.showResults = false;
                }
            } catch (error) {
                console.error('Erreur lors de la recherche:', error);
                this.results = [];
                this.showResults = false;
            } finally {
                this.searching = false;
            }
        },

        selectEleve(result) {
            // Vérifier si l'élève est déjà inscrit
            if (result.statut === 'Déjà inscrit') {
                showWarningModal(
                    'Élève déjà inscrit', 
                    `${result.data.nom} ${result.data.prenom} (INE: ${result.data.ine}) est déjà inscrit cette année. Vous pouvez continuer pour voir les détails, mais une nouvelle inscription sera refusée.`
                );
            }
            
            this.selectedEleve = result.data;
            this.selectedEleveId = result.data.id;
            this.searchTerm = result.data.nom + ' ' + result.data.prenom;
            this.showResults = false;
        },

        loadClasses() {
            if (!this.selectedNiveau) return;
            
            const niveaux = @json($niveaux ?? []);
            const niveau = niveaux.find(n => n.id == this.selectedNiveau);
            this.availableClasses = niveau ? niveau.classes : [];
            this.selectedClasse = '';
        },

        async loadFrais() {
            if (!this.selectedNiveau) return;

            try {
                const response = await fetch(`/inscriptions/frais-niveau/${this.selectedNiveau}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (response.ok) {
                    const data = await response.json();
                    this.montantTotal = data.total;
                }
            } catch (error) {
                console.error('Erreur lors du chargement des frais:', error);
            }
        },

        confirmerInscription() {
            // Vérifications simples
            if (!this.selectedEleve || !this.selectedNiveau || !this.selectedClasse) {
                return;
            }

            const montantPaye = parseFloat(document.querySelector('input[name="montant_paye"]').value) || 0;
            
            // Validation du montant payé
            if (montantPaye > this.montantTotal) {
                showNotification('Le montant payé ne peut pas dépasser le montant total des frais (' + this.montantTotal + ' FCFA)', 'error');
                return;
            }

            if (montantPaye < 0) {
                showNotification('Le montant payé ne peut pas être négatif', 'error');
                return;
            }

            const message = `Élève: ${this.selectedEleve.nom} ${this.selectedEleve.prenom}
INE: ${this.selectedEleve.ine}
Montant total: ${this.montantTotal} FCFA
Montant payé: ${montantPaye} FCFA
Reste à payer: ${this.montantTotal - montantPaye} FCFA`;

            showConfirmationModal(
                'Confirmer l\'inscription', 
                message, 
                () => {
                    document.querySelector('form[x-data]').submit();
                }
            );
        }
    }
}

function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function editPreInscription(data) {
    // Remplir les champs du formulaire
    document.getElementById('edit_nom').value = data.nom || '';
    document.getElementById('edit_prenom').value = data.prenom || '';
    document.getElementById('edit_ine').value = data.ine || '';
    document.getElementById('edit_date_naissance').value = data.date_naissance || '';
    document.getElementById('edit_lieu_naissance').value = data.lieu_naissance || '';
    document.getElementById('edit_adresse').value = data.adresse || '';
    document.getElementById('edit_contact').value = data.contact || '';
    document.getElementById('edit_tuteur').value = data.tuteur || '';
    document.getElementById('edit_etablissement_origine').value = data.etablissement_origine || '';
    
    // Mettre à jour l'action du formulaire
    document.getElementById('editPreInscriptionForm').action = `/inscriptions/pre-inscription/${data.id}`;
    
    // Ouvrir la modale
    openModal('editPreInscriptionModal');
}

function editInscription(data) {
    // Remplir les informations de l'élève (lecture seule)
    document.getElementById('edit_inscription_nom_complet').textContent = data.eleve.nom_complet;
    document.getElementById('edit_inscription_ine').textContent = data.eleve.ine;
    document.getElementById('edit_inscription_contact').textContent = data.eleve.contact || 'Pas de contact';
    document.getElementById('edit_inscription_date_naissance').textContent = data.eleve.date_naissance;
    
    // Remplir les champs modifiables
    document.getElementById('edit_inscription_niveau_id').value = data.niveau_id;
    document.getElementById('edit_inscription_montant_total').value = data.montant_total;
    document.getElementById('edit_inscription_montant_paye').value = data.montant_paye;
    document.getElementById('edit_inscription_mode_paiement').value = data.mode_paiement;
    document.getElementById('edit_inscription_statut').value = data.statut;
    document.getElementById('edit_inscription_remarques').value = data.remarques;
    
    // Charger les classes du niveau sélectionné
    loadClassesForEdit(data.niveau_id, data.classe_id);
    
    // Charger les frais pour le niveau (pour cohérence avec création)
    loadFraisForEdit(data.niveau_id);
    
    // Mettre à jour l'action du formulaire
    document.getElementById('editInscriptionForm').action = `/inscriptions/inscription/${data.id}`;
    
    // Ouvrir la modale
    openModal('editInscriptionModal');
}

async function loadClassesForEdit(niveauId, selectedClasseId = null) {
    const classeSelect = document.getElementById('edit_inscription_classe_id');
    
    // Vider le select
    classeSelect.innerHTML = '<option value="">Sélectionner une classe</option>';
    
    if (!niveauId) return;
    
    try {
        // Récupérer les classes depuis les données déjà disponibles
        const niveaux = @json($niveaux ?? []);
        const niveau = niveaux.find(n => n.id == niveauId);
        
        if (niveau && niveau.classes) {
            niveau.classes.forEach(classe => {
                const option = document.createElement('option');
                option.value = classe.id;
                option.textContent = classe.nom;
                if (classe.id == selectedClasseId) {
                    option.selected = true;
                }
                classeSelect.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Erreur lors du chargement des classes:', error);
    }
}

// Événement pour charger les classes quand le niveau change dans la modale d'édition
document.addEventListener('DOMContentLoaded', function() {
    const niveauSelect = document.getElementById('edit_inscription_niveau_id');
    if (niveauSelect) {
        niveauSelect.addEventListener('change', function() {
            loadClassesForEdit(this.value);
        });
    }
});

// Variables globales pour la confirmation d'action
let currentAction = null;
let currentInscriptionId = null;

function confirmAnnulerInscription(inscriptionId, nomEleve) {
    currentAction = 'annuler';
    currentInscriptionId = inscriptionId;
    
    // Configurer la modale
    document.getElementById('confirmTitle').textContent = 'Annuler l\'inscription';
    document.getElementById('confirmIcon').className = 'fas fa-undo text-orange-500 text-3xl';
    document.getElementById('confirmMessage').textContent = 'Êtes-vous sûr de vouloir annuler cette inscription ?';
    document.getElementById('confirmDetails').textContent = 
        `L'élève ${nomEleve} sera remis en pré-inscription. Cette action peut être annulée en refinalisant l'inscription.`;
    
    const confirmButton = document.getElementById('confirmButton');
    confirmButton.textContent = 'Annuler l\'inscription';
    confirmButton.className = 'px-4 py-2 text-sm font-medium text-white bg-orange-600 rounded-md hover:bg-orange-700';
    
    openModal('confirmActionModal');
}

function confirmSupprimerInscription(inscriptionId, nomEleve) {
    currentAction = 'supprimer';
    currentInscriptionId = inscriptionId;
    
    // Configurer la modale
    document.getElementById('confirmTitle').textContent = 'Supprimer l\'inscription';
    document.getElementById('confirmIcon').className = 'fas fa-trash text-red-500 text-3xl';
    document.getElementById('confirmMessage').textContent = 'Êtes-vous sûr de vouloir supprimer définitivement cette inscription ?';
    document.getElementById('confirmDetails').textContent = 
        `L'inscription de ${nomEleve} sera supprimée définitivement. Cette action est irréversible. La pré-inscription sera conservée.`;
    
    const confirmButton = document.getElementById('confirmButton');
    confirmButton.textContent = 'Supprimer définitivement';
    confirmButton.className = 'px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700';
    
    openModal('confirmActionModal');
}

function executeAction() {
    if (!currentAction || !currentInscriptionId) return;
    
    let form = document.createElement('form');
    form.method = 'POST';
    form.style.display = 'none';
    
    // Token CSRF
    let csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    form.appendChild(csrfInput);
    
    if (currentAction === 'annuler') {
        form.action = `/inscriptions/inscription/${currentInscriptionId}/annuler`;
    } else if (currentAction === 'supprimer') {
        form.action = `/inscriptions/inscription/${currentInscriptionId}`;
        
        // Méthode DELETE
        let methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
    }
    
    document.body.appendChild(form);
    form.submit();
}

// Initialiser les graphiques quand l'onglet rapports est affiché
document.addEventListener('DOMContentLoaded', function() {
    // Charger Chart.js depuis CDN
    const script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
    script.onload = function() {
        initializeCharts();
    };
    document.head.appendChild(script);
});

function initializeCharts() {
    // Vérifier que les éléments existent avant d'initialiser les graphiques
    const paiementCanvas = document.getElementById('paiementChart');
    if (paiementCanvas) {
        const paiementCtx = paiementCanvas.getContext('2d');
        new Chart(paiementCtx, {
        type: 'doughnut',
        data: {
            labels: ['Paiements complets', 'Paiements partiels'],
            datasets: [{
                data: [{{ $statistiques['paiements_complets'] ?? 0 }}, {{ $statistiques['paiements_partiels'] ?? 0 }}],
                backgroundColor: ['#10B981', '#F59E0B'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
        });
    }

    // Graphique modes de paiement
    const modePaiementCanvas = document.getElementById('modePaiementChart');
    if (modePaiementCanvas) {
        const modePaiementCtx = modePaiementCanvas.getContext('2d');
        const modesPaiement = @json($graphiques['modes_paiement'] ?? []);
        
        new Chart(modePaiementCtx, {
        type: 'pie',
        data: {
            labels: Object.keys(modesPaiement).map(mode => mode.replace('_', ' ').toUpperCase()),
            datasets: [{
                data: Object.values(modesPaiement),
                backgroundColor: ['#3B82F6', '#EF4444', '#8B5CF6', '#06B6D4'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
        });
    }

    // Graphique évolution des inscriptions (derniers 7 jours)
    const evolutionCanvas = document.getElementById('evolutionChart');
    if (evolutionCanvas) {
        const evolutionCtx = evolutionCanvas.getContext('2d');
        const evolutionData = @json($graphiques['evolution_7_jours'] ?? []);    // Créer un tableau des 7 derniers jours
    const derniersSept = [];
    const donneesEvolution = [];
    for (let i = 6; i >= 0; i--) {
        const date = new Date();
        date.setDate(date.getDate() - i);
        const dateStr = date.toISOString().split('T')[0];
        derniersSept.push(date.toLocaleDateString('fr-FR', { month: 'short', day: 'numeric' }));
        donneesEvolution.push(evolutionData[dateStr] || 0);
    }

    new Chart(evolutionCtx, {
        type: 'line',
        data: {
            labels: derniersSept,
            datasets: [{
                label: 'Inscriptions par jour',
                data: donneesEvolution,
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    }
}

// Fonction pour afficher l'aperçu de la liste d'appel
function showListeAppelPreview() {
    // Récupérer les valeurs des filtres
    const searchAdmin = document.getElementById('search_admin').value;
    const classeAdmin = document.getElementById('classe_admin').value;
    
    // Construire l'URL avec les paramètres
    let url = '{{ route("inscriptions.liste-appel") }}';
    const params = new URLSearchParams();
    
    if (searchAdmin) {
        params.append('search_admin', searchAdmin);
    }
    
    if (classeAdmin) {
        params.append('classe_admin', classeAdmin);
    }
    
    if (params.toString()) {
        url += '?' + params.toString();
    }
    
    // Ouvrir l'aperçu dans un nouvel onglet
    window.open(url, '_blank');
}

// Fonction pour générer le PDF de la liste d'appel
function generateAdministrativeListPdf() {
    // Récupérer les valeurs des filtres
    const searchAdmin = document.getElementById('search_admin').value;
    const classeAdmin = document.getElementById('classe_admin').value;
    
    // Construire l'URL avec les paramètres
    let url = '{{ route("inscriptions.liste-administrative.pdf") }}';
    const params = new URLSearchParams();
    
    if (searchAdmin) {
        params.append('search_admin', searchAdmin);
    }
    
    if (classeAdmin) {
        params.append('classe_admin', classeAdmin);
    }
    
    if (params.toString()) {
        url += '?' + params.toString();
    }
    
    // Ouvrir le PDF dans un nouvel onglet
    window.open(url, '_blank');
}

// Fonction pour charger les frais lors de l'édition
async function loadFraisForEdit(niveauId) {
    if (!niveauId) return;
    
    try {
        const response = await fetch(`/inscriptions/frais-niveau/${niveauId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (response.ok) {
            const data = await response.json();
            document.getElementById('edit_inscription_montant_total').value = data.total;
        }
    } catch (error) {
        console.error('Erreur lors du chargement des frais:', error);
    }
}

// Fonction pour valider le montant payé lors de l'édition
function validateMontantPayeEdit() {
    const montantTotal = parseFloat(document.getElementById('edit_inscription_montant_total').value) || 0;
    const montantPaye = parseFloat(document.getElementById('edit_inscription_montant_paye').value) || 0;
    const errorElement = document.getElementById('edit_montant_error');
    
    if (montantPaye > montantTotal) {
        errorElement.style.display = 'block';
        document.getElementById('edit_inscription_montant_paye').value = montantTotal;
    } else {
        errorElement.style.display = 'none';
    }
}

// Fonction pour appliquer automatiquement les filtres
function appliquerFiltres() {
    // Afficher un indicateur de chargement
    showLoadingIndicator();
    
    // Récupérer les valeurs des filtres
    const searchEleves = document.getElementById('search_eleves')?.value || '';
    const filterAnnee = document.getElementById('filter_annee')?.value || '';
    const filterNiveau = document.getElementById('filter_niveau')?.value || '';
    const filterClasse = document.getElementById('filter_classe')?.value || '';
    const filterStatut = document.getElementById('filter_statut')?.value || '';
    
    // Construire l'URL avec les paramètres
    const params = new URLSearchParams();
    params.append('tab', 'liste-eleves');
    if (searchEleves) params.append('search_eleves', searchEleves);
    if (filterAnnee) params.append('filter_annee', filterAnnee);
    if (filterNiveau) params.append('filter_niveau', filterNiveau);
    if (filterClasse) params.append('filter_classe', filterClasse);
    if (filterStatut) params.append('filter_statut', filterStatut);
    
    // Naviguer vers la nouvelle URL
    window.location.href = `{{ route('inscriptions.index') }}?${params.toString()}`;
}

// Fonction pour afficher un indicateur de chargement
function showLoadingIndicator() {
    const tableContainer = document.querySelector('.bg-white.rounded-lg.shadow-sm.border');
    if (tableContainer) {
        // Créer un overlay de chargement
        const loadingOverlay = document.createElement('div');
        loadingOverlay.className = 'absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10';
        loadingOverlay.innerHTML = `
            <div class="flex items-center space-x-2 text-blue-600">
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Filtrage en cours...</span>
            </div>
        `;
        
        // Positionner le conteneur relativement et ajouter l'overlay
        tableContainer.style.position = 'relative';
        tableContainer.appendChild(loadingOverlay);
    }
}

// Fonction pour réinitialiser les filtres
function reinitialiserFiltres() {
    window.location.href = `{{ route('inscriptions.index') }}?tab=liste-eleves`;
}

// Timers pour éviter les rechargements trop fréquents
let searchTimer;
let filterTimer;

// Fonction pour appliquer les filtres avec délai
function appliquerFiltresAvecDelai(delai = 300) {
    clearTimeout(filterTimer);
    filterTimer = setTimeout(() => {
        appliquerFiltres();
    }, delai);
}

// Initialiser les écouteurs d'événements pour les filtres dynamiques
document.addEventListener('DOMContentLoaded', function() {
    // Recherche automatique avec délai plus long
    const searchInput = document.getElementById('search_eleves');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(appliquerFiltres, 800); // Délai de 800ms pour la recherche
        });
    }
    
    // Filtres select - avec petit délai pour éviter les changements accidentels
    const selectFilters = ['filter_annee', 'filter_niveau', 'filter_classe', 'filter_statut'];
    selectFilters.forEach(filterId => {
        const selectElement = document.getElementById(filterId);
        if (selectElement) {
            selectElement.addEventListener('change', () => appliquerFiltresAvecDelai(200));
        }
    });
});

// Fonction pour exporter les inscriptions
function exportInscriptions() {
    // Récupérer les valeurs des filtres
    const searchEleves = document.getElementById('search_eleves')?.value || '';
    const filterAnnee = document.getElementById('filter_annee')?.value || '';
    const filterNiveau = document.getElementById('filter_niveau')?.value || '';
    const filterClasse = document.getElementById('filter_classe')?.value || '';
    const filterStatut = document.getElementById('filter_statut')?.value || '';
    
    // Construire l'URL avec les paramètres
    const params = new URLSearchParams();
    if (searchEleves) params.append('search_eleves', searchEleves);
    if (filterAnnee) params.append('filter_annee', filterAnnee);
    if (filterNiveau) params.append('filter_niveau', filterNiveau);
    if (filterClasse) params.append('filter_classe', filterClasse);
    if (filterStatut) params.append('filter_statut', filterStatut);
    
    // Rediriger vers l'export
    const exportUrl = `{{ route('inscriptions.export-inscriptions') }}?${params.toString()}`;
    window.open(exportUrl, '_blank');
}

// ===== NOUVELLES FONCTIONS POUR FINALISER ET ANNULER INSCRIPTIONS =====

// Fonction pour finaliser une inscription
function finaliserInscription(preInscriptionId) {
    // Récupérer les données de la pré-inscription depuis le DOM
    const row = event.target.closest('tr');
    const nomElement = row.querySelector('p.font-medium.text-gray-900');
    const ineElement = row.querySelector('p.text-sm.text-gray-500');
    
    if (!nomElement || !ineElement) {
        alert('Erreur: Impossible de récupérer les informations de l\'élève');
        return;
    }
    
    const nomComplet = nomElement.textContent.trim();
    const ineText = ineElement.textContent.trim();
    const ine = ineText.split('•')[0].trim(); // Récupérer seulement l'INE
    const dateNaissance = ineText.includes('•') ? ineText.split('•')[1].trim() : 'Non renseigné';
    
    // Récupérer le contact depuis le bouton d'appel s'il existe
    const contactButton = row.querySelector('a[href^="tel:"]');
    const contact = contactButton ? contactButton.getAttribute('href').replace('tel:', '') : 'Non renseigné';
    
    // Remplir la modale
    document.getElementById('finaliser_pre_inscription_id').value = preInscriptionId;
    document.getElementById('finaliser_nom_complet').textContent = nomComplet;
    document.getElementById('finaliser_ine').textContent = ine;
    document.getElementById('finaliser_contact').textContent = contact;
    document.getElementById('finaliser_date_naissance').textContent = dateNaissance;
    
    // Réinitialiser les champs du formulaire
    document.getElementById('finaliser_niveau_id').value = '';
    document.getElementById('finaliser_classe_id').value = '';
    document.getElementById('finaliser_montant_total').value = '';
    document.getElementById('finaliser_montant_paye').value = '';
    document.getElementById('finaliser_mode_paiement').value = '';
    
    // Ouvrir la modale
    openModal('finaliserInscriptionModal');
}

// Fonction pour annuler une inscription
function annulerInscription(inscriptionId, nomEleve) {
    // Remplir les informations dans la modale
    document.getElementById('annuler_nom_eleve').textContent = nomEleve;
    
    // Mettre à jour l'action du formulaire
    const form = document.getElementById('annulerInscriptionForm');
    form.action = `/inscriptions/inscription/${inscriptionId}/annuler`;
    
    // Ouvrir la modale
    openModal('annulerInscriptionModal');
}

// Gestionnaire pour le changement de niveau dans la modale de finalisation
document.addEventListener('DOMContentLoaded', function() {
    const finaliserNiveauSelect = document.getElementById('finaliser_niveau_id');
    if (finaliserNiveauSelect) {
        finaliserNiveauSelect.addEventListener('change', function() {
            loadClassesForFinaliser(this.value);
            loadFraisForFinaliser(this.value);
        });
    }
    
    // Validation des montants
    const montantTotalInput = document.getElementById('finaliser_montant_total');
    const montantPayeInput = document.getElementById('finaliser_montant_paye');
    
    if (montantTotalInput && montantPayeInput) {
        montantPayeInput.addEventListener('input', function() {
            const montantTotal = parseFloat(montantTotalInput.value) || 0;
            const montantPaye = parseFloat(this.value) || 0;
            
            if (montantPaye > montantTotal) {
                this.value = montantTotal;
                alert('Le montant payé ne peut pas être supérieur au montant total (' + montantTotal.toLocaleString() + ' FCFA)');
            }
        });
        
        // Valider lors de la soumission du formulaire
        const form = document.getElementById('finaliserInscriptionForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                const montantTotal = parseFloat(montantTotalInput.value) || 0;
                const niveauSelect = document.getElementById('finaliser_niveau_id');
                
                if (montantTotal <= 0) {
                    e.preventDefault();
                    alert('Veuillez sélectionner un niveau pour déterminer les frais d\'inscription');
                    niveauSelect.focus();
                    return false;
                }
            });
        }
    }
});

// Fonction pour charger les classes selon le niveau sélectionné (pour finalisation)
async function loadClassesForFinaliser(niveauId, selectedClasseId = null) {
    const classeSelect = document.getElementById('finaliser_classe_id');
    
    // Vider les options existantes
    classeSelect.innerHTML = '<option value="">-- Choisir une classe --</option>';
    
    if (!niveauId) return;
    
    try {
        const niveaux = @json($niveaux);
        const niveau = niveaux.find(n => n.id == niveauId);
        
        if (niveau && niveau.classes) {
            niveau.classes.forEach(classe => {
                const option = document.createElement('option');
                option.value = classe.id;
                option.textContent = classe.nom;
                if (classe.id == selectedClasseId) {
                    option.selected = true;
                }
                classeSelect.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Erreur lors du chargement des classes:', error);
    }
}

// Fonction pour charger les frais selon le niveau sélectionné (pour finalisation)
async function loadFraisForFinaliser(niveauId) {
    const montantTotalInput = document.getElementById('finaliser_montant_total');
    const montantPayeInput = document.getElementById('finaliser_montant_paye');
    
    if (!niveauId) {
        montantTotalInput.value = '';
        montantTotalInput.placeholder = 'Sélectionnez un niveau';
        montantPayeInput.value = '';
        return;
    }
    
    try {
        // Afficher un indicateur de chargement
        montantTotalInput.placeholder = 'Chargement...';
        
        const response = await fetch(`/inscriptions/frais-niveau/${niveauId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            montantTotalInput.value = data.total;
            montantTotalInput.placeholder = '';
            
            // Suggérer automatiquement le montant total comme montant payé si le champ est vide
            if (!montantPayeInput.value) {
                montantPayeInput.value = data.total;
            }
            
            // Mettre à jour la limite max du montant payé
            montantPayeInput.setAttribute('max', data.total);
            
        } else {
            console.error('Erreur lors du chargement des frais');
            montantTotalInput.placeholder = 'Erreur de chargement';
        }
    } catch (error) {
        console.error('Erreur lors du chargement des frais:', error);
        montantTotalInput.placeholder = 'Erreur de chargement';
    }
}
</script>

<!-- Modal Finaliser Inscription -->
<div id="finaliserInscriptionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Finaliser l'inscription</h3>
                    <button onclick="closeModal('finaliserInscriptionModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <form method="POST" action="{{ route('inscriptions.finaliser') }}" id="finaliserInscriptionForm" class="p-6">
                @csrf
                <input type="hidden" name="pre_inscription_id" id="finaliser_pre_inscription_id" value="">
                
                <!-- Informations de l'élève -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h4 class="font-medium text-gray-900 mb-3">Informations de l'élève</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Nom complet :</span>
                            <span class="font-medium text-gray-900" id="finaliser_nom_complet"></span>
                        </div>
                        <div>
                            <span class="text-gray-600">INE :</span>
                            <span class="font-medium text-gray-900" id="finaliser_ine"></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Contact :</span>
                            <span class="font-medium text-gray-900" id="finaliser_contact"></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Date de naissance :</span>
                            <span class="font-medium text-gray-900" id="finaliser_date_naissance"></span>
                        </div>
                    </div>
                </div>

                <!-- Choix niveau et classe -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="finaliser_niveau_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Niveau *
                            <span class="text-xs text-blue-600">Les frais se chargeront automatiquement</span>
                        </label>
                        <select name="niveau_id" id="finaliser_niveau_id" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Choisir un niveau --</option>
                            @foreach($niveaux as $niveau)
                                <option value="{{ $niveau->id }}">{{ $niveau->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="finaliser_classe_id" class="block text-sm font-medium text-gray-700 mb-2">Classe *</label>
                        <select name="classe_id" id="finaliser_classe_id" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Choisir une classe --</option>
                        </select>
                    </div>
                </div>

                <!-- Informations financières -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="finaliser_montant_total" class="block text-sm font-medium text-gray-700 mb-2">
                            Montant total (frais d'inscription) *
                            <span class="text-xs text-gray-500">Automatique selon le niveau</span>
                        </label>
                        <input type="number" name="montant_total" id="finaliser_montant_total" required readonly
                               class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Sélectionnez un niveau">
                    </div>
                    
                    <div>
                        <label for="finaliser_montant_paye" class="block text-sm font-medium text-gray-700 mb-2">Montant payé (FCFA) *</label>
                        <input type="number" name="montant_paye" id="finaliser_montant_paye" required min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Ex: 75000">
                    </div>
                </div>

                <div>
                    <label for="finaliser_mode_paiement" class="block text-sm font-medium text-gray-700 mb-2">Mode de paiement *</label>
                    <select name="mode_paiement" id="finaliser_mode_paiement" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Choisir le mode de paiement --</option>
                        <option value="orange_money">Orange Money</option>
                        <option value="wave">Wave</option>
                        <option value="free_money">Free Money</option>
                        <option value="billetage">Billetage</option>
                    </select>
                </div>

                <!-- Boutons -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                    <button type="button" onclick="closeModal('finaliserInscriptionModal')" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                        <i class="fas fa-check mr-2"></i>Finaliser l'inscription
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Annuler Inscription -->
<div id="annulerInscriptionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Annuler l'inscription</h3>
                    <button onclick="closeModal('annulerInscriptionModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-lg font-medium text-gray-900">Confirmer l'annulation</h4>
                        <p class="text-sm text-gray-600 mt-1">
                            Êtes-vous sûr de vouloir annuler l'inscription de <strong id="annuler_nom_eleve"></strong> ?
                        </p>
                    </div>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-yellow-600"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">
                                Que va-t-il se passer ?
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>L'inscription sera supprimée définitivement</li>
                                    <li>L'élève sera remis en pré-inscription</li>
                                    <li>Les informations de paiement seront perdues</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <form method="POST" id="annulerInscriptionForm">
                    @csrf
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('annulerInscriptionModal')" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                            Annuler
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                            <i class="fas fa-times mr-2"></i>Confirmer l'annulation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection