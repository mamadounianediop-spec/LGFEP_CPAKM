<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use App\Models\Niveau;
use App\Models\Classe;
use App\Models\Frais;
use App\Models\User;
use App\Models\AnneeScolaire;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class ParametresController extends Controller
{
    /**
     * Display the parametres index.
     */
    public function index(Request $request): View
    {
        $activeTab = $request->get('tab', 'etablissement');
        
        $etablissement = Etablissement::first();
        $niveaux = Niveau::with('classes')->get();
        $anneeActive = AnneeScolaire::getActive();
        // Afficher tous les frais actifs, pas seulement ceux de l'année courante
        $frais = Frais::with('niveau')->where('actif', true)->get();
        $users = User::where('actif', true)->get();
        $anneesScolaires = AnneeScolaire::orderBy('date_debut', 'desc')->get();

        return view('parametres.index', compact('etablissement', 'niveaux', 'frais', 'users', 'anneesScolaires', 'anneeActive', 'activeTab'));
    }

    /**
     * Store a new etablissement.
     */
    public function storeEtablissement(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'responsable' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Etablissement::create($validated);

        return redirect()->route('parametres.index')->with('success', 'Informations de l\'établissement enregistrées avec succès.');
    }

    /**
     * Update etablissement.
     */
    public function updateEtablissement(Request $request, Etablissement $etablissement): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'ninea' => 'nullable|string|max:50',
            'responsable' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $etablissement->update($validated);

        return redirect()->route('parametres.index')->with('success', 'Informations de l\'établissement mises à jour avec succès.');
    }

    /**
     * Store a new niveau.
     */
    public function storeNiveau(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100|unique:niveaux',
            'description' => 'nullable|string',
        ]);

        // Générer un code automatiquement basé sur le nom
        $code = $this->generateNiveauCode($validated['nom']);
        $validated['code'] = $code;

        Niveau::create($validated);

        return redirect()->route('parametres.index')->with('success', 'Niveau ajouté avec succès.');
    }

    /**
     * Edit niveau.
     */
    public function editNiveau(Niveau $niveau)
    {
        return response()->json([
            'id' => $niveau->id,
            'nom' => $niveau->nom,
            'description' => $niveau->description,
        ]);
    }

    /**
     * Update niveau.
     */
    public function updateNiveau(Request $request, Niveau $niveau): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:100', Rule::unique('niveaux')->ignore($niveau->id)],
            'description' => 'nullable|string',
        ]);

        // Si le nom change, régénérer le code
        if ($validated['nom'] !== $niveau->nom) {
            $code = $this->generateNiveauCode($validated['nom']);
            $validated['code'] = $code;
        }

        $niveau->update($validated);

        return redirect()->route('parametres.index')->with('success', 'Niveau mis à jour avec succès.');
    }

    /**
     * Delete niveau.
     */
    public function destroyNiveau(Niveau $niveau): RedirectResponse
    {
        if ($niveau->classes()->count() > 0) {
            return redirect()->route('parametres.index')->with('error', 'Impossible de supprimer ce niveau car il contient des classes.');
        }

        $niveau->delete();

        return redirect()->route('parametres.index')->with('success', 'Niveau supprimé avec succès.');
    }

    /**
     * Store a new classe.
     */
    public function storeClasse(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'niveau_id' => 'required|exists:niveaux,id',
            'effectif_max' => 'nullable|integer|min:0',
        ]);

        // Récupérer le niveau pour générer le code
        $niveau = Niveau::findOrFail($validated['niveau_id']);
        
        // Générer un code unique basé sur le niveau et le nom de la classe
        $codeBase = strtoupper(substr($niveau->nom, 0, 3)) . '-' . strtoupper($validated['nom']);
        $code = $codeBase;
        $counter = 1;
        
        // S'assurer que le code est unique
        while (Classe::where('code', $code)->exists()) {
            $code = $codeBase . '-' . $counter;
            $counter++;
        }
        
        $validated['code'] = $code;

        Classe::create($validated);

        return redirect()->route('parametres.index')->with('success', 'Classe ajoutée avec succès.');
    }

    /**
     * Update classe.
     */
    public function updateClasse(Request $request, Classe $classe): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'niveau_id' => 'required|exists:niveaux,id',
            'effectif_max' => 'nullable|integer|min:0',
        ]);

        // Si le nom ou le niveau change, régénérer le code
        if ($validated['nom'] !== $classe->nom || $validated['niveau_id'] !== $classe->niveau_id) {
            $niveau = Niveau::findOrFail($validated['niveau_id']);
            
            // Générer un nouveau code unique
            $codeBase = strtoupper(substr($niveau->nom, 0, 3)) . '-' . strtoupper($validated['nom']);
            $code = $codeBase;
            $counter = 1;
            
            // S'assurer que le code est unique (exclure la classe actuelle)
            while (Classe::where('code', $code)->where('id', '!=', $classe->id)->exists()) {
                $code = $codeBase . '-' . $counter;
                $counter++;
            }
            
            $validated['code'] = $code;
        }

        $classe->update($validated);

        return redirect()->route('parametres.index')->with('success', 'Classe mise à jour avec succès.');
    }

    /**
     * Delete classe.
     */
    public function destroyClasse(Classe $classe): RedirectResponse
    {
        $classe->delete();

        return redirect()->route('parametres.index')->with('success', 'Classe supprimée avec succès.');
    }

    /**
     * Store new frais.
     */
    public function storeFrais(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type_frais' => 'required|in:inscription,mensualite,transport,cantine,examen',
            'montant' => 'required|numeric|min:0',
            'niveau_id' => 'nullable|exists:niveaux,id',
            'actif' => 'boolean',
        ]);

        // Adapter les données pour le modèle
        $fraisData = [
            'type' => $validated['type_frais'],
            'nom' => ucfirst($validated['type_frais']),
            'montant' => $validated['montant'],
            'niveau_id' => $validated['niveau_id'],
            'actif' => $request->has('actif'),
            'annee_scolaire_id' => AnneeScolaire::getActive()->id,
        ];

        Frais::create($fraisData);

        return redirect()->route('parametres.index')->with('success', 'Frais ajouté avec succès.');
    }

    /**
     * Update frais.
     */
    public function updateFrais(Request $request, Frais $frais): RedirectResponse
    {
        $validated = $request->validate([
            'type_frais' => 'required|in:inscription,mensualite,transport,cantine,examen',
            'montant' => 'required|numeric|min:0',
            'niveau_id' => 'nullable|exists:niveaux,id',
            'actif' => 'boolean',
        ]);

        // Adapter les données pour le modèle
        $fraisData = [
            'type' => $validated['type_frais'],
            'nom' => ucfirst($validated['type_frais']),
            'montant' => $validated['montant'],
            'niveau_id' => $validated['niveau_id'],
            'actif' => $request->has('actif'),
        ];

        $frais->update($fraisData);

        return redirect()->route('parametres.index', ['tab' => 'frais'])
                        ->with('success', 'Frais mis à jour avec succès.');
    }

    /**
     * Delete frais.
     */
    public function destroyFrais(Frais $frais): RedirectResponse
    {
        $frais->delete();

        return redirect()->route('parametres.index', ['tab' => 'frais'])
                        ->with('success', 'Frais supprimé avec succès.');
    }

    /**
     * Store new user.
     */
    public function storeUser(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:administrateur,directeur,secretaire,surveillant',
            'actif' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['actif'] = $request->has('actif');

        User::create($validated);

        return redirect()->route('parametres.index')->with('success', 'Utilisateur ajouté avec succès.');
    }

    /**
     * Update user.
     */
    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:administrateur,directeur,secretaire,surveillant',
            'actif' => 'boolean',
        ]);

        if ($validated['password']) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['actif'] = $request->has('actif');

        $user->update($validated);

        return redirect()->route('parametres.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Delete user.
     */
    public function destroyUser(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('parametres.index')->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        // Protéger l'utilisateur admin
        if ($user->email === 'admin@cpakm.sn') {
            return redirect()->route('parametres.index')->with('error', 'L\'utilisateur administrateur ne peut pas être supprimé.');
        }

        $user->delete();

        return redirect()->route('parametres.index')->with('success', 'Utilisateur supprimé avec succès.');
    }

    /**
     * Store new année scolaire.
     */
    public function storeAnneeScolaire(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'libelle' => 'required|string|max:255|unique:annee_scolaires',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'description' => 'nullable|string',
        ]);

        AnneeScolaire::create($validated);

        return redirect()->route('parametres.index')->with('success', 'Année scolaire ajoutée avec succès.');
    }

    /**
     * Update année scolaire.
     */
    public function updateAnneeScolaire(Request $request, AnneeScolaire $anneeScolaire): RedirectResponse
    {
        $validated = $request->validate([
            'libelle' => ['required', 'string', 'max:255', Rule::unique('annee_scolaires')->ignore($anneeScolaire->id)],
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'description' => 'nullable|string',
        ]);

        $anneeScolaire->update($validated);

        return redirect()->route('parametres.index')->with('success', 'Année scolaire mise à jour avec succès.');
    }

    /**
     * Delete année scolaire.
     */
    public function destroyAnneeScolaire(AnneeScolaire $anneeScolaire): RedirectResponse
    {
        // Vérifier s'il y a des données liées
        if ($anneeScolaire->preInscriptions()->count() > 0 || 
            $anneeScolaire->inscriptions()->count() > 0 || 
            $anneeScolaire->frais()->count() > 0) {
            return redirect()->route('parametres.index')->with('error', 'Impossible de supprimer cette année scolaire car elle contient des données.');
        }

        $anneeScolaire->delete();

        return redirect()->route('parametres.index')->with('success', 'Année scolaire supprimée avec succès.');
    }

    /**
     * Activer année scolaire.
     */
    public function activerAnneeScolaire(AnneeScolaire $anneeScolaire)
    {
        try {
            $anneeScolaire->activer();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Generate a unique code for niveau based on its name
     */
    private function generateNiveauCode(string $nom): string
    {
        // Mapping des noms courants vers des codes standard
        $mapping = [
            'Cours d\'Initiation' => 'CI',
            'Cours Préparatoire' => 'CP',
            'Cours Élémentaire 1' => 'CE1',
            'Cours Élémentaire 2' => 'CE2',
            'Cours Moyen 1' => 'CM1',
            'Cours Moyen 2' => 'CM2',
            'Sixième' => '6EME',
            'Cinquième' => '5EME',
            'Quatrième' => '4EME',
            'Troisième' => '3EME',
            'Seconde' => '2NDE',
            'Première' => '1ERE',
            'Terminale' => 'TERM',
            'Maternelle' => 'MAT',
            'Primaire' => 'PRIM',
            'Collège' => 'COLL',
            'Lycée' => 'LYC'
        ];

        // Chercher d'abord dans le mapping
        foreach ($mapping as $key => $value) {
            if (stripos($nom, $key) !== false) {
                $baseCode = $value;
                break;
            }
        }

        // Si pas trouvé dans le mapping, générer un code basé sur les premières lettres
        if (!isset($baseCode)) {
            $words = explode(' ', strtoupper($nom));
            if (count($words) > 1) {
                // Prendre la première lettre de chaque mot
                $baseCode = '';
                foreach ($words as $word) {
                    if (strlen($word) > 0) {
                        $baseCode .= substr($word, 0, 1);
                    }
                }
                // Limiter à 4 caractères maximum
                $baseCode = substr($baseCode, 0, 4);
            } else {
                // Prendre les 4 premières lettres du mot
                $baseCode = substr(strtoupper($nom), 0, 4);
            }
        }

        // S'assurer que le code est unique
        $code = $baseCode;
        $counter = 1;
        
        while (Niveau::where('code', $code)->exists()) {
            $code = $baseCode . $counter;
            $counter++;
        }

        return $code;
    }
}