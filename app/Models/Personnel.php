<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Personnel extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'telephone',
        'adresse',
        'cni',
        'type_personnel',
        'discipline',
        'statut',
        'date_embauche',
        'date_fin',
        'mode_paiement',
        'montant_fixe',
        'tarif_heure',

    ];

    protected $casts = [
        'date_embauche' => 'date',
        'date_fin' => 'date',
    ];

    // Constantes pour les types de personnel
    const TYPE_DIRECTEUR = 'directeur';
    const TYPE_SURVEILLANT = 'surveillant';
    const TYPE_SECRETAIRE = 'secretaire';
    const TYPE_ENSEIGNANT = 'enseignant';
    const TYPE_GARDIEN = 'gardien';

    const TYPES = [
        self::TYPE_DIRECTEUR => 'Directeur',
        self::TYPE_SURVEILLANT => 'Surveillant',
        self::TYPE_SECRETAIRE => 'Secrétaire',
        self::TYPE_ENSEIGNANT => 'Enseignant',
        self::TYPE_GARDIEN => 'Gardien',
    ];

    // Constantes pour les statuts
    const STATUT_ACTIF = 'actif';
    const STATUT_INACTIF = 'inactif';

    const STATUTS = [
        self::STATUT_ACTIF => 'Actif',
        self::STATUT_INACTIF => 'Inactif',
    ];

    // Constantes pour les modes de paiement
    const MODE_FIXE = 'fixe';
    const MODE_HEURE = 'heure';

    const MODES_PAIEMENT = [
        self::MODE_FIXE => 'Salaire fixe (mensuel)',
        self::MODE_HEURE => 'Paiement à l\'heure',
    ];



    /**
     * Accesseur pour le nom complet
     */
    public function getNomCompletAttribute()
    {
        return $this->nom . ' ' . $this->prenom;
    }

    /**
     * Accesseur pour le type formaté
     */
    public function getTypeFormateAttribute()
    {
        return self::TYPES[$this->type_personnel] ?? $this->type_personnel;
    }

    /**
     * Accesseur pour le statut formaté
     */
    public function getStatutFormateAttribute()
    {
        return self::STATUTS[$this->statut] ?? $this->statut;
    }



    /**
     * Scope pour filtrer par type de personnel
     */
    public function scopeParType($query, $type)
    {
        return $query->where('type_personnel', $type);
    }

    /**
     * Scope pour les personnels actifs
     */
    public function scopeActif($query)
    {
        return $query->where('statut', self::STATUT_ACTIF);
    }

    /**
     * Validation rules
     */
    public static function rules($id = null)
    {
        return [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string',
            'cni' => 'nullable|string|max:20',
            'type_personnel' => 'required|in:directeur,surveillant,secretaire,enseignant,gardien',
            'statut' => 'required|in:actif,suspendu,conge',
            'date_embauche' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_embauche',
            'discipline' => 'nullable|string|max:255',

        ];
    }


}
