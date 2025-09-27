@extends('layouts.app')

@section('title', 'Aperçu Export Inscriptions')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- En-tête avec boutons d'action -->
    <div class="flex items-center justify-between mb-6 print:hidden">
        <div>
            <h1 class="text-lg font-bold text-gray-900">Aperçu Export Inscriptions</h1>
            <p class="text-sm text-gray-600">{{ $anneeActive->libelle }} - {{ count($resultats) }} élève(s)</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
                <i class="fas fa-print mr-2"></i>Imprimer
            </button>
            <button onclick="downloadPDF()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center">
                <i class="fas fa-download mr-2"></i>Télécharger PDF
            </button>
            <a href="{{ route('inscriptions.index', ['tab' => 'liste-eleves']) }}" 
               class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
        </div>
    </div>

    <!-- Document principal -->
    <div class="bg-white shadow-lg border-2 border-gray-300 rounded-lg overflow-hidden print:shadow-none print:border-none print:rounded-none">
        
        @php
            $etablissement = App\Models\Etablissement::first();
        @endphp

        <!-- En-tête compact -->
        <div class="bg-blue-600 text-white px-4 py-3 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-bold">{{ $etablissement->nom ?? 'Établissement Scolaire' }}</h3>
                <p class="text-xs opacity-90">
                    @if($etablissement)
                        {{ $etablissement->adresse }} | Tél: {{ $etablissement->telephone }}
                        @if($etablissement->ninea) | NINEA: {{ $etablissement->ninea }} @endif
                    @endif
                </p>
            </div>
            
            <div class="text-center">
                <h1 class="text-xl font-bold">
                    @if($type_export === 'non_inscrits')
                        LISTE DES ÉLÈVES NON INSCRITS
                    @else
                        LISTE DES ÉLÈVES INSCRITS
                    @endif
                </h1>
                <p class="text-sm">{{ $anneeActive->libelle }}</p>
            </div>
            
            <div class="text-right text-xs">
                Généré le<br>{{ date('d/m/Y à H:i') }}
            </div>
        </div>

        <!-- Filtres appliqués -->
        @if(!empty($filtres) && (isset($filtres['filter_classe']) || isset($filtres['filter_niveau']) || isset($filtres['filter_statut']) || isset($filtres['search_eleves']) || isset($filtres['filter_annee'])))
        <div class="bg-gray-50 px-4 py-2 border-b text-xs">
            <span class="font-bold text-blue-600">Filtres:</span>
            @if(isset($filtres['filter_annee']) && $filtres['filter_annee'])
                <span class="ml-2">Année: {{ \App\Models\AnneeScolaire::find($filtres['filter_annee'])->libelle ?? $filtres['filter_annee'] }}</span>
            @endif
            @if(isset($filtres['search_eleves']) && $filtres['search_eleves'])
                <span class="ml-2">Recherche: "{{ $filtres['search_eleves'] }}"</span>
            @endif
            @if(isset($filtres['filter_niveau']) && $filtres['filter_niveau'])
                <span class="ml-2">Niveau: {{ \App\Models\Niveau::find($filtres['filter_niveau'])->nom ?? $filtres['filter_niveau'] }}</span>
            @endif
            @if(isset($filtres['filter_classe']) && $filtres['filter_classe'])
                <span class="ml-2">Classe: {{ \App\Models\Classe::find($filtres['filter_classe'])->nom ?? $filtres['filter_classe'] }}</span>
            @endif
            @if(isset($filtres['filter_statut']) && $filtres['filter_statut'])
                <span class="ml-2">Statut: 
                    @if($filtres['filter_statut'] === 'inscrits') Inscrits (finalisés)
                    @elseif($filtres['filter_statut'] === 'non_inscrits') Non inscrits (pré-inscrits seulement)
                    @else {{ ucfirst($filtres['filter_statut']) }}
                    @endif
                </span>
            @endif
        </div>
        @endif

        <!-- Statistiques compactes -->
        <div class="bg-blue-50 px-4 py-3 border-b">
            <div class="grid grid-cols-4 gap-4 text-center text-xs">
                <div class="bg-white p-2 rounded border">
                    <div class="text-lg font-bold text-gray-800">{{ count($resultats) }}</div>
                    <div class="text-gray-600">Total Élèves</div>
                </div>
                
                @if($type_export === 'inscrits')
                <div class="bg-white p-2 rounded border">
                    <div class="text-lg font-bold text-green-600">{{ number_format($resultats->sum('montant_paye'), 0, ',', ' ') }}</div>
                    <div class="text-gray-600">Total Payé (FCFA)</div>
                </div>
                
                <div class="bg-white p-2 rounded border">
                    <div class="text-lg font-bold text-orange-600">{{ number_format($resultats->sum('montant_total'), 0, ',', ' ') }}</div>
                    <div class="text-gray-600">Total Dû (FCFA)</div>
                </div>
                
                <div class="bg-white p-2 rounded border">
                    <div class="text-lg font-bold text-blue-600">
                        @php
                            $totalDu = $resultats->sum('montant_total');
                            $totalPaye = $resultats->sum('montant_paye');
                            $tauxPaiement = $totalDu > 0 ? ($totalPaye / $totalDu) * 100 : 0;
                        @endphp
                        {{ number_format($tauxPaiement, 1) }}%
                    </div>
                    <div class="text-gray-600">Taux Paiement</div>
                </div>
                @else
                <div class="bg-white p-2 rounded border">
                    <div class="text-lg font-bold text-red-600">{{ count($resultats) }}</div>
                    <div class="text-gray-600">À Finaliser</div>
                </div>
                
                <div class="bg-white p-2 rounded border">
                    <div class="text-lg font-bold text-yellow-600">0</div>
                    <div class="text-gray-600">En Attente</div>
                </div>
                
                <div class="bg-white p-2 rounded border">
                    <div class="text-lg font-bold text-purple-600">{{ \App\Models\Inscription::where('annee_scolaire_id', $anneeActive->id)->count() }}</div>
                    <div class="text-gray-600">Déjà Inscrits</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Tableau optimisé -->
        <div class="p-4">
            @forelse($resultats as $resultat)
                @if($loop->first)
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-xs">
                        <thead>
                            <tr class="bg-blue-600 text-white">
                                <th class="border border-blue-700 px-2 py-2 text-left w-1/6">Élève</th>
                                <th class="border border-blue-700 px-2 py-2 text-left w-1/8">INE</th>
                                <th class="border border-blue-700 px-2 py-2 text-left w-1/10">Date Naissance</th>
                                <th class="border border-blue-700 px-2 py-2 text-left w-1/12">Classe</th>
                                @if($type_export === 'inscrits')
                                <th class="border border-blue-700 px-2 py-2 text-right w-1/10">Montant Dû</th>
                                <th class="border border-blue-700 px-2 py-2 text-right w-1/10">Montant Payé</th>
                                <th class="border border-blue-700 px-2 py-2 text-center w-1/12">Statut Paiement</th>
                                <th class="border border-blue-700 px-2 py-2 text-left w-1/12">Mode Paiement</th>
                                @endif
                                <th class="border border-blue-700 px-2 py-2 text-left w-1/10">Date {{ $type_export === 'inscrits' ? 'Inscription' : 'Pré-inscription' }}</th>
                                <th class="border border-blue-700 px-2 py-2 text-left w-1/12">Contact</th>
                            </tr>
                        </thead>
                        <tbody>
                @endif
                            @php
                                if ($type_export === 'non_inscrits') {
                                    $eleve = $resultat; // C'est une PreInscription
                                    $preinscription = $resultat;
                                    $inscription = null;
                                } else {
                                    $inscription = $resultat; // C'est une Inscription
                                    $preinscription = $inscription->preInscription;
                                    $eleve = $preinscription;
                                }
                            @endphp
                            <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                                <td class="border border-gray-300 px-2 py-2">
                                    <div class="font-bold">{{ strtoupper($eleve->nom ?? 'N/A') }}</div>
                                    <div class="text-gray-600 text-xs">{{ ucwords(strtolower($eleve->prenom ?? 'N/A')) }}</div>
                                </td>
                                <td class="border border-gray-300 px-2 py-2">{{ $eleve->ine ?? 'N/A' }}</td>
                                <td class="border border-gray-300 px-2 py-2">
                                    @if($eleve->date_naissance)
                                        {{ $eleve->date_naissance->format('d/m/Y') }}
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </td>
                                <td class="border border-gray-300 px-2 py-2">
                                    @if($type_export === 'inscrits')
                                        <div class="font-bold">{{ $inscription->classe->nom ?? 'N/A' }}</div>
                                        <div class="text-gray-600 text-xs">{{ $inscription->niveau->nom ?? '' }}</div>
                                    @else
                                        <div class="font-bold">{{ $eleve->classe->nom ?? 'N/A' }}</div>
                                        <div class="text-gray-600 text-xs">{{ $eleve->niveau->nom ?? '' }}</div>
                                    @endif
                                </td>
                                @if($type_export === 'inscrits')
                                <td class="border border-gray-300 px-2 py-2 text-right">{{ number_format($inscription->montant_total ?? 0, 0, ',', ' ') }}</td>
                                <td class="border border-gray-300 px-2 py-2 text-right font-bold text-green-600">{{ number_format($inscription->montant_paye ?? 0, 0, ',', ' ') }}</td>
                                <td class="border border-gray-300 px-2 py-2 text-center">
                                    <span class="px-2 py-1 rounded text-xs font-bold
                                        @if($inscription->statut_paiement === 'complet') bg-green-100 text-green-800
                                        @elseif($inscription->statut_paiement === 'partiel') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        @if($inscription->statut_paiement === 'complet') Complet
                                        @elseif($inscription->statut_paiement === 'partiel') Partiel
                                        @else Aucun
                                        @endif
                                    </span>
                                </td>
                                <td class="border border-gray-300 px-2 py-2">{{ ucfirst(str_replace('_', ' ', $inscription->mode_paiement ?? 'N/A')) }}</td>
                                @endif
                                <td class="border border-gray-300 px-2 py-2">
                                    @if($type_export === 'inscrits' && $inscription->created_at)
                                        <div>{{ $inscription->created_at->format('d/m/Y') }}</div>
                                        <div class="text-gray-600 text-xs">{{ $inscription->created_at->format('H:i') }}</div>
                                    @elseif($type_export === 'non_inscrits' && $eleve->created_at)
                                        <div>{{ $eleve->created_at->format('d/m/Y') }}</div>
                                        <div class="text-gray-600 text-xs">{{ $eleve->created_at->format('H:i') }}</div>
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </td>
                                <td class="border border-gray-300 px-2 py-2">
                                    @if($eleve->telephone)
                                        <div class="font-bold text-blue-600">{{ $eleve->telephone }}</div>
                                    @endif
                                    @if($eleve->email)
                                        <div class="text-gray-600 text-xs">{{ $eleve->email }}</div>
                                    @endif
                                    @if(!$eleve->telephone && !$eleve->email)
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </td>
                            </tr>
                @if($loop->last)
                        </tbody>
                    </table>
                </div>
                @endif
            @empty
                <div class="text-center py-12 text-gray-500">
                    <div class="text-4xl mb-4">📋</div>
                    <h3 class="text-lg font-bold mb-2">Aucun élève trouvé</h3>
                    <p>Aucun élève ne correspond aux critères de filtrage sélectionnés.</p>
                </div>
            @endforelse
        </div>

        <!-- Pied de page compact -->
        <div class="bg-gray-50 px-4 py-3 border-t text-center text-xs text-gray-600">
            <div><strong>{{ $etablissement->nom ?? 'Système LGFP' }}</strong> - Document généré le {{ date('d/m/Y à H:i:s') }}</div>
            <div class="mt-1">
                {{ count($resultats) }} élève(s) | 
                @if($type_export === 'inscrits')
                    Total payé: {{ number_format($resultats->sum('montant_paye'), 0, ',', ' ') }} FCFA
                @else
                    Élèves en attente de finalisation d'inscription
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function downloadPDF() {
    // Pour l'instant, utiliser l'impression du navigateur
    // Plus tard, on peut ajouter une route PDF dédiée
    alert('Fonctionnalité PDF en cours de développement. Utilisez "Imprimer" puis "Enregistrer au format PDF"');
}
</script>

<style>
@media print {
    body { 
        font-size: 10px !important; 
        line-height: 1.2 !important;
    }
    
    .print\\:hidden { 
        display: none !important; 
    }
    
    .print\\:shadow-none { 
        box-shadow: none !important; 
    }
    
    .print\\:border-none { 
        border: none !important; 
    }
    
    /* Cacher la navbar lors de l'impression */
    nav, header, .navbar {
        display: none !important;
    }
    
    /* Styles pour impression paysage */
    @page {
        margin: 0.8cm;
        size: A4 landscape;
    }
    
    /* Optimiser les couleurs pour l'impression */
    .bg-blue-600 {
        background-color: #f0f0f0 !important;
        color: #333 !important;
    }
    
    .bg-blue-50 {
        background-color: #f9f9f9 !important;
    }
    
    .text-blue-600, .text-green-600 {
        color: #333 !important;
    }
    
    .bg-green-100 {
        background-color: #e9e9e9 !important;
        color: #333 !important;
    }
    
    .bg-yellow-100 {
        background-color: #f0f0f0 !important;
        color: #333 !important;
    }
    
    .bg-red-100 {
        background-color: #e0e0e0 !important;
        color: #333 !important;
    }
}
</style>
@endsection