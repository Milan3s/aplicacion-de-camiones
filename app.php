<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido - AppCamiones</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #3498db, #2c3e50);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .welcome-container {
            max-width: 500px;
            width: 100%;
            margin: 0 auto;
        }
        
        .welcome-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .welcome-header {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 30px;
        }
        
        .welcome-header i {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        
        .welcome-body {
            padding: 30px;
            background-color: white;
            text-align: center;
        }
        
        .welcome-title {
            margin-bottom: 20px;
            color: #2c3e50;
        }
        
        .welcome-text {
            margin-bottom: 30px;
            color: #6c757d;
        }
        
        .btn-welcome {
            background-color: #2c3e50;
            border: none;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s;
            margin: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-welcome:hover {
            background-color: #1a252f;
            transform: translateY(-2px);
        }
        
        .btn-icon {
            margin-right: 8px;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="welcome-container">
            <div class="card welcome-card">
                <div class="welcome-header">
                    <i class="bi bi-truck"></i>
                    <h2>AppCamiones</h2>
                </div>
                <div class="welcome-body">
                    <h3 class="welcome-title">Bienvenido al Sistema</h3>
                    <p class="welcome-text">Por favor, selecciona una opción:</p>
                    <div class="d-flex flex-wrap justify-content-center">
                        <a href="views/auth/register.php" class="btn btn-primary btn-welcome">
                            <i class="bi bi-person-plus btn-icon"></i> Registrarse
                        </a>
                        <a href="views/auth/login.php" class="btn btn-primary btn-welcome">
                            <i class="bi bi-box-arrow-in-right btn-icon"></i> Iniciar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>