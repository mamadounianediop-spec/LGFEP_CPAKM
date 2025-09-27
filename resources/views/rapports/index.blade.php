@extends('layouts.app')

@section('title', 'Rapports')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-start mb-8">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-bar text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Rapports et Statistiques</h1>
                    <p class="text-sm text-gray-600">
                        Génération de rapports financiers et statistiques
                    </p>
                </div>
            </div>
            <button class="bg-orange-600 text-white px-4 py-2 rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 flex items-center">
                <i class="fas fa-file-pdf mr-2"></i>Générer Rapport
            </button>
        </div>

        <!-- Module en développement -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="text-center">
                <i class="fas fa-tools text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Module en développement</h3>
                <p class="text-gray-500">
                    Le module de rapports et statistiques sera développé prochainement.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection