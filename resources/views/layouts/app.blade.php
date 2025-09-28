<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administration - CPAKM')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Styles personnalisés - prévention débordement et responsive -->
    <style>
        /* PRÉVENTION DU DÉBORDEMENT HORIZONTAL */
        * {
            box-sizing: border-box;
        }
        
        html, body {
            max-width: 100%;
            overflow-x: hidden;
        }
        
        /* CORRECTIONS RESPONSIVE MODÉRÉES */
        @media (min-width: 1440px) {
            .max-w-7xl {
                max-width: 1400px !important;
            }
            
            h1 { font-size: 2.5rem !important; }
            h2 { font-size: 2rem !important; }
            h3 { font-size: 1.5rem !important; }
            
            .btn, button {
                padding: 0.75rem 1.5rem !important;
                font-size: 1rem !important;
            }
            
            .text-sm { font-size: 0.95rem !important; }
            .text-base { font-size: 1.1rem !important; }
            .text-lg { font-size: 1.2rem !important; }
        }
        
        /* Navigation responsive - prévention débordement */
        nav .hidden.md\\:flex {
            overflow: hidden;
            white-space: nowrap;
        }
        
        nav a {
            flex-shrink: 1;
            min-width: 0;
        }
        
        /* Animation d'apparition pour les modales */
        #notification-modal .modal-content {
            animation: fadeInScale 0.3s ease-out;
        }
        
        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        /* Style pour les notifications toast */
        .notification-toast {
            pointer-events: auto;
            transform: translateX(100%);
            opacity: 0;
            transition: all 0.3s ease-in-out;
        }
        
        .notification-toast.show {
            transform: translateX(0);
            opacity: 1;
        }

        /* Fermeture des modales avec clic extérieur */
        #notification-modal {
            backdrop-filter: blur(1px);
        }
    </style>
