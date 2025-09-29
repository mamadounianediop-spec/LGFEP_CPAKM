@extends('layouts.app')

@section('title', 'Logs d\'Activité')

@section('content')
<div class="min-h-screen bg-gray-50 py-6" x-data="activityLogs()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- En-tête -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 mb-2">
                    <div class="bg-purple-100 p-2 rounded-lg">
                        <i class="fas fa-history text-purple-600 text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Logs d'Activité</h1>
                        <p class="text-sm text-gray-600">Suivi des actions importantes du système</p>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button @click="exportLogs()" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                        <i class="fas fa-download mr-2"></i>Exporter
                    </button>
                    <button @click="refreshLogs()" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                        <i class="fas fa-sync-alt mr-2"></i>Actualiser
                    </button>
                    <button @click="logoutAccess()" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                        <i class="fas fa-shield-alt mr-2"></i>Verrouiller
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistiques rapides -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-list text-blue-600 text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-3 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Aujourd'hui</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $todayStats['total_today'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-green-600 text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-3 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Utilisateurs actifs</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $todayStats['unique_users_today'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-edit text-yellow-600 text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-3 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Modifications</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $todayStats['updates_today'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-trash text-red-600 text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-3 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Suppressions</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $todayStats['deletes_today'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-filter text-purple-600 mr-2"></i>
                Filtres de recherche
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                
                <!-- Recherche -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Recherche rapide</label>
                    <div class="relative">
                        <input x-model="filters.search" @input="applyFilters()" type="text" 
                               placeholder="Rechercher dans les logs..."
                               class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-md text-sm focus:ring-purple-500 focus:border-purple-500">
                        <div class="absolute left-3 top-2.5">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Utilisateur -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Utilisateur</label>
                    <select x-model="filters.user" @change="applyFilters()" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Tous les utilisateurs</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Action -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Action</label>
                    <select x-model="filters.action" @change="applyFilters()" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Toutes les actions</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}">{{ $action }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Date début -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date début</label>
                    <input x-model="filters.date_from" @change="applyFilters()" type="date" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-purple-500 focus:border-purple-500">
                </div>
                
                <!-- Date fin -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date fin</label>
                    <input x-model="filters.date_to" @change="applyFilters()" type="date" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-purple-500 focus:border-purple-500">
                </div>

                <!-- Boutons -->
                <div class="md:col-span-2 lg:col-span-5 flex justify-between items-center pt-4 border-t border-gray-200">
                    <div class="flex space-x-3">
                        <button type="button" @click="resetFilters()" 
                               class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                            <i class="fas fa-times mr-2"></i>Réinitialiser
                        </button>
                        <button type="button" @click="loadStorageStats()" 
                               class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                            <i class="fas fa-chart-bar mr-2"></i>Statistiques
                        </button>
                    </div>
                    <div class="flex space-x-2">
                        <button type="button" @click="cleanupLogs()" 
                               class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-orange-600 rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                            <i class="fas fa-broom mr-2"></i>Nettoyage
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table des logs -->
        <div class="bg-white shadow-sm rounded-lg border">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-list-ul text-purple-600 mr-2"></i>
                        Activités récentes
                    </h3>
                    <div class="text-sm text-gray-500">
                        <span x-text="total"></span> résultats
                    </div>
                </div>
            </div>
            
            <div class="overflow-hidden">
                <div x-show="loading" class="flex flex-col items-center justify-center py-12">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                    <p class="text-gray-500 mt-3 text-sm">Chargement des données...</p>
                </div>
            
            <div x-show="!loading">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Utilisateur
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Action
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                IP
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="activity in activities" :key="activity.id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 bg-gradient-to-br from-purple-100 to-purple-200 rounded-full flex items-center justify-center">
                                                <i class="fas fa-user text-purple-600 text-sm"></i>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900" x-text="activity.user?.name || 'Système'"></div>
                                            <div class="text-xs text-gray-500" x-text="activity.user?.role || 'System'"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <i :class="activity.action?.icon || 'fas fa-info-circle'" 
                                           :style="`color: ${getActionColorClass(activity.action?.color)}`" 
                                           class="mr-2"></i>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                              :class="getActionBadgeClass(activity.action?.code)" 
                                              x-text="activity.action?.label || activity.action?.code">
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900" x-text="activity.summary || activity.description"></div>
                                    <div class="text-xs text-gray-500 mt-1" x-show="activity.target?.model">
                                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-gray-700">
                                            <span x-text="activity.target?.model_label || activity.target?.model"></span>
                                            <span x-show="activity.target?.identifier" x-text="' ' + activity.target?.identifier"></span>
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1" x-show="activity.context?.method">
                                        <span x-text="activity.context?.method"></span>
                                        <span x-show="activity.context?.url" x-text="' • ' + activity.context?.url"></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>
                                        <div class="font-medium" x-text="activity.context?.ip_address || 'Inconnue'"></div>
                                        <div class="text-xs text-gray-400" x-text="activity.context?.browser || 'N/A'"></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>
                                        <div class="font-medium" x-text="activity.created_at_formatted || 'Date inconnue'"></div>
                                        <div class="text-xs text-gray-400" x-text="activity.created_at_human"></div>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        
                        <!-- Message quand aucune donnée -->
                        <tr x-show="!loading && activities.length === 0">
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-history text-4xl text-gray-300 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune activité trouvée</h3>
                                    <p class="text-sm text-gray-500">Essayez d'ajuster vos filtres ou attendez de nouvelles activités.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <div class="px-6 py-3 border-t border-gray-200 bg-gray-50" x-show="total > 0">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Affichage de <span class="font-medium" x-text="total > 0 ? (currentPage - 1) * perPage + 1 : 0"></span> à 
                            <span class="font-medium" x-text="total > 0 ? Math.min(currentPage * perPage, total) : 0"></span> sur 
                            <span class="font-medium" x-text="total || 0"></span> résultats
                        </div>
                        <div class="flex space-x-1">
                            <button @click="previousPage()" :disabled="currentPage === 1" 
                                    :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-purple-50'"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                                <i class="fas fa-chevron-left mr-1"></i>Précédent
                            </button>
                            <button @click="nextPage()" :disabled="currentPage >= lastPage" 
                                    :class="currentPage >= lastPage ? 'opacity-50 cursor-not-allowed' : 'hover:bg-purple-50'"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                                Suivant<i class="fas fa-chevron-right ml-1"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div><script>
