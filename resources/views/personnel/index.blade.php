@extends('layouts.app')

@section('title', 'Gestion du Personnel')

@section('content')
<div class="min-h-screen bg-gray-50 py-6" x-data="{ activeTab: '{{ $activeTab ?? 'gestion-personnel' }}' }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Messages d'alerte -->
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Succès!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times cursor-pointer"></i>
                </span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Erreur!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times cursor-pointer"></i>
                </span>
            </div>
        @endif
        
        <!-- En-tête -->
        <div class="mb-8">
            <div class="flex items-center space-x-3 mb-2">
                <div class="bg-green-100 p-2 rounded-lg">
                    <i class="fas fa-users text-green-600 text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Gestion du Personnel</h1>
                    <p class="text-sm text-gray-600">Gérez les employés de l'établissement CPAKM</p>
                </div>
            </div>
        </div>

        <!-- Navigation par onglets -->
        <div class="bg-white rounded-lg shadow-sm border mb-6">
            <nav class="flex space-x-1 p-1">
                <button @click="activeTab = 'gestion-personnel'" 
                        :class="activeTab === 'gestion-personnel' ? 'bg-green-600 text-white shadow-md' : 'text-gray-600 hover:text-green-600 hover:bg-gray-50'"
                        class="flex items-center px-4 py-3 rounded-md font-medium text-sm transition-all duration-200 flex-1 justify-center">
                    <i class="fas fa-users mr-2"></i>Gestion Personnel
                </button>
                <button @click="activeTab = 'etats-paiement'" data-tab="etats-paiement"
                        :class="activeTab === 'etats-paiement' ? 'bg-green-600 text-white shadow-md' : 'text-gray-600 hover:text-green-600 hover:bg-gray-50'"
                        class="flex items-center px-4 py-3 rounded-md font-medium text-sm transition-all duration-200 flex-1 justify-center">
                    <i class="fas fa-money-check mr-2"></i>États de Paiement
                </button>
                <button @click="activeTab = 'archives'" 
                        :class="activeTab === 'archives' ? 'bg-green-600 text-white shadow-md' : 'text-gray-600 hover:text-green-600 hover:bg-gray-50'"
                        class="flex items-center px-4 py-3 rounded-md font-medium text-sm transition-all duration-200 flex-1 justify-center">
                    <i class="fas fa-archive mr-2"></i>Archives
                </button>
                <button @click="activeTab = 'rapports'" 
                        :class="activeTab === 'rapports' ? 'bg-green-600 text-white shadow-md' : 'text-gray-600 hover:text-green-600 hover:bg-gray-50'"
                        class="flex items-center px-4 py-3 rounded-md font-medium text-sm transition-all duration-200 flex-1 justify-center">
                    <i class="fas fa-chart-bar mr-2"></i>Rapports
                </button>
            </nav>
        </div>

        <!-- Onglet Gestion Personnel -->
        <div x-show="activeTab === 'gestion-personnel'" x-transition class="space-y-6">
            
            <!-- Statistiques rapides -->
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-blue-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ $stats['total_personnel'] }}</p>
                        <p class="text-xs text-gray-500">Total Personnel</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-tie text-purple-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ $stats['directeurs_count'] }}</p>
                        <p class="text-xs text-gray-500">Directeurs</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-eye text-orange-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ $stats['surveillants_count'] }}</p>
                        <p class="text-xs text-gray-500">Surveillants</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-pink-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-cog text-pink-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ $stats['secretaires_count'] }}</p>
                        <p class="text-xs text-gray-500">Secrétaires</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chalkboard-teacher text-green-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ $stats['enseignants_count'] }}</p>
                        <p class="text-xs text-gray-500">Enseignants</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-shield-alt text-gray-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ $stats['gardiens_count'] }}</p>
                        <p class="text-xs text-gray-500">Gardiens</p>
                    </div>
                </div>
            </div>
        </div>

            <!-- Tableau unifié de tout le personnel -->
            @include('personnel.partials.tableau_unifie')
        </div>

        <!-- Onglet États de Paiement -->
        <div x-show="activeTab === 'etats-paiement'" x-transition class="space-y-6">
            @include('personnel.partials.etats_paiement')
        </div>

        <!-- Onglet Archives -->
        <div x-show="activeTab === 'archives'" x-transition class="space-y-6">
            @include('personnel.partials.archives')
        </div>

        <!-- Onglet Rapports -->
        <div x-show="activeTab === 'rapports'" x-transition class="space-y-6" 
             x-data="rapportsData()" x-init="$nextTick(() => initRapports())">
            @include('personnel.partials.reports_paiement')
        </div>
    </div>
</div>

<!-- Modales -->
@include('personnel.modals.personnel')

<!-- Styles supplémentaires pour les modales -->
<style>
/* Améliorer l'affichage des modales sur mobile */
@media (max-width: 640px) {
    .personnel-modal-container {
        padding: 0.5rem !important;
    }
    
    .personnel-modal {
        max-height: 100vh !important;
        margin: 0 !important;
        border-radius: 0 !important;
    }
    
    .personnel-modal .grid {
        grid-template-columns: 1fr !important;
    }
}

