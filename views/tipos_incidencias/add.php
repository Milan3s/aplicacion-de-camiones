<?php
// Manejo de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header('Location: ../../auth/login.php');
    exit();
}

require_once __DIR__ . '/../../controllers/TiposIncidenciasController.php';
$controller = new TiposIncidenciasController();

// Recuperar datos del formulario si hubo error
$formData = $_SESSION['form_data'] ?? [];
$error = $_SESSION['error'] ?? null;
unset($_SESSION['form_data'], $_SESSION['error']);

// Manejar envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->handleRequest();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Tipo de Incidencia - AppCamiones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            padding-top: 56px;
            background-color: #f8f9fa;
        }
        .form-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .form-title {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .is-invalid {
            border-color: #dc3545;
        }
        .invalid-feedback {
            color: #dc3545;
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
                        <a class="nav-link" href="../camiones/list.php"><i class="bi bi-truck me-1"></i> Camiones</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="list.php"><i class="bi bi-exclamation-triangle me-1"></i> Tipos Incidencia</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars($_SESSION['user']['nombre']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="../usuarios/perfil.php"><i class="bi bi-person me-2"></i> Mi Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="../../controllers/AuthController.php?logout=true"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container py-4">
        <div class="form-container">
            <div class="form-title">
                <h2><i class="bi bi-plus-circle me-2"></i>Agregar Tipo de Incidencia</h2>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="post" action="add.php?action=crear">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre *</label>
                    <input type="text" class="form-control <?= isset($error) ? 'is-invalid' : '' ?>" 
                           id="nombre" name="nombre" 
                           value="<?= htmlspecialchars($formData['nombre'] ?? '') ?>" 
                           required>
                    <div class="invalid-feedback">
                        Por favor proporcione un nombre válido.
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?= 
                        htmlspecialchars($formData['descripcion'] ?? '') 
                    ?></textarea>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="list.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Volver
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Cerrar automáticamente los mensajes después de 5 segundos
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>