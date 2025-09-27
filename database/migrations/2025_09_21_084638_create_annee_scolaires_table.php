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
        Schema::create('annee_scolaires', function (Blueprint $table) {
            $table->id();
            $table->string('libelle'); // Ex: 2024-2025
            $table->date('date_debut'); // Début de l'année scolaire
            $table->date('date_fin'); // Fin de l'année scolaire
            $table->boolean('actif')->default(false); // Une seule année peut être active
            $table->text('description')->nullable(); // Description optionnelle
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annee_scolaires');
    }
};
