#!/bin/bash

# Script de déploiement pour Hostinger
echo "🚀 DÉPLOIEMENT CPAKM SUR HOSTINGER"
echo "=================================="

# 1. Optimiser pour la production
echo "📦 Optimisation pour la production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 2. Créer un fichier ZIP pour l'upload
echo "📁 Création de l'archive de déploiement..."
echo "Fichiers à exclure du déploiement :"
echo "- .git/"
echo "- node_modules/"
echo "- .env (local)"
echo "- storage/logs/"
echo "- vendor/ (sera réinstallé sur le serveur)"

echo ""
echo "✅ Prêt pour le déploiement !"
echo ""
echo "📋 ÉTAPES SUIVANTES :"
echo "1. Connectez-vous à votre cPanel Hostinger"
echo "2. Allez dans File Manager"
echo "3. Supprimez le contenu de public_html/"
echo "4. Uploadez tous les fichiers SAUF :"
echo "   - .git/"
echo "   - node_modules/"
echo "   - .env (utilisez .env.production)"
echo "   - storage/logs/*"
echo "5. Renommez .env.production en .env"
echo "6. Installez Composer depuis cPanel"
echo "7. Exécutez les migrations"
echo ""
echo "🔗 Informations de base de données :"
echo "   Database: u579581035_cpamk"
echo "   Username: u579581035_cpamk_admin"
echo "   Host: localhost"
echo ""