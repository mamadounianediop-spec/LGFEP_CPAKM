<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aperçu Rapport - CPAKM</title>
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
                justify-content: space-between !important;
                margin: 10px 0 !important;
            }
            .summary-card {
                border: 1px solid #ddd !important;
                padding: 8px !important;
                text-align: center !important;
                width: 23% !important;
                background: #f9f9f9 !important;
            }
            .summary-card .label {
                font-size: 8px !important;
                margin-bottom: 2px !important;
            }
            .summary-card .value {
                font-size: 11px !important;
                font-weight: bold !important;
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
                    <h1 class="text-2xl font-bold text-gray-900">Aperçu du Rapport</h1>
                    <p class="text-sm text-gray-600 mt-1">
                        @if($filtres['annee'])Année: {{ $filtres['annee'] }}-{{ $filtres['annee'] + 1 }}@endif
                        @if($filtres['mois']) - {{ $nomMois[$filtres['mois']] ?? 'Mois ' . $filtres['mois'] }}@endif
                        @if($filtres['dateDebut']) - Du {{ $filtres['dateDebut'] }}@endif
                        @if($filtres['dateFin'] && $filtres['dateDebut']) au {{ $filtres['dateFin'] }}@endif
                        @if($filtres['statut']) - Statut: {{ $filtres['statut'] === 'paye' ? 'Payé' : 'En attente' }}@endif
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
                <h2 class="text-lg font-semibold text-gray-700 mb-4">RAPPORT DES ÉTATS DE PAIEMENT</h2>
                <div class="text-sm text-gray-600 space-y-1">
                    @if($filtres['annee'])
                        <p><strong>Année scolaire:</strong> {{ $filtres['annee'] }}-{{ $filtres['annee'] + 1 }}</p>
                    @endif
                    @if($filtres['mois'])
                        <p><strong>Mois:</strong> {{ $nomMois[$filtres['mois']] ?? 'Mois ' . $filtres['mois'] }}</p>
                    @endif
                    @if($filtres['dateDebut'] || $filtres['dateFin'])
                        <p><strong>Période:</strong> 
                            @if($filtres['dateDebut']) du {{ $filtres['dateDebut'] }}@endif
                            @if($filtres['dateFin'] && $filtres['dateDebut']) au {{ $filtres['dateFin'] }}@endif
                        </p>
                    @endif
                    @if($filtres['statut'])
                        <p><strong>Statut:</strong> {{ $filtres['statut'] === 'paye' ? 'Payé' : 'En attente' }}</p>
                    @endif
                    <p><strong>Date d'édition:</strong> {{ date('d/m/Y à H:i') }}</p>
                </div>
            </div>

            <!-- Résumé des totaux -->
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Résumé</h3>
                <div class="summary-cards grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="summary-card text-center bg-blue-50 rounded-lg p-3 border border-blue-200">
                        <div class="label text-xs text-blue-600 font-medium">Personnel Total</div>
                        <div class="value text-xl font-bold text-blue-700">{{ $totaux['personnel'] }}</div>
                    </div>
                    <div class="summary-card text-center bg-green-50 rounded-lg p-3 border border-green-200">
                        <div class="label text-xs text-green-600 font-medium">Montant Total</div>
                        <div class="value text-lg font-bold text-green-700">{{ number_format($totaux['net_total'], 0, ',', ' ') }} FCFA</div>
                    </div>
                    <div class="summary-card text-center bg-red-50 rounded-lg p-3 border border-red-200">
                        <div class="label text-xs text-red-600 font-medium">Total Retenues</div>
                        <div class="value text-lg font-bold text-red-700">{{ number_format($totaux['total_retenues'], 0, ',', ' ') }} FCFA</div>
                    </div>
                    <div class="summary-card text-center bg-yellow-50 rounded-lg p-3 border border-yellow-200">
                        <div class="label text-xs text-yellow-600 font-medium">Total Avances</div>
                        <div class="value text-lg font-bold text-yellow-700">{{ number_format($totaux['total_avances'], 0, ',', ' ') }} FCFA</div>
                    </div>
                </div>
            </div>

            <!-- Tableau détaillé -->
            <div class="px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Détail du Personnel</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="border border-gray-300 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Personnel</th>
                                <th class="border border-gray-300 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fonction</th>
                                <th class="border border-gray-300 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mode</th>
                                <th class="border border-gray-300 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heures</th>
                                <th class="border border-gray-300 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Primes</th>
                                <th class="border border-gray-300 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Retenues</th>
                                <th class="border border-gray-300 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="border border-gray-300 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avances</th>
                                <th class="border border-gray-300 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Restant</th>
                                <th class="border border-gray-300 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($etats as $etat)
                            <tr class="hover:bg-gray-50">
                                <td class="border border-gray-300 px-3 py-2 text-sm font-medium text-gray-900">{{ $etat->personnel->prenom }} {{ $etat->personnel->nom }}</td>
                                <td class="border border-gray-300 px-3 py-2 text-sm text-gray-700 text-center">{{ ucfirst($etat->personnel->type_personnel) }}</td>
                                <td class="border border-gray-300 px-3 py-2 text-sm text-gray-700 text-center">{{ $etat->personnel->mode_paiement === 'fixe' ? 'Fixe' : 'Horaire' }}</td>
                                <td class="border border-gray-300 px-3 py-2 text-sm text-gray-700 text-center">{{ $etat->heures_effectuees ?: '-' }}</td>
                                <td class="border border-gray-300 px-3 py-2 text-sm text-gray-900 text-right">{{ number_format($etat->primes, 0, ',', ' ') }}</td>
                                <td class="border border-gray-300 px-3 py-2 text-sm text-red-600 text-right font-medium">{{ number_format($etat->retenues, 0, ',', ' ') }}</td>
                                <td class="border border-gray-300 px-3 py-2 text-sm text-gray-900 text-right font-semibold">{{ number_format($etat->montant_total, 0, ',', ' ') }}</td>
                                <td class="border border-gray-300 px-3 py-2 text-sm text-yellow-600 text-right font-medium">{{ number_format($etat->avances, 0, ',', ' ') }}</td>
                                <td class="border border-gray-300 px-3 py-2 text-sm text-right font-semibold {{ $etat->restant > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ number_format($etat->restant, 0, ',', ' ') }}
                                </td>
                                <td class="border border-gray-300 px-3 py-2 text-sm text-center">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $etat->statut_paiement === 'paye' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $etat->statut_paiement === 'paye' ? 'Payé' : 'En attente' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100">
                            <tr class="font-bold">
                                <td colspan="4" class="border border-gray-300 px-3 py-2 text-right text-sm text-gray-900">TOTAUX :</td>
                                <td class="border border-gray-300 px-3 py-2 text-right text-sm text-gray-900">{{ number_format($etats->sum('primes'), 0, ',', ' ') }}</td>
                                <td class="border border-gray-300 px-3 py-2 text-right text-sm text-red-600">{{ number_format($totaux['total_retenues'], 0, ',', ' ') }}</td>
                                <td class="border border-gray-300 px-3 py-2 text-right text-sm text-gray-900">{{ number_format($totaux['net_total'], 0, ',', ' ') }}</td>
                                <td class="border border-gray-300 px-3 py-2 text-right text-sm text-yellow-600">{{ number_format($totaux['total_avances'], 0, ',', ' ') }}</td>
                                <td class="border border-gray-300 px-3 py-2 text-right text-sm text-gray-900">{{ number_format($totaux['total_restant'], 0, ',', ' ') }}</td>
                                <td class="border border-gray-300 px-3 py-2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Pied de page -->
            <div class="px-6 py-4 border-t border-gray-200 text-center text-sm text-gray-500">
                <p>© {{ date('Y') }} - Cours Privés Abdou Khadre Mbacké - Rapport généré le {{ date('d/m/Y à H:i') }}</p>
            </div>
        </div>
    </div>
</body>
</html>