<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Conectar a la base de datos para buscar un usuario
require_once '../../config/database.php';
require_once '../../models/Usuarios.php';
$db = new Database();
$usuarioModel = new Usuarios($db->connect());

// Obtener el ID del usuario desde POST
if (!isset($_POST['id_usuario'])) {
    header('Location: list.php');
    exit();
}
$id_usuario = $_POST['id_usuario'];
$usuario = $usuarioModel->readOne($id_usuario);

// Verificar si el usuario existe
if (!$usuario) {
    header('Location: list.php');
    exit();
}

// Procesar la eliminación si se confirma
$message = null;
$show_form = true; // Controla si se muestra el formulario de confirmación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    $result = $usuarioModel->delete($id_usuario);
    $message = [
        'type' => $result['success'] ? 'success' : 'danger',
        'text' => $result['message']
    ];
    if ($result['success']) {
        $show_form = false; // Ocultar el formulario si la eliminación fue exitosa
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Eliminación</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <style>
        body {
            background-color: #f8f9fa;
        }
        .user-details p {
            margin: 5px 0;
        }
        .actions {
            display: flex;
            gap: 10px;
        }
        .btn-cancel {
            text-align: center;
            line-height: 1.5;
            text-decoration: none;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .nav-link i {
            margin-right: 5px;
        }
        .btn i {
            margin-right: 5px;
        }
        .dropdown-toggle i {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="bi bi-truck"></i> AppCamiones</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/dashboard.php"><i class="bi bi-house-door"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="bi bi-people"></i> Gestión de Usuarios</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person"></i> <?php echo htmlspecialchars($_SESSION['user']['nombre']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="../../controllers/AuthController.php?logout=true"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Pantalla de confirmación -->
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="card-title text-center mb-4">Confirmar Eliminación</h2>
                <?php if ($show_form): ?>
                    <div class="alert alert-warning text-center" role="alert">
                        ¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.
                    </div>
                    <div class="user-details">
                        <p><strong>ID:</strong> <?php echo htmlspecialchars($usuario['id_usuario']); ?></p>
                        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['nombre']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
                        <p><strong>Rol:</strong> <?php echo htmlspecialchars($usuario['nombre_rol']); ?></p>
                        <p><strong>Fecha de Registro:</strong> <?php echo htmlspecialchars($usuario['fecha_registro']); ?></p>
                    </div>
                    <form action="delete.php" method="POST">
                        <input type="hidden" name="id_usuario" value="<?php echo $usuario['id_usuario']; ?>">
                        <input type="hidden" name="confirm_delete" value="1">
                        <div class="actions">
                            <button type="submit" class="btn btn-danger flex-fill"><i class="bi bi-trash"></i> Sí, Eliminar</button>
                            <a href="list.php" class="btn btn-secondary btn-cancel flex-fill"><i class="bi bi-x-circle"></i> Cancelar</a>
                        </div>
                    </form>
                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $message['type']; ?> mt-3" role="alert">
                            <?php echo htmlspecialchars($message['text']); ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $message['type']; ?> mt-3" role="alert">
                            <?php echo htmlspecialchars($message['text']); ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <a href="list.php" class="btn btn-primary d-block mt-3"><i class="bi bi-arrow-left"></i> Volver al Listado</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS y Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>
</html>