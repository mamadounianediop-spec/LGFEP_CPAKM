<!-- En-tête de l'onglet Année Scolaire -->
<div class="flex items-center justify-between">
    <div>
        <h3 class="text-lg font-semibold text-gray-900">Années Scolaires</h3>
        <p class="mt-1 text-sm text-gray-600">Gérez les années scolaires de l'établissement</p>
    </div>
    <button onclick="openModal('addAnneeModal')" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">
        <i class="fas fa-plus mr-2"></i>Nouvelle Année Scolaire
    </button>
</div>

<!-- Liste des années scolaires -->
<div class="bg-white rounded-lg shadow-sm border">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Libellé</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Période</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @if($anneesScolaires && $anneesScolaires->count() > 0)
                    @foreach($anneesScolaires as $annee)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                            {{ $annee->libelle }}
                            @if($annee->actif)
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Actuelle
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($annee->date_debut)->format('d/m/Y') }} - 
                            {{ \Carbon\Carbon::parse($annee->date_fin)->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if($annee->actif)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-pause-circle mr-1"></i>Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            {{ Str::limit($annee->description, 50) ?? 'Aucune description' }}
                        </td>
                        <td class="px-4 py-3 text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                @if(!$annee->actif)
                                    <button onclick="activerAnnee({{ $annee->id }})" 
                                            class="text-green-600 hover:text-green-900" 
                                            title="Activer cette année">
                                        <i class="fas fa-play-circle"></i>
                                    </button>
                                @endif
                                
                                <button onclick="editAnnee({{ $annee->id }}, '{{ $annee->libelle }}', '{{ $annee->date_debut }}', '{{ $annee->date_fin }}', '{{ $annee->description }}')" 
                                        class="text-indigo-600 hover:text-indigo-900" 
                                        title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                @if(!$annee->actif)
                                    <button onclick="confirmDeleteAnnee({{ $annee->id }})" 
                                            class="text-red-600 hover:text-red-900" 
                                            title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @else
                                    <span class="text-gray-400 cursor-not-allowed" title="L'année active ne peut pas être supprimée">
                                        <i class="fas fa-trash"></i>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-calendar-alt text-gray-300 text-4xl mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune année scolaire</h3>
                                <p class="text-sm text-gray-500 mb-4">Commencez par créer votre première année scolaire</p>
                                <button onclick="openModal('addAnneeModal')" 
                                        class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">
                                    <i class="fas fa-plus mr-2"></i>Créer une année scolaire
                                </button>
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<!-- Informations sur l'année active -->
@if($anneeActive)
<div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <i class="fas fa-info-circle text-blue-600"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-blue-800">Année scolaire active</h3>
            <div class="mt-1 text-sm text-blue-700">
                <p><strong>{{ $anneeActive->libelle }}</strong> - Du {{ \Carbon\Carbon::parse($anneeActive->date_debut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($anneeActive->date_fin)->format('d/m/Y') }}</p>
                @if($anneeActive->description)
                    <p class="mt-1">{{ $anneeActive->description }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endif