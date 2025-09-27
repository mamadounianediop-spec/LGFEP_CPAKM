<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class EtatPaiementMensuel extends Model
{
    use HasFactory;

    protected $table = 'etats_paiement_mensuels';

    protected $fillable = [
        'personnel_id',
        'annee',
        'mois',
        'heures_effectuees',
        'primes',
        'retenues',
        'type_retenue',
        'montant_total',
        'avances',
        'restant',
        'statut_paiement',
        'visible',
        'archive',
        'date_archive',
        'date_validation',
        'validateur_id',
        'commentaire_validation'
    ];

    protected $casts = [
        'heures_effectuees' => 'decimal:2',
        'primes' => 'decimal:0',
        'retenues' => 'decimal:0',
        'montant_total' => 'decimal:0',
        'avances' => 'decimal:0',
        'restant' => 'decimal:0',
        'visible' => 'boolean',
        'archive' => 'boolean',
        'date_archive' => 'datetime',
        'date_validation' => 'datetime'
    ];

    // Constantes pour les statuts de paiement
    const STATUT_EN_ATTENTE = 'en_attente';
    const STATUT_PAYE = 'paye';

    const STATUTS = [
        self::STATUT_EN_ATTENTE => 'En attente',
        self::STATUT_PAYE => 'Payé',
    ];

    /**
     * Relation avec le personnel
     */
    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
    }

    /**
     * Relation avec l'année scolaire
     */
    public function anneeScolaire()
    {
        return $this->belongsTo(AnneeScolaire::class, 'annee_scolaire_id');
    }

    /**
     * Relation avec le validateur
     */
    public function validateur()
    {
        return $this->belongsTo(User::class, 'validateur_id');
    }

    /**
     * Calculer automatiquement les montants
     */
    public function calculerMontants()
    {
        $personnel = $this->personnel;
        
        // Calcul du salaire de base selon le mode de paiement
        $salaireBase = 0;
        if ($personnel && $personnel->mode_paiement === 'fixe') {
            $salaireBase = $personnel->montant_fixe ?? 0;
        } else if ($personnel && $personnel->mode_paiement === 'heure') {
            $salaireBase = ($this->heures_effectuees ?? 0) * ($personnel->tarif_heure ?? 0);
        }

        // Utiliser directement la valeur de retenues saisie (comme pour primes)
        // Le calcul des retenues par pourcentage se fait côté JavaScript avant la sauvegarde
        $retenues = $this->retenues ?? 0;
        
        // Montant total = Salaire de base + Primes - Retenues
        $this->montant_total = $salaireBase + ($this->primes ?? 0) - $retenues;
        
        // Nouvelle logique : si pas d'avance = payé intégralement (restant = 0)
        // Si avance > 0 = restant = montant_total - avance
        if (($this->avances ?? 0) == 0) {
            $this->restant = 0; // Payé intégralement
        } else {
            $this->restant = $this->montant_total - ($this->avances ?? 0); // Reste à payer
        }
    }

    /**
     * Accesseurs pour compatibilité avec les vues
     */
    public function getSalaireBaseAttribute()
    {
        $personnel = $this->personnel;
        if (!$personnel) return 0;
        
        if ($personnel->mode_paiement === 'fixe') {
            return $personnel->montant_fixe ?? 0;
        } else if ($personnel->mode_paiement === 'heure') {
            return ($this->heures_effectuees ?? 0) * ($personnel->tarif_heure ?? 0);
        }
        
        return 0;
    }

    public function getMontantBrutAttribute()
    {
        // Montant brut = Salaire de base + Primes
        return $this->salaire_base + ($this->primes ?? 0);
    }

    public function getTotalRetenuesAttribute()
    {
        return $this->retenues ?? 0;
    }

    public function getNetAPayerAttribute()
    {
        // Net à payer = Montant brut - Retenues (c'est le montant_total)
        return $this->montant_total ?? 0;
    }

    public function getAvanceDonneeAttribute()
    {
        return $this->avances ?? 0;
    }

    public function getResteAPayerAttribute()
    {
        // Reste à payer = Net à payer - Avance donnée (c'est le restant)
        return $this->restant ?? 0;
    }

    /**
     * Sauvegarder avec calcul automatique
     */
    public function save(array $options = [])
    {
        $this->calculerMontants();
        return parent::save($options);
    }

    /**
     * Accesseur pour le nom du mois formaté
     */
    public function getMoisNomAttribute()
    {
        $moisNoms = [
            10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars',
            4 => 'Avril', 5 => 'Mai', 6 => 'Juin',
            7 => 'Juillet', 8 => 'Août', 9 => 'Septembre'
        ];
        
        return $moisNoms[$this->mois] ?? 'Mois inconnu';
    }

    /**
     * Accesseur pour le statut formaté
     */
    public function getStatutFormateAttribute()
    {
        return self::STATUTS[$this->statut_paiement] ?? $this->statut_paiement;
    }

    /**
     * Scope pour les états visibles
     */
    public function scopeVisible($query)
    {
        return $query->where('visible', true);
    }

    /**
     * Scope pour les états archivés
     */
    public function scopeArchive($query)
    {
        return $query->where('archive', true);
    }

    /**
     * Scope pour les états actifs (non archivés)
     */
    public function scopeActif($query)
    {
        return $query->where('archive', false);
    }

    /**
     * Scope pour une période donnée
     */
    public function scopePourPeriode($query, $annee, $mois)
    {
        return $query->where('annee', $annee)->where('mois', $mois);
    }

    /**
     * Basculer la visibilité
     */
    public function toggleVisibilite()
    {
        $this->visible = !$this->visible;
        $this->save();
        return $this->visible;
    }

    /**
     * Archiver l'état
     */
    public function archiver()
    {
        $this->archive = true;
        $this->save();
    }

    /**
     * Règles de validation
     */
    public static function rules($id = null)
    {
        return [
            'personnel_id' => 'required|exists:personnels,id',
            'annee' => 'required|integer|min:2020|max:2030',
            'mois' => 'required|integer|min:1|max:12',
            'heures_effectuees' => 'nullable|numeric|min:0|max:200',
            'primes' => 'nullable|numeric|min:0',
            'retenues' => 'nullable|numeric|min:0',
            'avances' => 'nullable|numeric|min:0',
            'statut_paiement' => 'required|in:en_attente,paye',
            'visible' => 'boolean',
            'archive' => 'boolean'
        ];
    }
}