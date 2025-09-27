<!-- Section Gardiens -->
<div class="bg-white rounded-lg shadow-sm border mb-6" x-data="{ isOpen: false }">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <div class="flex items-center">
            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                <i class="fas fa-shield-alt text-gray-600 text-sm"></i>
            </div>
            <div>
                <h3 class="text-lg font-medium text-gray-900">Gardiens</h3>
                <p class="text-sm text-gray-600">{{ count($personnelParType['gardien'] ?? []) }} gardien(s)</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="openPersonnelModal('gardien')" 
                    class="bg-gray-600 text-white px-3 py-2 rounded-md hover:bg-gray-700 text-sm">
                <i class="fas fa-plus mr-1"></i>Ajouter
            </button>
            <button @click="isOpen = !isOpen" class="text-gray-400 hover:text-gray-600">
                <i :class="isOpen ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
            </button>
        </div>
    </div>
    
    <div x-show="isOpen" x-transition class="overflow-hidden">
        @if(!empty($personnelParType['gardien']))
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gardien</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rémunération</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($personnelParType['gardien'] as $gardien)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $gardien->nom_complet }}</div>
                                        <div class="text-sm text-gray-500">{{ $gardien->adresse ?: 'Adresse non renseignée' }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $gardien->telephone }}</div>
                                    @if($gardien->cni)
                                        <div class="text-sm text-gray-500">CNI: {{ $gardien->cni }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $gardien->remuneration_formatted }}
                                    </div>
                                    @if($gardien->mode_paiement === 'heure')
                                        <div class="text-xs text-gray-500">
                                            Estimé: {{ number_format($gardien->getSalaireMensuelEstime(), 0, ',', ' ') }} FCFA/mois
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statutColors = [
                                            'actif' => 'bg-green-100 text-green-800',
                                            'suspendu' => 'bg-red-100 text-red-800',
                                            'conge' => 'bg-yellow-100 text-yellow-800'
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statutColors[$gardien->statut] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($gardien->statut) }}
                                    </span>
                                    @if($gardien->date_embauche)
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ \Carbon\Carbon::parse($gardien->date_embauche)->format('d/m/Y') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button onclick="editPersonnel({{ $gardien->id }})" 
                                                class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50 transition-colors"
                                                title="Modifier">
                                            <i class="fas fa-edit text-sm"></i>
                                        </button>
                                        <button onclick="toggleStatutPersonnel({{ $gardien->id }})" 
                                                class="text-{{ $gardien->statut === 'actif' ? 'orange' : 'green' }}-600 hover:text-{{ $gardien->statut === 'actif' ? 'orange' : 'green' }}-900 p-1 rounded hover:bg-{{ $gardien->statut === 'actif' ? 'orange' : 'green' }}-50 transition-colors"
                                                title="{{ $gardien->statut === 'actif' ? 'Suspendre' : 'Activer' }}">
                                            <i class="fas fa-{{ $gardien->statut === 'actif' ? 'user-slash' : 'user-check' }} text-sm"></i>
                                        </button>
                                        <button onclick="deletePersonnel({{ $gardien->id }})" 
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
                <i class="fas fa-shield-alt text-4xl mb-4 text-gray-300"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun gardien enregistré</h3>
                <p class="text-gray-600 mb-4">Commencez par ajouter votre premier gardien</p>
                <button onclick="openPersonnelModal('gardien')" 
                        class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 text-sm transition-colors">
                    <i class="fas fa-plus mr-2"></i>Ajouter le premier gardien
                </button>
            </div>
        @endif
    </div>
</div>