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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('niveau_id')->constrained('niveaux')->onDelete('cascade');
            $table->string('nom'); // Classe A, Classe B, etc.
            $table->string('code', 20)->unique(); // CI-A, CP-B, CE1-A, etc.
            $table->integer('effectif_max')->default(30);
            $table->text('description')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
