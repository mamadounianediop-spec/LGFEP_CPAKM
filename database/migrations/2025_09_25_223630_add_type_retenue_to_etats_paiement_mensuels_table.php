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
        Schema::table('etats_paiement_mensuels', function (Blueprint $table) {
            $table->string('type_retenue')->nullable()->after('retenues')->comment('Type de retenue: pourcentage ou custom');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('etats_paiement_mensuels', function (Blueprint $table) {
            $table->dropColumn('type_retenue');
        });
    }
};
