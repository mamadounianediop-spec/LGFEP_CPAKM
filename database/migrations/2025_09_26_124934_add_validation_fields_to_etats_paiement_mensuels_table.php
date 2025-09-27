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
            $table->timestamp('date_validation')->nullable()->after('statut_paiement');
            $table->unsignedBigInteger('validateur_id')->nullable()->after('date_validation');
            $table->text('commentaire_validation')->nullable()->after('validateur_id');
            
            $table->foreign('validateur_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('etats_paiement_mensuels', function (Blueprint $table) {
            $table->dropForeign(['validateur_id']);
            $table->dropColumn(['date_validation', 'validateur_id', 'commentaire_validation']);
        });
    }
};
