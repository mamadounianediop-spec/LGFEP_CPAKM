<?php

namespace App\Http\Controllers;

use App\Models\PreInscription;
use App\Models\Inscription;
use App\Models\Mensualite;
use App\Models\Personnel;
use App\Models\Service;
use App\Models\DepenseService;
use App\Models\AnneeScolaire;
use App\Models\Etablissement;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard avec statistiques dynamiques.
     */
    public function index(): View
    {
        $anneeActive = AnneeScolaire::getActive();
        $etablissement = Etablissement::first();
        
        // Si aucune année n'est active, rediriger vers paramètres
        if (!$anneeActive) {
            return redirect()->route('parametres.index')
                ->with('error', 'Aucune année scolaire n\'est activée. Veuillez activer une année scolaire.');
        }

        // 📊 STATISTIQUES PRINCIPALES
        $stats = $this->getStatistiquesPrincipales($anneeActive);
        
        // 📈 DONNÉES POUR GRAPHIQUES
        $chartData = $this->getChartData($anneeActive);
        
        // 🔄 ACTIVITÉS RÉCENTES
        $activitesRecentes = $this->getActivitesRecentes($anneeActive);
        
        // ⚠️ ALERTES ET NOTIFICATIONS
        $alertes = $this->getAlertes($anneeActive);
        
        // 📅 DONNÉES TEMPORELLES
        $donneesTemporelles = $this->getDonneesTemporelles($anneeActive);

        return view('dashboard', compact(
            'anneeActive',
            'etablissement', 
            'stats',
            'chartData',
            'activitesRecentes',
            'alertes',
            'donneesTemporelles'
        ));
    }
    
    /**
     * Récupérer les statistiques principales
     */
    private function getStatistiquesPrincipales($anneeActive)
    {
        // INSCRIPTIONS
        $totalPreInscriptions = PreInscription::where('annee_scolaire_id', $anneeActive->id)->count();
        $totalInscriptions = Inscription::where('annee_scolaire_id', $anneeActive->id)->count();
        $inscriptionsActives = Inscription::where('annee_scolaire_id', $anneeActive->id)
            ->where('statut', 'actif')->count();
        
        // FINANCIER - Mensualités
        $totalMensualites = Mensualite::where('annee_scolaire_id', $anneeActive->id)->sum('montant_du');
        $montantPaye = Mensualite::where('annee_scolaire_id', $anneeActive->id)->sum('montant_paye');
        $montantImpaye = $totalMensualites - $montantPaye;
        
        // Recettes du mois en cours
        $recettesMoisCourant = Mensualite::where('annee_scolaire_id', $anneeActive->id)
            ->where('mois_paiement', strtolower(Carbon::now()->locale('fr')->isoFormat('MMMM')))
            ->sum('montant_paye');
            
        // PERSONNEL
        $totalPersonnel = Personnel::where('statut', 'actif')->count();
        $personnelParType = Personnel::where('statut', 'actif')
            ->select('type_personnel', DB::raw('count(*) as total'))
            ->groupBy('type_personnel')
            ->pluck('total', 'type_personnel')
            ->toArray();
            
        // SERVICES ET DÉPENSES
        $totalServices = Service::where('annee_scolaire_id', $anneeActive->id)->count();
        $servicesActifs = Service::where('annee_scolaire_id', $anneeActive->id)
            ->where('statut', 'actif')->count();
        $totalDepensesServices = DepenseService::where('annee_scolaire_id', $anneeActive->id)->sum('montant');
        
        return [
            // Inscriptions
            'total_pre_inscriptions' => $totalPreInscriptions,
            'total_inscriptions' => $totalInscriptions,
            'inscriptions_actives' => $inscriptionsActives,
            'taux_conversion' => $totalPreInscriptions > 0 ? round(($totalInscriptions / $totalPreInscriptions) * 100, 1) : 0,
            
            // Financier
            'total_mensualites' => $totalMensualites,
            'montant_paye' => $montantPaye,
            'montant_impaye' => $montantImpaye,
            'recettes_mois_courant' => $recettesMoisCourant,
            'taux_paiement' => $totalMensualites > 0 ? round(($montantPaye / $totalMensualites) * 100, 1) : 0,
            
            // Personnel
            'total_personnel' => $totalPersonnel,
            'personnel_par_type' => $personnelParType,
            
            // Services
            'total_services' => $totalServices,
            'services_actifs' => $servicesActifs,
            'total_depenses_services' => $totalDepensesServices,
        ];
    }
    
    /**
     * Récupérer les données pour graphiques
     */
    private function getChartData($anneeActive)
    {
        // Évolution des recettes par mois (année scolaire)
        $recettesParMois = Mensualite::where('annee_scolaire_id', $anneeActive->id)
            ->select('mois_paiement', DB::raw('sum(montant_paye) as montant'))
            ->groupBy('mois_paiement')
            ->orderByRaw("FIELD(mois_paiement, 'octobre', 'novembre', 'decembre', 'janvier', 'fevrier', 'mars', 'avril', 'mai', 'juin', 'juillet')")
            ->get();
            
        // Répartition des paiements par statut
        $paiementsParStatut = Mensualite::where('annee_scolaire_id', $anneeActive->id)
            ->select('statut', DB::raw('count(*) as total'))
            ->groupBy('statut')
            ->get();
            
        // Évolution des inscriptions (par mois civil)
        $inscriptionsParMois = Inscription::where('annee_scolaire_id', $anneeActive->id)
            ->select(DB::raw('MONTH(date_inscription) as mois'), DB::raw('count(*) as total'))
            ->groupBy(DB::raw('MONTH(date_inscription)'))
            ->orderBy('mois')
            ->get();
            
        // Dépenses de services par catégorie
        $depensesParCategorie = DepenseService::join('services', 'depenses_services.service_id', '=', 'services.id')
            ->join('categories_services', 'services.categorie_service_id', '=', 'categories_services.id')
            ->where('depenses_services.annee_scolaire_id', $anneeActive->id)
            ->select('categories_services.nom', DB::raw('sum(depenses_services.montant) as montant'))
            ->groupBy('categories_services.nom')
            ->get();
            
        return [
            'recettes_par_mois' => $recettesParMois,
            'paiements_par_statut' => $paiementsParStatut,
            'inscriptions_par_mois' => $inscriptionsParMois,
            'depenses_par_categorie' => $depensesParCategorie,
        ];
    }
    
    /**
     * Récupérer les activités récentes
     */
    private function getActivitesRecentes($anneeActive)
    {
        $activites = collect();
        
        // Dernières inscriptions (5 plus récentes)
        $dernieresInscriptions = Inscription::with('preInscription')
            ->where('annee_scolaire_id', $anneeActive->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        foreach ($dernieresInscriptions as $inscription) {
            $activites->push([
                'type' => 'inscription',
                'icon' => 'fas fa-user-plus',
                'color' => 'blue',
                'message' => 'Nouvelle inscription: ' . $inscription->preInscription->nom . ' ' . $inscription->preInscription->prenom,
                'date' => $inscription->created_at,
                'url' => route('inscriptions.index', ['tab' => 'liste-eleves'])
            ]);
        }
        
        // Derniers paiements (5 plus récents)
        $derniersPaiements = Mensualite::with('inscription.preInscription')
            ->where('annee_scolaire_id', $anneeActive->id)
            ->where('statut', 'complet')
            ->orderBy('date_paiement', 'desc')
            ->limit(5)
            ->get();
            
        foreach ($derniersPaiements as $paiement) {
            $activites->push([
                'type' => 'paiement',
                'icon' => 'fas fa-money-check',
                'color' => 'green',
                'message' => 'Paiement reçu de ' . $paiement->inscription->preInscription->nom . ' ' . $paiement->inscription->preInscription->prenom . ' (' . number_format($paiement->montant_paye, 0, ',', ' ') . ' FCFA)',
                'date' => $paiement->date_paiement,
                'url' => route('mensualites.index', ['tab' => 'historique'])
            ]);
        }
        
        // Dernières dépenses de services (3 plus récentes)
        $dernieresDepenses = DepenseService::with('service')
            ->where('annee_scolaire_id', $anneeActive->id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
            
        foreach ($dernieresDepenses as $depense) {
            $activites->push([
                'type' => 'depense',
                'icon' => 'fas fa-receipt',
                'color' => 'orange',
                'message' => 'Dépense enregistrée: ' . $depense->service->nom . ' (' . number_format($depense->montant, 0, ',', ' ') . ' FCFA)',
                'date' => $depense->created_at,
                'url' => route('services.index', ['tab' => 'depenses'])
            ]);
        }
        
        // Trier par date décroissante et garder les 10 plus récentes
        return $activites->sortByDesc('date')->take(10);
    }
    
    /**
     * Récupérer les alertes et notifications
     */
    private function getAlertes($anneeActive)
    {
        $alertes = [];
        
        // Mensualités en retard (> 15 jours)
        $mensualitesEnRetard = Mensualite::where('annee_scolaire_id', $anneeActive->id)
            ->where('statut', 'impaye')
            ->where('created_at', '<', Carbon::now()->subDays(15))
            ->count();
            
        if ($mensualitesEnRetard > 0) {
            $alertes[] = [
                'type' => 'warning',
                'icon' => 'fas fa-exclamation-triangle',
                'message' => $mensualitesEnRetard . ' mensualité(s) en retard (>15 jours)',
                'url' => route('mensualites.index', ['tab' => 'historique'])
            ];
        }
        
        // Services en maintenance
        $servicesEnMaintenance = Service::where('annee_scolaire_id', $anneeActive->id)
            ->where('statut', 'en_maintenance')
            ->count();
            
        if ($servicesEnMaintenance > 0) {
            $alertes[] = [
                'type' => 'info',
                'icon' => 'fas fa-wrench',
                'message' => $servicesEnMaintenance . ' service(s) en maintenance',
                'url' => route('services.index')
            ];
        }
        
        // Pré-inscriptions non traitées (> 7 jours)
        $preInscriptionsEnAttente = PreInscription::where('annee_scolaire_id', $anneeActive->id)
            ->whereDoesntHave('inscription')
            ->where('created_at', '<', Carbon::now()->subDays(7))
            ->count();
            
        if ($preInscriptionsEnAttente > 0) {
            $alertes[] = [
                'type' => 'warning',
                'icon' => 'fas fa-clock',
                'message' => $preInscriptionsEnAttente . ' pré-inscription(s) en attente (>7 jours)',
                'url' => route('inscriptions.index')
            ];
        }
        
        return $alertes;
    }
    
    /**
     * Récupérer les données temporelles
     */
    private function getDonneesTemporelles($anneeActive)
    {
        return [
            'mois_actuel' => Carbon::now()->locale('fr')->isoFormat('MMMM YYYY'),
            'mois_scolaire_actuel' => $this->getMoisScolaireActuel(),
            'progression_annee' => $this->getProgressionAnnee($anneeActive),
        ];
    }
    
    /**
     * Déterminer le mois scolaire actuel
     */
    private function getMoisScolaireActuel()
    {
        $moisActuel = Carbon::now()->month;
        $moisScolaires = [
            10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars',
            4 => 'Avril', 5 => 'Mai', 6 => 'Juin', 7 => 'Juillet'
        ];
        
        return $moisScolaires[$moisActuel] ?? 'Vacances';
    }
    
    /**
     * Calculer la progression de l'année scolaire
     */
    private function getProgressionAnnee($anneeActive)
    {
        $debut = Carbon::parse($anneeActive->date_debut);
        $fin = Carbon::parse($anneeActive->date_fin);
        $maintenant = Carbon::now();
        
        if ($maintenant->lt($debut)) {
            return 0;
        } elseif ($maintenant->gt($fin)) {
            return 100;
        }
        
        $dureeTotal = $debut->diffInDays($fin);
        $dureeEcoulee = $debut->diffInDays($maintenant);
        
        return round(($dureeEcoulee / $dureeTotal) * 100, 1);
    }
}