/* Animation fluide pour les transitions */
.personnel-modal {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}

.personnel-modal::-webkit-scrollbar {
    width: 6px;
}

.personnel-modal::-webkit-scrollbar-track {
    background: #f7fafc;
}

.personnel-modal::-webkit-scrollbar-thumb {
    background-color: #cbd5e0;
    border-radius: 3px;
}

.personnel-modal::-webkit-scrollbar-thumb:hover {
    background-color: #a0aec0;
}

/* Focus visible amélioré */
.personnel-modal input:focus,
.personnel-modal select:focus,
.personnel-modal textarea:focus {
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
}
</style>

<script>
// Système de synchronisation global entre onglets États et Archives
window.personnelSyncData = {
    anneesScolaires: [],
    anneeActuelle: null,
    etatsPaiement: {},
    listeners: [],
    etatsListeners: []
};

// Fonction pour synchroniser les années scolaires entre les onglets
window.syncAnneesScolaires = function(data) {
    window.personnelSyncData.anneesScolaires = data.anneesScolaires || [];
    window.personnelSyncData.anneeActuelle = data.anneeActuelle || null;
    
    // Notifier tous les listeners
    window.personnelSyncData.listeners.forEach(listener => {
        if (typeof listener === 'function') {
            listener(window.personnelSyncData);
        }
    });
};

// Fonction pour synchroniser les états de paiement entre les onglets
window.syncEtatsPaiement = function(data) {
    const key = `${data.annee}-${data.mois}`;
    window.personnelSyncData.etatsPaiement[key] = data.etats || [];
    
    // Notifier tous les listeners d'états
    window.personnelSyncData.etatsListeners.forEach(listener => {
        if (typeof listener === 'function') {
            listener(data);
        }
    });
};

// Fonction pour s'abonner aux changements de synchronisation
window.addSyncListener = function(listener) {
    window.personnelSyncData.listeners.push(listener);
};

// Fonction pour s'abonner aux changements d'états de paiement
window.addEtatsListener = function(listener) {
    window.personnelSyncData.etatsListeners.push(listener);
};

// Fonction pour supprimer un listener
window.removeSyncListener = function(listener) {
    const index = window.personnelSyncData.listeners.indexOf(listener);
    if (index > -1) {
        window.personnelSyncData.listeners.splice(index, 1);
    }
};

// Fonction pour supprimer un listener d'états
window.removeEtatsListener = function(listener) {
    const index = window.personnelSyncData.etatsListeners.indexOf(listener);
    if (index > -1) {
        window.personnelSyncData.etatsListeners.splice(index, 1);
    }
};

// Fonction pour obtenir les données synchronisées
window.getSyncData = function() {
    return window.personnelSyncData;
};

// Fonction pour obtenir les états synchronisés pour une période
window.getSyncEtats = function(annee, mois) {
    const key = `${annee}-${mois}`;
    return window.personnelSyncData.etatsPaiement[key] || null;
};

