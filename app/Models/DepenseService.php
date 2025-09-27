<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepenseService extends Model
{
    protected $table = 'depenses_services';
    
    protected $fillable = [
        'service_id',
        'montant',
        'date_depense',
        'type_depense',
        'numero_facture',
        'description',
        'remarques',
        'etablissement_id',
        'annee_scolaire_id'
    ];

    protected $casts = [
        'date_depense' => 'date',
        'montant' => 'decimal:2',
    ];

    /**
     * Relation avec le service
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
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
     * Accessor pour le type de dépense formaté
     */
    public function getTypeDepenseFormatteAttribute()
    {
        return match($this->type_depense) {
            'achat' => 'Achat',
            'maintenance' => 'Maintenance',
            'location' => 'Location',
            'reparation' => 'Réparation',
            'consommation' => 'Consommation',
            'autre' => 'Autre',
            default => $this->type_depense
        };
    }

    /**
     * Accessor pour le montant formaté
     */
    public function getMontantFormatteAttribute()
    {
        return number_format($this->montant, 0, ',', ' ') . ' FCFA';
    }
}
