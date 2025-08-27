<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once '../../config/database.php';
require_once '../../models/Usuarios.php';
$db = new Database();

// Obtener los roles para el formulario
$query = "SELECT id_rol, nombre_rol FROM roles";
$stmt = $db->connect()->query($query);
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Inicializar variables para el formulario y mensajes
$message = null;
$form_data = [
    'nombre' => '',
    'email' => '',
    'id_rol' => ''
];

// Procesar el formulario si se ha enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'create') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $id_rol = $_POST['id_rol'] ?? '';

    // Guardar los datos del formulario para repoblar en caso de error
    $form_data = [
        'nombre' => $nombre,
        'email' => $email,
        'id_rol' => $id_rol
    ];

    // Validaciones básicas en el lado del servidor
    $errors = [];
    if (empty($nombre) || strlen($nombre) < 2) {
        $errors[] = "El nombre debe tener al menos 2 caracteres.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El email no es válido.";
    }
    if (empty($password) || strlen($password) < 6) {
        $errors[] = "La contraseña debe tener al menos 6 caracteres.";
    }
    if (empty($id_rol) || !is_numeric($id_rol)) {
        $errors[] = "Debes seleccionar un rol válido.";
    }

    // Si no hay errores, intentar crear el usuario
    if (empty($errors)) {
        $usuarioModel = new Usuarios($db->connect());
        if ($usuarioModel->create($nombre, $email, $password, $id_rol)) {
            $message = ['type' => 'success', 'text' => 'Usuario añadido correctamente.'];
            // Limpiar el formulario después de un éxito
            $form_data = ['nombre' => '', 'email' => '', 'id_rol' => ''];
        } else {
            $message = ['type' => 'error', 'text' => 'Error al crear el usuario. Es posible que el email ya esté registrado.'];
        }
    } else {
        $message = ['type' => 'error', 'text' => implode('<br>', $errors)];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Usuario - AppCamiones</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 70px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Navbar styles */
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
        }
        
        .nav-link.active {
            font-weight: 500;
            background-color: rgba(255,255,255,0.1);
            border-radius: 4px;
        }
        
        /* Main content container */
        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }
        
        /* Card styles */
        .add-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 30px;
            width: 100%;
            max-width: 600px;
            margin: 20px;
            border: none;
        }
        
        .card-header {
            background: none;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
        }
        
        .card-header i {
            margin-right: 10px;
            font-size: 1.5rem;
            color: var(--primary-color);
        }
        
        .card-title {
            color: var(--secondary-color);
            margin: 0;
            font-weight: 600;
        }
        
        /* Form styles */
        .form-label {
            font-weight: 500;
            color: var(--secondary-color);
            margin-bottom: 8px;
        }
        
        .form-control {
            border-radius: 5px;
            padding: 10px;
            border: 1px solid #ddd;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            padding: 10px 15px;
            font-weight: 500;
            width: 100%;
            margin-top: 15px;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            transform: translateY(-1px);
        }
        
        .btn-back {
            display: inline-block;
            margin-top: 15px;
            color: var(--primary-color);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .btn-back:hover {
            text-decoration: underline;
            color: #2980b9;
        }
        
        /* Messages */
        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
            border: none;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        /* Responsive adjustments */
        @media (max-width: 576px) {
            .add-card {
                padding: 20px;
                margin: 10px;
            }
            
            .card-header i {
                font-size: 1.2rem;
            }
            
            .card-title {
                font-size: 1.2rem;
            }
        }
    </style>
    <script>
        function validateForm() {
            const nombre = document.forms["addUserForm"]["nombre"].value;
            const email = document.forms["addUserForm"]["email"].value;
            const password = document.forms["addUserForm"]["password"].value;
            const id_rol = document.forms["addUserForm"]["id_rol"].value;
            let errors = [];

            if (nombre.length < 2) {
                errors.push("El nombre debe tener al menos 2 caracteres.");
            }
            if (!email.match(/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/)) {
                errors.push("El email no es válido.");
            }
            if (password.length < 6) {
                errors.push("La contraseña debe tener al menos 6 caracteres.");
            }
            if (!id_rol) {
                errors.push("Debes seleccionar un rol.");
            }

            if (errors.length > 0) {
                alert(errors.join("\n"));
                return false;
            }
            return true;
        }
    </script>
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
                        <a class="nav-link active" href="list.php"><i class="bi bi-people me-1"></i> Usuarios</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['user']['nombre']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="perfil.php"><i class="bi bi-person me-2"></i> Mi Perfil</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i> Configuración</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="../../controllers/AuthController.php?logout=true"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content - Centered vertically -->
    <div class="main-content">
        <div class="add-card">
            <div class="card-header">
                <i class="bi bi-person-plus"></i>
                <h2 class="card-title">Añadir Nuevo Usuario</h2>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message['type']; ?>">
                    <?php echo htmlspecialchars($message['text']); ?>
                </div>
            <?php endif; ?>
            
            <form name="addUserForm" action="add.php?action=create" method="POST" onsubmit="return validateForm()">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre completo</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" 
                           value="<?php echo htmlspecialchars($form_data['nombre']); ?>" required minlength="2">
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?php echo htmlspecialchars($form_data['email']); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Mínimo 6 caracteres" required minlength="6">
                </div>
                
                <div class="mb-3">
                    <label for="id_rol" class="form-label">Rol</label>
                    <select class="form-select" id="id_rol" name="id_rol" required>
                        <option value="">Selecciona un rol</option>
                        <?php foreach ($roles as $rol): ?>
                            <option value="<?php echo $rol['id_rol']; ?>" <?php echo $form_data['id_rol'] == $rol['id_rol'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($rol['nombre_rol']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Guardar Usuario
                </button>
                
                <a href="list.php" class="btn-back">
                    <i class="bi bi-arrow-left me-1"></i> Volver al Listado
                </a>
            </form>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>