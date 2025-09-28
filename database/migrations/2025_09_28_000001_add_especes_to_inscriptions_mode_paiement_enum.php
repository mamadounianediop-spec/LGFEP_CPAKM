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
        // Modifier l'ENUM pour ajouter 'especes'
        DB::statement("ALTER TABLE inscriptions MODIFY COLUMN mode_paiement ENUM('orange_money', 'wave', 'free_money', 'billetage', 'especes') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer 'especes' de l'ENUM
        DB::statement("ALTER TABLE inscriptions MODIFY COLUMN mode_paiement ENUM('orange_money', 'wave', 'free_money', 'billetage') NULL");
    }
};