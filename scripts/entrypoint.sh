#!/bin/sh
set -e

APP_DIR=/var/www/html

# Crea las carpetas de uploads (el volumen puede venir vacío)
mkdir -p "$APP_DIR/public/uploads/corredores" \
         "$APP_DIR/writable/cache" "$APP_DIR/writable/logs" "$APP_DIR/writable/session"

# Permisos
chown -R www-data:www-data "$APP_DIR/public/uploads" "$APP_DIR/writable" || true
find "$APP_DIR/public/uploads" "$APP_DIR/writable" -type d -exec chmod 2775 {} \; || true
find "$APP_DIR/public/uploads" "$APP_DIR/writable" -type f -exec chmod 0664 {} \; || true

echo "[Entrypoint] uploads/corredores asegurado."
exec apache2-foreground