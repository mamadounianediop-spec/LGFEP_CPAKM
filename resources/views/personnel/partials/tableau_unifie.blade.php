<!-- Tableau unifié de tout le personnel -->
<div class="bg-white rounded-lg shadow-sm border" x-data="personnelTable()">
    <!-- En-tête avec filtres et recherche -->
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <!-- Titre et bouton d'ajout -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Personnel de l'établissement</h3>
                        <p class="text-sm text-gray-600" x-text="`${filteredPersonnels.length} personnel(s) affiché(s) sur {{ $allPersonnels->count() }} au total`"></p>
                    </div>
                </div>
                <div class="lg:hidden">
                    <button @click="showFilters = !showFilters" 
                            class="bg-gray-600 text-white px-3 py-2 rounded-md hover:bg-gray-700 text-sm">
                        <i class="fas fa-filter mr-1"></i>Filtres
                    </button>
                </div>
            </div>

            <!-- Filtres et recherche -->
            <div class="flex flex-col lg:flex-row lg:items-center space-y-3 lg:space-y-0 lg:space-x-4" 
                 x-show="showFilters || window.innerWidth >= 1024"
                 x-transition>
                
                <!-- Barre de recherche -->
                <div class="relative flex-1 lg:flex-none">
                    <input type="text" x-model="searchTerm" @input="filterPersonnels()" 
                           placeholder="Rechercher nom, prénom, téléphone..." 
                           class="w-full lg:w-80 px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>

                <!-- Filtre par type -->
                <select x-model="filterType" @change="filterPersonnels()" 
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <option value="">Tous les types</option>
                    <option value="directeur">Directeurs</option>
                    <option value="surveillant">Surveillants</option>
                    <option value="secretaire">Secrétaires</option>
                    <option value="enseignant">Enseignants</option>
                    <option value="gardien">Gardiens</option>
                </select>

                <!-- Filtre par statut -->
                <select x-model="filterStatut" @change="filterPersonnels()" 
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <option value="">Tous les statuts</option>
                    <option value="actif">Actifs</option>
                    <option value="suspendu">Suspendus</option>
                    <option value="conge">En congé</option>
                </select>

                <!-- Bouton d'ajout -->
                <button onclick="openPersonnelModal()" 
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-sm whitespace-nowrap">
                    <i class="fas fa-plus mr-2"></i>Nouveau personnel
                </button>
            </div>
        </div>
    </div>

    <!-- Tableau -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" @click="sortBy('nom')">
                        <div class="flex items-center space-x-1">
                            <span>Personnel</span>
                            <i :class="sortField === 'nom' ? (sortDirection === 'asc' ? 'fas fa-sort-up text-blue-500' : 'fas fa-sort-down text-blue-500') : 'fas fa-sort text-gray-400'"></i>
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" @click="sortBy('type_personnel')">
                        <div class="flex items-center space-x-1">
                            <span>Fonction</span>
                            <i :class="sortField === 'type_personnel' ? (sortDirection === 'asc' ? 'fas fa-sort-up text-blue-500' : 'fas fa-sort-down text-blue-500') : 'fas fa-sort text-gray-400'"></i>
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Contact
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Rémunération
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" @click="sortBy('statut')">
                        <div class="flex items-center space-x-1">
                            <span>Statut</span>
                            <i :class="sortField === 'statut' ? (sortDirection === 'asc' ? 'fas fa-sort-up text-blue-500' : 'fas fa-sort-down text-blue-500') : 'fas fa-sort text-gray-400'"></i>
                        </div>
                    </th>
                    <th class="relative px-6 py-3">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="personnel in filteredPersonnels" :key="personnel.id">
                    <tr class="hover:bg-gray-50 transition-colors">
                        <!-- Personnel -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 mr-4">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" 
                                         :class="getTypeColorClass(personnel.type_personnel)">
                                        <i :class="getTypeIcon(personnel.type_personnel)"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900" x-text="personnel.nom + ' ' + personnel.prenom"></div>
                                    <div class="text-sm text-gray-500" x-text="personnel.adresse || 'Adresse non renseignée'"></div>
                                </div>
                            </div>
                        </td>

                        <!-- Fonction -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                  :class="getTypeColorBadge(personnel.type_personnel)"
                                  x-text="getTypeLabel(personnel.type_personnel)">
                            </span>
                            <div x-show="personnel.discipline" class="text-xs text-gray-500 mt-1" x-text="personnel.discipline"></div>
                        </td>

                        <!-- Contact -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900" x-text="personnel.telephone"></div>
                            <div x-show="personnel.cni" class="text-sm text-gray-500" x-text="'CNI: ' + personnel.cni"></div>
                        </td>

                        <!-- Rémunération -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900" x-text="formatRemuneration(personnel)"></div>
                            <div x-show="personnel.mode_paiement === 'heure'" class="text-xs text-gray-500" x-text="'Estimé: ' + formatEstimation(personnel) + ' FCFA/mois'"></div>
                        </td>



                        <!-- Statut -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                  :class="getStatutColorClass(personnel.statut)"
                                  x-text="personnel.statut.charAt(0).toUpperCase() + personnel.statut.slice(1)">
                            </span>
                            <div x-show="personnel.date_embauche" class="text-xs text-gray-500 mt-1" x-text="formatDate(personnel.date_embauche)"></div>
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <button @click="editPersonnel(personnel.id)" 
                                        class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50 transition-colors"
                                        title="Modifier">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                <button @click="toggleStatut(personnel.id)" 
                                        :class="personnel.statut === 'actif' ? 'text-orange-600 hover:text-orange-900 hover:bg-orange-50' : 'text-green-600 hover:text-green-900 hover:bg-green-50'"
                                        class="p-1 rounded transition-colors"
                                        :title="personnel.statut === 'actif' ? 'Suspendre' : 'Activer'">
                                    <i :class="personnel.statut === 'actif' ? 'fas fa-user-slash' : 'fas fa-user-check'" class="text-sm"></i>
                                </button>
                                <button @click="deletePersonnel(personnel.id)" 
                                        class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50 transition-colors"
                                        title="Supprimer">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>

        <!-- Message si aucun résultat -->
        <div x-show="filteredPersonnels.length === 0" class="text-center py-12 text-gray-500">
            <i class="fas fa-users text-4xl mb-4 text-gray-300"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun personnel trouvé</h3>
            <p class="text-gray-600 mb-4">Aucun personnel ne correspond aux critères de recherche</p>
            <button @click="clearFilters()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm">
                <i class="fas fa-times mr-2"></i>Effacer les filtres
            </button>
        </div>
    </div>
