@extends('layouts.app')

@section('title', 'Liste d\'Appel des Élèves')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- En-tête avec boutons d'action -->
    <div class="flex items-center justify-between mb-6 print:hidden">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Liste d'Appel des Élèves</h1>
            <p class="text-gray-600">{{ $etablissement->nom ?? 'ÉTABLISSEMENT SCOLAIRE' }} • {{ $anneeActive->libelle ?? 'Année en cours' }}</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
                <i class="fas fa-print mr-2"></i>Imprimer
            </button>
            <a href="{{ route('inscriptions.index', ['tab' => 'liste-administrative']) }}" 
               class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
        </div>
    </div>

    <!-- Aperçu de la liste d'appel (style PDF) -->
    <div class="bg-white shadow-lg border-2 border-gray-300 print:shadow-none print:border-none" style="font-family: 'DejaVu Sans', Arial, sans-serif;">
        
        <!-- En-tête principal -->
        <div class="border-2 border-blue-600 p-6 relative">
            <div class="absolute inset-0 flex items-center justify-center opacity-5 pointer-events-none">
                <div class="text-6xl font-bold text-blue-600 transform rotate-45">
                    {{ $etablissement->nom ?? 'ÉTABLISSEMENT' }}
                </div>
            </div>
            
            <div class="relative z-10">
                <!-- En-tête -->
                <div class="flex justify-between items-center border-b-2 border-blue-600 pb-4 mb-6">
                    <div class="w-2/5 pr-4">
                        <div class="text-base font-bold text-blue-600 mb-1 leading-tight">{{ $etablissement->nom ?? 'Nom de l\'établissement' }}</div>
                        <div class="text-xs text-gray-600 leading-tight">
                            @if($etablissement)
                                <div><strong>Adresse :</strong> {{ $etablissement->adresse }}</div>
                                <div><strong>Tél :</strong> {{ $etablissement->telephone }} | <strong>Email :</strong> {{ $etablissement->email }}</div>
                                <div><strong>NINEA :</strong> {{ $etablissement->ninea ?? 'Non défini' }}</div>
                                <div><strong>Responsable :</strong> {{ $etablissement->responsable }}</div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="w-2/5 text-center px-4">
                        <h1 class="text-xl font-bold text-blue-600 mb-1">LISTE D'APPEL DES ÉLÈVES</h1>
                        <div class="text-sm text-gray-600 font-bold">Année : {{ $anneeActive->libelle ?? 'Année en cours' }}</div>
                        <div class="text-sm text-gray-600">{{ \Carbon\Carbon::now()->format('d/m/Y à H:i') }}</div>
                    </div>
                    
                    <div class="w-1/5 text-center pl-4">
                        <!-- Statistiques compactes -->
                        <div class="bg-blue-50 border border-blue-200 rounded p-2 text-xs">
                            <div class="font-bold text-blue-600">{{ $totalEleves }}</div>
                            <div class="text-gray-600">Élèves</div>
                            <div class="mt-1">
                                <span class="text-blue-600">{{ $totalGarcons }}M</span> / 
                                <span class="text-pink-600">{{ $totalFilles }}F</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Contenu principal -->
                <div class="space-y-6">
                    @if(count($inscriptionsParNiveau) > 0)
                        @foreach($inscriptionsParNiveau as $niveau => $classes)
                            <div class="mb-6">
                                @foreach($classes as $classeNom => $inscriptions)
                                    <!-- En-tête Niveau et Classe sur une ligne -->
                                    <div class="border-b-2 border-gray-600 pb-2 mb-4">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-base font-bold text-gray-800 flex items-center">
                                                <i class="fas fa-layer-group mr-2"></i>
                                                NIVEAU : {{ $niveau }} - CLASSE : {{ $classeNom }}
                                            </h3>
                                            <span class="text-sm text-gray-600 font-medium border border-gray-300 px-3 py-1 rounded">{{ count($inscriptions) }} élève(s)</span>
                                        </div>
                                    </div>

                                    <!-- Tableau des élèves -->
                                    <div class="border border-gray-200 overflow-hidden mb-6">
                                        <table class="w-full text-sm">
                                            <thead class="bg-gray-100">
                                                <tr>
                                                    <th class="px-4 py-3 text-left text-sm font-bold text-gray-700 uppercase w-12 border-r border-gray-300">N°</th>
                                                    <th class="px-4 py-3 text-left text-sm font-bold text-gray-700 uppercase w-32 border-r border-gray-300">INE</th>
                                                    <th class="px-4 py-3 text-left text-sm font-bold text-gray-700 uppercase border-r border-gray-300">PRÉNOM</th>
                                                    <th class="px-4 py-3 text-left text-sm font-bold text-gray-700 uppercase border-r border-gray-300">NOM</th>
                                                    <th class="px-4 py-3 text-center text-sm font-bold text-gray-700 uppercase w-24 border-r border-gray-300">SEXE</th>
                                                    <th class="px-4 py-3 text-left text-sm font-bold text-gray-700 uppercase w-28 border-r border-gray-300">DATE NAISSANCE</th>
                                                    <th class="px-4 py-3 text-left text-sm font-bold text-gray-700 uppercase">LIEU NAISSANCE</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($inscriptions as $index => $inscription)
                                                    <tr class="hover:bg-gray-50 border-b border-gray-200">
                                                        <td class="px-4 py-4 text-base font-bold text-gray-900 text-center border-r border-gray-200">{{ $index + 1 }}</td>
                                                        <td class="px-4 py-4 text-sm text-gray-900 font-mono font-medium border-r border-gray-200">
                                                            {{ $inscription->preInscription->ine ?? 'N/A' }}
                                                        </td>
                                                        <td class="px-4 py-4 text-base text-gray-900 font-medium border-r border-gray-200">
                                                            {{ ucwords(strtolower($inscription->preInscription->prenom ?? '')) }}
                                                        </td>
                                                        <td class="px-4 py-4 text-base text-gray-900 font-bold uppercase border-r border-gray-200">
                                                            {{ strtoupper($inscription->preInscription->nom ?? '') }}
                                                        </td>
                                                        <td class="px-4 py-4 text-sm text-gray-900 text-center border-r border-gray-200">
                                                            @if(($inscription->preInscription->sexe ?? '') === 'M')
                                                                <span class="inline-flex items-center justify-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                    Masculin
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center justify-center px-2 py-1 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                                                                    Féminin
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-4 text-sm text-gray-900 font-medium border-r border-gray-200">
                                                            {{ $inscription->preInscription->date_naissance ? \Carbon\Carbon::parse($inscription->preInscription->date_naissance)->format('d/m/Y') : 'N/A' }}
                                                        </td>
                                                        <td class="px-4 py-4 text-sm text-gray-900">
                                                            {{ $inscription->preInscription->lieu_naissance ?? 'N/A' }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-12">
                            <div class="mx-auto h-12 w-12 text-gray-400">
                                <i class="fas fa-users text-4xl"></i>
                            </div>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun élève trouvé</h3>
                            <p class="mt-1 text-sm text-gray-500">Aucun élève ne correspond aux critères sélectionnés.</p>
                        </div>
                    @endif
                </div>
                
                <!-- Date de génération -->
                <div class="mt-8 pt-4 border-t border-gray-300 text-center">
                    <div class="text-xs text-gray-500">
                        Généré le {{ now()->format('d/m/Y à H:i') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body { 
            font-size: 11px; 
            line-height: 1.3;
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
        
        table {
            font-size: 11px;
        }
        
        th, td {
            padding: 4px 6px !important;
        }
        
        tbody tr {
            height: auto !important;
        }
        
        /* Ajuster la taille du texte pour l'impression */
        .text-base {
            font-size: 12px !important;
        }
        
        .text-sm {
            font-size: 11px !important;
        }
        
        @page {
            margin: 1cm;
            size: A4 portrait;
        }
        
        /* Styles d'impression pour les en-têtes */
        .bg-gray-100 {
            background-color: #f3f4f6 !important;
        }
        
        /* Bordures plus visibles à l'impression */
        .border-b-2 {
            border-bottom-width: 2px !important;
        }
        
        .border-b {
            border-bottom-width: 1px !important;
        }
    }
</style>
@endsection