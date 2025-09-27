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
        Schema::create('inscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pre_inscription_id')->constrained()->onDelete('cascade');
            $table->foreignId('niveau_id')->constrained()->onDelete('cascade');
            $table->foreignId('classe_id')->constrained()->onDelete('cascade');
            $table->decimal('montant_total', 10, 2);
            $table->decimal('montant_paye', 10, 2)->default(0);
            $table->enum('mode_paiement', ['orange_money', 'wave', 'free_money', 'billetage'])->nullable();
            $table->enum('statut_paiement', ['partiel', 'complet'])->default('partiel');
            $table->string('numero_recu')->unique();
            $table->date('date_inscription');
            $table->enum('statut', ['actif', 'suspendu', 'transfere'])->default('actif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscriptions');
    }
};
