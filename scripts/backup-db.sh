#!/bin/bash

# TRANSIT PLUS - AUTOMATED BACKUP SCRIPT
# This script backups the MariaDB database and the uploaded files.

BACKUP_DIR="./backups"
TIMESTAMP=$(date +"%Y-%m-%d_%H-%M-%S")
mkdir -p $BACKUP_DIR

echo "📂 Starting Backup: $TIMESTAMP"

# 1. Database Backup (MariaDB)
echo "💾 Dumping Database..."
docker exec btrans-db mariadb-dump -u btrans -ppassword btrans > "$BACKUP_DIR/db_backup_$TIMESTAMP.sql"

if [ $? -eq 0 ]; then
    echo "✅ Database dump successful."
else
    echo "❌ Database dump FAILED!"
fi

# 2. Files Backup (Storage/Uploads)
echo "📦 Archiving Uploads & Database..."
tar -czf "$BACKUP_DIR/full_backup_$TIMESTAMP.tar.gz" "$BACKUP_DIR/db_backup_$TIMESTAMP.sql" ./storage/app/public ./docker/mysql

if [ $? -eq 0 ]; then
    echo "✅ Full archive created: full_backup_$TIMESTAMP.tar.gz"
    # Clean up the raw SQL file to save space
    rm "$BACKUP_DIR/db_backup_$TIMESTAMP.sql"
else
    echo "❌ Archive creation FAILED!"
fi

# 3. Cleanup old backups (Keep last 7 days)
echo "🧹 Cleaning up old backups..."
find $BACKUP_DIR -type f -name "*.tar.gz" -mtime +7 -delete

echo "✨ Backup Process Completed!"
echo "-----------------------------------------------------------"
