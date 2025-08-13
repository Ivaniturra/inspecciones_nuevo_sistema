#!/usr/bin/env bash
set -e

# Crea carpeta app si no existe
mkdir -p app

# Usa contenedor de Composer para crear CodeIgniter 4 dentro de ./app
docker run --rm -v "$(pwd)/app":/app composer create-project codeigniter4/appstarter .

echo "Listo. Ahora ejecuta: docker compose up -d --build"
