#!/bin/bash

# TRANSIT PLUS - ROBUST DEPLOYMENT SCRIPT (UBUNTU LOCAL/SSH)
echo "🚀 Démarrage du déploiement Transit Plus..."

# 1. Copie du .env si absent
if [ ! -f .env ]; then
    echo "📄 Création du fichier .env..."
    cp .env.example .env
    echo "⚠️  Configurez votre .env avant de continuer si nécessaire."
fi

# 2. Nettoyage et Lancement
echo "🏗️  Construction et lancement des conteneurs..."
docker-compose down
# Optionnel: sudo rm -rf docker/mysql # À décommenter si vous voulez un reset total
docker-compose up -d --build

# 3. Réglage des permissions (Crucial)
echo "🔒 Fixation des permissions des dossiers..."
docker exec -u root btrans-app chown -R btrans:www-data /var/www
docker exec -u root btrans-app chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# 4. Attente de la base de données
echo "⏳ Attente que la base de données soit prête..."
until docker exec btrans-db mariadb-admin ping --silent; do
    echo "   ...en attente de MariaDB..."
    sleep 3
done
echo "✅ Base de données opérationnelle !"

# 5. Installation et Setup Laravel
echo "📦 Installation des dépendances et setup..."
docker exec btrans-app composer install --no-dev --optimize-autoloader
docker exec btrans-app php artisan key:generate --force
docker exec btrans-app php artisan storage:link
docker exec btrans-app php artisan migrate:fresh --force
docker exec btrans-app php artisan db:seed --force

# 6. Optimisations finales
echo "⚡ Nettoyage et mise en cache..."
docker exec btrans-app php artisan config:cache
docker exec btrans-app php artisan route:cache
docker exec btrans-app php artisan view:cache

echo "-----------------------------------------------------------"
echo "✅ DÉPLOIEMENT TERMINÉ AVEC SUCCÈS !"
echo "🌐 Accès : http://votre-ip-serveur"
echo "-----------------------------------------------------------"
