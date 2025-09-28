@extends('layouts.app')

@section('title', 'Comparaison des Systèmes de Rapport')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">
                        <i class="fas fa-balance-scale"></i>
                        COMPARAISON : Ancien vs Nouveau Système de Rapport
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('rapport.global') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-eye"></i> Voir Ancien
                        </a>
                        <a href="{{ route('rapport.nouveau-systeme') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-eye"></i> Voir Nouveau
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    
                    <!-- Vue d'ensemble -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h4><i class="fas fa-info-circle"></i> Résumé de la Comparaison</h4>
                                <p><strong>Ancien système :</strong> Utilise des estimations et des champs parfois inexistants dans la DB</p>
                                <p><strong>Nouveau système :</strong> Basé sur l'analyse complète de la structure DB réelle - UNIQUEMENT des données qui existent</p>
                            </div>
                        </div>
                    </div>

                    <!-- Comparaison technique -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-warning">
                                <div class="card-header bg-warning">
                                    <h4><i class="fas fa-exclamation-triangle"></i> ANCIEN SYSTÈME</h4>
                                </div>
                                <div class="card-body">
                                    <h5>📅 Période</h5>
                                    <p>• Mois scolaires (Octobre → Septembre)</p>
                                    <p>• Logique complexe avec distinction réel/prévisionnel</p>
                                    
                                    <h5>💰 Calculs</h5>
                                    <p>• <span class="text-danger">Estimations personnel artificielles</span></p>
                                    <p>• <span class="text-danger">Utilise des champs inexistants (montant_mensuel)</span></p>
                                    <p>• Logique temporelle complexe</p>
                                    
                                    <h5>🗃️ Structure DB</h5>
                                    <p>• <span class="text-danger">Assume des relations qui n'existent pas</span></p>
                                    <p>• Utilise 'fonction' au lieu de 'type_personnel'</p>
                                    <p>• Calculs basés sur suppositions</p>
                                    
                                    <h5>⚠️ Problèmes</h5>
                                    <ul class="text-danger">
                                        <li>Erreurs colonnes inexistantes</li>
                                        <li>Relations Eloquent cassées</li>
                                        <li>Données artificielles</li>
                                        <li>Complexité inutile</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h4><i class="fas fa-check-circle"></i> NOUVEAU SYSTÈME</h4>
                                </div>
                                <div class="card-body">
                                    <h5>📅 Période</h5>
                                    <p>• <span class="text-success">12 mois civils simples (Jan → Déc)</span></p>
                                    <p>• Logique claire et directe</p>
                                    
                                    <h5>💰 Calculs</h5>
                                    <p>• <span class="text-success">UNIQUEMENT des données réelles</span></p>
                                    <p>• <span class="text-success">Utilise les vrais champs DB</span></p>
                                    <p>• Aucune estimation artificielle</p>
                                    
                                    <h5>🗃️ Structure DB</h5>
                                    <p>• <span class="text-success">Basé sur analyse complète DB</span></p>
                                    <p>• Respecte les vrais champs et relations</p>
                                    <p>• Calculs sur données existantes uniquement</p>
                                    
                                    <h5>✅ Avantages</h5>
                                    <ul class="text-success">
                                        <li>Aucune erreur de colonne</li>
                                        <li>Relations Eloquent correctes</li>
                                        <li>Données 100% réelles</li>
                                        <li>Code simple et maintenable</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Comparaison des données -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h4><i class="fas fa-database"></i> Comparaison des Sources de Données</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Type de Données</th>
                                            <th class="bg-warning">Ancien Système</th>
                                            <th class="bg-success text-white">Nouveau Système</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Recettes Inscriptions</strong></td>
                                            <td><span class="text-warning">Estimation complexe</span></td>
                                            <td><span class="text-success">inscriptions.montant_paye</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Recettes Mensualités</strong></td>
                                            <td><span class="text-warning">Logique temporelle compliquée</span></td>
                                            <td><span class="text-success">mensualites.montant_paye WHERE date_paiement NOT NULL</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Dépenses Personnel</strong></td>
                                            <td><span class="text-danger">Estimation artificielle 50K/mois</span></td>
                                            <td><span class="text-info">0 FCFA (pas de table dédiée)</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Dépenses Services</strong></td>
                                            <td><span class="text-warning">Calcul complexe</span></td>
                                            <td><span class="text-success">depenses_services.montant</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Structure Tarifaire</strong></td>
                                            <td><span class="text-danger">montant_mensuel (inexistant)</span></td>
                                            <td><span class="text-success">frais.type avec montants réels</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Personnel Info</strong></td>
                                            <td><span class="text-danger">Colonne 'fonction' (inexistante)</span></td>
                                            <td><span class="text-success">personnels.type_personnel</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Comparaison des fonctionnalités -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h4><i class="fas fa-features"></i> Comparaison des Fonctionnalités</h4>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Fonctionnalité</th>
                                            <th class="bg-warning">Ancien</th>
                                            <th class="bg-success text-white">Nouveau</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Rapport Mensuel</td>
                                            <td>Mois scolaires</td>
                                            <td>Mois civils</td>
                                            <td><span class="badge badge-success">Amélioré</span></td>
                                        </tr>
                                        <tr>
                                            <td>Distinction Réel/Prévisionnel</td>
                                            <td>Oui (complexe)</td>
                                            <td>Non (données réelles uniquement)</td>
                                            <td><span class="badge badge-info">Simplifié</span></td>
                                        </tr>
                                        <tr>
                                            <td>Export PDF/Excel</td>
                                            <td>Oui</td>
                                            <td>Oui</td>
                                            <td><span class="badge badge-success">Maintenu</span></td>
                                        </tr>
                                        <tr>
                                            <td>Analyses Avancées</td>
                                            <td>Basiques</td>
                                            <td>Évolution, tendances, répartition</td>
                                            <td><span class="badge badge-success">Amélioré</span></td>
                                        </tr>
                                        <tr>
                                            <td>Gestion Erreurs DB</td>
                                            <td>Nombreuses erreurs</td>
                                            <td>Aucune erreur</td>
                                            <td><span class="badge badge-success">Corrigé</span></td>
                                        </tr>
                                        <tr>
                                            <td>Maintenabilité Code</td>
                                            <td>Complexe</td>
                                            <td>Simple et clair</td>
                                            <td><span class="badge badge-success">Amélioré</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Recommandation -->
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-success">
                                <h4><i class="fas fa-lightbulb"></i> RECOMMANDATION</h4>
                                <p><strong>Utilisez le NOUVEAU système</strong> car :</p>
                                <ul>
                                    <li>✅ <strong>Aucune erreur</strong> - Basé sur la structure DB réelle</li>
                                    <li>✅ <strong>Données fiables</strong> - Uniquement ce qui existe vraiment</li>
                                    <li>✅ <strong>Plus simple</strong> - Code clair et maintenable</li>
                                    <li>✅ <strong>Plus rapide</strong> - Moins de calculs complexes</li>
                                    <li>✅ <strong>Plus précis</strong> - Aucune estimation artificielle</li>
                                </ul>
                                
                                <div class="mt-3">
                                    <a href="{{ route('rapport.nouveau-systeme') }}" class="btn btn-success btn-lg">
                                        <i class="fas fa-rocket"></i> Utiliser le Nouveau Système
                                    </a>
                                    <a href="{{ route('rapport.global') }}" class="btn btn-warning btn-lg ml-2">
                                        <i class="fas fa-eye"></i> Voir l'Ancien (pour référence)
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    console.log('Page de comparaison chargée');
});
</script>
@endsection