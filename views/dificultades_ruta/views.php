<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once __DIR__ . '/../../controllers/DificultadesRutaController.php';
$controller = new DificultadesRutaController();

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: list.php');
    exit();
}

$dificultad = $controller->obtenerDificultadPorId($id);
if (!$dificultad) {
    header('Location: list.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles Dificultad de Ruta - AppCamiones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            padding-top: 56px;
            background-color: #f8f9fa;
        }
        .detail-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .detail-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .detail-label {
            font-weight: bold;
            color: #666;
        }
        .detail-value {
            margin-top: 5px;
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
                        <a class="nav-link active" href="#">
                            <i class="bi bi-signpost-2 me-1"></i> Dificultades Ruta
                        </a>
                    </li>
                </ul>
                <div class="d-flex">
                    <span class="navbar-text text-white me-3">
                        <i class="bi bi-person-circle me-1"></i>
                        <?= htmlspecialchars($_SESSION['user']['nombre']) ?>
                    </span>
                    <a href="../../controllers/AuthController.php?logout=true" class="btn btn-outline-light">
                        <i class="bi bi-box-arrow-right me-1"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="container">
        <div class="detail-container">
            <h2 class="mb-4"><i class="bi bi-eye me-2"></i>Detalles de la Dificultad de Ruta</h2>
            
            <div class="detail-item">
                <div class="detail-label">ID</div>
                <div class="detail-value"><?= htmlspecialchars($dificultad['id_dificultad']) ?></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Nombre</div>
                <div class="detail-value"><?= htmlspecialchars($dificultad['nombre']) ?></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Descripción</div>
                <div class="detail-value"><?= htmlspecialchars($dificultad['descripcion'] ?? 'N/A') ?></div>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <a href="list.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
                <div>
                    <a href="edit.php?id=<?= $dificultad['id_dificultad'] ?>" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i> Editar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>