function activityLogs() {
    return {
        activities: [],
        loading: false,
        currentPage: 1,
        lastPage: 1,
        perPage: 20,
        total: 0,
        filters: {
            search: '',
            user: '',
            action: '',
            model: '',
            date_from: '',
            date_to: ''
        },
        
        init() {
            this.loadActivities();
        },
        
        async loadActivities() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page: this.currentPage,
                    per_page: this.perPage,
                    ...this.filters
                });
                
                const response = await fetch(`{{ route('activity.get-activities') }}?${params}`);
                
                // Vérifier si un mot de passe est requis
                if (response.status === 403) {
                    const errorData = await response.json();
                    if (errorData.requires_password) {
                        this.handlePasswordRequired();
                        return;
                    }
                }
                
                const data = await response.json();
                this.activities = data.data || [];
                this.currentPage = data.current_page || 1;
                this.lastPage = data.last_page || 1;
                this.total = data.total || 0;
                this.perPage = data.per_page || 25;
                
                // Debug temporaire
                console.log('Données reçues:', data);
                console.log('Nombre d\'activités:', this.activities.length);
            } catch (error) {
                console.error('Erreur lors du chargement des activités:', error);
            }
            this.loading = false;
        },
        
        applyFilters() {
            this.currentPage = 1;
            this.loadActivities();
        },
        
        refreshLogs() {
            this.loadActivities();
        },

        resetFilters() {
            this.filters = {
                search: '',
                user: '',
                action: '',
                model: '',
                date_from: '',
                date_to: ''
            };
            this.currentPage = 1;
            this.loadActivities();
        },

        async loadStorageStats() {
            try {
                const response = await fetch(`{{ route('activity.storage-stats') }}`);
                const data = await response.json();
                
                if (data.success) {
                    const stats = data.stats;
                    alert(`Statistiques de stockage:\n` +
                          `• Total des logs: ${stats.total_logs.toLocaleString()}\n` +
                          `• Taille: ${stats.size_mb} MB\n` +
                          `• Plus ancien: ${stats.oldest_log ? new Date(stats.oldest_log).toLocaleDateString('fr-FR') : 'Aucun'}\n` +
                          `• Plus récent: ${stats.newest_log ? new Date(stats.newest_log).toLocaleDateString('fr-FR') : 'Aucun'}`);
                }
            } catch (error) {
                console.error('Erreur lors du chargement des statistiques:', error);
            }
        },

        async cleanupLogs() {
            if (!confirm('Voulez-vous nettoyer les anciens logs ? Cette action est irréversible.')) {
                return;
            }

            const type = confirm('Cliquez OK pour un nettoyage intelligent, Annuler pour un nettoyage standard (30 jours)') ? 'smart' : 'basic';
            
            try {
                const formData = new FormData();
                formData.append('cleanup_type', type);
                if (type === 'basic') {
                    formData.append('days', '30');
                }
                formData.append('_token', '{{ csrf_token() }}');

                const response = await fetch(`{{ route('activity.cleanup') }}`, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert(`Nettoyage terminé !\n${data.message}`);
                    this.loadActivities(); // Recharger les données
                } else {
                    alert('Erreur lors du nettoyage');
                }
            } catch (error) {
                console.error('Erreur lors du nettoyage:', error);
                alert('Erreur lors du nettoyage');
            }
        },
        
        previousPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.loadActivities();
            }
        },
        
        nextPage() {
            if (this.currentPage < this.lastPage) {
                this.currentPage++;
                this.loadActivities();
            }
        },
        
        async exportLogs() {
            const params = new URLSearchParams(this.filters);
            window.open(`{{ route('activity.export') }}?${params}`, '_blank');
        },
        
        getActionBadgeClass(action) {
            const classes = {
                'CREATE': 'bg-green-100 text-green-800',
                'UPDATE': 'bg-blue-100 text-blue-800',
                'DELETE': 'bg-red-100 text-red-800',
                'LOGIN': 'bg-purple-100 text-purple-800',
                'LOGOUT': 'bg-gray-100 text-gray-800',
                'DOWNLOAD': 'bg-yellow-100 text-yellow-800',
                'PRINT': 'bg-indigo-100 text-indigo-800',
                'EXPORT': 'bg-orange-100 text-orange-800',
                'SEARCH': 'bg-teal-100 text-teal-800',
                'VIEW': 'bg-gray-100 text-gray-800'
            };
            return classes[action] || 'bg-gray-100 text-gray-800';
        },

        getActionColorClass(color) {
            const colors = {
                'green': '#10b981',
                'blue': '#3b82f6',
                'red': '#ef4444',
                'purple': '#8b5cf6',
                'gray': '#6b7280',
                'yellow': '#f59e0b',
                'indigo': '#6366f1',
                'orange': '#f97316',
                'teal': '#14b8a6'
            };
            return colors[color] || '#6b7280';
        },
        
        formatDate(dateString) {
            // Cette fonction n'est plus utilisée car on utilise created_at_formatted du serveur
            return dateString || 'Date inconnue';
        },

        handlePasswordRequired() {
            const password = prompt('Accès protégé - Veuillez saisir le mot de passe pour accéder aux logs d\'activité:');
            if (password) {
                this.submitPassword(password);
            } else {
                // Rediriger vers le dashboard si l'utilisateur annule
                window.location.href = '{{ route('dashboard') }}';
            }
        },

        async submitPassword(password) {
            try {
                const formData = new FormData();
                formData.append('activity_password', password);
                formData.append('_token', '{{ csrf_token() }}');

                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Mot de passe correct, recharger les activités
                    this.loadActivities();
                } else {
                    // Mot de passe incorrect
                    alert('Mot de passe incorrect. Veuillez réessayer.');
                    this.handlePasswordRequired();
                }
            } catch (error) {
                console.error('Erreur lors de la vérification du mot de passe:', error);
                alert('Erreur lors de la vérification. Veuillez recharger la page.');
            }
        },

        async logoutAccess() {
            if (!confirm('Voulez-vous vraiment verrouiller l\'accès aux logs ? Vous devrez saisir à nouveau le mot de passe.')) {
                return;
            }

            try {
                const response = await fetch('{{ route('activity.logout') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    alert('Accès aux logs verrouillé avec succès.');
                    // Rediriger vers le dashboard
                    window.location.href = '{{ route('dashboard') }}';
                } else {
                    alert('Erreur lors du verrouillage.');
                }
            } catch (error) {
                console.error('Erreur lors du verrouillage:', error);
                alert('Erreur lors du verrouillage.');
            }
        }
    }
}
</script>
@endsection