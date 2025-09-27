<!-- Modal de gestion du personnel -->
<div x-data="personnelModal()" x-show="showModal" x-cloak 
     class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center p-4 overflow-y-auto personnel-modal-container"
     @keydown.escape.window="closeModal()"
     @open-personnel-modal.window="openModal($event.detail.type, $event.detail.data)">
    
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[95vh] overflow-y-auto my-4 personnel-modal"
         @click.away="closeModal()"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95">
        
        <!-- En-tête de la modal -->
        <div class="flex justify-between items-center p-6 border-b border-gray-200 bg-gray-50 rounded-t-lg">
            <h3 class="text-xl font-semibold text-gray-900" x-text="modalTitle"></h3>
            <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded-full p-2 transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Formulaire -->
        <form @submit.prevent="submitForm()" class="p-6">
            <!-- Informations personnelles -->
            <div class="mb-6">
                <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                    <i class="fas fa-user text-blue-500 mr-2"></i>
                    Informations personnelles
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nom -->
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">
                            Nom <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nom" x-model="form.nom" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <span x-show="errors.nom" class="text-red-500 text-xs mt-1" x-text="errors.nom"></span>
                    </div>

                    <!-- Prénom -->
                    <div>
                        <label for="prenom" class="block text-sm font-medium text-gray-700 mb-1">
                            Prénom <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="prenom" x-model="form.prenom" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <span x-show="errors.prenom" class="text-red-500 text-xs mt-1" x-text="errors.prenom"></span>
                    </div>

                    <!-- Téléphone -->
                    <div>
                        <label for="telephone" class="block text-sm font-medium text-gray-700 mb-1">
                            Téléphone <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" id="telephone" x-model="form.telephone" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <span x-show="errors.telephone" class="text-red-500 text-xs mt-1" x-text="errors.telephone"></span>
                    </div>

                    <!-- CNI -->
                    <div>
                        <label for="cni" class="block text-sm font-medium text-gray-700 mb-1">
                            CNI
                        </label>
                        <input type="text" id="cni" x-model="form.cni"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <span x-show="errors.cni" class="text-red-500 text-xs mt-1" x-text="errors.cni"></span>
                    </div>

                    <!-- Adresse (span 2 colonnes) -->
                    <div class="md:col-span-2">
                        <label for="adresse" class="block text-sm font-medium text-gray-700 mb-1">
                            Adresse
                        </label>
                        <input type="text" id="adresse" x-model="form.adresse"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <span x-show="errors.adresse" class="text-red-500 text-xs mt-1" x-text="errors.adresse"></span>
                    </div>
                </div>
            </div>

            <!-- Informations professionnelles -->
            <div class="mb-6">
                <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                    <i class="fas fa-briefcase text-green-500 mr-2"></i>
                    Informations professionnelles
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Type de personnel -->
                    <div>
                        <label for="type_personnel" class="block text-sm font-medium text-gray-700 mb-1">
                            Fonction <span class="text-red-500">*</span>
                        </label>
                        <select id="type_personnel" x-model="form.type_personnel" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-sm">
                            <option value="">Sélectionner</option>
                            <option value="directeur">Directeur</option>
                            <option value="surveillant">Surveillant</option>
                            <option value="secretaire">Secrétaire</option>
                            <option value="enseignant">Enseignant</option>
                            <option value="gardien">Gardien</option>
                        </select>
                        <span x-show="errors.type_personnel" class="text-red-500 text-xs mt-1" x-text="errors.type_personnel"></span>
                    </div>

                    <!-- Discipline (pour les enseignants) -->
                    <div x-show="form.type_personnel === 'enseignant'" x-transition>
                        <label for="discipline" class="block text-sm font-medium text-gray-700 mb-1">
                            Discipline
                        </label>
                        <select id="discipline" x-model="form.discipline"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-sm">
                            <option value="">Sélectionner</option>
                            <option value="Mathématiques">Mathématiques</option>
                            <option value="Français">Français</option>
                            <option value="Anglais">Anglais</option>
                            <option value="Sciences Physiques">Sciences Physiques</option>
                            <option value="SVT">SVT</option>
                            <option value="Histoire-Géographie">Histoire-Géographie</option>
                            <option value="Philosophie">Philosophie</option>
                            <option value="EPS">EPS</option>
                            <option value="Informatique">Informatique</option>
                            <option value="Arabe">Arabe</option>
                        </select>
                        <span x-show="errors.discipline" class="text-red-500 text-xs mt-1" x-text="errors.discipline"></span>
                    </div>

                    <!-- Statut -->
                    <div>
                        <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">
                            Statut <span class="text-red-500">*</span>
                        </label>
                        <select id="statut" x-model="form.statut" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-sm">
                            <option value="actif">Actif</option>
                            <option value="suspendu">Suspendu</option>
                            <option value="conge">En congé</option>
                        </select>
                        <span x-show="errors.statut" class="text-red-500 text-xs mt-1" x-text="errors.statut"></span>
                    </div>

                    <!-- Date d'embauche -->
                    <div>
                        <label for="date_embauche" class="block text-sm font-medium text-gray-700 mb-1">
                            Date d'embauche
                        </label>
                        <input type="date" id="date_embauche" x-model="form.date_embauche"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <span x-show="errors.date_embauche" class="text-red-500 text-xs mt-1" x-text="errors.date_embauche"></span>
                    </div>
                </div>
            </div>

            <!-- Rémunération -->
            <div class="mb-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                    <i class="fas fa-coins text-yellow-500 mr-2"></i>
                    Rémunération
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Mode de paiement -->
                    <div>
                        <label for="mode_paiement" class="block text-sm font-medium text-gray-700 mb-1">
                            Mode <span class="text-red-500">*</span>
                        </label>
                        <select id="mode_paiement" x-model="form.mode_paiement" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-sm">
                            <option value="fixe">Mensuel</option>
                            <option value="heure">Par heure</option>
                        </select>
                        <span x-show="errors.mode_paiement" class="text-red-500 text-xs mt-1" x-text="errors.mode_paiement"></span>
                    </div>

                    <!-- Montant fixe -->
                    <div x-show="form.mode_paiement === 'fixe'" x-transition>
                        <label for="montant_fixe" class="block text-sm font-medium text-gray-700 mb-1">
                            Salaire mensuel <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="montant_fixe" x-model="form.montant_fixe" 
                               min="0" step="5000" placeholder="150000"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <span x-show="errors.montant_fixe" class="text-red-500 text-xs mt-1" x-text="errors.montant_fixe"></span>
                        <p class="text-xs text-gray-500 mt-1">FCFA/mois</p>
                    </div>

                    <!-- Tarif heure -->
                    <div x-show="form.mode_paiement === 'heure'" x-transition>
                        <label for="tarif_heure" class="block text-sm font-medium text-gray-700 mb-1">
                            Tarif horaire <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="tarif_heure" x-model="form.tarif_heure" 
                               min="0" step="250" placeholder="2500"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <span x-show="errors.tarif_heure" class="text-red-500 text-xs mt-1" x-text="errors.tarif_heure"></span>
                        <p class="text-xs text-gray-500 mt-1">FCFA/heure</p>
                    </div>
                </div>
            </div>

        </form>

        <!-- Boutons d'action -->
        <div class="flex justify-end space-x-4 p-6 bg-gray-50 border-t border-gray-200 rounded-b-lg">
            <button type="button" @click="closeModal()"
                    class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors font-medium">
                Annuler
            </button>
            <button type="submit" :disabled="loading" @click="submitForm()"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:opacity-50 transition-colors font-medium">
                <span x-show="!loading" x-text="isEditing ? 'Modifier' : 'Ajouter'"></span>
                <span x-show="loading" class="flex items-center">
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    Traitement...
                </span>
            </button>
        </div>
    </div>
            </div>
        </form>
    </div>
