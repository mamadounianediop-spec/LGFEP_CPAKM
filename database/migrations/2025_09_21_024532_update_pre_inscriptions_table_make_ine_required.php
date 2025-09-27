<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // D'abord, générer des INE et remplir les champs NULL pour les enregistrements existants
        DB::statement("
            UPDATE pre_inscriptions 
            SET ine = CONCAT('INE', YEAR(NOW()), LPAD(id, 3, '0')) 
            WHERE ine IS NULL OR ine = ''
        ");
        
        DB::statement("
            UPDATE pre_inscriptions 
            SET date_naissance = '2000-01-01' 
            WHERE date_naissance IS NULL
        ");
        
        DB::statement("
            UPDATE pre_inscriptions 
            SET lieu_naissance = 'Non renseigné' 
            WHERE lieu_naissance IS NULL OR lieu_naissance = ''
        ");
        
        DB::statement("
            UPDATE pre_inscriptions 
            SET adresse = 'Adresse non renseignée' 
            WHERE adresse IS NULL OR adresse = ''
        ");
        
        DB::statement("
            UPDATE pre_inscriptions 
            SET contact = '00 000 00 00' 
            WHERE contact IS NULL OR contact = ''
        ");
        
        DB::statement("
            UPDATE pre_inscriptions 
            SET tuteur = 'Tuteur non renseigné' 
            WHERE tuteur IS NULL OR tuteur = ''
        ");
        
        DB::statement("
            UPDATE pre_inscriptions 
            SET etablissement_origine = 'Établissement non renseigné' 
            WHERE etablissement_origine IS NULL OR etablissement_origine = ''
        ");
        
        Schema::table('pre_inscriptions', function (Blueprint $table) {
            // Rendre les champs obligatoires
            $table->string('ine')->nullable(false)->change();
            $table->date('date_naissance')->nullable(false)->change();
            $table->string('lieu_naissance')->nullable(false)->change();
            $table->text('adresse')->nullable(false)->change();
            $table->string('contact')->nullable(false)->change();
            $table->string('tuteur')->nullable(false)->change();
            $table->string('etablissement_origine')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pre_inscriptions', function (Blueprint $table) {
            // Remettre les champs comme nullable
            $table->string('ine')->nullable()->change();
            $table->date('date_naissance')->nullable()->change();
            $table->string('lieu_naissance')->nullable()->change();
            $table->text('adresse')->nullable()->change();
            $table->string('contact')->nullable()->change();
            $table->string('tuteur')->nullable()->change();
            $table->string('etablissement_origine')->nullable()->change();
        });
    }
};
