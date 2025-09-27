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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['administrateur', 'directeur', 'secretaire', 'surveillant'])->default('secretaire');
            $table->string('telephone')->nullable();
            $table->text('adresse')->nullable();
            $table->boolean('actif')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'telephone', 'adresse', 'actif']);
        });
    }
};
