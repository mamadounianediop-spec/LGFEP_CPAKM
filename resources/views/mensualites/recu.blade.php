@extends('layouts.app')

@section('title', 'Reçu de paiement mensualité')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- En-tête avec boutons d'action -->
    <div class="flex items-center justify-between mb-6 print:hidden">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Aperçu du reçu de paiement</h1>
            <p class="text-gray-600">{{ $mensualite->numero_recu }}</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
                <i class="fas fa-print mr-2"></i>Imprimer
            </button>
            <a href="{{ route('mensualites.index', ['tab' => 'historique']) }}" 
               class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
        </div>
    </div>

    <!-- Aperçu du reçu (style PDF) -->
    <div class="bg-white shadow-lg border-2 border-gray-300 print:shadow-none print:border-none" style="font-family: 'DejaVu Sans', Arial, sans-serif;">
        
        @php
            $etablissement = App\Models\Etablissement::first();
            $qrData = json_encode([
                'numero_recu' => $mensualite->numero_recu,
                'ine' => $mensualite->inscription->preInscription->ine,
                'nom' => $mensualite->inscription->preInscription->nom,
                'prenom' => $mensualite->inscription->preInscription->prenom,
                'classe' => $mensualite->inscription->classe->nom,
                'niveau' => $mensualite->inscription->niveau->nom,
                'mois' => $mensualite->mois_libelle,
                'montant_paye' => $mensualite->montant_paye,
                'date_paiement' => $mensualite->date_paiement->format('d/m/Y'),
                'etablissement' => $etablissement->nom ?? 'Non défini'
            ]);
        @endphp

        <!-- Premier Reçu -->
        <div class="border-2 border-green-600 p-6 mb-4 relative">
            <div class="absolute inset-0 flex items-center justify-center opacity-5 pointer-events-none">
                <div class="text-6xl font-bold text-green-600 transform rotate-45">
                    {{ $etablissement->nom ?? 'ÉTABLISSEMENT' }}
                </div>
            </div>
            
            <div class="relative z-10">
                <!-- En-tête -->
                <div class="flex justify-between items-center border-b-2 border-green-600 pb-4 mb-6">
                    <div class="flex-1">
                        <div class="text-lg font-bold text-green-600 mb-1">{{ $etablissement->nom ?? 'Nom de l\'établissement' }}</div>
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
                        <h1 class="text-xl font-bold text-green-600 mb-1">REÇU DE PAIEMENT MENSUALITÉ</h1>
                        <div class="text-sm text-gray-600 font-bold">N° {{ $mensualite->numero_recu }}</div>
                        <div class="text-sm text-gray-600 font-bold">Année : {{ $mensualite->inscription->anneeScolaire->nom }}</div>
                    </div>
                    
                    <div class="w-20 text-center">
                        <div class="w-16 h-16 border border-gray-300 bg-gray-100 flex items-center justify-center mb-1">
                            @if(class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode'))
                                {!! QrCode::size(60)->generate($qrData) !!}
                            @else
                                <i class="fas fa-qrcode text-2xl text-gray-400"></i>
                            @endif
                        </div>
                        <div class="text-xs text-gray-600">Code QR</div>
                    </div>
                </div>
                
                <!-- Contenu -->
                <div class="flex gap-8">
                    <div class="flex-2 space-y-6">
                        <!-- Informations de l'élève -->
                        <div>
                            <div class="text-sm font-bold text-green-600 mb-3 border-b border-gray-300 pb-1">INFORMATIONS DE L'ÉLÈVE</div>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <div class="text-xs text-gray-600 font-bold mb-1">Nom</div>
                                    <div class="border-b border-dotted border-gray-400 pb-1">{{ strtoupper($mensualite->inscription->preInscription->nom) }}</div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-600 font-bold mb-1">Prénom</div>
                                    <div class="border-b border-dotted border-gray-400 pb-1">{{ ucwords(strtolower($mensualite->inscription->preInscription->prenom)) }}</div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-600 font-bold mb-1">INE</div>
                                    <div class="border-b border-dotted border-gray-400 pb-1">{{ $mensualite->inscription->preInscription->ine }}</div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-600 font-bold mb-1">Date de naissance</div>
                                    <div class="border-b border-dotted border-gray-400 pb-1">{{ \Carbon\Carbon::parse($mensualite->inscription->preInscription->date_naissance)->format('d/m/Y') }}</div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-600 font-bold mb-1">Lieu de naissance</div>
                                    <div class="border-b border-dotted border-gray-400 pb-1">{{ $mensualite->inscription->preInscription->lieu_naissance }}</div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-600 font-bold mb-1">Contact</div>
                                    <div class="border-b border-dotted border-gray-400 pb-1">{{ $mensualite->inscription->preInscription->contact }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Informations académiques -->
                        <div>
                            <div class="text-sm font-bold text-green-600 mb-3 border-b border-gray-300 pb-1">INFORMATIONS ACADÉMIQUES</div>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <div class="text-xs text-gray-600 font-bold mb-1">Niveau</div>
                                    <div class="border-b border-dotted border-gray-400 pb-1">{{ $mensualite->inscription->niveau->nom }}</div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-600 font-bold mb-1">Classe</div>
                                    <div class="border-b border-dotted border-gray-400 pb-1">{{ $mensualite->inscription->classe->nom }} ({{ $mensualite->inscription->classe->code }})</div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-600 font-bold mb-1">Mois de paiement</div>
                                    <div class="border-b border-dotted border-gray-400 pb-1">{{ $mensualite->mois_libelle }}</div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-600 font-bold mb-1">Mode de paiement</div>
                                    <div class="border-b border-dotted border-gray-400 pb-1">{{ $mensualite->mode_paiement_libelle }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex-1 border-l border-gray-300 pl-6">
                        <!-- Informations financières -->
                        <div class="mb-6">
                            <div class="text-sm font-bold text-green-600 mb-3 border-b border-gray-300 pb-1">INFORMATIONS FINANCIÈRES</div>
                            
                            <div class="bg-gray-50 p-4 rounded mb-4">
                                <div class="grid grid-cols-1 gap-4 text-center text-sm">
                                    <div>
                                        <div class="text-xs text-gray-600 mb-1">Montant Mensualité</div>
                                        <div class="text-lg font-bold text-green-600">{{ number_format($mensualite->montant_du, 0, ',', ' ') }} FCFA</div>
                                    </div>
                                    <div>
                                        <div class="text-xs text-gray-600 mb-1">Montant Payé</div>
                                        <div class="text-lg font-bold text-blue-600">{{ number_format($mensualite->montant_paye, 0, ',', ' ') }} FCFA</div>
                                    </div>
                                    @if($mensualite->solde_restant > 0)
                                    <div>
                                        <div class="text-xs text-gray-600 mb-1">Solde Restant</div>
                                        <div class="text-lg font-bold text-red-600">{{ number_format($mensualite->solde_restant, 0, ',', ' ') }} FCFA</div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Statut du paiement -->
                            <div class="text-center mb-4">
                                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    @if($mensualite->statut === 'complet') bg-green-100 text-green-800
                                    @elseif($mensualite->statut === 'partiel') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    @if($mensualite->statut === 'complet')
                                        <i class="fas fa-check-circle mr-1"></i>Paiement Complet
                                    @elseif($mensualite->statut === 'partiel')
                                        <i class="fas fa-clock mr-1"></i>Paiement Partiel
                                    @else
                                        <i class="fas fa-times-circle mr-1"></i>Impayé
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Observations -->
                        @if($mensualite->observations)
                        <div class="mb-6">
                            <div class="text-sm font-bold text-green-600 mb-2 border-b border-gray-300 pb-1">OBSERVATIONS</div>
                            <div class="text-sm text-gray-700 bg-gray-50 p-3 rounded">
                                {{ $mensualite->observations }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Signatures -->
                <div class="mt-8 pt-4 border-t border-gray-300 flex justify-between">
                    <div class="text-center w-48">
                        <div class="h-12 mb-2"></div>
                        <div class="border-t border-gray-400 text-xs text-gray-600 pt-1">Signature de l'élève/tuteur</div>
                    </div>
                    <div class="text-center w-48">
                        <div class="h-12 mb-2"></div>
                        <div class="border-t border-gray-400 text-xs text-gray-600 pt-1">Cachet et signature de l'établissement</div>
                    </div>
                </div>
                
                <div class="absolute bottom-2 right-4 text-xs text-gray-500">
                    Généré le {{ now()->format('d/m/Y à H:i') }}
                </div>
            </div>
        </div>

        <!-- Coupon d'encaissement -->
        <div class="mt-8 border-2 border-dashed border-gray-400 p-4">
            <div class="text-center mb-3">
                <h3 class="text-sm font-bold text-gray-700 uppercase">Coupon d'Encaissement</h3>
                <div class="text-xs text-gray-600">À détacher et conserver</div>
            </div>
            
            <div class="flex justify-between items-center">
                <div class="flex-1">
                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="font-bold">Élève :</span> {{ strtoupper($mensualite->inscription->preInscription->nom) }} {{ ucwords(strtolower($mensualite->inscription->preInscription->prenom)) }}
                        </div>
                        <div>
                            <span class="font-bold">INE :</span> {{ $mensualite->inscription->preInscription->ine }}
                        </div>
                        <div>
                            <span class="font-bold">Classe :</span> {{ $mensualite->inscription->classe->nom }} ({{ $mensualite->inscription->classe->code }})
                        </div>
                        <div>
                            <span class="font-bold">Reçu N° :</span> {{ $mensualite->numero_recu }}
                        </div>
                        <div>
                            <span class="font-bold">Type :</span> MENSUALITÉ {{ $mensualite->mois_libelle }} {{ $mensualite->inscription->anneeScolaire->nom }}
                        </div>
                        <div>
                            <span class="font-bold">Montant Dû :</span> {{ number_format($mensualite->montant_du, 0, ',', ' ') }} FCFA
                        </div>
                    </div>
                </div>
                
                <div class="ml-6 text-center border-l border-gray-300 pl-6">
                    <div class="text-xs text-gray-600 mb-1">Montant Encaissé</div>
                    <div class="text-lg font-bold text-green-600">{{ number_format($mensualite->montant_paye, 0, ',', ' ') }} FCFA</div>
                    <div class="text-xs text-gray-600 mt-1">{{ $mensualite->date_paiement->format('d/m/Y') }}</div>
                    
                    @if($mensualite->solde_restant > 0)
                        <div class="mt-2 p-2 bg-yellow-100 rounded">
                            <div class="text-xs text-yellow-800 font-bold">Reste à payer</div>
                            <div class="text-sm font-bold text-red-600">{{ number_format($mensualite->solde_restant, 0, ',', ' ') }} FCFA</div>
                        </div>
                    @else
                        <div class="mt-2 p-2 bg-green-100 rounded">
                            <div class="text-xs text-green-800 font-bold">SOLDE</div>
                        </div>
                    @endif
                </div>
                
                <div class="ml-6 text-center">
                    <div class="text-xs text-gray-600 mb-2">Signature & Cachet</div>
                    <div class="w-20 h-8 border-b border-gray-400"></div>
                    <div class="text-xs text-gray-600 mt-1">{{ $etablissement->nom ?? 'École' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .flex-2 {
        flex: 2;
    }
    
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
        
        /* Style pour le coupon d'encaissement */
        .border-dashed {
            border-style: dashed !important;
        }
        
        @page {
            margin: 1cm;
            size: A4;
        }
    }
</style>
@endsection