</div>

<script>
function personnelModal() {
    return {
        showModal: false,
        loading: false,
        isEditing: false,
        modalTitle: 'Ajouter un personnel',
        form: {
            nom: '',
            prenom: '',
            telephone: '',
            adresse: '',
            cni: '',
            type_personnel: '',
            discipline: '',
            statut: 'actif',
            date_embauche: '',
            mode_paiement: 'fixe',
            montant_fixe: '',
            tarif_heure: ''
        },
        errors: {},
        personnelId: null,

        openModal(type = null, personnelData = null) {
            this.showModal = true;
            this.errors = {};
            
            if (personnelData) {
                this.isEditing = true;
                this.modalTitle = 'Modifier le personnel';
                this.personnelId = personnelData.id;
                this.form = { ...personnelData };
            } else {
                this.isEditing = false;
                this.modalTitle = 'Ajouter un personnel';
                this.personnelId = null;
                this.resetForm();
                if (type) {
                    this.form.type_personnel = type;
                }
            }
        },

        closeModal() {
            this.showModal = false;
            this.resetForm();
        },

        resetForm() {
            this.form = {
                nom: '',
                prenom: '',
                telephone: '',
                adresse: '',
                cni: '',
                type_personnel: '',
                discipline: '',
                statut: 'actif',
                date_embauche: '',

            };
            this.errors = {};
            this.personnelId = null;
        },

        async submitForm() {
            this.loading = true;
            this.errors = {};

            try {
                const url = this.isEditing ? 
                    `/personnel/${this.personnelId}` : 
                    '/personnel';
                
                const method = this.isEditing ? 'PUT' : 'POST';

                const formData = new FormData();
                Object.keys(this.form).forEach(key => {
                    if (this.form[key] !== null && this.form[key] !== '') {
                        formData.append(key, this.form[key]);
                    }
                });

                if (this.isEditing) {
                    formData.append('_method', 'PUT');
                }
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });

                const result = await response.json();

                if (response.ok) {
                    this.closeModal();
                    location.reload(); // Recharger la page pour voir les changements
                } else {
                    if (result.errors) {
                        this.errors = result.errors;
                    } else {
                        alert('Une erreur est survenue lors de la sauvegarde.');
                    }
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Une erreur de connexion est survenue.');
            } finally {
                this.loading = false;
            }
        }
    }
}

// Fonctions globales pour les boutons
function openPersonnelModal(type = null) {
    window.dispatchEvent(new CustomEvent('open-personnel-modal', {
        detail: { type: type, data: null }
    }));
}

function editPersonnel(personnelId) {
    // Récupérer les données du personnel via AJAX
    fetch(`/personnel/${personnelId}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.json())
    .then(data => {
        window.dispatchEvent(new CustomEvent('open-personnel-modal', {
            detail: { type: null, data: data }
        }));
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors du chargement des données du personnel.');
    });
}

function deletePersonnel(personnelId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce personnel ?')) {
        const formData = new FormData();
        formData.append('_method', 'DELETE');
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        fetch(`/personnel/${personnelId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                location.reload();
            } else {
                alert('Erreur lors de la suppression.');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue.');
        });
    }
}

function toggleStatutPersonnel(personnelId) {
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

    fetch(`/personnel/${personnelId}/toggle-statut`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            location.reload();
        } else {
            alert('Erreur lors du changement de statut.');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur est survenue.');
    });
}

// Plus d'initialisation nécessaire - les événements personnalisés gèrent la communication
</script>