# CodeIgniter 4 + Docker (Starter)

Este es un starter para levantar CodeIgniter 4 con Docker desde cero.

## Requisitos
- Docker y Docker Compose instalados.
- Puertos libres: 8000 (web) y 3306 (MySQL).

## Pasos (desde cero)
1. **Opcional pero recomendado**: borrar lo que tengas corriendo del intento anterior desde la carpeta de tu proyecto viejo:
   ```bash
   docker compose down -v --remove-orphans
   ```
   Si no usabas Compose en esa carpeta, puedes hacer limpieza global (¡borra TODO!):
   ```bash
   docker system prune -a --volumes
   ```

2. Descomprime este zip en una carpeta vacía, por ejemplo `codeigniter4-docker/`.

3. Crea el proyecto CodeIgniter 4 dentro de `./app` usando Composer en contenedor (no necesitas tener Composer local):
   - Linux/Mac:
     ```bash
     bash scripts/init.sh
     ```
   - Windows (PowerShell):
     ```powershell
     .\scripts\init.ps1
     ```

4. Levanta los contenedores:
   ```bash
   docker compose up -d --build
   ```

5. Abre en el navegador:
   - http://localhost:8000

6. Configura la DB en `app/app/Config/Database.php` (CI4):
   ```php
   public $default = [
       'DSN'      => '',
       'hostname' => 'db',
       'username' => 'ciuser',
       'password' => 'cipass',
       'database' => 'codeigniter4',
       'DBDriver' => 'MySQLi',
       'DBPrefix' => '',
       'pConnect' => false,
       'DBDebug'  => (ENVIRONMENT !== 'production'),
       'cacheOn'  => false,
       'charset'  => 'utf8',
       'DBCollat' => 'utf8_general_ci',
       'swapPre'  => '',
       'encrypt'  => false,
       'compress' => false,
       'strictOn' => false,
       'failover' => [],
       'port'     => 3306,
   ];
   ```

## Scripts incluidos
- `scripts/init.sh` (Linux/Mac): crea `./app` con CodeIgniter 4 vía contenedor Composer.
- `scripts/init.ps1` (Windows PowerShell): hace lo mismo en Windows.

## Comandos útiles
- Ver logs:
  ```bash
  docker compose logs -f app
  docker compose logs -f db
  ```
- Reconstruir:
  ```bash
  docker compose up -d --build
  ```
- Apagar y borrar volúmenes del stack actual:
  ```bash
  docker compose down -v --remove-orphans
  ```

## Notas
- Si el puerto 3306 está en uso (otra instalación de MySQL local), cambia el mapeo en `docker-compose.yml`, por ejemplo: `"3307:3306"` y conecta al puerto 3307.
- El `.htaccess` de CodeIgniter viene listo; `mod_rewrite` ya está habilitado en el Dockerfile.
