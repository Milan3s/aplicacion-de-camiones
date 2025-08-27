<?php
// Iniciar sesión si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar autenticación
if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once __DIR__ . '/../../controllers/CamionesController.php';
require_once __DIR__ . '/../../models/Camiones.php';

$controller = new CamionesController();
$camionModel = $controller->getCamionModel();

// Verificar ID del camión
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: list.php?error=ID no proporcionado');
    exit();
}

$id = (int)$_GET['id'];

// Obtener datos del camión
$camion = $camionModel->getCamionById($id);

if (!$camion) {
    header('Location: list.php?error=Camion no encontrado');
    exit();
}

// Obtener datos adicionales
$estados = $camionModel->getEstadosCamion();
$conductores = $camionModel->getConductores();

// Obtener nombre del estado y responsable
$nombreEstado = '';
$nombreResponsable = 'Sin asignar';

foreach ($estados as $estado) {
    if ($estado['id_estado_camion'] == $camion['id_estado_camion']) {
        $nombreEstado = $estado['nombre'];
        break;
    }
}

if (!empty($camion['id_usuario_responsable'])) {
    foreach ($conductores as $conductor) {
        if ($conductor['id_usuario'] == $camion['id_usuario_responsable']) {
            $nombreResponsable = $conductor['nombre'];
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Camión - AppCamiones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            padding-top: 56px;
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .details-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }
        .details-card {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border: none;
            border-radius: 0.5rem;
        }
        .card-header {
            border-radius: 0.5rem 0.5rem 0 0 !important;
        }
        .detail-item {
            margin-bottom: 1.25rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .detail-label {
            font-weight: 600;
            color: #495057;
            min-width: 150px;
            display: inline-block;
        }
        .detail-value {
            color: #212529;
        }
        .badge-estado {
            font-size: 0.9rem;
            padding: 0.5rem 0.8rem;
            border-radius: 0.25rem;
        }
        .action-buttons {
            margin-top: 2rem;
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
                        <a class="nav-link active" href="list.php"><i class="bi bi-truck me-1"></i> Camiones</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../usuarios/list.php"><i class="bi bi-people me-1"></i> Usuarios</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['user']['nombre']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="../auth/perfil.php"><i class="bi bi-person me-2"></i> Mi Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="../../controllers/AuthController.php?logout=true"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido principal - Centrado verticalmente -->
    <div class="details-container">
        <div class="container">
            <div class="details-card card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-truck me-2"></i>Detalles del Camión #<?php echo htmlspecialchars($camion['id_camion']); ?>
                        </h4>
                        <a href="list.php" class="btn btn-light btn-sm">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Matrícula:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($camion['matricula']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Marca:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($camion['marca']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Modelo:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($camion['modelo']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Capacidad:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($camion['capacidad']); ?> toneladas</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Estado:</span>
                                <span class="badge badge-estado 
                                    <?php echo $nombreEstado == 'disponible' ? 'bg-success' : 
                                          ($nombreEstado == 'en_mantenimiento' ? 'bg-warning text-dark' : 
                                          ($nombreEstado == 'en_ruta' ? 'bg-primary' : 'bg-secondary')); ?>">
                                    <?php echo htmlspecialchars($nombreEstado); ?>
                                </span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Fecha adquisición:</span>
                                <span class="detail-value"><?php echo date('d/m/Y', strtotime($camion['fecha_adquisicion'])); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Última revisión:</span>
                                <span class="detail-value">
                                    <?php echo $camion['ultima_revision'] ? date('d/m/Y', strtotime($camion['ultima_revision'])) : 'No registrada'; ?>
                                </span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Responsable:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($nombreResponsable); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="action-buttons d-flex justify-content-end">
                        <a href="edit.php?id=<?php echo $camion['id_camion']; ?>" class="btn btn-warning me-2">
                            <i class="bi bi-pencil me-1"></i> Editar
                        </a>
                        <a href="list.php?action=eliminar&id=<?php echo $camion['id_camion']; ?>" 
                           class="btn btn-danger" 
                           onclick="return confirm('¿Estás seguro de eliminar este camión?')">
                            <i class="bi bi-trash me-1"></i> Eliminar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>