</div>

<script>
function personnelTable() {
    return {
        allPersonnels: @json($allPersonnels),
        filteredPersonnels: @json($allPersonnels),
        searchTerm: '',
        filterType: '',
        filterStatut: '',
        showFilters: false,
        sortField: 'nom',
        sortDirection: 'asc',

        init() {
            this.filterPersonnels();
        },

        filterPersonnels() {
            let filtered = this.allPersonnels;

            // Filtre par recherche
            if (this.searchTerm) {
                const term = this.searchTerm.toLowerCase();
                filtered = filtered.filter(p => 
                    p.nom.toLowerCase().includes(term) ||
                    p.prenom.toLowerCase().includes(term) ||
                    p.telephone.includes(term) ||
                    (p.cni && p.cni.includes(term))
                );
            }

            // Filtre par type
            if (this.filterType) {
                filtered = filtered.filter(p => p.type_personnel === this.filterType);
            }

            // Filtre par statut
            if (this.filterStatut) {
                filtered = filtered.filter(p => p.statut === this.filterStatut);
            }

            this.filteredPersonnels = filtered;
            this.sortPersonnels();
        },

        sortBy(field) {
            if (this.sortField === field) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortField = field;
                this.sortDirection = 'asc';
            }
            this.sortPersonnels();
        },

        sortPersonnels() {
            this.filteredPersonnels.sort((a, b) => {
                let aVal = a[this.sortField] || '';
                let bVal = b[this.sortField] || '';
                
                if (typeof aVal === 'string') {
                    aVal = aVal.toLowerCase();
                    bVal = bVal.toLowerCase();
                }

                if (this.sortDirection === 'asc') {
                    return aVal > bVal ? 1 : -1;
                } else {
                    return aVal < bVal ? 1 : -1;
                }
            });
        },

        clearFilters() {
            this.searchTerm = '';
            this.filterType = '';
            this.filterStatut = '';
            this.filterPersonnels();
        },

        getTypeColorClass(type) {
            const colors = {
                'directeur': 'bg-purple-100 text-purple-600',
                'surveillant': 'bg-orange-100 text-orange-600', 
                'secretaire': 'bg-pink-100 text-pink-600',
                'enseignant': 'bg-green-100 text-green-600',
                'gardien': 'bg-gray-100 text-gray-600'
            };
            return colors[type] || 'bg-gray-100 text-gray-600';
        },

        getTypeColorBadge(type) {
            const colors = {
                'directeur': 'bg-purple-100 text-purple-800',
                'surveillant': 'bg-orange-100 text-orange-800', 
                'secretaire': 'bg-pink-100 text-pink-800',
                'enseignant': 'bg-green-100 text-green-800',
                'gardien': 'bg-gray-100 text-gray-800'
            };
            return colors[type] || 'bg-gray-100 text-gray-800';
        },

        getTypeIcon(type) {
            const icons = {
                'directeur': 'fas fa-user-tie text-sm',
                'surveillant': 'fas fa-eye text-sm',
                'secretaire': 'fas fa-user-cog text-sm',
                'enseignant': 'fas fa-chalkboard-teacher text-sm',
                'gardien': 'fas fa-shield-alt text-sm'
            };
            return icons[type] || 'fas fa-user text-sm';
        },

        getTypeLabel(type) {
            const labels = {
                'directeur': 'Directeur',
                'surveillant': 'Surveillant',
                'secretaire': 'Secrétaire',
                'enseignant': 'Enseignant',
                'gardien': 'Gardien'
            };
            return labels[type] || type;
        },

        getStatutColorClass(statut) {
            const colors = {
                'actif': 'bg-green-100 text-green-800',
                'suspendu': 'bg-red-100 text-red-800',
                'conge': 'bg-yellow-100 text-yellow-800'
            };
            return colors[statut] || 'bg-gray-100 text-gray-800';
        },



        formatRemuneration(personnel) {
            if (personnel.mode_paiement === 'fixe' && personnel.montant_fixe) {
                return new Intl.NumberFormat('fr-FR').format(personnel.montant_fixe) + ' FCFA/mois';
            } else if (personnel.mode_paiement === 'heure' && personnel.tarif_heure) {
                return new Intl.NumberFormat('fr-FR').format(personnel.tarif_heure) + ' FCFA/heure';
            }
            return 'Non défini';
        },

        formatEstimation(personnel) {
            if (personnel.mode_paiement === 'heure' && personnel.tarif_heure) {
                return new Intl.NumberFormat('fr-FR').format(personnel.tarif_heure * 80);
            }
            return '0';
        },

        formatDate(dateString) {
            if (!dateString) return '';
            return new Date(dateString).toLocaleDateString('fr-FR');
        },

        editPersonnel(id) {
            editPersonnel(id);
        },

        toggleStatut(id) {
            toggleStatutPersonnel(id);
        },

        deletePersonnel(id) {
            deletePersonnel(id);
        }
    }
}
</script>