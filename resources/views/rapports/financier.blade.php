@extends('rapports.base')

@section('title', $titre)

@section('content')
    <div class="info-section">
        <h3 style="margin-top: 0;">Synthèse Financière</h3>
        <div class="info-row">
            <span class="info-label">Total mensualités générées :</span>
            <span class="info-value">{{ $total_mensualites }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Mensualités payées :</span>
            <span class="info-value" style="color: #28a745;">{{ $total_payes }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Mensualités impayées :</span>
            <span class="info-value" style="color: #dc3545;">{{ $total_impayes }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Taux de paiement :</span>
            <span class="info-value" style="font-weight: bold;">{{ $taux_paiement }}%</span>
        </div>
    </div>

    <div class="info-section">
        <h3 style="margin-top: 0;">Montants</h3>
        <div class="info-row">
            <span class="info-label">Montant total dû :</span>
            <span class="info-value">{{ number_format($montant_du, 0, ',', ' ') }} FCFA</span>
        </div>
        <div class="info-row">
            <span class="info-label">Montant total encaissé :</span>
            <span class="info-value montant">{{ number_format($montant_total, 0, ',', ' ') }} FCFA</span>
        </div>
        <div class="info-row">
            <span class="info-label">Reste à encaisser :</span>
            <span class="info-value" style="color: #dc3545; font-weight: bold;">{{ number_format($montant_du - $montant_total, 0, ',', ' ') }} FCFA</span>
        </div>
    </div>

    <!-- Graphique simple en texte -->
    <div style="margin-top: 20px;">
        <h4>Répartition des paiements</h4>
        <div style="display: flex; align-items: center; margin-bottom: 10px;">
            <div style="width: 20px; height: 20px; background-color: #28a745; margin-right: 10px;"></div>
            <span>Payé ({{ $taux_paiement }}%)</span>
        </div>
        <div style="display: flex; align-items: center;">
            <div style="width: 20px; height: 20px; background-color: #dc3545; margin-right: 10px;"></div>
            <span>Impayé ({{ 100 - $taux_paiement }}%)</span>
        </div>
    </div>
@endsection