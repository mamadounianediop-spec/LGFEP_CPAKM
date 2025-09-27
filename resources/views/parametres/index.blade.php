@extends('layouts.app')

@section('title', 'Configuration du Système')

@section('content')
<div class="py-6" x-data="{ activeTab: '{{ $activeTab }}' }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête amélioré -->
        <div class="mb-8">
            <div class="flex items-center space-x-3 mb-2">
                <div class="bg-indigo-100 p-2 rounded-lg">
                    <i class="fas fa-cogs text-indigo-600 text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Configuration du Système</h1>
                    <p class="text-sm text-gray-600">Gérez les paramètres de votre établissement scolaire</p>
                </div>
            </div>
        </div>

        <!-- Navigation par onglets améliorée -->
        <div class="bg-white rounded-lg shadow-sm border mb-6">
            <nav class="flex space-x-1 p-1">
                <button @click="activeTab = 'etablissement'" 
                        :class="activeTab === 'etablissement' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50'"
                        class="flex items-center px-4 py-3 rounded-md font-medium text-sm transition-all duration-200 flex-1 justify-center">
                    <i class="fas fa-university mr-2"></i>Établissement
                </button>
                <button @click="activeTab = 'niveaux'" 
                        :class="activeTab === 'niveaux' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50'"
                        class="flex items-center px-4 py-3 rounded-md font-medium text-sm transition-all duration-200 flex-1 justify-center">
                    <i class="fas fa-layer-group mr-2"></i>Niveaux & Classes
                </button>
                <button @click="activeTab = 'frais'" 
                        :class="activeTab === 'frais' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50'"
                        class="flex items-center px-4 py-3 rounded-md font-medium text-sm transition-all duration-200 flex-1 justify-center">
                    <i class="fas fa-money-bill-wave mr-2"></i>Frais de Scolarité
                </button>
                <button @click="activeTab = 'users'" 
                        :class="activeTab === 'users' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50'"
                        class="flex items-center px-4 py-3 rounded-md font-medium text-sm transition-all duration-200 flex-1 justify-center">
                    <i class="fas fa-users mr-2"></i>Utilisateurs
                </button>
                <button @click="activeTab = 'annee-scolaire'" 
                        :class="activeTab === 'annee-scolaire' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50'"
                        class="flex items-center px-4 py-3 rounded-md font-medium text-sm transition-all duration-200 flex-1 justify-center">
                    <i class="fas fa-calendar-alt mr-2"></i>Année Scolaire
                </button>
            </nav>
        </div>

        <!-- Contenu des onglets -->
        <div class="mt-6">
            <!-- Onglet Établissement -->
            <div x-show="activeTab === 'etablissement'" x-transition class="space-y-6" x-data="{ editMode: false }">
                <div class="bg-white rounded-lg shadow-sm border">
                    @if(isset($etablissement))
                        <!-- Mode Affichage -->
                        <div x-show="!editMode" class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-green-100 p-2 rounded-lg">
                                        <i class="fas fa-check-circle text-green-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">Informations de l'Établissement</h3>
                                        <p class="text-sm text-gray-600">Données configurées et validées</p>
                                    </div>
                                </div>
                                <button @click="editMode = true" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700 transition-colors">
                                    <i class="fas fa-edit mr-2"></i>Modifier
                                </button>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <dt class="text-sm font-medium text-gray-500 mb-1">Nom de l'établissement</dt>
                                        <dd class="text-lg font-semibold text-gray-900">{{ $etablissement->nom }}</dd>
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <dt class="text-sm font-medium text-gray-500 mb-1">Responsable</dt>
                                        <dd class="text-lg font-semibold text-gray-900">{{ $etablissement->responsable }}</dd>
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <dt class="text-sm font-medium text-gray-500 mb-1">Adresse</dt>
                                        <dd class="text-lg font-semibold text-gray-900">{{ $etablissement->adresse }}</dd>
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <dt class="text-sm font-medium text-gray-500 mb-1">Téléphone</dt>
                                        <dd class="text-lg font-semibold text-gray-900">{{ $etablissement->telephone }}</dd>
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <dt class="text-sm font-medium text-gray-500 mb-1">Email</dt>
                                        <dd class="text-lg font-semibold text-gray-900">{{ $etablissement->email }}</dd>
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <dt class="text-sm font-medium text-gray-500 mb-1">NINEA</dt>
                                        <dd class="text-lg font-semibold text-gray-900">{{ $etablissement->ninea ?? 'Non défini' }}</dd>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mode Édition -->
                        <div x-show="editMode" class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-orange-100 p-2 rounded-lg">
                                        <i class="fas fa-edit text-orange-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">Modifier les Informations</h3>
                                        <p class="text-sm text-gray-600">Mettez à jour les données de votre établissement</p>
                                    </div>
                                </div>
                                <button @click="editMode = false" class="text-gray-600 hover:text-gray-800">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            
                            <form action="{{ route('parametres.etablissement.update', $etablissement->id) }}" method="POST" class="space-y-4">
                                @csrf
                                @method('PUT')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="edit_nom" class="block text-sm font-medium text-gray-700 mb-1">Nom de l'établissement</label>
                                        <input type="text" name="nom" id="edit_nom" value="{{ old('nom', $etablissement->nom) }}"
                                               class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        @error('nom')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    
                                    <div>
                                        <label for="edit_responsable" class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                                        <input type="text" name="responsable" id="edit_responsable" value="{{ old('responsable', $etablissement->responsable) }}"
                                               class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        @error('responsable')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    
                                    <div>
                                        <label for="edit_adresse" class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                                        <input type="text" name="adresse" id="edit_adresse" value="{{ old('adresse', $etablissement->adresse) }}"
                                               class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        @error('adresse')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    
                                    <div>
                                        <label for="edit_telephone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                                        <input type="text" name="telephone" id="edit_telephone" value="{{ old('telephone', $etablissement->telephone) }}"
                                               class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        @error('telephone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    
                                    <div class="md:col-span-2">
                                        <label for="edit_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                        <input type="email" name="email" id="edit_email" value="{{ old('email', $etablissement->email) }}"
                                               class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    
                                    <div>
                                        <label for="edit_ninea" class="block text-sm font-medium text-gray-700 mb-1">NINEA</label>
                                        <input type="text" name="ninea" id="edit_ninea" value="{{ old('ninea', $etablissement->ninea) }}"
                                               placeholder="Ex: 123456789"
                                               class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        @error('ninea')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                                
                                <div class="flex justify-end space-x-3 pt-6 border-t">
                                    <button type="button" @click="editMode = false" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 text-sm hover:bg-gray-50">
                                        Annuler
                                    </button>
                                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">
                                        <i class="fas fa-save mr-2"></i>Enregistrer
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <!-- Formulaire de création -->
                        <div class="max-w-2xl">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Configuration initiale</h3>
                            <form action="{{ route('parametres.etablissement.store') }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="nom" class="block text-sm font-medium text-gray-700">Nom de l'établissement</label>
                                    <input type="text" name="nom" id="nom" value="{{ old('nom', 'Cours Privés Abdou Khadre Mbacké') }}"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    @error('nom')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                                
                                <div>
                                    <label for="responsable" class="block text-sm font-medium text-gray-700">Responsable</label>
                                    <input type="text" name="responsable" id="responsable" value="{{ old('responsable', 'Abdou Khadre Mbacké') }}"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    @error('responsable')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                                
                                <div>
                                    <label for="adresse" class="block text-sm font-medium text-gray-700">Adresse</label>
                                    <input type="text" name="adresse" id="adresse" value="{{ old('adresse', 'Touba, Sénégal') }}"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    @error('adresse')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                                
                                <div>
                                    <label for="telephone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                                    <input type="text" name="telephone" id="telephone" value="{{ old('telephone', '77 123 45 67') }}"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    @error('telephone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                                
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" name="email" id="email" value="{{ old('email', 'contact@cpakm.sn') }}"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                                
                                <div>
                                    <label for="ninea" class="block text-sm font-medium text-gray-700">NINEA</label>
                                    <input type="text" name="ninea" id="ninea" value="{{ old('ninea') }}"
                                           placeholder="Ex: 123456789"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    @error('ninea')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                                
                                <div class="pt-4">
                                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md text-sm hover:bg-indigo-700">
                                        <i class="fas fa-save mr-2"></i>Enregistrer
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Onglet Niveaux & Classes -->
            <div x-show="activeTab === 'niveaux'" x-transition class="space-y-6">
                <div class="bg-white rounded-lg shadow-sm border">
                    <div class="flex items-center justify-between p-6 border-b">
                        <div class="flex items-center space-x-3">
                            <div class="bg-blue-100 p-2 rounded-lg">
                                <i class="fas fa-layer-group text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Niveaux et Classes</h3>
                                <p class="text-sm text-gray-600">Organisez votre structure pédagogique</p>
                            </div>
                        </div>
                        <button onclick="openModal('addNiveauModal')" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Nouveau niveau
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Niveau</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Classes</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nb. Classes</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @if(isset($niveaux) && $niveaux->count() > 0)
                                    @foreach($niveaux as $niveau)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                                    <i class="fas fa-graduation-cap text-blue-600 text-sm"></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ $niveau->nom }}</div>
                                                    <div class="text-sm text-gray-500">{{ $niveau->description ?? 'Niveau scolaire' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="space-y-2">
                                                @if($niveau->classes->count() > 0)
                                                    @foreach($niveau->classes as $classe)
                                                        <div class="flex items-center justify-between bg-blue-50 p-2 rounded-lg">
                                                            <div class="flex items-center space-x-2">
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                    {{ $classe->nom }}
                                                                </span>
                                                                <span class="text-xs text-gray-500">{{ $classe->code }}</span>
                                                                @if($classe->effectif_max)
                                                                    <span class="text-xs text-gray-400">(Max: {{ $classe->effectif_max }})</span>
                                                                @endif
                                                            </div>
                                                            <div class="flex items-center space-x-1">
                                                                <button onclick="editClasse({{ $classe->id }}, '{{ $classe->nom }}', {{ $classe->niveau_id }}, {{ $classe->effectif_max ?? 30 }})" 
                                                                        class="text-indigo-600 hover:text-indigo-900 p-1 rounded" title="Modifier la classe">
                                                                    <i class="fas fa-edit text-xs"></i>
                                                                </button>
                                                                <button onclick="confirmDeleteClasse({{ $classe->id }})" 
                                                                        class="text-red-600 hover:text-red-900 p-1 rounded" title="Supprimer la classe">
                                                                    <i class="fas fa-trash text-xs"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <span class="text-gray-400 text-sm italic">Aucune classe</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $niveau->classes->count() > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $niveau->classes->count() }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-2">
                                                <button onclick="addClasse({{ $niveau->id }}, '{{ $niveau->nom }}')" 
                                                        class="text-blue-600 hover:text-blue-900 p-1 rounded" title="Ajouter une classe">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                                <button onclick="editNiveau({{ $niveau->id }}, '{{ $niveau->nom }}')" 
                                                        class="text-indigo-600 hover:text-indigo-900 p-1 rounded" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button onclick="confirmDeleteNiveau({{ $niveau->id }})" 
                                                        class="text-red-600 hover:text-red-900 p-1 rounded" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <div class="bg-gray-100 p-6 rounded-full mb-4">
                                                    <i class="fas fa-layer-group text-gray-400 text-3xl"></i>
                                                </div>
                                                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun niveau configuré</h3>
                                                <p class="text-gray-500 mb-4">Commencez par créer votre premier niveau scolaire</p>
                                                <button onclick="openModal('addNiveauModal')" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
                                                    <i class="fas fa-plus mr-2"></i>Créer un niveau
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Onglet Frais -->
            <div x-show="activeTab === 'frais'" x-transition class="space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Frais de Scolarité</h3>
                        <p class="mt-1 text-sm text-gray-600">Définissez les tarifs pour votre établissement</p>
                    </div>
                    <button onclick="openModal('addFraisModal')" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700">
                        <i class="fas fa-plus mr-2"></i>Nouveau frais
                    </button>
                </div>

                <div class="bg-white rounded-lg shadow-sm border">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type de frais</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Niveau</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @if(isset($frais) && $frais->count() > 0)
                                    @foreach($frais as $frais_item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ ucfirst($frais_item->type) }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $frais_item->niveau->nom ?? 'Tous' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ number_format($frais_item->montant, 0, ',', ' ') }} FCFA</td>
                                        <td class="px-4 py-3 text-sm">
                                            @if($frais_item->actif)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Actif</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactif</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm font-medium">
                                            <button onclick="editFrais({{ $frais_item->id }}, '{{ $frais_item->type }}', {{ $frais_item->montant }}, {{ $frais_item->niveau_id ?? 'null' }}, {{ $frais_item->actif ? 'true' : 'false' }})" 
                                                    class="text-indigo-600 hover:text-indigo-900 mr-2" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            <form action="{{ route('parametres.frais.destroy', $frais_item) }}" method="POST" class="inline" 
                                                  onsubmit="return confirm('Voulez-vous vraiment supprimer ce frais : {{ $frais_item->type }} - {{ $frais_item->niveau->nom ?? "Tous" }} ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                            <i class="fas fa-money-bill-wave text-2xl mb-2"></i>
                                            <p>Aucun frais configuré</p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Onglet Utilisateurs -->
            <div x-show="activeTab === 'users'" x-transition class="space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Utilisateurs</h3>
                        <p class="mt-1 text-sm text-gray-600">Gérez les accès au système</p>
                    </div>
                    <button onclick="openModal('addUserModal')" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">
                        <i class="fas fa-plus mr-2"></i>Nouvel utilisateur
                    </button>
                </div>

                <div class="bg-white rounded-lg shadow-sm border">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @if(isset($users) && $users->count() > 0)
                                    @foreach($users as $user)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $user->name }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $user->email }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ ucfirst($user->role) }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            @if($user->actif)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Actif</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactif</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm font-medium">
                                            <button class="text-indigo-600 hover:text-indigo-900 mr-2">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            @if($user->email !== 'admin@cpakm.sn')
                                                <button class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @else
                                                <span class="text-gray-400 cursor-not-allowed" title="L'administrateur ne peut pas être supprimé">
                                                    <i class="fas fa-trash"></i>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                            <i class="fas fa-users text-gray-300 text-4xl mb-4"></i>
                                            <p>Aucun utilisateur trouvé</p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Onglet Année Scolaire -->
            <div x-show="activeTab === 'annee-scolaire'" x-transition class="space-y-6">
                @include('parametres.partials.annees-scolaires')
            </div>
        </div>
    </div>
