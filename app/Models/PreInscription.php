<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PreInscription extends Model
{
    use HasFactory;

    const SEXE_MASCULIN = 'M';
    const SEXE_FEMININ = 'F';

    protected $fillable = [
        'ine',
        'nom',
        'prenom',
        'sexe',
        'date_naissance',
        'lieu_naissance',
        'adresse',
        'contact',
        'tuteur',
        'etablissement_origine',
        'statut',
        'annee_scolaire_id'
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    /**
     * Obtenir les options de sexe
     */
    public static function getSexeOptions()
    {
        return [
            self::SEXE_MASCULIN => 'Masculin',
            self::SEXE_FEMININ => 'Féminin'
        ];
    }

    /**
     * Obtenir le libellé du sexe
     */
    public function getSexeLibelleAttribute()
    {
        return self::getSexeOptions()[$this->sexe] ?? '';
    }

    /**
     * Générer un INE automatique si pas fourni
     */
    public static function generateINE()
    {
        $year = date('Y');
        $lastNumber = self::where('ine', 'like', $year . '%')
                          ->whereNotNull('ine')
                          ->orderBy('ine', 'desc')
                          ->first();
        
        if ($lastNumber) {
            $lastSequence = substr($lastNumber->ine, -4);
            $newSequence = str_pad($lastSequence + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newSequence = '0001';
        }
        
        return $year . $newSequence;
    }

    /**
     * Relation avec inscription
     */
    public function inscription()
    {
        return $this->hasOne(Inscription::class);
    }

    /**
     * Relation avec année scolaire
     */
    public function anneeScolaire()
    {
        return $this->belongsTo(AnneeScolaire::class);
    }

    /**
     * Scope pour recherche
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('nom', 'like', "%{$search}%")
              ->orWhere('prenom', 'like', "%{$search}%")
              ->orWhere('ine', 'like', "%{$search}%");
        });
    }
}
