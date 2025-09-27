<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aperçu Rapport Inscriptions - {{ $etablissement->nom ?? 'ÉTABLISSEMENT' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Styles pour optimiser l'affichage des cartes */
        .summary-card {
            transition: all 0.2s ease;
        }
        .summary-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
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
                gap: 6px !important;
            }
            .summary-card {
                border: 1px solid #ddd !important;
                padding: 4px 6px !important;
                text-align: center !important;
                width: 18% !important;
                min-width: 100px !important;
                background: #f9f9f9 !important;
                border-radius: 2px !important;
                box-shadow: none !important;
            }
            .summary-card .text-xs {
                font-size: 6px !important;
                line-height: 1.1 !important;
                margin-bottom: 1px !important;
            }
            .summary-card .text-lg {
                font-size: 9px !important;
                font-weight: bold !important;
                line-height: 1.2 !important;
            }
            .summary-card .text-xs.text-gray-400 {
                font-size: 5px !important;
                line-height: 1 !important;
                margin-top: 1px !important;
            }
            /* Styles spécifiques pour les chart-cards après le tableau - copie exacte des summary-cards */
            .chart-section {
                display: flex !important;
                flex-wrap: wrap !important;
                justify-content: space-between !important;
                margin: 5px 0 !important;
                gap: 6px !important;
            }
            .chart-card {
                border: 1px solid #ddd !important;
                padding: 4px 6px !important;
                text-align: center !important;
                width: 18% !important;
                min-width: 100px !important;
                background: #f9f9f9 !important;
                border-radius: 2px !important;
                box-shadow: none !important;
            }
            .chart-card .text-xs {
                font-size: 6px !important;
                line-height: 1.1 !important;
                margin-bottom: 1px !important;
            }
            .chart-card .text-lg {
                font-size: 9px !important;
                font-weight: bold !important;
                line-height: 1.2 !important;
            }
            .chart-card .text-xs.text-gray-600 {
                font-size: 5px !important;
                line-height: 1 !important;
                margin-top: 1px !important;
            }
            /* Styles pour l'impression - garder simple */
            .bg-gray-50 {
                background: white !important;
            }
            .shadow-sm {
                box-shadow: none !important;
            }
            .rounded-lg {
                border-radius: 2px !important;
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
                    <h1 class="text-2xl font-bold text-gray-900">Aperçu du Rapport des Inscriptions</h1>
                    <p class="text-sm text-gray-600 mt-1">
                        @if(isset($filtres['annee']) && $filtres['annee'])
                            Année: {{ $inscriptions->first()?->anneeScolaire?->libelle ?? 'N/A' }}
                        @endif
                        @if(isset($filtres['dateDebut']) && $filtres['dateDebut']) - Du {{ $filtres['dateDebut'] }}@endif
                        @if(isset($filtres['dateFin']) && $filtres['dateFin'] && isset($filtres['dateDebut']) && $filtres['dateDebut']) au {{ $filtres['dateFin'] }}@endif
                        @if(isset($filtres['niveau']) && $filtres['niveau']) - Niveau sélectionné@endif
                        @if(isset($filtres['statut']) && $filtres['statut']) - Statut: {{ ucfirst(str_replace('-', ' ', $filtres['statut'])) }}@endif
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
                <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $etablissement->nom ?? 'ÉTABLISSEMENT SCOLAIRE' }}</h1>
                <h2 class="text-lg font-semibold text-gray-700 mb-4">RAPPORT DES INSCRIPTIONS</h2>
                <div class="text-sm text-gray-600 space-y-1">
                    @if(isset($filtres['annee']) && $filtres['annee'])
                        <p><strong>Année scolaire:</strong> {{ $inscriptions->first()?->anneeScolaire?->libelle ?? 'N/A' }}</p>
                    @endif
                    @if((isset($filtres['dateDebut']) && $filtres['dateDebut']) || (isset($filtres['dateFin']) && $filtres['dateFin']))
                        <p><strong>Période:</strong> 
                            @if(isset($filtres['dateDebut']) && $filtres['dateDebut']) du {{ \Carbon\Carbon::parse($filtres['dateDebut'])->format('d/m/Y') }}@endif
                            @if((isset($filtres['dateFin']) && $filtres['dateFin']) && (isset($filtres['dateDebut']) && $filtres['dateDebut'])) au {{ \Carbon\Carbon::parse($filtres['dateFin'])->format('d/m/Y') }}@endif
                        </p>
                    @endif
                    @if(isset($filtres['niveau']) && $filtres['niveau'])
                        <p><strong>Niveau:</strong> {{ $niveaux->find($filtres['niveau'])->nom ?? 'Sélectionné' }}</p>
                    @endif
                    @if(isset($filtres['statut']) && $filtres['statut'])
                        <p><strong>Statut:</strong> {{ ucfirst(str_replace('-', ' ', $filtres['statut'])) }}</p>
                    @endif
                    <p><strong>Date d'édition:</strong> {{ now()->format('d/m/Y à H:i') }}</p>
                </div>
            </div>

            <!-- Résumé statistique -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <div class="summary-cards grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    <div class="summary-card bg-white p-4 rounded-lg border shadow-sm">
                        <div class="label text-xs text-gray-500 uppercase font-medium mb-1">Total Élèves</div>
                        <div class="value text-lg font-bold text-blue-600">{{ number_format($totaux['total_eleves'], 0, ',', ' ') }}</div>
                    </div>
                    <div class="summary-card bg-white p-4 rounded-lg border shadow-sm">
                        <div class="label text-xs text-gray-500 uppercase font-medium mb-1">Inscriptions Validées</div>
                        <div class="value text-lg font-bold text-green-600">{{ number_format($totaux['inscriptions_validees'], 0, ',', ' ') }}</div>
                        <div class="text-xs text-gray-400">{{ number_format($totaux['taux_conversion'], 1) }}% conversion</div>
                    </div>
                    <div class="summary-card bg-white p-4 rounded-lg border shadow-sm">
                        <div class="label text-xs text-gray-500 uppercase font-medium mb-1">Total Recettes</div>
                        <div class="value text-lg font-bold text-indigo-600">{{ number_format($totaux['montant_total'], 0, ',', ' ') }} FCFA</div>
                    </div>
                    <div class="summary-card bg-white p-4 rounded-lg border shadow-sm">
                        <div class="label text-xs text-gray-500 uppercase font-medium mb-1">Montant Moyen</div>
                        <div class="value text-lg font-bold text-purple-600">{{ number_format($totaux['montant_moyen'], 0, ',', ' ') }} FCFA</div>
                    </div>
                    <div class="summary-card bg-white p-4 rounded-lg border shadow-sm">
                        <div class="label text-xs text-gray-500 uppercase font-medium mb-1">Niveaux</div>
                        <div class="value text-lg font-bold text-orange-600">{{ count($graphiques['niveaux']) }}</div>
                        <div class="text-xs text-gray-400">différents</div>
                    </div>
                </div>
            </div>

            <!-- Tableau des données -->
            <div class="px-6 py-4">
                @if(count($details) > 0)
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            <i class="fas fa-table mr-2 text-blue-600"></i>
                            Détail des Inscriptions ({{ number_format(count($details), 0, ',', ' ') }} entrées)
                        </h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Élève</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Niveau/Classe</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Inscription</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant Total</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant Payé</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mode</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Reçu</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($details as $inscription)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $inscription['nom_complet'] }}</div>
                                            <div class="text-xs text-gray-500">{{ $inscription['ine'] }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $inscription['niveau'] }}</div>
                                            <div class="text-xs text-gray-500">{{ $inscription['classe'] }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $inscription['date_inscription'] }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ number_format($inscription['montant_total'], 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-green-600">
                                            {{ number_format($inscription['montant_paye'], 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $inscription['mode_paiement'] }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @switch($inscription['statut_paiement'])
                                                @case('Complet')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <i class="fas fa-check-circle mr-1"></i>Complet
                                                    </span>
                                                @break
                                                @case('Partiel')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        <i class="fas fa-coins mr-1"></i>Partiel
                                                    </span>
                                                @break
                                                @default
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        {{ $inscription['statut_paiement'] }}
                                                    </span>
                                            @endswitch
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $inscription['numero_recu'] ?? '-' }}</td>
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
                        <p class="mt-1 text-sm text-gray-500">Aucune inscription ne correspond aux critères sélectionnés.</p>
                    </div>
                @endif
            </div>

            <!-- Analyse par niveau -->
            @if(count($graphiques['niveaux']) > 0)
                <div class="px-6 py-4 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-chart-pie mr-2 text-green-600"></i>
                        Répartition par Niveau
                    </h3>
                    <div class="chart-section grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                        @foreach($graphiques['niveaux'] as $niveau)
                            <div class="bg-gray-50 rounded-lg p-3 border chart-card">
                                <div class="text-xs font-medium text-gray-900">{{ $niveau['niveau'] }}</div>
                                <div class="mt-1">
                                    <div class="text-lg font-bold text-blue-600">{{ $niveau['count'] }}</div>
                                    <div class="text-xs text-gray-600">{{ number_format($niveau['montant'], 0, ',', ' ') }} FCFA</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Analyse par mode de paiement -->
            @if(count($graphiques['modes_paiement']) > 0)
                <div class="px-6 py-4 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-credit-card mr-2 text-purple-600"></i>
                        Modes de Paiement
                    </h3>
                    <div class="chart-section grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                        @foreach($graphiques['modes_paiement'] as $mode)
                            <div class="bg-gray-50 rounded-lg p-3 border chart-card">
                                <div class="text-xs font-medium text-gray-900">{{ $mode['mode'] }}</div>
                                <div class="mt-1">
                                    <div class="text-lg font-bold text-purple-600">{{ $mode['count'] }}</div>
                                    <div class="text-xs text-gray-600">{{ number_format($mode['montant'], 0, ',', ' ') }} FCFA</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Pied de page -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 text-center">
                <p class="text-xs text-gray-500">
                    © {{ now()->format('Y') }} - {{ $etablissement->nom ?? 'ÉTABLISSEMENT SCOLAIRE' }} - Rapport généré le {{ now()->format('d/m/Y à H:i') }}
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