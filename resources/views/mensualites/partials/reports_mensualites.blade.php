<!-- Rapports des Mensualités -->
<div class="bg-white rounded-lg shadow-sm border" x-data="rapportsMensualitesModule()">
    <!-- En-tête -->
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <!-- Titre -->
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-blue-600 text-sm"></i>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Rapports des Mensualités</h3>
                    <p class="text-sm text-gray-600">Analyse des paiements de mensualités avec filtres dynamiques</p>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex space-x-2">
                <button @click="ouvrirApercu()" :disabled="!rapports || !rapports.details || rapports.details.length === 0"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50 text-sm font-medium transition-colors">
                    <i class="fas fa-eye mr-2"></i>Aperçu
                </button>
                <button @click="chargerRapports()" :disabled="chargement"
                        class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 disabled:opacity-50 text-sm font-medium transition-colors">
                    <i class="fas fa-sync-alt mr-2" :class="chargement ? 'fa-spin' : ''"></i>Actualiser
                </button>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Année scolaire -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Année scolaire</label>
                <select x-model="filtres.annee" @change="chargerRapports()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <template x-for="annee in anneesScolaires" :key="annee.valeur">
                        <option :value="annee.valeur" x-text="annee.libelle" :selected="annee.actuelle"></option>
                    </template>
                </select>
            </div>

            <!-- Mois scolaire (Octobre à Juillet) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mois</label>
                <select x-model="filtres.mois" @change="chargerRapports()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">Tous les mois</option>
                    <option value="octobre">Octobre</option>
                    <option value="novembre">Novembre</option>
                    <option value="decembre">Décembre</option>
                    <option value="janvier">Janvier</option>
                    <option value="fevrier">Février</option>
                    <option value="mars">Mars</option>
                    <option value="avril">Avril</option>
                    <option value="mai">Mai</option>
                    <option value="juin">Juin</option>
                    <option value="juillet">Juillet</option>
                </select>
            </div>

            <!-- Date de début des paiements -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date de début</label>
                <input type="date" x-model="filtres.dateDebut" @change="chargerRapports()"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>

            <!-- Date de fin des paiements -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date de fin</label>
                <input type="date" x-model="filtres.dateFin" @change="chargerRapports()"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>

            <!-- Statut des paiements -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                <select x-model="filtres.statut" @change="chargerRapports()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">Tous les statuts</option>
                    <option value="complet">Payé intégralement</option>
                    <option value="partiel">Paiement partiel</option>
                    <option value="impaye">Impayé</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Chargement -->
    <div x-show="chargement" class="flex items-center justify-center py-12">
        <div class="flex items-center space-x-2 text-gray-600">
            <i class="fas fa-spinner fa-spin"></i>
            <span>Chargement des rapports de mensualités...</span>
        </div>
    </div>

    <!-- Cartes des totaux -->
    <div x-show="!chargement && rapports" class="p-6">
        <!-- Résumé global - Cartes principales -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Élèves inscrits -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-graduate text-blue-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Élèves Inscrits</p>
                        <p class="text-2xl font-bold text-gray-900" x-text="rapports?.eleves_total || 0"></p>
                        <p class="text-xs text-gray-400" x-text="`${rapports?.eleves_avec_mensualites || 0} avec mensualités`"></p>
                    </div>
                </div>
            </div>

            <!-- Montant total dû -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-money-bill-wave text-orange-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Montant Total Dû</p>
                        <p class="text-lg font-bold text-orange-600" x-text="formatMontant(rapports?.montant_total_du || 0)"></p>
                    </div>
                </div>
            </div>

            <!-- Montant total payé -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Montant Total Payé</p>
                        <p class="text-lg font-bold text-green-600" x-text="formatMontant(rapports?.montant_total_paye || 0)"></p>
                    </div>
                </div>
            </div>

            <!-- Montant restant -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Montant Restant</p>
                        <p class="text-lg font-bold text-red-600" x-text="formatMontant(rapports?.montant_restant || 0)"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cartes secondaires -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <!-- Élèves avec mensualités -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-cyan-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Élèves avec Mensualités</p>
                        <p class="text-lg font-bold text-cyan-600" x-text="rapports?.eleves_avec_mensualites || 0"></p>
                    </div>
                </div>
            </div>

            <!-- Pourcentage de paiement -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-percentage text-purple-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Taux de Paiement</p>
                        <p class="text-lg font-bold text-purple-600" x-text="(rapports?.pourcentage_paiement || 0) + '%'"></p>
                    </div>
                </div>
            </div>

            <!-- Mensualités complètes -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-double text-indigo-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Paiements Complets</p>
                        <p class="text-lg font-bold text-indigo-600">
                            <span x-text="rapports?.par_statut?.complet?.count || 0"></span> / 
                            <span x-text="rapports?.mensualites_total || 0"></span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Paiements en retard -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-yellow-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Impayés</p>
                        <p class="text-lg font-bold" 
                           :class="(rapports?.par_statut?.impaye?.count || 0) > 0 ? 'text-red-600' : 'text-green-600'"
                           x-text="rapports?.par_statut?.impaye?.count || 0"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau détaillé -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Détail des Mensualités</h3>
                <p class="text-sm text-gray-600">Liste détaillée correspondant aux filtres appliqués</p>
            </div>
            
            <div x-show="rapports?.details && rapports.details.length > 0" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Élève</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Classe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mois</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant Dû</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant Payé</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Restant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="detail in rapports.details" :key="detail.id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900" x-text="`${detail.eleve.prenom} ${detail.eleve.nom}`"></div>
                                    <div class="text-xs text-gray-500" x-text="detail.eleve.ine"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="detail.eleve.classe"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="detail.mois_paiement"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="formatMontant(detail.montant_du)"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600" x-text="formatMontant(detail.montant_paye)"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" 
                                    :class="detail.solde_restant > 0 ? 'text-red-600' : 'text-green-600'"
                                    x-text="formatMontant(detail.solde_restant)"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="detail.mode_paiement || '-'"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="detail.date_paiement || '-'"></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                          :class="getStatutColorClass(detail.statut)"
                                          x-text="getStatutLabel(detail.statut)">
                                    </span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div x-show="!rapports?.details || rapports.details.length === 0" class="text-center py-12">
                <i class="fas fa-table text-gray-300 text-4xl mb-4"></i>
                <p class="text-gray-500">Aucune donnée détaillée disponible</p>
            </div>
        </div>
    </div>

    <!-- Message si aucune donnée -->
    <div x-show="!chargement && !rapports" class="text-center py-12">
        <i class="fas fa-chart-line text-gray-300 text-6xl mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune donnée</h3>
        <p class="text-gray-600">Aucune mensualité trouvée pour les critères sélectionnés.</p>
    </div>
