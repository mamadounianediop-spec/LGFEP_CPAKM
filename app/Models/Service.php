<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'nom',
        'categorie_service_id',
        'description',
        'prix',
        'fournisseur',
        'date_acquisition',
        'statut',
        'remarques',
        'etablissement_id',
        'annee_scolaire_id'
    ];

    protected $casts = [
        'date_acquisition' => 'date',
    ];

    /**
     * Relation avec la catégorie de service
     */
    public function categorieService(): BelongsTo
    {
        return $this->belongsTo(CategorieService::class);
    }

    /**
     * Relation avec l'établissement
     */
    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    /**
     * Relation avec l'année scolaire
     */
    public function anneeScolaire(): BelongsTo
    {
        return $this->belongsTo(AnneeScolaire::class);
    }

    /**
     * Relation avec les dépenses
     */
    public function depenses(): HasMany
    {
        return $this->hasMany(DepenseService::class);
    }

    /**
     * Scope pour les services actifs
     */
    public function scopeActif($query)
    {
        return $query->where('statut', 'actif');
    }

    /**
     * Accessor pour le statut formaté
     */
    public function getStatutFormatteAttribute()
    {
        return match($this->statut) {
            'actif' => 'Actif',
            'inactif' => 'Inactif',
            'en_maintenance' => 'En maintenance',
            default => $this->statut
        };
    }
}
