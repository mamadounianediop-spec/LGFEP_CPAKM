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
        Schema::table('personnels', function (Blueprint $table) {
            $table->dropColumn('statut');
        });
        
        Schema::table('personnels', function (Blueprint $table) {
            $table->enum('statut', ['actif', 'suspendu', 'conge'])->default('actif')->after('discipline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personnels', function (Blueprint $table) {
            $table->dropColumn('statut');
        });
        
        Schema::table('personnels', function (Blueprint $table) {
            $table->enum('statut', ['actif', 'inactif'])->default('actif')->after('discipline');
        });
    }
};
