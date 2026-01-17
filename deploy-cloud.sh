#!/bin/bash

# TRANSIT PLUS - CLOUD DEPLOYMENT SCRIPT (MARIADB EDITION)
echo "🚀 Starting Robust Cloud Deployment..."

# 1. Check for proxy_net
if [ ! "$(docker network ls | grep proxy_net)" ]; then
  docker network create proxy_net
fi

# 2. Setup Env
if [ ! -f .env ]; then
    cp .env.example .env
    echo "⚠️  Update your .env on VPS!"
fi

# 3. Clean and Restart
echo "🏗️  Building and launching containers..."
docker-compose -f docker-compose.cloud.yml down
docker-compose -f docker-compose.cloud.yml up -d --build

# 4. Fix Permissions
echo "🔒 Fixing directory permissions..."
docker exec -u root btrans-app chown -R btrans:www-data /var/www
docker exec -u root btrans-app chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# 5. Wait for Database
echo "⏳ Waiting for MariaDB to be ready..."
until docker exec btrans-db mariadb-admin ping --silent; do
    echo "   ...waiting for DB..."
    sleep 3
done
echo "✅ Database is ALIVE!"

# 6. Laravel Operations
echo "⚙️  Running Laravel Setup..."
docker exec btrans-app composer install --no-dev --optimize-autoloader
docker exec btrans-app php artisan key:generate --force
docker exec btrans-app php artisan storage:link
docker exec btrans-app php artisan migrate:fresh --force
docker exec btrans-app php artisan db:seed --force

# 7. Caching
docker exec btrans-app php artisan config:cache
docker exec btrans-app php artisan route:cache
docker exec btrans-app php artisan view:cache

echo "-----------------------------------------------------------"
echo "✅ DEPLOYMENT SUCCESSFUL!"
echo "🔗 Domain: https://transitplus.favoured.cloud"
echo "-----------------------------------------------------------"
