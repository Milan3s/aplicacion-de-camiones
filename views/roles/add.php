<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once __DIR__ . '/../../controllers/RolesController.php';
$controller = new RolesController();
$data = $controller->handleRequest();

$success = isset($data['success']) ? $data['success'] : null;
$error = isset($data['error']) ? $data['error'] : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Rol - AppCamiones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            padding-top: 56px; /* Ajustado para coincidir con Rutas */
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 2rem 0;
        }
        
        .form-container {
            max-width: 800px; /* Ajustado para coincidir con Rutas */
            width: 100%;
            margin: 0 auto;
            padding: 2rem;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1); /* Sombra más pronunciada como en Rutas */
        }
        
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
        
        .form-title {
            color: #2c3e50;
            border-bottom: 2px solid #f8f9fa;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }
        
        .btn-action {
            min-width: 120px;
        }
        
        .footer-buttons {
            margin-top: 2rem;
            display: flex;
            justify-content: space-between;
        }
        
        .alert {
            border-radius: 0.25rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand" href="../auth/dashboard.php">
                <i class="bi bi-truck me-2"></i>AppCamiones
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/dashboard.php"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="list.php"><i class="bi bi-person-gear me-1"></i> Roles</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['user']['nombre']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="../usuarios/perfil.php"><i class="bi bi-person me-2"></i> Mi Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="../../controllers/AuthController.php?logout=true"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido principal - Centrado verticalmente -->
    <main class="main-content">
        <div class="container">
            <div class="form-container">
                <h2 class="form-title text-center"><i class="bi bi-person-gear me-2"></i>Agregar Nuevo Rol</h2>
                
                <!-- Mensajes de feedback -->
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <?= htmlspecialchars($success) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="add.php?action=agregar" id="rolForm">
                    <div class="row g-3">
                        <!-- Campo: Nombre del Rol -->
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="nombre_rol" class="form-label required-field">Nombre del Rol</label>
                                <input type="text" class="form-control" id="nombre_rol" name="nombre_rol" required
                                    value="<?= isset($_POST['nombre_rol']) ? htmlspecialchars($_POST['nombre_rol']) : '' ?>">
                            </div>
                        </div>
                        <!-- Campo: Descripción -->
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="descripcion" class="form-label required-field">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required><?= isset($_POST['descripcion']) ? htmlspecialchars($_POST['descripcion']) : '' ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <div>
                            <button type="submit" class="btn btn-primary btn-action me-2">
                                <i class="bi bi-save me-1"></i> Guardar
                            </button>
                            <button type="reset" class="btn btn-outline-secondary btn-action">
                                <i class="bi bi-eraser me-1"></i> Limpiar
                            </button>
                        </div>
                        <?php if (isset($success)): ?>
                            <button type="button" class="btn btn-outline-primary btn-action" onclick="resetForm()">
                                <i class="bi bi-plus-circle me-1"></i> Agregar otro
                            </button>
                        <?php endif; ?>
                    </div>
                </form>

                <!-- Botón de volver al listado debajo del formulario -->
                <div class="text-center mt-4">
                    <a href="list.php" class="btn btn-secondary btn-action">
                        <i class="bi bi-arrow-left me-1"></i> Volver al listado
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Resetear formulario
        function resetForm() {
            document.getElementById('rolForm').reset();
            $('.alert').alert('close');
            // Hacer scroll al inicio del formulario
            window.scrollTo({
                top: document.querySelector('.form-container').offsetTop - 100,
                behavior: 'smooth'
            });
        }

        // Auto-ocultar mensajes después de 5 segundos
        $(document).ready(function() {
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
            
            // Asegurar que el foco esté en el primer campo al cargar
            $('#nombre_rol').focus();
        });
    </script>
</body>
</html>