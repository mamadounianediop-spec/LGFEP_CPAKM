<!-- Rapports des États de Paiement -->
<div class="bg-white rounded-lg shadow-sm border" x-data="rapportsModule()">
    <!-- En-tête -->
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <!-- Titre -->
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-bar text-green-600 text-sm"></i>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Rapports des États de Paiement</h3>
                    <p class="text-sm text-gray-600">Analyse des données de paiement avec filtres dynamiques</p>
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
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <!-- Année -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Année scolaire</label>
                <select x-model="filtres.annee" @change="chargerRapports()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm">
                    <template x-for="annee in anneesScolaires" :key="annee.valeur">
                        <option :value="annee.valeur" x-text="annee.libelle" :selected="annee.actuelle"></option>
                    </template>
                </select>
            </div>

            <!-- Mois -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mois</label>
                <select x-model="filtres.mois" @change="chargerRapports()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm">
                    <option value="">Tous les mois</option>
                    <option value="1">Janvier</option>
                    <option value="2">Février</option>
                    <option value="3">Mars</option>
                    <option value="4">Avril</option>
                    <option value="5">Mai</option>
                    <option value="6">Juin</option>
                    <option value="7">Juillet</option>
                    <option value="8">Août</option>
                    <option value="9">Septembre</option>
                    <option value="10">Octobre</option>
                    <option value="11">Novembre</option>
                    <option value="12">Décembre</option>
                </select>
            </div>

            <!-- Date de début -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date de début</label>
                <input type="date" x-model="filtres.dateDebut" @change="chargerRapports()"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm">
            </div>

            <!-- Date de fin -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date de fin</label>
                <input type="date" x-model="filtres.dateFin" @change="chargerRapports()"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm">
            </div>

            <!-- Statut -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                <select x-model="filtres.statut" @change="chargerRapports()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm">
                    <option value="">Tous les statuts</option>
                    <option value="paye">Payé</option>
                    <option value="en_attente">En attente</option>
                </select>
            </div>

            <!-- Type de données -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Données</label>
                <select x-model="filtres.type" @change="chargerRapports()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm">
                    <option value="tous">Tous les états</option>
                    <option value="archives">États archivés uniquement</option>
                    <option value="actuels">États actuels uniquement</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Chargement -->
    <div x-show="chargement" class="flex items-center justify-center py-12">
        <div class="flex items-center space-x-2 text-gray-600">
            <i class="fas fa-spinner fa-spin"></i>
            <span>Chargement des rapports...</span>
        </div>
    </div>

    <!-- Cartes des totaux -->
    <div x-show="!chargement && rapports" class="p-6">
        <!-- Résumé global - Cartes améliorées -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Personnel total -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-blue-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Personnel Total</p>
                        <p class="text-2xl font-bold text-gray-900" x-text="rapports?.personnel || 0"></p>
                    </div>
                </div>
            </div>

            <!-- Montant total -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-money-bill-wave text-green-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Montant Total</p>
                        <p class="text-lg font-bold text-green-600" x-text="formatMontant(rapports?.total_montant || 0)"></p>
                    </div>
                </div>
            </div>

            <!-- Total retenues -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-minus-circle text-red-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Retenues</p>
                        <p class="text-lg font-bold text-red-600" x-text="formatMontant(rapports?.total_retenues || 0)"></p>
                    </div>
                </div>
            </div>

            <!-- Total avances -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-hand-holding-usd text-yellow-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Avances</p>
                        <p class="text-lg font-bold text-yellow-600" x-text="formatMontant(rapports?.total_avances || 0)"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cartes secondaires -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <!-- Net à payer -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calculator text-purple-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Net à Payer</p>
                        <p class="text-lg font-bold text-purple-600" x-text="formatMontant(rapports?.net_total || 0)"></p>
                    </div>
                </div>
            </div>

            <!-- Restant à payer -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-hourglass-half text-orange-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Restant à Payer</p>
                        <p class="text-lg font-bold" 
                           :class="(rapports?.total_restant || 0) > 0 ? 'text-red-600' : 'text-green-600'"
                           x-text="formatMontant(rapports?.total_restant || 0)"></p>
                    </div>
                </div>
            </div>

            <!-- Statut global -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-pie text-indigo-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Payés / Total</p>
                        <p class="text-lg font-bold text-indigo-600">
                            <span x-text="rapports?.par_statut?.payes?.count || 0"></span> / 
                            <span x-text="rapports?.personnel || 0"></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau détaillé -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Détail du Personnel</h3>
                <p class="text-sm text-gray-600">Liste détaillée correspondant aux filtres appliqués</p>
            </div>
            
            <div x-show="rapports?.details && rapports.details.length > 0" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Personnel</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fonction</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heures</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Primes</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Retenues</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avances</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Restant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="detail in rapports.details" :key="detail.id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900" x-text="`${detail.personnel.prenom} ${detail.personnel.nom}`"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                          :class="getFonctionColorClass(detail.personnel.type_personnel)"
                                          x-text="detail.personnel.type_personnel.charAt(0).toUpperCase() + detail.personnel.type_personnel.slice(1)">
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="detail.personnel.mode_paiement === 'fixe' ? 'Fixe' : 'Horaire'"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="detail.heures_effectuees || '-'"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="formatMontant(detail.primes)"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600" x-text="formatMontant(detail.retenues)"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="formatMontant(detail.montant_total)"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-600" x-text="formatMontant(detail.avances)"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" 
                                    :class="detail.restant > 0 ? 'text-red-600' : 'text-green-600'"
                                    x-text="formatMontant(detail.restant)"></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                          :class="detail.statut_paiement === 'paye' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                                          x-text="detail.statut_paiement === 'paye' ? 'Payé' : 'En attente'">
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
        <i class="fas fa-chart-bar text-gray-300 text-6xl mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune donnée</h3>
        <p class="text-gray-600">Aucun état de paiement trouvé pour les critères sélectionnés.</p>
    </div>
