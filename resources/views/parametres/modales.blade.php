<!-- Modal Ajouter Niveau -->
<div id="addNiveauModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Ajouter un Niveau</h3>
                <button onclick="closeModal('addNiveauModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form action="{{ route('parametres.niveaux.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="niveau_nom" class="block text-sm font-medium text-gray-700">Nom du niveau</label>
                    <input type="text" name="nom" id="niveau_nom" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Ex: Cours d'initiation, CP, CE1...">
                </div>
                
                <div>
                    <label for="niveau_description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="niveau_description" rows="2"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Description du niveau (optionnel)"></textarea>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeModal('addNiveauModal')" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 text-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifier Niveau -->
<div id="editNiveauModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Modifier le Niveau</h3>
                <button onclick="closeModal('editNiveauModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="editNiveauForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="edit_niveau_nom" class="block text-sm font-medium text-gray-700">Nom du niveau</label>
                    <input type="text" name="nom" id="edit_niveau_nom" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label for="edit_niveau_description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="edit_niveau_description" rows="2"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeModal('editNiveauModal')" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 text-sm hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ajouter Classe -->
<div id="addClasseModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Ajouter une Classe</h3>
                <button onclick="closeModal('addClasseModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form action="{{ route('parametres.classes.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="classe_nom" class="block text-sm font-medium text-gray-700">Nom de la classe</label>
                    <input type="text" name="nom" id="classe_nom" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Ex: CP A, CE1 B...">
                </div>
                
                <div>
                    <label for="niveau_id" class="block text-sm font-medium text-gray-700">Niveau</label>
                    <select name="niveau_id" id="niveau_id" required 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Sélectionner un niveau</option>
                        @if(isset($niveaux))
                            @foreach($niveaux as $niveau)
                                <option value="{{ $niveau->id }}">{{ $niveau->nom }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                
                <div>
                    <label for="classe_effectif" class="block text-sm font-medium text-gray-700">Effectif</label>
                    <input type="number" name="effectif" id="classe_effectif" min="0" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Nombre d'élèves">
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeModal('addClasseModal')" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 text-sm hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ajouter Niveau -->
<div id="addNiveauModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Ajouter un Niveau</h3>
                <button onclick="closeModal('addNiveauModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form action="{{ route('parametres.niveaux.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="niveau_nom" class="block text-sm font-medium text-gray-700">Nom du niveau</label>
                    <input type="text" name="nom" id="niveau_nom" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Ex: Maternelle, Primaire, Collège...">
                </div>
                
                <div>
                    <label for="niveau_description" class="block text-sm font-medium text-gray-700">Description (optionnel)</label>
                    <textarea name="description" id="niveau_description" rows="2"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Description du niveau..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeModal('addNiveauModal')" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 text-sm hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifier Niveau -->
<div id="editNiveauModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Modifier le Niveau</h3>
                <button onclick="closeModal('editNiveauModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="editNiveauForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_niveau_id">
                
                <div>
                    <label for="edit_niveau_nom" class="block text-sm font-medium text-gray-700">Nom du niveau</label>
                    <input type="text" name="nom" id="edit_niveau_nom" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label for="edit_niveau_description" class="block text-sm font-medium text-gray-700">Description (optionnel)</label>
                    <textarea name="description" id="edit_niveau_description" rows="2"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeModal('editNiveauModal')" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 text-sm hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                        Modifier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ajouter Classe -->
<div id="addClasseModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Ajouter une Classe</h3>
                <button onclick="closeModal('addClasseModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-1"></i>
                    Niveau: <span id="niveau_nom_display" class="font-medium"></span>
                </p>
            </div>
            
            <form action="{{ route('parametres.classes.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="niveau_id" id="classe_niveau_id">
                
                <div>
                    <label for="classe_nom" class="block text-sm font-medium text-gray-700">Nom de la classe</label>
                    <input type="text" name="nom" id="classe_nom" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Ex: CP1, CE1, 6ème A...">
                </div>
                
                <div>
                    <label for="classe_effectif_max" class="block text-sm font-medium text-gray-700">Effectif maximum (optionnel)</label>
                    <input type="number" name="effectif_max" id="classe_effectif_max" min="1"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Ex: 30">
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeModal('addClasseModal')" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 text-sm hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifier Classe -->
<div id="editClasseModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Modifier la Classe</h3>
                <button onclick="closeModal('editClasseModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="editClasseForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_classe_id">
                <input type="hidden" name="niveau_id" id="edit_classe_niveau_id">
                
                <div>
                    <label for="edit_classe_nom" class="block text-sm font-medium text-gray-700">Nom de la classe</label>
                    <input type="text" name="nom" id="edit_classe_nom" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label for="edit_classe_effectif_max" class="block text-sm font-medium text-gray-700">Effectif maximum (optionnel)</label>
                    <input type="number" name="effectif_max" id="edit_classe_effectif_max" min="1"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeModal('editClasseModal')" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 text-sm hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                        Modifier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ajouter Frais -->
<div id="addFraisModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Ajouter des Frais</h3>
                <button onclick="closeModal('addFraisModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form action="{{ route('parametres.frais.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="type_frais" class="block text-sm font-medium text-gray-700">Type de frais</label>
                    <select name="type_frais" id="type_frais" required 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Sélectionner le type</option>
                        <option value="inscription">Inscription</option>
                        <option value="mensualite">Mensualité</option>
                        <option value="transport">Transport</option>
                        <option value="cantine">Cantine</option>
                        <option value="examen">Examen</option>
                    </select>
                </div>
                
                <div>
                    <label for="frais_montant" class="block text-sm font-medium text-gray-700">Montant (FCFA)</label>
                    <input type="number" name="montant" id="frais_montant" required min="0" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Montant en FCFA">
                </div>
                
                <div>
                    <label for="frais_niveau_id" class="block text-sm font-medium text-gray-700">Niveau</label>
                    <select name="niveau_id" id="frais_niveau_id" 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Tous les niveaux</option>
                        @if(isset($niveaux))
                            @foreach($niveaux as $niveau)
                                <option value="{{ $niveau->id }}">{{ $niveau->nom }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="actif" id="frais_actif" value="1" checked
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="frais_actif" class="ml-2 block text-sm text-gray-900">
                        Actif
                    </label>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeModal('addFraisModal')" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 text-sm hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifier Frais -->
<div id="editFraisModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Modifier les Frais</h3>
                <button onclick="closeModal('editFraisModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form action="" method="POST" id="editFraisForm" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="edit_type_frais" class="block text-sm font-medium text-gray-700">Type de frais</label>
                    <select name="type_frais" id="edit_type_frais" required 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Sélectionner le type</option>
                        <option value="inscription">Inscription</option>
                        <option value="mensualite">Mensualité</option>
                        <option value="transport">Transport</option>
                        <option value="cantine">Cantine</option>
                        <option value="examen">Examen</option>
                    </select>
                </div>
                
                <div>
                    <label for="edit_frais_montant" class="block text-sm font-medium text-gray-700">Montant (FCFA)</label>
                    <input type="number" name="montant" id="edit_frais_montant" required min="0" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Montant en FCFA">
                </div>
                
                <div>
                    <label for="edit_frais_niveau_id" class="block text-sm font-medium text-gray-700">Niveau</label>
                    <select name="niveau_id" id="edit_frais_niveau_id" 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Tous les niveaux</option>
                        @if(isset($niveaux))
                            @foreach($niveaux as $niveau)
                                <option value="{{ $niveau->id }}">{{ $niveau->nom }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="actif" id="edit_frais_actif" value="1"
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="edit_frais_actif" class="ml-2 block text-sm text-gray-900">
                        Actif
                    </label>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeModal('editFraisModal')" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 text-sm hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                        Modifier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ajouter Utilisateur -->
<div id="addUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Ajouter un Utilisateur</h3>
                <button onclick="closeModal('addUserModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form action="{{ route('parametres.users.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="user_name" class="block text-sm font-medium text-gray-700">Nom complet</label>
                    <input type="text" name="name" id="user_name" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Nom et prénom">
                </div>
                
                <div>
                    <label for="user_email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="user_email" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="email@exemple.com">
                </div>
                
                <div>
                    <label for="user_password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                    <input type="password" name="password" id="user_password" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Minimum 6 caractères">
                </div>
                
                <div>
                    <label for="user_password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer</label>
                    <input type="password" name="password_confirmation" id="user_password_confirmation" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Confirmer le mot de passe">
                </div>
                
                <div>
                    <label for="user_role" class="block text-sm font-medium text-gray-700">Rôle</label>
                    <select name="role" id="user_role" required 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Sélectionner un rôle</option>
                        <option value="administrateur">Administrateur</option>
                        <option value="directeur">Directeur</option>
                        <option value="secretaire">Secrétaire</option>
                        <option value="surveillant">Surveillant</option>
                    </select>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="actif" id="user_actif" value="1" checked
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="user_actif" class="ml-2 block text-sm text-gray-900">
                        Compte actif
                    </label>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeModal('addUserModal')" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 text-sm hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifier Utilisateur -->
<div id="editUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Modifier l'Utilisateur</h3>
                <button onclick="closeModal('editUserModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="editUserForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="edit_user_name" class="block text-sm font-medium text-gray-700">Nom complet</label>
                    <input type="text" name="name" id="edit_user_name" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Nom et prénom">
                </div>
                
                <div>
                    <label for="edit_user_email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="edit_user_email" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="email@exemple.com">
                </div>
                
                <div>
                    <label for="edit_user_password" class="block text-sm font-medium text-gray-700">Mot de passe <small>(laisser vide si inchangé)</small></label>
                    <input type="password" name="password" id="edit_user_password" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Nouveau mot de passe (optionnel)">
                </div>
                
                <div>
                    <label for="edit_user_password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer</label>
                    <input type="password" name="password_confirmation" id="edit_user_password_confirmation" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Confirmer le nouveau mot de passe">
                </div>
                
                <div>
                    <label for="edit_user_role" class="block text-sm font-medium text-gray-700">Rôle</label>
                    <select name="role" id="edit_user_role" required 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Sélectionner un rôle</option>
                        <option value="administrateur">Administrateur</option>
                        <option value="directeur">Directeur</option>
                        <option value="secretaire">Secrétaire</option>
                        <option value="surveillant">Surveillant</option>
                    </select>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="actif" id="edit_user_actif" value="1"
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="edit_user_actif" class="ml-2 block text-sm text-gray-900">
                        Compte actif
                    </label>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeModal('editUserModal')" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 text-sm hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmation de Suppression -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-sm shadow-lg rounded-lg bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Confirmer la suppression</h3>
            <p class="text-sm text-gray-500 mb-6">Cette action est irréversible. Êtes-vous sûr ?</p>
            
            <div class="flex justify-center space-x-3">
                <button onclick="closeModal('deleteModal')" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 text-sm rounded-md hover:bg-gray-400">
                    Annuler
                </button>
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700">
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajouter Année Scolaire -->
<div id="addAnneeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Nouvelle Année Scolaire</h3>
                    <button onclick="closeModal('addAnneeModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <form method="POST" action="{{ route('parametres.annees-scolaires.store') }}" class="p-6">
                @csrf
                
                <div class="space-y-4">
                    <div>
                        <label for="libelle" class="block text-sm font-medium text-gray-700">Libellé *</label>
                        <input type="text" name="libelle" id="libelle" required
                               placeholder="Ex: 2024-2025"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('libelle')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="date_debut" class="block text-sm font-medium text-gray-700">Date de début *</label>
                            <input type="date" name="date_debut" id="date_debut" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('date_debut')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        
                        <div>
                            <label for="date_fin" class="block text-sm font-medium text-gray-700">Date de fin *</label>
                            <input type="date" name="date_fin" id="date_fin" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('date_fin')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3"
                                  placeholder="Description optionnelle de l'année scolaire..."
                                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                        @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                    <button type="button" onclick="closeModal('addAnneeModal')" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i>Créer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifier Année Scolaire -->
<div id="editAnneeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Modifier l'Année Scolaire</h3>
                    <button onclick="closeModal('editAnneeModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <form method="POST" action="" id="editAnneeForm" class="p-6">
                @csrf
                @method('PUT')
                
                <div class="space-y-4">
                    <div>
                        <label for="edit_libelle" class="block text-sm font-medium text-gray-700">Libellé *</label>
                        <input type="text" name="libelle" id="edit_libelle" required
                               placeholder="Ex: 2024-2025"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="edit_date_debut" class="block text-sm font-medium text-gray-700">Date de début *</label>
                            <input type="date" name="date_debut" id="edit_date_debut" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label for="edit_date_fin" class="block text-sm font-medium text-gray-700">Date de fin *</label>
                            <input type="date" name="date_fin" id="edit_date_fin" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    
                    <div>
                        <label for="edit_description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="edit_description" rows="3"
                                  placeholder="Description optionnelle de l'année scolaire..."
                                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                    <button type="button" onclick="closeModal('editAnneeModal')" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i>Modifier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// URLs générées par Laravel
const deleteUrls = {
    niveau: '{{ route("parametres.niveaux.destroy", ":id") }}',
    classe: '{{ route("parametres.classes.destroy", ":id") }}',
    frais: '{{ route("parametres.frais.destroy", ":id") }}',
    user: '{{ route("parametres.users.destroy", ":id") }}'
};

// Fonctions pour gérer les modales
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Fonction pour éditer un niveau
function editNiveau(niveauId) {
    // Vous devrez récupérer les données du niveau depuis le serveur
    // ou les passer depuis la vue Blade
    fetch(`/parametres/niveaux/${niveauId}/edit`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('edit_niveau_nom').value = data.nom;
            document.getElementById('edit_niveau_description').value = data.description || '';
            document.getElementById('editNiveauForm').action = `/parametres/niveaux/${niveauId}`;
            openModal('editNiveauModal');
        });
}

// Fonction pour éditer un utilisateur
function editUser(userId, name, email, role, actif) {
    document.getElementById('editUserForm').action = `/parametres/users/${userId}`;
    document.getElementById('edit_user_name').value = name;
    document.getElementById('edit_user_email').value = email;
    document.getElementById('edit_user_role').value = role;
    document.getElementById('edit_user_actif').checked = actif;
    
    // Vider les champs de mot de passe
    document.getElementById('edit_user_password').value = '';
    document.getElementById('edit_user_password_confirmation').value = '';
    
    openModal('editUserModal');
}

// Fonction pour confirmer la suppression
function confirmDelete(type, id, description = '') {
    let deleteUrl = '';
    
    // Utiliser les URLs Laravel générées
    if (deleteUrls[type]) {
        deleteUrl = deleteUrls[type].replace(':id', id);
    } else {
        console.error('Type non supporté:', type);
        return;
    }
    
    // Debug: afficher l'URL générée
    console.log('URL de suppression générée:', deleteUrl);
    console.log('Type:', type, 'ID:', id);
    
    // Mettre à jour le message de confirmation avec la description si fournie
    const modalContent = document.querySelector('#deleteModal p');
    if (description) {
        modalContent.textContent = `Voulez-vous vraiment supprimer "${description}" ? Cette action est irréversible.`;
    } else {
        modalContent.textContent = 'Cette action est irréversible. Êtes-vous sûr ?';
    }
    
    document.getElementById('deleteForm').action = deleteUrl;
    openModal('deleteModal');
}

// Fonction pour modifier les frais
function editFrais(id, type, montant, niveauId, actif) {
    document.getElementById('editFraisForm').action = `/parametres/frais/${id}`;
    document.getElementById('edit_type_frais').value = type;
    document.getElementById('edit_frais_montant').value = montant;
    document.getElementById('edit_frais_niveau_id').value = niveauId || '';
    document.getElementById('edit_frais_actif').checked = actif;
    openModal('editFraisModal');
}

// Fermer les modales en cliquant à l'extérieur
window.onclick = function(event) {
    const modals = ['addNiveauModal', 'editNiveauModal', 'addClasseModal', 'addFraisModal', 'editFraisModal', 'addUserModal', 'editUserModal', 'deleteModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (event.target == modal) {
            closeModal(modalId);
        }
    });
}

// Échapper pour fermer les modales
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modals = ['addNiveauModal', 'editNiveauModal', 'addClasseModal', 'addFraisModal', 'editFraisModal', 'addUserModal', 'editUserModal', 'addAnneeModal', 'editAnneeModal', 'deleteModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (!modal.classList.contains('hidden')) {
                closeModal(modalId);
            }
        });
    }
});
</script>