<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use App\Models\EtatPaiementMensuel;
use App\Models\AnneeScolaire;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class PersonnelController extends Controller
{
    /**
     * Obtenir l'année scolaire actuelle depuis le module paramètres
     */
    private function getAnneeScolaireActuelle()
    {
        $anneeActive = AnneeScolaire::getActive();
        
        if ($anneeActive) {
            // Extraire l'année de début du libellé (ex: "2025-2026" -> 2025)
            $libelle = $anneeActive->libelle;
            if (preg_match('/^(\d{4})-(\d{4})$/', $libelle, $matches)) {
                return (int) $matches[1];
            }
        }
        
        // Fallback vers le calcul automatique si aucune année active
        $moisActuel = date('n');
        $anneeActuelle = date('Y');
        
        if ($moisActuel >= 10) {
            return $anneeActuelle;
        } else {
            return $anneeActuelle - 1;
        }
    }

    /**
     * Obtenir le libellé de l'année scolaire
     */
    private function getLibelleAnneeScolaire($anneeDebut)
    {
        return $anneeDebut . '-' . ($anneeDebut + 1);
    }

    /**
     * Obtenir les années scolaires disponibles
     */
    private function getAnneesScolairesDisponibles()
    {
        $anneeActuelle = $this->getAnneeScolaireActuelle();
        return [
            $anneeActuelle - 1,
            $anneeActuelle,
            $anneeActuelle + 1
        ];
    }

    /**
     * Afficher la liste du personnel
     */
    public function index(Request $request): View
    {
        $activeTab = $request->get('tab', 'gestion-personnel');

        // Récupérer tous les personnels groupés par type
        $personnelParType = [
            'directeur' => Personnel::parType('directeur')->get(),
            'surveillant' => Personnel::parType('surveillant')->get(),
            'secretaire' => Personnel::parType('secretaire')->get(),
            'enseignant' => Personnel::parType('enseignant')->get(),
            'gardien' => Personnel::parType('gardien')->get(),
        ];

        // Tous les personnels pour le tableau unifié
        $allPersonnels = Personnel::orderBy('nom')->orderBy('prenom')->get();

        // Statistiques rapides
        $stats = [
            'total_personnel' => Personnel::count(),
            'directeurs_count' => $personnelParType['directeur']->count(),
            'surveillants_count' => $personnelParType['surveillant']->count(),
            'secretaires_count' => $personnelParType['secretaire']->count(),
            'enseignants_count' => $personnelParType['enseignant']->count(),
            'gardiens_count' => $personnelParType['gardien']->count(),
        ];

        // Données pour le module de rapports
        $anneesScolaires = AnneeScolaire::orderBy('libelle')->get();
        $etablissements = \App\Models\Etablissement::orderBy('nom')->get();

        return view('personnel.index', compact(
            'activeTab',
            'personnelParType',
            'allPersonnels',
            'stats',
            'anneesScolaires',
            'etablissements'
        ));
    }

    /**
     * Créer un nouveau personnel
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'telephone' => 'required|string|max:20',
                'adresse' => 'nullable|string|max:500',
                'cni' => 'nullable|string|max:50',
                'type_personnel' => 'required|in:directeur,surveillant,secretaire,enseignant,gardien',
                'discipline' => 'nullable|string|max:255',
                'statut' => 'required|in:actif,suspendu,conge',
                'date_embauche' => 'nullable|date',
                'mode_paiement' => 'required|in:fixe,heure',
                'montant_fixe' => 'required_if:mode_paiement,fixe|nullable|numeric|min:0',
                'tarif_heure' => 'required_if:mode_paiement,heure|nullable|numeric|min:0'
            ]);

            Personnel::create($validated);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Personnel ajouté avec succès']);
            }

            return redirect()->route('personnel.index', ['tab' => 'gestion-personnel'])
                ->with('success', 'Personnel ajouté avec succès.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors de l\'ajout'], 500);
            }
            return redirect()->back()->with('error', 'Erreur lors de l\'ajout du personnel');
        }
    }

    /**
     * Afficher les données d'un personnel pour édition
     */
    public function edit(Personnel $personnel)
    {
        if (request()->expectsJson()) {
            return response()->json($personnel);
        }
        
        return view('personnel.edit', compact('personnel'));
    }

    /**
     * Mettre à jour un personnel
     */
    public function update(Request $request, Personnel $personnel)
    {
        try {
            $validated = $request->validate([
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'telephone' => 'required|string|max:20',
                'adresse' => 'nullable|string|max:500',
                'cni' => 'nullable|string|max:50',
                'type_personnel' => 'required|in:directeur,surveillant,secretaire,enseignant,gardien',
                'discipline' => 'nullable|string|max:255',
                'statut' => 'required|in:actif,suspendu,conge',
                'date_embauche' => 'nullable|date',
                'mode_paiement' => 'required|in:fixe,heure',
                'montant_fixe' => 'required_if:mode_paiement,fixe|nullable|numeric|min:0',
                'tarif_heure' => 'required_if:mode_paiement,heure|nullable|numeric|min:0'
            ]);

            $personnel->update($validated);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Personnel modifié avec succès']);
            }

            return redirect()->route('personnel.index', ['tab' => 'gestion-personnel'])
                ->with('success', 'Personnel modifié avec succès.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors de la modification'], 500);
            }
            return redirect()->back()->with('error', 'Erreur lors de la modification du personnel');
        }
    }

    /**
     * Supprimer un personnel
     */
    public function destroy(Personnel $personnel)
    {
        try {
            $personnel->delete();

            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Personnel supprimé avec succès']);
            }

            return redirect()->route('personnel.index', ['tab' => 'gestion-personnel'])
                ->with('success', 'Personnel supprimé avec succès.');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors de la suppression'], 500);
            }
            return redirect()->back()->with('error', 'Erreur lors de la suppression du personnel');
        }
    }

    /**
     * Changer le statut d'un personnel (actif/suspendu/conge)
     */
    public function toggleStatut(Personnel $personnel)
    {
        try {
            // Cycle entre les statuts : actif -> suspendu -> actif
            $newStatut = $personnel->statut === 'actif' ? 'suspendu' : 'actif';
            
            $personnel->update(['statut' => $newStatut]);

            $message = $newStatut === 'actif' ? 'Personnel activé' : 'Personnel suspendu';

            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => $message, 'statut' => $newStatut]);
            }

            return redirect()->route('personnel.index', ['tab' => 'gestion-personnel'])
                ->with('success', $message . ' avec succès.');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors du changement de statut'], 500);
            }
            return redirect()->back()->with('error', 'Erreur lors du changement de statut');
        }
    }

    /**
     * Récupérer les états de paiement pour un mois donné
     */
    public function getEtatsPaiement($annee, $mois)
    {
        try {
            // Récupérer tous les personnels actifs
            $personnels = Personnel::actif()->get();
            
            $etats = [];
            
            foreach ($personnels as $personnel) {
                // Chercher ou créer l'état de paiement pour ce personnel et cette période
                $etat = EtatPaiementMensuel::firstOrCreate(
                    [
                        'personnel_id' => $personnel->id,
                        'annee' => $annee,
                        'mois' => $mois,
                    ],
                    [
                        'heures_effectuees' => 0,
                        'primes' => 0,
                        'retenues' => 0,
                        'type_retenue' => '0', // Défaut à 0%
                        'avances' => 0,
                        'statut_paiement' => 'en_attente',
                        'visible' => true,
                        'archive' => false,
                    ]
                );
                
                // Recalculer automatiquement les montants
                $etat->calculerMontants();
                $etat->save();
                
                // Charger la relation personnel
                $etat->load('personnel');
                
                $etats[] = $etat;
            }
            
            return response()->json($etats);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du chargement des états'], 500);
        }
    }

    /**
     * Sauvegarder un état de paiement
     */
    public function sauvegarderEtatPaiement(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'nullable|exists:etats_paiement_mensuels,id',
                'personnel_id' => 'required|exists:personnels,id',
                'annee' => 'required|integer|min:2020|max:2030',
                'mois' => 'required|integer|min:1|max:12',
                'heures_effectuees' => 'nullable|numeric|min:0|max:200',
                'primes' => 'nullable|numeric|min:0',
                'retenues' => 'nullable|numeric|min:0',
                'type_retenue' => 'nullable|string|max:20',
                'avances' => 'nullable|numeric|min:0',
                'statut_paiement' => 'required|in:en_attente,paye',
            ]);

            // Trouver ou créer l'état
            if ($request->id) {
                $etat = EtatPaiementMensuel::findOrFail($request->id);
                $etat->update($validated);
            } else {
                $etat = EtatPaiementMensuel::create($validated);
            }

            // Si le statut passe à "payé", valider automatiquement
            if ($validated['statut_paiement'] === 'paye' && !$etat->date_validation) {
                $etat->date_validation = now();
                $etat->validateur_id = auth()->id();
                $etat->commentaire_validation = 'Validation automatique lors du passage au statut "Payé"';
            }
            
            // Si le statut passe à "en_attente", retirer la validation
            if ($validated['statut_paiement'] === 'en_attente' && $etat->date_validation) {
                $etat->date_validation = null;
                $etat->validateur_id = null;
                $etat->commentaire_validation = null;
            }

            // Recalculer les montants
            $etat->calculerMontants();
            $etat->save();

            return response()->json([
                'success' => true,
                'message' => 'État sauvegardé avec succès',
                'id' => $etat->id,
                'personnel_id' => $etat->personnel_id,
                'annee' => $etat->annee,
                'mois' => $etat->mois,
                'heures_effectuees' => $etat->heures_effectuees,
                'primes' => $etat->primes,
                'retenues' => $etat->retenues,
                'type_retenue' => $etat->type_retenue,
                'avances' => $etat->avances,
                'montant_total' => $etat->montant_total,
                'restant' => $etat->restant,
                'statut_paiement' => $etat->statut_paiement,
                'visible' => $etat->visible,
                'archive' => $etat->archive,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la sauvegarde'], 500);
        }
    }

    /**
     * Basculer la visibilité d'un état de paiement
     */
    public function toggleVisibiliteEtat(EtatPaiementMensuel $etat)
    {
        try {
            $etat->visible = !$etat->visible;
            $etat->save();

            return response()->json([
                'success' => true,
                'visible' => $etat->visible,
                'message' => $etat->visible ? 'Personnel affiché' : 'Personnel masqué',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du changement de visibilité'], 500);
        }
    }

    /**
     * Archiver tous les états de paiement d'un mois (seulement les états visibles)
     */
    public function archiverEtatsPaiement($annee, $mois)
    {
        try {
            DB::beginTransaction();

            $etats = EtatPaiementMensuel::where('annee', $annee)
                ->where('mois', $mois)
                ->where('archive', false)
                ->where('visible', true) // Seulement les états visibles
                ->get();

            if ($etats->isEmpty()) {
                return response()->json(['error' => 'Aucun état visible à archiver pour cette période'], 404);
            }

            // Marquer seulement les états visibles comme archivés
            EtatPaiementMensuel::where('annee', $annee)
                ->where('mois', $mois)
                ->where('visible', true) // Seulement les états visibles
                ->update([
                    'archive' => true,
                    'date_archive' => now(),
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'États archivés avec succès',
                'count' => $etats->count(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erreur lors de l\'archivage'], 500);
        }
    }

    /**
     * Récupérer les archives par année (seulement les états visibles archivés)
     */
    public function getArchives($annee)
    {
        try {
            // Récupérer tous les mois archivés pour cette année
            $moisArchives = EtatPaiementMensuel::where('annee', $annee)
                ->where('archive', true)
                ->where('visible', true)
                ->select('mois', 'date_archive')
                ->groupBy('mois', 'date_archive')
                ->orderBy('mois', 'desc')
                ->get();

            $archives = [];
            
            foreach ($moisArchives as $moisArchive) {
                // Pour chaque mois, récupérer les états comme dans aperçu
                $etats = EtatPaiementMensuel::where('annee', $annee)
                    ->where('mois', $moisArchive->mois)
                    ->where('archive', true)
                    ->where('visible', true)
                    ->get();

                if ($etats->count() > 0) {
                    $archives[] = [
                        'annee' => $annee,  
                        'mois' => $moisArchive->mois,
                        'date_archive' => $moisArchive->date_archive,
                        'nombre_personnel' => $etats->count(),
                        'total_montant' => $etats->sum('montant_total'),
                        'total_retenues' => $etats->sum('retenues'), // Exactement comme dans aperçu
                        'total_avances' => $etats->sum('avances'),
                        'total_restant' => $etats->sum('restant')
                    ];
                }
            }

            return response()->json($archives);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du chargement des archives'], 500);
        }
    }

    /**
     * Données pour les rapports avec filtres dynamiques
     */
    public function getRapportsData(Request $request)
    {
        try {
            $annee = $request->get('annee');
            $mois = $request->get('mois');
            $dateDebut = $request->get('dateDebut');
            $dateFin = $request->get('dateFin');
            $statut = $request->get('statut');
            $type = $request->get('type', 'tous');

            // Base query
            $query = EtatPaiementMensuel::with('personnel')
                ->where('visible', true);

            // Filtrer par année
            if ($annee) {
                $query->where('annee', $annee);
            }

            // Filtrer par mois
            if ($mois) {
                $query->where('mois', $mois);
            }

            // Filtrer par type (archivés, actuels, tous)
            if ($type === 'archives') {
                $query->where('archive', true);
            } elseif ($type === 'actuels') {
                $query->where('archive', false);
            }

            // Filtrer par statut
            if ($statut) {
                $query->where('statut_paiement', $statut);
            }

            // Filtrer par période de dates (basé sur created_at)
            if ($dateDebut && $dateFin) {
                $query->whereBetween('created_at', [
                    $dateDebut . ' 00:00:00',
                    $dateFin . ' 23:59:59'
                ]);
            } elseif ($dateDebut) {
                $query->whereDate('created_at', '>=', $dateDebut);
            } elseif ($dateFin) {
                $query->whereDate('created_at', '<=', $dateFin);
            }

            $etats = $query->get();

            if ($etats->isEmpty()) {
                return response()->json(null);
            }

            // Calculs exactement comme dans aperçu/print
            $totaux = [
                'personnel' => $etats->count(),
                'montant_brut' => $etats->sum(function($etat) {
                    return $etat->salaire_base + ($etat->primes ?? 0);
                }),
                'total_retenues' => $etats->sum('retenues'),
                'total_montant' => $etats->sum('montant_total'),
                'net_total' => $etats->sum('montant_total'),
                'total_avances' => $etats->sum('avances'),
                'total_restant' => $etats->sum('restant'),
            ];

            // Statistiques par statut
            $parStatut = [
                'payes' => [
                    'count' => $etats->where('statut_paiement', 'paye')->count(),
                    'montant' => $etats->where('statut_paiement', 'paye')->sum('montant_total')
                ],
                'en_attente' => [
                    'count' => $etats->where('statut_paiement', 'en_attente')->count(),
                    'montant' => $etats->where('statut_paiement', 'en_attente')->sum('montant_total')
                ]
            ];

            $totaux['par_statut'] = $parStatut;
            
            // Ajouter les détails du personnel
            $totaux['details'] = $etats->map(function ($etat) {
                return [
                    'id' => $etat->id,
                    'personnel' => [
                        'nom' => $etat->personnel->nom,
                        'prenom' => $etat->personnel->prenom,
                        'type_personnel' => $etat->personnel->type_personnel,
                        'mode_paiement' => $etat->personnel->mode_paiement
                    ],
                    'heures_effectuees' => $etat->heures_effectuees,
                    'primes' => $etat->primes,
                    'retenues' => $etat->retenues,
                    'montant_total' => $etat->montant_total,
                    'avances' => $etat->avances,
                    'restant' => $etat->restant,
                    'statut_paiement' => $etat->statut_paiement
                ];
            });

            return response()->json($totaux);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du chargement des rapports'], 500);
        }
    }

    /**
     * Aperçu des rapports avec filtres
     */
    public function apercuRapports(Request $request)
    {
        try {
            $annee = $request->get('annee');
            $mois = $request->get('mois');
            $dateDebut = $request->get('dateDebut');
            $dateFin = $request->get('dateFin');
            $statut = $request->get('statut');
            $type = $request->get('type', 'tous');

            // Même logique que getRapportsData
            $query = EtatPaiementMensuel::with('personnel')
                ->where('visible', true);

            if ($annee) $query->where('annee', $annee);
            if ($mois) $query->where('mois', $mois);
            if ($statut) $query->where('statut_paiement', $statut);

            if ($type === 'archives') {
                $query->where('archive', true);
            } elseif ($type === 'actuels') {
                $query->where('archive', false);
            }

            if ($dateDebut && $dateFin) {
                $query->whereBetween('created_at', [
                    $dateDebut . ' 00:00:00',
                    $dateFin . ' 23:59:59'
                ]);
            } elseif ($dateDebut) {
                $query->whereDate('created_at', '>=', $dateDebut);
            } elseif ($dateFin) {
                $query->whereDate('created_at', '<=', $dateFin);
            }

            $etats = $query->join('personnels', 'etats_paiement_mensuels.personnel_id', '=', 'personnels.id')
                ->orderBy('personnels.nom')
                ->select('etats_paiement_mensuels.*')
                ->get();

            if ($etats->isEmpty()) {
                abort(404, 'Aucun état de paiement trouvé pour les critères sélectionnés');
            }

            // Calculer les totaux
            $totaux = [
                'personnel' => $etats->count(),
                'montant_brut' => $etats->sum(function($etat) {
                    return $etat->salaire_base + ($etat->primes ?? 0);
                }),
                'total_retenues' => $etats->sum('retenues'),
                'net_total' => $etats->sum('montant_total'),
                'total_avances' => $etats->sum('avances'),
                'total_restant' => $etats->sum('restant'),
            ];

            // Informations sur les filtres pour l'affichage
            $filtres = [
                'annee' => $annee,
                'mois' => $mois,
                'dateDebut' => $dateDebut,
                'dateFin' => $dateFin,
                'statut' => $statut,
                'type' => $type
            ];

            $nomMois = [
                1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
            ];

            return view('personnel.apercu_rapports', compact('etats', 'totaux', 'filtres', 'nomMois'));
        } catch (\Exception $e) {
            abort(500, 'Erreur lors de la génération de l\'aperçu');
        }
    }

    /**
     * Récupérer le détail d'une archive
     */
    public function getDetailArchive($annee, $mois)
    {
        try {
            $details = EtatPaiementMensuel::with('personnel')
                ->where('annee', $annee)
                ->where('mois', $mois)
                ->where('archive', true)
                ->where('visible', true) // Seulement les états visibles
                ->join('personnels', 'etats_paiement_mensuels.personnel_id', '=', 'personnels.id')
                ->orderBy('personnels.nom')
                ->select('etats_paiement_mensuels.*')
                ->get();

            return response()->json($details);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du chargement des détails'], 500);
        }
    }

    /**
     * Aperçu des états de paiement (archivés ou actuels)
     */
    public function apercuEtatsPaiement($annee, $mois)
    {
        try {
            // Chercher d'abord les états non archivés, puis les archivés si aucun trouvé
            $etats = EtatPaiementMensuel::with('personnel')
                ->where('annee', $annee)
                ->where('mois', $mois)
                ->where('visible', true) // Seulement les états visibles
                ->join('personnels', 'etats_paiement_mensuels.personnel_id', '=', 'personnels.id')
                ->orderBy('personnels.nom')
                ->select('etats_paiement_mensuels.*')
                ->get();

            if ($etats->isEmpty()) {
                abort(404, 'Aucun état de paiement trouvé pour cette période');
            }

            $nomMois = [
                1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
            ];

            // Calculs basés sur les vrais champs de la table
            $totaux = [
                'personnel' => $etats->count(),
                'montant_brut' => $etats->sum(function($etat) {
                    return $etat->salaire_base + ($etat->primes ?? 0);
                }),
                'total_retenues' => $etats->sum('retenues'),
                'net_total' => $etats->sum('montant_total'), // montant_total = net à payer
                'total_avances' => $etats->sum('avances'),
                'total_restant' => $etats->sum('restant'),
            ];

            return view('personnel.apercu_etats', compact('etats', 'annee', 'mois', 'nomMois', 'totaux'));
        } catch (\Exception $e) {
            \Log::error('Erreur aperçu états: ' . $e->getMessage());
            abort(500, 'Erreur lors de la génération de l\'aperçu');
        }
    }

    /**
     * Imprimer une archive (vue PDF)
     */
    public function printArchive($annee, $mois)
    {
        try {
            $etats = EtatPaiementMensuel::with('personnel')
                ->where('annee', $annee)
                ->where('mois', $mois)
                ->where('archive', true)
                ->where('visible', true) // Seulement les états visibles
                ->join('personnels', 'etats_paiement_mensuels.personnel_id', '=', 'personnels.id')
                ->orderBy('personnels.nom')
                ->select('etats_paiement_mensuels.*')
                ->get();

            if ($etats->isEmpty()) {
                abort(404, 'Archive non trouvée');
            }

            $nomMois = [
                1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
            ];

            // Calculs basés sur les vrais champs de la table
            $totaux = [
                'personnel' => $etats->count(),
                'montant_brut' => $etats->sum(function($etat) {
                    return $etat->salaire_base + ($etat->primes ?? 0);
                }),
                'total_retenues' => $etats->sum('retenues'),
                'net_total' => $etats->sum('montant_total'), // montant_total = net à payer
                'total_avances' => $etats->sum('avances'),
                'total_restant' => $etats->sum('restant'),
            ];

            return view('personnel.archive_print', compact('etats', 'annee', 'mois', 'nomMois', 'totaux'));
        } catch (\Exception $e) {
            \Log::error('Erreur printArchive: ' . $e->getMessage());
            abort(500, 'Erreur lors de la génération de l\'impression: ' . $e->getMessage());
        }
    }

    /**
     * Recalculer tous les états d'une période (fonction de debug/maintenance)
     */
    public function recalculerEtatsPaiement($annee, $mois)
    {
        try {
            $etats = EtatPaiementMensuel::with('personnel')
                ->where('annee', $annee)
                ->where('mois', $mois)
                ->get();
            
            $count = 0;
            foreach ($etats as $etat) {
                \Log::info("Avant recalcul - ID: {$etat->id}, Personnel: {$etat->personnel->nom}, Retenues: {$etat->retenues}, Type: {$etat->type_retenue}");
                
                $etat->calculerMontants();
                $etat->save();
                
                \Log::info("Après recalcul - ID: {$etat->id}, Retenues: {$etat->retenues}, Montant total: {$etat->montant_total}");
                $count++;
            }
            
            return response()->json([
                'success' => true,
                'message' => "Recalcul terminé pour {$count} états de paiement",
                'count' => $count
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erreur recalcul: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors du recalcul'], 500);
        }
    }

    /**
     * Récupérer les années scolaires disponibles depuis le module paramètres
     */
    public function getAnneesScolaires()
    {
        try {
            $anneesScolaires = AnneeScolaire::orderBy('libelle', 'desc')->get();
            $anneesFormatees = [];
            
            \Log::info('Années scolaires trouvées: ' . $anneesScolaires->count());
            
            if ($anneesScolaires->count() > 0) {
                // Utiliser les années du module paramètres
                foreach ($anneesScolaires as $annee) {
                    // Extraire l'année de début du libellé (ex: "2025-2026" -> 2025)
                    $valeur = null;
                    if (preg_match('/^(\d{4})-(\d{4})$/', $annee->libelle, $matches)) {
                        $valeur = (int) $matches[1];
                    }
                    
                    $anneesFormatees[] = [
                        'valeur' => $valeur,
                        'libelle' => $annee->libelle,
                        'actuelle' => $annee->actif
                    ];
                    
                    \Log::info('Année ajoutée: ' . $annee->libelle . ' (valeur: ' . $valeur . ', active: ' . ($annee->actif ? 'oui' : 'non') . ')');
                }
            } else {
                // Fallback vers l'ancien système si aucune année dans paramètres
                $annees = $this->getAnneesScolairesDisponibles();
                foreach ($annees as $annee) {
                    $anneesFormatees[] = [
                        'valeur' => $annee,
                        'libelle' => $this->getLibelleAnneeScolaire($annee),
                        'actuelle' => $annee === $this->getAnneeScolaireActuelle()
                    ];
                }
                \Log::info('Utilisation du fallback avec ' . count($annees) . ' années');
            }
            
            \Log::info('Réponse finale: ' . json_encode($anneesFormatees));
            return response()->json($anneesFormatees);
            
        } catch (\Exception $e) {
            \Log::error('Erreur dans getAnneesScolaires: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors du chargement des années scolaires'], 500);
        }
    }

    /**
     * Obtenir les données pour les rapports - États PAYÉS uniquement
     */
    public function getReportsData(Request $request)
    {
        try {
            // Requête de base pour les états PAYÉS seulement
            $query = EtatPaiementMensuel::with(['personnel:id,nom,prenom,fonction'])
                ->where('statut_paiement', 'paye');

            // Appliquer les filtres
            if ($request->filled('annee')) {
                $query->where('annee', $request->annee);
            }
            
            if ($request->filled('mois')) {
                $query->where('mois', $request->mois);
            }

            // Récupérer uniquement les états payés
            $etatsPayes = $query->get();
            
            // Calculer les totaux
            $totaux = [
                'nombre_personnel' => $etatsPayes->count(),
                'total_retenues' => (int)$etatsPayes->sum('retenues'),
                'total_avances' => (int)$etatsPayes->sum('avances'),
                'net_total' => (int)$etatsPayes->sum('montant_total'),
                'montant_brut' => (int)($etatsPayes->sum('montant_total') + $etatsPayes->sum('retenues'))
            ];

            // Préparer la liste détaillée
            $etatsDetails = $etatsPayes->map(function($etat) {
                return [
                    'id' => $etat->id,
                    'personnel_nom' => $etat->personnel->nom . ' ' . $etat->personnel->prenom,
                    'personnel_fonction' => $etat->personnel->fonction,
                    'annee' => $etat->annee,
                    'mois' => $etat->mois,
                    'mois_nom' => $this->getMoisNom($etat->mois),
                    'heures_effectuees' => $etat->heures_effectuees,
                    'primes' => (int)$etat->primes,
                    'retenues' => (int)$etat->retenues,
                    'montant_total' => (int)$etat->montant_total,
                    'avances' => (int)$etat->avances,
                    'restant' => (int)$etat->restant,
                    'statut_paiement' => $etat->statut_paiement,
                    'archive' => $etat->archive,
                    'created_at' => $etat->created_at->format('d/m/Y H:i')
                ];
            });

            return response()->json([
                'success' => true,
                'totaux' => $totaux,
                'etats' => $etatsDetails,
                'filtres_appliques' => [
                    'annee' => $request->get('annee'),
                    'mois' => $request->get('mois')
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur dans getReportsData: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors du chargement des rapports'
            ], 500);
        }
    }

    /**
     * Obtenir le nom du mois
     */
    private function getMoisNom($numeroMois)
    {
        $mois = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];
        return $mois[$numeroMois] ?? 'Mois inconnu';
    }

    /**
     * Valider un état de paiement
     */
    public function validateEtat(Request $request, $id)
    {
        try {
            $etat = EtatPaiementMensuel::findOrFail($id);
            
            $etat->update([
                'date_validation' => now(),
                'validateur_id' => auth()->id(),
                'commentaire_validation' => $request->input('commentaire')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'État validé avec succès'
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur dans validateEtat: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la validation'], 500);
        }
    }

    /**
     * Générer un rapport
     */
    public function generateReport(Request $request)
    {
        try {
            $type = $request->input('type', 'summary');
            
            // Récupérer les données avec les filtres
            $query = EtatPaiementMensuel::with([
                'personnel:id,nom,prenom,fonction,etablissement_id',
                'anneeScolaire:id,libelle',
                'validateur:id,nom,prenom'
            ]);

            // Appliquer les filtres
            if ($request->filled('annee_id')) {
                $query->where('annee_scolaire_id', $request->annee_id);
            }
            if ($request->filled('mois')) {
                $query->where('mois', $request->mois);
            }
            if ($request->filled('etablissement_id')) {
                $query->whereHas('personnel', function($q) use ($request) {
                    $q->where('etablissement_id', $request->etablissement_id);
                });
            }

            $etats = $query->get();

            switch ($type) {
                case 'summary':
                    return $this->generateSummaryReport($etats, $request);
                case 'detailed':
                    return $this->generateDetailedReport($etats, $request);
                case 'pending':
                    return $this->generatePendingReport($etats, $request);
                default:
                    return $this->generateSummaryReport($etats, $request);
            }

        } catch (\Exception $e) {
            \Log::error('Erreur dans generateReport: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la génération du rapport'], 500);
        }
    }

    /**
     * Exporter les données des rapports
     */
    public function exportData(Request $request)
    {
        try {
            $format = $request->input('format', 'pdf');
            
            // Récupérer les données avec les mêmes filtres que getReportsData
            $query = EtatPaiementMensuel::with([
                'personnel:id,nom,prenom,fonction,etablissement_id',
                'validateur:id,nom,prenom'
            ])
            ->where('statut_paiement', 'payé')
            ->whereNotNull('date_validation');

            // Appliquer les filtres
            if ($request->filled('annee_id')) {
                $query->where('annee', $request->annee_id);
            }
            if ($request->filled('mois')) {
                $query->where('mois', $request->mois);
            }

            // Filtres par date de validation
            if ($request->filled('date_debut')) {
                $query->whereDate('date_validation', '>=', $request->date_debut);
            }
            if ($request->filled('date_fin')) {
                $query->whereDate('date_validation', '<=', $request->date_fin);
            }

            $etats = $query->orderBy('date_validation', 'desc')->get();

            if ($format === 'excel') {
                return $this->exportToExcel($etats, $request);
            } else {
                return $this->exportToPdf($etats, $request);
            }

        } catch (\Exception $e) {
            \Log::error('Erreur dans exportData: ' . $e->getMessage());
            return response('Erreur lors de l\'export: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Générer un rapport de synthèse
     */
    private function generateSummaryReport($etats, $request)
    {
        $data = [
            'etats' => $etats,
            'total_etats' => $etats->count(),
            'total_valides' => $etats->where('date_validation', '!=', null)->count(),
            'total_en_attente' => $etats->where('date_validation', null)->count(),
            'montant_total' => $etats->sum('net_a_payer'),
            'filters' => $request->all()
        ];

        return view('personnel.reports.summary', $data);
    }

    /**
     * Générer un rapport détaillé
     */
    private function generateDetailedReport($etats, $request)
    {
        $data = [
            'etats' => $etats,
            'filters' => $request->all()
        ];

        return view('personnel.reports.detailed', $data);
    }

    /**
     * Générer un rapport des validations en attente
     */
    private function generatePendingReport($etats, $request)
    {
        $etatsEnAttente = $etats->where('date_validation', null);
        
        $data = [
            'etats' => $etatsEnAttente,
            'filters' => $request->all()
        ];

        return view('personnel.reports.pending', $data);
    }

    /**
     * Export vers Excel
     */
    private function exportToExcel($etats, $request)
    {
        // Pour l'instant, retourner un CSV simple
        $filename = 'rapport_paiements_' . now()->format('Y-m-d_H-i') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($etats) {
            $file = fopen('php://output', 'w');
            
            // En-têtes CSV
            fputcsv($file, [
                'Personnel',
                'Fonction', 
                'Période',
                'Salaire Base',
                'Avances',
                'Retenues',
                'Net Payé',
                'Date Validation',
                'Validé par'
            ]);

            // Données
            foreach ($etats as $etat) {
                fputcsv($file, [
                    $etat->personnel->nom . ' ' . $etat->personnel->prenom,
                    $etat->personnel->fonction,
                    $etat->mois . '/' . $etat->annee,
                    $etat->montant_total,
                    $etat->avances ?? 0,
                    $etat->retenues ?? 0,
                    $etat->montant_total - ($etat->avances ?? 0),
                    $etat->date_validation->format('d/m/Y H:i'),
                    $etat->validateur ? $etat->validateur->name : ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export vers PDF
     */
    private function exportToPdf($etats, $request)
    {
        try {
            $data = [
                'etats' => $etats,
                'filters' => $request->all(),
                'date_export' => now()->format('d/m/Y à H:i'),
                'total_montant' => $etats->sum(function($etat) {
                    return $etat->montant_total - ($etat->avances ?? 0);
                }),
                'etablissement' => \App\Models\Etablissement::first()
            ];

            $pdf = \PDF::loadView('personnel.reports.export_pdf', $data);
            
            $filename = 'rapport_paiements_' . now()->format('Y-m-d_H-i') . '.pdf';
            
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            \Log::error('Erreur export PDF: ' . $e->getMessage());
            return response('Export PDF temporairement indisponible. Données: ' . $etats->count() . ' paiements trouvés.', 200)
                ->header('Content-Type', 'text/plain');
        }
    }

}
