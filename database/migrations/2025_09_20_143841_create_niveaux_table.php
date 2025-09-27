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
        Schema::create('niveaux', function (Blueprint $table) {
            $table->id();
            $table->string('nom'); // CI, CP, CE1, CE2, CM1, CM2, 6ème, 5ème, 4ème, 3ème, 2nde, 1ère, Terminale
            $table->string('code', 10)->unique(); // CI, CP, CE1, etc.
            $table->text('description')->nullable();
            $table->integer('ordre')->default(0); // Pour l'ordre d'affichage
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('niveaux');
    }
};
