@extends('layouts.app')

@section('title', 'Aperçu Export Mensualités')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- En-tête avec boutons d'action -->
    <div class="flex items-center justify-between mb-6 print:hidden">
        <div>
            <h1 class="text-lg font-bold text-gray-900">Aperçu Export Mensualités</h1>
            <p class="text-sm text-gray-600">{{ $anneeActive->nom }} - {{ count($paiements) }} transaction(s)</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
                <i class="fas fa-print mr-2"></i>Imprimer
            </button>
            <button onclick="downloadPDF()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center">
                <i class="fas fa-download mr-2"></i>Télécharger PDF
            </button>
            <a href="{{ route('mensualites.index', ['tab' => 'historique']) }}" 
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
        <div class="bg-green-600 text-white px-4 py-3 flex justify-between items-center">
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
                <h1 class="text-xl font-bold">RAPPORT MENSUALITÉS</h1>
                <p class="text-sm">{{ $anneeActive->nom }}</p>
            </div>
            
            <div class="text-right text-xs">
                Généré le<br>{{ date('d/m/Y à H:i') }}
            </div>
        </div>

        <!-- Filtres appliqués -->
        @if(!empty($filtres) && (isset($filtres['filter_classe']) || isset($filtres['filter_mois']) || isset($filtres['filter_statut']) || isset($filtres['filter_periode']) || isset($filtres['filter_annee'])))
        <div class="bg-gray-50 px-4 py-2 border-b text-xs">
            <span class="font-bold text-green-600">Filtres:</span>
            @if(isset($filtres['filter_annee']) && $filtres['filter_annee'])
                <span class="ml-2">Année: {{ \App\Models\AnneeScolaire::find($filtres['filter_annee'])->nom ?? $filtres['filter_annee'] }}</span>
            @endif
            @if(isset($filtres['filter_classe']) && $filtres['filter_classe'])
                <span class="ml-2">Classe: {{ \App\Models\Classe::find($filtres['filter_classe'])->nom ?? $filtres['filter_classe'] }}</span>
            @endif
            @if(isset($filtres['filter_mois']) && $filtres['filter_mois'])
                <span class="ml-2">Mois: {{ \App\Models\Mensualite::MOIS[$filtres['filter_mois']] ?? ucfirst($filtres['filter_mois']) }}</span>
            @endif
            @if(isset($filtres['filter_statut']) && $filtres['filter_statut'])
                <span class="ml-2">Statut: 
                    @if($filtres['filter_statut'] === 'paye') Payés
                    @elseif($filtres['filter_statut'] === 'complet') Complets
                    @elseif($filtres['filter_statut'] === 'partiel') Partiels  
                    @elseif($filtres['filter_statut'] === 'impaye') Impayés
                    @else {{ ucfirst($filtres['filter_statut']) }}
                    @endif
                </span>
            @endif
            @if(isset($filtres['filter_periode']) && $filtres['filter_periode'])
                <span class="ml-2">Période: {{ $filtres['filter_periode'] }} derniers jours</span>
            @endif
        </div>
        @endif

        <!-- Statistiques compactes -->
        <div class="bg-blue-50 px-4 py-3 border-b">
            <div class="grid grid-cols-6 gap-4 text-center text-xs">
                <div class="bg-white p-2 rounded border">
                    <div class="text-lg font-bold text-gray-800">{{ count($paiements) }}</div>
                    <div class="text-gray-600">Total</div>
                </div>
                
                <div class="bg-white p-2 rounded border">
                    <div class="text-lg font-bold text-green-600">{{ number_format($paiements->sum('montant_paye'), 0, ',', ' ') }}</div>
                    <div class="text-gray-600">Encaissé (FCFA)</div>
                </div>
                
                <div class="bg-white p-2 rounded border">
                    <div class="text-lg font-bold text-green-500">{{ $paiements->where('statut', 'complet')->count() }}</div>
                    <div class="text-gray-600">Complets</div>
                </div>
                
                <div class="bg-white p-2 rounded border">
                    <div class="text-lg font-bold text-yellow-500">{{ $paiements->where('statut', 'partiel')->count() }}</div>
                    <div class="text-gray-600">Partiels</div>
                </div>
                
                <div class="bg-white p-2 rounded border">
                    <div class="text-lg font-bold text-red-500">{{ $paiements->where('statut', 'impaye')->count() }}</div>
                    <div class="text-gray-600">Impayés</div>
                </div>
                
                <div class="bg-white p-2 rounded border">
                    <div class="text-lg font-bold text-blue-600">
                        @php
                            $totalTransactions = $paiements->count();
                            $completTransactions = $paiements->where('statut', 'complet')->count();
                            $tauxRecouvrement = $totalTransactions > 0 ? ($completTransactions / $totalTransactions) * 100 : 0;
                        @endphp
                        {{ number_format($tauxRecouvrement, 1) }}%
                    </div>
                    <div class="text-gray-600">Taux</div>
                </div>
            </div>
        </div>

        <!-- Tableau optimisé -->
        <div class="p-4">
            @forelse($paiements as $paiement)
                @if($loop->first)
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-xs">
                        <thead>
                            <tr class="bg-green-600 text-white">
                                <th class="border border-green-700 px-2 py-2 text-left w-1/6">Élève</th>
                                <th class="border border-green-700 px-2 py-2 text-left w-1/12">Classe</th>
                                <th class="border border-green-700 px-2 py-2 text-left w-1/10">INE</th>
                                <th class="border border-green-700 px-2 py-2 text-left w-1/12">Mois</th>
                                <th class="border border-green-700 px-2 py-2 text-right w-1/10">Montant Dû</th>
                                <th class="border border-green-700 px-2 py-2 text-right w-1/10">Montant Payé</th>
                                <th class="border border-green-700 px-2 py-2 text-center w-1/12">Statut</th>
                                <th class="border border-green-700 px-2 py-2 text-left w-1/12">Mode</th>
                                <th class="border border-green-700 px-2 py-2 text-left w-1/10">Date</th>
                                <th class="border border-green-700 px-2 py-2 text-left w-1/12">N° Reçu</th>
                            </tr>
                        </thead>
                        <tbody>
                @endif
                            <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                                <td class="border border-gray-300 px-2 py-2">
                                    <div class="font-bold">{{ strtoupper($paiement->inscription->preInscription->nom ?? 'N/A') }}</div>
                                    <div class="text-gray-600 text-xs">{{ ucwords(strtolower($paiement->inscription->preInscription->prenom ?? 'N/A')) }}</div>
                                </td>
                                <td class="border border-gray-300 px-2 py-2">
                                    <div class="font-bold">{{ $paiement->inscription->classe->nom ?? 'N/A' }}</div>
                                    <div class="text-gray-600 text-xs">{{ $paiement->inscription->classe->code ?? '' }}</div>
                                </td>
                                <td class="border border-gray-300 px-2 py-2">{{ $paiement->inscription->preInscription->ine ?? 'N/A' }}</td>
                                <td class="border border-gray-300 px-2 py-2">{{ $paiement->mois_libelle }}</td>
                                <td class="border border-gray-300 px-2 py-2 text-right">{{ number_format($paiement->montant_du, 0, ',', ' ') }}</td>
                                <td class="border border-gray-300 px-2 py-2 text-right font-bold text-green-600">{{ number_format($paiement->montant_paye, 0, ',', ' ') }}</td>
                                <td class="border border-gray-300 px-2 py-2 text-center">
                                    <span class="px-2 py-1 rounded text-xs font-bold
                                        @if($paiement->statut === 'complet') bg-green-100 text-green-800
                                        @elseif($paiement->statut === 'partiel') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        @if($paiement->statut === 'complet') Complet
                                        @elseif($paiement->statut === 'partiel') Partiel
                                        @else Impayé
                                        @endif
                                    </span>
                                </td>
                                <td class="border border-gray-300 px-2 py-2">{{ $paiement->mode_paiement_libelle ?? 'N/A' }}</td>
                                <td class="border border-gray-300 px-2 py-2">
                                    @if($paiement->date_paiement)
                                        <div>{{ $paiement->date_paiement->format('d/m/Y') }}</div>
                                        <div class="text-gray-600 text-xs">{{ $paiement->date_paiement->format('H:i') }}</div>
                                    @else
                                        <span class="text-red-500 text-xs">Non payé</span>
                                    @endif
                                </td>
                                <td class="border border-gray-300 px-2 py-2">
                                    @if($paiement->numero_recu)
                                        <span class="font-bold text-green-600">{{ $paiement->numero_recu }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
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
                    <div class="text-4xl mb-4">📊</div>
                    <h3 class="text-lg font-bold mb-2">Aucun paiement trouvé</h3>
                    <p>Aucun paiement ne correspond aux critères de filtrage sélectionnés.</p>
                </div>
            @endforelse
        </div>

        <!-- Pied de page compact -->
        <div class="bg-gray-50 px-4 py-3 border-t text-center text-xs text-gray-600">
            <div><strong>{{ $etablissement->nom ?? 'Système LGFP' }}</strong> - Document généré le {{ date('d/m/Y à H:i:s') }}</div>
            <div class="mt-1">
                {{ count($paiements) }} transaction(s) | Total encaissé: {{ number_format($paiements->sum('montant_paye'), 0, ',', ' ') }} FCFA
            </div>
        </div>
    </div>
</div>

<script>
function downloadPDF() {
    const params = new URLSearchParams(window.location.search);
    const downloadUrl = `{{ route('mensualites.download-export-pdf') }}?${params.toString()}`;
    window.open(downloadUrl, '_blank');
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
    .bg-green-600 {
        background-color: #f0f0f0 !important;
        color: #333 !important;
    }
    
    .bg-blue-50 {
        background-color: #f9f9f9 !important;
    }
    
    .text-green-600, .text-green-500 {
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