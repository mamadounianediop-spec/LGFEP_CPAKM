<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'pre_inscription_id',
        'niveau_id',
        'classe_id',
        'montant_total',
        'montant_paye',
        'mode_paiement',
        'statut_paiement',
        'numero_recu',
        'date_inscription',
        'statut',
        'remarques',
        'annee_scolaire_id'
    ];

    protected $casts = [
        'date_inscription' => 'date',
        'montant_total' => 'decimal:2',
        'montant_paye' => 'decimal:2',
    ];

    /**
     * Relation avec pré-inscription
     */
    public function preInscription()
    {
        return $this->belongsTo(PreInscription::class);
    }

    /**
     * Relation avec niveau
     */
    public function niveau()
    {
        return $this->belongsTo(Niveau::class);
    }

    /**
     * Relation avec classe
     */
    public function classe()
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
     * Relation avec mensualités
     */
    public function mensualites()
    {
        return $this->hasMany(Mensualite::class);
    }

    /**
     * Générer un numéro de reçu
     */
    public static function generateNumeroRecu()
    {
        $year = date('Y');
        $lastRecu = self::where('numero_recu', 'like', 'REC' . $year . '%')
                       ->orderBy('numero_recu', 'desc')
                       ->first();
        
        if ($lastRecu) {
            $lastNumber = substr($lastRecu->numero_recu, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return 'REC' . $year . $newNumber;
    }

    /**
     * Vérifier si le paiement est complet
     */
    public function isPaiementComplet()
    {
        return $this->montant_paye >= $this->montant_total;
    }

    /**
     * Scope pour les inscriptions de l'année courante
     */
    public function scopeCurrentYear($query)
    {
        return $query->whereYear('date_inscription', date('Y'));
    }
}
