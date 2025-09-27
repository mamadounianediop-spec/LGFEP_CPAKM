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
        Schema::create('frais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('niveau_id')->nullable()->constrained('niveaux')->onDelete('cascade');
            $table->foreignId('classe_id')->nullable()->constrained('classes')->onDelete('cascade');
            $table->string('type'); // inscription, mensualite, autre
            $table->string('nom'); // Frais d'inscription CI, Mensualité CP, etc.
            $table->decimal('montant', 10, 2);
            $table->text('description')->nullable();
            $table->boolean('obligatoire')->default(true);
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frais');
    }
};
