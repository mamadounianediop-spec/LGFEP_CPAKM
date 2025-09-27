<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategorieService extends Model
{
    protected $table = 'categories_services';
    protected $fillable = [
        'nom',
        'description',
        'icone',
        'couleur',
        'actif'
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    /**
     * Relation avec les services
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Scope pour les catégories actives
     */
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }
}
