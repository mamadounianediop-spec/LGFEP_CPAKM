<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modifier la colonne enum pour inclure juillet et les mois corrects
        DB::statement("ALTER TABLE mensualites MODIFY COLUMN mois_paiement ENUM('octobre', 'novembre', 'decembre', 'janvier', 'fevrier', 'mars', 'avril', 'mai', 'juin', 'juillet') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restaurer l'ancien enum sans juillet
        DB::statement("ALTER TABLE mensualites MODIFY COLUMN mois_paiement ENUM('octobre', 'novembre', 'decembre', 'janvier', 'fevrier', 'mars', 'avril', 'mai', 'juin') NOT NULL");
    }
};
