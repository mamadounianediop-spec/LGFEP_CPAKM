<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Dépenses Services - {{ $etablissement->nom }}</title>
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
        
        .filters-info {
            border: 1px solid #333;
            padding: 10px;
            margin-bottom: 15px;
        }
        
        .filters-title {
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }
        
        .filter-item {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px dotted #ccc;
            padding: 2px 0;
        }
        
        .filter-label {
            font-weight: 500;
        }
        
        .summary {
            border: 1px solid #333;
            padding: 10px;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .summary-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        
        .stat-item {
            text-align: center;
            border: 1px solid #ccc;
            padding: 8px;
        }
        
        .stat-value {
            font-size: 16px;
            font-weight: bold;
        }
        
        .stat-label {
            font-size: 10px;
            text-transform: uppercase;
            margin-top: 2px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 11px;
        }
        
        th, td {
            border: 1px solid #333;
            padding: 6px 8px;
            text-align: left;
        }
        
        th {
            background-color: #f8f8f8;
            font-weight: bold;
        }
        
        .montant {
            text-align: right;
            font-weight: bold;
        }
        
        .type-badge {
            display: inline-block;
            padding: 1px 4px;
            border: 1px solid #333;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
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
            <h1>Rapport des Dépenses Services</h1>
            <p>Généré le {{ now()->format('d/m/Y à H:i') }}</p>
        </div>
    
        <div class="filters-info">
            <div class="filters-title">Filtres appliqués :</div>
            <div class="filters-grid">
                <div class="filter-item">
                    <span class="filter-label">Année scolaire :</span>
                    <span class="filter-value">{{ $filtres['annee'] }}</span>
                </div>
                <div class="filter-item">
                    <span class="filter-label">Mois :</span>
                    <span class="filter-value">{{ $filtres['mois'] }}</span>
                </div>
                <div class="filter-item">
                    <span class="filter-label">Service :</span>
                    <span class="filter-value">{{ $filtres['service'] }}</span>
                </div>
                <div class="filter-item">
                    <span class="filter-label">Catégorie :</span>
                    <span class="filter-value">{{ $filtres['categorie'] }}</span>
                </div>
                <div class="filter-item">
                    <span class="filter-label">Recherche :</span>
                    <span class="filter-value">{{ $filtres['recherche'] }}</span>
                </div>
            </div>
        </div>
    
        <div class="summary">
            <div class="summary-title">Résumé</div>
            <div class="summary-stats">
                <div class="stat-item">
                    <div class="stat-value">{{ $depenses->count() }}</div>
                    <div class="stat-label">Dépenses</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ number_format($depenses->sum('montant'), 0, ',', ' ') }}</div>
                    <div class="stat-label">Montant Total (FCFA)</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $depenses->count() > 0 ? number_format($depenses->avg('montant'), 0, ',', ' ') : 0 }}</div>
                    <div class="stat-label">Moyenne (FCFA)</div>
                </div>
            </div>
        </div>
    
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Service</th>
                <th>Type</th>
                <th>Description</th>
                <th>N° Facture</th>
                <th>Montant (FCFA)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($depenses as $depense)
            <tr>
                <td>{{ \Carbon\Carbon::parse($depense->date_depense)->format('d/m/Y') }}</td>
                <td>{{ $depense->service->nom ?? 'N/A' }}</td>
                <td>
                    <span class="type-badge">
                        {{ ucfirst($depense->type_depense) }}
                    </span>
                </td>
                <td>{{ $depense->description ?: '-' }}</td>
                <td>{{ $depense->numero_facture ?: '-' }}</td>
                <td class="montant">{{ number_format($depense->montant, 0, ',', ' ') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 30px; color: #6b7280;">
                    Aucune dépense trouvée avec les critères spécifiés
                </td>
            </tr>
            @endforelse
        </tbody>
        @if($depenses->count() > 0)
        <tfoot>
            <tr style="font-weight: bold; border-top: 2px solid #333;">
                <td colspan="5" style="text-align: right;">TOTAL GÉNÉRAL :</td>
                <td class="montant">{{ number_format($depenses->sum('montant'), 0, ',', ' ') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
    
        <div class="footer">
            <p>Document généré automatiquement par le système de gestion {{ $etablissement->nom }}</p>
            <p>{{ now()->format('d/m/Y à H:i:s') }}</p>
        </div>
    </div>
</body>
</html>