// Module Rapports - Version Nettoyée
window.rapportsData = function() {
    return {
        filtres: {
            annee: '',
            mois: ''
        },
        totaux: {
            nombre_personnel: 0,
            total_retenues: 0,
            total_avances: 0,
            net_total: 0,
            montant_brut: 0
        },
        etats: [],
        chargement: false,
        erreur: null,

        // Initialisation
        initRapports() {
            this.chargerRapports();
        },

        // Chargement des rapports
        async chargerRapports() {
            this.chargement = true;
            this.erreur = null;
            
            try {
                const params = new URLSearchParams();
                if (this.filtres.annee) params.append('annee', this.filtres.annee);
                if (this.filtres.mois) params.append('mois', this.filtres.mois);
                
                const url = `{{ route("personnel.reports.data") }}?${params.toString()}`;
                const response = await fetch(url);
                
                if (!response.ok) {
                    throw new Error(`Erreur HTTP: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    this.totaux = data.totaux;
                    this.etats = data.etats;
                } else {
                    throw new Error(data.error || 'Erreur lors du chargement');
                }
                
            } catch (error) {
                console.error('Erreur rapports:', error);
                this.erreur = error.message;
                this.resetData();
            } finally {
                this.chargement = false;
            }
        },

        // Réinitialiser les données
        resetData() {
            this.totaux = {
                nombre_personnel: 0,
                total_retenues: 0,
                total_avances: 0,
                net_total: 0,
                montant_brut: 0
            };
            this.etats = [];
        },

        // Méthodes utilitaires pour les rapports
        formatMontant(montant) {
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'XOF',
                minimumFractionDigits: 0
            }).format(montant || 0);
        },



        getMoisNom(numeroMois) {
            const mois = [
                '', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
            ];
            return mois[numeroMois] || '';
        },

        formatPeriode(mois, annee) {
            return `${this.getMoisNom(mois)} ${annee}`;
        },

        formatDateTime(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR', {
                day: '2-digit',
                month: '2-digit', 
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        // Filtres rapides
        setQuickFilter(type) {
            this.quickFilter = type;
            const today = new Date();
            
            switch(type) {
                case 'today':
                    this.filters.date_debut = this.formatDate(today);
                    this.filters.date_fin = this.formatDate(today);
                    break;
                case 'yesterday':
                    const yesterday = new Date(today);
                    yesterday.setDate(yesterday.getDate() - 1);
                    this.filters.date_debut = this.formatDate(yesterday);
                    this.filters.date_fin = this.formatDate(yesterday);
                    break;
                case 'week':
                    const weekStart = new Date(today);
                    weekStart.setDate(today.getDate() - today.getDay());
                    this.filters.date_debut = this.formatDate(weekStart);
                    this.filters.date_fin = this.formatDate(today);
                    break;
                case 'month':
                    const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
                    this.filters.date_debut = this.formatDate(monthStart);
                    this.filters.date_fin = this.formatDate(today);
                    break;
                case 'custom':
                    // Pas de changement automatique pour personnalisé
                    break;
            }
            
            if (type !== 'custom') {
                this.applyFilters();
            }
        },

        // Application des filtres
        applyFilters() {
            this.filteredReports = this.allReports.filter(report => {
                // Seulement les paiements validés
                if (!report.date_validation) {
                    return false;
                }
                
                // Filtre par année scolaire
                if (this.filters.annee_id && report.annee_scolaire_id !== parseInt(this.filters.annee_id)) {
                    return false;
                }
                
                // Filtre par mois
                if (this.filters.mois && report.mois !== this.filters.mois) {
                    return false;
                }
                
                // Filtre par date de validation
                if (this.filters.date_debut || this.filters.date_fin) {
                    const validationDate = new Date(report.date_validation).toISOString().split('T')[0];
                    
                    if (this.filters.date_debut && validationDate < this.filters.date_debut) {
                        return false;
                    }
                    
                    if (this.filters.date_fin && validationDate > this.filters.date_fin) {
                        return false;
                    }
                }
                
                return true;
            });
            
            this.currentPage = 1;
            this.updateStats();
        },

        // Mise à jour des statistiques
        updateStats() {
            const uniquePersonnel = [...new Set(this.filteredReports.map(r => r.personnel_id))];
            
            this.reportStats = {
                valides: this.filteredReports.length,
                personnel_paye: uniquePersonnel.length,
                montant_total: this.filteredReports.reduce((sum, r) => sum + (r.net_a_payer || 0), 0)
            };
        },



        // Export de données
        async exportData(format) {
            const params = new URLSearchParams(this.filters);
            params.append('format', format);
            params.append('quick_filter', this.quickFilter);
            
            window.open(`{{ route("personnel.reports.export") }}?${params.toString()}`, '_blank');
        },

        // Pagination
        get totalPages() {
            return Math.ceil(this.filteredReports.length / this.itemsPerPage);
        },

        get paginatedReports() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredReports.slice(start, end);
        },

        get visiblePages() {
            const total = this.totalPages;
            const current = this.currentPage;
            const range = 5;
            
            let start = Math.max(1, current - Math.floor(range / 2));
            let end = Math.min(total, start + range - 1);
            
            if (end - start + 1 < range) {
                start = Math.max(1, end - range + 1);
            }
            
            const pages = [];
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            return pages;
        },

        goToPage(page) {
            this.currentPage = page;
        },

        previousPage() {
            if (this.currentPage > 1) this.currentPage--;
        },

        nextPage() {
            if (this.currentPage < this.totalPages) this.currentPage++;
        },

        // Formatage
        formatCurrency(amount) {
            return new Intl.NumberFormat('fr-FR').format(amount) + ' FCFA';
        },

        formatDate(date) {
            if (!date) return '';
            if (typeof date === 'string') {
                return new Date(date).toLocaleDateString('fr-FR');
            }
            // Pour les objets Date
            return date.toISOString().split('T')[0];
        },

        formatDateTime(dateString) {
            if (!dateString) return '';
            return new Date(dateString).toLocaleString('fr-FR');
        },

        formatPeriode(mois, annee) {
            const moisNames = {
                '01': 'Janvier', '02': 'Février', '03': 'Mars', '04': 'Avril',
                '05': 'Mai', '06': 'Juin', '07': 'Juillet', '08': 'Août',
                '09': 'Septembre', '10': 'Octobre', '11': 'Novembre', '12': 'Décembre'
            };
            return `${moisNames[mois] || mois} ${annee}`;
        },

        // Actions détaillées
        viewDetails(etatId) {
            window.open(`{{ url('/personnel/etats') }}/${etatId}/apercu`, '_blank');
        },

        printEtat(etatId) {
            window.open(`{{ url('/personnel/etats') }}/${etatId}/print`, '_blank');
        },

        // Notification
        showNotification(message, type = 'info') {
            // Utiliser les notifications existantes du système
            const alertClass = type === 'success' ? 'alert-success' : 
                              type === 'error' ? 'alert-danger' : 'alert-info';
            
            const notification = document.createElement('div');
            notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 5000);
        }
    };
};
</script>

@endsection