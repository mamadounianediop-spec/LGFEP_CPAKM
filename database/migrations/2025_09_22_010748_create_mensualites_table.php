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
        Schema::create('mensualites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscription_id')->constrained('inscriptions')->onDelete('cascade');
            $table->enum('mois_paiement', [
                'octobre', 'novembre', 'decembre', 'janvier', 
                'fevrier', 'mars', 'avril', 'mai', 'juin', 'juillet'
            ]);
            $table->decimal('montant_du', 10, 2);
            $table->decimal('montant_paye', 10, 2)->default(0);
            $table->enum('mode_paiement', [
                'especes', 'virement', 'cheque', 'orange_money', 'wave', 'free_money'
            ])->nullable();
            $table->string('numero_recu')->unique();
            $table->date('date_paiement')->nullable();
            $table->enum('statut', ['complet', 'partiel', 'impaye'])->default('impaye');
            $table->text('observations')->nullable();
            $table->foreignId('annee_scolaire_id')->constrained('annee_scolaires')->onDelete('cascade');
            $table->timestamps();
            
            // Index pour performance
            $table->index(['inscription_id', 'mois_paiement', 'annee_scolaire_id']);
            $table->index(['statut', 'mois_paiement']);
            $table->index('date_paiement');
            
            // Contrainte unique pour éviter doublons
            $table->unique(['inscription_id', 'mois_paiement', 'annee_scolaire_id'], 'mensualites_unique_constraint');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mensualites');
    }
};
