<?php
// Manejo de sesión solo en la vista
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once __DIR__ . '/../../controllers/CamionesController.php';
$controller = new CamionesController();
$data = $controller->handleRequest();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Camión - AppCamiones</title>
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
                        <a class="nav-link active" href="list.php"><i class="bi bi-truck me-1"></i> Camiones</a>
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
    <main class="main-content">
        <div class="container">
            <div class="form-container">
                <h2 class="form-title text-center"><i class="bi bi-truck me-2"></i>Agregar Nuevo Camión</h2>
                
                <!-- Mensajes de feedback -->
                <?php if (isset($data['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?= htmlspecialchars($data['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($data['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <?= htmlspecialchars($data['success']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="add.php?action=agregar" id="camionForm">
                    <div class="row g-3">
                        <!-- Sección 1: Datos básicos -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="matricula" class="form-label required-field">Matrícula</label>
                                <input type="text" class="form-control" id="matricula" name="matricula" required
                                    value="<?= isset($_POST['matricula']) ? htmlspecialchars($_POST['matricula']) : '' ?>">
                                <div class="invalid-feedback" id="matriculaFeedback">La matrícula ya está registrada</div>
                            </div>
                            <div class="mb-3">
                                <label for="marca" class="form-label required-field">Marca</label>
                                <input type="text" class="form-control" id="marca" name="marca" required
                                    value="<?= isset($_POST['marca']) ? htmlspecialchars($_POST['marca']) : '' ?>">
                            </div>
                            <div class="mb-3">
                                <label for="modelo" class="form-label required-field">Modelo</label>
                                <input type="text" class="form-control" id="modelo" name="modelo" required
                                    value="<?= isset($_POST['modelo']) ? htmlspecialchars($_POST['modelo']) : '' ?>">
                            </div>
                            <div class="mb-3">
                                <label for="capacidad" class="form-label required-field">Capacidad (ton)</label>
                                <input type="number" step="0.01" class="form-control" id="capacidad" name="capacidad" required
                                    value="<?= isset($_POST['capacidad']) ? htmlspecialchars($_POST['capacidad']) : '' ?>">
                            </div>
                        </div>
                        
                        <!-- Sección 2: Estado y fechas -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estado" class="form-label required-field">Estado</label>
                                <select class="form-select" id="estado" name="id_estado_camion" required>
                                    <option value="">Seleccione un estado</option>
                                    <?php foreach ($data['estados'] as $estado): ?>
                                        <option value="<?= $estado['id_estado_camion'] ?>"
                                            <?= (isset($_POST['id_estado_camion']) && $_POST['id_estado_camion'] == $estado['id_estado_camion']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($estado['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="fecha_adquisicion" class="form-label required-field">Fecha adquisición</label>
                                <input type="date" class="form-control" id="fecha_adquisicion" name="fecha_adquisicion" required
                                    value="<?= isset($_POST['fecha_adquisicion']) ? htmlspecialchars($_POST['fecha_adquisicion']) : '' ?>">
                            </div>
                            <div class="mb-3">
                                <label for="ultima_revision" class="form-label">Última revisión</label>
                                <input type="date" class="form-control" id="ultima_revision" name="ultima_revision"
                                    value="<?= isset($_POST['ultima_revision']) ? htmlspecialchars($_POST['ultima_revision']) : '' ?>">
                            </div>
                            <div class="mb-3">
                                <label for="responsable" class="form-label">Responsable</label>
                                <select class="form-select" id="responsable" name="id_usuario_responsable">
                                    <option value="">Seleccione un responsable</option>
                                    <?php foreach ($data['conductores'] as $conductor): ?>
                                        <option value="<?= $conductor['id_usuario'] ?>"
                                            <?= (isset($_POST['id_usuario_responsable']) && $_POST['id_usuario_responsable'] == $conductor['id_usuario']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($conductor['nombre']) ?>
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
                        <?php if (isset($data['success'])): ?>
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
        // Validación de matrícula
        $('#matricula').on('blur', function() {
            const matricula = $(this).val();
            if (matricula.length === 0) return;
            
            $.post('add.php?check_matricula=1', {matricula: matricula}, function(response) {
                const input = $('#matricula');
                const feedback = $('#matriculaFeedback');
                
                if (response.exists) {
                    input.addClass('is-invalid');
                    feedback.show();
                } else {
                    input.removeClass('is-invalid');
                    feedback.hide();
                }
            }, 'json');
        });

        // Resetear formulario
        function resetForm() {
            document.getElementById('camionForm').reset();
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
            $('#matricula').focus();
        });
    </script>
</body>
</html>