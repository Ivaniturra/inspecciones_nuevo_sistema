Param()

$ErrorActionPreference = "Stop"

if (!(Test-Path "app")) {
    New-Item -ItemType Directory -Path "app" | Out-Null
}

# Usar contenedor Composer para crear CodeIgniter 4
docker run --rm -v "${PWD}/app:/app" composer create-project codeigniter4/appstarter .

Write-Host "Listo. Ahora ejecuta: docker compose up -d --build"
