<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aperçu Rapport Mensualités - CPAKM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            @page {
                size: A4 landscape;
                margin: 0.5in;
            }
            body { 
                font-size: 11px; 
                margin: 0;
                padding: 0;
            }
            table {
                font-size: 10px;
            }
            th, td {
                padding: 3px 5px !important;
            }
            .summary-cards {
                display: flex !important;
                flex-wrap: wrap !important;
                justify-content: space-between !important;
                margin: 5px 0 !important;
                gap: 4px !important;
            }
            .summary-card {
                border: 1px solid #ddd !important;
                padding: 3px 4px !important;
                text-align: center !important;
                width: 15.5% !important;
                min-width: 90px !important;
                background: #f9f9f9 !important;
                border-radius: 2px !important;
                box-shadow: none !important;
            }
            .summary-card .text-xs {
                font-size: 5px !important;
                line-height: 1.1 !important;
                margin-bottom: 1px !important;
            }
            .summary-card .text-lg {
                font-size: 8px !important;
                font-weight: bold !important;
                line-height: 1.2 !important;
            }
            .summary-card .text-xs.text-gray-400 {
                font-size: 4px !important;
                line-height: 1 !important;
                margin-top: 1px !important;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 px-4">
        <!-- En-tête avec boutons -->
        <div class="bg-white rounded-lg shadow-sm border mb-6 no-print">
            <div class="px-6 py-4 flex items-center justify-between border-b border-gray-200">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Aperçu du Rapport des Mensualités</h1>
                    <p class="text-sm text-gray-600 mt-1">
                        @if(isset($filtres['annee']) && $filtres['annee'])
                            Année: {{ $mensualites->first()?->anneeScolaire?->libelle ?? 'N/A' }}
                        @endif
                        @if(isset($filtres['mois']) && $filtres['mois']) - {{ ucfirst($filtres['mois']) }}@endif
                        @if(isset($filtres['dateDebut']) && $filtres['dateDebut']) - Du {{ $filtres['dateDebut'] }}@endif
                        @if(isset($filtres['dateFin']) && $filtres['dateFin'] && isset($filtres['dateDebut']) && $filtres['dateDebut']) au {{ $filtres['dateFin'] }}@endif
                        @if(isset($filtres['statut']) && $filtres['statut']) - Statut: 
                            @switch($filtres['statut'])
                                @case('complet') Payé intégralement @break
                                @case('partiel') Paiement partiel @break
                                @case('impaye') Impayé @break
                                @default {{ $filtres['statut'] }}
                            @endswitch
                        @endif
                    </p>
                </div>
                <div class="flex space-x-2">
                    <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-print mr-2"></i>Imprimer
                    </button>
                    <button onclick="window.close()" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
                        <i class="fas fa-times mr-2"></i>Fermer
                    </button>
                </div>
            </div>
        </div>

        <!-- Contenu à imprimer -->
        <div class="bg-white rounded-lg shadow-sm border">
            <!-- En-tête du rapport -->
            <div class="px-6 py-6 border-b border-gray-200 text-center">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">COURS PRIVÉS ABDOU KHADRE MBACKÉ</h1>
                <h2 class="text-lg font-semibold text-gray-700 mb-4">RAPPORT DES MENSUALITÉS</h2>
                <div class="text-sm text-gray-600 space-y-1">
                    @if(isset($filtres['annee']) && $filtres['annee'])
                        <p><strong>Année scolaire:</strong> {{ $mensualites->first()?->anneeScolaire?->libelle ?? 'N/A' }}</p>
                    @endif
                    @if(isset($filtres['mois']) && $filtres['mois'])
                        <p><strong>Mois:</strong> {{ ucfirst($filtres['mois']) }}</p>
                    @endif
                    @if((isset($filtres['dateDebut']) && $filtres['dateDebut']) || (isset($filtres['dateFin']) && $filtres['dateFin']))
                        <p><strong>Période:</strong> 
                            @if(isset($filtres['dateDebut']) && $filtres['dateDebut']) du {{ \Carbon\Carbon::parse($filtres['dateDebut'])->format('d/m/Y') }}@endif
                            @if((isset($filtres['dateFin']) && $filtres['dateFin']) && (isset($filtres['dateDebut']) && $filtres['dateDebut'])) au {{ \Carbon\Carbon::parse($filtres['dateFin'])->format('d/m/Y') }}@endif
                        </p>
                    @endif
                    @if(isset($filtres['statut']) && $filtres['statut'])
                        <p><strong>Statut:</strong> 
                            @switch($filtres['statut'])
                                @case('complet') Payé intégralement @break
                                @case('partiel') Paiement partiel @break
                                @case('impaye') Impayé @break
                                @default {{ $filtres['statut'] }}
                            @endswitch
                        </p>
                    @endif
                    <p><strong>Date d'édition:</strong> {{ now()->format('d/m/Y à H:i') }}</p>
                </div>
            </div>

            <!-- Résumé statistique -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <div class="summary-cards grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <div class="summary-card bg-white p-4 rounded-lg border shadow-sm">
                        <div class="label text-xs text-gray-500 uppercase font-medium mb-1">Élèves Inscrits</div>
                        <div class="value text-lg font-bold text-blue-600">{{ number_format($totaux['eleves_total'], 0, ',', ' ') }}</div>
                        <div class="text-xs text-gray-400">{{ number_format($totaux['eleves_avec_mensualites'], 0, ',', ' ') }} avec mensualités</div>
                    </div>
                    <div class="summary-card bg-white p-4 rounded-lg border shadow-sm">
                        <div class="label text-xs text-gray-500 uppercase font-medium mb-1">Total Mensualités</div>
                        <div class="value text-lg font-bold text-indigo-600">{{ number_format($totaux['mensualites_total'], 0, ',', ' ') }}</div>
                    </div>
                    <div class="summary-card bg-white p-4 rounded-lg border shadow-sm">
                        <div class="label text-xs text-gray-500 uppercase font-medium mb-1">Montant Total Dû</div>
                        <div class="value text-lg font-bold text-orange-600">{{ number_format($totaux['montant_total_du'], 0, ',', ' ') }} FCFA</div>
                    </div>
                    <div class="summary-card bg-white p-4 rounded-lg border shadow-sm">
                        <div class="label text-xs text-gray-500 uppercase font-medium mb-1">Montant Payé</div>
                        <div class="value text-lg font-bold text-green-600">{{ number_format($totaux['montant_total_paye'], 0, ',', ' ') }} FCFA</div>
                    </div>
                    <div class="summary-card bg-white p-4 rounded-lg border shadow-sm">
                        <div class="label text-xs text-gray-500 uppercase font-medium mb-1">Montant Restant</div>
                        <div class="value text-lg font-bold text-red-600">{{ number_format($totaux['montant_restant'], 0, ',', ' ') }} FCFA</div>
                    </div>
                    <div class="summary-card bg-white p-4 rounded-lg border shadow-sm">
                        <div class="label text-xs text-gray-500 uppercase font-medium mb-1">Taux Paiement</div>
                        <div class="value text-lg font-bold text-purple-600">{{ $totaux['pourcentage_paiement'] }}%</div>
                    </div>
                </div>
            </div>

            <!-- Tableau des données -->
            <div class="px-6 py-4">
                @if($mensualites->count() > 0)
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            <i class="fas fa-table mr-2 text-blue-600"></i>
                            Détail des Mensualités ({{ number_format($mensualites->count(), 0, ',', ' ') }} entrées)
                        </h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Élève</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Classe</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mois</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant Dû</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant Payé</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Restant</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mode</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Reçu</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($mensualites as $mensualite)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $mensualite->inscription->preInscription->prenom }} 
                                                {{ $mensualite->inscription->preInscription->nom }}
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $mensualite->inscription->preInscription->ine }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                            {{ $mensualite->inscription->classe->nom ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                            {{ ucfirst($mensualite->mois_paiement) }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-orange-600">
                                            {{ number_format($mensualite->montant_du, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-green-600">
                                            {{ number_format($mensualite->montant_paye, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium {{ ($mensualite->montant_du - $mensualite->montant_paye) > 0 ? 'text-red-600' : 'text-green-600' }}">
                                            {{ number_format($mensualite->montant_du - $mensualite->montant_paye, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                            @if($mensualite->mode_paiement)
                                                @switch($mensualite->mode_paiement)
                                                    @case('especes') Espèces @break
                                                    @case('virement') Virement @break
                                                    @case('cheque') Chèque @break
                                                    @case('orange_money') Orange Money @break
                                                    @case('wave') Wave @break
                                                    @case('free_money') Free Money @break
                                                    @default {{ $mensualite->mode_paiement }}
                                                @endswitch
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                            {{ $mensualite->date_paiement?->format('d/m/Y') ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @switch($mensualite->statut)
                                                @case('complet')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <i class="fas fa-check-circle mr-1"></i>Payé
                                                    </span>
                                                @break
                                                @case('partiel')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        <i class="fas fa-coins mr-1"></i>Partiel
                                                    </span>
                                                @break
                                                @case('impaye')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <i class="fas fa-times-circle mr-1"></i>Impayé
                                                    </span>
                                                @break
                                                @default
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        {{ $mensualite->statut }}
                                                    </span>
                                            @endswitch
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                            {{ $mensualite->numero_recu ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="mx-auto h-12 w-12 text-gray-400">
                            <i class="fas fa-table text-4xl"></i>
                        </div>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune donnée disponible</h3>
                        <p class="mt-1 text-sm text-gray-500">Aucune mensualité ne correspond aux critères sélectionnés.</p>
                    </div>
                @endif
            </div>

            <!-- Pied de page -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 text-center">
                <p class="text-xs text-gray-500">
                    © {{ now()->format('Y') }} - Cours Privés Abdou Khadre Mbacké - Rapport généré le {{ now()->format('d/m/Y à H:i') }}
                </p>
            </div>
        </div>
    </div>

    <script>
        // Ajustements pour l'impression
        window.addEventListener('beforeprint', function() {
            // Ajustements spécifiques avant impression si nécessaire
        });
        
        window.addEventListener('afterprint', function() {
            // Restaurer après impression si nécessaire
        });
    </script>
</body>
</html>