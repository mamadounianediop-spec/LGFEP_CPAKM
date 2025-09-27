<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aperçu Rapport Mensualités - LGFP</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
        color: #333;
        margin: 0;
        padding: 20px;
        background-color: #f8fafc;
    }
    .container {
        max-width: 1200px;
        margin: 0 auto;
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .header {
        text-align: center;
        margin-bottom: 40px;
        border-bottom: 3px solid #3b82f6;
        padding-bottom: 20px;
    }
    .header h1 {
        color: #1f2937;
        margin: 0;
        font-size: 2.5rem;
        font-weight: bold;
    }
    .header p {
        color: #6b7280;
        margin: 10px 0 0 0;
        font-size: 1.1rem;
    }
    .filters-info {
        background: #f1f5f9;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 30px;
        border-left: 4px solid #3b82f6;
    }
    .filters-info h3 {
        margin: 0 0 15px 0;
        color: #1f2937;
        font-size: 1.2rem;
    }
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }
    .filter-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
    }
    .filter-label {
        font-weight: 600;
        color: #374151;
    }
    .filter-value {
        color: #3b82f6;
        font-weight: 500;
    }
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }
    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-left: 5px solid;
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
    }
    .stat-card.blue { border-left-color: #3b82f6; }
    .stat-card.green { border-left-color: #10b981; }
    .stat-card.orange { border-left-color: #f59e0b; }
    .stat-card.red { border-left-color: #ef4444; }
    .stat-card.purple { border-left-color: #8b5cf6; }
    .stat-card.indigo { border-left-color: #6366f1; }
    .stat-title {
        font-size: 0.9rem;
        color: #6b7280;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stat-value {
        font-size: 2rem;
        font-weight: bold;
        margin: 0;
    }
    .stat-value.blue { color: #3b82f6; }
    .stat-value.green { color: #10b981; }
    .stat-value.orange { color: #f59e0b; }
    .stat-value.red { color: #ef4444; }
    .stat-value.purple { color: #8b5cf6; }
    .stat-value.indigo { color: #6366f1; }
    .table-container {
        margin-top: 40px;
        overflow-x: auto;
    }
    .table-header {
        background: #f8fafc;
        padding: 20px;
        border-radius: 8px 8px 0 0;
        border-bottom: 2px solid #e5e7eb;
    }
    .table-header h3 {
        margin: 0;
        color: #1f2937;
        font-size: 1.3rem;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
    }
    th, td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #e5e7eb;
    }
    th {
        background: #f9fafb;
        font-weight: 600;
        color: #374151;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    tr:hover {
        background: #f9fafb;
    }
    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .badge.complet { background: #dcfce7; color: #166534; }
    .badge.partiel { background: #fef3c7; color: #92400e; }
    .badge.impaye { background: #fecaca; color: #991b1b; }
    .print-actions {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
    }
    .btn {
        display: inline-block;
        padding: 10px 20px;
        background: #3b82f6;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-weight: 500;
        margin-left: 10px;
        transition: background-color 0.2s;
    }
    .btn:hover {
        background: #2563eb;
    }
    .btn.secondary {
        background: #6b7280;
    }
    .btn.secondary:hover {
        background: #4b5563;
    }
    @media print {
        .print-actions { display: none; }
        body { background: white; padding: 0; }
        .container { box-shadow: none; }
    }
    .montant {
        font-weight: 600;
    }
    .montant.du { color: #f59e0b; }
    .montant.paye { color: #10b981; }
    .montant.restant { color: #ef4444; }
    .no-data {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
    }
    .no-data i {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.5;
    }
    </style>
</head>
<body>
    <!-- Actions d'impression -->
    <div class="print-actions">
        <button onclick="window.print()" class="btn">
            <i class="fas fa-print mr-2"></i>Imprimer
        </button>
        <button onclick="window.close()" class="btn secondary">
            <i class="fas fa-times mr-2"></i>Fermer
        </button>
    </div>

    <div class="container">
        <!-- En-tête -->
        <div class="header">
            <h1><i class="fas fa-chart-line mr-3"></i>Rapport des Mensualités</h1>
            <p>Analyse détaillée des paiements de mensualités</p>
            <p style="font-size: 0.9rem; color: #9ca3af;">
                Généré le {{ now()->format('d/m/Y à H:i') }}
            </p>
        </div>

        <!-- Informations sur les filtres appliqués -->
        <div class="filters-info">
            <h3><i class="fas fa-filter mr-2"></i>Critères de Filtrage</h3>
            <div class="filter-grid">
                @if(isset($filtres['annee']))
                    <div class="filter-item">
                        <span class="filter-label">Année scolaire :</span>
                        <span class="filter-value">{{ $mensualites->first()?->anneeScolaire?->nom ?? 'N/A' }}</span>
                    </div>
                @endif
                @if(isset($filtres['mois']) && $filtres['mois'])
                    <div class="filter-item">
                        <span class="filter-label">Mois :</span>
                        <span class="filter-value">{{ ucfirst($filtres['mois']) }}</span>
                    </div>
                @endif
                @if(isset($filtres['dateDebut']) && $filtres['dateDebut'])
                    <div class="filter-item">
                        <span class="filter-label">Du :</span>
                        <span class="filter-value">{{ \Carbon\Carbon::parse($filtres['dateDebut'])->format('d/m/Y') }}</span>
                    </div>
                @endif
                @if(isset($filtres['dateFin']) && $filtres['dateFin'])
                    <div class="filter-item">
                        <span class="filter-label">Au :</span>
                        <span class="filter-value">{{ \Carbon\Carbon::parse($filtres['dateFin'])->format('d/m/Y') }}</span>
                    </div>
                @endif
                @if(isset($filtres['statut']) && $filtres['statut'])
                    <div class="filter-item">
                        <span class="filter-label">Statut :</span>
                        <span class="filter-value">
                            @switch($filtres['statut'])
                                @case('complet') Payé intégralement @break
                                @case('partiel') Paiement partiel @break
                                @case('impaye') Impayé @break
                                @default {{ $filtres['statut'] }}
                            @endswitch
                        </span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Statistiques globales -->
        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-title">Élèves Inscrits</div>
                <div class="stat-value blue">{{ $totaux['eleves_total'] }}</div>
                @if(isset($totaux['eleves_avec_mensualites']))
                    <div style="font-size: 0.8rem; color: #6b7280; margin-top: 5px;">
                        {{ $totaux['eleves_avec_mensualites'] }} avec mensualités
                    </div>
                @endif
            </div>
            <div class="stat-card orange">
                <div class="stat-title">Montant Total Dû</div>
                <div class="stat-value orange">{{ number_format($totaux['montant_total_du'], 0, ',', ' ') }} FCFA</div>
            </div>
            <div class="stat-card green">
                <div class="stat-title">Montant Total Payé</div>
                <div class="stat-value green">{{ number_format($totaux['montant_total_paye'], 0, ',', ' ') }} FCFA</div>
            </div>
            <div class="stat-card red">
                <div class="stat-title">Montant Restant</div>
                <div class="stat-value red">{{ number_format($totaux['montant_restant'], 0, ',', ' ') }} FCFA</div>
            </div>
            <div class="stat-card purple">
                <div class="stat-title">Taux de Paiement</div>
                <div class="stat-value purple">{{ $totaux['pourcentage_paiement'] }}%</div>
            </div>
            <div class="stat-card indigo">
                <div class="stat-title">Mensualités Total</div>
                <div class="stat-value indigo">{{ $totaux['mensualites_total'] }}</div>
            </div>
        </div>

        <!-- Tableau détaillé -->
        <div class="table-container">
            <div class="table-header">
                <h3><i class="fas fa-table mr-2"></i>Détail des Mensualités ({{ $mensualites->count() }} entrées)</h3>
            </div>
            
            @if($mensualites->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Élève</th>
                            <th>Classe</th>
                            <th>Mois</th>
                            <th>Montant Dû</th>
                            <th>Montant Payé</th>
                            <th>Restant</th>
                            <th>Mode Paiement</th>
                            <th>Date Paiement</th>
                            <th>Statut</th>
                            <th>N° Reçu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mensualites as $mensualite)
                            <tr>
                                <td>
                                    <strong>{{ $mensualite->inscription->preInscription->prenom }} {{ $mensualite->inscription->preInscription->nom }}</strong>
                                    <br><small style="color: #6b7280;">{{ $mensualite->inscription->preInscription->ine }}</small>
                                </td>
                                <td>{{ $mensualite->inscription->classe->nom ?? 'N/A' }}</td>
                                <td>{{ $mensualite->mois_libelle }}</td>
                                <td>
                                    <span class="montant du">{{ number_format($mensualite->montant_du, 0, ',', ' ') }} FCFA</span>
                                </td>
                                <td>
                                    <span class="montant paye">{{ number_format($mensualite->montant_paye, 0, ',', ' ') }} FCFA</span>
                                </td>
                                <td>
                                    <span class="montant restant">{{ number_format($mensualite->solde_restant, 0, ',', ' ') }} FCFA</span>
                                </td>
                                <td>{{ $mensualite->mode_paiement_libelle ?? '-' }}</td>
                                <td>{{ $mensualite->date_paiement?->format('d/m/Y') ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $mensualite->statut }}">
                                        @switch($mensualite->statut)
                                            @case('complet') Payé @break
                                            @case('partiel') Partiel @break
                                            @case('impaye') Impayé @break
                                            @default {{ $mensualite->statut }}
                                        @endswitch
                                    </span>
                                </td>
                                <td>{{ $mensualite->numero_recu ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-data">
                    <i class="fas fa-table"></i>
                    <h3>Aucune donnée disponible</h3>
                    <p>Aucune mensualité ne correspond aux critères sélectionnés.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Ajuster automatiquement l'affichage pour l'impression
        window.addEventListener('beforeprint', function() {
            document.body.style.fontSize = '12px';
        });
        
        window.addEventListener('afterprint', function() {
            document.body.style.fontSize = '';
        });
    </script>
</body>
</html>