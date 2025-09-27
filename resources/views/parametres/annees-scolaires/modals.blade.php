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