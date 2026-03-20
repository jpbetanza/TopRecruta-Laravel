#!/bin/sh
set -e

DB_DATABASE="${DB_DATABASE:-/var/www/html/database/database.sqlite}"

# Generate APP_KEY if not set
if [ -z "${APP_KEY}" ]; then
    echo "WARNING: APP_KEY is not set. Generating automatically — set it persistently in your environment."
    export APP_KEY=$(php artisan key:generate --show --no-ansi)
fi

# Create SQLite database file if it doesn't exist
if [ ! -f "${DB_DATABASE}" ]; then
    touch "${DB_DATABASE}"
    chown www-data:www-data "${DB_DATABASE}"
fi

# Garantir diretório de uploads dentro do volume
UPLOADS_DIR="$(dirname ${DB_DATABASE})/uploads"
mkdir -p "${UPLOADS_DIR}"
chown -R www-data:www-data "${UPLOADS_DIR}"

# Run migrations
php artisan migrate --force --no-interaction

# Create storage symlink (public/storage → storage/app/public)
php artisan storage:link --force

# Re-run config cache so runtime secrets (APP_KEY, APP_URL, etc.) are included
php artisan config:cache

# Hand off to the container's main command (apache2-foreground)
exec "$@"