</head>
<body class="bg-gray-50 font-inter">
    <!-- Navigation principale -->
    <nav class="bg-white shadow-lg border-b border-gray-200 print:hidden" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8 xl:px-10">
            <div class="flex justify-between items-center h-16 xl:h-18">
                <!-- Logo et titre -->
                <div class="flex items-center">
                    <div class="flex-shrink-0 mr-4 md:mr-8 xl:mr-10">
                        <h1 class="text-lg md:text-xl xl:text-2xl font-bold text-indigo-600">CPAKM</h1>
                        <p class="text-xs xl:text-sm text-gray-500 hidden sm:block">Gestion Financière</p>
                    </div>
                </div>

                <!-- Bouton menu mobile -->
                <div class="md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" 
                            class="text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700 p-2">
                        <svg class="h-6 w-6 fill-current" viewBox="0 0 24 24">
                            <path x-show="!mobileMenuOpen" fill-rule="evenodd" d="M4 6h16v2H4V6zm0 5h16v2H4v-2zm0 5h16v2H4v-2z"/>
                            <path x-show="mobileMenuOpen" fill-rule="evenodd" d="M18.278 16.864a1 1 0 0 1-1.414 1.414l-4.829-4.828-4.828 4.828a1 1 0 0 1-1.414-1.414l4.828-4.829-4.828-4.828a1 1 0 0 1 1.414-1.414l4.829 4.828 4.828-4.828a1 1 0 1 1 1.414 1.414l-4.828 4.829 4.828 4.828z"/>
                        </svg>
                    </button>
                </div>

                <!-- Menu de navigation Desktop -->
                <div class="hidden md:flex items-center space-x-1 lg:space-x-4 xl:space-x-6 flex-1 justify-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center text-gray-700 hover:text-indigo-600 px-2 lg:px-3 xl:px-4 py-2 text-xs lg:text-sm xl:text-base font-medium transition-colors rounded-lg hover:bg-gray-50 {{ request()->routeIs('dashboard') ? 'text-indigo-600 bg-indigo-50 border border-indigo-200' : '' }}">
                        <i class="fas fa-tachometer-alt mr-1 lg:mr-2"></i>
                        <span class="hidden lg:inline">Dashboard</span>
                        <span class="lg:hidden">Home</span>
                    </a>
                    
                    <a href="{{ route('inscriptions.index') }}" class="flex items-center text-gray-700 hover:text-indigo-600 px-2 lg:px-3 xl:px-4 py-2 text-xs lg:text-sm xl:text-base font-medium transition-colors rounded-lg hover:bg-gray-50 {{ request()->routeIs('inscriptions.*') ? 'text-indigo-600 bg-indigo-50 border border-indigo-200' : '' }}">
                        <i class="fas fa-user-graduate mr-1 lg:mr-2"></i>
                        <span class="hidden lg:inline">Inscriptions</span>
                        <span class="lg:hidden">Inscr.</span>
                    </a>
                    
                    <a href="{{ route('mensualites.index') }}" class="flex items-center text-gray-700 hover:text-indigo-600 px-2 lg:px-3 xl:px-4 py-2 text-xs lg:text-sm xl:text-base font-medium transition-colors rounded-lg hover:bg-gray-50 {{ request()->routeIs('mensualites.*') ? 'text-indigo-600 bg-indigo-50 border border-indigo-200' : '' }}">
                        <i class="fas fa-money-bill-wave mr-1 lg:mr-2"></i>
                        <span class="hidden lg:inline">Mensualités</span>
                        <span class="lg:hidden">Pay.</span>
                    </a>
                    
                    @if(auth()->user()->isAdminOrDirector())
                    <a href="{{ route('personnel.index') }}" class="flex items-center text-gray-700 hover:text-indigo-600 px-2 lg:px-3 xl:px-4 py-2 text-xs lg:text-sm xl:text-base font-medium transition-colors rounded-lg hover:bg-gray-50 {{ request()->routeIs('personnel.*') ? 'text-indigo-600 bg-indigo-50 border border-indigo-200' : '' }}">
                        <i class="fas fa-users mr-1 lg:mr-2"></i>
                        <span class="hidden lg:inline">Personnel</span>
                        <span class="lg:hidden">Staff</span>
                    </a>
                    
                    <a href="{{ route('services.index') }}" class="flex items-center text-gray-700 hover:text-indigo-600 px-2 lg:px-3 xl:px-4 py-2 text-xs lg:text-sm xl:text-base font-medium transition-colors rounded-lg hover:bg-gray-50 {{ request()->routeIs('services.*') ? 'text-indigo-600 bg-indigo-50 border border-indigo-200' : '' }}">
                        <i class="fas fa-cogs mr-1 lg:mr-2"></i>
                        <span class="hidden lg:inline">Services</span>
                        <span class="lg:hidden">Serv.</span>
                    </a>
                    
                    <a href="{{ route('parametres.index') }}" class="flex items-center text-gray-700 hover:text-indigo-600 px-2 lg:px-3 xl:px-4 py-2 text-xs lg:text-sm xl:text-base font-medium transition-colors rounded-lg hover:bg-gray-50 {{ request()->routeIs('parametres.*') ? 'text-indigo-600 bg-indigo-50 border border-indigo-200' : '' }}">
                        <i class="fas fa-cog mr-1 lg:mr-2"></i>
                        <span class="hidden lg:inline">Paramètres</span>
                        <span class="lg:hidden">Config</span>
                    </a>
                    @endif
                </div>

                <!-- User menu -->
                <div class="hidden md:flex items-center space-x-2 lg:space-x-3 ml-auto">
                    <div class="flex items-center space-x-1 lg:space-x-2">
                        <span class="text-gray-700 text-xs lg:text-sm font-medium max-w-20 lg:max-w-32 truncate">
                            {{ Str::limit(Auth::user()->name, 10, '...') }}
                        </span>
                        <span class="inline-flex items-center px-1 lg:px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                            {{ substr(ucfirst(Auth::user()->role), 0, 3) }}
                        </span>
                    </div>
                    <div class="border-l border-gray-200 pl-2 lg:pl-3">
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="flex items-center text-gray-500 hover:text-red-600 transition-colors p-1.5 rounded-lg hover:bg-gray-100" title="Se déconnecter">
                                <i class="fas fa-sign-out-alt text-sm"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu mobile -->
        <div class="md:hidden border-t border-gray-200" x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95">
            <div class="px-4 pt-2 pb-3 space-y-1">
                <!-- Info utilisateur mobile -->
                <div class="flex items-center justify-between px-3 py-2 bg-gray-50 rounded-md mb-3">
                    <div class="flex items-center space-x-2">
                        <span class="text-gray-700 text-sm font-medium truncate max-w-32">
                            {{ Auth::user()->name }}
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                            {{ substr(ucfirst(Auth::user()->role), 0, 5) }}
                        </span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-red-600 p-1" title="Se déconnecter">
                            <i class="fas fa-sign-out-alt text-sm"></i>
                        </button>
                    </form>
                </div>
                
                <!-- Navigation mobile -->
                <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-3 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md transition-colors {{ request()->routeIs('dashboard') ? 'text-indigo-600 bg-indigo-50' : '' }}" @click="mobileMenuOpen = false">
                    <i class="fas fa-tachometer-alt mr-3 w-5 text-center"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('inscriptions.index') }}" class="flex items-center px-3 py-3 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md transition-colors {{ request()->routeIs('inscriptions.*') ? 'text-indigo-600 bg-indigo-50' : '' }}" @click="mobileMenuOpen = false">
                    <i class="fas fa-user-graduate mr-3 w-5 text-center"></i>
                    <span>Inscriptions</span>
                </a>
                <a href="{{ route('mensualites.index') }}" class="flex items-center px-3 py-3 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md transition-colors {{ request()->routeIs('mensualites.*') ? 'text-indigo-600 bg-indigo-50' : '' }}" @click="mobileMenuOpen = false">
                    <i class="fas fa-money-bill-wave mr-3 w-5 text-center"></i>
                    <span>Mensualités</span>
                </a>
                @if(auth()->user()->isAdminOrDirector())
                <a href="{{ route('personnel.index') }}" class="flex items-center px-3 py-3 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md transition-colors {{ request()->routeIs('personnel.*') ? 'text-indigo-600 bg-indigo-50' : '' }}" @click="mobileMenuOpen = false">
                    <i class="fas fa-users mr-3 w-5 text-center"></i>
                    <span>Personnel</span>
                </a>
                <a href="{{ route('services.index') }}" class="flex items-center px-3 py-3 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md transition-colors {{ request()->routeIs('services.*') ? 'text-indigo-600 bg-indigo-50' : '' }}" @click="mobileMenuOpen = false">
                    <i class="fas fa-cogs mr-3 w-5 text-center"></i>
                    <span>Services</span>
                </a>
                <a href="{{ route('parametres.index') }}" class="flex items-center px-3 py-3 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md transition-colors {{ request()->routeIs('parametres.*') ? 'text-indigo-600 bg-indigo-50' : '' }}" @click="mobileMenuOpen = false">
                    <i class="fas fa-cog mr-3 w-5 text-center"></i>
                    <span>Paramètres</span>
                </a>
                @endif
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <main class="min-h-screen">
        <div class="w-full max-w-full overflow-x-hidden px-2 sm:px-4 lg:px-8 xl:px-10 py-4 xl:py-6">
            @yield('content')
        </div>
    </main>

    <!-- Modale de confirmation -->
    <div id="confirmation-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 text-center mb-2" id="modal-title">Confirmer l'action</h3>
            <p class="text-sm text-gray-500 text-center mb-6" id="modal-message">Êtes-vous sûr de vouloir effectuer cette action ?</p>
            <div class="flex space-x-3">
                <button id="modal-cancel" class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Annuler
                </button>
                <button id="modal-confirm" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Confirmer
                </button>
            </div>
        </div>
    </div>

    <!-- Modale d'avertissement -->
    <div id="warning-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-yellow-100 rounded-full mb-4">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 text-center mb-2" id="warning-modal-title">Avertissement</h3>
            <p class="text-sm text-gray-500 text-center mb-6" id="warning-modal-message">Message d'avertissement</p>
            <div class="flex justify-center">
                <button id="warning-modal-ok" class="px-6 py-2 text-sm font-medium text-white bg-yellow-600 border border-transparent rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    Compris
                </button>
            </div>
        </div>
    </div>

    <!-- Messages flash -->
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showNotification('{{ session('success') }}', 'success');
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showNotification('{{ session('error') }}', 'error');
            });
        </script>
    @endif

    <script>
        // Système de notifications professionnelles
        function showNotification(message, type = 'info') {
            const container = document.getElementById('notification-container');
            const notification = document.createElement('div');
            
            const icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            };
            
            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                warning: 'bg-yellow-500',
                info: 'bg-blue-500'
            };
            
            notification.className = `${colors[type]} text-white px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full opacity-0 max-w-sm`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${icons[type]} mr-3"></i>
                    <span class="flex-1">${message}</span>
                    <button onclick="closeNotification(this)" class="ml-3 text-white hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            container.appendChild(notification);
            
            // Animation d'apparition
            setTimeout(() => {
                notification.classList.remove('translate-x-full', 'opacity-0');
            }, 100);
            
            // Auto-suppression après 5 secondes
            setTimeout(() => {
                closeNotification(notification.querySelector('button'));
            }, 5000);
        }
        
        function closeNotification(button) {
            const notification = button.closest('div').parentElement;
            notification.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }
        
        // Fonction pour afficher une modale de confirmation
        function showConfirmationModal(title, message, onConfirm) {
            const modal = document.getElementById('confirmation-modal');
            const modalTitle = document.getElementById('modal-title');
            const modalMessage = document.getElementById('modal-message');
            const modalCancel = document.getElementById('modal-cancel');
            const modalConfirm = document.getElementById('modal-confirm');
            
            modalTitle.textContent = title;
            modalMessage.textContent = message;
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            modalCancel.onclick = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            };
            
            modalConfirm.onclick = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                onConfirm();
            };
        }
        
        // Fonction pour afficher une modale d'avertissement
        function showWarningModal(title, message) {
            const modal = document.getElementById('warning-modal');
            const modalTitle = document.getElementById('warning-modal-title');
            const modalMessage = document.getElementById('warning-modal-message');
            const modalOk = document.getElementById('warning-modal-ok');
            
            modalTitle.textContent = title;
            modalMessage.textContent = message;
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            modalOk.onclick = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            };
        }
    </script>
    
    @stack('scripts')

    <!-- Système de notifications professionnelles -->
    <div id="notification-container" class="fixed top-4 right-4 z-50 space-y-3"></div>

    <!-- Modale de notification simple -->
    <div id="notificationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
            <div class="mt-3 text-center">
                <div id="modal-icon-container" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                    <i id="modal-icon" class="fas fa-info-circle text-blue-600"></i>
                </div>
                <h3 id="modal-title" class="text-lg font-medium text-gray-900 mb-2">Information</h3>
                <div id="modal-content" class="text-sm text-gray-500 mb-6">Message</div>
                
                <div class="flex justify-center space-x-3">
                    <button id="modal-cancel" onclick="closeNotificationModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 text-sm rounded-md hover:bg-gray-400 hidden">
                        Annuler
                    </button>
                    <button id="modal-confirm" onclick="closeNotificationModal()" 
                            class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Système de notifications professionnel
        const NotificationSystem = {
            // Configuration des types
            types: {
                success: {
                    bgColor: 'bg-green-100',
                    textColor: 'text-green-800',
                    borderColor: 'border-green-200',
                    icon: 'fas fa-check-circle',
                    iconColor: 'text-green-600'
                },
                error: {
                    bgColor: 'bg-red-100',
                    textColor: 'text-red-800',
                    borderColor: 'border-red-200',
                    icon: 'fas fa-exclamation-circle',
                    iconColor: 'text-red-600'
                },
                warning: {
                    bgColor: 'bg-yellow-100',
                    textColor: 'text-yellow-800',
                    borderColor: 'border-yellow-200',
                    icon: 'fas fa-exclamation-triangle',
                    iconColor: 'text-yellow-600'
                },
                info: {
                    bgColor: 'bg-blue-100',
                    textColor: 'text-blue-800',
                    borderColor: 'border-blue-200',
                    icon: 'fas fa-info-circle',
                    iconColor: 'text-blue-600'
                }
            },

            // Afficher une notification toast
            show(message, type = 'info', duration = 5000) {
                console.log('NotificationSystem.show called:', message, type);
                const container = document.getElementById('notification-container');
                if (!container) {
                    console.error('notification-container not found');
                    return;
                }
                console.log('Container found:', container);
                
                const config = this.types[type];
                if (!config) {
                    console.error('Unknown notification type:', type);
                    return;
                }
                
                const id = 'notification-' + Date.now();
                console.log('Creating notification with ID:', id);

                const notification = document.createElement('div');
                notification.id = id;
                notification.className = `notification-toast max-w-sm w-full ${config.bgColor} border ${config.borderColor} rounded-lg shadow-lg`;
                
                notification.innerHTML = `
                    <div class="flex p-4">
                        <div class="flex-shrink-0">
                            <i class="${config.icon} ${config.iconColor}"></i>
                        </div>
                        <div class="ml-3 w-0 flex-1">
                            <p class="text-sm font-medium ${config.textColor}">${message}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button onclick="NotificationSystem.hide('${id}')" class="inline-flex ${config.textColor} hover:opacity-75">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    </div>
                `;

                container.appendChild(notification);
                console.log('Notification appended to container');

                // Animation d'entrée avec classe CSS
                setTimeout(() => {
                    notification.classList.add('show');
                    console.log('Show class added');
                }, 50);

                // Auto-suppression
                if (duration > 0) {
                    setTimeout(() => {
                        this.hide(id);
                    }, duration);
                }

                return id;
            },
            },

            // Masquer une notification
            hide(id) {
                const notification = document.getElementById(id);
                if (notification) {
                    notification.classList.remove('show');
                    setTimeout(() => {
                        notification.remove();
                    }, 300);
                }
            },

            // Afficher une modale de notification détaillée
            showModal(title, content, type = 'info', options = {}) {
                console.log('NotificationSystem.showModal called:', title, type);
                const modal = document.getElementById('notification-modal');
                if (!modal) {
                    console.error('notification-modal not found');
                    return;
                }
                console.log('Modal found:', modal);
                
                const titleEl = document.getElementById('notification-title');
                const contentEl = document.getElementById('notification-content');
                const iconEl = document.getElementById('notification-icon');
                const iconClassEl = document.getElementById('notification-icon-class');
                const primaryBtn = document.getElementById('notification-primary-btn');
                const secondaryBtn = document.getElementById('notification-secondary-btn');

                if (!titleEl || !contentEl || !iconEl || !iconClassEl || !primaryBtn || !secondaryBtn) {
                    console.error('Some modal elements not found');
                    return;
                }

                const config = this.types[type];

                // Configuration de l'icône
                iconEl.className = `mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10 ${config.bgColor}`;
                iconClassEl.className = `text-xl ${config.icon} ${config.iconColor}`;

                // Contenu
                titleEl.textContent = title;
                contentEl.innerHTML = content;

                // Style du bouton primaire selon le type
                let primaryBtnClass = 'w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm ';
                
                switch(type) {
                    case 'success':
                        primaryBtnClass += 'bg-green-600 hover:bg-green-700 focus:ring-green-500';
                        break;
                    case 'error':
                        primaryBtnClass += 'bg-red-600 hover:bg-red-700 focus:ring-red-500';
                        break;
                    case 'warning':
                        primaryBtnClass += 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500';
                        break;
                    default:
                        primaryBtnClass += 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500';
                }
                
                primaryBtn.className = primaryBtnClass;

                // Boutons
                if (options.primaryText) {
                    primaryBtn.textContent = options.primaryText;
                } else {
                    primaryBtn.textContent = 'OK';
                }
                
                if (options.secondaryText) {
                    secondaryBtn.textContent = options.secondaryText;
                    secondaryBtn.classList.remove('hidden');
                } else {
                    secondaryBtn.classList.add('hidden');
                }

                // Actions
                primaryBtn.onclick = () => {
                    if (options.onPrimary) options.onPrimary();
                    this.closeModal();
                };
                
                if (options.onSecondary) {
                    secondaryBtn.onclick = () => {
                        options.onSecondary();
                        this.closeModal();
                    };
                } else {
                    secondaryBtn.onclick = () => {
                        this.closeModal();
                    };
                }

                // Afficher la modale
                modal.classList.remove('hidden');
                console.log('Modal shown');
                
                // Focus sur le premier bouton pour l'accessibilité
                setTimeout(() => {
                    primaryBtn.focus();
                }, 100);
            },

            // Fermer la modale
            closeModal() {
                const modal = document.getElementById('notification-modal');
                modal.classList.add('hidden');
            }
        };

        // Fonctions globales pour compatibilité
        function showNotification(message, type = 'info', duration = 5000) {
            console.log('showNotification called:', message, type);
            if (typeof NotificationSystem === 'undefined') {
                console.error('NotificationSystem not found');
                return;
            }
            return NotificationSystem.show(message, type, duration);
        }

        function showNotificationModal(title, content, type = 'info', options = {}) {
            console.log('showNotificationModal called:', title, type);
            if (typeof NotificationSystem === 'undefined') {
                console.error('NotificationSystem not found');
                return;
            }
            return NotificationSystem.showModal(title, content, type, options);
        }

        function closeNotificationModal() {
            console.log('closeNotificationModal called');
            if (typeof NotificationSystem === 'undefined') {
                console.error('NotificationSystem not found');
                return;
            }
            NotificationSystem.closeModal();
        }

        // Gestion de la touche Escape pour fermer la modale
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('notification-modal');
                if (!modal.classList.contains('hidden')) {
                    closeNotificationModal();
                }
            }
        });

        // Gestion du clic sur l'overlay pour fermer la modale
        document.getElementById('notification-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeNotificationModal();
            }
        });

        // Remplacer les alert() par des notifications
    </script>
</body>
</html>