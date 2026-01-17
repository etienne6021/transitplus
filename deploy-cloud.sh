#!/bin/bash

# TRANSIT PLUS - CLOUD DEPLOYMENT SCRIPT (VPS + NPM)
# Domain: transitplus.favoured.cloud

echo "🚀 Starting Cloud Deployment for Transit Plus..."

# 1. Check for proxy_net network
if [ ! "$(docker network ls | grep proxy_net)" ]; then
  echo "🌐 Creating proxy_net network..."
  docker network create proxy_net
else
  echo "✅ proxy_net network already exists."
fi

# 2. Setup Environment
if [ ! -f .env ]; then
    echo "📄 Creating .env file from .env.example..."
    cp .env.example .env
    echo "⚠️  Please update .env with your production credentials!"
    echo "🔗 URL should be: APP_URL=https://transitplus.favoured.cloud"
fi

# 3. Build and Start Containers
echo "🏗️  Building and launching Docker containers (Cloud Mode)..."
docker-compose -f docker-compose.cloud.yml up -d --build

# 4. Fix Permissions (Crucial for VPS)
echo "🔒 Fixing directory permissions..."
docker exec -u root btrans-app chown -R btrans:www-data /var/www
docker exec -u root btrans-app chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# 5. Install Dependencies
echo "📦 Installing Composer dependencies..."
docker exec btrans-app composer install --no-dev --optimize-autoloader

# 5. Wait for Database
echo "⏳ Waiting for database to be ready..."
until docker exec btrans-db mysqladmin ping -h localhost --silent; do
    echo "   ...waiting for MySQL..."
    sleep 2
done
echo "✅ Database is UP!"

# 6. Laravel Setup
echo "⚙️  Running Laravel optimizations..."
docker exec btrans-app php artisan key:generate --force
docker exec btrans-app php artisan storage:link
docker exec btrans-app php artisan migrate --force
docker exec btrans-app php artisan db:seed --force

# 6. Optimization & Caching
echo "⚡ Caching configurations..."
docker exec btrans-app php artisan config:cache
docker exec btrans-app php artisan route:cache
docker exec btrans-app php artisan view:cache
docker exec btrans-app php artisan icons:cache

echo "-----------------------------------------------------------"
echo "✅ DEPLOYMENT COMPLETE!"
echo "🔗 Access: https://transitplus.favoured.cloud"
echo "🛠️  Nginx Proxy Manager Config:"
echo "   - Scheme: http"
echo "   - Forward Host: btrans-nginx"
echo "   - Forward Port: 80"
echo "   - Network: proxy_net"
echo "-----------------------------------------------------------"
