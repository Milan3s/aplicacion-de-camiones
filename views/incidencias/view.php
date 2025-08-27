<?php
// Manejo de sesión solo en la vista
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header('Location: ' . BASE_URL . '/views/auth/login.php');
    exit();
}

require_once __DIR__ . '/../../controllers/IncidenciaController.php';
$controller = new IncidenciaController();

// Obtener los datos de la incidencia usando el método handleViewRequest del controlador
$incidencia = $controller->handleViewRequest();

// Verificar si hay un error (manejo opcional)
if (isset($incidencia['error'])) {
    // Redirigir con mensaje de error o mostrar en la vista
    header('Location: list.php?error=' . urlencode($incidencia['error']));
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de Incidencia - AppCamiones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            padding-top: 56px;
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
        .detail-container {
            max-width: 800px;
            width: 100%;
            margin: 0 auto;
            padding: 2rem;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }
        .detail-title {
            color: #2c3e50;
            border-bottom: 2px solid #f8f9fa;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }
        .detail-card {
            border-left: 4px solid #0d6efd;
            padding-left: 1rem;
            margin-bottom: 1.5rem;
        }
        .detail-label {
            font-weight: 600;
            color: #495057;
        }
        .detail-value {
            color: #212529;
        }
        .badge-prioridad-alta {
            background-color: #dc3545;
            color: white;
        }
        .badge-prioridad-media {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-prioridad-baja {
            background-color: #198754;
            color: white;
        }
        .badge-estado-pendiente {
            background-color: #6c757d;
            color: white;
        }
        .badge-estado-en-progreso {
            background-color: #0d6efd;
            color: white;
        }
        .badge-estado-resuelto {
            background-color: #198754;
            color: white;
        }
        .btn-action {
            min-width: 120px;
        }
        .text-muted-small {
            font-size: 0.875rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL . '/views/auth/dashboard.php'; ?>">
                <i class="bi bi-truck me-2"></i>AppCamiones
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL . '/views/auth/dashboard.php'; ?>"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo BASE_URL . '/views/incidencias/list.php'; ?>"><i class="bi bi-exclamation-triangle me-1"></i> Incidencias</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['user']['nombre']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="<?php echo BASE_URL . '/views/auth/perfil.php'; ?>"><i class="bi bi-person me-2"></i> Mi Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL . '/controllers/AuthController.php?logout=true'; ?>"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido principal - Centrado verticalmente -->
    <main class="main-content">
        <div class="container">
            <div class="detail-container">
                <h2 class="detail-title text-center">
                    <i class="bi bi-exclamation-triangle me-2"></i>Detalles de Incidencia #<?php echo htmlspecialchars($incidencia['id_incidencia']); ?>
                </h2>
                
                <!-- Sección de información básica -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="detail-card">
                            <h5 class="mb-3"><i class="bi bi-info-circle me-2"></i>Información Principal</h5>
                            <div class="mb-3">
                                <span class="detail-label">Título:</span>
                                <span class="detail-value d-block"><?php echo htmlspecialchars($incidencia['titulo']); ?></span>
                            </div>
                            <div class="mb-3">
                                <span class="detail-label">Descripción:</span>
                                <span class="detail-value d-block"><?php echo htmlspecialchars($incidencia['descripcion']); ?></span>
                            </div>
                            <div class="mb-3">
                                <span class="detail-label">Tipo de Incidencia:</span>
                                <span class="detail-value d-block"><?php echo htmlspecialchars($incidencia['tipo_incidencia']); ?></span>
                            </div>
                            <div class="mb-3">
                                <span class="detail-label">Camión:</span>
                                <span class="detail-value d-block"><?php echo htmlspecialchars($incidencia['matricula_camion'] ?? 'No asignado'); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="detail-card">
                            <h5 class="mb-3"><i class="bi bi-clipboard-data me-2"></i>Estado y Prioridad</h5>
                            <div class="mb-4">
                                <span class="detail-label">Prioridad:</span>
                                <?php
                                $prioridadClass = 'badge-prioridad-baja';
                                if (strtolower($incidencia['nombre_prioridad']) === 'alta') {
                                    $prioridadClass = 'badge-prioridad-alta';
                                } elseif (strtolower($incidencia['nombre_prioridad']) === 'media') {
                                    $prioridadClass = 'badge-prioridad-media';
                                }
                                ?>
                                <span class="badge <?php echo $prioridadClass; ?> rounded-pill"><?php echo htmlspecialchars($incidencia['nombre_prioridad']); ?></span>
                            </div>
                            <div>
                                <span class="detail-label">Estado:</span>
                                <?php
                                $estadoClass = 'badge-estado-pendiente';
                                if (strtolower($incidencia['nombre_estado']) === 'en progreso') {
                                    $estadoClass = 'badge-estado-en-progreso';
                                } elseif (strtolower($incidencia['nombre_estado']) === 'resuelto') {
                                    $estadoClass = 'badge-estado-resuelto';
                                }
                                ?>
                                <span class="badge <?php echo $estadoClass; ?> rounded-pill"><?php echo htmlspecialchars($incidencia['nombre_estado']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sección de usuarios y fechas -->
                <div class="detail-card">
                    <h5 class="mb-3"><i class="bi bi-person me-2"></i>Información de Usuarios y Fechas</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <span class="detail-label">Usuario que Reporta:</span>
                                <span class="detail-value d-block"><?php echo htmlspecialchars($incidencia['nombre_usuario_reporta']); ?></span>
                            </div>
                            <div class="mb-3">
                                <span class="detail-label">Usuario Asignado:</span>
                                <span class="detail-value d-block"><?php echo htmlspecialchars($incidencia['nombre_usuario_asignado'] ?? 'No asignado'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <span class="detail-label">Fecha de Reporte:</span>
                                <span class="detail-value d-block"><?php echo date('d/m/Y H:i', strtotime($incidencia['fecha_reporte'])); ?></span>
                            </div>
                            <div class="mb-3">
                                <span class="detail-label">Fecha de Resolución:</span>
                                <span class="detail-value d-block"><?php echo $incidencia['fecha_resolucion'] ? date('d/m/Y H:i', strtotime($incidencia['fecha_resolucion'])) : 'No resuelta'; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Botones de acción -->
                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                    <a href="<?php echo BASE_URL . '/views/incidencias/list.php'; ?>" class="btn btn-outline-secondary btn-action">
                        <i class="bi bi-arrow-left me-1"></i> Volver al listado
                    </a>
                    <div>
                        <a href="<?php echo BASE_URL . '/views/incidencias/edit.php?id=' . $incidencia['id_incidencia']; ?>" class="btn btn-primary btn-action me-2">
                            <i class="bi bi-pencil me-1"></i> Editar
                        </a>
                        <a href="<?php echo BASE_URL . '/views/incidencias/delete.php?id=' . $incidencia['id_incidencia']; ?>" 
                           class="btn btn-outline-danger btn-action"
                           onclick="return confirm('¿Estás seguro de eliminar esta incidencia? Esta acción no se puede deshacer.')">
                            <i class="bi bi-trash me-1"></i> Eliminar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-enfocar el primer elemento al cargar
        document.addEventListener('DOMContentLoaded', function() {
            // Puedes añadir funcionalidad JavaScript aquí si es necesario
        });
    </script>
</body>
</html>