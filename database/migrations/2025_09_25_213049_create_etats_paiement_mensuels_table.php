<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('etats_paiement_mensuels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personnel_id')->constrained('personnels')->cascadeOnDelete();
            
            // Période
            $table->year('annee');
            $table->tinyInteger('mois'); // 1-12
            
            // Données de paie
            $table->decimal('heures_effectuees', 8, 2)->nullable(); // Pour mode heure
            $table->decimal('primes', 10, 0)->default(0); // Primes en FCFA
            $table->decimal('retenues', 10, 0)->default(0); // Retenues en FCFA
            $table->decimal('montant_total', 10, 0)->default(0); // Calculé automatiquement
            $table->decimal('avances', 10, 0)->default(0); // Avances versées
            $table->decimal('restant', 10, 0)->default(0); // Restant à payer
            
            // Statut et états
            $table->enum('statut_paiement', ['en_attente', 'paye'])->default('en_attente');
            $table->boolean('visible')->default(true); // Pour masquer/afficher
            $table->boolean('archive')->default(false); // Pour archivage
            
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['personnel_id', 'annee', 'mois']);
            $table->index(['annee', 'mois']);
            $table->index(['archive']);
            $table->index(['visible']);
            
            // Contrainte d'unicité : un seul état par personnel par mois
            $table->unique(['personnel_id', 'annee', 'mois']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etats_paiement_mensuels');
    }
};
