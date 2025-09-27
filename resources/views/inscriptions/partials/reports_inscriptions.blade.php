<!-- Rapports des Inscriptions -->
<div class="bg-white rounded-lg shadow-sm border" x-data="rapportsInscriptionsModule()">
    <!-- En-tête -->
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <!-- Titre -->
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-blue-600 text-sm"></i>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Rapports des Inscriptions</h3>
                    <p class="text-sm text-gray-600">Analyse des inscriptions avec filtres dynamiques</p>
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

            <!-- Date de début -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date de début</label>
                <input type="date" x-model="filtres.dateDebut" @change="chargerRapports()"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>

            <!-- Date de fin -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date de fin</label>
                <input type="date" x-model="filtres.dateFin" @change="chargerRapports()"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>

            <!-- Niveau -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Niveau</label>
                <select x-model="filtres.niveau" @change="chargerRapports()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">Tous les niveaux</option>
                    @foreach($niveaux ?? [] as $niveau)
                        <option value="{{ $niveau->id }}">{{ $niveau->nom }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Statut -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                <select x-model="filtres.statut" @change="chargerRapports()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">Tous les statuts</option>
                    <option value="complet">Paiement complet</option>
                    <option value="partiel">Paiement partiel</option>
                    <option value="impaye">Non payé</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Chargement -->
    <div x-show="chargement" class="flex items-center justify-center py-12">
        <div class="flex items-center space-x-2 text-gray-600">
            <i class="fas fa-spinner fa-spin"></i>
            <span>Chargement des rapports d'inscriptions...</span>
        </div>
    </div>

    <!-- Cartes des totaux -->
    <div x-show="!chargement && rapports" class="p-6">
        <!-- Résumé global - Cartes principales -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total élèves -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-graduate text-blue-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Élèves</p>
                        <p class="text-2xl font-bold text-gray-900" x-text="rapports?.totaux?.total_eleves || 0"></p>
                        <p class="text-xs text-gray-400" x-text="`${rapports?.totaux?.inscriptions_validees || 0} inscriptions`"></p>
                    </div>
                </div>
            </div>

            <!-- Montant total des inscriptions -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-money-bill-wave text-orange-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Montant Total</p>
                        <p class="text-lg font-bold text-orange-600" x-text="formatMontant(rapports?.totaux?.montant_total || 0)"></p>
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
                        <p class="text-sm font-medium text-gray-500">Montant Payé</p>
                        <p class="text-lg font-bold text-green-600" x-text="formatMontant(rapports?.totaux?.montant_paye || 0)"></p>
                    </div>
                </div>
            </div>

            <!-- Montant moyen -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calculator text-purple-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Montant Moyen</p>
                        <p class="text-lg font-bold text-purple-600" x-text="formatMontant(rapports?.totaux?.montant_moyen || 0)"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cartes secondaires -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <!-- Taux de conversion -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-percentage text-cyan-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Taux de Conversion</p>
                        <p class="text-lg font-bold text-cyan-600" x-text="(rapports?.totaux?.taux_conversion || 0) + '%'"></p>
                    </div>
                </div>
            </div>

            <!-- Paiements complets -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-double text-indigo-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Paiements Complets</p>
                        <p class="text-lg font-bold text-indigo-600" x-text="rapports?.totaux?.paiements_complets || 0"></p>
                    </div>
                </div>
            </div>

            <!-- Paiements partiels -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-yellow-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Paiements Partiels</p>
                        <p class="text-lg font-bold text-yellow-600" x-text="rapports?.totaux?.paiements_partiels || 0"></p>
                    </div>
                </div>
            </div>

            <!-- Non payés -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Non Payés</p>
                        <p class="text-lg font-bold text-red-600" x-text="rapports?.totaux?.non_payes || 0"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau détaillé -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Détail des Inscriptions</h3>
                <p class="text-sm text-gray-600">Liste détaillée correspondant aux filtres appliqués</p>
            </div>
            
            <div x-show="rapports?.details && rapports.details.length > 0" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Élève</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Niveau/Classe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Inscription</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant Payé</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Paiement</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="detail in rapports.details" :key="detail.id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900" x-text="detail.nom_complet"></div>
                                    <div class="text-xs text-gray-500" x-text="detail.ine"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900" x-text="detail.niveau"></div>
                                    <div class="text-xs text-gray-500" x-text="detail.classe"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="detail.date_inscription"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="formatMontant(detail.montant_total)"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600" x-text="formatMontant(detail.montant_paye)"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="detail.mode_paiement || '-'"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="detail.date_paiement || '-'"></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                          :class="getStatutColorClass(detail.statut_paiement)"
                                          x-text="getStatutLabel(detail.statut_paiement)">
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
        <p class="text-gray-600">Aucune inscription trouvée pour les critères sélectionnés.</p>
    </div>
</div>

<script>
function rapportsInscriptionsModule() {
    return {
        rapports: null,
        chargement: false,
        anneesScolaires: [],
        filtres: {
            annee: null,
            dateDebut: '',
            dateFin: '',
            niveau: '',
            statut: ''
        },

        async init() {
            await this.chargerAnneesScolaires();
            this.chargerRapports();
        },

        async chargerAnneesScolaires() {
            try {
                const response = await fetch('/inscriptions/annees-scolaires');  
                if (response.ok) {
                    this.anneesScolaires = await response.json();
                    
                    // Trouver l'année active et l'initialiser
                    const anneeActuelle = this.anneesScolaires.find(a => a.actuelle);
                    if (anneeActuelle) {
                        this.filtres.annee = anneeActuelle.valeur;
                    } else if (this.anneesScolaires.length > 0) {
                        this.filtres.annee = this.anneesScolaires[0].valeur;
                    }
                } else {
                    console.error('Erreur réponse annees scolaires:', response.status);
                }
            } catch (error) {
                console.error('Erreur lors du chargement des années scolaires:', error);
                // Fallback avec l'année actuelle
                const currentYear = new Date().getFullYear();
                this.filtres.annee = currentYear;
                this.anneesScolaires = [
                    { valeur: currentYear, libelle: `${currentYear}-${currentYear + 1}`, actuelle: true }
                ];
            }
        },

        async chargerRapports() {
            this.chargement = true;
            
            try {
                // Construire l'URL avec les filtres
                const params = new URLSearchParams();
                if (this.filtres.annee) params.append('annee', this.filtres.annee);
                if (this.filtres.dateDebut) params.append('dateDebut', this.filtres.dateDebut);
                if (this.filtres.dateFin) params.append('dateFin', this.filtres.dateFin);
                if (this.filtres.niveau) params.append('niveau', this.filtres.niveau);
                if (this.filtres.statut) params.append('statut', this.filtres.statut);

                const response = await fetch(`/inscriptions/rapports-data?${params.toString()}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    this.rapports = await response.json();
                } else {
                    console.error('Erreur lors du chargement des rapports d\'inscriptions');
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
            if (this.filtres.dateDebut) params.append('dateDebut', this.filtres.dateDebut);
            if (this.filtres.dateFin) params.append('dateFin', this.filtres.dateFin);
            if (this.filtres.niveau) params.append('niveau', this.filtres.niveau);
            if (this.filtres.statut) params.append('statut', this.filtres.statut);

            const url = `{{ route('inscriptions.rapports.apercu.inscriptions') }}?${params.toString()}`;
            window.open(url, '_blank');
        },

        formatMontant(montant) {
            return new Intl.NumberFormat('fr-FR').format(montant || 0) + ' FCFA';
        },

        getStatutColorClass(statut) {
            const classes = {
                'Complet': 'bg-green-100 text-green-800',
                'Partiel': 'bg-yellow-100 text-yellow-800',
                'Non payé': 'bg-red-100 text-red-800'
            };
            return classes[statut] || 'bg-gray-100 text-gray-800';
        },

        getStatutLabel(statut) {
            return statut || 'Inconnu';
        }
    }
}
</script>