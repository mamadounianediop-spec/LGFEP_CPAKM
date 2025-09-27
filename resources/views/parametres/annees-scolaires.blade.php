@extends('layouts.app')

@section('title', 'Gestion des Années Scolaires')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center space-x-3 mb-2">
                        <div class="bg-blue-100 p-2 rounded-lg">
                            <i class="fas fa-calendar-alt text-blue-600 text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Gestion des Années Scolaires</h1>
                            <p class="text-sm text-gray-600">Configurez les années scolaires de l'établissement</p>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('parametres.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md text-sm hover:bg-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Retour
                    </a>
                    <button onclick="openModal('addAnneeModal')" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i>Nouvelle Année
                    </button>
                </div>
            </div>
        </div>

        <!-- Informations importantes -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-yellow-900">Informations importantes</h4>
                    <div class="mt-2 text-sm text-yellow-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Une seule année scolaire peut être active à la fois</li>
                            <li>Toutes les inscriptions et pré-inscriptions sont liées à l'année active</li>
                            <li>Il est recommandé de ne pas supprimer les années contenant des données</li>
                            <li>L'activation d'une nouvelle année désactive automatiquement l'ancienne</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des années scolaires -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Années Scolaires</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Libellé</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Période</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Données</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($anneesScolaires as $annee)
                        <tr class="hover:bg-gray-50 {{ $annee->actif ? 'bg-blue-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($annee->actif)
                                        <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $annee->libelle }}</div>
                                        @if($annee->description)
                                            <div class="text-sm text-gray-500">{{ $annee->description }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div>
                                    <div>{{ $annee->date_debut->format('d/m/Y') }} - {{ $annee->date_fin->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-400">
                                        @if($annee->isEnCours())
                                            <span class="text-green-600">En cours</span>
                                        @elseif($annee->date_debut > now())
                                            <span class="text-blue-600">Future</span>
                                        @else
                                            <span class="text-gray-600">Passée</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($annee->actif)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check mr-1"></i>Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="space-y-1">
                                    <div>Pré-inscriptions: {{ $annee->preInscriptions()->count() }}</div>
                                    <div>Inscriptions: {{ $annee->inscriptions()->count() }}</div>
                                    <div>Frais: {{ $annee->frais()->count() }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    @unless($annee->actif)
                                        <form method="POST" action="{{ route('parametres.annees-scolaires.activer', $annee) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900" title="Activer">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </form>
                                    @endunless
                                    
                                    <button onclick="editAnnee({{ json_encode([
                                        'id' => $annee->id,
                                        'libelle' => $annee->libelle,
                                        'date_debut' => $annee->date_debut->format('Y-m-d'),
                                        'date_fin' => $annee->date_fin->format('Y-m-d'),
                                        'description' => $annee->description
                                    ]) }})" class="text-blue-600 hover:text-blue-900" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    @unless($annee->actif || $annee->preInscriptions()->exists() || $annee->inscriptions()->exists() || $annee->frais()->exists())
                                        <form method="POST" action="{{ route('parametres.annees-scolaires.destroy', $annee) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette année scolaire ?')"
                                                    class="text-red-600 hover:text-red-900" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <i class="fas fa-calendar-alt text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500">Aucune année scolaire configurée</p>
                                <button onclick="openModal('addAnneeModal')" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
                                    Créer la première année
                                </button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('parametres.annees-scolaires.modals')
</div>

<script>
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function editAnnee(data) {
    document.getElementById('edit_libelle').value = data.libelle;
    document.getElementById('edit_date_debut').value = data.date_debut;
    document.getElementById('edit_date_fin').value = data.date_fin;
    document.getElementById('edit_description').value = data.description || '';
    
    document.getElementById('editAnneeForm').action = `/parametres/annees-scolaires/${data.id}`;
    
    openModal('editAnneeModal');
}
</script>
@endsection