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
            $table->timestamp('date_archive')->nullable()->after('archive')->comment('Date d\'archivage de l\'état');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('etats_paiement_mensuels', function (Blueprint $table) {
            $table->dropColumn('date_archive');
        });
    }
};
