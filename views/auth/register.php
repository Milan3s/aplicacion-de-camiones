<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - AppCamiones</title>
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
        
        .register-container {
            max-width: 400px;
            width: 100%;
            margin: 0 auto;
        }
        
        .register-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .register-header {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 20px;
        }
        
        .register-header i {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .register-body {
            padding: 30px;
            background-color: white;
        }
        
        .form-control {
            padding: 12px 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        
        .input-group-text {
            background-color: #f8f9fa;
            height: 50px;
            display: flex;
            align-items: center;
        }
        
        .btn-register {
            background-color: #2c3e50;
            border: none;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-register:hover {
            background-color: #1a252f;
        }
        
        .login-link {
            color: #2c3e50;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .login-link:hover {
            color: #3498db;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-container">
            <div class="card register-card">
                <div class="register-header">
                    <i class="bi bi-truck"></i>
                    <h2>AppCamiones</h2>
                    <p class="mb-0">Crear Cuenta</p>
                </div>
                <div class="register-body">
                    <form action="../../controllers/AuthController.php" method="POST">
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                <input type="text" name="nombre" class="form-control" placeholder="Nombre completo" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="Correo electrónico" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
                            </div>
                        </div>
                        <button type="submit" name="register" class="btn btn-primary btn-register w-100">
                            <i class="bi bi-person-plus me-2"></i> Registrarse
                        </button>
                        <div class="text-center mt-3">
                            <a href="login.php" class="login-link">
                                <i class="bi bi-box-arrow-in-right me-1"></i> ¿Ya tienes cuenta? Inicia sesión
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>