@extends('layouts.app')

@section('title', 'Aperçu Export Mensualités')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- En-tête avec boutons d'action -->
    <div class="flex items-center justify-between mb-6 print:hidden">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Aperçu Export Mensualités</h1>
            <p class="text-gray-600">{{ $anneeActive->nom }} - {{ count($paiements) }} transaction(s)</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
                <i class="fas fa-print mr-2"></i>Imprimer
            </button>
            <button onclick="downloadPDF()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center">
                <i class="fas fa-download mr-2"></i>Télécharger PDF
            </button>
            <a href="{{ route('mensualites.index', ['tab' => 'historique']) }}" 
               class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
        </div>
    </div>

    <!-- Document principal -->
    <div class="bg-white shadow-lg border-2 border-gray-300 print:shadow-none print:border-none">
        
        @php
            $etablissement = App\Models\Etablissement::first();
        @endphp
        /* Styles pour l'aperçu et l'impression */
        .document-export {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.3;
            color: #333;
        }
        
        /* En-tête compact */
        .header-compact {
            background: linear-gradient(135deg, #198754 0%, #20c997 100%);
            color: white;
            padding: 15px 20px;
            margin: -1px -1px 0 -1px;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .establishment-info h3 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .establishment-details {
            font-size: 10px;
            opacity: 0.9;
        }
        
        .document-title {
            text-align: center;
            flex: 1;
            padding: 0 20px;
        }
        
        .document-title h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .document-subtitle {
            font-size: 12px;
            opacity: 0.9;
        }
        
        .generation-info {
            text-align: right;
            font-size: 10px;
        }
        
        /* Section filtres compacte */
        .filters-compact {
            background: #f8f9fa;
            padding: 12px 20px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .filters-title {
            font-size: 12px;
            font-weight: bold;
            color: #198754;
            margin-bottom: 8px;
        }
        
        .filters-list {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .filter-item {
            font-size: 11px;
        }
        
        .filter-label {
            color: #6c757d;
            font-weight: bold;
        }
        
        .filter-value {
            color: #198754;
        }
        
        /* Statistiques compactes */
        .stats-compact {
            padding: 15px 20px;
            background: #e3f2fd;
            border-bottom: 1px solid #e9ecef;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 15px;
            text-align: center;
        }
        
        .stat-item {
            background: white;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }
        
        .stat-value {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
        
        .stat-label {
            font-size: 9px;
            color: #6c757d;
            margin-top: 2px;
        }
        
        /* Tableau optimisé */
        .table-container {
            padding: 0 20px 20px 20px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        
        .data-table thead {
            background: #198754;
            color: white;
        }
        
        .data-table th {
            padding: 8px 6px;
            font-size: 9px;
            font-weight: bold;
            text-align: left;
            border-right: 1px solid rgba(255,255,255,0.2);
        }
        
        .data-table th:last-child {
            border-right: none;
        }
        
        .data-table td {
            padding: 6px;
            border-bottom: 1px solid #e9ecef;
            border-right: 1px solid #f8f9fa;
            vertical-align: top;
        }
        
        .data-table td:last-child {
            border-right: none;
        }
        
        .data-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        /* Largeurs de colonnes optimisées */
        .col-eleve { width: 18%; }
        .col-classe { width: 10%; }
        .col-ine { width: 12%; }
        .col-mois { width: 10%; }
        .col-montant { width: 12%; text-align: right; }
        .col-statut { width: 10%; text-align: center; }
        .col-mode { width: 10%; }
        .col-date { width: 12%; }
        .col-recu { width: 10%; }
        
        /* Badges de statut */
        .status-badge {
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-badge.complet {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        
        .status-badge.partiel {
            background-color: #fff3cd;
            color: #664d03;
        }
        
        .status-badge.impaye {
            background-color: #f8d7da;
            color: #58151c;
        }
        
        /* Message vide */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }
        
        /* Pied de page */
        .footer-compact {
            background: #f8f9fa;
            padding: 12px 20px;
            text-align: center;
            border-top: 1px solid #e9ecef;
            font-size: 10px;
            color: #6c757d;
        }
        
        /* Impression */
        @media print {
            body { 
                font-size: 10px; 
                line-height: 1.2;
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
            
            .header-compact {
                background: white !important;
                color: #333 !important;
                border-bottom: 2px solid #333;
            }
            
            .filters-compact {
                background: white !important;
                border: 1px solid #ccc;
            }
            
            .stats-compact {
                background: white !important;
                border: 1px solid #ccc;
            }
            
            .data-table thead {
                background: #f0f0f0 !important;
                color: #333 !important;
            }
            
            .status-badge {
                border: 1px solid #333 !important;
            }
            
            @page {
                margin: 0.8cm;
                size: A4 landscape;
            }
        }
        </style>
        
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }
        
        .establishment-info {
            flex: 1;
        }
        
        .establishment-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .establishment-details {
            font-size: 11px;
            opacity: 0.9;
            line-height: 1.5;
        }
        
        .document-title {
            text-align: center;
            flex: 1;
            padding: 0 20px;
        }
        
        .document-title h1 {
            font-size: 22px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 8px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .document-subtitle {
            font-size: 13px;
            opacity: 0.9;
            font-weight: 500;
        }
        
        .generation-info {
            text-align: right;
            font-size: 11px;
            opacity: 0.9;
        }
        
        .generation-date {
            background: rgba(255,255,255,0.2);
            padding: 8px 12px;
            border-radius: 20px;
            margin-top: 10px;
            display: inline-block;
        }
        
        /* Section des filtres */
        .filters-section {
            background: #f8f9fa;
            border-bottom: 3px solid #198754;
            padding: 20px 30px;
        }
        
        .filters-title {
            color: #198754;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .filters-title i {
            margin-right: 8px;
        }
        
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .filter-item {
            background: white;
            padding: 12px 15px;
            border-radius: 8px;
            border-left: 4px solid #198754;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .filter-label {
            font-size: 10px;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 4px;
        }
        
        .filter-value {
            font-size: 12px;
            color: #198754;
            font-weight: 600;
        }
        
        /* Section statistiques */
        .stats-section {
            padding: 25px 30px;
            background: linear-gradient(45deg, #e3f2fd 0%, #f3e5f5 100%);
            border-bottom: 2px solid #e9ecef;
        }
        
        .stats-title {
            color: #1976d2;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            border: 2px solid transparent;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .stat-card.primary { border-color: #0d6efd; }
        .stat-card.success { border-color: #198754; }
        .stat-card.warning { border-color: #ffc107; }
        .stat-card.danger { border-color: #dc3545; }
        .stat-card.info { border-color: #0dcaf0; }
        
        .stat-icon {
            font-size: 24px;
            margin-bottom: 10px;
            color: #6c757d;
        }
        
        .stat-card.primary .stat-icon { color: #0d6efd; }
        .stat-card.success .stat-icon { color: #198754; }
        .stat-card.warning .stat-icon { color: #ffc107; }
        .stat-card.danger .stat-icon { color: #dc3545; }
        .stat-card.info .stat-icon { color: #0dcaf0; }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 11px;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: bold;
        }
        
        /* Tableau des données */
        .data-section {
            padding: 30px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .data-table thead {
            background: linear-gradient(135deg, #198754 0%, #20c997 100%);
            color: white;
        }
        
        .data-table th {
            padding: 15px 10px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
            border-right: 1px solid rgba(255,255,255,0.2);
        }
        
        .data-table th:last-child {
            border-right: none;
        }
        
        .data-table td {
            padding: 12px 10px;
            font-size: 10px;
            border-bottom: 1px solid #e9ecef;
            border-right: 1px solid #f8f9fa;
        }
        
        .data-table td:last-child {
            border-right: none;
        }
        
        .data-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .data-table tbody tr:hover {
            background-color: #e8f5e8;
        }
        
        /* Badges de statut */
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-badge.complet {
            background-color: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
        }
        
        .status-badge.partiel {
            background-color: #fff3cd;
            color: #664d03;
            border: 1px solid #ffecb5;
        }
        
        .status-badge.impaye {
            background-color: #f8d7da;
            color: #58151c;
            border: 1px solid #f5c2c7;
        }
        
        /* Montants */
        .amount {
            font-weight: bold;
            text-align: right;
        }
        
        .amount.positive { color: #198754; }
        .amount.negative { color: #dc3545; }
        
        /* Message vide */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .empty-state h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        
        .empty-state p {
            font-size: 14px;
        }
        
        /* Pied de page */
        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            border-top: 2px solid #e9ecef;
            font-size: 11px;
            color: #6c757d;
        }
        
        .footer-logo {
            font-weight: bold;
            color: #198754;
            margin-bottom: 5px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header-top {
                flex-direction: column;
                text-align: center;
            }
            
            .document-title {
                padding: 20px 0;
            }
            
            .filters-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .data-table {
                font-size: 9px;
            }
            
            .data-table th,
            .data-table td {
                padding: 8px 5px;
            }
        }
        
        /* Impression */
        @media print {
            body {
                background: white;
                padding: 0;
                font-size: 10px;
            }
            
            .document-container {
                box-shadow: none;
                border: 1px solid #333;
                border-radius: 0;
                margin: 0;
            }
            
            .header {
                background: white !important;
                color: #333 !important;
                border-bottom: 2px solid #333;
            }
            
            .header::before {
                display: none;
            }
            
            .filters-section {
                background: white !important;
                border: 1px solid #ccc;
            }
            
            .stats-section {
                background: white !important;
                border: 1px solid #ccc;
            }
            
            .stat-card {
                border: 1px solid #333 !important;
                box-shadow: none;
            }
            
            .data-table tbody tr:hover {
                background-color: transparent;
            }
            
            .data-table thead {
                background: #f0f0f0 !important;
                color: #333 !important;
            }
            
            .status-badge {
                border: 1px solid #333 !important;
            }
            
            @page {
                margin: 0.8cm;
                size: A4 landscape;
            }
            
            /* Cacher certains éléments à l'impression */
            .print-hide {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="document-export border-2 border-green-600">
        
        <!-- En-tête compact -->
        <div class="header-compact">
            <div class="header-content">
                <div class="establishment-info">
                    <h3>{{ $etablissement->nom ?? 'Établissement Scolaire' }}</h3>
                    <div class="establishment-details">
                        @if($etablissement)
                            {{ $etablissement->adresse }} | Tél: {{ $etablissement->telephone }}
                            @if($etablissement->ninea) | NINEA: {{ $etablissement->ninea }} @endif
                        @endif
                    </div>
                </div>
                
                <div class="document-title">
                    <h1>RAPPORT MENSUALITÉS</h1>
                    <div class="document-subtitle">{{ $anneeActive->nom }}</div>
                </div>
                
                <div class="generation-info">
                    Généré le {{ date('d/m/Y à H:i') }}
                </div>
            </div>
        </div>

        <!-- Filtres appliqués (compact) -->
        @if(!empty($filtres) && (isset($filtres['filter_classe']) || isset($filtres['filter_mois']) || isset($filtres['filter_statut']) || isset($filtres['filter_periode']) || isset($filtres['filter_annee'])))
        <div class="filters-compact">
            <div class="filters-title">Filtres appliqués:</div>
            <div class="filters-list">
                @if(isset($filtres['filter_annee']) && $filtres['filter_annee'])
                    <div class="filter-item">
                        <span class="filter-label">Année:</span> 
                        <span class="filter-value">{{ \App\Models\AnneeScolaire::find($filtres['filter_annee'])->nom ?? $filtres['filter_annee'] }}</span>
                    </div>
                @endif
                @if(isset($filtres['filter_classe']) && $filtres['filter_classe'])
                    <div class="filter-item">
                        <span class="filter-label">Classe:</span> 
                        <span class="filter-value">{{ \App\Models\Classe::find($filtres['filter_classe'])->nom ?? $filtres['filter_classe'] }}</span>
                    </div>
                @endif
                @if(isset($filtres['filter_mois']) && $filtres['filter_mois'])
                    <div class="filter-item">
                        <span class="filter-label">Mois:</span> 
                        <span class="filter-value">{{ \App\Models\Mensualite::MOIS[$filtres['filter_mois']] ?? ucfirst($filtres['filter_mois']) }}</span>
                    </div>
                @endif
                @if(isset($filtres['filter_statut']) && $filtres['filter_statut'])
                    <div class="filter-item">
                        <span class="filter-label">Statut:</span> 
                        <span class="filter-value">
                            @if($filtres['filter_statut'] === 'paye') Payés
                            @elseif($filtres['filter_statut'] === 'complet') Complets
                            @elseif($filtres['filter_statut'] === 'partiel') Partiels  
                            @elseif($filtres['filter_statut'] === 'impaye') Impayés
                            @else {{ ucfirst($filtres['filter_statut']) }}
                            @endif
                        </span>
                    </div>
                @endif
                @if(isset($filtres['filter_periode']) && $filtres['filter_periode'])
                    <div class="filter-item">
                        <span class="filter-label">Période:</span> 
                        <span class="filter-value">{{ $filtres['filter_periode'] }} derniers jours</span>
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Statistiques compactes -->
        <div class="stats-compact">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-value">{{ count($paiements) }}</div>
                    <div class="stat-label">Total</div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-value">{{ number_format($paiements->sum('montant_paye'), 0, ',', ' ') }}</div>
                    <div class="stat-label">Encaissé (FCFA)</div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-value">{{ $paiements->where('statut', 'complet')->count() }}</div>
                    <div class="stat-label">Complets</div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-value">{{ $paiements->where('statut', 'partiel')->count() }}</div>
                    <div class="stat-label">Partiels</div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-value">{{ $paiements->where('statut', 'impaye')->count() }}</div>
                    <div class="stat-label">Impayés</div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-value">
                        @php
                            $totalTransactions = $paiements->count();
                            $completTransactions = $paiements->where('statut', 'complet')->count();
                            $tauxRecouvrement = $totalTransactions > 0 ? ($completTransactions / $totalTransactions) * 100 : 0;
                        @endphp
                        {{ number_format($tauxRecouvrement, 1) }}%
                    </div>
                    <div class="stat-label">Taux Recouvrement</div>
                </div>
            </div>
        </div>

        <!-- Tableau optimisé -->
        <div class="table-container">
            @forelse($paiements as $paiement)
                @if($loop->first)
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="col-eleve">Élève</th>
                            <th class="col-classe">Classe</th>
                            <th class="col-ine">INE</th>
                            <th class="col-mois">Mois</th>
                            <th class="col-montant">Montant Dû</th>
                            <th class="col-montant">Montant Payé</th>
                            <th class="col-statut">Statut</th>
                            <th class="col-mode">Mode</th>
                            <th class="col-date">Date</th>
                            <th class="col-recu">N° Reçu</th>
                        </tr>
                    </thead>
                    <tbody>
                @endif
                        <tr>
                            <td class="col-eleve">
                                <div><strong>{{ strtoupper($paiement->inscription->preInscription->nom ?? 'N/A') }}</strong></div>
                                <div style="font-size: 9px; color: #6c757d;">{{ ucwords(strtolower($paiement->inscription->preInscription->prenom ?? 'N/A')) }}</div>
                            </td>
                            <td class="col-classe">
                                <div><strong>{{ $paiement->inscription->classe->nom ?? 'N/A' }}</strong></div>
                                <div style="font-size: 9px; color: #6c757d;">{{ $paiement->inscription->classe->code ?? '' }}</div>
                            </td>
                            <td class="col-ine">{{ $paiement->inscription->preInscription->ine ?? 'N/A' }}</td>
                            <td class="col-mois">{{ $paiement->mois_libelle }}</td>
                            <td class="col-montant">{{ number_format($paiement->montant_du, 0, ',', ' ') }}</td>
                            <td class="col-montant" style="font-weight: bold; color: #198754;">{{ number_format($paiement->montant_paye, 0, ',', ' ') }}</td>
                            <td class="col-statut">
                                <span class="status-badge {{ $paiement->statut }}">
                                    @if($paiement->statut === 'complet') Complet
                                    @elseif($paiement->statut === 'partiel') Partiel
                                    @else Impayé
                                    @endif
                                </span>
                            </td>
                            <td class="col-mode">{{ $paiement->mode_paiement_libelle ?? 'N/A' }}</td>
                            <td class="col-date">
                                @if($paiement->date_paiement)
                                    <div>{{ $paiement->date_paiement->format('d/m/Y') }}</div>
                                    <div style="font-size: 9px; color: #6c757d;">{{ $paiement->date_paiement->format('H:i') }}</div>
                                @else
                                    <span style="color: #dc3545; font-size: 9px;">Non payé</span>
                                @endif
                            </td>
                            <td class="col-recu">
                                @if($paiement->numero_recu)
                                    <span style="font-weight: bold; color: #198754;">{{ $paiement->numero_recu }}</span>
                                @else
                                    <span style="color: #6c757d;">-</span>
                                @endif
                            </td>
                        </tr>
                @if($loop->last)
                    </tbody>
                </table>
                @endif
            @empty
                <div class="empty-state">
                    <div style="font-size: 48px; margin-bottom: 20px; opacity: 0.5;">📊</div>
                    <h3>Aucun paiement trouvé</h3>
                    <p>Aucun paiement ne correspond aux critères de filtrage sélectionnés.</p>
                </div>
            @endforelse
        </div>

        <!-- Pied de page compact -->
        <div class="footer-compact">
            <div><strong>{{ $etablissement->nom ?? 'Système LGFP' }}</strong> - Document généré le {{ date('d/m/Y à H:i:s') }}</div>
            <div style="margin-top: 5px;">
                {{ count($paiements) }} transaction(s) | Total encaissé: {{ number_format($paiements->sum('montant_paye'), 0, ',', ' ') }} FCFA
            </div>
        </div>
    </div>
</div>

<script>
function downloadPDF() {
    const params = new URLSearchParams(window.location.search);
    const downloadUrl = `{{ route('mensualites.download-export-pdf') }}?${params.toString()}`;
    window.open(downloadUrl, '_blank');
}
</script>

<style>
    /* Styles supplémentaires pour l'aperçu */
    .flex-2 {
        flex: 2;
    }
    
    @media print {
        body { 
            font-size: 10px; 
            line-height: 1.2;
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
        
        @page {
            margin: 0.8cm;
            size: A4 landscape;
        }
    }
</style>
@endsection