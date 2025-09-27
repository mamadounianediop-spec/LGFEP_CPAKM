<!-- États de Paiement Mensuels -->
<style>
/* Style pour les en-têtes fixes */
.sticky-header {
    position: sticky;
    top: 0;
    z-index: 10;
    background-color: rgb(249 250 251);
    box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
}


</style>

<div class="bg-white rounded-lg shadow-sm border" x-data="etatsPaiementModule()">
    <!-- Message de consultation d'archive -->
    <div x-show="consultationArchive" x-transition class="px-6 py-3 bg-blue-50 border-b border-blue-200">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <i class="fas fa-info-circle text-blue-600"></i>
                <span class="text-sm text-blue-800 font-medium">
                    Consultation d'archive - Période sélectionnée depuis les archives
                </span>
            </div>
            <button @click="consultationArchive = false" class="text-blue-400 hover:text-blue-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- En-tête avec filtre par mois -->
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <!-- Titre -->
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-money-check text-green-600 text-sm"></i>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">États de Paiement Mensuels</h3>
                    <p class="text-sm text-gray-600" x-show="anneeActuelle" x-text="`Année scolaire ${anneeActuelle}-${anneeActuelle + 1}`"></p>
                </div>
            </div>

            <!-- Sélecteurs de période -->
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <label for="annee-select" class="text-sm font-medium text-gray-700">Année scolaire :</label>
                    <select id="annee-select" x-model="anneeActuelle" @change="chargerEtatMensuel()"
                            class="px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white text-sm">
                        <template x-for="annee in anneesScolaires" :key="annee.valeur">
                            <option :value="annee.valeur" x-text="annee.libelle" :selected="annee.actuelle"></option>
                        </template>
                    </select>
                </div>
                <div class="flex items-center space-x-2">
                    <label for="mois-select" class="text-sm font-medium text-gray-700">Mois :</label>
                    <select id="mois-select" x-model="moisSelectionne" @change="chargerEtatMensuel()"
                            class="px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white text-sm">
                        <option value="10">Octobre</option>
                        <option value="11">Novembre</option>
                        <option value="12">Décembre</option>
                        <option value="1">Janvier</option>
                        <option value="2">Février</option>
                        <option value="3">Mars</option>
                        <option value="4">Avril</option>
                        <option value="5">Mai</option>
                        <option value="6">Juin</option>
                        <option value="7">Juillet</option>
                        <option value="8">Août</option>
                        <option value="9">Septembre</option>
                    </select>
                </div>
                
                <!-- Boutons d'action -->
                <button @click="apercu()" :disabled="etats.length === 0"
                        class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 disabled:opacity-50 text-sm font-medium transition-colors">
                    <i class="fas fa-eye mr-2"></i>Aperçu
                </button>
                <button @click="archiverPeriode()" :disabled="chargement || etats.length === 0"
                        class="bg-orange-600 text-white px-4 py-2 rounded-md hover:bg-orange-700 disabled:opacity-50 text-sm font-medium transition-colors">
                    <i class="fas fa-archive mr-2"></i>Archiver la période
                </button>
                <button @click="archiverEtat()" :disabled="etats.length === 0"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-sm font-medium transition-colors">
                    <i class="fas fa-archive mr-2"></i>Archiver l'état
                </button>
            </div>
        </div>
    </div>

    <!-- Chargement -->
    <div x-show="chargement" class="flex items-center justify-center py-12">
        <div class="flex items-center space-x-2 text-gray-600">
            <i class="fas fa-spinner fa-spin"></i>
            <span>Chargement des données...</span>
        </div>
    </div>

    <!-- Tableau des états de paiement -->
    <div x-show="!chargement">
        <table class="w-full table-fixed divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="w-1/4 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Personnel
                    </th>
                    <th class="w-20 px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Mode
                    </th>
                    <th class="w-16 px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Heures
                    </th>
                    <th class="w-20 px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Primes
                    </th>
                    <th class="w-28 px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Retenues
                    </th>
                    <th class="w-24 px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Total
                    </th>
                    <th class="w-20 px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Avances
                    </th>
                    <th class="w-24 px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Restant
                    </th>
                    <th class="w-20 px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Action
                    </th>
                    <th class="w-12 px-2 py-3 text-center">
                        <i class="fas fa-eye text-gray-400"></i>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="etat in etats" :key="etat.id">
                    <tr :class="!etat.visible ? 'opacity-50 bg-gray-50' : 'hover:bg-gray-50'" class="transition-colors">
                        <!-- Personnel avec Fonction -->
                        <td class="px-4 py-3">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600 text-xs"></i>
                                </div>
                                <div class="ml-2 min-w-0 flex-1">
                                    <div class="text-sm font-medium text-gray-900 truncate" x-text="`${etat.personnel.prenom} ${etat.personnel.nom}`"></div>
                                    <div class="flex items-center space-x-1 mt-1">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium"
                                              :class="getFonctionColorClass(etat.personnel.type_personnel)"
                                              x-text="etat.personnel.type_personnel.charAt(0).toUpperCase() + etat.personnel.type_personnel.slice(1)">
                                        </span>
                                        <span class="text-xs text-gray-500" x-text="`ID:${etat.personnel.id}`"></span>
                                    </div>
                                    <div x-show="!etat.visible" class="text-xs text-red-500 italic">Masqué</div>
                                </div>
                            </div>
                        </td>

                        <!-- Mode de paiement -->
                        <td class="px-2 py-3">
                            <div class="text-xs">
                                <div class="font-medium text-gray-900" x-text="etat.personnel.mode_paiement === 'fixe' ? 'Fixe' : 'Heure'"></div>
                                <div class="text-xs text-gray-500 truncate" x-show="etat.personnel.mode_paiement === 'fixe'" x-text="formatMontant(etat.personnel.montant_fixe)"></div>
                                <div class="text-xs text-gray-500" x-show="etat.personnel.mode_paiement === 'heure'" x-text="formatMontant(etat.personnel.tarif_heure) + '/h'"></div>
                            </div>
                        </td>

                        <!-- Heures effectuées -->
                        <td class="px-2 py-3">
                            <input x-show="etat.personnel.mode_paiement === 'heure'" 
                                   type="number" step="1" min="0" max="200"
                                   x-model="etat.heures_effectuees"
                                   @change="calculerMontants(etat); sauvegarderEtat(etat)"
                                   class="w-full px-2 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <span x-show="etat.personnel.mode_paiement === 'fixe'" class="text-xs text-gray-400">-</span>
                        </td>

                        <!-- Primes -->
                        <td class="px-2 py-3">
                            <input type="number" step="1000" min="0"
                                   x-model="etat.primes"
                                   @change="calculerMontants(etat); sauvegarderEtat(etat)"
                                   class="w-full px-2 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        </td>

                        <!-- Retenues -->
                        <td class="px-3 py-3">
                            <select x-model="etat.type_retenue" 
                                    @change="appliquerRetenue(etat); sauvegarderEtat(etat)"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500 min-w-[100px]">
                                <option value="0">0% (Aucune)</option>
                                <option value="5">5%</option>
                                <option value="10">10%</option>
                                <option value="15">15%</option>
                            </select>
                        </td>

                        <!-- Montant total -->
                        <td class="px-2 py-3">
                            <div class="text-xs font-medium text-gray-900 truncate" x-text="formatMontant(etat.montant_total)"></div>
                        </td>

                        <!-- Avances -->
                        <td class="px-2 py-3">
                            <input type="number" step="1000" min="0"
                                   x-model="etat.avances"
                                   @change="calculerMontants(etat); sauvegarderEtat(etat)"
                                   class="w-full px-2 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        </td>

                        <!-- Restant -->
                        <td class="px-2 py-3">
                            <div class="text-xs font-medium truncate" 
                                 :class="etat.restant > 0 ? 'text-red-600' : 'text-green-600'"
                                 x-text="formatMontant(etat.restant)"></div>
                        </td>

                        <!-- Action/Statut -->
                        <td class="px-2 py-3">
                            <select x-model="etat.statut_paiement" @change="sauvegarderEtat(etat)"
                                    :class="etat.statut_paiement === 'paye' ? 
                                           'w-full px-1 py-1 text-xs border border-green-300 rounded focus:ring-1 focus:ring-green-500 focus:border-green-500 bg-green-50 text-green-800 font-medium' : 
                                           'w-full px-1 py-1 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-green-500 focus:border-green-500'">
                                <option value="en_attente">Attente</option>
                                <option value="paye">Payé</option>
                            </select>
                        </td>

                        <!-- Bouton masquer/afficher -->
                        <td class="px-2 py-3 text-center">
                            <button @click="toggleVisibilite(etat)" 
                                    :class="etat.visible ? 'text-gray-600 hover:text-red-600' : 'text-red-600 hover:text-gray-600'"
                                    class="transition-colors">
                                <i :class="etat.visible ? 'fas fa-eye' : 'fas fa-eye-slash'" class="text-sm"></i>
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <!-- Message si aucune donnée -->
    <div x-show="!chargement && etats.length === 0" class="text-center py-12">
        <i class="fas fa-money-check text-gray-300 text-6xl mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun état de paiement</h3>
        <p class="text-gray-600">Les états seront créés automatiquement lors de la première saisie.</p>
    </div>

    <!-- Résumé en bas -->
    <div x-show="etats.length > 0" class="bg-gray-50 px-6 py-4 border-t border-gray-200">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div class="text-center">
                <div class="font-medium text-gray-900" x-text="etats.filter(e => e.visible).length"></div>
                <div class="text-gray-600">Personnel visible</div>
            </div>
            <div class="text-center">
                <div class="font-medium text-green-600" x-text="formatMontant(totalVisible)"></div>
                <div class="text-gray-600">Total à payer</div>
            </div>
            <div class="text-center">
                <div class="font-medium text-blue-600" x-text="formatMontant(totalAvances)"></div>
                <div class="text-gray-600">Total avances</div>
            </div>
            <div class="text-center">
                <div class="font-medium text-red-600" x-text="formatMontant(totalRestant)"></div>
                <div class="text-gray-600">Total restant</div>
            </div>
        </div>
    </div>
