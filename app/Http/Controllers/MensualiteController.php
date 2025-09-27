<?php

namespace App\Http\Controllers;

use App\Models\Mensualite;
use App\Models\Inscription;
use App\Models\PreInscription;
use App\Models\AnneeScolaire;
use App\Models\Etablissement;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class MensualiteController extends Controller
{
    /**
     * Page principale du module mensualités
     */
    public function index(Request $request): View
    {
        $activeTab = $request->get('tab', 'paiements');
        $anneeActive = AnneeScolaire::getActive();
        
        // Si aucune année n'est active, rediriger vers paramètres
        if (!$anneeActive) {
            return redirect()->route('parametres.index')
                ->with('error', 'Aucune année scolaire n\'est activée. Veuillez activer une année scolaire.');
        }

        // Variables pour l'onglet PAIEMENTS
        $selectedEleve = null;
        $mensualites = collect();

        // Si un élève est recherché
        if ($request->has('eleve_id') && $request->eleve_id) {
            $inscription = Inscription::with(['preInscription', 'niveau', 'classe'])
                ->where('id', $request->eleve_id)
                ->where('annee_scolaire_id', $anneeActive->id)
                ->first();

            if ($inscription) {
                $selectedEleve = $inscription;
                
                // Récupérer ou créer les mensualités pour cet élève
                $mensualites = $this->getMensualitesForInscription($inscription);
            }
        }

        // Variables pour l'onglet HISTORIQUE - chargement par défaut
        $classes = \App\Models\Classe::orderBy('nom')->get();
        $anneesScolaires = \App\Models\AnneeScolaire::orderBy('libelle', 'desc')->get();
        
        // Filtre année scolaire (par défaut l'année active ou celle sélectionnée)
        $anneeSelectionnee = $request->filter_annee ?: $anneeActive->id;
        
        // Créer automatiquement les mensualités manquantes pour TOUS les élèves inscrits
        $toutesInscriptions = Inscription::where('annee_scolaire_id', $anneeSelectionnee)->get();
        foreach ($toutesInscriptions as $inscription) {
            $this->creerMensualitesManquantes($inscription, $anneeActive);
        }
        
        // Corriger les mensualités avec montant_du = 0 (pour les anciens inscrits)
        $this->corrigerMensualitesAvecMontantZero($anneeSelectionnee);
        
        // Maintenant récupérer TOUTES les mensualités de TOUS les élèves inscrits
        $query = Mensualite::with(['inscription.preInscription', 'inscription.classe'])
            ->where('annee_scolaire_id', $anneeSelectionnee);

        // Appliquer les filtres si on est sur l'onglet historique
        if ($activeTab === 'historique') {
            // Filtre classe
            if ($request->filter_classe) {
                $query->whereHas('inscription', function($q) use ($request) {
                    $q->where('classe_id', $request->filter_classe);
                });
            }

            // Filtre mois
            if ($request->filter_mois) {
                $query->where('mois_paiement', $request->filter_mois);
            }

            // Filtre statut
            if ($request->filter_statut) {
                $query->where('statut', $request->filter_statut);
            }

            // Filtre période
            if ($request->filter_periode && !in_array($request->filter_statut, ['impaye'])) {
                $days = (int) $request->filter_periode;
                $query->where('date_paiement', '>=', now()->subDays($days));
            }
        }

        // Ordonner par date de paiement puis par date de création
        $query->orderBy('date_paiement', 'desc')
              ->orderBy('created_at', 'desc');

        // Compter le total avant pagination
        $historiqueCount = $query->count();

        // Pagination - plus d'éléments par défaut, moins si filtres appliqués
        $perPage = $activeTab === 'historique' ? 10 : 5;
        $historiquePaiements = $query->paginate($perPage);
        $historiquePaiements->appends($request->query());

        // Variables pour l'onglet TABLEAU DE BORD
        // Charger les statistiques avec cache pour améliorer les performances
        $dashboardStats = Cache::remember(
            'mensualites_dashboard_stats_' . $anneeActive->id, 
            300, // 5 minutes de cache
            function () use ($anneeActive) {
                return $this->getDashboardStats($anneeActive);
            }
        );

        // Variables pour l'onglet REÇUS & RAPPORTS
        // Charger toujours les données de base pour l'affichage
        $rapportsData = $this->getRapportsData($anneeActive, $request);

        return view('mensualites.index', compact(
            'activeTab',
            'anneeActive',
            'selectedEleve',
            'mensualites',
            'historiquePaiements',
            'historiqueCount',
            'classes',
            'anneesScolaires',
            'dashboardStats',
            'rapportsData'
        ));
    }

    /**
     * Recherche d'élèves inscrits (AJAX)
     */
    public function searchEleves(Request $request): JsonResponse
    {
        $search = $request->get('q');
        $anneeActive = AnneeScolaire::getActive();
        
        if (!$anneeActive || !$search) {
            return response()->json([]);
        }

        $inscriptions = Inscription::with(['preInscription', 'niveau', 'classe'])
            ->where('annee_scolaire_id', $anneeActive->id)
            ->where('statut', 'actif')
            ->whereHas('preInscription', function($query) use ($search) {
                $query->where('nom', 'like', "%{$search}%")
                      ->orWhere('prenom', 'like', "%{$search}%")
                      ->orWhere('ine', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get()
            ->map(function($inscription) {
                return [
                    'id' => $inscription->id,
                    'nom' => $inscription->preInscription->nom,
                    'prenom' => $inscription->preInscription->prenom,
                    'ine' => $inscription->preInscription->ine,
                    'classe' => $inscription->classe->nom,
                    'niveau' => $inscription->niveau->nom,
                    'display' => $inscription->preInscription->nom . ' ' . $inscription->preInscription->prenom . ' (' . $inscription->preInscription->ine . ') - ' . $inscription->classe->nom
                ];
            });

        return response()->json($inscriptions);
    }

    /**
     * Récupérer ou créer les mensualités pour une inscription
     */
    private function getMensualitesForInscription(Inscription $inscription)
    {
        // Vérifier si les mensualités existent déjà
        $mensualitesExistantes = Mensualite::where('inscription_id', $inscription->id)
            ->where('annee_scolaire_id', $inscription->annee_scolaire_id)
            ->get()
            ->keyBy('mois_paiement');

        $mensualites = collect();
        
        // Les 10 mois de l'année scolaire (octobre à juillet)
        $moisScolaires = [
            'octobre', 'novembre', 'decembre', 'janvier', 'fevrier', 
            'mars', 'avril', 'mai', 'juin', 'juillet'
        ];

        foreach ($moisScolaires as $mois) {
            if (isset($mensualitesExistantes[$mois])) {
                // Mensualité existe déjà
                $mensualites->push($mensualitesExistantes[$mois]);
            } else {
                // Créer une nouvelle mensualité
                $nouvelleMensualite = Mensualite::create([
                    'inscription_id' => $inscription->id,
                    'annee_scolaire_id' => $inscription->annee_scolaire_id,
                    'mois_paiement' => $mois,
                    'montant_du' => $this->getMontantMensualite($inscription->niveau_id),
                    'montant_paye' => 0,
                    'statut' => 'impaye', // Selon la migration existante
                    'numero_recu' => null // Sera généré lors du paiement
                ]);
                
                $mensualites->push($nouvelleMensualite);
            }
        }

        return $mensualites->sortBy(function($mensualite) use ($moisScolaires) {
            return array_search($mensualite->mois_paiement, $moisScolaires);
        });
    }

    /**
     * Obtenir le montant de la mensualité selon le niveau
     */
    private function getMontantMensualite($niveauId)
    {
        $anneeActive = AnneeScolaire::getActive();
        
        // Debug
        \Log::info("getMontantMensualite - Niveau: $niveauId, Année: {$anneeActive->id}");
        
        // Récupérer le frais mensualité pour ce niveau et cette année
        $frais = \App\Models\Frais::where('niveau_id', $niveauId)
            ->where('type', 'mensualite')
            ->where('annee_scolaire_id', $anneeActive->id)
            ->where('actif', true)
            ->first();

        \Log::info("Frais trouvé: " . ($frais ? $frais->montant : 'AUCUN'));

        return $frais ? $frais->montant : 15000; // Montant par défaut
    }

    /**
     * Corriger les montants des mensualités existantes
     */
    public function corrigerMontants()
    {
        try {
            $mensualitesCorrigees = 0;
            
            // Récupérer toutes les mensualités avec montant_du = 0
            $mensualitesACorreger = Mensualite::with('inscription.niveau')
                ->where('montant_du', 0)
                ->get();
            
            foreach ($mensualitesACorreger as $mensualite) {
                $niveauId = $mensualite->inscription->niveau_id;
                $montantCorrect = $this->getMontantMensualite($niveauId);
                
                $mensualite->montant_du = $montantCorrect;
                $mensualite->save();
                $mensualitesCorrigees++;
            }
            
            return response()->json([
                'success' => true,
                'message' => "Correction réussie de {$mensualitesCorrigees} mensualités"
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la correction: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Enregistrer un paiement de mensualité
     */
    public function enregistrerPaiement(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'mensualite_id' => 'required|exists:mensualites,id',
                'montant_recu' => 'required|numeric|min:1|max:1000000',
                'mode_paiement' => 'required|in:especes,virement,cheque,orange_money,wave,free_money',
                'observations' => 'nullable|string|max:500',
                'paiement_complet' => 'nullable'
            ]);

            $mensualite = Mensualite::findOrFail($validated['mensualite_id']);
            $montantRecu = $validated['montant_recu'];

            // Si paiement complet, calculer le montant restant
            if ($request->paiement_complet == '1') {
                $montantRecu = $mensualite->montant_du - $mensualite->montant_paye;
            }

            // Vérifier que le montant ne dépasse pas ce qui reste à payer
            $montantRestant = $mensualite->montant_du - $mensualite->montant_paye;
            if ($montantRecu > $montantRestant) {
                return redirect()->back()->with('error', 'Le montant saisi dépasse le montant restant à payer.');
            }

            // Mettre à jour la mensualité
            $nouveauMontantPaye = $mensualite->montant_paye + $montantRecu;
            $nouveauStatut = ($nouveauMontantPaye >= $mensualite->montant_du) ? 'complet' : 'partiel';

            $mensualite->update([
                'montant_paye' => $nouveauMontantPaye,
                'mode_paiement' => $validated['mode_paiement'],
                'date_paiement' => now(),
                'statut' => $nouveauStatut,
                'observations' => $validated['observations'],
                'numero_recu' => $mensualite->numero_recu ?: Mensualite::generateNumeroRecu()
            ]);

            // Vider le cache des statistiques du tableau de bord
            Cache::forget('mensualites_dashboard_stats_' . $mensualite->annee_scolaire_id);

            return redirect()->route('mensualites.index', ['eleve_id' => $mensualite->inscription_id, 'tab' => 'paiements'])
                ->with('success', 'Paiement enregistré avec succès.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'enregistrement du paiement: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue lors de l\'enregistrement du paiement: ' . $e->getMessage());
        }
    }

    /**
     * Exporter les paiements selon les filtres
     */
    public function exportPaiements(Request $request)
    {
        $anneeActive = AnneeScolaire::getActive();
        
        // Utiliser l'année sélectionnée ou l'année active par défaut
        $anneeId = $request->filter_annee ?? $anneeActive->id;
        $anneeSelectionnee = AnneeScolaire::find($anneeId) ?? $anneeActive;
        
        // Construire la query des paiements avec filtres
        $query = Mensualite::with(['inscription.preInscription', 'inscription.classe', 'inscription.anneeScolaire'])
            ->where('annee_scolaire_id', $anneeId);

        // Appliquer les mêmes filtres que l'historique
        if ($request->filter_classe) {
            $query->whereHas('inscription', function($q) use ($request) {
                $q->where('classe_id', $request->filter_classe);
            });
        }

        if ($request->filter_mois) {
            $query->where('mois_paiement', $request->filter_mois);
        }

        // Gestion du filtre statut comme dans l'historique
        if ($request->filter_statut) {
            switch ($request->filter_statut) {
                case 'paye':
                    // Payés = tous les paiements avec date_paiement (complet + partiel)
                    $query->whereNotNull('date_paiement');
                    break;
                case 'complet':
                    $query->where('statut', 'complet');
                    break;
                case 'partiel':
                    $query->where('statut', 'partiel');
                    break;
                case 'impaye':
                    $query->where('statut', 'impaye')
                          ->whereNull('date_paiement');
                    break;
            }
        }

        if ($request->filter_periode) {
            $days = (int) $request->filter_periode;
            $query->where('date_paiement', '>=', now()->subDays($days));
        }

        $paiements = $query->orderBy('date_paiement', 'desc')->get();

        // Afficher l'aperçu de l'export
        return view('mensualites.export', [
            'paiements' => $paiements,
            'anneeActive' => $anneeSelectionnee,
            'filtres' => $request->all()
        ]);
    }

    /**
     * Télécharger le PDF des paiements exportés
     */
    public function downloadExportPDF(Request $request)
    {
        $anneeActive = AnneeScolaire::getActive();
        
        // Utiliser l'année sélectionnée ou l'année active par défaut
        $anneeId = $request->filter_annee ?? $anneeActive->id;
        $anneeSelectionnee = AnneeScolaire::find($anneeId) ?? $anneeActive;
        
        // Construire la query des paiements avec filtres
        $query = Mensualite::with(['inscription.preInscription', 'inscription.classe', 'inscription.anneeScolaire'])
            ->where('annee_scolaire_id', $anneeId);

        // Appliquer les mêmes filtres que l'historique
        if ($request->filter_classe) {
            $query->whereHas('inscription', function($q) use ($request) {
                $q->where('classe_id', $request->filter_classe);
            });
        }

        if ($request->filter_mois) {
            $query->where('mois_paiement', $request->filter_mois);
        }

        // Gestion du filtre statut comme dans l'historique
        if ($request->filter_statut) {
            switch ($request->filter_statut) {
                case 'paye':
                    // Payés = tous les paiements avec date_paiement (complet + partiel)
                    $query->whereNotNull('date_paiement');
                    break;
                case 'complet':
                    $query->where('statut', 'complet');
                    break;
                case 'partiel':
                    $query->where('statut', 'partiel');
                    break;
                case 'impaye':
                    $query->where('statut', 'impaye')
                          ->whereNull('date_paiement');
                    break;
            }
        }

        if ($request->filter_periode) {
            $days = (int) $request->filter_periode;
            $query->where('date_paiement', '>=', now()->subDays($days));
        }

        $paiements = $query->orderBy('date_paiement', 'desc')->get();

        // Générer le PDF pour téléchargement
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('mensualites.export-pdf', [
            'paiements' => $paiements,
            'anneeActive' => $anneeSelectionnee,
            'filtres' => $request->all()
        ]);

        $filename = 'export_mensualites_' . $anneeSelectionnee->nom . '_' . date('Y-m-d_H-i') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Afficher le reçu de paiement mensualité
     */
    public function voirRecu($id)
    {
        $mensualite = Mensualite::with([
            'inscription.preInscription', 
            'inscription.classe', 
            'inscription.niveau', 
            'inscription.anneeScolaire'
        ])->findOrFail($id);

        // Vérifier que la mensualité a bien été payée
        if (!$mensualite->numero_recu || $mensualite->montant_paye == 0) {
            return redirect()->back()->with('error', 'Aucun reçu disponible pour cette mensualité.');
        }

        return view('mensualites.recu', compact('mensualite'));
    }



    /**
     * Calculer les statistiques pour le tableau de bord (optimisé)
     */
    private function getDashboardStats($anneeActive)
    {
        // Statistiques des élèves inscrits
        $elevesInscrits = Inscription::where('annee_scolaire_id', $anneeActive->id)->count();
        
        // Statistiques générales des mensualités en une seule requête
        $generalStats = Mensualite::where('annee_scolaire_id', $anneeActive->id)
            ->selectRaw('
                COUNT(*) as total_mensualites,
                COUNT(DISTINCT inscription_id) as eleves_avec_mensualites,
                SUM(CASE WHEN statut = "complet" THEN 1 ELSE 0 END) as mensualites_complet,
                SUM(CASE WHEN statut = "partiel" THEN 1 ELSE 0 END) as mensualites_partiel,
                SUM(CASE WHEN statut = "impaye" THEN 1 ELSE 0 END) as mensualites_impaye,
                SUM(CASE WHEN statut IN ("complet", "partiel") THEN 1 ELSE 0 END) as mensualites_paye,
                SUM(montant_du) as montant_total,
                SUM(montant_paye) as montant_paye
            ')
            ->first();

        // Calculs des prévisions basées sur les élèves inscrits
        $mensualitesAttendues = $elevesInscrits * 10; // 10 mois d'octobre à juillet
        $mensualitesManquantes = max(0, $mensualitesAttendues - $generalStats->total_mensualites);
        $tauxCouvertureMensualites = $mensualitesAttendues > 0 ? 
            round(($generalStats->total_mensualites / $mensualitesAttendues) * 100, 2) : 0;

        // Calcul du montant moyen d'une mensualité
        $montantMoyenMensualite = $generalStats->total_mensualites > 0 ? 
            $generalStats->montant_total / $generalStats->total_mensualites : 0;
        
        // Prévision du montant potentiel pour les mensualités manquantes
        $montantPotentielManquant = $mensualitesManquantes * $montantMoyenMensualite;

        $montantRestant = $generalStats->montant_total - $generalStats->montant_paye;
        $tauxRecouvrement = $generalStats->montant_total > 0 ? 
            round(($generalStats->montant_paye / $generalStats->montant_total) * 100, 2) : 0;

        // Statistiques par mois
        $statsMois = Mensualite::where('annee_scolaire_id', $anneeActive->id)
            ->selectRaw('mois_paiement, 
                COUNT(*) as total,
                SUM(CASE WHEN statut = "complet" THEN 1 ELSE 0 END) as complet,
                SUM(CASE WHEN statut = "partiel" THEN 1 ELSE 0 END) as partiel,
                SUM(CASE WHEN statut IN ("complet", "partiel") THEN 1 ELSE 0 END) as paye,
                SUM(montant_du) as montant_du_total,
                SUM(montant_paye) as montant_paye_total')
            ->groupBy('mois_paiement')
            ->orderByRaw("FIELD(mois_paiement, 'septembre', 'octobre', 'novembre', 'decembre', 'janvier', 'fevrier', 'mars', 'avril', 'mai', 'juin')")
            ->get();

        // Statistiques par niveau
        $statsNiveau = Mensualite::where('mensualites.annee_scolaire_id', $anneeActive->id)
            ->join('inscriptions', 'mensualites.inscription_id', '=', 'inscriptions.id')
            ->join('niveaux', 'inscriptions.niveau_id', '=', 'niveaux.id')
            ->selectRaw('niveaux.nom as niveau_nom,
                COUNT(mensualites.id) as total,
                SUM(CASE WHEN mensualites.statut = "complet" THEN 1 ELSE 0 END) as paye,
                SUM(mensualites.montant_du) as montant_du_total,
                SUM(mensualites.montant_paye) as montant_paye_total')
            ->groupBy('niveaux.id', 'niveaux.nom', 'niveaux.ordre')
            ->orderBy('niveaux.ordre')
            ->get();

        // Top 5 des meilleures classes (optimisé)
        $topClasses = Mensualite::where('mensualites.annee_scolaire_id', $anneeActive->id)
            ->join('inscriptions', 'mensualites.inscription_id', '=', 'inscriptions.id')
            ->join('classes', 'inscriptions.classe_id', '=', 'classes.id')
            ->selectRaw('classes.nom as classe_nom,
                COUNT(mensualites.id) as total_mensualites,
                SUM(CASE WHEN mensualites.statut = "complet" THEN 1 ELSE 0 END) as mensualites_completes,
                SUM(CASE WHEN mensualites.statut = "partiel" THEN 1 ELSE 0 END) as mensualites_partielles,
                SUM(CASE WHEN mensualites.statut IN ("complet", "partiel") THEN 1 ELSE 0 END) as mensualites_payees,
                ROUND((SUM(CASE WHEN mensualites.statut IN ("complet", "partiel") THEN 1 ELSE 0 END) / COUNT(mensualites.id)) * 100, 2) as taux_paiement,
                SUM(mensualites.montant_paye) as montant_total_paye')
            ->groupBy('classes.id', 'classes.nom')
            ->having('total_mensualites', '>', 0)
            ->orderBy('taux_paiement', 'desc')
            ->limit(5)
            ->get();

        // Paiements récents (derniers 7 jours) - Limité pour la performance
        $paiementsRecents = Mensualite::with(['inscription.preInscription:id,nom,prenom', 'inscription.classe:id,nom'])
            ->where('annee_scolaire_id', $anneeActive->id)
            ->whereNotNull('date_paiement')
            ->where('date_paiement', '>=', now()->subDays(7))
            ->orderBy('date_paiement', 'desc')
            ->limit(8)
            ->get();

        return [
            'general' => [
                'eleves_inscrits' => $elevesInscrits,
                'eleves_avec_mensualites' => $generalStats->eleves_avec_mensualites,
                'total_mensualites' => $generalStats->total_mensualites,
                'mensualites_attendues' => $mensualitesAttendues,
                'mensualites_manquantes' => $mensualitesManquantes,
                'taux_couverture_mensualites' => $tauxCouvertureMensualites,
                'mensualites_paye' => $generalStats->mensualites_paye,
                'mensualites_complet' => $generalStats->mensualites_complet,
                'mensualites_partiel' => $generalStats->mensualites_partiel,
                'mensualites_impaye' => $generalStats->mensualites_impaye,
                'montant_total' => $generalStats->montant_total,
                'montant_paye' => $generalStats->montant_paye,
                'montant_restant' => $montantRestant,
                'montant_moyen_mensualite' => $montantMoyenMensualite,
                'montant_potentiel_manquant' => $montantPotentielManquant,
                'taux_recouvrement' => $tauxRecouvrement,
            ],
            'par_mois' => $statsMois,
            'par_niveau' => $statsNiveau,
            'top_classes' => $topClasses,
            'paiements_recents' => $paiementsRecents,
        ];
    }

    /**
     * Préparer les données pour l'onglet Reçus & Rapports
     */
    private function getRapportsData($anneeActive, $request)
    {
        // Récupérer les classes et niveaux pour les filtres
        $classes = \App\Models\Classe::orderBy('nom')->get();
        $niveaux = \App\Models\Niveau::orderBy('ordre')->get();

        // Statistiques de base pour les rapports
        $totalReceusGeneres = Mensualite::where('annee_scolaire_id', $anneeActive->id)
            ->whereNotNull('numero_recu')
            ->count();

        $dernierReceGenere = Mensualite::where('annee_scolaire_id', $anneeActive->id)
            ->whereNotNull('numero_recu')
            ->orderBy('created_at', 'desc')
            ->first();

        // Statistiques par période
        $paiementsParMois = Mensualite::where('annee_scolaire_id', $anneeActive->id)
            ->whereNotNull('date_paiement')
            ->selectRaw('MONTH(date_paiement) as mois, YEAR(date_paiement) as annee, COUNT(*) as total, SUM(montant_paye) as montant')
            ->groupBy('annee', 'mois')
            ->orderBy('annee', 'desc')
            ->orderBy('mois', 'desc')
            ->limit(12)
            ->get();

        // Reçus récents (20 derniers)
        $recusRecents = Mensualite::with(['inscription.preInscription', 'inscription.classe', 'inscription.niveau'])
            ->where('annee_scolaire_id', $anneeActive->id)
            ->whereNotNull('numero_recu')
            ->orderBy('date_paiement', 'desc')
            ->limit(20)
            ->get();

        // Rapports prédéfinis disponibles
        $rapportsDisponibles = [
            [
                'nom' => 'Rapport mensuel des paiements',
                'description' => 'Détail de tous les paiements du mois en cours',
                'icone' => 'fa-calendar-month',
                'couleur' => 'blue',
                'type' => 'mensuel'
            ],
            [
                'nom' => 'Rapport par classe',
                'description' => 'État des paiements groupés par classe',
                'icone' => 'fa-users',
                'couleur' => 'green',
                'type' => 'classe'
            ],
            [
                'nom' => 'Rapport par niveau',
                'description' => 'Analyse des paiements par niveau d\'étude',
                'icone' => 'fa-graduation-cap',
                'couleur' => 'purple',
                'type' => 'niveau'
            ],
            [
                'nom' => 'Rapport des impayés',
                'description' => 'Liste des mensualités non payées',
                'icone' => 'fa-exclamation-triangle',
                'couleur' => 'red',
                'type' => 'impayes'
            ],
            [
                'nom' => 'Rapport financier global',
                'description' => 'Synthèse financière complète',
                'icone' => 'fa-chart-line',
                'couleur' => 'indigo',
                'type' => 'financier'
            ]
        ];

        return [
            'classes' => $classes,
            'niveaux' => $niveaux,
            'total_recus' => $totalReceusGeneres,
            'dernier_recu' => $dernierReceGenere,
            'paiements_par_mois' => $paiementsParMois,
            'recus_recents' => $recusRecents,
            'rapports_disponibles' => $rapportsDisponibles
        ];
    }

    /**
     * Générer un rapport prédéfini
     */
    public function genererRapport($type)
    {
        $anneeActive = \App\Models\AnneeScolaire::where('actif', true)->first();
        if (!$anneeActive) {
            return redirect()->back()->with('error', 'Aucune année scolaire active trouvée');
        }

        switch ($type) {
            case 'mensuel':
                return $this->rapportMensuel($anneeActive);
            case 'classe':
                return $this->rapportParClasse($anneeActive);
            case 'niveau':
                return $this->rapportParNiveau($anneeActive);
            case 'impayes':
                return $this->rapportImpayes($anneeActive);
            case 'financier':
                return $this->rapportFinancier($anneeActive);
            default:
                return redirect()->back()->with('error', 'Type de rapport non reconnu');
        }
    }

    /**
     * Générer un rapport personnalisé
     */
    public function rapportPersonnalise(Request $request)
    {
        $anneeActive = \App\Models\AnneeScolaire::where('actif', true)->first();
        if (!$anneeActive) {
            return redirect()->back()->with('error', 'Aucune année scolaire active trouvée');
        }

        // Récupérer les filtres avec la nouvelle logique
        $periode = $request->get('periode');
        $dateDebut = $request->get('date_debut');
        $dateFin = $request->get('date_fin');
        $classeId = $request->get('classe_id');
        $niveauId = $request->get('niveau_id');
        $statutPaiement = $request->get('statut_paiement', 'tous');

        // Construire la requête de base
        $query = Mensualite::with(['inscription.preInscription', 'inscription.classe', 'inscription.niveau'])
            ->where('annee_scolaire_id', $anneeActive->id);

        // Appliquer les filtres de période
        if ($periode && $periode !== 'personnalisee') {
            $this->appliquerFiltrePeriode($query, $periode);
        } elseif ($periode === 'personnalisee' && ($dateDebut || $dateFin)) {
            if ($dateDebut) $query->where('date_paiement', '>=', $dateDebut);
            if ($dateFin) $query->where('date_paiement', '<=', $dateFin);
        }

        // Appliquer les filtres sur les statuts de paiement
        if ($statutPaiement !== 'tous') {
            $this->appliquerFiltreStatut($query, $statutPaiement);
        }

        // Appliquer les autres filtres
        if ($classeId) {
            $query->whereHas('inscription', function($q) use ($classeId) {
                $q->where('classe_id', $classeId);
            });
        }
        if ($niveauId) {
            $query->whereHas('inscription', function($q) use ($niveauId) {
                $q->where('niveau_id', $niveauId);
            });
        }

        $mensualites = $query->orderBy('date_paiement', 'desc')->get();

        // Préparer les paramètres pour le PDF
        $parametres = $request->all();
        $parametres['statut_selectionne'] = $statutPaiement;
        $parametres['periode_selectionnee'] = $periode;

        // Générer le PDF
        return $this->genererPdfPersonnalise($mensualites, $parametres, $anneeActive);
    }

    /**
     * Appliquer un filtre de période prédéfinie
     */
    private function appliquerFiltrePeriode($query, $periode)
    {
        switch ($periode) {
            case 'aujourd_hui':
                $query->whereDate('date_paiement', today());
                break;
            case 'cette_semaine':
                $query->whereBetween('date_paiement', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'ce_mois':
                $query->whereMonth('date_paiement', now()->month)
                      ->whereYear('date_paiement', now()->year);
                break;
            case 'mois_dernier':
                $query->whereMonth('date_paiement', now()->subMonth()->month)
                      ->whereYear('date_paiement', now()->subMonth()->year);
                break;
            case 'ce_trimestre':
                $startOfQuarter = now()->startOfQuarter();
                $endOfQuarter = now()->endOfQuarter();
                $query->whereBetween('date_paiement', [$startOfQuarter, $endOfQuarter]);
                break;
            case 'cette_annee':
                $query->whereYear('date_paiement', now()->year);
                break;
        }
    }

    /**
     * Appliquer un filtre de statut de paiement
     */
    private function appliquerFiltreStatut($query, $statut)
    {
        switch ($statut) {
            case 'paye':
                $query->where('statut', 'paye')
                      ->where('montant_paye', '>', 0)
                      ->whereRaw('montant_paye >= montant_du');
                break;
            case 'partiel':
                $query->where(function($q) {
                    $q->where('statut', 'partiel')
                      ->orWhere(function($subQ) {
                          $subQ->where('montant_paye', '>', 0)
                               ->whereRaw('montant_paye < montant_du');
                      });
                });
                break;
            case 'impaye':
                $query->where(function($q) {
                    $q->where('statut', 'impaye')
                      ->orWhere('montant_paye', '=', 0);
                });
                break;
        }
    }

    /**
     * Export Excel personnalisé
     */
    public function exportExcel(Request $request)
    {
        $anneeActive = \App\Models\AnneeScolaire::where('actif', true)->first();
        if (!$anneeActive) {
            return redirect()->back()->with('error', 'Aucune année scolaire active trouvée');
        }

        // Récupérer les filtres avec la nouvelle logique
        $periode = $request->get('periode');
        $dateDebut = $request->get('date_debut');
        $dateFin = $request->get('date_fin');
        $classeId = $request->get('classe_id');
        $niveauId = $request->get('niveau_id');
        $statutPaiement = $request->get('statut_paiement', 'tous');

        // Construire la requête de base
        $query = Mensualite::with(['inscription.preInscription', 'inscription.classe', 'inscription.niveau'])
            ->where('annee_scolaire_id', $anneeActive->id);

        // Appliquer les filtres de période
        if ($periode && $periode !== 'personnalisee') {
            $this->appliquerFiltrePeriode($query, $periode);
        } elseif ($periode === 'personnalisee' && ($dateDebut || $dateFin)) {
            if ($dateDebut) $query->where('date_paiement', '>=', $dateDebut);
            if ($dateFin) $query->where('date_paiement', '<=', $dateFin);
        }

        // Appliquer les filtres sur les statuts de paiement
        if ($statutPaiement !== 'tous') {
            $this->appliquerFiltreStatut($query, $statutPaiement);
        }

        // Appliquer les autres filtres
        if ($classeId) {
            $query->whereHas('inscription', function($q) use ($classeId) {
                $q->where('classe_id', $classeId);
            });
        }
        if ($niveauId) {
            $query->whereHas('inscription', function($q) use ($niveauId) {
                $q->where('niveau_id', $niveauId);
            });
        }

        $mensualites = $query->orderBy('date_paiement', 'desc')->get();

        // Préparer les paramètres pour l'Excel
        $parametres = $request->all();
        $parametres['statut_selectionne'] = $statutPaiement;
        $parametres['periode_selectionnee'] = $periode;

        // Générer le fichier Excel
        return $this->genererExcel($mensualites, $parametres, $anneeActive);
    }


    
    /**
     * Modifier une mensualité existante
     */
    public function edit($id)
    {
        $mensualite = Mensualite::with(['inscription.preInscription', 'inscription.classe', 'inscription.niveau'])
            ->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'mensualite' => [
                'id' => $mensualite->id,
                'eleve_nom' => $mensualite->inscription->preInscription->nom . ' ' . $mensualite->inscription->preInscription->prenom,
                'eleve_ine' => $mensualite->inscription->preInscription->ine,
                'classe' => $mensualite->inscription->classe->nom,
                'niveau' => $mensualite->inscription->niveau->nom,
                'mois' => $mensualite->mois,
                'mois_libelle' => $mensualite->mois_libelle,
                'montant_du' => $mensualite->montant_du,
                'montant_paye' => $mensualite->montant_paye,
                'mode_paiement' => $mensualite->mode_paiement,
                'date_paiement' => $mensualite->date_paiement ? $mensualite->date_paiement->format('Y-m-d') : null,
                'numero_recu' => $mensualite->numero_recu,
                'remarques' => $mensualite->observations, // Mapper observations vers remarques
                'statut' => $mensualite->statut
            ]
        ]);
    }
    
    /**
     * Mettre à jour une mensualité existante
     */
    public function update(Request $request, $id)
    {
        try {
            $mensualite = Mensualite::findOrFail($id);
            
            // Validation de base
            $validated = $request->validate([
                'montant_paye' => 'required|numeric|min:0',
                'mode_paiement' => 'required|string|in:especes,cheque,virement,carte,mobile',
                'date_paiement' => 'required|date',
                'remarques' => 'nullable|string|max:500'
            ]);
            
            // VALIDATION MÉTIER : Vérifier que le montant ne dépasse pas le montant dû
            if ($validated['montant_paye'] > $mensualite->montant_du) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le montant payé (' . number_format($validated['montant_paye'], 0, ',', ' ') . ' FCFA) ne peut pas dépasser le montant dû (' . number_format($mensualite->montant_du, 0, ',', ' ') . ' FCFA).',
                    'errors' => [
                        'montant_paye' => ['Le montant payé ne peut pas dépasser le montant dû']
                    ]
                ], 422);
            }
            
            // Calculer le nouveau statut
            if ($validated['montant_paye'] >= $mensualite->montant_du) {
                $nouveauStatut = 'complet';
            } elseif ($validated['montant_paye'] > 0) {
                $nouveauStatut = 'partiel';
            } else {
                $nouveauStatut = 'impaye';
            }
            
            // Sauvegarder l'ancien numéro de reçu et générer un nouveau si nécessaire
            $ancienNumeroRecu = $mensualite->numero_recu;
            $nouveauNumeroRecu = $ancienNumeroRecu;
            
            // Si le montant payé change et qu'il y a un paiement, générer un nouveau reçu
            if ($validated['montant_paye'] > 0 && $validated['montant_paye'] != $mensualite->montant_paye) {
                $nouveauNumeroRecu = $this->genererNumeroRecu($mensualite->annee_scolaire_id);
            }
            
            // Mettre à jour la mensualité
            $mensualite->update([
                'montant_paye' => $validated['montant_paye'],
                'mode_paiement' => $validated['mode_paiement'],
                'date_paiement' => $validated['date_paiement'],
                'observations' => $validated['remarques'], // Mapper remarques vers observations
                'statut' => $nouveauStatut,
                'numero_recu' => $nouveauNumeroRecu
            ]);
            
            $message = 'Paiement modifié avec succès';
            if ($nouveauNumeroRecu !== $ancienNumeroRecu) {
                $message .= '. Un nouveau reçu a été généré: ' . $nouveauNumeroRecu;
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'nouveau_numero_recu' => $nouveauNumeroRecu,
                'ancien_numero_recu' => $ancienNumeroRecu
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error("Erreur lors de la mise à jour mensualité ID: {$id}", [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Générer un numéro de reçu unique
     */
    private function genererNumeroRecu()
    {
        try {
            $anneeActive = AnneeScolaire::getActive();
            
            if (!$anneeActive) {
                throw new \Exception('Aucune année scolaire active trouvée');
            }
            
            // Extraire l'année de début à partir de la date de début ou du libellé
            $anneeDebut = null;
            if ($anneeActive->date_debut) {
                $anneeDebut = $anneeActive->date_debut->format('Y');
            } elseif ($anneeActive->libelle) {
                // Extraire l'année du libellé (ex: "2025-2026" -> "2025")
                preg_match('/(\d{4})/', $anneeActive->libelle, $matches);
                $anneeDebut = $matches[1] ?? date('Y');
            } else {
                $anneeDebut = date('Y');
            }
            
            $prefixe = 'REC-' . $anneeDebut . '-';
            
            // Trouver le dernier numéro de reçu pour cette année
            $dernierRecu = Mensualite::where('annee_scolaire_id', $anneeActive->id)
                ->whereNotNull('numero_recu')
                ->where('numero_recu', 'like', $prefixe . '%')
                ->orderBy('numero_recu', 'desc')
                ->first();
            
            if ($dernierRecu && $dernierRecu->numero_recu) {
                // Extraire le numéro séquentiel du dernier reçu
                $parts = explode('-', $dernierRecu->numero_recu);
                $dernierNumero = (int) end($parts);
                $nouveauNumero = $dernierNumero + 1;
            } else {
                $nouveauNumero = 1;
            }
            
            return $prefixe . str_pad($nouveauNumero, 4, '0', STR_PAD_LEFT);
            
        } catch (\Exception $e) {
            \Log::error("Erreur génération numéro reçu: " . $e->getMessage());
            // Fallback: utiliser timestamp si erreur
            return 'REC-' . date('Y') . '-' . str_pad(time() % 10000, 4, '0', STR_PAD_LEFT);
        }
    }

    /**
     * Supprimer une mensualité
     */
    public function destroy($id)
    {
        $mensualite = Mensualite::findOrFail($id);
        
        // Vérifier si on peut supprimer (par exemple, pas plus de 24h après création)
        $peutSupprimer = $mensualite->created_at->diffInHours(now()) <= 24;
        
        if (!$peutSupprimer) {
            return response()->json([
                'success' => false,
                'message' => 'Ce paiement ne peut plus être supprimé (plus de 24h depuis sa création)'
            ], 422);
        }
        
        $numeroRecu = $mensualite->numero_recu;
        $eleveName = $mensualite->inscription->preInscription->nom . ' ' . $mensualite->inscription->preInscription->prenom;
        
        $mensualite->delete();
        
        return response()->json([
            'success' => true,
            'message' => "Paiement supprimé avec succès pour {$eleveName}",
            'numero_recu_supprime' => $numeroRecu
        ]);
    }

    /**
     * Données pour les rapports de mensualités avec filtres dynamiques
     */
    public function getRapportsMensualites(Request $request): JsonResponse
    {
        try {
            $annee = $request->get('annee');
            $mois = $request->get('mois');
            $dateDebut = $request->get('dateDebut');
            $dateFin = $request->get('dateFin');
            $statut = $request->get('statut');

            // Base query avec relations
            $query = Mensualite::with(['inscription.preInscription', 'inscription.classe', 'anneeScolaire']);

            // Filtrer par année scolaire
            if ($annee) {
                $query->where('annee_scolaire_id', $annee);
            } else {
                // Par défaut, année active
                $anneeActive = AnneeScolaire::getActive();
                if ($anneeActive) {
                    $query->where('annee_scolaire_id', $anneeActive->id);
                }
            }

            // Filtrer par mois scolaire (octobre à juillet)
            if ($mois) {
                $query->where('mois_paiement', $mois);
            }

            // Filtrer par statut
            if ($statut) {
                $query->where('statut', $statut);
            }

            // Filtrer par période de paiement
            if ($dateDebut && $dateFin) {
                $query->whereBetween('date_paiement', [
                    $dateDebut . ' 00:00:00',
                    $dateFin . ' 23:59:59'
                ]);
            } elseif ($dateDebut) {
                $query->whereDate('date_paiement', '>=', $dateDebut);
            } elseif ($dateFin) {
                $query->whereDate('date_paiement', '<=', $dateFin);
            }

            $mensualites = $query->get();

            if ($mensualites->isEmpty()) {
                return response()->json(null);
            }

            // Calculs spécifiques aux mensualités
            
            // Calculer le total des élèves inscrits pour l'année scolaire (pas seulement ceux avec mensualités)
            $elevesInscritQuery = Inscription::where('annee_scolaire_id', $annee ?: AnneeScolaire::getActive()?->id);
            $elevesInscritsTotal = $elevesInscritQuery->count();
            
            // Élèves ayant des mensualités (pour comparaison)
            $elevesAvecMensualites = $mensualites->unique('inscription_id')->count();
            
            $totaux = [
                'eleves_total' => $elevesInscritsTotal, // Total des élèves inscrits
                'eleves_avec_mensualites' => $elevesAvecMensualites, // Élèves ayant des mensualités
                'mensualites_total' => $mensualites->count(),
                'montant_total_du' => $mensualites->sum('montant_du'),
                'montant_total_paye' => $mensualites->sum('montant_paye'),
                'montant_restant' => $mensualites->sum(function($m) {
                    return $m->montant_du - $m->montant_paye;
                }),
            ];

            // Statistiques par statut
            $parStatut = [
                'complet' => [
                    'count' => $mensualites->where('statut', 'complet')->count(),
                    'montant_paye' => $mensualites->where('statut', 'complet')->sum('montant_paye')
                ],
                'partiel' => [
                    'count' => $mensualites->where('statut', 'partiel')->count(),
                    'montant_paye' => $mensualites->where('statut', 'partiel')->sum('montant_paye')
                ],
                'impaye' => [
                    'count' => $mensualites->where('statut', 'impaye')->count(),
                    'montant_du' => $mensualites->where('statut', 'impaye')->sum('montant_du')
                ]
            ];

            $totaux['par_statut'] = $parStatut;
            
            // Pourcentage de paiement
            $totaux['pourcentage_paiement'] = $totaux['montant_total_du'] > 0 
                ? round(($totaux['montant_total_paye'] / $totaux['montant_total_du']) * 100, 2)
                : 0;

            // Ajouter les détails des mensualités
            $totaux['details'] = $mensualites->map(function ($mensualite) {
                return [
                    'id' => $mensualite->id,
                    'eleve' => [
                        'nom' => $mensualite->inscription->preInscription->nom,
                        'prenom' => $mensualite->inscription->preInscription->prenom,
                        'ine' => $mensualite->inscription->preInscription->ine,
                        'classe' => $mensualite->inscription->classe->nom ?? 'N/A'
                    ],
                    'mois_paiement' => $mensualite->mois_libelle,
                    'montant_du' => $mensualite->montant_du,
                    'montant_paye' => $mensualite->montant_paye,
                    'solde_restant' => $mensualite->solde_restant,
                    'mode_paiement' => $mensualite->mode_paiement_libelle,
                    'date_paiement' => $mensualite->date_paiement?->format('d/m/Y'),
                    'statut' => $mensualite->statut,
                    'numero_recu' => $mensualite->numero_recu
                ];
            });

            return response()->json($totaux);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du chargement des rapports de mensualités'], 500);
        }
    }

    /**
     * Aperçu des rapports de mensualités avec filtres
     */
    public function apercuRapportsMensualites(Request $request): View
    {
        try {
            $annee = $request->get('annee');
            $mois = $request->get('mois');
            $dateDebut = $request->get('dateDebut');
            $dateFin = $request->get('dateFin');
            $statut = $request->get('statut');

            // Même logique que getRapportsMensualites
            $query = Mensualite::with(['inscription.preInscription', 'inscription.classe', 'anneeScolaire']);

            if ($annee) {
                $query->where('annee_scolaire_id', $annee);
            } else {
                $anneeActive = AnneeScolaire::getActive();
                if ($anneeActive) {
                    $query->where('annee_scolaire_id', $anneeActive->id);
                }
            }

            if ($mois) $query->where('mois_paiement', $mois);
            if ($statut) $query->where('statut', $statut);

            if ($dateDebut && $dateFin) {
                $query->whereBetween('date_paiement', [
                    $dateDebut . ' 00:00:00',
                    $dateFin . ' 23:59:59'
                ]);
            } elseif ($dateDebut) {
                $query->whereDate('date_paiement', '>=', $dateDebut);
            } elseif ($dateFin) {
                $query->whereDate('date_paiement', '<=', $dateFin);
            }

            $mensualites = $query->orderBy('created_at', 'desc')->get();

            if ($mensualites->isEmpty()) {
                abort(404, 'Aucune mensualité trouvée pour les critères sélectionnés');
            }

            // Calculer les totaux
            
            // Calculer le total des élèves inscrits pour l'année scolaire
            $elevesInscritQuery = Inscription::where('annee_scolaire_id', $annee ?: AnneeScolaire::getActive()?->id);
            $elevesInscritsTotal = $elevesInscritQuery->count();
            
            $totaux = [
                'eleves_total' => $elevesInscritsTotal, // Total des élèves inscrits
                'eleves_avec_mensualites' => $mensualites->unique('inscription_id')->count(), // Élèves ayant des mensualités
                'mensualites_total' => $mensualites->count(),
                'montant_total_du' => $mensualites->sum('montant_du'),
                'montant_total_paye' => $mensualites->sum('montant_paye'),
                'montant_restant' => $mensualites->sum(function($m) {
                    return $m->montant_du - $m->montant_paye;
                }),
                'pourcentage_paiement' => 0
            ];

            $totaux['pourcentage_paiement'] = $totaux['montant_total_du'] > 0 
                ? round(($totaux['montant_total_paye'] / $totaux['montant_total_du']) * 100, 2)
                : 0;

            // Informations sur les filtres pour l'affichage
            $filtres = [
                'annee' => $annee,
                'mois' => $mois,
                'dateDebut' => $dateDebut,
                'dateFin' => $dateFin,
                'statut' => $statut
            ];

            return view('mensualites.apercu_rapports', compact('mensualites', 'totaux', 'filtres'));
        } catch (\Exception $e) {
            abort(500, 'Erreur lors de la génération de l\'aperçu des rapports de mensualités');
        }
    }

    /**
     * Récupérer les années scolaires pour les filtres
     */
    public function getAnneesScolaires(): JsonResponse
    {
        try {
            $annees = AnneeScolaire::orderBy('date_debut', 'desc')->get()->map(function($annee) {
                return [
                    'valeur' => $annee->id,
                    'libelle' => $annee->libelle,
                    'actuelle' => $annee->actif
                ];
            });

            return response()->json($annees);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du chargement des années scolaires'], 500);
        }
    }

    /**
     * Créer les mensualités manquantes pour une inscription en base de données
     */
    private function creerMensualitesManquantes($inscription, $anneeActive)
    {
        // Utiliser seulement les mois autorisés par l'ENUM en base
        $moisScolaires = ['octobre', 'novembre', 'decembre', 'janvier', 'fevrier', 'mars', 'avril', 'mai', 'juin', 'juillet'];
        
        // Obtenir le montant de mensualité selon le niveau de l'élève
        $montantMensualite = $this->getMontantMensualite($inscription->niveau_id);
        
        foreach ($moisScolaires as $mois) {
            // Vérifier si la mensualité existe déjà en base
            $mensualiteExiste = Mensualite::where('inscription_id', $inscription->id)
                ->where('annee_scolaire_id', $anneeActive->id)
                ->where('mois_paiement', $mois)
                ->exists();
            
            if (!$mensualiteExiste) {
                // Créer la mensualité en base de données
                Mensualite::create([
                    'inscription_id' => $inscription->id,
                    'annee_scolaire_id' => $anneeActive->id,
                    'mois_paiement' => $mois,
                    'montant_du' => $montantMensualite,
                    'montant_paye' => 0,
                    'statut' => 'impaye',
                    'date_paiement' => null
                ]);
            }
        }
    }

    /**
     * Corriger automatiquement les mensualités avec montant_du = 0
     */
    private function corrigerMensualitesAvecMontantZero($anneeId)
    {
        // Récupérer les mensualités avec montant_du = 0
        $mensualitesACorreger = Mensualite::with('inscription.niveau')
            ->where('annee_scolaire_id', $anneeId)
            ->where('montant_du', 0)
            ->get();
        
        foreach ($mensualitesACorreger as $mensualite) {
            $montantCorrect = $this->getMontantMensualite($mensualite->inscription->niveau_id);
            
            // Mettre à jour seulement si un montant valide est trouvé
            if ($montantCorrect > 0) {
                $mensualite->update(['montant_du' => $montantCorrect]);
            }
        }
    }

}