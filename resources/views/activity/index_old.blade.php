@extends('layouts.app')

@section('title', 'Logs d\'Activité')

@section('content')
<div class="container mx-auto px-4 py-6" x-data="activityLogs()">
    
    <!-- En-tête -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Logs d'Activité</h1>
            <p class="text-gray-600">Suivi des actions importantes du système</p>
        </div>
        <div class="flex space-x-2">
            <button @click="exportLogs()" class="btn btn-success">
                <i class="fas fa-download mr-1"></i>Exporter
            </button>
            <button @click="refreshLogs()" class="btn btn-primary">
                <i class="fas fa-sync-alt mr-1"></i>Actualiser
            </button>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="bg-blue-100 p-2 rounded">
                    <i class="fas fa-list text-blue-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-2xl font-bold">{{ $todayStats['total_today'] }}</p>
                    <p class="text-sm text-gray-600">Aujourd'hui</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="bg-green-100 p-2 rounded">
                            <i class="fas fa-sign-in-alt text-green-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-lg font-semibold text-gray-900">{{ $todayStats['logins_today'] }}</p>
                        <p class="text-xs text-gray-500">Connexions</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-plus text-purple-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-lg font-semibold text-gray-900">{{ $todayStats['creates_today'] }}</p>
                        <p class="text-xs text-gray-500">Créations</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-edit text-yellow-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-lg font-semibold text-gray-900">{{ $todayStats['updates_today'] }}</p>
                        <p class="text-xs text-gray-500">Modifications</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-trash text-red-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-lg font-semibold text-gray-900">{{ $todayStats['deletes_today'] }}</p>
                        <p class="text-xs text-gray-500">Suppressions</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-download text-indigo-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-lg font-semibold text-gray-900">{{ $todayStats['downloads_today'] }}</p>
                        <p class="text-xs text-gray-500">Téléchargements</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-pink-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-pink-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-lg font-semibold text-gray-900">{{ $todayStats['unique_users_today'] }}</p>
                        <p class="text-xs text-gray-500">Utilisateurs Uniques</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-teal-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-globe text-teal-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-lg font-semibold text-gray-900">{{ $todayStats['unique_ips_today'] }}</p>
                        <p class="text-xs text-gray-500">IPs Uniques</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-lg shadow mb-6 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Filtres</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                
                <!-- Recherche -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Recherche</label>
                    <input x-model="filters.search" @input="applyFilters()" type="text" 
                           placeholder="Rechercher dans les logs..."
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Utilisateur -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Utilisateur</label>
                    <select x-model="filters.user" @change="applyFilters()" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Toutes les actions</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}">{{ $action }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Modèle -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Module</label>
                    <select x-model="filters.model" @change="applyFilters()" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tous les modules</option>
                        @foreach($models as $model)
                            <option value="{{ $model }}">{{ $model }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date début -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date début</label>
                    <input x-model="filters.date_from" @change="applyFilters()" type="date" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Date fin -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date fin</label>
                    <input x-model="filters.date_to" @change="applyFilters()" type="date" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div class="mt-4 flex space-x-2">
                <button @click="resetFilters()" class="text-gray-600 hover:text-gray-800 text-sm">
                    <i class="fas fa-times mr-1"></i>Réinitialiser les filtres
                </button>
                <button @click="setToday()" class="text-blue-600 hover:text-blue-800 text-sm">
                    <i class="fas fa-calendar-day mr-1"></i>Aujourd'hui
                </button>
                <button @click="setWeek()" class="text-blue-600 hover:text-blue-800 text-sm">
                    <i class="fas fa-calendar-week mr-1"></i>Cette semaine
                </button>
            </div>
        </div>

        <!-- Liste des activités -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    Activités récentes
                    <span x-text="activities.length" class="text-sm text-gray-500 ml-2"></span>
                </h3>
            </div>

            <!-- Loading -->
            <div x-show="loading" class="p-6 text-center">
                <i class="fas fa-spinner fa-spin text-gray-400 text-2xl"></i>
                <p class="text-gray-500 mt-2">Chargement des activités...</p>
            </div>

            <!-- Liste -->
            <div x-show="!loading" class="divide-y divide-gray-200">
                <template x-for="activity in activities" :key="activity.id">
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start space-x-4">
                            <!-- Icône d'action -->
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                                     :class="{
                                         'bg-green-100': activity.action === 'CREATE',
                                         'bg-blue-100': activity.action === 'LOGIN',
                                         'bg-yellow-100': activity.action === 'UPDATE',
                                         'bg-red-100': activity.action === 'DELETE',
                                         'bg-purple-100': activity.action === 'DOWNLOAD',
                                         'bg-gray-100': !['CREATE', 'LOGIN', 'UPDATE', 'DELETE', 'DOWNLOAD'].includes(activity.action)
                                     }">
                                    <i class="text-sm"
                                       :class="{
                                           'fas fa-plus text-green-600': activity.action === 'CREATE',
                                           'fas fa-sign-in-alt text-blue-600': activity.action === 'LOGIN',
                                           'fas fa-edit text-yellow-600': activity.action === 'UPDATE',
                                           'fas fa-trash text-red-600': activity.action === 'DELETE',
                                           'fas fa-download text-purple-600': activity.action === 'DOWNLOAD',
                                           'fas fa-eye text-gray-600': activity.action === 'VIEW',
                                           'fas fa-search text-gray-600': activity.action === 'SEARCH',
                                           'fas fa-print text-gray-600': activity.action === 'PRINT',
                                           'fas fa-sign-out-alt text-gray-600': activity.action === 'LOGOUT',
                                           'fas fa-question text-gray-600': !['CREATE', 'LOGIN', 'UPDATE', 'DELETE', 'DOWNLOAD', 'VIEW', 'SEARCH', 'PRINT', 'LOGOUT'].includes(activity.action)
                                       }"></i>
                                </div>
                            </div>

                            <!-- Contenu -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-900" x-text="activity.description"></p>
                                    <div class="flex items-center space-x-2 text-xs text-gray-500">
                                        <span x-text="activity.created_at_human"></span>
                                        <span>•</span>
                                        <span x-text="activity.created_at"></span>
                                    </div>
                                </div>
                                
                                <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded" x-text="activity.user"></span>
                                    <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded" x-text="activity.action"></span>
                                    <span x-show="activity.model" class="bg-green-100 text-green-800 px-2 py-1 rounded" x-text="activity.model"></span>
                                    <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded">
                                        <i class="fas fa-globe mr-1"></i><span x-text="activity.ip_address"></span>
                                    </span>
                                    <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded">
                                        <i class="fas fa-browser mr-1"></i><span x-text="activity.browser"></span>
                                    </span>
                                </div>

                                <!-- Détails -->
                                <div x-show="activity.details && Object.keys(activity.details).length > 0" 
                                     class="mt-3 p-3 bg-gray-50 rounded text-xs">
                                    <template x-for="(value, key) in activity.details" :key="key">
                                        <div class="mb-1">
                                            <span class="font-medium" x-text="key + ':'"></span>
                                            <span x-text="JSON.stringify(value)"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Pagination -->
            <div x-show="!loading && pagination.last_page > 1" class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Affichage de <span x-text="((pagination.current_page - 1) * pagination.per_page) + 1"></span> à 
                        <span x-text="Math.min(pagination.current_page * pagination.per_page, pagination.total)"></span> 
                        sur <span x-text="pagination.total"></span> résultats
                    </div>
                    <div class="flex space-x-2">
                        <button @click="changePage(pagination.current_page - 1)" 
                                :disabled="pagination.current_page <= 1"
                                :class="pagination.current_page <= 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                                class="px-3 py-2 border border-gray-300 rounded text-sm">
                            Précédent
                        </button>
                        <button @click="changePage(pagination.current_page + 1)" 
                                :disabled="pagination.current_page >= pagination.last_page"
                                :class="pagination.current_page >= pagination.last_page ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                                class="px-3 py-2 border border-gray-300 rounded text-sm">
                            Suivant
                        </button>
                    </div>
                </div>
            </div>

            <!-- Message si aucun résultat -->
            <div x-show="!loading && activities.length === 0" class="p-6 text-center">
                <i class="fas fa-search text-gray-400 text-3xl mb-4"></i>
                <p class="text-gray-500">Aucune activité trouvée pour les critères sélectionnés.</p>
            </div>
        </div>
    </div>
