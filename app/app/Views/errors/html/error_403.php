<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? '403 - Acceso denegado' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
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
            color: #dc3545;
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
            color: #dc3545;
            margin-bottom: 1rem;
            animation: shake 2s ease-in-out infinite;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        .btn-home {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        }
        .btn-login {
            background: linear-gradient(45deg, #007bff, #0056b3);
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
            margin-left: 10px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
        }
        .access-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 15px;
            padding: 1.5rem;
            margin-top: 2rem;
            text-align: left;
        }
        .access-info h6 {
            color: #856404;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .access-info p {
            color: #856404;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-container">
            <div class="error-icon">
                <i class="fas fa-ban"></i>
            </div>
            
            <div class="error-code">403</div>
            
            <h1 class="error-title">Acceso denegado</h1>
            
            <p class="error-message">
                No tienes permisos para acceder a esta sección del sistema.
                Si crees que deberías tener acceso, contacta al administrador.
            </p>
            
            <div class="d-flex justify-content-center flex-wrap gap-2 mb-4">
              
                <a href="<?= base_url('logout') ?>" class="btn btn-primary btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>Cambiar usuario
                </a>
            </div>
            
            <div class="access-info">
                <h6><i class="fas fa-info-circle me-2"></i>Información sobre acceso:</h6>
                <p>
                    Cada usuario tiene permisos específicos según su rol en el sistema. 
                    Si necesitas acceso adicional, solicítalo al administrador del sistema.
                </p>
            </div>
        </div>
    </div>
</body>
</html>