</div>

<script>
function rapportsMensualitesModule() {
    return {
        rapports: null,
        chargement: false,
        anneesScolaires: [],
        filtres: {
            annee: null,
            mois: '',
            dateDebut: '',
            dateFin: '',
            statut: ''
        },

        async init() {
            await this.chargerAnneesScolaires();
            this.chargerRapports();
        },

        async chargerAnneesScolaires() {
            try {
                const response = await fetch('/mensualites/annees-scolaires');
                if (response.ok) {
                    this.anneesScolaires = await response.json();
                    
                    const anneeActuelle = this.anneesScolaires.find(a => a.actuelle);
                    if (anneeActuelle) {
                        this.filtres.annee = anneeActuelle.valeur;
                    } else if (this.anneesScolaires.length > 0) {
                        this.filtres.annee = this.anneesScolaires[0].valeur;
                    }
                }
            } catch (error) {
                console.error('Erreur lors du chargement des années scolaires:', error);
                this.filtres.annee = new Date().getFullYear();
                this.anneesScolaires = [
                    { valeur: this.filtres.annee, libelle: `${this.filtres.annee}-${this.filtres.annee + 1}`, actuelle: true }
                ];
            }
        },

        async chargerRapports() {
            this.chargement = true;
            
            try {
                // Construire l'URL avec les filtres
                const params = new URLSearchParams();
                if (this.filtres.annee) params.append('annee', this.filtres.annee);
                if (this.filtres.mois) params.append('mois', this.filtres.mois);
                if (this.filtres.dateDebut) params.append('dateDebut', this.filtres.dateDebut);
                if (this.filtres.dateFin) params.append('dateFin', this.filtres.dateFin);
                if (this.filtres.statut) params.append('statut', this.filtres.statut);

                const response = await fetch(`/mensualites/rapports-data?${params.toString()}`);
                
                if (response.ok) {
                    this.rapports = await response.json();
                } else {
                    console.error('Erreur lors du chargement des rapports de mensualités');
                    this.rapports = null;
                }
            } catch (error) {
                console.error('Erreur:', error);
                this.rapports = null;
            } finally {
                this.chargement = false;
            }
        },

        ouvrirApercu() {
            if (!this.rapports || !this.rapports.details || this.rapports.details.length === 0) {
                return;
            }

            // Construire l'URL d'aperçu avec les mêmes filtres
            const params = new URLSearchParams();
            if (this.filtres.annee) params.append('annee', this.filtres.annee);
            if (this.filtres.mois) params.append('mois', this.filtres.mois);
            if (this.filtres.dateDebut) params.append('dateDebut', this.filtres.dateDebut);
            if (this.filtres.dateFin) params.append('dateFin', this.filtres.dateFin);
            if (this.filtres.statut) params.append('statut', this.filtres.statut);

            const url = `/mensualites/rapports-apercu?${params.toString()}`;
            window.open(url, '_blank');
        },

        formatMontant(montant) {
            return new Intl.NumberFormat('fr-FR').format(montant || 0) + ' FCFA';
        },

        getStatutColorClass(statut) {
            const classes = {
                'complet': 'bg-green-100 text-green-800',
                'partiel': 'bg-yellow-100 text-yellow-800',
                'impaye': 'bg-red-100 text-red-800'
            };
            return classes[statut] || 'bg-gray-100 text-gray-800';
        },

        getStatutLabel(statut) {
            const labels = {
                'complet': 'Payé',
                'partiel': 'Partiel',
                'impaye': 'Impayé'
            };
            return labels[statut] || 'Inconnu';
        }
    }
}
</script>