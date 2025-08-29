#!/bin/sh
set -eu

APP_DIR=/var/www/html
UPLOADS="$APP_DIR/public/uploads"
WRITABLE="$APP_DIR/writable"

# Crea carpetas (el volumen puede venir vacío)
mkdir -p "$UPLOADS/corredores" \
         "$WRITABLE/cache" "$WRITABLE/logs" "$WRITABLE/session"

# (Opcional) .htaccess para evitar ejecución en uploads
if [ ! -f "$UPLOADS/.htaccess" ]; then
  cat > "$UPLOADS/.htaccess" <<'HT'
RemoveType .php .phtml .phar
php_flag engine off
Options -ExecCGI
<FilesMatch "\.(php|phtml|phar|pl|py|rb|cgi|sh)$">
  Require all denied
</FilesMatch>
HT
fi

# Permisos (setgid en directorios para heredar grupo)
chown -R www-data:www-data "$UPLOADS" "$WRITABLE" || true
find "$UPLOADS" "$WRITABLE" -type d -exec chmod 2775 {} \; || true
find "$UPLOADS" "$WRITABLE" -type f -exec chmod 0664 {} \; || true

echo "[Entrypoint] uploads/corredores listo."
exec apache2-foreground
