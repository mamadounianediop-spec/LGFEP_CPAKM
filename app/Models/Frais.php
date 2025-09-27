<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Frais extends Model
{
    protected $fillable = [
        'niveau_id',
        'classe_id',
        'type',
        'nom',
        'montant',
        'description',
        'obligatoire',
        'actif',
        'annee_scolaire_id'
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'obligatoire' => 'boolean',
        'actif' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the niveau that owns the frais.
     */
    public function niveau(): BelongsTo
    {
        return $this->belongsTo(Niveau::class);
    }

    /**
     * Get the classe that owns the frais.
     */
    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    /**
     * Relation avec année scolaire
     */
    public function anneeScolaire()
    {
        return $this->belongsTo(AnneeScolaire::class);
    }

    /**
     * Scope a query to only include active frais.
     */
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Scope a query to filter by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get the formatted montant.
     */
    public function getFormattedMontantAttribute()
    {
        return number_format($this->montant, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Validation rules
     */
    public static function rules($id = null)
    {
        return [
            'niveau_id' => 'nullable|exists:niveaux,id',
            'classe_id' => 'nullable|exists:classes,id',
            'type' => 'required|in:inscription,mensualite,autre',
            'nom' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'obligatoire' => 'boolean',
            'actif' => 'boolean'
        ];
    }

    /**
     * Get type options for forms
     */
    public static function getTypeOptions()
    {
        return [
            'inscription' => 'Frais d\'inscription',
            'mensualite' => 'Mensualité',
            'autre' => 'Autre'
        ];
    }
}