</div>

<script>
function etatsPaiementModule() {
    return {
        etats: [],
        chargement: false,
        moisSelectionne: new Date().getMonth() + 1, // Mois actuel
        anneeActuelle: null,
        anneesScolaires: [],
        consultationArchive: false,
        
        async init() {
            await this.chargerAnneesScolaires();
            this.chargerEtatMensuel();
            
            // S'abonner aux changements de synchronisation
            if (window.addSyncListener) {
                window.addSyncListener((syncData) => {
                    // Mettre à jour seulement si les données ne sont pas identiques
                    if (JSON.stringify(this.anneesScolaires) !== JSON.stringify(syncData.anneesScolaires)) {
                        this.anneesScolaires = syncData.anneesScolaires;
                    }
                    if (this.anneeActuelle !== syncData.anneeActuelle) {
                        this.anneeActuelle = syncData.anneeActuelle;
                    }
                });
            }
            
            // Écouter les événements de changement de période depuis les archives
            document.addEventListener('changerPeriodeEtatsPaiement', (event) => {
                this.anneeActuelle = event.detail.annee;
                this.moisSelectionne = event.detail.mois;
                this.consultationArchive = true;
                this.chargerEtatMensuel();
                
                // Masquer le message après 5 secondes
                setTimeout(() => {
                    this.consultationArchive = false;
                }, 5000);
            });
        },

        async chargerAnneesScolaires() {
            try {
                const response = await fetch('/personnel/annees-scolaires');
                if (response.ok) {
                    this.anneesScolaires = await response.json();
                    console.log('Années scolaires chargées:', this.anneesScolaires);
                    
                    // Définir l'année actuelle par défaut
                    const anneeActuelle = this.anneesScolaires.find(a => a.actuelle);
                    if (anneeActuelle) {
                        this.anneeActuelle = anneeActuelle.valeur;
                        console.log('Année actuelle définie:', this.anneeActuelle);
                    } else {
                        // Si aucune année n'est marquée comme actuelle, prendre la première
                        if (this.anneesScolaires.length > 0) {
                            this.anneeActuelle = this.anneesScolaires[0].valeur;
                            console.log('Année par défaut (première):', this.anneeActuelle);
                        } else {
                            this.anneeActuelle = new Date().getFullYear();
                            console.log('Année par défaut (année courante):', this.anneeActuelle);
                        }
                    }
                    
                    // Synchroniser avec les autres onglets
                    if (window.syncAnneesScolaires) {
                        window.syncAnneesScolaires({
                            anneesScolaires: this.anneesScolaires,
                            anneeActuelle: this.anneeActuelle
                        });
                    }
                } else {
                    console.error('Erreur response:', response.status, response.statusText);
                    throw new Error('Erreur de réponse');
                }
            } catch (error) {
                console.error('Erreur lors du chargement des années scolaires:', error);
                // Fallback plus robuste
                this.anneeActuelle = new Date().getFullYear();
                this.anneesScolaires = [
                    { valeur: this.anneeActuelle, libelle: this.anneeActuelle + '-' + (this.anneeActuelle + 1), actuelle: true }
                ];
                
                console.log('Fallback - Année actuelle:', this.anneeActuelle);
                
                // Synchroniser même en cas d'erreur
                if (window.syncAnneesScolaires) {
                    window.syncAnneesScolaires({
                        anneesScolaires: this.anneesScolaires,
                        anneeActuelle: this.anneeActuelle
                    });
                }
            }
        },

        async chargerEtatMensuel() {
            this.chargement = true;
            console.log('Chargement des états pour:', this.anneeActuelle, this.moisSelectionne);
            
            // Vérifier que les paramètres sont valides
            if (!this.anneeActuelle || !this.moisSelectionne) {
                console.error('Paramètres invalides - année:', this.anneeActuelle, 'mois:', this.moisSelectionne);
                this.chargement = false;
                return;
            }
            
            try {
                const url = `/personnel/etats-paiement/${this.anneeActuelle}/${this.moisSelectionne}`;
                console.log('URL de requête:', url);
                
                const response = await fetch(url);
                if (response.ok) {
                    this.etats = await response.json();
                    console.log('États chargés:', this.etats.length, 'éléments');
                } else {
                    console.error('Erreur lors du chargement des états:', response.status, response.statusText);
                    const errorText = await response.text();
                    console.error('Réponse d\'erreur:', errorText);
                }
            } catch (error) {
                console.error('Erreur lors du chargement des états:', error);
            } finally {
                this.chargement = false;
            }
        },

        async sauvegarderEtat(etat) {
            // Nettoyer et valider les données avant sauvegarde
            this.nettoyerDonneesEtat(etat);
            
            // Calculer immédiatement côté client pour une réactivité instantanée
            this.calculerMontants(etat);

            try {
                const response = await fetch('/personnel/etats-paiement/sauvegarder', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        id: etat.id,
                        personnel_id: etat.personnel_id,
                        annee: this.anneeActuelle,
                        mois: this.moisSelectionne,
                        heures_effectuees: parseFloat(etat.heures_effectuees) || 0,
                        primes: parseFloat(etat.primes) || 0,
                        retenues: parseFloat(etat.retenues) || 0,
                        type_retenue: etat.type_retenue || null,
                        avances: parseFloat(etat.avances) || 0,
                        statut_paiement: etat.statut_paiement
                    })
                });

                if (response.ok) {
                    const data = await response.json();
                    // Mettre à jour avec TOUTES les valeurs serveur pour être sûr
                    etat.heures_effectuees = data.heures_effectuees;
                    etat.primes = data.primes;
                    etat.retenues = data.retenues;
                    etat.avances = data.avances;
                    etat.montant_total = data.montant_total;
                    etat.restant = data.restant;
                    etat.statut_paiement = data.statut_paiement;
                    
                    // Synchroniser les changements avec les autres onglets
                    if (window.syncEtatsPaiement) {
                        window.syncEtatsPaiement({
                            annee: this.anneeActuelle,
                            mois: this.moisSelectionne,
                            etats: this.etats
                        });
                    }
                } else {
                    console.error('Erreur lors de la sauvegarde');
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        },

        appliquerRetenue(etat) {
            const pourcentage = parseFloat(etat.type_retenue) || 0;
            
            // Si 0%, pas de retenues
            if (pourcentage === 0) {
                etat.retenues = 0;
                this.calculerMontants(etat);
                return;
            }

            // Calcul du salaire de base pour appliquer le pourcentage
            let salaireBase = 0;
            if (etat.personnel.mode_paiement === 'fixe') {
                salaireBase = parseFloat(etat.personnel.montant_fixe) || 0;
            } else if (etat.personnel.mode_paiement === 'heure') {
                const heures = parseFloat(etat.heures_effectuees) || 0;
                const tarifHeure = parseFloat(etat.personnel.tarif_heure) || 0;
                salaireBase = heures * tarifHeure;
            }

            const primes = parseFloat(etat.primes) || 0;
            const salaireBrut = salaireBase + primes;
            
            etat.retenues = Math.round(salaireBrut * pourcentage / 100);
            
            this.calculerMontants(etat);
            this.sauvegarderEtat(etat);
        },

        nettoyerDonneesEtat(etat) {
            // Nettoyer et normaliser les valeurs
            etat.heures_effectuees = etat.heures_effectuees === '' || etat.heures_effectuees === null ? 0 : parseFloat(etat.heures_effectuees) || 0;
            etat.primes = etat.primes === '' || etat.primes === null ? 0 : parseFloat(etat.primes) || 0;
            etat.retenues = etat.retenues === '' || etat.retenues === null ? 0 : parseFloat(etat.retenues) || 0;
            etat.avances = etat.avances === '' || etat.avances === null ? 0 : parseFloat(etat.avances) || 0;
            
            // S'assurer que le statut est valide
            if (!etat.statut_paiement || !['en_attente', 'paye'].includes(etat.statut_paiement)) {
                etat.statut_paiement = 'en_attente';
            }
        },

        calculerMontants(etat) {
            // Nettoyer d'abord les données
            this.nettoyerDonneesEtat(etat);
            
            // Calcul du salaire de base
            let salaireBase = 0;
            if (etat.personnel.mode_paiement === 'fixe') {
                salaireBase = parseFloat(etat.personnel.montant_fixe) || 0;
            } else if (etat.personnel.mode_paiement === 'heure') {
                const heures = parseFloat(etat.heures_effectuees) || 0;
                const tarifHeure = parseFloat(etat.personnel.tarif_heure) || 0;
                salaireBase = heures * tarifHeure;
            }

            // Calcul du montant total
            const primes = parseFloat(etat.primes) || 0;
            const retenues = parseFloat(etat.retenues) || 0;
            etat.montant_total = salaireBase + primes - retenues;

            // Calcul du restant selon la nouvelle logique
            const avances = parseFloat(etat.avances) || 0;
            if (avances == 0) {
                etat.restant = 0; // Payé intégralement
            } else {
                etat.restant = etat.montant_total - avances; // Reste à payer
            }
        },

        async toggleVisibilite(etat) {
            try {
                const response = await fetch(`/personnel/etats-paiement/${etat.id}/toggle-visibilite`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    etat.visible = data.visible;
                } else {
                    console.error('Erreur lors du changement de visibilité');
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        },

        async archiverEtat() {
            if (confirm('Êtes-vous sûr de vouloir archiver cet état ? Il sera déplacé dans les archives.')) {
                try {
                    const response = await fetch(`/personnel/etats-paiement/archiver/${this.anneeActuelle}/${this.moisSelectionne}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    if (response.ok) {
                        alert('État archivé avec succès !');
                        this.chargerEtatMensuel();
                    } else {
                        console.error('Erreur lors de l\'archivage');
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                }
            }
        },

        async archiverPeriode() {
            if (!confirm(`Voulez-vous archiver tous les états de paiement pour ${this.getNomMois(this.moisSelectionne)} ${this.anneeActuelle} ?\n\nCette action est irréversible et déplacera tous les états vers les archives.`)) {
                return;
            }

            try {
                const response = await fetch(`/personnel/etats-paiement/${this.anneeActuelle}/${this.moisSelectionne}/archiver`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    alert('Période archivée avec succès !');
                    // Recharger les données
                    this.chargerEtatMensuel();
                } else {
                    const error = await response.json();
                    alert('Erreur lors de l\'archivage : ' + (error.message || 'Erreur inconnue'));
                }
            } catch (error) {
                alert('Erreur lors de l\'archivage : ' + error.message);
            }
        },

        getNomMois(numeroMois) {
            const mois = [
                '', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
            ];
            return mois[numeroMois] || 'Inconnu';
        },

        formatMontant(montant) {
            return new Intl.NumberFormat('fr-FR').format(montant || 0) + ' FCFA';
        },

        apercu() {
            // Petit délai pour permettre aux sauvegardes automatiques en cours de se terminer
            setTimeout(() => {
                const url = `/personnel/etats-paiement/${this.anneeActuelle}/${this.moisSelectionne}/apercu`;
                window.open(url, '_blank');
            }, 200);
        },

        getFonctionColorClass(type) {
            const classes = {
                'directeur': 'bg-purple-100 text-purple-800',
                'surveillant': 'bg-orange-100 text-orange-800',
                'secretaire': 'bg-pink-100 text-pink-800',
                'enseignant': 'bg-green-100 text-green-800',
                'gardien': 'bg-gray-100 text-gray-800'
            };
            return classes[type] || 'bg-gray-100 text-gray-800';
        },

        get totalVisible() {
            return this.etats.filter(e => e.visible).reduce((sum, e) => {
                return sum + (parseFloat(e.montant_total) || 0);
            }, 0);
        },

        get totalAvances() {
            return this.etats.filter(e => e.visible).reduce((sum, e) => {
                return sum + (parseFloat(e.avances) || 0);
            }, 0);
        },

        get totalRestant() {
            return this.etats.filter(e => e.visible).reduce((sum, e) => {
                return sum + (parseFloat(e.restant) || 0);
            }, 0);
        }
    }
}
</script>
