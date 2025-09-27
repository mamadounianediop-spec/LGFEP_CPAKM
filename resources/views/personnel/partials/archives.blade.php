<!-- Archives des États de Paiement -->
<div class="bg-white rounded-lg shadow-sm border" x-data="archivesModule()">
    <!-- En-tête -->
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <!-- Titre -->
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-archive text-blue-600 text-sm"></i>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Archives des États de Paiement</h3>
                    <p class="text-sm text-gray-600">Consultation des états archivés par période</p>
                </div>
            </div>

            <!-- Filtres -->
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <label for="annee-select" class="text-sm font-medium text-gray-700">Année scolaire :</label>
                    <select id="annee-select" x-model="anneeSelectionnee" @change="chargerArchives()"
                            class="px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-sm">
                        <template x-for="annee in anneesScolaires" :key="annee.valeur">
                            <option :value="annee.valeur" x-text="annee.libelle" :selected="annee.actuelle"></option>
                        </template>
                    </select>
                </div>
                
                <button @click="chargerArchives()" :disabled="chargement"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50 text-sm font-medium transition-colors">
                    <i class="fas fa-sync-alt mr-2" :class="chargement ? 'fa-spin' : ''"></i>Actualiser
                </button>
            </div>
        </div>
    </div>

    <!-- Chargement -->
    <div x-show="chargement" class="flex items-center justify-center py-12">
        <div class="flex items-center space-x-2 text-gray-600">
            <i class="fas fa-spinner fa-spin"></i>
            <span>Chargement des archives...</span>
        </div>
    </div>

    <!-- Grille des archives par mois -->
    <div x-show="!chargement" class="p-6">
        <div x-show="archives.length === 0" class="text-center py-12">
            <i class="fas fa-archive text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune archive</h3>
            <p class="text-gray-600">Aucun état de paiement archivé trouvé pour l'année sélectionnée.</p>
        </div>

        <div x-show="archives.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <template x-for="archive in archives" :key="`${archive.annee}-${archive.mois}`">
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                    <!-- En-tête de la carte -->
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-calendar text-blue-600 text-xs"></i>
                                </div>
                                <h4 class="font-medium text-gray-900" x-text="`${getNomMois(archive.mois)} ${archive.annee}`"></h4>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Archivé
                            </span>
                        </div>
                    </div>

                    <!-- Contenu de la carte -->
                    <div class="p-4 space-y-3">
                        <!-- Statistiques rapides -->
                        <div class="grid grid-cols-2 gap-2 text-sm mb-3">
                            <div class="text-center bg-gray-50 rounded-lg p-2">
                                <div class="font-semibold text-gray-900" x-text="archive.nombre_personnel"></div>
                                <div class="text-gray-600 text-xs">Personnel</div>
                            </div>
                            <div class="text-center bg-green-50 rounded-lg p-2">
                                <div class="font-semibold text-green-700 text-xs" x-text="formatMontant(archive.total_montant)"></div>
                                <div class="text-green-600 text-xs">Montant total</div>
                            </div>
                        </div>
                        
                        <!-- Statistiques détaillées -->
                        <div class="grid grid-cols-3 gap-2 text-xs">
                            <div class="text-center bg-red-50 rounded p-2">
                                <div class="font-semibold text-red-700" x-text="formatMontant(archive.total_retenues)"></div>
                                <div class="text-red-600 text-xs">Retenues</div>
                            </div>
                            <div class="text-center bg-yellow-50 rounded p-2">
                                <div class="font-semibold text-yellow-700" x-text="formatMontant(archive.total_avances)"></div>
                                <div class="text-yellow-600 text-xs">Avances</div>
                            </div>
                            <div class="text-center bg-blue-50 rounded p-2">
                                <div class="font-semibold text-blue-700" x-text="formatMontant(archive.total_restant)"></div>
                                <div class="text-blue-600 text-xs">Restant</div>
                            </div>
                        </div>

                        <!-- Informations d'archivage -->
                        <div class="text-xs text-gray-500 border-t pt-2">
                            <div class="flex items-center justify-between">
                                <span>Archivé le :</span>
                                <span x-text="formatDate(archive.date_archive)"></span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex space-x-2 pt-2">
                            <button @click="voirDetailArchive(archive)" 
                                    class="flex-1 bg-blue-600 text-white px-3 py-2 rounded-md hover:bg-blue-700 text-sm font-medium transition-colors">
                                <i class="fas fa-eye mr-1"></i>Consulter
                            </button>
                            <button @click="imprimerArchive(archive)" 
                                    class="flex-1 bg-gray-600 text-white px-3 py-2 rounded-md hover:bg-gray-700 text-sm font-medium transition-colors">
                                <i class="fas fa-print mr-1"></i>Imprimer
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<!-- Modal de consultation détaillée -->
<div x-show="modalDetail" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" 
     x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" 
     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
    
    <div x-show="modalDetail" x-transition:enter="transition ease-out duration-300" 
         x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100" 
         x-transition:leave-end="opacity-0 transform scale-95"
         class="bg-white rounded-lg shadow-xl max-w-6xl w-full max-h-[90vh] overflow-hidden">
        
        <!-- En-tête modal -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-alt text-blue-600 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900" x-show="archiveDetail" 
                            x-text="`État de Paiement - ${getNomMois(archiveDetail?.mois)} ${archiveDetail?.annee}`"></h3>
                        <p class="text-sm text-gray-600">Consultation détaillée de l'archive</p>
                    </div>
                </div>
                <button @click="fermerModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Contenu modal -->
        <div class="p-6 overflow-y-auto max-h-[70vh]">
            <div x-show="chargementDetail" class="flex items-center justify-center py-12">
                <div class="flex items-center space-x-2 text-gray-600">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Chargement des détails...</span>
                </div>
            </div>

            <div x-show="!chargementDetail && detailsPersonnel.length > 0">
                <!-- Résumé -->
                <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-blue-700" x-text="detailsPersonnel.length"></div>
                        <div class="text-sm text-blue-600">Personnel total</div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-green-700" x-text="formatMontant(totalDetaille)"></div>
                        <div class="text-sm text-green-600">Montant total</div>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-yellow-700" x-text="formatMontant(totalAvancesDetaille)"></div>
                        <div class="text-sm text-yellow-600">Total avances</div>
                    </div>
                    <div class="bg-red-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-red-700" x-text="formatMontant(totalRestantDetaille)"></div>
                        <div class="text-sm text-red-600">Total restant</div>
                    </div>
                </div>

                <!-- Tableau détaillé -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Personnel</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fonction</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Heures</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Primes</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Retenues</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avances</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Restant</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="detail in detailsPersonnel" :key="detail.id">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900" x-text="`${detail.personnel.prenom} ${detail.personnel.nom}`"></div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                              :class="getFonctionColorClass(detail.personnel.type_personnel)"
                                              x-text="detail.personnel.type_personnel.charAt(0).toUpperCase() + detail.personnel.type_personnel.slice(1)">
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900" x-text="detail.heures_effectuees || '-'"></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900" x-text="formatMontant(detail.primes)"></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900" x-text="formatMontant(detail.retenues)"></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900" x-text="formatMontant(detail.montant_total)"></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900" x-text="formatMontant(detail.avances)"></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium" 
                                        :class="detail.restant > 0 ? 'text-red-600' : 'text-green-600'"
                                        x-text="formatMontant(detail.restant)"></td>
                                    <td class="px-4 py-3 whitespace-nowrap">
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
            </div>
        </div>

        <!-- Pied modal -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end space-x-3">
            <button @click="fermerModal()" 
                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                Fermer
            </button>
            <button @click="imprimerArchive(archiveDetail)" x-show="archiveDetail"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                <i class="fas fa-print mr-2"></i>Imprimer
            </button>
        </div>
    </div>
