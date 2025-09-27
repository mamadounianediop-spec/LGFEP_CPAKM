<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport Analytique Services - {{ $etablissement->nom }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            @page {
                size: A4;
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
                padding: 4px 6px !important;
            }
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            background: white;
        }
        
        .container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .logo {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        
        .container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
        }
        
        .filters-info {
            border: 1px solid #333;
            padding: 10px;
            margin-bottom: 15px;
        }
        
        .section {
            margin-bottom: 20px;
            break-inside: avoid;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
            border-bottom: 1px solid #333;
            padding-bottom: 5px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .stat-card {
            border: 1px solid #333;
            padding: 10px;
            text-align: center;
        }
        
        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        
        .stat-label {
            font-size: 10px;
            text-transform: uppercase;
            margin-top: 2px;
        }
        
        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 15px;
        }
        
        .breakdown-table th,
        .breakdown-table td {
            border: 1px solid #333;
            padding: 6px 8px;
            text-align: left;
        }
        
        .breakdown-table th {
            background-color: #f8f8f8;
            font-weight: bold;
        }
        
        .montant {
            text-align: right;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            border-top: 1px solid #333;
            padding-top: 10px;
        }
        
        .print-actions {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <!-- Actions d'impression -->
    <div class="print-actions no-print">
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mr-2">
            <i class="fas fa-print mr-2"></i>Imprimer
        </button>
        <button onclick="window.close()" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
            <i class="fas fa-times mr-2"></i>Fermer
        </button>
    </div>
    
    <div class="container">
        <div class="header">
            <div class="logo">{{ $etablissement->nom }}</div>
            <h1>Rapport Analytique des Services</h1>
            <p>Généré le {{ now()->format('d/m/Y à H:i') }}</p>
        </div>
    
        <div class="filters-info">
            <strong>Filtres Appliqués</strong><br>
            <div style="margin-top: 5px;">
                @if(request('annee_scolaire'))
                    <span><strong>Année:</strong> {{ request('annee_scolaire') }} | </span>
                @endif
                @if(request('mois'))
                    <span><strong>Mois:</strong> {{ ucfirst(request('mois')) }} | </span>
                @endif
                @if(request('service'))
                    <span><strong>Service:</strong> {{ request('service') }} | </span>
                @endif
                @if(request('categorie'))
                    <span><strong>Catégorie:</strong> {{ request('categorie') }} | </span>
                @endif
                @if(request('recherche'))
                    <span><strong>Recherche:</strong> "{{ request('recherche') }}"</span>
                @endif
            </div>
        </div>
    
        <div class="section">
            <div class="section-title">Vue d'ensemble</div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ $stats['total_depenses'] }}</div>
                    <div class="stat-label">Total Dépenses</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($stats['montant_total'], 0, ',', ' ') }}</div>
                    <div class="stat-label">Montant Total (FCFA)</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($stats['moyenne_depense'], 0, ',', ' ') }}</div>
                    <div class="stat-label">Moyenne par Dépense (FCFA)</div>
                </div>
            </div>
        </div>
    
        @if($stats['par_type']->count() > 0)
        <div class="section">
            <div class="section-title">Répartition par Type de Dépense</div>
            <table class="breakdown-table">
                <thead>
                    <tr>
                        <th>Type de Dépense</th>
                        <th>Nombre</th>
                        <th>Montant (FCFA)</th>
                        <th>Pourcentage</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats['par_type'] as $type => $data)
                    <tr>
                        <td>{{ ucfirst($type) }}</td>
                        <td>{{ $data['count'] }}</td>
                        <td class="montant">{{ number_format($data['montant'], 0, ',', ' ') }}</td>
                        <td>{{ $stats['montant_total'] > 0 ? number_format(($data['montant'] / $stats['montant_total']) * 100, 1) : 0 }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    
    @if($stats['par_service']->count() > 0)
    <div class="section">
        <div class="section-title">🔧 Répartition par Service</div>
        <table class="breakdown-table">
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Nombre de Dépenses</th>
                    <th>Montant Total (FCFA)</th>
                    <th>Pourcentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['par_service'] as $service => $data)
                <tr>
                    <td>{{ $service }}</td>
                    <td>{{ $data['count'] }}</td>
                    <td class="montant">{{ number_format($data['montant'], 0, ',', ' ') }}</td>
                    <td>{{ $stats['montant_total'] > 0 ? number_format(($data['montant'] / $stats['montant_total']) * 100, 1) : 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    
    @if($stats['par_mois']->count() > 0)
    <div class="section">
        <div class="section-title">📅 Évolution Mensuelle</div>
        <table class="breakdown-table">
            <thead>
                <tr>
                    <th>Mois</th>
                    <th>Nombre de Dépenses</th>
                    <th>Montant (FCFA)</th>
                    <th>Moyenne (FCFA)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['par_mois'] as $mois => $data)
                <tr>
                    <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $mois)->format('F Y') }}</td>
                    <td>{{ $data['count'] }}</td>
                    <td class="montant">{{ number_format($data['montant'], 0, ',', ' ') }}</td>
                    <td class="montant">{{ number_format($data['count'] > 0 ? $data['montant'] / $data['count'] : 0, 0, ',', ' ') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    
        <div class="section">
            <div class="section-title">Détail des Dépenses</div>
            <table class="breakdown-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Service</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Montant (FCFA)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($depenses->take(50) as $depense)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($depense->date_depense)->format('d/m/Y') }}</td>
                        <td>{{ $depense->service->nom ?? 'N/A' }}</td>
                        <td>{{ ucfirst($depense->type_depense) }}</td>
                        <td>{{ $depense->description ?: '-' }}</td>
                        <td class="montant">{{ number_format($depense->montant, 0, ',', ' ') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 30px;">
                            Aucune dépense trouvée avec les critères spécifiés
                        </td>
                    </tr>
                    @endforelse
                    @if($depenses->count() > 50)
                    <tr>
                        <td colspan="5" style="text-align: center; font-style: italic; color: #6b7280;">
                            ... et {{ $depenses->count() - 50 }} autres dépenses (export complet disponible séparément)
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    
        <div class="footer">
            <p><strong>Résumé :</strong> Ce rapport analyse {{ $stats['total_depenses'] }} dépenses pour un montant total de {{ number_format($stats['montant_total'], 0, ',', ' ') }} FCFA</p>
            <p>Document généré automatiquement par le système de gestion {{ $etablissement->nom }}</p>
            <p>{{ now()->format('d/m/Y à H:i:s') }}</p>
        </div>
    </div>
</body>
</html>