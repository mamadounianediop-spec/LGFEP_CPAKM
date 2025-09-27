<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Rapport')</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        
        .header .subtitle {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }
        
        .info-section {
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            color: #333;
        }
        
        .info-value {
            color: #666;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 11px;
        }
        
        td {
            font-size: 10px;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .total-row {
            background-color: #e9ecef;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 10px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .montant {
            font-weight: bold;
            color: #28a745;
        }
        
        .statut-paye {
            color: #28a745;
            font-weight: bold;
        }
        
        .statut-impaye {
            color: #dc3545;
            font-weight: bold;
        }
        
        .statut-partiel {
            color: #ffc107;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>@yield('title')</h1>
        <div class="subtitle">Année Scolaire : {{ $annee ?? '' }}</div>
        <div class="subtitle">Généré le : {{ date('d/m/Y à H:i') }}</div>
    </div>

    @yield('content')

    <div class="footer">
        <p>Système de Gestion Scolaire - {{ config('app.name') }}</p>
    </div>
</body>
</html>