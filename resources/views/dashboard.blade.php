@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-3 mb-2">
                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tachometer-alt text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                    <p class="text-sm text-gray-600">
                        Aperçu des activités de l'établissement CPAKM
                    </p>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Élèves -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user-graduate text-white"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Total Élèves
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    245
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recettes du Mois -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-money-bill-wave text-white"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Recettes du Mois
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    2,450,000 FCFA
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Impayés -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-white"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Impayés
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    156,000 FCFA
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personnel -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-white"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Personnel
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    18
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Rapides -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Actions Principales -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Actions Rapides</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <a href="#" class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                            <i class="fas fa-user-plus text-blue-600 text-2xl mb-2"></i>
                            <span class="text-sm font-medium text-blue-900">Nouvelle Inscription</span>
                        </a>
                        <a href="#" class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                            <i class="fas fa-money-check text-green-600 text-2xl mb-2"></i>
                            <span class="text-sm font-medium text-green-900">Enregistrer Paiement</span>
                        </a>
                        <a href="#" class="flex flex-col items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                            <i class="fas fa-file-invoice text-purple-600 text-2xl mb-2"></i>
                            <span class="text-sm font-medium text-purple-900">Générer Facture</span>
                        </a>
                        @if(auth()->user()->isAdminOrDirector())
                        <a href="{{ route('parametres.index') }}" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-cog text-gray-600 text-2xl mb-2"></i>
                            <span class="text-sm font-medium text-gray-900">Paramètres</span>
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Activités Récentes -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Activités Récentes</h3>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-green-600 text-sm"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-gray-900">Paiement reçu de Fatou Diop</p>
                                <p class="text-xs text-gray-500">Il y a 2 heures</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user-plus text-blue-600 text-sm"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-gray-900">Nouvelle inscription: Moussa Fall</p>
                                <p class="text-xs text-gray-500">Il y a 4 heures</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-exclamation text-yellow-600 text-sm"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-gray-900">Rappel: Paiement en retard</p>
                                <p class="text-xs text-gray-500">Il y a 6 heures</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphiques et Tableaux -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Évolution des Recettes -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Évolution des Recettes</h3>
                    <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                        <p class="text-gray-500">Graphique des recettes (à implémenter)</p>
                    </div>
                </div>
            </div>

            <!-- Dernières Inscriptions -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Dernières Inscriptions</h3>
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Élève
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Classe
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Aïssatou Ba
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        6ème A
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ now()->subDays(1)->format('d/m/Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Moustapha Sow
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Terminale S
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ now()->subDays(2)->format('d/m/Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Khady Ndiaye
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        4ème B
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ now()->subDays(3)->format('d/m/Y') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection