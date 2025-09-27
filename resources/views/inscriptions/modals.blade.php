<!-- Modal Ajouter Pré-inscription -->
<div id="addPreInscriptionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Nouvelle pré-inscription</h3>
                    <button onclick="closeModal('addPreInscriptionModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <form method="POST" action="{{ route('inscriptions.pre-inscription.store') }}" class="p-6">
                @csrf
                
                <!-- Informations obligatoires -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-700">Nom *</label>
                        <input type="text" name="nom" id="nom" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('nom')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    
                    <div>
                        <label for="prenom" class="block text-sm font-medium text-gray-700">Prénom *</label>
                        <input type="text" name="prenom" id="prenom" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('prenom')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Sexe *</label>
                        <div class="flex items-center space-x-6">
                            <div class="flex items-center">
                                <input type="radio" name="sexe" id="sexe_m" value="M" required
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                <label for="sexe_m" class="ml-2 block text-sm text-gray-900">Masculin</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" name="sexe" id="sexe_f" value="F" required
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                <label for="sexe_f" class="ml-2 block text-sm text-gray-900">Féminin</label>
                            </div>
                        </div>
                        @error('sexe')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <!-- INE -->
                <div class="mb-6">
                    <label for="ine" class="block text-sm font-medium text-gray-700">
                        INE 
                        <span class="text-gray-500">(optionnel - généré automatiquement si vide)</span>
                    </label>
                    <input type="text" name="ine" id="ine" placeholder="Laisser vide pour génération automatique"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Si l'élève a déjà un INE d'un ancien système, le saisir ici.</p>
                    @error('ine')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Informations personnelles optionnelles -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="date_naissance" class="block text-sm font-medium text-gray-700">Date de naissance</label>
                        <input type="date" name="date_naissance" id="date_naissance"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('date_naissance')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    
                    <div>
                        <label for="lieu_naissance" class="block text-sm font-medium text-gray-700">Lieu de naissance</label>
                        <input type="text" name="lieu_naissance" id="lieu_naissance"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('lieu_naissance')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <!-- Adresse -->
                <div class="mb-6">
                    <label for="adresse" class="block text-sm font-medium text-gray-700">Adresse</label>
                    <textarea name="adresse" id="adresse" rows="3"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                    @error('adresse')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Contact et tuteur -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="contact" class="block text-sm font-medium text-gray-700">Contact/Téléphone</label>
                        <input type="text" name="contact" id="contact"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('contact')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    
                    <div>
                        <label for="tuteur" class="block text-sm font-medium text-gray-700">Tuteur/Parent</label>
                        <input type="text" name="tuteur" id="tuteur"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('tuteur')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <!-- Établissement d'origine -->
                <div class="mb-6">
                    <label for="etablissement_origine" class="block text-sm font-medium text-gray-700">Établissement d'origine</label>
                    <input type="text" name="etablissement_origine" id="etablissement_origine"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('etablissement_origine')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Boutons -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeModal('addPreInscriptionModal')" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifier Pré-inscription -->
<div id="editPreInscriptionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Modifier la pré-inscription</h3>
                    <button onclick="closeModal('editPreInscriptionModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <form method="POST" action="" id="editPreInscriptionForm" class="p-6">
                @csrf
                @method('PUT')
                
                <!-- Informations obligatoires -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="edit_nom" class="block text-sm font-medium text-gray-700">Nom *</label>
                        <input type="text" name="nom" id="edit_nom" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="edit_prenom" class="block text-sm font-medium text-gray-700">Prénom *</label>
                        <input type="text" name="prenom" id="edit_prenom" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Sexe *</label>
                        <div class="flex items-center space-x-6">
                            <div class="flex items-center">
                                <input type="radio" name="sexe" id="edit_sexe_m" value="M" required
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                <label for="edit_sexe_m" class="ml-2 block text-sm text-gray-900">Masculin</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" name="sexe" id="edit_sexe_f" value="F" required
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                <label for="edit_sexe_f" class="ml-2 block text-sm text-gray-900">Féminin</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- INE -->
                <div class="mb-6">
                    <label for="edit_ine" class="block text-sm font-medium text-gray-700">
                        INE
                        <span class="text-gray-500">(optionnel - généré automatiquement si vide)</span>
                    </label>
                    <input type="text" name="ine" id="edit_ine" placeholder="Laisser vide pour génération automatique"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Si vide, un INE sera généré automatiquement.</p>
                </div>

                <!-- Informations personnelles optionnelles -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="edit_date_naissance" class="block text-sm font-medium text-gray-700">Date de naissance</label>
                        <input type="date" name="date_naissance" id="edit_date_naissance"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="edit_lieu_naissance" class="block text-sm font-medium text-gray-700">Lieu de naissance</label>
                        <input type="text" name="lieu_naissance" id="edit_lieu_naissance"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <!-- Adresse -->
                <div class="mb-6">
                    <label for="edit_adresse" class="block text-sm font-medium text-gray-700">Adresse</label>
                    <textarea name="adresse" id="edit_adresse" rows="3"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>

                <!-- Contact et tuteur -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="edit_contact" class="block text-sm font-medium text-gray-700">Contact/Téléphone</label>
                        <input type="text" name="contact" id="edit_contact"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="edit_tuteur" class="block text-sm font-medium text-gray-700">Tuteur/Parent</label>
                        <input type="text" name="tuteur" id="edit_tuteur"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <!-- Établissement d'origine -->
                <div class="mb-6">
                    <label for="edit_etablissement_origine" class="block text-sm font-medium text-gray-700">Établissement d'origine</label>
                    <input type="text" name="etablissement_origine" id="edit_etablissement_origine"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Boutons -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeModal('editPreInscriptionModal')" 
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

