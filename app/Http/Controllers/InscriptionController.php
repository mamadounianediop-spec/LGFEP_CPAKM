<?php

namespace App\Http\Controllers;

use App\Models\PreInscription;
use App\Models\Inscription;
use App\Models\Niveau;
use App\Models\Classe;
use App\Models\Frais;
use App\Models\AnneeScolaire;
use App\Models\Etablissement;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class InscriptionController extends Controller
{
    /**
     * Page principale avec les 4 onglets
     */
    public function index(Request $request): View
    {
        $activeTab = $request->get('tab', 'pre-inscription');
        $anneeActive = AnneeScolaire::getActive();
        
        // Si aucune année n'est active, rediriger vers paramètres
        if (!$anneeActive) {
            return redirect()->route('parametres.index')
                ->with('error', 'Aucune année scolaire n\'est activée. Veuillez activer une année scolaire.');
        }
        
        // Données pour pré-inscription
        $preInscriptions = PreInscription::with('inscription')
            ->where('annee_scolaire_id', $anneeActive->id)
            ->when($request->search, function($query, $search) {
                $query->search($search);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Données pour inscription
        $preInscriptsDisponibles = PreInscription::whereDoesntHave('inscription')
            ->where('annee_scolaire_id', $anneeActive->id)
            ->when($request->search_inscription, function($query, $search) {
                $query->search($search);
            })
            ->get();

        // Données pour liste élèves avec filtres - LOGIQUE CORRIGÉE
        if ($request->filter_statut === 'non_inscrits') {
            // Pour "non inscrits": récupérer les pré-inscriptions sans inscription
            $inscriptionsQuery = PreInscription::with(['inscription'])
                ->where('annee_scolaire_id', request('filter_annee', $anneeActive->id))
                ->whereDoesntHave('inscription');
                
            // Filtres pour pré-inscriptions
            if ($request->search_eleves) {
                $inscriptionsQuery->where(function($q) use ($request) {
                    $q->where('nom', 'LIKE', '%' . $request->search_eleves . '%')
                      ->orWhere('prenom', 'LIKE', '%' . $request->search_eleves . '%')
                      ->orWhere('ine', 'LIKE', '%' . $request->search_eleves . '%');
                });
            }

            if ($request->filter_niveau) {
                $inscriptionsQuery->where('niveau_id', $request->filter_niveau);
            }

            if ($request->filter_classe) {
                $inscriptionsQuery->where('classe_id', $request->filter_classe);
            }
        } else {
            // Pour "inscrits" ou autres: récupérer les inscriptions finalisées
            $inscriptionsQuery = Inscription::with(['preInscription', 'niveau', 'classe'])
                ->where('annee_scolaire_id', request('filter_annee', $anneeActive->id));

            // Filtres pour inscriptions
            if ($request->search_eleves) {
                $inscriptionsQuery->whereHas('preInscription', function($q) use ($request) {
                    $q->where('nom', 'LIKE', '%' . $request->search_eleves . '%')
                      ->orWhere('prenom', 'LIKE', '%' . $request->search_eleves . '%')
                      ->orWhere('ine', 'LIKE', '%' . $request->search_eleves . '%');
                });
            }

            if ($request->filter_niveau) {
                $inscriptionsQuery->where('niveau_id', $request->filter_niveau);
            }

            if ($request->filter_classe) {
                $inscriptionsQuery->where('classe_id', $request->filter_classe);
            }

            // Filtre spécifique pour les statuts d'inscription (actif, suspendu, annulé)
            if ($request->filter_statut && $request->filter_statut !== 'inscrits') {
                $inscriptionsQuery->where('statut', $request->filter_statut);
            }
        }

        $inscriptions = $inscriptionsQuery->orderBy('created_at', 'desc')->paginate(15);

        // Données pour rapports
        $statistiques = [
            'total_pre_inscriptions' => PreInscription::where('annee_scolaire_id', $anneeActive->id)->count(),
            'inscriptions_validees' => Inscription::where('annee_scolaire_id', $anneeActive->id)->count(),
            'paiements_complets' => Inscription::where('annee_scolaire_id', $anneeActive->id)->where('statut_paiement', 'complet')->count(),
            'paiements_partiels' => Inscription::where('annee_scolaire_id', $anneeActive->id)->where('statut_paiement', 'partiel')->count(),
            'total_recettes' => Inscription::where('annee_scolaire_id', $anneeActive->id)->sum('montant_paye'),
        ];

        // Données pour les graphiques
        $graphiques = [
            'modes_paiement' => Inscription::selectRaw('mode_paiement, COUNT(*) as count')
                ->where('annee_scolaire_id', $anneeActive->id)
                ->groupBy('mode_paiement')
                ->get()
                ->pluck('count', 'mode_paiement'),
            'evolution_7_jours' => $this->getEvolutionInscriptions($anneeActive->id)
        ];

        $niveaux = Niveau::with('classes')->get();
        $classes = Classe::all();
        $anneesScolaires = AnneeScolaire::orderBy('libelle', 'desc')->get();

        return view('inscriptions.index', compact(
            'activeTab', 'preInscriptions', 'preInscriptsDisponibles', 
            'inscriptions', 'statistiques', 'graphiques', 'niveaux', 'classes', 
            'anneeActive', 'anneesScolaires'
        ));
    }

    /**
     * Obtenir l'évolution des inscriptions sur 7 jours
     */
    private function getEvolutionInscriptions($anneeScolaireId)
    {
        $evolution = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = Inscription::where('annee_scolaire_id', $anneeScolaireId)
                ->whereDate('created_at', $date)
                ->count();
            $evolution[$date] = $count;
        }
        return $evolution;
    }

    /**
     * Enregistrer une pré-inscription
     */
    public function storePreInscription(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|regex:/^[a-zA-ZÀ-ÿ\s\-]+$/',
            'prenom' => 'required|string|max:255|regex:/^[a-zA-ZÀ-ÿ\s\-]+$/',
            'sexe' => 'required|in:M,F',
            'ine' => 'nullable|string|max:20|unique:pre_inscriptions,ine|regex:/^[A-Z0-9]+$/',
            'date_naissance' => 'nullable|date|before:today|after:1995-01-01|before:' . now()->subYears(10)->format('Y-m-d'),
            'lieu_naissance' => 'nullable|string|max:255',
            'adresse' => 'nullable|string|min:10',
            'contact' => 'nullable|string|regex:/^[0-9\s\-\+]+$/|min:8',
            'tuteur' => 'nullable|string|max:255',
            'etablissement_origine' => 'nullable|string|max:255',
        ], [
            'nom.required' => 'Le nom est obligatoire',
            'nom.regex' => 'Le nom ne doit contenir que des lettres',
            'prenom.required' => 'Le prénom est obligatoire',
            'prenom.regex' => 'Le prénom ne doit contenir que des lettres',
            'sexe.required' => 'Le sexe est obligatoire',
            'sexe.in' => 'Le sexe doit être M (Masculin) ou F (Féminin)',
            'ine.unique' => 'Cet INE existe déjà dans le système',
            'ine.regex' => 'L\'INE doit contenir uniquement des lettres majuscules et des chiffres',
            'date_naissance.before' => 'L\'élève doit avoir au moins 10 ans (né avant le ' . now()->subYears(10)->format('d/m/Y') . ')',
            'date_naissance.after' => 'La date de naissance doit être postérieure à 1995 (âge maximum 30 ans)',
            'adresse.min' => 'L\'adresse doit contenir au moins 10 caractères',
            'contact.regex' => 'Le numéro de contact n\'est pas valide',
            'contact.min' => 'Le numéro de contact doit contenir au moins 8 caractères',
        ]);

        // Générer INE si pas fourni
        if (empty($validated['ine'])) {
            $validated['ine'] = PreInscription::generateINE();
        }

        // Vérifier l'unicité de l'INE encore une fois
        while (PreInscription::where('ine', $validated['ine'])->exists()) {
            $validated['ine'] = PreInscription::generateINE();
        }

        // Ajouter l'année scolaire active
        $validated['annee_scolaire_id'] = AnneeScolaire::getActive()->id;

        PreInscription::create($validated);

        return redirect()->route('inscriptions.index', ['tab' => 'pre-inscription'])
                        ->with('success', 'Pré-inscription enregistrée avec succès.');
    }

    /**
     * Mettre à jour une pré-inscription
     */
    public function updatePreInscription(Request $request, PreInscription $preInscription): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|regex:/^[a-zA-ZÀ-ÿ\s\-]+$/',
            'prenom' => 'required|string|max:255|regex:/^[a-zA-ZÀ-ÿ\s\-]+$/',
            'sexe' => 'required|in:M,F',
            'ine' => 'nullable|string|max:20|unique:pre_inscriptions,ine,' . $preInscription->id . '|regex:/^[A-Z0-9]+$/',
            'date_naissance' => 'nullable|date|before:today|after:1995-01-01|before:' . now()->subYears(10)->format('Y-m-d'),
            'lieu_naissance' => 'nullable|string|max:255',
            'adresse' => 'nullable|string|min:10',
            'contact' => 'nullable|string|regex:/^[0-9\s\-\+]+$/|min:8',
            'tuteur' => 'nullable|string|max:255',
            'etablissement_origine' => 'nullable|string|max:255',
        ], [
            'nom.required' => 'Le nom est obligatoire',
            'nom.regex' => 'Le nom ne doit contenir que des lettres',
            'prenom.required' => 'Le prénom est obligatoire',
            'prenom.regex' => 'Le prénom ne doit contenir que des lettres',
            'sexe.required' => 'Le sexe est obligatoire',
            'sexe.in' => 'Le sexe doit être M (Masculin) ou F (Féminin)',
            'ine.unique' => 'Cet INE existe déjà dans le système',
            'ine.regex' => 'L\'INE doit contenir uniquement des lettres majuscules et des chiffres',
            'date_naissance.before' => 'L\'élève doit avoir au moins 10 ans (né avant le ' . now()->subYears(10)->format('d/m/Y') . ')',
            'date_naissance.after' => 'La date de naissance doit être postérieure à 1995 (âge maximum 30 ans)',
            'adresse.min' => 'L\'adresse doit contenir au moins 10 caractères',
            'contact.regex' => 'Le numéro de contact n\'est pas valide',
            'contact.min' => 'Le numéro de contact doit contenir au moins 8 caractères',
        ]);

        // Générer INE si pas fourni
        if (empty($validated['ine'])) {
            $validated['ine'] = PreInscription::generateINE();
        }

        // Vérifier l'unicité de l'INE encore une fois
        while (PreInscription::where('ine', $validated['ine'])->where('id', '!=', $preInscription->id)->exists()) {
            $validated['ine'] = PreInscription::generateINE();
        }

        $preInscription->update($validated);

        return redirect()->route('inscriptions.index', ['tab' => 'pre-inscription'])
                        ->with('success', 'Pré-inscription modifiée avec succès.');
    }

    /**
     * Finaliser une inscription
     */
    public function finaliserInscription(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pre_inscription_id' => 'required|exists:pre_inscriptions,id',
            'niveau_id' => 'required|exists:niveaux,id',
            'classe_id' => 'required|exists:classes,id',
            'montant_total' => 'required|numeric|min:1000|max:1000000',
            'montant_paye' => 'required|numeric|min:0|lte:montant_total',
            'mode_paiement' => 'required|in:orange_money,wave,free_money,billetage,especes',
        ], [
            'pre_inscription_id.required' => 'Vous devez sélectionner un élève',
            'pre_inscription_id.exists' => 'L\'élève sélectionné n\'existe pas',
            'niveau_id.required' => 'Le niveau est obligatoire',
            'niveau_id.exists' => 'Le niveau sélectionné n\'existe pas',
            'classe_id.required' => 'La classe est obligatoire',
            'classe_id.exists' => 'La classe sélectionnée n\'existe pas',
            'montant_total.required' => 'Le montant total est obligatoire',
            'montant_total.min' => 'Le montant total doit être d\'au moins 1 000 FCFA',
            'montant_total.max' => 'Le montant total ne peut pas dépasser 1 000 000 FCFA',
            'montant_paye.required' => 'Le montant payé est obligatoire',
            'montant_paye.min' => 'Le montant payé ne peut pas être négatif',
            'montant_paye.lte' => 'Le montant payé ne peut pas dépasser le montant total',
            'mode_paiement.required' => 'Le mode de paiement est obligatoire',
            'mode_paiement.in' => 'Le mode de paiement sélectionné n\'est pas valide',
        ]);

        // Vérifier que l'élève n'est pas déjà inscrit
        $preInscription = PreInscription::find($validated['pre_inscription_id']);
        if ($preInscription->inscription) {
            return redirect()->back()->with('error', 'Cet élève (INE: ' . $preInscription->ine . ') est déjà inscrit.');
        }

        // Vérifier l'unicité de l'INE pour les inscriptions de l'année courante
        $anneeActive = AnneeScolaire::getActive();
        $existingInscription = Inscription::whereHas('preInscription', function($query) use ($preInscription) {
            $query->where('ine', $preInscription->ine);
        })->where('annee_scolaire_id', $anneeActive->id)->first();

        if ($existingInscription) {
            return redirect()->back()->with('error', 'Un élève avec l\'INE ' . $preInscription->ine . ' est déjà inscrit cette année.');
        }

        // Vérifier que la classe appartient au niveau
        $classe = Classe::where('id', $validated['classe_id'])
                        ->where('niveau_id', $validated['niveau_id'])
                        ->first();

        if (!$classe) {
            return redirect()->back()->with('error', 'La classe sélectionnée ne correspond pas au niveau.');
        }

        // Vérifier qu'il y a des frais configurés pour ce niveau
        $fraisNiveau = Frais::where('niveau_id', $validated['niveau_id'])
                           ->where('actif', true)
                           ->where('type', 'inscription')
                           ->get();

        if ($fraisNiveau->isEmpty()) {
            return redirect()->back()->with('error', 'Aucun frais d\'inscription n\'est configuré pour ce niveau. Veuillez contacter l\'administration pour configurer les frais avant de procéder à l\'inscription.');
        }

        // Vérifier que le montant total correspond aux frais configurés
        $totalFraisAttendus = $fraisNiveau->sum('montant');
        if ($validated['montant_total'] != $totalFraisAttendus) {
            return redirect()->back()->with('error', 'Le montant total (' . number_format($validated['montant_total'], 0, ',', ' ') . ' FCFA) ne correspond pas aux frais configurés pour ce niveau (' . number_format($totalFraisAttendus, 0, ',', ' ') . ' FCFA).');
        }

        $validated['numero_recu'] = Inscription::generateNumeroRecu();
        $validated['date_inscription'] = Carbon::now();
        $validated['statut_paiement'] = $validated['montant_paye'] >= $validated['montant_total'] ? 'complet' : 'partiel';
        $validated['annee_scolaire_id'] = $anneeActive->id;

        $inscription = Inscription::create($validated);

        // Mettre à jour le statut de la pré-inscription
        $preInscription->update(['statut' => 'inscrit']);

        // Rediriger automatiquement vers le reçu
        return redirect()->route('inscriptions.recu', $inscription)
                        ->with('success', 'Inscription finalisée avec succès pour l\'élève INE: ' . $preInscription->ine);
    }

    /**
     * Supprimer une pré-inscription
     */
    public function destroyPreInscription(PreInscription $preInscription): RedirectResponse
    {
        if ($preInscription->inscription) {
            return redirect()->back()->with('error', 'Impossible de supprimer une pré-inscription déjà finalisée.');
        }

        $preInscription->delete();

        return redirect()->route('inscriptions.index', ['tab' => 'pre-inscription'])
                        ->with('success', 'Pré-inscription supprimée avec succès.');
    }

    /**
     * Recherche intelligente pour inscriptions
     */
    public function searchPreInscriptions(Request $request)
    {
        $search = $request->get('q');
        $anneeActive = AnneeScolaire::getActive();
        
        // Chercher dans TOUTES les pré-inscriptions (inscrites ou non)
        $results = PreInscription::where('annee_scolaire_id', $anneeActive->id)
            ->search($search)
            ->with('inscription') // Charger la relation pour vérifier le statut
            ->limit(10)
            ->get()
            ->map(function($preInscription) {
                $statut = $preInscription->inscription ? 'Déjà inscrit' : 'Non inscrit';
                $couleur = $preInscription->inscription ? 'text-green-600' : 'text-blue-600';
                
                return [
                    'id' => $preInscription->id,
                    'text' => $preInscription->nom . ' ' . $preInscription->prenom . ' (' . $preInscription->ine . ')',
                    'statut' => $statut,
                    'couleur' => $couleur,
                    'data' => $preInscription
                ];
            });

        return response()->json($results);
    }

    /**
     * Obtenir les frais pour un niveau
     */
    public function getFraisNiveau(Niveau $niveau)
    {
        // Obtenir uniquement les frais d'inscription actifs pour ce niveau
        $frais = Frais::where('niveau_id', $niveau->id)
                     ->where('actif', true)
                     ->where('type', 'inscription')
                     ->get();
        $total = $frais->sum('montant');

        return response()->json([
            'frais' => $frais,
            'total' => $total,
            'niveau_nom' => $niveau->nom
        ]);
    }

    /**
     * Mettre à jour une inscription
     */
    public function updateInscription(Request $request, Inscription $inscription)
    {
        $validated = $request->validate([
            'niveau_id' => 'required|exists:niveaux,id',
            'classe_id' => 'required|exists:classes,id',
            'montant_total' => 'required|numeric|min:0',
            'montant_paye' => 'required|numeric|min:0',
            'mode_paiement' => 'required|in:orange_money,wave,free_money,billetage,especes',
            'statut' => 'required|in:actif,suspendu,abandonne',
            'remarques' => 'nullable|string|max:1000'
        ], [
            'niveau_id.required' => 'Le niveau est obligatoire.',
            'niveau_id.exists' => 'Le niveau sélectionné n\'existe pas.',
            'classe_id.required' => 'La classe est obligatoire.',
            'classe_id.exists' => 'La classe sélectionnée n\'existe pas.',
            'montant_total.required' => 'Le montant total est obligatoire.',
            'montant_total.numeric' => 'Le montant total doit être un nombre.',
            'montant_total.min' => 'Le montant total ne peut pas être négatif.',
            'montant_paye.required' => 'Le montant payé est obligatoire.',
            'montant_paye.numeric' => 'Le montant payé doit être un nombre.',
            'montant_paye.min' => 'Le montant payé ne peut pas être négatif.',
            'mode_paiement.required' => 'Le mode de paiement est obligatoire.',
            'mode_paiement.in' => 'Le mode de paiement sélectionné n\'est pas valide.',
            'statut.required' => 'Le statut est obligatoire.',
            'statut.in' => 'Le statut sélectionné n\'est pas valide.',
            'remarques.max' => 'Les remarques ne peuvent pas dépasser 1000 caractères.'
        ]);

        // Vérifier que la classe appartient au niveau
        $classe = Classe::where('id', $validated['classe_id'])
                         ->where('niveau_id', $validated['niveau_id'])
                         ->first();

        if (!$classe) {
            return back()->withErrors(['classe_id' => 'La classe sélectionnée ne correspond pas au niveau choisi.']);
        }

        // Vérifier que le montant payé ne dépasse pas le montant total
        if ($validated['montant_paye'] > $validated['montant_total']) {
            return back()->withErrors(['montant_paye' => 'Le montant payé ne peut pas être supérieur au montant total.']);
        }

        // Calculer le statut de paiement
        if ($validated['montant_paye'] >= $validated['montant_total']) {
            $statut_paiement = 'complet';
        } else {
            $statut_paiement = 'partiel';
        }

        // Mettre à jour l'inscription
        $inscription->update([
            'niveau_id' => $validated['niveau_id'],
            'classe_id' => $validated['classe_id'],
            'montant_total' => $validated['montant_total'],
            'montant_paye' => $validated['montant_paye'],
            'mode_paiement' => $validated['mode_paiement'],
            'statut_paiement' => $statut_paiement,
            'statut' => $validated['statut'],
            'remarques' => $validated['remarques']
        ]);

        return redirect()
            ->route('inscriptions.index', ['tab' => 'liste-eleves'])
            ->with('success', 'Inscription mise à jour avec succès !');
    }

    /**
     * Annuler une inscription (remettre en pré-inscription)
     */
    public function annulerInscription(Inscription $inscription)
    {
        try {
            // Remettre la pré-inscription en statut "en_attente"
            $inscription->preInscription->update([
                'statut' => 'en_attente'
            ]);

            // Supprimer l'inscription
            $inscription->delete();

            return redirect()
                ->route('inscriptions.index', ['tab' => 'pre-inscription'])
                ->with('success', 'Inscription annulée avec succès ! L\'élève est remis en pré-inscription.');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de l\'annulation de l\'inscription : ' . $e->getMessage()]);
        }
    }

    /**
     * Supprimer définitivement une inscription
     */
    public function deleteInscription(Inscription $inscription)
    {
        try {
            $nomEleve = $inscription->preInscription->nom . ' ' . $inscription->preInscription->prenom;
            
            // Supprimer l'inscription (la pré-inscription reste)
            $inscription->delete();

            return redirect()
                ->route('inscriptions.index', ['tab' => 'liste-eleves'])
                ->with('success', 'Inscription de ' . $nomEleve . ' supprimée avec succès !');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la suppression : ' . $e->getMessage()]);
        }
    }

    /**
     * Afficher le reçu d'inscription
     */
    public function recu(Inscription $inscription): View
    {
        $inscription->load(['preInscription', 'niveau', 'classe']);
        
        return view('inscriptions.recu', compact('inscription'));
    }

    /**
     * Aperçu de la liste d'appel
     */
    public function listeAppel(Request $request)
    {
        $anneeScolaireActive = AnneeScolaire::getActive();
        $etablissement = Etablissement::first();
        
        // Construire la requête pour les inscriptions
        $query = Inscription::with(['preInscription', 'classe', 'classe.niveau'])
            ->where('statut', 'actif');
        
        if ($anneeScolaireActive) {
            $query->where('annee_scolaire_id', $anneeScolaireActive->id);
        }
        
        // Appliquer les filtres
        if ($request->search_admin) {
            $search = $request->search_admin;
            $query->whereHas('preInscription', function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('ine', 'like', "%{$search}%");
            })->orWhere('numero_recu', 'like', "%{$search}%");
        }
        
        if ($request->classe_admin) {
            $query->where('classe_id', $request->classe_admin);
        }
        
        $inscriptions = $query->orderBy('numero_recu')->get();
        
        // Organiser les données par niveau et classe
        $inscriptionsParNiveau = [];
        $totalEleves = 0;
        $totalGarcons = 0;
        $totalFilles = 0;
        $totalClasses = 0;
        $classesVues = [];
        
        foreach ($inscriptions as $inscription) {
            $niveauNom = $inscription->classe->niveau->nom ?? 'Niveau non défini';
            $classeNom = $inscription->classe->nom ?? 'Classe non définie';
            
            if (!isset($inscriptionsParNiveau[$niveauNom])) {
                $inscriptionsParNiveau[$niveauNom] = [];
            }
            
            if (!isset($inscriptionsParNiveau[$niveauNom][$classeNom])) {
                $inscriptionsParNiveau[$niveauNom][$classeNom] = [];
            }
            
            $inscriptionsParNiveau[$niveauNom][$classeNom][] = $inscription;
            
            // Calculs des statistiques
            $totalEleves++;
            if (($inscription->preInscription->sexe ?? '') === 'M') {
                $totalGarcons++;
            } else {
                $totalFilles++;
            }
            
            // Compter les classes uniques
            $classeKey = $inscription->classe_id;
            if (!in_array($classeKey, $classesVues)) {
                $classesVues[] = $classeKey;
                $totalClasses++;
            }
        }
        
        return view('inscriptions.liste-appel', [
            'inscriptionsParNiveau' => $inscriptionsParNiveau,
            'etablissement' => $etablissement,
            'anneeActive' => $anneeScolaireActive,
            'totalEleves' => $totalEleves,
            'totalGarcons' => $totalGarcons,
            'totalFilles' => $totalFilles,
            'totalClasses' => $totalClasses,
        ]);
    }

    /**
     * Générer le PDF de la liste d'appel
     */
    public function listeAdministrativePdf(Request $request)
    {
        $anneeScolaireActive = AnneeScolaire::getActive();
        $etablissement = Etablissement::first();
        
        // Construire la requête pour les inscriptions
        $query = Inscription::with(['preInscription', 'classe', 'classe.niveau'])
            ->where('statut', 'actif'); // Le statut correct est 'actif'
        
        if ($anneeScolaireActive) {
            $query->where('annee_scolaire_id', $anneeScolaireActive->id);
        }
        
        // Appliquer les filtres
        if ($request->search_admin) {
            $search = $request->search_admin;
            $query->whereHas('preInscription', function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%");
            })->orWhere('numero_recu', 'like', "%{$search}%");
        }
        
        if ($request->classe_admin) {
            $query->where('classe_id', $request->classe_admin);
        }
        
        $inscriptions = $query->orderBy('numero_recu')->get();
        
        // Obtenir les informations de niveau et classe
        $classeInfo = null;
        $niveauInfo = null;
        
        if ($request->classe_admin && $inscriptions->isNotEmpty()) {
            $classeInfo = $inscriptions->first()->classe->nom ?? null;
            $niveauInfo = $inscriptions->first()->classe->niveau->nom ?? null;
        }
        
        // Générer le PDF
        $pdf = PDF::loadView('inscriptions.liste-appel-pdf', [
            'inscriptions' => $inscriptions,
            'etablissement' => $etablissement,
            'anneeScolaire' => $anneeScolaireActive,
            'filters' => [
                'classe' => $classeInfo,
                'niveau' => $niveauInfo,
                'search' => $request->search_admin,
            ],
            'dateGeneration' => now(),
        ]);
        
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'liste-appel-' . ($anneeScolaireActive->libelle ?? 'current') . '-' . now()->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Export des inscriptions avec filtres
     */
    public function exportInscriptions(Request $request)
    {
        $anneeActive = AnneeScolaire::getActive();
        
        // Utiliser l'année sélectionnée ou l'année active par défaut
        $anneeId = $request->filter_annee ?? $anneeActive->id;
        $anneeSelectionnee = AnneeScolaire::find($anneeId) ?? $anneeActive;
        
        // Construire la query avec filtres - logique corrigée pour statuts
        if ($request->filter_statut === 'non_inscrits') {
            // Pour "non inscrits": récupérer les pré-inscriptions sans inscription
            $query = PreInscription::with(['inscription'])
                ->where('annee_scolaire_id', $anneeId)
                ->whereDoesntHave('inscription');
        } else {
            // Pour "inscrits" ou autres: récupérer les inscriptions
            $query = Inscription::with(['preInscription', 'niveau', 'classe'])
                ->where('annee_scolaire_id', $anneeId);
                
            if ($request->filter_statut === 'inscrits') {
                // Pas de filtre supplémentaire, toutes les inscriptions
            }
        }

        // Appliquer les autres filtres
        if ($request->search_eleves) {
            if ($request->filter_statut === 'non_inscrits') {
                $query->where(function($q) use ($request) {
                    $q->where('nom', 'LIKE', '%' . $request->search_eleves . '%')
                      ->orWhere('prenom', 'LIKE', '%' . $request->search_eleves . '%')
                      ->orWhere('ine', 'LIKE', '%' . $request->search_eleves . '%');
                });
            } else {
                $query->whereHas('preInscription', function($q) use ($request) {
                    $q->where('nom', 'LIKE', '%' . $request->search_eleves . '%')
                      ->orWhere('prenom', 'LIKE', '%' . $request->search_eleves . '%')
                      ->orWhere('ine', 'LIKE', '%' . $request->search_eleves . '%');
                });
            }
        }

        if ($request->filter_niveau) {
            if ($request->filter_statut === 'non_inscrits') {
                $query->where('niveau_id', $request->filter_niveau);
            } else {
                $query->where('niveau_id', $request->filter_niveau);
            }
        }

        if ($request->filter_classe) {
            if ($request->filter_statut === 'non_inscrits') {
                $query->where('classe_id', $request->filter_classe);
            } else {
                $query->where('classe_id', $request->filter_classe);
            }
        }

        $resultats = $query->orderBy('created_at', 'desc')->get();

        // Afficher l'aperçu de l'export
        return view('inscriptions.export', [
            'resultats' => $resultats,
            'anneeActive' => $anneeSelectionnee,
            'filtres' => $request->all(),
            'type_export' => $request->filter_statut === 'non_inscrits' ? 'non_inscrits' : 'inscrits'
        ]);
    }

    /**
     * API pour récupérer les élèves avec filtres avancés
     */

    /**
     * Génération des rapports d'inscriptions avec filtres
     */
    public function rapports(Request $request)
    {
        // Cette méthode ne fait que retourner la vue, comme dans le module mensualités
        // Les données sont récupérées via AJAX par getRapportsInscriptions()
        return view('inscriptions.rapports'); // Si cette vue existe, sinon on garde l'onglet
    }

    /**
     * Récupérer les données des rapports d'inscriptions (AJAX)
     * Structure exactement comme getRapportsMensualites() du module mensualités
     */
    public function getRapportsInscriptions(Request $request)
    {
        try {
            // Utiliser le premier établissement comme les autres méthodes
            $etablissement = Etablissement::first();
            $annee = $request->get('annee');
            $dateDebut = $request->get('dateDebut');
            $dateFin = $request->get('dateFin');
            $niveau = $request->get('niveau');
            $statut = $request->get('statut');

            // Base query avec relations - corrigé selon la vraie structure
            $query = \App\Models\Inscription::with([
                'preInscription',  
                'niveau', 
                'classe',
                'anneeScolaire'
            ]);

            // Filtrer par année scolaire
            if ($annee) {
                $query->where('annee_scolaire_id', $annee);
            } else {
                // Par défaut, année active
                $anneeActive = \App\Models\AnneeScolaire::getActive();
                if ($anneeActive) {
                    $query->where('annee_scolaire_id', $anneeActive->id);
                }
            }

            // Filtrer par niveau
            if ($niveau) {
                $query->where('niveau_id', $niveau);
            }

            // Filtrer par statut
            if ($statut) {
                switch ($statut) {
                    case 'complet':
                        $query->whereRaw('montant_paye >= montant_total');
                        break;
                    case 'partiel':
                        $query->where('montant_paye', '>', 0)
                              ->whereRaw('montant_paye < montant_total');
                        break;
                    case 'impaye':
                        $query->where('montant_paye', 0);
                        break;
                }
            }

            // Filtrer par période d'inscription
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

            $inscriptions = $query->get();

            if ($inscriptions->isEmpty()) {
                return response()->json(null);
            }

            // Calculs pour les totaux ADAPTÉS pour les inscriptions
            $totalEleves = $inscriptions->count();
            $montantTotal = $inscriptions->sum('montant_total');
            $montantPaye = $inscriptions->sum('montant_paye');
            $montantMoyen = $totalEleves > 0 ? $montantTotal / $totalEleves : 0;
            
            // Compter par statut de paiement
            $paiementsComplets = $inscriptions->filter(function($inscription) {
                return $inscription->montant_paye >= $inscription->montant_total;
            })->count();
            
            $paiementsPartiels = $inscriptions->filter(function($inscription) {
                return $inscription->montant_paye > 0 && $inscription->montant_paye < $inscription->montant_total;
            })->count();
            
            $nonPayes = $inscriptions->filter(function($inscription) {
                return $inscription->montant_paye == 0;
            })->count();
            
            // Calcul taux de conversion (inscriptions vs pré-inscriptions)
            $anneeId = $annee ?: \App\Models\AnneeScolaire::getActive()?->id;
            $totalPreInscriptions = \App\Models\PreInscription::where('annee_scolaire_id', $anneeId)->count();
            $tauxConversion = $totalPreInscriptions > 0 ? ($totalEleves / $totalPreInscriptions) * 100 : 0;

            $totaux = [
                'total_eleves' => $totalEleves,
                'inscriptions_validees' => $totalEleves, // Pour les inscriptions, on considère toutes comme validées
                'montant_total' => $montantTotal,
                'montant_paye' => $montantPaye,
                'montant_moyen' => round($montantMoyen),
                'taux_conversion' => round($tauxConversion, 1),
                'paiements_complets' => $paiementsComplets,
                'paiements_partiels' => $paiementsPartiels,
                'non_payes' => $nonPayes,
                // Structure mensualités pour compatibilité
                'eleves_total' => $totalEleves,
                'eleves_avec_mensualites' => $totalEleves,
                'mensualites_total' => $totalEleves,
                'montant_total_du' => $montantTotal,
                'montant_total_paye' => $montantPaye,
                'montant_restant' => $montantTotal - $montantPaye,
                'pourcentage_paiement' => $montantTotal > 0 ? round(($montantPaye / $montantTotal) * 100, 2) : 0
            ];

            // Statistiques par statut
            $parStatut = [
                'complet' => [
                    'count' => $paiementsComplets,
                    'montant_paye' => $inscriptions->filter(function($i) { return $i->montant_paye >= $i->montant_total; })->sum('montant_paye')
                ],
                'partiel' => [
                    'count' => $paiementsPartiels,
                    'montant_paye' => $inscriptions->filter(function($i) { return $i->montant_paye > 0 && $i->montant_paye < $i->montant_total; })->sum('montant_paye')
                ],
                'impaye' => [
                    'count' => $nonPayes,
                    'montant_du' => $inscriptions->filter(function($i) { return $i->montant_paye == 0; })->sum('montant_total')
                ]
            ];

            $totaux['par_statut'] = $parStatut;

            // Préparer les détails pour le tableau
            $details = $inscriptions->map(function ($inscription) {
                return [
                    'id' => $inscription->id,
                    'nom_complet' => $inscription->preInscription->nom . ' ' . $inscription->preInscription->prenom,
                    'ine' => $inscription->preInscription->ine,
                    'niveau' => $inscription->niveau->nom ?? 'N/A',
                    'classe' => $inscription->classe->nom ?? 'N/A',
                    'date_inscription' => $inscription->created_at->format('d/m/Y'),
                    'montant_total' => $inscription->montant_total,
                    'montant_paye' => $inscription->montant_paye,
                    'mode_paiement' => $inscription->mode_paiement ?? 'N/A',
                    'date_paiement' => $inscription->created_at->format('d/m/Y'),
                    'statut_paiement' => $inscription->montant_paye >= $inscription->montant_total ? 'Complet' : 
                                        ($inscription->montant_paye > 0 ? 'Partiel' : 'Non payé'),
                    'numero_recu' => $inscription->numero_recu,
                    // Format mensualités pour compatibilité
                    'eleve' => [
                        'nom' => $inscription->preInscription->nom,
                        'prenom' => $inscription->preInscription->prenom,
                        'ine' => $inscription->preInscription->ine,
                        'classe' => $inscription->classe->nom ?? 'N/A'
                    ],
                    'mois_paiement' => 'Inscription ' . $inscription->created_at->format('m/Y'),
                    'montant_du' => $inscription->montant_total,
                    'solde_restant' => $inscription->montant_total - $inscription->montant_paye,
                    'date_paiement' => $inscription->created_at->format('d/m/Y'),
                    'statut' => $inscription->montant_paye >= $inscription->montant_total ? 'complet' : 
                               ($inscription->montant_paye > 0 ? 'partiel' : 'impaye')
                ];
            });

            // Structure de retour EXACTEMENT comme mensualités
            return response()->json([
                'totaux' => $totaux,
                'details' => $details,
                // Compatibilité avec structure mensualités
                'eleves_total' => $totaux['total_eleves'],
                'eleves_avec_mensualites' => $totaux['total_eleves'],
                'mensualites_total' => $totaux['total_eleves'], 
                'montant_total_du' => $totaux['montant_total'],
                'montant_total_paye' => $totaux['montant_paye'],
                'montant_restant' => $totaux['montant_total'] - $totaux['montant_paye'],
                'pourcentage_paiement' => $totaux['pourcentage_paiement'],
                'par_statut' => $parStatut
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors du chargement des rapports d\'inscriptions: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors du chargement des rapports d\'inscriptions'], 500);
        }
    }
    
    /**
     * Aperçu des rapports d'inscriptions
     */
    public function rapportsApercu(Request $request)
    {
        try {
            $annee = $request->get('annee');
            $dateDebut = $request->get('dateDebut');
            $dateFin = $request->get('dateFin');
            $niveau = $request->get('niveau');
            $statut = $request->get('statut');

            // Même logique que getRapportsInscriptions
            $query = Inscription::with(['preInscription', 'niveau', 'classe', 'anneeScolaire']);

            if ($annee) {
                $query->where('annee_scolaire_id', $annee);
            } else {
                $anneeActive = AnneeScolaire::getActive();
                if ($anneeActive) {
                    $query->where('annee_scolaire_id', $anneeActive->id);
                }
            }

            if ($niveau) $query->where('niveau_id', $niveau);

            if ($statut) {
                switch ($statut) {
                    case 'complet':
                        $query->whereRaw('montant_paye >= montant_total');
                        break;
                    case 'partiel':
                        $query->where('montant_paye', '>', 0)
                              ->whereRaw('montant_paye < montant_total');
                        break;
                    case 'impaye':
                        $query->where('montant_paye', 0);
                        break;
                }
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

            $inscriptions = $query->orderBy('created_at', 'desc')->get();

            if ($inscriptions->isEmpty()) {
                abort(404, 'Aucune inscription trouvée pour les critères sélectionnés');
            }

            // Calculer les totaux (format complet pour la vue)
            $totalEleves = $inscriptions->count();
            $montantTotal = $inscriptions->sum('montant_total');
            $montantPaye = $inscriptions->sum('montant_paye');
            $montantMoyen = $totalEleves > 0 ? $montantTotal / $totalEleves : 0;
            
            // Calculer taux de conversion (inscriptions vs pré-inscriptions)
            $anneeId = $annee ?: AnneeScolaire::getActive()?->id;
            $totalPreInscriptions = \App\Models\PreInscription::where('annee_scolaire_id', $anneeId)->count();
            $tauxConversion = $totalPreInscriptions > 0 ? ($totalEleves / $totalPreInscriptions) * 100 : 0;

            $totaux = [
                'total_eleves' => $totalEleves,
                'inscriptions_validees' => $totalEleves, // Pour les inscriptions, toutes sont validées
                'montant_total' => $montantTotal,
                'montant_paye' => $montantPaye,
                'montant_moyen' => round($montantMoyen),
                'montant_restant' => $montantTotal - $montantPaye,
                'taux_conversion' => round($tauxConversion, 1),
                'pourcentage_paiement' => $montantTotal > 0 
                    ? round(($montantPaye / $montantTotal) * 100, 2)
                    : 0
            ];

            // Préparer les détails pour le tableau
            $details = $inscriptions->map(function ($inscription) {
                return [
                    'nom_complet' => $inscription->preInscription->prenom . ' ' . $inscription->preInscription->nom,
                    'ine' => $inscription->preInscription->ine,
                    'niveau' => $inscription->niveau->nom ?? 'N/A',
                    'classe' => $inscription->classe->nom ?? 'N/A',
                    'date_inscription' => $inscription->created_at->format('d/m/Y'),
                    'montant_total' => $inscription->montant_total,
                    'montant_paye' => $inscription->montant_paye,
                    'mode_paiement' => $inscription->mode_paiement ?? 'N/A',
                    'statut_paiement' => $inscription->montant_paye >= $inscription->montant_total ? 'Complet' : 
                                        ($inscription->montant_paye > 0 ? 'Partiel' : 'Non payé'),
                    'numero_recu' => $inscription->numero_recu
                ];
            });

            // Préparer les graphiques (statistiques)
            $graphiques = [
                'niveaux' => $inscriptions->groupBy(function($inscription) {
                    return $inscription->niveau->nom ?? 'N/A';
                })->map(function($group, $niveau) {
                    return [
                        'niveau' => $niveau,
                        'count' => $group->count(),
                        'montant' => $group->sum('montant_total')
                    ];
                })->values(),
                
                'modes_paiement' => $inscriptions->groupBy(function($inscription) {
                    return $inscription->mode_paiement ?? 'N/A';
                })->map(function($group, $mode) {
                    return [
                        'mode' => $mode,
                        'count' => $group->count(),
                        'montant' => $group->sum('montant_paye')
                    ];
                })->values()
            ];

            // Informations sur les filtres pour l'affichage
            $filtres = [
                'annee' => $annee,
                'dateDebut' => $dateDebut,
                'dateFin' => $dateFin,
                'niveau' => $niveau,
                'statut' => $statut
            ];

            // Récupérer l'établissement et les niveaux pour l'en-tête
            $etablissement = \App\Models\Etablissement::first();
            $niveaux = \App\Models\Niveau::with('classes')->get();

            return view('inscriptions.apercu_rapports', compact('inscriptions', 'totaux', 'filtres', 'details', 'graphiques', 'etablissement', 'niveaux'));
        } catch (\Exception $e) {
            abort(500, 'Erreur lors de la génération de l\'aperçu des rapports d\'inscriptions');
        }
    }
    
    /**
     * Données d'évolution mensuelle
     */
    private function getEvolutionMensuelle($inscriptions)
    {
        return $inscriptions->groupBy(function($inscription) {
            return $inscription->date_inscription->format('Y-m');
        })->map(function($group) {
            return $group->count();
        });
    }
    
    /**
     * Répartition par niveau
     */
    private function getRepartitionNiveau($inscriptions)
    {
        return $inscriptions->groupBy(function($inscription) {
            return $inscription->niveau->nom ?? 'Non défini';
        })->map(function($group, $niveau) {
            return [
                'niveau' => $niveau,
                'count' => $group->count(),
                'montant' => $group->sum('montant_paye')
            ];
        })->values();
    }
    
    /**
     * Répartition par mode de paiement
     */
    private function getModesPaiement($inscriptions)
    {
        return $inscriptions->groupBy('mode_paiement')->map(function($group, $mode) {
            return [
                'mode' => ucfirst(str_replace('_', ' ', $mode)),
                'count' => $group->count(),
                'montant' => $group->sum('montant_paye')
            ];
        })->values();
    }
    
    /**
     * Répartition par statut de paiement
     */
    private function getStatutsPaiement($inscriptions)
    {
        return $inscriptions->groupBy('statut_paiement')->map(function($group, $statut) {
            return [
                'statut' => ucfirst($statut),
                'count' => $group->count(),
                'montant' => $group->sum('montant_paye')
            ];
        })->values();
    }

    /**
     * Récupérer les années scolaires pour les filtres
     * EXACTEMENT comme dans MensualiteController
     */
    public function anneesScolaires()
    {
        try {
            $annees = \App\Models\AnneeScolaire::orderBy('date_debut', 'desc')->get()->map(function($annee) {
                return [
                    'valeur' => $annee->id,
                    'libelle' => $annee->libelle,
                    'actuelle' => (bool) $annee->actif // S'assurer que c'est un boolean
                ];
            });

            return response()->json($annees);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du chargement des années scolaires'], 500);
        }
    }

    /**
     * Récupérer les niveaux pour les filtres
     */
    public function niveaux()
    {
        // Utiliser tous les niveaux comme dans la méthode index()
        $niveaux = \App\Models\Niveau::with('classes')->get();
        
        return response()->json($niveaux->map(function($niveau) {
            return [
                'id' => $niveau->id,
                'nom' => $niveau->nom
            ];
        }));
    }

    /**
     * Calculer les totaux des inscriptions
     */
    private function calculateTotals($inscriptions)
    {
        $totalEleves = $inscriptions->count();
        $montantTotal = $inscriptions->sum('montant_total');
        $montantPaye = $inscriptions->sum('montant_paye');
        $montantMoyen = $totalEleves > 0 ? $montantTotal / $totalEleves : 0;
        
        // Compter par statut de paiement
        $paiementsComplets = $inscriptions->filter(function($inscription) {
            return $inscription->montant_paye >= $inscription->montant_total;
        })->count();
        
        $paiementsPartiels = $inscriptions->filter(function($inscription) {
            return $inscription->montant_paye > 0 && $inscription->montant_paye < $inscription->montant_total;
        })->count();
        
        $nonPayes = $inscriptions->filter(function($inscription) {
            return $inscription->montant_paye == 0;
        })->count();
        
        // Taux de conversion
        $etablissement = auth()->user()->etablissement;
        $totalPreInscriptions = \App\Models\PreInscription::where('etablissement_id', $etablissement->id)->count();
        $tauxConversion = $totalPreInscriptions > 0 ? ($totalEleves / $totalPreInscriptions) * 100 : 0;

        return [
            'total_eleves' => $totalEleves,
            'inscriptions_validees' => $totalEleves, // Pour l'instant, on considère toutes les inscriptions comme validées
            'montant_total' => $montantTotal,
            'montant_paye' => $montantPaye,
            'montant_moyen' => round($montantMoyen),
            'taux_conversion' => round($tauxConversion, 1),
            'paiements_complets' => $paiementsComplets,
            'paiements_partiels' => $paiementsPartiels,
            'non_payes' => $nonPayes
        ];
    }

    /**
     * Afficher la fiche complète d'un élève
     */
    public function ficheEleve($inscriptionId)
    {
        $inscription = Inscription::with([
            'preInscription',
            'niveau', 
            'classe',
            'anneeScolaire',
            'mensualites' => function($query) {
                $query->orderByRaw("FIELD(mois_paiement, 'octobre', 'novembre', 'decembre', 'janvier', 'fevrier', 'mars', 'avril', 'mai', 'juin', 'juillet')");
            }
        ])->findOrFail($inscriptionId);

        // Statistiques des paiements mensuels
        $totalMensualites = $inscription->mensualites->count();
        $mensualitesPayees = $inscription->mensualites->where('statut', 'complet')->count();
        $mensualitesPartielles = $inscription->mensualites->where('statut', 'partiel')->count();
        $mensualitesImpayes = $inscription->mensualites->where('statut', 'impaye')->count();

        $montantTotalMensualites = $inscription->mensualites->sum('montant_du');
        $montantPayeMensualites = $inscription->mensualites->sum('montant_paye');
        $soldeRestantMensualites = $montantTotalMensualites - $montantPayeMensualites;

        return view('inscriptions.fiche-eleve', compact(
            'inscription',
            'totalMensualites',
            'mensualitesPayees', 
            'mensualitesPartielles',
            'mensualitesImpayes',
            'montantTotalMensualites',
            'montantPayeMensualites',
            'soldeRestantMensualites'
        ));
    }

}
