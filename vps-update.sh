#!/bin/bash

# TRANSIT PLUS - SCRIPT DE MISE À JOUR (UPDATE) POUR VPS UBUNTU
# Ce script met à jour le code, les dépendances et la base de données sans effacer les données existantes.

echo "-----------------------------------------------------------"
echo "🚀 Démarrage de la mise à jour Transit Plus..."
echo "-----------------------------------------------------------"

# 1. Récupération du code
echo "📥 Récupération des dernières modifications (Git)..."
git pull origin main

# 2. Dépendances PHP
echo "📦 Mise à jour des dépendances Composer..."
docker exec btrans-app composer install --no-dev --optimize-autoloader

# 3. Base de données
echo "🗄️  Exécution des nouvelles migrations..."
# Note : On utilise 'migrate' et non 'migrate:fresh' pour ne pas perdre les données !
docker exec btrans-app php artisan migrate --force

# 4. Nettoyage et Optimisation du Cache
echo "⚡ Optimisation du système..."
docker exec btrans-app php artisan optimize:clear
docker exec btrans-app php artisan optimize
docker exec btrans-app php artisan filament:cache-components
docker exec btrans-app php artisan icons:cache

# 5. Gestion des Assets (Vite)
# Si vous avez Node installé sur le VPS, décommentez les lignes suivantes :
# echo "🎨 Compilation des assets (Vite)..."
# npm install
# npm run build

echo "-----------------------------------------------------------"
echo "✅ MISE À JOUR TERMINÉE AVEC SUCCÈS !"
echo "-----------------------------------------------------------"
