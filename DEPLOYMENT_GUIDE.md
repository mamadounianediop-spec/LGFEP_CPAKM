# 🚀 GUIDE DE DÉPLOIEMENT CPAKM
# Domaine: cpakm.lougabusiness.com
# Date: $(date)

## ✅ ÉTAPES TERMINÉES LOCALEMENT
- [x] Projet optimisé pour la production
- [x] Cache Laravel configuré
- [x] Fichier .env.production créé
- [x] Clé d'application générée
- [x] .htaccess optimisé pour Hostinger

## 📋 INFORMATIONS DE DÉPLOIEMENT
Domain: cpakm.lougabusiness.com
Database: u579581035_cpamk
Username: u579581035_cpamk_admin
Password: Lougabusiness1@25

## 🔧 ÉTAPES À SUIVRE SUR HOSTINGER

### 1. CONNEXION CPANEL
- Connectez-vous à votre cPanel Hostinger
- Localisez "File Manager"
- Ouvrez le dossier public_html

### 2. NETTOYAGE (SI NÉCESSAIRE)
- Supprimez tous les fichiers existants dans public_html/
- OU créez un sous-dossier si vous voulez garder d'autres sites

### 3. UPLOAD DES FICHIERS
Uploadez TOUS les fichiers du projet SAUF:
- .git/ (dossier)
- node_modules/ (dossier)
- .env (fichier local)
- storage/logs/*.log (fichiers de logs)
- bootstrap/cache/*.php (fichiers de cache)

### 4. RENOMMAGE
- Renommez .env.production en .env

### 5. PERMISSIONS
Définissez les permissions suivantes:
- Dossiers: 755
- Fichiers: 644
- storage/ et bootstrap/cache/: 775

### 6. COMPOSER (Terminal cPanel)
```bash
cd public_html
composer install --no-dev --optimize-autoloader
```

### 7. LARAVEL SETUP
```bash
php artisan key:generate
php artisan migrate
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 8. TEST
Visitez: https://cpakm.lougabusiness.com

## 🚨 POINTS IMPORTANTS
- La base de données DOIT être créée dans cPanel
- L'utilisateur DOIT avoir tous les privilèges
- SSL DOIT être activé pour HTTPS
- PHP version recommandée: 8.1+

## 📞 EN CAS DE PROBLÈME
1. Vérifiez les logs: storage/logs/laravel.log
2. Vérifiez la configuration DB dans .env
3. Assurez-vous que Composer est installé
4. Vérifiez les permissions des dossiers