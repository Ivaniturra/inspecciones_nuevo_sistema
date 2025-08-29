#!/bin/sh
set -eu

APP_DIR=/var/www/html
UPLOADS="$APP_DIR/public/uploads"
WRITABLE="$APP_DIR/writable"

# Crear carpetas necesarias (volumen puede venir vacío)
mkdir -p "$UPLOADS/corredores" \
         "$WRITABLE/cache" "$WRITABLE/logs" "$WRITABLE/session"

# Propietario y permisos
chown -R www-data:www-data "$UPLOADS" "$WRITABLE" || true
# setgid en directorios para heredar el grupo www-data
chmod 2775 "$UPLOADS" "$UPLOADS/corredores" || true
find "$UPLOADS" "$WRITABLE" -type d -exec chmod 2775 {} \; || true
find "$UPLOADS" "$WRITABLE" -type f -exec chmod 0664 {} \; || true

echo "[Entrypoint] uploads/corredores asegurado."
exec apache2-foreground
