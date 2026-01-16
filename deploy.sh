#!/bin/bash

# Script de déploiement automatisé pour Transit Plus (Ubuntu)
# Usage: ./deploy.sh

echo "🚀 Démarrage du déploiement de Transit Plus..."

# 1. Vérification de Docker
if ! [ -x "$(command -v docker-compose)" ]; then
  echo "❌ Erreur: docker-compose n'est pas installé." >&2
  exit 1
fi

# 2. Copie du .env si inexistant
if [ ! -f .env ]; then
    echo "📄 Création du fichier .env à partir de l'exemple..."
    cp .env.example .env
    echo "⚠️  N'OUBLIEZ PAS DE CONFIGURER VOTRE .ENV (DB_HOST=db, etc.)"
fi

# 3. Build et Lancement des conteneurs
echo "🏗️  Construction des images Docker..."
docker-compose up -d --build

# 4. Installation des dépendances et Setup Laravel
echo "📦 Installation des dépendances Composer..."
docker-compose exec app composer install --no-dev --optimize-autoloader

echo "🔑 Génération de la clé d'application..."
docker-compose exec app php artisan key:generate --force

echo "📂 Création du lien symbolique de stockage..."
docker-compose exec app php artisan storage:link

echo "🗄️  Exécution des migrations et seeders..."
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan db:seed --class=RolesAndPermissionsSeeder --force

echo "🧹 Nettoyage du cache..."
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

echo "✅ Déploiement terminé avec succès !"
echo "🌐 L'application est accessible sur http://votre-ip:8000"
