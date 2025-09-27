<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Export Mensualités - {{ $anneeActive->nom }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.2;
            color: #333;
        }
        
        .document {
            width: 100%;
            border: 1px solid #333;
        }
        
        /* En-tête compact */
        .header {
            background: #f0f0f0;
            padding: 10px 15px;
            border-bottom: 2px solid #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .establishment-info h3 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .establishment-details {
            font-size: 8px;
        }
        
        .document-title {
            text-align: center;
            flex: 1;
            padding: 0 20px;
        }
        
        .document-title h1 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .document-subtitle {
            font-size: 10px;
        }
        
        .generation-info {
            text-align: right;
            font-size: 8px;
        }
        
        /* Filtres */
        .filters {
            padding: 8px 15px;
            background: #f8f9fa;
            border-bottom: 1px solid #ddd;
            font-size: 9px;
        }
        
        .filters-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .filters-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .filter-item {
            font-size: 8px;
        }
        
        .filter-label {
            font-weight: bold;
        }
        
        /* Statistiques */
        .stats {
            padding: 8px 15px;
            background: #e3f2fd;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-around;
            text-align: center;
        }
        
        .stat-item {
            font-size: 8px;
        }
        
        .stat-value {
            font-size: 12px;
            font-weight: bold;
        }
        
        .stat-label {
            font-size: 7px;
            color: #666;
        }
        
        /* Tableau */
        .table-container {
            padding: 10px 15px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }
        
        .data-table th {
            background: #333;
            color: white;
            padding: 5px 3px;
            font-size: 7px;
            font-weight: bold;
            text-align: left;
            border: 1px solid #666;
        }
        
        .data-table td {
            padding: 4px 3px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        
        .data-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        /* Largeurs colonnes */
        .col-eleve { width: 18%; }
        .col-classe { width: 10%; }
        .col-ine { width: 12%; }
        .col-mois { width: 10%; }
        .col-montant { width: 12%; text-align: right; }
        .col-statut { width: 8%; text-align: center; }
        .col-mode { width: 10%; }
        .col-date { width: 10%; }
        .col-recu { width: 10%; }
        
        .status-badge {
            padding: 1px 4px;
            border-radius: 6px;
            font-size: 6px;
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
        
        .footer {
            padding: 8px 15px;
            text-align: center;
            border-top: 1px solid #ddd;
            background: #f8f9fa;
            font-size: 8px;
            color: #666;
        }
        
        @page {
            margin: 0.8cm;
            size: A4 landscape;
        }
    </style>
</head>
<body>
    @php
        $etablissement = App\Models\Etablissement::first();
    @endphp
    
    <div class="document">
        <!-- En-tête -->
        <div class="header">
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
        
        <!-- Filtres -->
        @if(!empty($filtres) && (isset($filtres['filter_classe']) || isset($filtres['filter_mois']) || isset($filtres['filter_statut']) || isset($filtres['filter_periode']) || isset($filtres['filter_annee'])))
        <div class="filters">
            <div class="filters-title">Filtres appliqués:</div>
            <div class="filters-list">
                @if(isset($filtres['filter_annee']) && $filtres['filter_annee'])
                    <div class="filter-item">
                        <span class="filter-label">Année:</span> {{ \App\Models\AnneeScolaire::find($filtres['filter_annee'])->nom ?? $filtres['filter_annee'] }}
                    </div>
                @endif
                @if(isset($filtres['filter_classe']) && $filtres['filter_classe'])
                    <div class="filter-item">
                        <span class="filter-label">Classe:</span> {{ \App\Models\Classe::find($filtres['filter_classe'])->nom ?? $filtres['filter_classe'] }}
                    </div>
                @endif
                @if(isset($filtres['filter_mois']) && $filtres['filter_mois'])
                    <div class="filter-item">
                        <span class="filter-label">Mois:</span> {{ \App\Models\Mensualite::MOIS[$filtres['filter_mois']] ?? ucfirst($filtres['filter_mois']) }}
                    </div>
                @endif
                @if(isset($filtres['filter_statut']) && $filtres['filter_statut'])
                    <div class="filter-item">
                        <span class="filter-label">Statut:</span> 
                        @if($filtres['filter_statut'] === 'paye') Payés
                        @elseif($filtres['filter_statut'] === 'complet') Complets
                        @elseif($filtres['filter_statut'] === 'partiel') Partiels  
                        @elseif($filtres['filter_statut'] === 'impaye') Impayés
                        @else {{ ucfirst($filtres['filter_statut']) }}
                        @endif
                    </div>
                @endif
                @if(isset($filtres['filter_periode']) && $filtres['filter_periode'])
                    <div class="filter-item">
                        <span class="filter-label">Période:</span> {{ $filtres['filter_periode'] }} derniers jours
                    </div>
                @endif
            </div>
        </div>
        @endif
        
        <!-- Statistiques -->
        <div class="stats">
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
        
        <!-- Tableau -->
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
                            <th class="col-montant">M. Dû</th>
                            <th class="col-montant">M. Payé</th>
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
                                <strong>{{ strtoupper($paiement->inscription->preInscription->nom ?? 'N/A') }}</strong><br>
                                <span style="font-size: 7px;">{{ ucwords(strtolower($paiement->inscription->preInscription->prenom ?? 'N/A')) }}</span>
                            </td>
                            <td class="col-classe">
                                <strong>{{ $paiement->inscription->classe->nom ?? 'N/A' }}</strong><br>
                                <span style="font-size: 7px;">{{ $paiement->inscription->classe->code ?? '' }}</span>
                            </td>
                            <td class="col-ine">{{ $paiement->inscription->preInscription->ine ?? 'N/A' }}</td>
                            <td class="col-mois">{{ $paiement->mois_libelle }}</td>
                            <td class="col-montant">{{ number_format($paiement->montant_du, 0, ',', ' ') }}</td>
                            <td class="col-montant" style="font-weight: bold;">{{ number_format($paiement->montant_paye, 0, ',', ' ') }}</td>
                            <td class="col-statut">
                                <span class="status-badge {{ $paiement->statut }}">
                                    @if($paiement->statut === 'complet') OK
                                    @elseif($paiement->statut === 'partiel') PARTIEL
                                    @else IMPAYE
                                    @endif
                                </span>
                            </td>
                            <td class="col-mode">{{ $paiement->mode_paiement_libelle ?? 'N/A' }}</td>
                            <td class="col-date">
                                @if($paiement->date_paiement)
                                    {{ $paiement->date_paiement->format('d/m/Y') }}<br>
                                    <span style="font-size: 7px;">{{ $paiement->date_paiement->format('H:i') }}</span>
                                @else
                                    <span style="color: #dc3545; font-size: 7px;">Non payé</span>
                                @endif
                            </td>
                            <td class="col-recu">
                                @if($paiement->numero_recu)
                                    <strong>{{ $paiement->numero_recu }}</strong>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                @if($loop->last)
                    </tbody>
                </table>
                @endif
            @empty
                <div style="text-align: center; padding: 40px; color: #666;">
                    <div style="font-size: 24px; margin-bottom: 10px;">📊</div>
                    <div>Aucun paiement trouvé</div>
                </div>
            @endforelse
        </div>
        
        <!-- Pied de page -->
        <div class="footer">
            <strong>{{ $etablissement->nom ?? 'Système LGFP' }}</strong> - Document généré le {{ date('d/m/Y à H:i:s') }} | 
            {{ count($paiements) }} transaction(s) | Total: {{ number_format($paiements->sum('montant_paye'), 0, ',', ' ') }} FCFA
        </div>
    </div>
</body>
</html>