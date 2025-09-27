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
        Schema::create('pre_inscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('ine')->nullable()->unique();
            $table->string('nom');
            $table->string('prenom');
            $table->date('date_naissance')->nullable();
            $table->string('lieu_naissance')->nullable();
            $table->text('adresse')->nullable();
            $table->string('contact')->nullable();
            $table->string('tuteur')->nullable();
            $table->string('etablissement_origine')->nullable();
            $table->enum('statut', ['en_attente', 'inscrit', 'rejete'])->default('en_attente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pre_inscriptions');
    }
};
