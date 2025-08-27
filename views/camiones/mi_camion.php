<?php
session_start();

require_once __DIR__ . '/../../controllers/CamionesController.php';

// Instanciar el controlador
$controller = new CamionesController();

// Llamar directamente al método miCamion
$data = $controller->miCamion();

// Asignar las variables para la vista
$camion = $data['camion'] ?? null;
$estado_camion = $data['estado_camion'] ?? null;

// Incluir la vista
include_once __DIR__ . '/mi_camion.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Camión - AppCamiones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 70px;
        }
        .navbar-brand {
            font-weight: 700;
            color: white;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .badge {
            font-size: 0.9rem;
            padding: 0.5em 1em;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-truck me-2"></i>AppCamiones
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../../views/auth/dashboard.php">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="mi_camion.php">
                            <i class="bi bi-truck me-1"></i> Mi Camión
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../rutas/mi_ruta.php">
                            <i class="bi bi-signpost-2 me-1"></i> Mi Ruta
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../incidencias/add.php">
                            <i class="bi bi-exclamation-triangle me-1"></i> Reportar Incidencia
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <span class="text-white me-3">
                        <i class="bi bi-person-circle me-1"></i>
                        <?= htmlspecialchars($_SESSION['user']['nombre']) ?>
                        <span class="badge badge-conductor">Conductor</span>
                    </span>
                    <div class="dropdown">
                        <a class="dropdown-toggle text-white text-decoration-none" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-gear-fill"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="../usuarios/perfil.php">
                                <i class="bi bi-person me-2"></i> Mi Perfil
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="../../controllers/AuthController.php?logout=true">
                                <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="container py-5">
        <h1 class="display-5 fw-bold text-primary mb-4">
            <i class="bi bi-truck"></i> Mi Camión
        </h1>

        <?php if ($camion): ?>
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Detalles del Camión</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Matrícula:</strong> <?= htmlspecialchars($camion['matricula']) ?></p>
                            <p><strong>Marca:</strong> <?= htmlspecialchars($camion['marca']) ?></p>
                            <p><strong>Modelo:</strong> <?= htmlspecialchars($camion['modelo']) ?></p>
                            <p><strong>Capacidad:</strong> <?= htmlspecialchars($camion['capacidad']) ?> toneladas</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Estado:</strong> 
                                <span class="badge bg-<?= $estado_camion['nombre'] == 'disponible' ? 'success' : ($estado_camion['nombre'] == 'en ruta' ? 'info' : 'warning') ?>">
                                    <?= htmlspecialchars($estado_camion['nombre']) ?>
                                </span>
                            </p>
                            <p><strong>Fecha de Adquisición:</strong> <?= htmlspecialchars($camion['fecha_adquisicion']) ?></p>
                            <p><strong>Última Revisión:</strong> <?= htmlspecialchars($camion['ultima_revision']) ?: 'No disponible' ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                No tienes un camión asignado actualmente.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>