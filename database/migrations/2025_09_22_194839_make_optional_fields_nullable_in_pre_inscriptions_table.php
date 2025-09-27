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
        Schema::table('pre_inscriptions', function (Blueprint $table) {
            // Rendre nullable tous les champs non-essentiels selon nos nouvelles règles de validation
            $table->date('date_naissance')->nullable()->change();
            $table->string('lieu_naissance')->nullable()->change();
            $table->text('adresse')->nullable()->change();
            $table->string('contact')->nullable()->change();
            $table->string('tuteur')->nullable()->change();
            $table->string('etablissement_origine')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pre_inscriptions', function (Blueprint $table) {
            // Remettre les contraintes NOT NULL si besoin de rollback
            $table->date('date_naissance')->nullable(false)->change();
            $table->string('lieu_naissance')->nullable(false)->change();
            $table->text('adresse')->nullable(false)->change();
            $table->string('contact')->nullable(false)->change();
            $table->string('tuteur')->nullable(false)->change();
            $table->string('etablissement_origine')->nullable(false)->change();
        });
    }
};
