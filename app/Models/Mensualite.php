<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mensualite extends Model
{
    use HasFactory;

    protected $fillable = [
        'inscription_id',
        'mois_paiement',
        'montant_du',
        'montant_paye',
        'mode_paiement',
        'numero_recu',
        'date_paiement',
        'statut',
        'observations',
        'annee_scolaire_id'
    ];

    protected $casts = [
        'date_paiement' => 'date',
        'montant_du' => 'decimal:2',
        'montant_paye' => 'decimal:2',
    ];

    // Constantes pour les statuts
    const STATUT_COMPLET = 'complet';
    const STATUT_PARTIEL = 'partiel';
    const STATUT_IMPAYE = 'impaye';

    // Constantes pour les mois (année scolaire octobre à juillet)
    const MOIS = [
        'octobre' => 'Octobre',
        'novembre' => 'Novembre',
        'decembre' => 'Décembre',
        'janvier' => 'Janvier',
        'fevrier' => 'Février',
        'mars' => 'Mars',
        'avril' => 'Avril',
        'mai' => 'Mai',
        'juin' => 'Juin',
        'juillet' => 'Juillet'
    ];

    // Constantes pour les modes de paiement
    const MODES_PAIEMENT = [
        'especes' => 'Espèces',
        'virement' => 'Virement bancaire',
        'cheque' => 'Chèque',
        'orange_money' => 'Orange Money',
        'wave' => 'Wave',
        'free_money' => 'Free Money'
    ];

    /**
     * Relations
     */
    public function inscription()
    {
        return $this->belongsTo(Inscription::class);
    }

    public function anneeScolaire()
    {
        return $this->belongsTo(AnneeScolaire::class, 'annee_scolaire_id');
    }

    /**
     * Accesseurs
     */
    public function getMoisLibelleAttribute()
    {
        return self::MOIS[$this->mois_paiement] ?? $this->mois_paiement;
    }

    public function getModePaiementLibelleAttribute()
    {
        return self::MODES_PAIEMENT[$this->mode_paiement] ?? $this->mode_paiement;
    }

    public function getSoldeRestantAttribute()
    {
        return $this->montant_du - $this->montant_paye;
    }

    public function getStatutBadgeAttribute()
    {
        return match($this->statut) {
            'complet' => ['class' => 'bg-green-100 text-green-800', 'label' => 'Payé'],
            'partiel' => ['class' => 'bg-yellow-100 text-yellow-800', 'label' => 'Partiel'],
            'impaye' => ['class' => 'bg-red-100 text-red-800', 'label' => 'Impayé'],
            default => ['class' => 'bg-gray-100 text-gray-800', 'label' => 'Inconnu']
        };
    }

    /**
     * Scopes
     */
    public function scopeAnneeActive($query)
    {
        $anneeActive = AnneeScolaire::getActive();
        if ($anneeActive) {
            return $query->where('annee_scolaire_id', $anneeActive->id);
        }
        return $query;
    }

    public function scopeByMois($query, $mois)
    {
        return $query->where('mois_paiement', $mois);
    }

    public function scopeByStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    public function scopeImpayes($query)
    {
        return $query->where('statut', 'impaye');
    }

    public function scopeRetards($query, $joursRetard = 15)
    {
        return $query->where('statut', 'impaye')
                    ->where('created_at', '<', now()->subDays($joursRetard));
    }

    /**
     * Méthodes statiques
     */
    public static function generateNumeroRecu()
    {
        $year = date('Y');
        $lastRecu = self::where('numero_recu', 'like', "MEN{$year}%")
                        ->orderBy('numero_recu', 'desc')
                        ->first();
        
        if ($lastRecu) {
            $lastNumber = intval(substr($lastRecu->numero_recu, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "MEN{$year}{$newNumber}";
    }

    public static function getMoisOptions()
    {
        return self::MOIS;
    }

    public static function getModePaiementOptions()
    {
        return self::MODES_PAIEMENT;
    }

    /**
     * Méthodes métier
     */
    public function marquerPaye($montant, $modePaiement)
    {
        $this->montant_paye += $montant;
        $this->mode_paiement = $modePaiement;
        $this->date_paiement = now();
        
        if ($this->montant_paye >= $this->montant_du) {
            $this->statut = 'complet';
        } else {
            $this->statut = 'partiel';
        }
        
        $this->save();
    }

    public function estEnRetard($joursRetard = 15)
    {
        return $this->statut === 'impaye' && 
               $this->created_at->diffInDays(now()) > $joursRetard;
    }
}
