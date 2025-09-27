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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->foreignId('categorie_service_id')->constrained('categories_services')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->string('fournisseur')->nullable();
            $table->date('date_acquisition')->nullable();
            $table->enum('statut', ['actif', 'inactif', 'en_maintenance'])->default('actif');
            $table->text('remarques')->nullable();
            $table->foreignId('etablissement_id')->constrained()->onDelete('cascade');
            $table->foreignId('annee_scolaire_id')->constrained('annee_scolaires')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
