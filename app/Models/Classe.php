<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classe extends Model
{
    protected $fillable = [
        'niveau_id',
        'nom',
        'code',
        'effectif_max',
        'description',
        'actif'
    ];

    protected $casts = [
        'actif' => 'boolean',
        'effectif_max' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the niveau that owns the classe.
     */
    public function niveau(): BelongsTo
    {
        return $this->belongsTo(Niveau::class);
    }

    /**
     * Get the frais for the classe.
     */
    public function frais(): HasMany
    {
        return $this->hasMany(Frais::class);
    }

    /**
     * Scope a query to only include active classes.
     */
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Get the full name (niveau + classe).
     */
    public function getFullNameAttribute()
    {
        return $this->niveau->nom . ' - ' . $this->nom;
    }

    /**
     * Validation rules
     */
    public static function rules($id = null)
    {
        return [
            'niveau_id' => 'required|exists:niveaux,id',
            'nom' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:classes,code,' . $id,
            'effectif_max' => 'integer|min:1|max:100',
            'description' => 'nullable|string',
            'actif' => 'boolean'
        ];
    }
}
