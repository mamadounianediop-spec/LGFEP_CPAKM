@extends('layouts.app')

@section('title', 'Fiche de dépense de service')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- En-tête avec boutons d'action -->
    <div class="flex items-center justify-between mb-6 print:hidden">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Aperçu de la fiche de dépense</h1>
            <p class="text-gray-600">{{ $depense->service->nom }} - {{ number_format($depense->montant, 0, ',', ' ') }} FCFA</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
                <i class="fas fa-print mr-2"></i>Imprimer
            </button>
            <a href="{{ route('services.index', ['tab' => 'depenses']) }}" 
               class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Retour aux dépenses
            </a>
        </div>
    </div>

    <!-- Aperçu de la fiche (style PDF) -->
    <div class="bg-white shadow-lg border-2 border-gray-300 print:shadow-none print:border-none" style="font-family: 'DejaVu Sans', Arial, sans-serif;">
        
        @php
            $qrData = json_encode([
                'id_depense' => $depense->id,
                'service' => $depense->service->nom,
                'categorie' => $depense->service->categorieService->nom ?? 'Non définie',
                'type_depense' => $depense->type_depense,
                'montant' => $depense->montant,
                'date_depense' => \Carbon\Carbon::parse($depense->date_depense)->format('d/m/Y'),
                'numero_facture' => $depense->numero_facture,
                'etablissement' => $etablissement->nom ?? 'Non défini'
            ]);
        @endphp

        <!-- Fiche principale -->
        <div class="border-2 border-orange-600 p-6 mb-4 relative">
            <div class="absolute inset-0 flex items-center justify-center opacity-5 pointer-events-none">
                <div class="text-6xl font-bold text-orange-600 transform rotate-45">
                    {{ $etablissement->nom ?? 'ÉTABLISSEMENT' }}
                </div>
            </div>
            
            <div class="relative z-10">
                <!-- En-tête -->
                <div class="flex justify-between items-center border-b-2 border-orange-600 pb-4 mb-6">
                    <div class="flex-1">
                        <div class="text-lg font-bold text-orange-600 mb-1">{{ $etablissement->nom ?? 'Nom de l\'établissement' }}</div>
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
                        <h1 class="text-xl font-bold text-orange-600 mb-1">FICHE DE DÉPENSE DE SERVICE</h1>
                        <div class="text-sm text-gray-600 font-bold">N° {{ str_pad($depense->id, 6, '0', STR_PAD_LEFT) }}</div>
                        <div class="text-sm text-gray-600 font-bold">Année : {{ $depense->anneeScolaire->nom ?? 'Non définie' }}</div>
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
                        <!-- Informations du service -->
                        <div>
                            <div class="text-sm font-bold text-orange-600 mb-3 border-b border-gray-300 pb-1">INFORMATIONS DU SERVICE</div>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <div class="text-xs text-gray-600 font-bold mb-1">Nom du service</div>
                                    <div class="border-b border-dotted border-gray-400 pb-1">{{ $depense->service->nom }}</div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-600 font-bold mb-1">Catégorie</div>
                                    <div class="border-b border-dotted border-gray-400 pb-1">{{ $depense->service->categorieService->nom ?? 'Non définie' }}</div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-600 font-bold mb-1">Fournisseur du service</div>
                                    <div class="border-b border-dotted border-gray-400 pb-1">{{ $depense->service->fournisseur ?? 'Non renseigné' }}</div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-600 font-bold mb-1">Statut du service</div>
                                    <div class="border-b border-dotted border-gray-400 pb-1">
                                        @if($depense->service->statut === 'actif')
                                            <span class="text-green-600 font-medium">Actif</span>
                                        @elseif($depense->service->statut === 'en_maintenance')
                                            <span class="text-yellow-600 font-medium">En maintenance</span>
                                        @else
                                            <span class="text-red-600 font-medium">Inactif</span>
                                        @endif
                                    </div>  
                                </div>
                                @if($depense->service->date_acquisition)
                                <div>
                                    <div class="text-xs text-gray-600 font-bold mb-1">Date d'acquisition</div>
                                    <div class="border-b border-dotted border-gray-400 pb-1">{{ \Carbon\Carbon::parse($depense->service->date_acquisition)->format('d/m/Y') }}</div>
                                </div>
                                @endif
                                <div>
                                    <div class="text-xs text-gray-600 font-bold mb-1">Description du service</div>
                                    <div class="border-b border-dotted border-gray-400 pb-1">{{ $depense->service->description ?? 'Aucune description' }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Informations de la dépense -->
                        <div>
                            <div class="text-sm font-bold text-orange-600 mb-3 border-b border-gray-300 pb-1">DÉTAILS DE LA DÉPENSE</div>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <div class="text-xs text-gray-600 font-bold mb-1">Type de dépense</div>
                                    <div class="border-b border-dotted border-gray-400 pb-1">
                                        @switch($depense->type_depense)
                                            @case('achat')
                                                <span class="font-medium">Achat</span>
                                                @break
                                            @case('maintenance')
                                                <span class="font-medium">Maintenance</span>
                                                @break
                                            @case('location')
                                                <span class="font-medium">Location</span>
                                                @break
                                            @case('reparation')
                                                <span class="font-medium">Réparation</span>
                                                @break
                                            @case('consommation')
                                                <span class="font-medium">Consommation</span>
                                                @break
                                            @default
                                                <span class="font-medium">Autre</span>
                                        @endswitch
                                    </div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-600 font-bold mb-1">Date de la dépense</div>
                                    <div class="border-b border-dotted border-gray-400 pb-1">{{ \Carbon\Carbon::parse($depense->date_depense)->format('d/m/Y') }}</div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-600 font-bold mb-1">N° de facture</div>
                                    <div class="border-b border-dotted border-gray-400 pb-1">{{ $depense->numero_facture ?? 'Non renseigné' }}</div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-600 font-bold mb-1">Créé le</div>
                                    <div class="border-b border-dotted border-gray-400 pb-1">{{ $depense->created_at->format('d/m/Y à H:i') }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Description de la dépense -->
                        @if($depense->description)
                        <div>
                            <div class="text-sm font-bold text-orange-600 mb-3 border-b border-gray-300 pb-1">DESCRIPTION DE LA DÉPENSE</div>
                            <div class="text-sm text-gray-700 bg-gray-50 p-3 rounded border">
                                {{ $depense->description }}
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <div class="flex-1 border-l border-gray-300 pl-6">
                        <!-- Informations financières -->
                        <div class="mb-6">
                            <div class="text-sm font-bold text-orange-600 mb-3 border-b border-gray-300 pb-1">INFORMATIONS FINANCIÈRES</div>
                            
                            <div class="bg-orange-50 border border-orange-200 p-4 rounded mb-4">
                                <div class="text-center">
                                    <div class="text-xs text-gray-600 mb-1">Montant de la dépense</div>
                                    <div class="text-2xl font-bold text-orange-600">{{ number_format($depense->montant, 0, ',', ' ') }} FCFA</div>
                                    <div class="text-xs text-gray-500 mt-1">{{ ucfirst(\Carbon\Carbon::parse($depense->date_depense)->locale('fr')->isoFormat('MMMM YYYY')) }}</div>
                                </div>
                            </div>
                            
                            <!-- Classification de la dépense -->
                            <div class="text-center mb-4">
                                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    @switch($depense->type_depense)
                                        @case('achat') bg-blue-100 text-blue-800 @break
                                        @case('maintenance') bg-yellow-100 text-yellow-800 @break
                                        @case('location') bg-purple-100 text-purple-800 @break
                                        @case('reparation') bg-red-100 text-red-800 @break
                                        @case('consommation') bg-green-100 text-green-800 @break
                                        @default bg-gray-100 text-gray-800
                                    @endswitch">
                                    @switch($depense->type_depense)
                                        @case('achat')
                                            <i class="fas fa-shopping-cart mr-1"></i>Achat
                                            @break
                                        @case('maintenance')
                                            <i class="fas fa-tools mr-1"></i>Maintenance
                                            @break
                                        @case('location')
                                            <i class="fas fa-handshake mr-1"></i>Location
                                            @break
                                        @case('reparation')
                                            <i class="fas fa-wrench mr-1"></i>Réparation
                                            @break
                                        @case('consommation')
                                            <i class="fas fa-tint mr-1"></i>Consommation
                                            @break
                                        @default
                                            <i class="fas fa-receipt mr-1"></i>Autre
                                    @endswitch
                                </div>
                            </div>

                            <!-- Récapitulatif -->
                            <div class="bg-gray-50 p-3 rounded border text-xs">
                                <div class="font-bold text-gray-700 mb-2">RÉCAPITULATIF</div>
                                <div class="space-y-1">
                                    <div class="flex justify-between">
                                        <span>Service :</span>
                                        <span class="font-medium">{{ Str::limit($depense->service->nom, 20) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Type :</span>
                                        <span class="font-medium">{{ ucfirst($depense->type_depense) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Date :</span>
                                        <span class="font-medium">{{ \Carbon\Carbon::parse($depense->date_depense)->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="border-t border-gray-300 pt-1 mt-2">
                                        <div class="flex justify-between font-bold">
                                            <span>Total :</span>
                                            <span class="text-orange-600">{{ number_format($depense->montant, 0, ',', ' ') }} FCFA</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Signatures -->
                <div class="mt-8 pt-4 border-t border-gray-300 flex justify-between">
                    <div class="text-center w-48">
                        <div class="h-12 mb-2"></div>
                        <div class="border-t border-gray-400 text-xs text-gray-600 pt-1">Signature du responsable</div>
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

        <!-- Coupon de suivi -->
        <div class="mt-8 border-2 border-dashed border-gray-400 p-4">
            <div class="text-center mb-3">
                <h3 class="text-sm font-bold text-gray-700 uppercase">Coupon de Suivi des Dépenses</h3>
                <div class="text-xs text-gray-600">À détacher et conserver dans les archives</div>
            </div>
            
            <div class="flex justify-between items-center">
                <div class="flex-1">
                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="font-bold">Service :</span> {{ $depense->service->nom }}
                        </div>
                        <div>
                            <span class="font-bold">Catégorie :</span> {{ $depense->service->categorieService->nom ?? 'Non définie' }}
                        </div>
                        <div>
                            <span class="font-bold">Type de dépense :</span> {{ ucfirst($depense->type_depense) }}
                        </div>
                        <div>
                            <span class="font-bold">N° Fiche :</span> {{ str_pad($depense->id, 6, '0', STR_PAD_LEFT) }}
                        </div>
                        <div>
                            <span class="font-bold">Facture N° :</span> {{ $depense->numero_facture ?? 'Non renseigné' }}
                        </div>
                        <div>
                            <span class="font-bold">Année :</span> {{ $depense->anneeScolaire->nom ?? 'Non définie' }}
                        </div>
                    </div>
                </div>
                
                <div class="ml-6 text-center border-l border-gray-300 pl-6">
                    <div class="text-xs text-gray-600 mb-1">Montant Dépensé</div>
                    <div class="text-lg font-bold text-orange-600">{{ number_format($depense->montant, 0, ',', ' ') }} FCFA</div>
                    <div class="text-xs text-gray-600 mt-1">{{ \Carbon\Carbon::parse($depense->date_depense)->format('d/m/Y') }}</div>
                    
                    <div class="mt-2 p-2 bg-orange-100 rounded">
                        <div class="text-xs text-orange-800 font-bold">DÉPENSE {{ strtoupper($depense->type_depense) }}</div>
                        <div class="text-xs text-gray-600">{{ $etablissement->nom ?? 'École' }}</div>
                    </div>
                </div>
                
                <div class="ml-6 text-center">
                    <div class="text-xs text-gray-600 mb-2">Visa & Cachet</div>
                    <div class="w-20 h-8 border-b border-gray-400"></div>
                    <div class="text-xs text-gray-600 mt-1">Comptabilité</div>
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
        
        /* Style pour le coupon de suivi */
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