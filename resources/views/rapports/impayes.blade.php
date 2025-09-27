@extends('rapports.base')

@section('title', $titre)

@section('content')
    <div class="info-section">
        <h3 style="margin-top: 0;">Résumé des Impayés</h3>
        <div class="info-row">
            <span class="info-label">Nombre total d'impayés :</span>
            <span class="info-value">{{ $total_impayes }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Montant total dû :</span>
            <span class="info-value" style="color: #dc3545; font-weight: bold;">{{ number_format($montant_total_impaye, 0, ',', ' ') }} FCFA</span>
        </div>
    </div>

    @if($mensualites->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Élève</th>
                    <th>Classe</th>
                    <th>Mois</th>
                    <th class="text-right">Montant Dû</th>
                    <th class="text-center">Durée du Retard</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mensualites as $mensualite)
                    <tr>
                        <td>{{ $mensualite->inscription->preInscription->nom }} {{ $mensualite->inscription->preInscription->prenom }}</td>
                        <td>{{ $mensualite->inscription->classe->nom ?? 'N/A' }}</td>
                        <td>{{ $mensualite->mois }}</td>
                        <td class="text-right" style="color: #dc3545; font-weight: bold;">{{ number_format($mensualite->montant_du, 0, ',', ' ') }}</td>
                        <td class="text-center">
                            @php
                                $dateEcheance = \Carbon\Carbon::parse($mensualite->date_echeance ?? $mensualite->created_at);
                                $maintenant = \Carbon\Carbon::now();
                                $retard = $dateEcheance->diffInDays($maintenant);
                            @endphp
                            {{ $retard }} jour(s)
                        </td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3" class="text-right">TOTAL :</td>
                    <td class="text-right">{{ number_format($montant_total_impaye, 0, ',', ' ') }} FCFA</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 40px; color: #28a745;">
            <p>Aucun impayé trouvé. Félicitations !</p>
        </div>
    @endif
@endsection