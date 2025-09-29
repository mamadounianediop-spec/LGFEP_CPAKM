@extends('layouts.app')

@section('title', 'Accès aux Logs d\'Activité')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- En-tête -->
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-shield-alt text-purple-600 text-2xl"></i>
            </div>
            <h2 class="mt-6 text-3xl font-bold text-gray-900">Accès Sécurisé</h2>
            <p class="mt-2 text-sm text-gray-600">
                Saisissez le mot de passe pour accéder aux logs d'activité
            </p>
        </div>

        <!-- Formulaire -->
        <div class="bg-white shadow-sm rounded-lg border">
            <form id="passwordForm" method="POST" action="{{ route('activity.authenticate-password') }}" class="px-6 py-8 space-y-6">
                @csrf
                
                <!-- Info utilisateur -->
                <div class="text-center text-sm text-gray-500">
                    Connecté en tant que <span class="font-medium text-gray-700">{{ auth()->user()->name }}</span>
                </div>

                <!-- Champ mot de passe -->
                <div>
                    <label for="activity_password" class="sr-only">Mot de passe</label>
                    <div class="relative">
                        <input type="password" 
                               name="password" 
                               id="activity_password"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-md placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('password') border-red-500 @enderror"
                               placeholder="Mot de passe d'accès"
                               required
                               autocomplete="off">
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Boutons -->
                <div class="space-y-4">
                    <button type="submit" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                        <i class="fas fa-unlock-alt mr-2"></i>
                        Accéder aux Logs
                    </button>

                    <a href="{{ route('dashboard') }}" 
                       class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Retour
                    </a>
                </div>
            </form>
        </div>

        <!-- Note de sécurité -->
        <div class="text-center">
            <p class="text-xs text-gray-500">
                <i class="fas fa-info-circle mr-1"></i>
                Accès valide 30 minutes • Tous les accès sont enregistrés
            </p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Focus automatique sur le champ mot de passe
    document.getElementById('activity_password').focus();
    
    // Gérer la soumission du formulaire
    const form = document.getElementById('passwordForm');
    form.addEventListener('submit', function() {
        const button = form.querySelector('button[type="submit"]');
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Vérification...';
        button.disabled = true;
    });
});
</script>
@endsection