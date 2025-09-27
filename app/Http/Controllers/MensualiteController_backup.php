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
        
        // Variables par défaut
        $selectedEleve = null;
        $mensualites = [];

        // Variables spécifiques à chaque onglet
        if ($activeTab === 'paiements' && $request->eleve_id) {
            // Charger les mensualités pour l'élève sélectionné
            try {
                $selectedEleve = Inscription::with(['preInscription', 'classe', 'anneeScolaire'])
                    ->findOrFail($request->eleve_id);
                    
                $mensualites = Mensualite::with(['inscription.preInscription', 'anneeScolaire'])
                    ->where('inscription_id', $selectedEleve->id)
                    ->where('annee_scolaire_id', $anneeActive->id)
                    ->orderBy('mois_paiement')
                    ->get();
            } catch (\Exception $e) {
                $selectedEleve = null;
                $mensualites = [];
            }
        }

        // Variables pour l'onglet HISTORIQUE
        $classes = \App\Models\Classe::orderBy('nom')->get();
        $anneesScolaires = \App\Models\AnneeScolaire::orderBy('libelle', 'desc')->get();
        
        // Query simple pour l'historique des mensualités
        $query = Mensualite::with(['inscription.preInscription', 'inscription.classe', 'anneeScolaire'])
            ->where('annee_scolaire_id', $anneeActive->id);

        // Appliquer les filtres si on est sur l'onglet historique
        if ($activeTab === 'historique') {
            if ($request->filter_classe) {
                $query->whereHas('inscription', function($q) use ($request) {
                    $q->where('classe_id', $request->filter_classe);
                });
            }

            if ($request->filter_eleve) {
                $query->where('inscription_id', $request->filter_eleve);
            }

            if ($request->filter_mois) {
                $query->where('mois_paiement', $request->filter_mois);
            }

            if ($request->filter_statut) {
                $query->where('statut', $request->filter_statut);
            }

            if ($request->filter_periode) {
                $days = (int) $request->filter_periode;
                $query->where('date_paiement', '>=', now()->subDays($days));
            }
        }

        // Ordonner et paginer
        $query->orderBy('date_paiement', 'desc')->orderBy('created_at', 'desc');
        $historiqueCount = $query->count();
        $perPage = $activeTab === 'historique' ? 10 : 5;
        $historiquePaiements = $query->paginate($perPage);
        $historiquePaiements->appends($request->query());

        // Variables pour l'onglet TABLEAU DE BORD
        $dashboardStats = Cache::remember(
            'mensualites_dashboard_stats_' . $anneeActive->id, 
            300,
            function () use ($anneeActive) {
                return $this->getDashboardStats($anneeActive);
            }
        );

        // Variables pour l'onglet REÇUS & RAPPORTS
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
        
        $eleves = Inscription::with(['preInscription', 'classe'])
            ->where('annee_scolaire_id', $anneeActive->id)
            ->whereHas('preInscription', function($query) use ($search) {
                $query->where('nom_eleve', 'LIKE', "%{$search}%")
                      ->orWhere('prenom_eleve', 'LIKE', "%{$search}%")
                      ->orWhere('ine', 'LIKE', "%{$search}%");
            })
            ->limit(10)
            ->get();

        return response()->json($eleves->map(function($eleve) {
            return [
                'id' => $eleve->id,
                'text' => $eleve->preInscription->nom_eleve . ' ' . $eleve->preInscription->prenom_eleve . 
                         ' - ' . $eleve->classe->nom . ' (' . $eleve->preInscription->ine . ')',
                'nom' => $eleve->preInscription->nom_eleve,
                'prenom' => $eleve->preInscription->prenom_eleve,
                'classe' => $eleve->classe->nom,
                'ine' => $eleve->preInscription->ine
            ];
        }));
    }

    /**
     * Statistiques pour le tableau de bord
     */
    private function getDashboardStats($anneeActive)
    {
        // Compter tous les élèves inscrits
        $totalEleves = Inscription::where('annee_scolaire_id', $anneeActive->id)->count();
        
        // Compter les paiements par statut
        $paiementsComplets = Mensualite::where('annee_scolaire_id', $anneeActive->id)
            ->where('statut', 'complet')->count();
            
        $paiementsPartiels = Mensualite::where('annee_scolaire_id', $anneeActive->id)
            ->where('statut', 'partiel')->count();
            
        $paiementsImpayes = Mensualite::where('annee_scolaire_id', $anneeActive->id)
            ->where('statut', 'impaye')->count();

        // Montants
        $montantTotal = Mensualite::where('annee_scolaire_id', $anneeActive->id)->sum('montant_du');
        $montantPaye = Mensualite::where('annee_scolaire_id', $anneeActive->id)->sum('montant_paye');
        $montantRestant = $montantTotal - $montantPaye;

        return [
            'totalEleves' => $totalEleves,
            'paiementsComplets' => $paiementsComplets,
            'paiementsPartiels' => $paiementsPartiels,
            'paiementsImpayes' => $paiementsImpayes,
            'montantTotal' => $montantTotal,
            'montantPaye' => $montantPaye,
            'montantRestant' => $montantRestant,
            'tauxRecouvrement' => $montantTotal > 0 ? round(($montantPaye / $montantTotal) * 100, 2) : 0
        ];
    }

    /**
     * Données pour l'onglet rapports
     */
    private function getRapportsData($anneeActive, $request)
    {
        // Statistiques de base
        $totalEleves = Inscription::where('annee_scolaire_id', $anneeActive->id)->count();
        $totalMensualites = Mensualite::where('annee_scolaire_id', $anneeActive->id)->count();
        $montantTotal = Mensualite::where('annee_scolaire_id', $anneeActive->id)->sum('montant_du');
        $montantPaye = Mensualite::where('annee_scolaire_id', $anneeActive->id)->sum('montant_paye');

        return [
            'totalEleves' => $totalEleves,
            'totalMensualites' => $totalMensualites,
            'montantTotal' => $montantTotal,
            'montantPaye' => $montantPaye,
            'montantRestant' => $montantTotal - $montantPaye,
            'tauxRecouvrement' => $montantTotal > 0 ? round(($montantPaye / $montantTotal) * 100, 2) : 0
        ];
    }

    /**
     * API pour les rapports
     */
    public function getRapportsMensualites(Request $request): JsonResponse
    {
        $anneeActive = AnneeScolaire::getActive();
        $query = Mensualite::with(['inscription.preInscription', 'inscription.classe', 'anneeScolaire'])
            ->where('annee_scolaire_id', $anneeActive->id);

        // Filtres
        if ($request->classe_id) {
            $query->whereHas('inscription', function($q) use ($request) {
                $q->where('classe_id', $request->classe_id);
            });
        }

        if ($request->statut) {
            $query->where('statut', $request->statut);
        }

        if ($request->mois) {
            $query->where('mois_paiement', $request->mois);
        }

        $mensualites = $query->orderBy('created_at', 'desc')->get();

        // Statistiques
        $stats = [
            'total' => $mensualites->count(),
            'montantTotal' => $mensualites->sum('montant_du'),
            'montantPaye' => $mensualites->sum('montant_paye'),
            'montantRestant' => $mensualites->sum('montant_du') - $mensualites->sum('montant_paye')
        ];

        return response()->json([
            'mensualites' => $mensualites,
            'statistiques' => $stats
        ]);
    }

    /**
     * Aperçu des rapports pour impression
     */
    public function apercuRapportsMensualites(Request $request): View
    {
        $response = $this->getRapportsMensualites($request);
        $data = json_decode($response->getContent(), true);

        return view('mensualites.apercu_rapports', [
            'mensualites' => collect($data['mensualites']),
            'statistiques' => $data['statistiques'],
            'filtres' => $request->only(['classe_id', 'statut', 'mois']),
            'dateGeneration' => now()->format('d/m/Y à H:i')
        ]);
    }

    /**
     * Obtenir les années scolaires
     */
    public function getAnneesScolaires(): JsonResponse
    {
        $annees = AnneeScolaire::orderBy('libelle', 'desc')->get();
        return response()->json($annees);
    }
}