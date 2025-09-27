<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\CategorieService;
use App\Models\DepenseService;
use App\Models\Etablissement;
use App\Models\AnneeScolaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    /**
     * Récupérer l'établissement de façon sécurisée
     */
    private function getEtablissement()
    {
        // Essayer d'abord avec l'utilisateur, sinon prendre le premier établissement
        $etablissement = Auth::user()->etablissement ?? Etablissement::first();
        
        if (!$etablissement) {
            abort(404, 'Aucun établissement trouvé');
        }
        
        return $etablissement;
    }

    /**
     * Vue principale du module services
     */
    public function index()
    {
        $etablissement = $this->getEtablissement();
        $anneeActive = AnneeScolaire::getActive();
        $anneeScolaires = AnneeScolaire::orderBy('date_debut', 'desc')->get();
        $categoriesServices = CategorieService::actif()->get();
        
        // Récupérer les services et dépenses pour l'affichage (toutes les années temporairement)
        $services = Service::with(['categorieService', 'anneeScolaire'])
            ->where('etablissement_id', $etablissement->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        $depenses = DepenseService::with(['service.categorieService', 'anneeScolaire'])
            ->where('etablissement_id', $etablissement->id)
            ->orderBy('date_depense', 'desc')
            ->get();

        return view('services.index', compact('etablissement', 'anneeActive', 'anneeScolaires', 'categoriesServices', 'services', 'depenses'));
    }

    /**
     * Obtenir la liste des services
     */
    public function getServices(Request $request)
    {
        $etablissement = $this->getEtablissement();
        
        $query = Service::with(['categorieService', 'anneeScolaire'])
            ->where('etablissement_id', $etablissement->id);

        // Filtres
        if ($request->annee_scolaire_id) {
            $query->where('annee_scolaire_id', $request->annee_scolaire_id);
        }
        
        if ($request->categorie_id) {
            $query->where('categorie_service_id', $request->categorie_id);
        }
        
        if ($request->statut) {
            $query->where('statut', $request->statut);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->search . '%')
                  ->orWhere('fournisseur', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $services = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($services);
    }

    /**
     * Créer un nouveau service
     */
    public function storeService(Request $request)
    {
        $etablissement = $this->getEtablissement();

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'categorie_service_id' => 'required|exists:categories_services,id',
            'description' => 'nullable|string',
            'fournisseur' => 'nullable|string|max:255',
            'date_acquisition' => 'nullable|date',
            'statut' => 'required|in:actif,inactif,en_maintenance',
            'remarques' => 'nullable|string',
            'annee_scolaire_id' => 'nullable|exists:annee_scolaires,id'
        ]);

        $validated['etablissement_id'] = $etablissement->id;
        
        // Utiliser l'année scolaire active si non spécifiée
        if (!$validated['annee_scolaire_id']) {
            $validated['annee_scolaire_id'] = AnneeScolaire::getActive()->id;
        }

        $service = Service::create($validated);

        return response()->json([
            'services' => $services,
            'total' => $services->count()
        ]);
    }

    /**
     * Mettre à jour un service
     */
    public function updateService(Request $request, Service $service)
    {
        $etablissement = $this->getEtablissement();
        
        // Vérifier que le service appartient à l'établissement
        if ($service->etablissement_id !== $etablissement->id) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'categorie_service_id' => 'required|exists:categories_services,id',
            'description' => 'nullable|string',
            'fournisseur' => 'nullable|string|max:255',
            'date_acquisition' => 'nullable|date',
            'statut' => 'required|in:actif,inactif,en_maintenance',
            'remarques' => 'nullable|string',
            'annee_scolaire_id' => 'required|exists:annee_scolaires,id'
        ]);

        $service->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Service modifié avec succès',
            'service' => $service->load(['categorieService', 'anneeScolaire'])
        ]);
    }

    /**
     * Supprimer un service
     */
    public function destroyService(Service $service)
    {
        $etablissement = $this->getEtablissement();
        
        // Vérifier que le service appartient à l'établissement
        if ($service->etablissement_id !== $etablissement->id) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service supprimé avec succès'
        ]);
    }

    /**
     * Obtenir la liste des dépenses de services
     */
    public function getDepenses(Request $request)
    {
        $etablissement = $this->getEtablissement();
        
        $query = DepenseService::with(['service.categorieService', 'anneeScolaire'])
            ->where('etablissement_id', $etablissement->id);

        // Filtres
        if ($request->annee_scolaire_id) {
            $query->where('annee_scolaire_id', $request->annee_scolaire_id);
        }
        
        if ($request->service_id) {
            $query->where('service_id', $request->service_id);
        }
        
        if ($request->type_depense) {
            $query->where('type_depense', $request->type_depense);
        }

        if ($request->date_debut && $request->date_fin) {
            $query->whereBetween('date_depense', [$request->date_debut, $request->date_fin]);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%')
                  ->orWhere('numero_facture', 'like', '%' . $request->search . '%')
                  ->orWhereHas('service', function($sq) use ($request) {
                      $sq->where('nom', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $depenses = $query->orderBy('date_depense', 'desc')->paginate(15);

        return response()->json($depenses);
    }

    /**
     * Créer une nouvelle dépense
     */
    public function storeDepense(Request $request)
    {
        $etablissement = $this->getEtablissement();

        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'montant' => 'required|numeric|min:0',
            'date_depense' => 'required|date',
            'type_depense' => 'required|in:achat,maintenance,location,reparation,consommation,autre',
            'numero_facture' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'remarques' => 'nullable|string',
            'annee_scolaire_id' => 'nullable|exists:annee_scolaires,id'
        ]);

        $validated['etablissement_id'] = $etablissement->id;
        
        // Utiliser l'année scolaire active si non spécifiée
        if (!$validated['annee_scolaire_id']) {
            $validated['annee_scolaire_id'] = AnneeScolaire::getActive()->id;
        }

        $depense = DepenseService::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Dépense ajoutée avec succès',
            'depense' => $depense->load(['service.categorieService', 'anneeScolaire'])
        ]);
    }

    /**
     * Mettre à jour une dépense
     */
    public function updateDepense(Request $request, DepenseService $depense)
    {
        $etablissement = $this->getEtablissement();
        
        // Vérifier que la dépense appartient à l'établissement
        if ($depense->etablissement_id !== $etablissement->id) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'montant' => 'required|numeric|min:0',
            'date_depense' => 'required|date',
            'type_depense' => 'required|in:achat,maintenance,location,reparation,consommation,autre',
            'numero_facture' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'remarques' => 'nullable|string',
            'annee_scolaire_id' => 'required|exists:annee_scolaires,id'
        ]);

        $depense->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Dépense modifiée avec succès',
            'depense' => $depense->load(['service.categorieService', 'anneeScolaire'])
        ]);
    }

    /**
     * Supprimer une dépense
     */
    public function destroyDepense(DepenseService $depense)
    {
        $etablissement = $this->getEtablissement();
        
        // Vérifier que la dépense appartient à l'établissement
        if ($depense->etablissement_id !== $etablissement->id) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $depense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dépense supprimée avec succès'
        ]);
    }

    /**
     * Obtenir les services pour un select
     */
    public function getServicesForSelect(Request $request)
    {
        $etablissement = $this->getEtablissement();
        
        $services = Service::where('etablissement_id', $etablissement->id)
            ->where('statut', 'actif');
            
        if ($request->annee_scolaire_id) {
            $services->where('annee_scolaire_id', $request->annee_scolaire_id);
        }
        
        return response()->json($services->get(['id', 'nom']));
    }

    /**
     * Générer la fiche de dépense pour impression
     */
    public function ficheDepense(DepenseService $depense)
    {
        $etablissement = $this->getEtablissement();
        
        // Vérifier que la dépense appartient à l'établissement
        if ($depense->etablissement_id !== $etablissement->id) {
            abort(403, 'Accès non autorisé');
        }

        // Charger les relations
        $depense->load(['service.categorieService', 'anneeScolaire']);

        return view('services.fiche-depense', compact('depense', 'etablissement'));
    }

    /**
     * Filtrer les dépenses selon les critères
     */
    public function filtrerDepenses(Request $request)
    {
        $etablissement = $this->getEtablissement();
        
        $query = DepenseService::with(['service.categorieService', 'anneeScolaire'])
            ->where('etablissement_id', $etablissement->id);

        // Filtres
        if ($request->annee_scolaire_id) {
            $query->where('annee_scolaire_id', $request->annee_scolaire_id);
        }
        
        if ($request->mois) {
            $query->whereMonth('date_depense', $request->mois);
        }
        
        if ($request->service_id) {
            $query->where('service_id', $request->service_id);
        }
        
        if ($request->categorie_id) {
            $query->whereHas('service', function($q) use ($request) {
                $q->where('categorie_service_id', $request->categorie_id);
            });
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%')
                  ->orWhere('numero_facture', 'like', '%' . $request->search . '%')
                  ->orWhere('type_depense', 'like', '%' . $request->search . '%')
                  ->orWhereHas('service', function($sq) use ($request) {
                      $sq->where('nom', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $depenses = $query->orderBy('date_depense', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'depenses' => $depenses,
            'total' => $depenses->count(),
            'montant_total' => $depenses->sum('montant')
        ]);
    }

    /**
     * Générer un rapport selon les critères
     */
    public function genererRapport(Request $request)
    {
        $etablissement = $this->getEtablissement();
        
        $query = DepenseService::with(['service.categorieService', 'anneeScolaire'])
            ->where('etablissement_id', $etablissement->id);

        // Appliquer les mêmes filtres que pour les dépenses
        if ($request->annee_scolaire_id) {
            $query->where('annee_scolaire_id', $request->annee_scolaire_id);
        }
        
        if ($request->mois) {
            $query->whereMonth('date_depense', $request->mois);
        }
        
        if ($request->service_id) {
            $query->where('service_id', $request->service_id);
        }
        
        if ($request->categorie_id) {
            $query->whereHas('service', function($q) use ($request) {
                $q->where('categorie_service_id', $request->categorie_id);
            });
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%')
                  ->orWhere('numero_facture', 'like', '%' . $request->search . '%')
                  ->orWhere('type_depense', 'like', '%' . $request->search . '%')
                  ->orWhereHas('service', function($sq) use ($request) {
                      $sq->where('nom', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $depenses = $query->orderBy('date_depense', 'desc')->get();
        
        // Statistiques
        $stats = [
            'total_depenses' => $depenses->count(),
            'montant_total' => $depenses->sum('montant'),
            'moyenne_depense' => $depenses->count() > 0 ? $depenses->avg('montant') : 0,
            'par_type' => $depenses->groupBy('type_depense')->map(function($items) {
                return [
                    'count' => $items->count(),
                    'montant' => $items->sum('montant')
                ];
            }),
            'par_service' => $depenses->groupBy('service.nom')->map(function($items) {
                return [
                    'count' => $items->count(),
                    'montant' => $items->sum('montant')
                ];
            }),
            'par_mois' => $depenses->groupBy(function($item) {
                return \Carbon\Carbon::parse($item->date_depense)->format('Y-m');
            })->map(function($items) {
                return [
                    'count' => $items->count(),
                    'montant' => $items->sum('montant')
                ];
            })
        ];
        
        return response()->json([
            'success' => true,
            'depenses' => $depenses,
            'statistiques' => $stats
        ]);
    }

    /**
     * Exporter les dépenses filtrées en PDF
     */
    public function exporterDepenses(Request $request)
    {
        $etablissement = $this->getEtablissement();
        
        $query = DepenseService::with(['service.categorieService', 'anneeScolaire'])
            ->where('etablissement_id', $etablissement->id);

        // Appliquer les filtres
        if ($request->annee_scolaire_id) {
            $query->where('annee_scolaire_id', $request->annee_scolaire_id);
        }
        
        if ($request->mois) {
            $query->whereMonth('date_depense', $request->mois);
        }
        
        if ($request->service_id) {
            $query->where('service_id', $request->service_id);
        }
        
        if ($request->categorie_id) {
            $query->whereHas('service', function($q) use ($request) {
                $q->where('categorie_service_id', $request->categorie_id);
            });
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%')
                  ->orWhere('numero_facture', 'like', '%' . $request->search . '%')
                  ->orWhere('type_depense', 'like', '%' . $request->search . '%')
                  ->orWhereHas('service', function($sq) use ($request) {
                      $sq->where('nom', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $depenses = $query->orderBy('date_depense', 'desc')->get();
        
        // Informations sur les filtres appliqués
        $filtres = [
            'annee' => $request->annee_scolaire_id ? AnneeScolaire::find($request->annee_scolaire_id)->libelle : 'Toutes',
            'mois' => $request->mois ? date('F', mktime(0, 0, 0, $request->mois, 1)) : 'Tous',
            'service' => $request->service_id ? Service::find($request->service_id)->nom : 'Tous',
            'categorie' => $request->categorie_id ? CategorieService::find($request->categorie_id)->nom : 'Toutes',
            'recherche' => $request->search ?: 'Aucune'
        ];

        return view('services.export-depenses', compact('depenses', 'etablissement', 'filtres'));
    }

    /**
     * Aperçu des dépenses filtrées
     */
    public function apercuDepenses(Request $request)
    {
        return $this->exporterDepenses($request);
    }

    /**
     * Exporter le rapport en PDF
     */
    public function exporterRapportPDF(Request $request)
    {
        $etablissement = $this->getEtablissement();
        
        $query = DepenseService::with(['service.categorieService', 'anneeScolaire'])
            ->where('etablissement_id', $etablissement->id);

        // Appliquer les filtres (même logique que genererRapport)
        if ($request->annee_scolaire_id) {
            $query->where('annee_scolaire_id', $request->annee_scolaire_id);
        }
        
        if ($request->mois) {
            $query->whereMonth('date_depense', $request->mois);
        }
        
        if ($request->service_id) {
            $query->where('service_id', $request->service_id);
        }
        
        if ($request->categorie_id) {
            $query->whereHas('service', function($q) use ($request) {
                $q->where('categorie_service_id', $request->categorie_id);
            });
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%')
                  ->orWhere('numero_facture', 'like', '%' . $request->search . '%')
                  ->orWhere('type_depense', 'like', '%' . $request->search . '%')
                  ->orWhereHas('service', function($sq) use ($request) {
                      $sq->where('nom', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $depenses = $query->orderBy('date_depense', 'desc')->get();
        
        // Statistiques complètes
        $stats = [
            'total_depenses' => $depenses->count(),
            'montant_total' => $depenses->sum('montant'),
            'moyenne_depense' => $depenses->count() > 0 ? $depenses->avg('montant') : 0,
            'par_type' => $depenses->groupBy('type_depense')->map(function($items) {
                return [
                    'count' => $items->count(),
                    'montant' => $items->sum('montant')
                ];
            }),
            'par_service' => $depenses->groupBy('service.nom')->map(function($items) {
                return [
                    'count' => $items->count(),
                    'montant' => $items->sum('montant')
                ];
            }),
            'par_mois' => $depenses->groupBy(function($item) {
                return \Carbon\Carbon::parse($item->date_depense)->format('Y-m');
            })->map(function($items) {
                return [
                    'count' => $items->count(),
                    'montant' => $items->sum('montant')
                ];
            })
        ];

        // Informations sur les filtres appliqués
        $filtres = [
            'annee' => $request->annee_scolaire_id ? AnneeScolaire::find($request->annee_scolaire_id)->libelle : 'Toutes',
            'mois' => $request->mois ? date('F', mktime(0, 0, 0, $request->mois, 1)) : 'Tous',
            'service' => $request->service_id ? Service::find($request->service_id)->nom : 'Tous',
            'categorie' => $request->categorie_id ? CategorieService::find($request->categorie_id)->nom : 'Toutes',
            'recherche' => $request->search ?: 'Aucune'
        ];

        return view('services.export-rapport', compact('depenses', 'etablissement', 'filtres', 'stats'));
    }
}
