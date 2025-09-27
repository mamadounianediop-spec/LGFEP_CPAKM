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
        Schema::table('inscriptions', function (Blueprint $table) {
            $table->text('remarques')->nullable()->after('statut');
            $table->dropColumn('statut');
        });
        
        // Recréer la colonne statut avec les nouvelles valeurs
        Schema::table('inscriptions', function (Blueprint $table) {
            $table->enum('statut', ['actif', 'suspendu', 'abandonne'])->default('actif')->after('date_inscription');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            $table->dropColumn(['remarques', 'statut']);
        });
        
        // Restaurer l'ancienne colonne statut
        Schema::table('inscriptions', function (Blueprint $table) {
            $table->enum('statut', ['actif', 'suspendu', 'transfere'])->default('actif')->after('date_inscription');
        });
    }
};
