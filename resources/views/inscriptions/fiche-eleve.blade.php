@extends('layouts.app')

@section('title', 'Fiche Élève')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- En-tête avec boutons d'action -->
    <div class="flex items-center justify-between mb-6 print:hidden">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Fiche Élève</h1>
            <p class="text-gray-600">{{ $inscription->preInscription->nom }} {{ $inscription->preInscription->prenom }} | {{ $inscription->numero_recu }}</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
                <i class="fas fa-print mr-2"></i>Imprimer
            </button>
            <a href="{{ route('inscriptions.index', ['tab' => 'liste-eleves']) }}" 
               class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Retour à la liste
            </a>
        </div>
    </div>

    <!-- Fiche élève -->
    <div class="bg-white shadow-lg rounded-lg border" style="font-family: 'DejaVu Sans', Arial, sans-serif;">
        
        @php
            $etablissement = App\Models\Etablissement::first();
            $qrData = json_encode([
                'numero_recu' => $inscription->numero_recu,
                'ine' => $inscription->preInscription->ine,
                'nom' => $inscription->preInscription->nom,
                'prenom' => $inscription->preInscription->prenom,
                'classe' => $inscription->classe->nom,
                'niveau' => $inscription->niveau->nom,
                'montant_total' => $inscription->montant_total,
                'montant_paye' => $inscription->montant_paye,
                'date_inscription' => $inscription->date_inscription->format('d/m/Y'),
                'etablissement' => $etablissement->nom ?? 'Non défini',
                'type' => 'FICHE_ELEVE'
            ]);
        @endphp

        <div class="p-6">
            <!-- En-tête -->
            <div class="flex justify-between items-center border-b-2 border-blue-600 pb-4 mb-6">
                <div class="flex-1">
                    <div class="text-lg font-bold text-blue-600 mb-1">{{ $etablissement->nom ?? 'Nom de l\'établissement' }}</div>
                    <div class="text-xs text-gray-600 leading-tight">
                        @if($etablissement)
                            <div><strong>Adresse :</strong> {{ $etablissement->adresse }}</div>
                            <div><strong>Tél :</strong> {{ $etablissement->telephone }} | <strong>Email :</strong> {{ $etablissement->email }}</div>
                            <div><strong>Responsable :</strong> {{ $etablissement->responsable }}</div>
                        @endif
                    </div>
                </div>
                
                <div class="flex-1 text-center">
                    <h1 class="text-xl font-bold text-blue-600 mb-1">FICHE ÉLÈVE</h1>
                    <div class="text-sm text-gray-600 font-bold">N° {{ $inscription->numero_recu }}</div>
                    <div class="text-sm text-gray-600 font-bold">Année : {{ $inscription->anneeScolaire->nom }}</div>
                </div>
                
                <div class="w-20 text-center">
                    <div class="w-16 h-16 border border-gray-300 bg-gray-100 flex items-center justify-center mb-1">
                        @if(class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode'))
                            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(60)->generate($qrData) !!}
                        @else
                            <i class="fas fa-qrcode text-2xl text-gray-400"></i>
                        @endif
                    </div>
                    
                </div>
            </div>
            
            <!-- Contenu principal -->
            <div class="flex gap-8">
                
                <!-- Colonne gauche : Informations élève et inscription -->
                <div class="flex-2 space-y-4">
                    
                    <!-- Informations de l'élève -->
                    <div>
                        <div class="text-sm font-bold text-blue-600 mb-3 border-b border-gray-300 pb-1">INFORMATIONS DE L'ÉLÈVE</div>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <div class="text-xs text-gray-600 font-bold mb-1">Nom</div>
                                <div class="border-b border-dotted border-gray-400 pb-1">{{ strtoupper($inscription->preInscription->nom) }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-600 font-bold mb-1">Prénom</div>
                                <div class="border-b border-dotted border-gray-400 pb-1">{{ ucwords(strtolower($inscription->preInscription->prenom)) }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-600 font-bold mb-1">INE</div>
                                <div class="border-b border-dotted border-gray-400 pb-1">{{ $inscription->preInscription->ine }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-600 font-bold mb-1">Date de naissance</div>
                                <div class="border-b border-dotted border-gray-400 pb-1">
                                    {{ \Carbon\Carbon::parse($inscription->preInscription->date_naissance)->format('d/m/Y') }}
                                    <span class="text-gray-500 text-xs">({{ \Carbon\Carbon::parse($inscription->preInscription->date_naissance)->age }} ans)</span>
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-600 font-bold mb-1">Lieu de naissance</div>
                                <div class="border-b border-dotted border-gray-400 pb-1">{{ $inscription->preInscription->lieu_naissance }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-600 font-bold mb-1">Contact</div>
                                <div class="border-b border-dotted border-gray-400 pb-1">{{ $inscription->preInscription->contact }}</div>
                            </div>
                            @if($inscription->preInscription->sexe)
                            <div>
                                <div class="text-xs text-gray-600 font-bold mb-1">Sexe</div>
                                <div class="border-b border-dotted border-gray-400 pb-1">{{ ucfirst($inscription->preInscription->sexe) }}</div>
                            </div>
                            @endif
                        </div>
                        @if($inscription->preInscription->adresse)
                        <div class="mt-4">
                            <div class="text-xs text-gray-600 font-bold mb-1">Adresse</div>
                            <div class="border-b border-dotted border-gray-400 pb-1">{{ $inscription->preInscription->adresse }}</div>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Informations académiques -->
                    <div>
                        <div class="text-sm font-bold text-blue-600 mb-3 border-b border-gray-300 pb-1">INFORMATIONS ACADÉMIQUES</div>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <div class="text-xs text-gray-600 font-bold mb-1">Niveau</div>
                                <div class="border-b border-dotted border-gray-400 pb-1">{{ $inscription->niveau->nom }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-600 font-bold mb-1">Classe</div>
                                <div class="border-b border-dotted border-gray-400 pb-1">{{ $inscription->classe->nom }} ({{ $inscription->classe->code }})</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-600 font-bold mb-1">Date d'inscription</div>
                                <div class="border-b border-dotted border-gray-400 pb-1">{{ $inscription->date_inscription->format('d/m/Y') }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-600 font-bold mb-1">Mode de paiement</div>
                                <div class="border-b border-dotted border-gray-400 pb-1">{{ ucfirst(str_replace('_', ' ', $inscription->mode_paiement)) }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Historique des mensualités - Version compacte -->
                    <div>
                        <div class="text-sm font-bold text-blue-600 mb-3 border-b border-gray-300 pb-1">HISTORIQUE DES MENSUALITÉS</div>
                        
                        @if($inscription->mensualites->count() > 0)
                            <div class="space-y-2">
                                @foreach($inscription->mensualites as $mensualite)
                                    <div class="flex justify-between items-center py-1 border-b border-dotted border-gray-300">
                                        <div class="flex-1">
                                            <span class="font-semibold text-xs">{{ ucfirst($mensualite->mois_paiement) }}</span>
                                            @if($mensualite->date_paiement)
                                                <span class="text-xs text-gray-600 ml-2">{{ $mensualite->date_paiement->format('d/m/Y') }}</span>
                                            @endif
                                            @if($mensualite->numero_recu)
                                                <span class="text-xs text-gray-500 ml-2">({{ $mensualite->numero_recu }})</span>
                                            @endif
                                        </div>
                                        <div class="text-right flex items-center">
                                            <span class="text-xs mr-2">{{ number_format($mensualite->montant_paye, 0, ',', ' ') }}/{{ number_format($mensualite->montant_du, 0, ',', ' ') }} F</span>
                                            <span class="px-1 py-0.5 text-xs rounded {{ $mensualite->statut == 'complet' ? 'bg-green-100 text-green-800' : ($mensualite->statut == 'partiel' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                {{ $mensualite->statut == 'complet' ? 'P' : ($mensualite->statut == 'partiel' ? 'PA' : 'I') }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-gray-600 py-4">
                                <p class="text-xs">Aucune mensualité enregistrée</p>
                            </div>
                        @endif
                        
                        @if($inscription->mensualites->where('statut', 'impaye')->count() > 0)
                        @php
                            $mensualitesImpayeesCollection = $inscription->mensualites->where('statut', 'impaye');
                            $currentMonth = strtolower(now()->locale('fr')->translatedFormat('F'));
                            $currentYear = now()->year;
                            
                            // Mois de l'année scolaire dans l'ordre
                            $moisScolaires = ['octobre', 'novembre', 'decembre', 'janvier', 'fevrier', 'mars', 'avril', 'mai', 'juin', 'juillet'];
                            
                            // Déterminer l'année scolaire actuelle
                            $anneeScolaireActuelle = now()->month >= 8 ? $currentYear : $currentYear - 1;
                            
                            // Vérifier si nous sommes dans l'année scolaire de l'inscription
                            $estAnneeScolaireActuelle = $inscription->anneeScolaire && 
                                                       str_contains($inscription->anneeScolaire->nom, (string)$anneeScolaireActuelle);
                            
                            // Déterminer les mensualités vraiment en retard (mois déjà passés)
                            $mensualitesEnRetard = collect();
                            $mensualitesAPayer = collect();
                            
                            if ($estAnneeScolaireActuelle) {
                                foreach ($mensualitesImpayeesCollection as $mensualite) {
                                    $moisMensualite = $mensualite->mois_paiement;
                                    $indexMoisActuel = array_search($currentMonth, $moisScolaires);
                                    $indexMoisMensualite = array_search($moisMensualite, $moisScolaires);
                                    
                                    // Si le mois de la mensualité est passé
                                    if ($indexMoisMensualite !== false && $indexMoisActuel !== false && $indexMoisMensualite < $indexMoisActuel) {
                                        $mensualitesEnRetard->push($mensualite);
                                    } else {
                                        $mensualitesAPayer->push($mensualite);
                                    }
                                }
                            } else {
                                // Si ce n'est pas l'année scolaire actuelle, tout est soit en retard soit déjà fini
                                $mensualitesEnRetard = $mensualitesImpayeesCollection;
                            }
                        @endphp
                        
                        @if($mensualitesEnRetard->count() > 0)
                        <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded">
                            <div class="text-xs font-bold text-red-600 mb-2">MENSUALITÉS EN RETARD</div>
                            <div class="flex flex-wrap gap-1">
                                @foreach($mensualitesEnRetard as $mensualiteRetard)
                                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded">
                                        {{ ucfirst($mensualiteRetard->mois_paiement) }} 
                                        ({{ number_format($mensualiteRetard->montant_du, 0, ',', ' ') }} F)
                                    </span>
                                @endforeach
                            </div>
                            <div class="mt-2 text-xs text-red-600 font-semibold">
                                Total en retard: {{ number_format($mensualitesEnRetard->sum('montant_du'), 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                        @endif
                        @endif
                    </div>
                </div>
                
                <!-- Colonne droite : Résumé financier -->
                <div class="flex-1 border-l border-gray-300 pl-6">
                    
                    <!-- Informations financières inscription -->
                    <div class="mb-6">
                        <div class="text-sm font-bold text-blue-600 mb-3 border-b border-gray-300 pb-1">INFORMATIONS FINANCIÈRES</div>
                        
                        <div class="bg-gray-50 p-4 rounded mb-4">
                            <div class="grid grid-cols-2 gap-4 text-center text-sm">
                                <div>
                                    <div class="text-xs text-gray-600 mb-1">Montant Total</div>
                                    <div class="text-lg font-bold text-blue-600">{{ number_format($inscription->montant_total, 0, ',', ' ') }} FCFA</div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-600 mb-1">Montant Payé</div>
                                    <div class="text-lg font-bold text-blue-600">{{ number_format($inscription->montant_paye, 0, ',', ' ') }} FCFA</div>
                                </div>
                            </div>
                            
                            @if($inscription->montant_paye < $inscription->montant_total)
                                <div class="text-center mt-4">
                                    <div class="text-xs text-gray-600 mb-1">Reste à payer</div>
                                    <div class="text-lg font-bold text-red-600">{{ number_format($inscription->montant_total - $inscription->montant_paye, 0, ',', ' ') }} FCFA</div>
                                </div>
                            @endif
                            
                            <div class="text-center mt-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ ($inscription->montant_paye >= $inscription->montant_total) ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ($inscription->montant_paye >= $inscription->montant_total) ? 'Paiement Complet' : 'Paiement Partiel' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Résumé mensualités -->
                    <div class="mb-6">
                        <div class="text-sm font-bold text-blue-600 mb-3 border-b border-gray-300 pb-1">RÉSUMÉ MENSUALITÉS</div>
                        
                        <div class="bg-gray-50 p-4 rounded">
                            <div class="text-center mb-3">
                                <div class="text-xs text-gray-600 mb-1">Total mensualités</div>
                                <div class="text-lg font-bold text-blue-600">{{ number_format($montantTotalMensualites, 0, ',', ' ') }} FCFA</div>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-2 text-xs mb-3">
                                <div class="text-center">
                                    <div class="text-green-600 font-bold">{{ number_format($montantPayeMensualites, 0, ',', ' ') }} F</div>
                                    <div class="text-gray-600">Payé</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-red-600 font-bold">{{ number_format($soldeRestantMensualites, 0, ',', ' ') }} F</div>
                                    <div class="text-gray-600">Restant</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-orange-600 font-bold">{{ $mensualitesImpayes }}</div>
                                    <div class="text-gray-600">Mois</div>
                                </div>
                            </div>
                            
                            @if($soldeRestantMensualites > 0)
                            <div class="mt-3 p-2 bg-red-50 border border-red-200 rounded">
                                <div class="text-center">
                                    <div class="text-xs text-red-600 font-bold mb-1">MENSUALITÉS RESTANTES</div>
                                    <div class="text-sm font-bold text-red-600">{{ number_format($soldeRestantMensualites, 0, ',', ' ') }} FCFA</div>
                                    <div class="text-xs text-red-500">{{ $mensualitesImpayes }} mois impayé{{ $mensualitesImpayes > 1 ? 's' : '' }}</div>
                                </div>
                            </div>
                            @else
                            <div class="mt-3 p-2 bg-green-50 border border-green-200 rounded">
                                <div class="text-center">
                                    <div class="text-xs text-green-600 font-bold">MENSUALITÉS À JOUR</div>
                                    <div class="text-xs text-green-500">Toutes les mensualités sont payées</div>
                                </div>
                            </div>
                            @endif
                            
                            <!-- Statistiques compactes -->
                            <div class="grid grid-cols-4 gap-1 text-xs">
                                <div class="bg-green-100 p-1 rounded text-center">
                                    <div class="font-bold text-green-800">{{ $mensualitesPayees }}</div>
                                    <div class="text-green-600 text-xs">P</div>
                                </div>
                                <div class="bg-yellow-100 p-1 rounded text-center">
                                    <div class="font-bold text-yellow-800">{{ $mensualitesPartielles }}</div>
                                    <div class="text-yellow-600 text-xs">PA</div>
                                </div>
                                <div class="bg-red-100 p-1 rounded text-center">
                                    <div class="font-bold text-red-800">{{ $mensualitesImpayes }}</div>
                                    <div class="text-red-600 text-xs">I</div>
                                </div>
                                <div class="bg-blue-100 p-1 rounded text-center">
                                    <div class="font-bold text-blue-800">{{ $totalMensualites }}</div>
                                    <div class="text-blue-600 text-xs">T</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($inscription->preInscription->tuteur || $inscription->preInscription->adresse)
                    <!-- Tuteur/Responsable -->
                    <div class="mb-6">
                        <div class="text-sm font-bold text-blue-600 mb-3 border-b border-gray-300 pb-1">TUTEUR/RESPONSABLE</div>
                        <div class="text-sm space-y-2">
                            @if($inscription->preInscription->tuteur)
                                <div>
                                    <div class="text-xs text-gray-600 font-bold mb-1">Nom du tuteur</div>
                                    <div class="border-b border-dotted border-gray-400 pb-1">{{ $inscription->preInscription->tuteur }}</div>
                                </div>
                            @endif
                            @if($inscription->preInscription->adresse)
                                <div>
                                    <div class="text-xs text-gray-600 font-bold mb-1">Adresse</div>
                                    <div class="border-b border-dotted border-gray-400 pb-1">{{ $inscription->preInscription->adresse }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Légende des statuts -->
                    <div>
                        <div class="text-sm font-bold text-blue-600 mb-3 border-b border-gray-300 pb-1">LÉGENDE</div>
                        <div class="text-xs text-gray-700 space-y-1">
                            <div class="flex items-center">
                                <span class="px-1 py-0.5 text-xs rounded bg-green-100 text-green-800 mr-2 font-bold">P</span>
                                <span>Payé (mensualité complètement payée)</span>
                            </div>
                            <div class="flex items-center">
                                <span class="px-1 py-0.5 text-xs rounded bg-yellow-100 text-yellow-800 mr-2 font-bold">PA</span>
                                <span>Partiel (paiement incomplet)</span>
                            </div>
                            <div class="flex items-center">
                                <span class="px-1 py-0.5 text-xs rounded bg-red-100 text-red-800 mr-2 font-bold">I</span>
                                <span>Impayé (aucun paiement)</span>
                            </div>
                            <div class="flex items-center">
                                <span class="px-1 py-0.5 text-xs rounded bg-blue-100 text-blue-800 mr-2 font-bold">T</span>
                                <span>Total (nombre total de mensualités)</span>
                            </div>
                        </div>
                    </div>

                    @if($inscription->remarques)
                    <!-- Remarques -->
                    <div class="mt-4">
                        <div class="text-sm font-bold text-blue-600 mb-3 border-b border-gray-300 pb-1">REMARQUES</div>
                        <div class="text-xs text-gray-700 bg-gray-50 p-2 rounded">
                            {{ $inscription->remarques }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Footer -->
            <div class="mt-8 pt-4 border-t border-gray-300 text-center text-xs text-gray-500">
                <p>Fiche générée le {{ now()->format('d/m/Y à H:i') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Styles d'impression -->
<style>
    .flex-2 {
        flex: 2;
    }
    
    @media print {
        /* Masquer les éléments de navigation */
        nav, .navbar, header, .sidebar, .top-nav { display: none !important; }
        
        /* Masquer les boutons d'action */
        .print\\:hidden { display: none !important; }
        
        /* Configuration de la page */
        @page {
            margin: 1cm;
            size: A4;
        }
        
        /* Optimiser le layout pour l'impression */
        body { 
            font-size: 12px; 
            line-height: 1.4;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        /* Réajuster les conteneurs */
        .max-w-6xl { 
            max-width: none !important; 
            margin: 0 !important;
        }
        
        /* Supprimer les ombres et bordures arrondies */
        .shadow-lg { box-shadow: none !important; }
        .rounded-lg, .rounded { border-radius: 0 !important; }
        
        /* Optimiser les couleurs de fond pour l'impression */
        .bg-gray-50 { 
            background-color: #f9f9f9 !important; 
            -webkit-print-color-adjust: exact;
        }
        
        /* Optimiser la taille des textes */
        .text-2xl { font-size: 18px !important; }
        .text-xl { font-size: 16px !important; }
        .text-lg { font-size: 14px !important; }
        .text-sm { font-size: 12px !important; }
        .text-xs { font-size: 10px !important; }
        
        /* Forcer l'affichage des couleurs pour les badges */
        .bg-green-100, .bg-yellow-100, .bg-red-100, .bg-blue-100 {
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        
        /* Optimiser l'espacement pour l'impression */
        .space-y-4 > * + * { margin-top: 0.75rem !important; }
        .space-y-2 > * + * { margin-top: 0.5rem !important; }
        .gap-8 { gap: 1rem !important; }
        .gap-4 { gap: 0.75rem !important; }
        
        /* Améliorer la lisibilité des bordures */
        .border-dotted {
            border-style: dotted !important;
        }
        
        /* Éviter les coupures de page inappropriées */
        .space-y-4 > div {
            page-break-inside: avoid;
        }
        
        /* Style pour le coupon d'historique mensualités */
        .space-y-2 > div {
            page-break-inside: avoid;
        }
    }
</style>
@endsection