</div>

<!-- Modales -->
@include('parametres.modales')

<script>
// Fonctions JavaScript pour les modales
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function confirmDelete(url) {
    document.getElementById('deleteForm').action = url;
    openModal('deleteModal');
}

// Fonctions pour les niveaux et classes
function addClasse(niveauId, niveauNom) {
    // Remplir les données du niveau dans le modal d'ajout de classe
    document.getElementById('classe_niveau_id').value = niveauId;
    document.getElementById('niveau_nom_display').textContent = niveauNom;
    openModal('addClasseModal');
}

function editNiveau(niveauId, niveauNom) {
    // Remplir les données pour édition
    document.getElementById('edit_niveau_id').value = niveauId;
    document.getElementById('edit_niveau_nom').value = niveauNom;
    document.getElementById('editNiveauForm').action = `/parametres/niveaux/${niveauId}`;
    openModal('editNiveauModal');
}

function confirmDeleteNiveau(niveauId) {
    document.getElementById('deleteForm').action = `/parametres/niveaux/${niveauId}`;
    openModal('deleteModal');
}

function editClasse(classeId, classeNom, niveauId, effectifMax) {
    document.getElementById('edit_classe_id').value = classeId;
    document.getElementById('edit_classe_nom').value = classeNom;
    document.getElementById('edit_classe_niveau_id').value = niveauId;
    document.getElementById('edit_classe_effectif_max').value = effectifMax || '';
    document.getElementById('editClasseForm').action = `/parametres/classes/${classeId}`;
    openModal('editClasseModal');
}

function confirmDeleteClasse(classeId) {
    document.getElementById('deleteForm').action = `/parametres/classes/${classeId}`;
    openModal('deleteModal');
}

// Fonctions pour les années scolaires
function editAnnee(id, libelle, dateDebut, dateFin, description) {
    document.getElementById('edit_libelle').value = libelle;
    document.getElementById('edit_date_debut').value = dateDebut;
    document.getElementById('edit_date_fin').value = dateFin;
    document.getElementById('edit_description').value = description || '';
    document.getElementById('editAnneeForm').action = `/parametres/annees-scolaires/${id}`;
    openModal('editAnneeModal');
}

function confirmDeleteAnnee(id) {
    document.getElementById('deleteForm').action = `/parametres/annees-scolaires/${id}`;
    openModal('deleteModal');
}

function activerAnnee(id) {
    if (confirm('Voulez-vous vraiment activer cette année scolaire ? L\'année actuellement active sera désactivée.')) {
        fetch(`/parametres/annees-scolaires/${id}/activer`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de l\'activation de l\'année scolaire');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de l\'activation de l\'année scolaire');
        });
    }
}
</script>

@endsection