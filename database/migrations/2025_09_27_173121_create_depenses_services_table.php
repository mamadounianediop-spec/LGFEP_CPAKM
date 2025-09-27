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
        Schema::create('depenses_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->decimal('montant', 15, 2);
            $table->date('date_depense');
            $table->enum('type_depense', ['achat', 'maintenance', 'location', 'reparation', 'consommation', 'autre'])->default('achat');
            $table->string('numero_facture')->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('depenses_services');
    }
};
