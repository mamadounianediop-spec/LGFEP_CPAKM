<!-- Section Directeurs -->
<div class="bg-white rounded-lg shadow-sm border mb-6" x-data="{ isOpen: false }">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <div class="flex items-center">
            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                <i class="fas fa-user-tie text-purple-600 text-sm"></i>
            </div>
            <div>
                <h3 class="text-lg font-medium text-gray-900">Directeurs</h3>
                <p class="text-sm text-gray-600">{{ count($personnelParType['directeur'] ?? []) }} directeur(s)</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="openPersonnelModal('directeur')" 
                    class="bg-purple-600 text-white px-3 py-2 rounded-md hover:bg-purple-700 text-sm">
                <i class="fas fa-plus mr-1"></i>Ajouter
            </button>
            <button @click="isOpen = !isOpen" class="text-gray-400 hover:text-gray-600">
                <i :class="isOpen ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
            </button>
        </div>
    </div>
    
    <div x-show="isOpen" x-transition class="overflow-hidden">
        @if(!empty($personnelParType['directeur']))
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-purple-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-purple-600 uppercase tracking-wider">Directeur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-purple-600 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-purple-600 uppercase tracking-wider">Rémunération</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-purple-600 uppercase tracking-wider">Statut</th>
                            <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($personnelParType['directeur'] as $directeur)
                            <tr class="hover:bg-purple-50 transition-colors border-l-4 border-purple-500">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-user-tie text-purple-600"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ $directeur->nom_complet }}</div>
                                            <div class="text-sm text-purple-600 font-medium">Directeur de l'établissement</div>
                                            @if($directeur->adresse)
                                                <div class="text-sm text-gray-500">{{ $directeur->adresse }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-medium">{{ $directeur->telephone }}</div>
                                    @if($directeur->cni)
                                        <div class="text-sm text-gray-500">CNI: {{ $directeur->cni }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ $directeur->remuneration_formatted }}
                                    </div>
                                    @if($directeur->mode_paiement === 'heure')
                                        <div class="text-xs text-gray-500">
                                            Estimé: {{ number_format($directeur->getSalaireMensuelEstime(), 0, ',', ' ') }} FCFA/mois
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statutColors = [
                                            'actif' => 'bg-green-100 text-green-800 border-green-200',
                                            'suspendu' => 'bg-red-100 text-red-800 border-red-200',
                                            'conge' => 'bg-yellow-100 text-yellow-800 border-yellow-200'
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $statutColors[$directeur->statut] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                                        <i class="fas fa-circle text-xs mr-1"></i>
                                        {{ ucfirst($directeur->statut) }}
                                    </span>
                                    @if($directeur->date_embauche)
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ \Carbon\Carbon::parse($directeur->date_embauche)->format('d/m/Y') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button onclick="editPersonnel({{ $directeur->id }})" 
                                                class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50 transition-colors"
                                                title="Modifier">
                                            <i class="fas fa-edit text-sm"></i>
                                        </button>
                                        <button onclick="toggleStatutPersonnel({{ $directeur->id }})" 
                                                class="text-{{ $directeur->statut === 'actif' ? 'orange' : 'green' }}-600 hover:text-{{ $directeur->statut === 'actif' ? 'orange' : 'green' }}-900 p-1 rounded hover:bg-{{ $directeur->statut === 'actif' ? 'orange' : 'green' }}-50 transition-colors"
                                                title="{{ $directeur->statut === 'actif' ? 'Suspendre' : 'Activer' }}">
                                            <i class="fas fa-{{ $directeur->statut === 'actif' ? 'user-slash' : 'user-check' }} text-sm"></i>
                                        </button>
                                        <button onclick="deletePersonnel({{ $directeur->id }})" 
                                                class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50 transition-colors"
                                                title="Supprimer">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-user-tie text-4xl mb-4 text-purple-300"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun directeur nommé</h3>
                <p class="text-gray-600 mb-4">L'établissement a besoin d'un directeur pour fonctionner</p>
                <button onclick="openPersonnelModal('directeur')" 
                        class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 text-sm transition-colors">
                    <i class="fas fa-plus mr-2"></i>Nommer le directeur
                </button>
            </div>
        @endif
    </div>
</div>