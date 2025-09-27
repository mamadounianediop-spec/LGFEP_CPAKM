@extends('rapports.base')

@section('title', $titre)

@section('content')
    <div class="info-section">
        <h3 style="margin-top: 0;">Résumé de {{ $periode }}</h3>
        <div class="info-row">
            <span class="info-label">Nombre de paiements :</span>
            <span class="info-value">{{ $total_paiements }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Montant total encaissé :</span>
            <span class="info-value montant">{{ number_format($montant_total, 0, ',', ' ') }} FCFA</span>
        </div>
    </div>

    @if($mensualites->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>N° Reçu</th>
                    <th>Élève</th>
                    <th>Classe</th>
                    <th>Mois</th>
                    <th class="text-right">Montant</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mensualites as $mensualite)
                    <tr>
                        <td>{{ $mensualite->date_paiement->format('d/m/Y') }}</td>
                        <td>{{ $mensualite->numero_recu }}</td>
                        <td>{{ $mensualite->inscription->preInscription->nom }} {{ $mensualite->inscription->preInscription->prenom }}</td>
                        <td>{{ $mensualite->inscription->classe->nom ?? 'N/A' }}</td>
                        <td>{{ $mensualite->mois }}</td>
                        <td class="text-right montant">{{ number_format($mensualite->montant_paye, 0, ',', ' ') }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="5" class="text-right">TOTAL :</td>
                    <td class="text-right">{{ number_format($montant_total, 0, ',', ' ') }} FCFA</td>
                </tr>
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p>Aucun paiement effectué ce mois-ci.</p>
        </div>
    @endif
@endsection