</div>

<script>
function activityLogs() {
    return {
        activities: [],
        loading: false,
        filters: {
            search: '',
            user: '',
            action: '',
            model: '',
            date_from: '',
            date_to: '',
            level: ''
        },
        pagination: {
            current_page: 1,
            last_page: 1,
            total: 0,
            per_page: 25
        },
        
        init() {
            this.loadActivities();
            // Auto-refresh toutes les 30 secondes
            setInterval(() => {
                this.loadActivities(false);
            }, 30000);
        },
        
        async loadActivities(showLoading = true) {
            if (showLoading) this.loading = true;
            
            try {
                const params = new URLSearchParams({
                    page: this.pagination.current_page,
                    ...this.filters
                });
                
                const response = await fetch(`{{ route('activity.get-activities') }}?${params}`);
                const data = await response.json();
                
                this.activities = data.activities;
                this.pagination = data.pagination;
            } catch (error) {
                console.error('Erreur lors du chargement des activités:', error);
            } finally {
                this.loading = false;
            }
        },
        
        applyFilters() {
            this.pagination.current_page = 1;
            this.loadActivities();
        },
        
        changePage(page) {
            if (page >= 1 && page <= this.pagination.last_page) {
                this.pagination.current_page = page;
                this.loadActivities();
            }
        },
        
        resetFilters() {
            this.filters = {
                search: '',
                user: '',
                action: '',
                model: '',
                date_from: '',
                date_to: '',
                level: ''
            };
            this.applyFilters();
        },
        
        setToday() {
            const today = new Date().toISOString().split('T')[0];
            this.filters.date_from = today;
            this.filters.date_to = today;
            this.applyFilters();
        },
        
        setWeek() {
            const today = new Date();
            const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
            this.filters.date_from = weekAgo.toISOString().split('T')[0];
            this.filters.date_to = today.toISOString().split('T')[0];
            this.applyFilters();
        },
        
        refreshLogs() {
            this.loadActivities();
        },
        
        exportLogs() {
            const params = new URLSearchParams(this.filters);
            window.open(`{{ route('activity.export') }}?${params}`, '_blank');
        }
    }
}
</script>
@endsection