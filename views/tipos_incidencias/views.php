<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ' . dirname(__DIR__) . '/auth/login.php');
    exit();
}

require_once __DIR__ . '/../../controllers/TiposIncidenciasController.php';
$controller = new TiposIncidenciasController();

$id = $_GET['id'] ?? null;
if (!$id) {
    $_SESSION['error'] = 'ID de tipo de incidencia no especificado';
    header('Location: list.php');
    exit();
}

try {
    $tipo = $controller->obtenerTipoPorId($id);
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: list.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles Tipo Incidencia - AppCamiones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            padding-top: 56px;
            background-color: #f8f9fa;
        }
        .detail-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .detail-header {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .detail-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .detail-label {
            font-weight: 600;
            color: #495057;
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
                        <a class="nav-link" href="../auth/dashboard.php">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="list.php">
                            <i class="bi bi-exclamation-triangle me-1"></i> Tipos Incidencia
                        </a>
                    </li>
                </ul>
                <div class="d-flex">
                    <span class="navbar-text text-white me-3">
                        <i class="bi bi-person-circle me-1"></i>
                        <?= htmlspecialchars($_SESSION['user']['nombre']) ?>
                    </span>
                    <a href="<?= dirname(__DIR__, 2) . '/controllers/AuthController.php?logout=true' ?>" class="btn btn-outline-light">
                        <i class="bi bi-box-arrow-right me-1"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="container py-4">
        <div class="detail-container">
            <div class="detail-header d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Detalles del Tipo de Incidencia
                </h2>
                <span class="badge bg-primary">ID: <?= htmlspecialchars($tipo['id_tipo_incidencia']) ?></span>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="detail-item">
                <div class="detail-label">Nombre</div>
                <div class="fs-5"><?= htmlspecialchars($tipo['nombre']) ?></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Descripción</div>
                <div><?= !empty($tipo['descripcion']) ? htmlspecialchars($tipo['descripcion']) : 'N/A' ?></div>
            </div>

            <div class="mt-4">
                <a href="list.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Volver al listado
                </a>
            </div>
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