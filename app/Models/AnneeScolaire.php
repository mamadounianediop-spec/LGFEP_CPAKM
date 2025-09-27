<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AnneeScolaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'libelle',
        'date_debut',
        'date_fin',
        'actif',
        'description'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'actif' => 'boolean'
    ];

    /**
     * Obtenir l'année scolaire active
     */
    public static function getActive()
    {
        return self::where('actif', true)->first();
    }

    /**
     * Activer cette année scolaire (désactive les autres)
     */
    public function activer()
    {
        // Désactiver toutes les autres années
        self::where('id', '!=', $this->id)->update(['actif' => false]);
        
        // Activer cette année
        $this->update(['actif' => true]);
    }

    /**
     * Scope pour les années actives
     */
    public function scopeActive($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Relations avec les pré-inscriptions
     */
    public function preInscriptions()
    {
        return $this->hasMany(PreInscription::class);
    }

    /**
     * Relations avec les inscriptions
     */
    public function inscriptions()
    {
        return $this->hasMany(Inscription::class);
    }

    /**
     * Relations avec les frais
     */
    public function frais()
    {
        return $this->hasMany(Frais::class);
    }

    /**
     * Relations avec les mensualités
     */
    public function mensualites()
    {
        return $this->hasMany(Mensualite::class);
    }

    /**
     * Vérifier si l'année est en cours
     */
    public function isEnCours()
    {
        $today = now()->toDateString();
        return $today >= $this->date_debut->toDateString() && 
               $today <= $this->date_fin->toDateString();
    }

    /**
     * Format d'affichage
     */
    public function getDisplayNameAttribute()
    {
        return $this->libelle . ($this->actif ? ' (Active)' : '');
    }

    /**
     * Accesseur pour compatibilité avec nom
     */
    public function getNomAttribute()
    {
        return $this->libelle;
    }
}
