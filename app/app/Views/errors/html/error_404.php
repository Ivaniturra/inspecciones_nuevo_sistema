<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? '404 - Página no encontrada' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .error-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            padding: 3rem;
            text-align: center;
            max-width: 600px;
            margin: 0 auto;
        }
        .error-code {
            font-size: 8rem;
            font-weight: 900;
            color: #6c757d;
            line-height: 1;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }
        .error-title {
            font-size: 2rem;
            color: #495057;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        .error-message {
            font-size: 1.1rem;
            color: #6c757d;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .error-icon {
            font-size: 4rem;
            color: #ffc107;
            margin-bottom: 1rem;
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .btn-home {
            background: linear-gradient(45deg, #007bff, #0056b3);
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        }
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
        }
        .btn-back {
            background: transparent;
            color: #6c757d;
            border: 2px solid #dee2e6;
            border-radius: 50px;
            padding: 10px 25px;
            margin-right: 10px;
            transition: all 0.3s ease;
        }
        .btn-back:hover {
            background: #f8f9fa;
            border-color: #adb5bd;
            color: #495057;
        }
        .suggestions {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 1.5rem;
            margin-top: 2rem;
            text-align: left;
        }
        .suggestions h6 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .suggestions ul {
            margin: 0;
            padding-left: 1.2rem;
        }
        .suggestions li {
            color: #6c757d;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-container">
            <div class="error-icon">
                <i class="fas fa-search"></i>
            </div>
            
            <div class="error-code">404</div>
            
            <h1 class="error-title">Página no encontrada</h1>
            
            <p class="error-message">
                Lo sentimos, la página que estás buscando no existe o ha sido movida.
                Puede que hayas escrito mal la dirección o que el enlace esté desactualizado.
            </p>
            
            <div class="d-flex justify-content-center flex-wrap gap-2 mb-4">
                <button onclick="history.back()" class="btn btn-back">
                    <i class="fas fa-arrow-left me-2"></i>Volver atrás
                </button>
                <a href="<?= base_url('/') ?>" class="btn btn-primary btn-home">
                    <i class="fas fa-home me-2"></i>Ir al inicio
                </a>
            </div>
            
            <div class="suggestions">
                <h6><i class="fas fa-lightbulb me-2"></i>Sugerencias:</h6>
                <ul>
                    <li>Verifica que la URL esté escrita correctamente</li>
                    <li>Intenta buscar desde la página principal</li>
                    <li>Contacta al administrador si crees que esto es un error</li>
                </ul>
            </div>
        </div>
    </div>
    
    <script>
        // Agregar un poco de interactividad
        document.querySelector('.error-code').addEventListener('mouseover', function() {
            this.style.transform = 'scale(1.1)';
            this.style.transition = 'transform 0.3s ease';
        });
        
        document.querySelector('.error-code').addEventListener('mouseout', function() {
            this.style.transform = 'scale(1)';
        });
    </script>
</body>
</html>