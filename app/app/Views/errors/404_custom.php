<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página no encontrada - 404</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .error-container { min-height: 100vh; }
    </style>
</head>
<body>
    <div class="container-fluid error-container d-flex align-items-center justify-content-center">
        <div class="text-center">
            <h1 class="display-1 text-muted">404</h1>
            <h2 class="mb-4">Página no encontrada</h2>
            <p class="lead mb-4">La página que buscas no existe o ha sido movida.</p>
            <a href="<?= base_url() ?>" class="btn btn-primary">
                <i class="fas fa-home me-2"></i>Volver al inicio
            </a>
        </div>
    </div>
</body>
</html>