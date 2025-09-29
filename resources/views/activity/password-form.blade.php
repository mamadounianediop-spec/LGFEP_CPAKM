@extends('layouts.app')

@section('title', 'Accès aux Logs d\'Activité')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Card de connexion -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <!-- En-tête -->
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-shield-alt text-white text-2xl"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-medium text-white">Accès Sécurisé</h3>
                        <p class="text-sm text-purple-200">Logs d'Activité du Système</p>
                    </div>
                </div>
            </div>

            <!-- Formulaire -->
            <form id="passwordForm" method="POST" action="{{ route('activity.authenticate-password') }}" class="px-6 py-6">
                @csrf
                
                <!-- Message d'information -->
                <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Accès Protégé</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>Cette section nécessite un mot de passe spécial pour accéder aux logs d'activité du système.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contexte utilisateur -->
                <div class="mb-6 p-3 bg-gray-50 rounded-md">
                    <div class="flex items-center">
                        <i class="fas fa-user text-gray-500 mr-3"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-700">Utilisateur connecté</p>
                            <p class="text-xs text-gray-500">{{ auth()->user()->name }} ({{ auth()->user()->email }})</p>
                        </div>
                    </div>
                </div>

                <!-- Champ mot de passe -->
                <div class="mb-6">
                    <label for="activity_password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-key mr-2 text-purple-500"></i>
                        Mot de passe d'accès aux logs
                    </label>
                    <div class="relative">
                        <input type="password" 
                               name="password" 
                               id="activity_password"
                               class="block w-full px-4 py-3 pl-10 pr-10 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('password') border-red-500 @enderror"
                               placeholder="Saisissez le mot de passe d'accès"
                               required
                               autocomplete="off">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button type="button" id="togglePassword" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    @error('password')
                        <p class="mt-2 text-xs text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Boutons -->
                <div class="space-y-3">
                    <button type="submit" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                        <i class="fas fa-unlock-alt mr-2"></i>
                        Accéder aux Logs
                    </button>

                    <!-- Bouton retour -->
                    <a href="{{ route('dashboard') }}" 
                       class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Retour au Dashboard
                    </a>
                </div>
            </form>

            <!-- Pied de page -->
            <div class="bg-gray-50 px-6 py-4 text-center">
                <p class="text-xs text-gray-500">
                    <i class="fas fa-clock mr-1"></i>
                    L'accès sera valide pendant 30 minutes après authentification
                </p>
            </div>
        </div>

        <!-- Informations de sécurité -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-shield-check text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Sécurité des données</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Tous les accès aux logs sont enregistrés</li>
                            <li>L'accès expire automatiquement après 30 minutes</li>
                            <li>Le mot de passe est indépendant de votre compte utilisateur</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Focus automatique sur le champ mot de passe
    document.getElementById('activity_password').focus();
    
    // Toggle pour afficher/masquer le mot de passe
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('activity_password');
    
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        const icon = this.querySelector('i');
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    });
    
    // Gérer la soumission du formulaire
    const form = document.getElementById('passwordForm');
    form.addEventListener('submit', function(e) {
        const button = form.querySelector('button[type="submit"]');
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Vérification...';
        button.disabled = true;
    });
});
</script>
@endsection