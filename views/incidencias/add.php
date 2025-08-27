<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once __DIR__ . '/../../controllers/IncidenciaController.php';
$controller = new IncidenciaController();

// Manejar el envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'agregar') {
    $result = $controller->crearIncidencia($_POST);
    if (isset($result['success'])) {
        header('Location: add.php?success=' . urlencode($result['success']));
        exit();
    } else {
        $error = $result['error'];
    }
}

// Obtener datos para los select
// Obtener datos para los select (sin fetchAll, ya que el controlador devuelve arrays)
$tiposIncidencia = $controller->obtenerTiposIncidencia();
$prioridades = $controller->obtenerPrioridades();
$estados = $controller->obtenerEstadosIncidencia();
$camiones = $controller->obtenerCamiones();
$rutas = $controller->obtenerRutas();
$usuarios = $controller->obtenerUsuarios();

// Mensajes
$success = isset($_GET['success']) ? $_GET['success'] : null;
$error = isset($error) ? $error : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Incidencia - AppCamiones</title>
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
        .form-container {
            max-width: 800px;
            width: 100%;
            margin: 0 auto;
            padding: 2rem;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
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
                        <a class="nav-link active" href="list.php"><i class="bi bi-exclamation-triangle me-1"></i> Incidencias</a>
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

    <!-- Contenido principal -->
    <main class="main-content">
        <div class="container">
            <div class="form-container">
                <h2 class="form-title text-center"><i class="bi bi-exclamation-triangle me-2"></i>Agregar Nueva Incidencia</h2>
                
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

                <form method="POST" action="add.php?action=agregar" id="incidenciaForm">
                    <div class="row g-3">
                        <!-- Datos básicos -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="titulo" class="form-label required-field">Título</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" required
                                    value="<?= isset($_POST['titulo']) ? htmlspecialchars($_POST['titulo']) : '' ?>">
                            </div>
                            <div class="mb-3">
                                <label for="descripcion" class="form-label required-field">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required><?= isset($_POST['descripcion']) ? htmlspecialchars($_POST['descripcion']) : '' ?></textarea>
                            </div>
                        </div>
                        
                        <!-- Tipo y prioridad -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tipo_incidencia" class="form-label required-field">Tipo de Incidencia</label>
                                <select class="form-select" id="tipo_incidencia" name="id_tipo_incidencia" required>
                                    <option value="">Seleccione un tipo</option>
                                    <?php foreach ($tiposIncidencia as $tipo): ?>
                                        <option value="<?= $tipo['id_tipo_incidencia'] ?>"
                                            <?= (isset($_POST['id_tipo_incidencia']) && $_POST['id_tipo_incidencia'] == $tipo['id_tipo_incidencia']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($tipo['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="prioridad" class="form-label required-field">Prioridad</label>
                                <select class="form-select" id="prioridad" name="id_prioridad" required>
                                    <option value="">Seleccione una prioridad</option>
                                    <?php foreach ($prioridades as $prioridad): ?>
                                        <option value="<?= $prioridad['id_prioridad'] ?>"
                                            <?= (isset($_POST['id_prioridad']) && $_POST['id_prioridad'] == $prioridad['id_prioridad']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($prioridad['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Estado, camión y ruta -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estado" class="form-label required-field">Estado</label>
                                <select class="form-select" id="estado" name="id_estado_incidencia" required>
                                    <option value="">Seleccione un estado</option>
                                    <?php foreach ($estados as $estado): ?>
                                        <option value="<?= $estado['id_estado_incidencia'] ?>"
                                            <?= (isset($_POST['id_estado_incidencia']) && $_POST['id_estado_incidencia'] == $estado['id_estado_incidencia']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($estado['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="camion" class="form-label">Camión (Opcional)</label>
                                <select class="form-select" id="camion" name="id_camion">
                                    <option value="">Seleccione un camión</option>
                                    <?php foreach ($camiones as $camion): ?>
                                        <option value="<?= $camion['id_camion'] ?>"
                                            <?= (isset($_POST['id_camion']) && $_POST['id_camion'] == $camion['id_camion']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($camion['matricula']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ruta" class="form-label">Ruta (Opcional)</label>
                                <select class="form-select" id="ruta" name="id_ruta">
                                    <option value="">Seleccione una ruta</option>
                                    <?php foreach ($rutas as $ruta): ?>
                                        <option value="<?= $ruta['id_ruta'] ?>"
                                            <?= (isset($_POST['id_ruta']) && $_POST['id_ruta'] == $ruta['id_ruta']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($ruta['origen'] . ' - ' . $ruta['destino']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="usuario_asignado" class="form-label">Usuario Asignado (Opcional)</label>
                                <select class="form-select" id="usuario_asignado" name="id_usuario_asignado">
                                    <option value="">Seleccione un usuario</option>
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <option value="<?= $usuario['id_usuario'] ?>"
                                            <?= (isset($_POST['id_usuario_asignado']) && $_POST['id_usuario_asignado'] == $usuario['id_usuario']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($usuario['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
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
                                <i class="bi bi-plus-circle me-1"></i> Agregar otra
                            </button>
                        <?php endif; ?>
                    </div>
                </form>

                <!-- Botón de volver al listado -->
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
        function resetForm() {
            document.getElementById('incidenciaForm').reset();
            $('.alert').alert('close');
            window.scrollTo({
                top: document.querySelector('.form-container').offsetTop - 100,
                behavior: 'smooth'
            });
        }

        $(document).ready(function() {
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
            $('#titulo').focus();
        });
    </script>
</body>
</html>