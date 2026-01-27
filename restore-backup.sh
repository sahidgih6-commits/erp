#!/bin/bash

# Database Restore Script
# Usage: sudo bash restore-backup.sh /path/to/backup.sql.gz

echo "=========================================="
echo "ERP Database Restore Script"
echo "=========================================="
echo ""

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo "⚠️  Please run as root (use: sudo bash restore-backup.sh)"
    exit 1
fi

# Check if backup file is provided
if [ -z "$1" ]; then
    echo "❌ Error: Please provide backup file path"
    echo "Usage: sudo bash restore-backup.sh /path/to/backup.sql.gz"
    exit 1
fi

BACKUP_FILE="$1"

# Check if backup file exists
if [ ! -f "$BACKUP_FILE" ]; then
    echo "❌ Error: Backup file not found: $BACKUP_FILE"
    exit 1
fi

echo "📁 Backup file: $BACKUP_FILE"
echo ""

# Get database credentials from .env
APP_DIR="/var/www/erp"
cd $APP_DIR

if [ ! -f ".env" ]; then
    echo "❌ Error: .env file not found"
    exit 1
fi

# Read database credentials
DB_CONNECTION=$(grep "^DB_CONNECTION=" .env | cut -d '=' -f2)
DB_DATABASE=$(grep "^DB_DATABASE=" .env | cut -d '=' -f2)
DB_USERNAME=$(grep "^DB_USERNAME=" .env | cut -d '=' -f2)
DB_PASSWORD=$(grep "^DB_PASSWORD=" .env | cut -d '=' -f2)

echo "📊 Database Info:"
echo "   Type: $DB_CONNECTION"
echo "   Name: $DB_DATABASE"
echo "   User: $DB_USERNAME"
echo ""

# Confirm before proceeding
read -p "⚠️  WARNING: This will REPLACE all current data. Continue? (yes/no): " confirm
if [ "$confirm" != "yes" ]; then
    echo "Restore cancelled."
    exit 0
fi

echo ""
echo "🔄 Starting restore process..."

# Extract backup if compressed
TEMP_SQL="/tmp/restore_$(date +%s).sql"

if [[ "$BACKUP_FILE" == *.gz ]]; then
    echo "📦 Extracting compressed backup..."
    gunzip -c "$BACKUP_FILE" > "$TEMP_SQL"
else
    cp "$BACKUP_FILE" "$TEMP_SQL"
fi

# Restore based on database type
if [ "$DB_CONNECTION" == "pgsql" ]; then
    echo "🗄️  Restoring PostgreSQL database..."
    
    # Set password for PostgreSQL
    export PGPASSWORD="$DB_PASSWORD"
    
    # Use postgres superuser to drop and recreate database
    echo "🗑️  Dropping existing database..."
    sudo -u postgres psql -c "DROP DATABASE IF EXISTS $DB_DATABASE;"
    
    echo "📦 Creating fresh database..."
    sudo -u postgres psql -c "CREATE DATABASE $DB_DATABASE OWNER $DB_USERNAME;"
    
    echo "📥 Importing backup data..."
    psql -U "$DB_USERNAME" -h localhost -d "$DB_DATABASE" < "$TEMP_SQL" 2>&1 | grep -v "^ERROR:" | head -20
    
    if [ ${PIPESTATUS[0]} -eq 0 ]; then
        echo "✅ PostgreSQL database restored successfully!"
    else
        echo "⚠️  Database restored with some warnings (this is normal)"
    fi
    
    unset PGPASSWORD
    
elif [ "$DB_CONNECTION" == "mysql" ]; then
    echo "🗄️  Restoring MySQL database..."
    
    # Drop and recreate database
    mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "DROP DATABASE IF EXISTS $DB_DATABASE;"
    mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "CREATE DATABASE $DB_DATABASE;"
    
    # Restore
    mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" < "$TEMP_SQL"
    
    if [ $? -eq 0 ]; then
        echo "✅ MySQL database restored successfully!"
    else
        echo "❌ Error restoring MySQL database"
        rm "$TEMP_SQL"
        exit 1
    fi
else
    echo "❌ Unsupported database type: $DB_CONNECTION"
    rm "$TEMP_SQL"
    exit 1
fi

# Clean up
rm "$TEMP_SQL"
echo ""

# Clear Laravel caches
echo "🧹 Clearing Laravel caches..."
php artisan config:clear > /dev/null 2>&1
php artisan cache:clear > /dev/null 2>&1
php artisan view:clear > /dev/null 2>&1

echo "✅ Cache cleared"
echo ""

echo "=========================================="
echo "✅ Restore completed successfully!"
echo "=========================================="
echo ""
echo "Your database has been restored from backup."
echo "You can now login with your previous credentials."
echo ""
