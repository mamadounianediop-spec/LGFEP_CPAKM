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
        // Ajouter année scolaire aux pré-inscriptions
        Schema::table('pre_inscriptions', function (Blueprint $table) {
            $table->foreignId('annee_scolaire_id')->nullable()->constrained('annee_scolaires')->onDelete('cascade');
        });

        // Ajouter année scolaire aux inscriptions  
        Schema::table('inscriptions', function (Blueprint $table) {
            $table->foreignId('annee_scolaire_id')->nullable()->constrained('annee_scolaires')->onDelete('cascade');
        });

        // Ajouter année scolaire aux frais
        Schema::table('frais', function (Blueprint $table) {
            $table->foreignId('annee_scolaire_id')->nullable()->constrained('annee_scolaires')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pre_inscriptions', function (Blueprint $table) {
            $table->dropForeign(['annee_scolaire_id']);
            $table->dropColumn('annee_scolaire_id');
        });

        Schema::table('inscriptions', function (Blueprint $table) {
            $table->dropForeign(['annee_scolaire_id']);
            $table->dropColumn('annee_scolaire_id');
        });

        Schema::table('frais', function (Blueprint $table) {
            $table->dropForeign(['annee_scolaire_id']);
            $table->dropColumn('annee_scolaire_id');
        });
    }
};