</div>

<script>
function rapportsModule() {
    return {
        rapports: null,
        chargement: false,
        anneesScolaires: [],
        filtres: {
            annee: null,
            mois: '',
            dateDebut: '',
            dateFin: '',
            statut: '',
            type: 'tous'
        },

        async init() {
            await this.chargerAnneesScolaires();
            this.chargerRapports();

            // Synchronisation avec les autres onglets
            if (window.addSyncListener) {
                window.addSyncListener((syncData) => {
                    this.anneesScolaires = syncData.anneesScolaires;
                    if (!this.filtres.annee) {
                        this.filtres.annee = syncData.anneeActuelle;
                    }
                });
            }
        },

        async chargerAnneesScolaires() {
            // Vérifier d'abord si les données sont déjà synchronisées
            if (window.getSyncData && window.getSyncData().anneesScolaires.length > 0) {
                const syncData = window.getSyncData();
                this.anneesScolaires = syncData.anneesScolaires;
                this.filtres.annee = syncData.anneeActuelle;
                return;
            }

            try {
                const response = await fetch('/personnel/annees-scolaires');
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
                if (this.filtres.type) params.append('type', this.filtres.type);

                const response = await fetch(`/personnel/rapports-data?${params.toString()}`);
                
                if (response.ok) {
                    this.rapports = await response.json();
                } else {
                    console.error('Erreur lors du chargement des rapports');
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
            if (this.filtres.type) params.append('type', this.filtres.type);

            const url = `/personnel/rapports-apercu?${params.toString()}`;
            window.open(url, '_blank');
        },

        formatMontant(montant) {
            return new Intl.NumberFormat('fr-FR').format(montant || 0) + ' FCFA';
        },

        getFonctionColorClass(type) {
            const classes = {
                'directeur': 'bg-purple-100 text-purple-800',
                'surveillant': 'bg-orange-100 text-orange-800',
                'secretaire': 'bg-pink-100 text-pink-800',
                'enseignant': 'bg-green-100 text-green-800',
                'gardien': 'bg-gray-100 text-gray-800'
            };
            return classes[type] || 'bg-gray-100 text-gray-800';
        }
    }
}
</script>
