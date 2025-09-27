@extends('rapports.base')

@section('title', $titre)

@section('content')
    <div class="info-section">
        <h3 style="margin-top: 0;">Résumé du Rapport</h3>
        <div class="info-row">
            <span class="info-label">Nombre total de paiements :</span>
            <span class="info-value">{{ $total_paiements }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Montant total :</span>
            <span class="info-value montant">{{ number_format($montant_total, 0, ',', ' ') }} FCFA</span>
        </div>
        
        @if(!empty($filtres))
            <h4>Filtres appliqués :</h4>
            @if(!empty($filtres['periode_selectionnee']))
                <div class="info-row">
                    <span class="info-label">Période :</span>
                    <span class="info-value">
                        @php
                            $periodesLabels = [
                                'aujourd_hui' => 'Aujourd\'hui',
                                'cette_semaine' => 'Cette semaine',
                                'ce_mois' => 'Ce mois',
                                'mois_dernier' => 'Mois dernier',
                                'ce_trimestre' => 'Ce trimestre',
                                'cette_annee' => 'Cette année scolaire',
                                'personnalisee' => 'Période personnalisée'
                            ];
                        @endphp
                        {{ $periodesLabels[$filtres['periode_selectionnee']] ?? $filtres['periode_selectionnee'] }}
                    </span>
                </div>
            @endif
            @if(!empty($filtres['statut_selectionne']) && $filtres['statut_selectionne'] !== 'tous')
                <div class="info-row">
                    <span class="info-label">Statut de paiement :</span>
                    <span class="info-value">
                        @php
                            $statutsLabels = [
                                'paye' => 'Payé intégralement',
                                'partiel' => 'Partiellement payé',
                                'impaye' => 'Impayé'
                            ];
                        @endphp
                        {{ $statutsLabels[$filtres['statut_selectionne']] ?? $filtres['statut_selectionne'] }}
                    </span>
                </div>
            @endif
            @if(!empty($filtres['date_debut']))
                <div class="info-row">
                    <span class="info-label">Date de début :</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($filtres['date_debut'])->format('d/m/Y') }}</span>
                </div>
            @endif
            @if(!empty($filtres['date_fin']))
                <div class="info-row">
                    <span class="info-label">Date de fin :</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($filtres['date_fin'])->format('d/m/Y') }}</span>
                </div>
            @endif
            @if(!empty($filtres['classe_id']))
                <div class="info-row">
                    <span class="info-label">Classe :</span>
                    <span class="info-value">{{ \App\Models\Classe::find($filtres['classe_id'])->nom ?? 'N/A' }}</span>
                </div>
            @endif
            @if(!empty($filtres['niveau_id']))
                <div class="info-row">
                    <span class="info-label">Niveau :</span>
                    <span class="info-value">{{ \App\Models\Niveau::find($filtres['niveau_id'])->nom ?? 'N/A' }}</span>
                </div>
            @endif
        @endif
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
                    <th class="text-right">Montant Dû</th>
                    <th class="text-right">Montant Payé</th>
                    <th class="text-center">Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mensualites as $mensualite)
                    <tr>
                        <td>{{ $mensualite->date_paiement ? $mensualite->date_paiement->format('d/m/Y') : '-' }}</td>
                        <td>{{ $mensualite->numero_recu ?? '-' }}</td>
                        <td>{{ $mensualite->inscription->preInscription->nom }} {{ $mensualite->inscription->preInscription->prenom }}</td>
                        <td>{{ $mensualite->inscription->classe->nom ?? 'N/A' }}</td>
                        <td>{{ $mensualite->mois }}</td>
                        <td class="text-right">{{ number_format($mensualite->montant_du, 0, ',', ' ') }}</td>
                        <td class="text-right montant">{{ number_format($mensualite->montant_paye, 0, ',', ' ') }}</td>
                        <td class="text-center">
                            <span class="statut-{{ $mensualite->statut }}">
                                {{ ucfirst($mensualite->statut) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="6" class="text-right">TOTAL :</td>
                    <td class="text-right">{{ number_format($montant_total, 0, ',', ' ') }} FCFA</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p>Aucun paiement trouvé avec les critères sélectionnés.</p>
        </div>
    @endif
@endsection