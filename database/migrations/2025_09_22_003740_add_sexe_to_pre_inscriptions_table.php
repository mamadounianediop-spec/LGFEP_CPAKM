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
            $table->enum('sexe', ['M', 'F'])->after('prenom')->comment('M: Masculin, F: Féminin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pre_inscriptions', function (Blueprint $table) {
            $table->dropColumn('sexe');
        });
    }
};
