@extends('layouts.app')

@section('title', 'Archive État Personnel - ' . $nomMois[$mois] . ' ' . $annee)

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- En-tête avec boutons d'action -->
    <div class="flex items-center justify-between mb-6 print:hidden">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Archive État de Paiement Personnel</h1>
            <p class="text-gray-600">{{ $nomMois[$mois] }} {{ $annee }} - {{ $totaux['personnel'] }} membres</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
                <i class="fas fa-print mr-2"></i>Imprimer
            </button>
            <a href="{{ route('personnel.index') }}?tab=archives" 
               class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
        </div>
    </div>

    <!-- Document d'archive (style PDF) -->
    <div class="bg-white shadow-lg border-2 border-gray-300 print:shadow-none print:border-none" style="font-family: 'DejaVu Sans', Arial, sans-serif;">
        
        @php
            $etablissement = App\Models\Etablissement::first();
        @endphp

        <!-- Archive principale -->
        <div class="border-2 border-blue-600 p-6 mb-4 relative">
            <div class="absolute inset-0 flex items-center justify-center opacity-5 pointer-events-none">
                <div class="text-6xl font-bold text-blue-600 transform rotate-45">
                    {{ strtoupper($etablissement->nom ?? 'ÉTABLISSEMENT') }}
                </div>
            </div>
            
            <div class="relative z-10">
                <!-- En-tête -->
                <div class="flex justify-between items-center border-b-2 border-blue-600 pb-4 mb-6">
                    <div class="flex-1">
                        <div class="text-lg font-bold text-blue-600 mb-1">{{ $etablissement->nom ?? 'Nom de l\'établissement' }}</div>
                        <div class="text-xs text-gray-600 leading-tight">
                            @if($etablissement)
                                <div><strong>Adresse :</strong> {{ $etablissement->adresse }}</div>
                                <div><strong>Tél :</strong> {{ $etablissement->telephone }} | <strong>Email :</strong> {{ $etablissement->email }}</div>
                                <div><strong>NINEA :</strong> {{ $etablissement->ninea ?? 'Non défini' }}</div>
                                <div><strong>Responsable :</strong> {{ $etablissement->responsable }}</div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex-1 text-center">
                        <h1 class="text-xl font-bold text-blue-600 mb-1">ÉTAT DE PAIEMENT PERSONNEL</h1>
                        <div class="text-sm text-gray-600 font-bold">Archive - {{ $nomMois[$mois] }} {{ $annee }}</div>
                        <div class="text-sm text-gray-600 font-bold">Année Scolaire {{ $annee }}-{{ $annee + 1 }}</div>
                    </div>
                    
                    <div class="w-20 text-center">
                        <div class="w-16 h-16 border border-gray-300 bg-gray-100 flex items-center justify-center mb-1">
                            @php
                                $qrData = json_encode([
                                    'type' => 'archive_personnel',
                                    'etablissement' => $etablissement->nom ?? 'Non défini',
                                    'periode' => $nomMois[$mois] . ' ' . $annee,
                                    'personnel_count' => $totaux['personnel'],
                                    'montant_total' => $totaux['montant_brut'],
                                    'date_generation' => now()->format('d/m/Y H:i'),
                                    'archive_date' => $etats->first() && $etats->first()->date_archive ? 
                                        (is_object($etats->first()->date_archive) ? 
                                            $etats->first()->date_archive->format('d/m/Y H:i') : 
                                            date('d/m/Y H:i', strtotime($etats->first()->date_archive))) : 
                                        now()->format('d/m/Y H:i')
                                ]);
                            @endphp
                            @if(class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode'))
                                {!! QrCode::size(60)->generate($qrData) !!}
                            @else
                                <i class="fas fa-qrcode text-2xl text-gray-400"></i>
                            @endif
                        </div>
                        <div class="text-xs text-gray-600">Code QR</div>
                    </div>
                </div>
                
                <!-- Informations de synthèse -->
                <div class="mb-6">
                    <div class="text-sm font-bold text-blue-600 mb-3 border-b border-gray-300 pb-1">INFORMATIONS DE SYNTHÈSE</div>
                    <div class="grid grid-cols-5 gap-3 text-sm">
                        <div class="bg-gray-50 p-3 rounded text-center">
                            <div class="text-xs text-gray-600 mb-1">Date d'archivage</div>
                            <div class="font-bold">
                                @if($etats->first() && $etats->first()->date_archive)
                                    @if(is_object($etats->first()->date_archive))
                                        {{ $etats->first()->date_archive->format('d/m/Y H:i') }}
                                    @else
                                        {{ date('d/m/Y H:i', strtotime($etats->first()->date_archive)) }}
                                    @endif
                                @else
                                    {{ now()->format('d/m/Y H:i') }}
                                @endif
                            </div>
                        </div>
                        <div class="bg-blue-50 p-3 rounded text-center">
                            <div class="text-xs text-gray-600 mb-1">Total Primes</div>
                            <div class="font-bold text-blue-600">{{ number_format($etats->sum('primes'), 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="bg-green-50 p-3 rounded text-center">
                            <div class="text-xs text-gray-600 mb-1">Montant Total</div>
                            <div class="font-bold text-green-600">{{ number_format($totaux['montant_brut'], 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="bg-yellow-50 p-3 rounded text-center">
                            <div class="text-xs text-gray-600 mb-1">Total Retenues</div>
                            <div class="font-bold text-yellow-600">{{ number_format($totaux['total_retenues'], 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="bg-red-50 p-3 rounded text-center">
                            <div class="text-xs text-gray-600 mb-1">Total Restant</div>
                            <div class="font-bold text-red-600">{{ number_format($totaux['total_restant'], 0, ',', ' ') }} FCFA</div>
                        </div>
                    </div>
                </div>
                
                <!-- Tableau du personnel -->
                <div class="mb-6">
                    <div class="text-sm font-bold text-blue-600 mb-3 border-b border-gray-300 pb-1">DÉTAIL PAR PERSONNEL</div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs border-collapse">
                            <thead>
                                <tr class="bg-blue-50">
                                    <th class="border border-gray-300 px-2 py-2 text-left font-bold">Personnel</th>
                                    <th class="border border-gray-300 px-2 py-2 text-left font-bold">Poste</th>
                                    <th class="border border-gray-300 px-2 py-2 text-center font-bold">Mode</th>
                                    <th class="border border-gray-300 px-2 py-2 text-right font-bold">Salaire Base</th>
                                    <th class="border border-gray-300 px-2 py-2 text-right font-bold">Primes</th>
                                    <th class="border border-gray-300 px-2 py-2 text-right font-bold">Retenues</th>
                                    <th class="border border-gray-300 px-2 py-2 text-right font-bold">Net</th>
                                    <th class="border border-gray-300 px-2 py-2 text-right font-bold">Avance</th>
                                    <th class="border border-gray-300 px-2 py-2 text-right font-bold">Solde</th>
                                    <th class="border border-gray-300 px-2 py-2 text-center font-bold">État</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($etats as $etat)
                                @php
                                    // État de paiement logique
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="border border-gray-300 px-2 py-2">
                                        <div class="font-medium">{{ $etat->personnel->nom }} {{ $etat->personnel->prenom }}</div>
                                        @if($etat->personnel->mode_paiement === 'heure')
                                        <div class="text-xs text-gray-500">{{ $etat->heures_effectuees }}h × {{ number_format($etat->personnel->tarif_heure, 0, ',', ' ') }}</div>
                                        @endif
                                    </td>
                                    <td class="border border-gray-300 px-2 py-2">{{ ucfirst($etat->personnel->type_personnel) }} - {{ $etat->personnel->discipline ?? '' }}</td>
                                    <td class="border border-gray-300 px-2 py-2 text-center">
                                        <span class="px-2 py-1 bg-gray-100 rounded text-xs">{{ $etat->personnel->mode_paiement ?? 'Standard' }}</span>
                                    </td>
                                    <td class="border border-gray-300 px-2 py-2 text-right font-medium">{{ number_format($etat->salaire_base, 0, ',', ' ') }}</td>
                                    <td class="border border-gray-300 px-2 py-2 text-right text-blue-600 font-medium">{{ number_format($etat->primes, 0, ',', ' ') }}</td>
                                    <td class="border border-gray-300 px-2 py-2 text-right text-yellow-600">{{ number_format($etat->total_retenues, 0, ',', ' ') }}</td>
                                    <td class="border border-gray-300 px-2 py-2 text-right font-medium text-green-600">{{ number_format($etat->net_a_payer, 0, ',', ' ') }}</td>
                                    <td class="border border-gray-300 px-2 py-2 text-right">{{ number_format($etat->avance_donnee, 0, ',', ' ') }}</td>
                                    <td class="border border-gray-300 px-2 py-2 text-right font-medium {{ $etat->reste_a_payer > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        {{ number_format($etat->reste_a_payer, 0, ',', ' ') }}
                                    </td>
                                    <td class="border border-gray-300 px-2 py-2 text-center">
                                        @php
                                            $avance = $etat->avance_donnee;
                                            
                                            // Logique : pas d'avance = payé intégralement
                                            if ($avance == 0) {
                                                $etat_paiement = 'Payé intégralement';
                                                $class_etat = 'text-green-600 font-medium';
                                            } else {
                                                // Il y a une avance = paiement partiel
                                                $etat_paiement = 'Avec avance';
                                                $class_etat = 'text-orange-600 font-medium';
                                            }
                                        @endphp
                                        <span class="{{ $class_etat }}">{{ $etat_paiement }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-blue-100 font-bold">
                                    <td class="border border-gray-300 px-2 py-2" colspan="3">TOTAUX</td>
                                    <td class="border border-gray-300 px-2 py-2 text-right">
                                        {{ number_format($etats->sum('salaire_base'), 0, ',', ' ') }}
                                    </td>
                                    <td class="border border-gray-300 px-2 py-2 text-right">{{ number_format($etats->sum('primes'), 0, ',', ' ') }}</td>
                                    <td class="border border-gray-300 px-2 py-2 text-right">{{ number_format($totaux['total_retenues'], 0, ',', ' ') }}</td>
                                    <td class="border border-gray-300 px-2 py-2 text-right">{{ number_format($totaux['net_total'], 0, ',', ' ') }}</td>
                                    <td class="border border-gray-300 px-2 py-2 text-right">{{ number_format($totaux['total_avances'], 0, ',', ' ') }}</td>
                                    <td class="border border-gray-300 px-2 py-2 text-right">{{ number_format($totaux['total_restant'], 0, ',', ' ') }}</td>
                                    <td class="border border-gray-300 px-2 py-2 text-center">-</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                
                <!-- Signatures -->
                <div class="mt-8 pt-4 border-t border-gray-300 flex justify-between">
                    <div class="text-center w-48">
                        <div class="h-12 mb-2"></div>
                        <div class="border-t border-gray-400 text-xs text-gray-600 pt-1">Signature Directeur</div>
                    </div>
                    <div class="text-center w-48">
                        <div class="h-12 mb-2"></div>
                        <div class="border-t border-gray-400 text-xs text-gray-600 pt-1">Cachet de l'établissement</div>
                    </div>
                </div>
                
                <div class="absolute bottom-2 right-4 text-xs text-gray-500">
                    Généré le {{ now()->format('d/m/Y à H:i') }}
                </div>
            </div>
        </div>


    </div>
</div>

<style>
    @media print {
        body { 
            font-size: 12px; 
            line-height: 1.4;
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
        
        /* Style pour le résumé d'archivage */
        .border-dashed {
            border-style: dashed !important;
        }
        
        @page {
            margin: 1cm;
            size: A4 landscape;
        }
    }
</style>
@endsection