</div>

<script>
function archivesModule() {
    return {
        archives: [],
        chargement: false,
        anneeActuelle: null,
        anneeSelectionnee: null,
        anneesScolaires: [],
        modalDetail: false,
        chargementDetail: false,
        archiveDetail: null,
        detailsPersonnel: [],
        
        async init() {
            await this.chargerAnneesScolaires();
            this.chargerArchives();
            
            // S'abonner aux changements de synchronisation
            if (window.addSyncListener) {
                window.addSyncListener((syncData) => {
                    this.anneesScolaires = syncData.anneesScolaires;
                    this.anneeActuelle = syncData.anneeActuelle;
                    // Ne pas changer l'année sélectionnée automatiquement pour ne pas perturber l'utilisateur
                });
            }
            
            // S'abonner aux changements d'états de paiement pour rafraîchir les archives
            if (window.addEtatsListener) {
                window.addEtatsListener((data) => {
                    // Si des états ont été modifiés pour la période actuelle, recharger les archives
                    if (data.annee === this.anneeSelectionnee) {
                        this.chargerArchives();
                    }
                });
            }
        },

        async chargerAnneesScolaires() {
            // Vérifier d'abord si les données sont déjà synchronisées
            if (window.getSyncData && window.getSyncData().anneesScolaires.length > 0) {
                const syncData = window.getSyncData();
                this.anneesScolaires = syncData.anneesScolaires;
                this.anneeActuelle = syncData.anneeActuelle;
                this.anneeSelectionnee = syncData.anneeActuelle;
                return;
            }
            
            try {
                const response = await fetch('/personnel/annees-scolaires');
                if (response.ok) {
                    this.anneesScolaires = await response.json();
                    
                    // Utiliser l'année marquée comme actuelle depuis le module paramètres
                    const anneeActuelle = this.anneesScolaires.find(a => a.actuelle);
                    if (anneeActuelle) {
                        this.anneeActuelle = anneeActuelle.valeur;
                        this.anneeSelectionnee = anneeActuelle.valeur;
                    } else if (this.anneesScolaires.length > 0) {
                        // Si aucune année n'est marquée comme actuelle, prendre la première
                        this.anneeActuelle = this.anneesScolaires[0].valeur;
                        this.anneeSelectionnee = this.anneesScolaires[0].valeur;
                    }
                    
                    // Synchroniser avec les autres onglets
                    if (window.syncAnneesScolaires) {
                        window.syncAnneesScolaires({
                            anneesScolaires: this.anneesScolaires,
                            anneeActuelle: this.anneeActuelle
                        });
                    }
                }
            } catch (error) {
                console.error('Erreur lors du chargement des années scolaires:', error);
                // Fallback
                this.anneeActuelle = new Date().getFullYear();
                this.anneeSelectionnee = new Date().getFullYear();
                this.anneesScolaires = [
                    { valeur: this.anneeActuelle, libelle: `${this.anneeActuelle}-${this.anneeActuelle + 1}`, actuelle: true }
                ];
            }
        },

        async chargerArchives() {
            this.chargement = true;
            try {
                const response = await fetch(`/personnel/archives/${this.anneeSelectionnee}`);
                if (response.ok) {
                    this.archives = await response.json();
                } else {
                    console.error('Erreur lors du chargement des archives');
                    this.archives = [];
                }
            } catch (error) {
                console.error('Erreur:', error);
                this.archives = [];
            } finally {
                this.chargement = false;
            }
        },

        voirDetailArchive(archive) {
            // Rediriger vers l'onglet États de Paiement avec la période sélectionnée
            // Nous utilisons un événement personnalisé pour communiquer avec l'onglet parent
            const event = new CustomEvent('changerPeriodeEtatsPaiement', {
                detail: {
                    annee: archive.annee,
                    mois: archive.mois
                }
            });
            document.dispatchEvent(event);
            
            // Changer d'onglet vers États de Paiement
            const ongletEtats = document.querySelector('[data-tab="etats-paiement"]');
            if (ongletEtats) {
                ongletEtats.click();
            }
        },

        async imprimerArchive(archive) {
            try {
                const url = `/personnel/archives/${archive.annee}/${archive.mois}/print`;
                window.open(url, '_blank');
            } catch (error) {
                console.error('Erreur lors de l\'impression:', error);
            }
        },

        fermerModal() {
            this.modalDetail = false;
            this.archiveDetail = null;
            this.detailsPersonnel = [];
        },

        formatMontant(montant) {
            return new Intl.NumberFormat('fr-FR').format(montant || 0) + ' FCFA';
        },

        formatDate(dateString) {
            if (!dateString) return '-';
            return new Date(dateString).toLocaleDateString('fr-FR');
        },

        getNomMois(numeroMois) {
            const mois = [
                '', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
            ];
            return mois[numeroMois] || 'Inconnu';
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
        },

        get totalDetaille() {
            return this.detailsPersonnel.reduce((sum, detail) => sum + (detail.montant_total || 0), 0);
        },

        get totalAvancesDetaille() {
            return this.detailsPersonnel.reduce((sum, detail) => sum + (detail.avances || 0), 0);
        },

        get totalRestantDetaille() {
            return this.detailsPersonnel.reduce((sum, detail) => sum + (detail.restant || 0), 0);
        }
    }
}
</script>