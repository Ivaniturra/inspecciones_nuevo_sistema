#!/bin/sh
set -e

APP_DIR=/var/www/html

# Asegurar que existen las carpetas necesarias
mkdir -p "$APP_DIR/writable/cache" \
         "$APP_DIR/writable/logs" \
         "$APP_DIR/writable/session" \
         "$APP_DIR/public/uploads"

# Dar permisos correctos
chown -R www-data:www-data "$APP_DIR/writable" "$APP_DIR/public/uploads" || true
find "$APP_DIR/writable" "$APP_DIR/public/uploads" -type d -exec chmod 2775 {} \;
find "$APP_DIR/writable" "$APP_DIR/public/uploads" -type f -exec chmod 0664 {} \;

echo "[Entrypoint] Permisos aplicados ✅"
exec apache2-foreground
