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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action'); // CREATE, UPDATE, DELETE, LOGIN, LOGOUT, DOWNLOAD, VIEW, etc.
            $table->string('model')->nullable(); // Inscription, Personnel, Mensualite, etc.
            $table->unsignedBigInteger('model_id')->nullable(); // ID de l'enregistrement concerné
            $table->string('description'); // Description lisible de l'action
            $table->json('details')->nullable(); // Détails supplémentaires (anciennes/nouvelles valeurs)
            $table->string('ip_address', 45); // Support IPv4 et IPv6
            $table->text('user_agent')->nullable(); // Navigateur et OS
            $table->string('method', 10)->nullable(); // GET, POST, PUT, DELETE
            $table->text('url')->nullable(); // URL de l'action
            $table->string('referer')->nullable(); // Page précédente
            $table->enum('level', ['info', 'warning', 'error'])->default('info'); // Niveau d'importance
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index(['model', 'model_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
