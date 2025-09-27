<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Niveau extends Model
{
    protected $fillable = [
        'nom',
        'code',
        'description',
        'ordre',
        'actif'
    ];

    protected $casts = [
        'actif' => 'boolean',
        'ordre' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the classes for the niveau.
     */
    public function classes(): HasMany
    {
        return $this->hasMany(Classe::class);
    }

    /**
     * Get the frais for the niveau.
     */
    public function frais(): HasMany
    {
        return $this->hasMany(Frais::class);
    }

    /**
     * Get the inscriptions for the niveau.
     */
    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class);
    }

    /**
     * Get the pre-inscriptions through classes.
     */
    public function preInscriptions()
    {
        return PreInscription::whereHas('inscription', function($query) {
            $query->where('niveau_id', $this->id);
        });
    }

    /**
     * Scope a query to only include active niveaux.
     */
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Scope a query to order by ordre.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('ordre');
    }

    /**
     * Validation rules
     */
    public static function rules($id = null)
    {
        return [
            'nom' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:niveaux,code,' . $id,
            'description' => 'nullable|string',
            'ordre' => 'integer|min:0',
            'actif' => 'boolean'
        ];
    }
}