<!-- Modal Modifier Inscription -->
<div id="editInscriptionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Modifier l'inscription</h3>
                    <button onclick="closeModal('editInscriptionModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <form method="POST" action="" id="editInscriptionForm" class="p-6">
                @csrf
                @method('PUT')
                
                <!-- Informations de l'élève (lecture seule) -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <h4 class="font-medium text-gray-900 mb-3">Informations de l'élève</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Nom complet:</span>
                            <span class="ml-2 font-medium" id="edit_inscription_nom_complet"></span>
                        </div>
                        <div>
                            <span class="text-gray-600">INE:</span>
                            <span class="ml-2 font-mono" id="edit_inscription_ine"></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Contact:</span>
                            <span class="ml-2" id="edit_inscription_contact"></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Date de naissance:</span>
                            <span class="ml-2" id="edit_inscription_date_naissance"></span>
                        </div>
                    </div>
                </div>

                <!-- Informations d'inscription modifiables -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="edit_inscription_niveau_id" class="block text-sm font-medium text-gray-700">Niveau *</label>
                        <select name="niveau_id" id="edit_inscription_niveau_id" required onchange="loadClassesForEdit(this.value); loadFraisForEdit(this.value)"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionner un niveau</option>
                            @if(isset($niveaux))
                                @foreach($niveaux as $niveau)
                                    <option value="{{ $niveau->id }}">{{ $niveau->nom }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div>
                        <label for="edit_inscription_classe_id" class="block text-sm font-medium text-gray-700">Classe *</label>
                        <select name="classe_id" id="edit_inscription_classe_id" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionner une classe</option>
                        </select>
                    </div>
                </div>

                <!-- Informations financières -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="edit_inscription_montant_total" class="block text-sm font-medium text-gray-700">
                            Montant total (frais d'inscription) *
                            <span class="text-xs text-gray-500">Automatique selon le niveau</span>
                        </label>
                        <input type="number" name="montant_total" id="edit_inscription_montant_total" required step="0.01" min="0" readonly
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm bg-gray-50 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="edit_inscription_montant_paye" class="block text-sm font-medium text-gray-700">Montant payé *</label>
                        <input type="number" name="montant_paye" id="edit_inscription_montant_paye" required step="0.01" min="0" 
                               oninput="validateMontantPayeEdit()"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                        <p id="edit_montant_error" class="text-red-500 text-xs mt-1" style="display: none;">
                            Le montant payé ne peut pas dépasser le montant total
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="edit_inscription_mode_paiement" class="block text-sm font-medium text-gray-700">Mode de paiement *</label>
                        <select name="mode_paiement" id="edit_inscription_mode_paiement" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionner un mode</option>
                            <option value="orange_money">Orange Money</option>
                            <option value="wave">Wave</option>
                            <option value="free_money">Free Money</option>
                            <option value="billetage">Billetage</option>
                        </select>
                    </div>

                    <div>
                        <label for="edit_inscription_statut" class="block text-sm font-medium text-gray-700">Statut *</label>
                        <select name="statut" id="edit_inscription_statut" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="actif">Actif</option>
                            <option value="suspendu">Suspendu</option>
                            <option value="abandonne">Abandonné</option>
                        </select>
                    </div>
                </div>

                <!-- Remarques -->
                <div class="mb-6">
                    <label for="edit_inscription_remarques" class="block text-sm font-medium text-gray-700">Remarques</label>
                    <textarea name="remarques" id="edit_inscription_remarques" rows="3"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Remarques ou notes particulières..."></textarea>
                </div>

                <!-- Boutons -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeModal('editInscriptionModal')" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i>Modifier l'inscription
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de confirmation pour annulation/suppression -->
<div id="confirmActionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900" id="confirmTitle">Confirmer l'action</h3>
                    <button onclick="closeModal('confirmActionModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <i id="confirmIcon" class="fas fa-exclamation-triangle text-orange-500 text-3xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-900" id="confirmMessage">
                            Êtes-vous sûr de vouloir effectuer cette action ?
                        </p>
                    </div>
                </div>
                
                <div class="bg-gray-50 p-3 rounded-lg mb-4">
                    <p class="text-sm text-gray-600" id="confirmDetails"></p>
                </div>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <button onclick="closeModal('confirmActionModal')" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                    Annuler
                </button>
                <button id="confirmButton" onclick="executeAction()" 
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                    Confirmer
                </button>
            </div>
        </div>
    </div>
</div>