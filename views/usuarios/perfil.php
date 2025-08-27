<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

require_once '../../config/database.php';
$db = new Database();
$conn = $db->connect();
$query = "SELECT nombre_rol FROM roles WHERE id_rol = :id_rol";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id_rol', $_SESSION['user']['id_rol'], PDO::PARAM_INT);
$stmt->execute();
$rol = $stmt->fetch(PDO::FETCH_ASSOC)['nombre_rol'];

// Formatear fecha de registro
$fecha_registro = "No disponible";
if(isset($_SESSION['user']['fecha_registro'])) {
    $fecha = new DateTime($_SESSION['user']['fecha_registro']);
    $fecha_registro = $fecha->format('d/m/Y H:i:s');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario - AppCamiones</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }
        
        body {
            background-color: var(--light-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 60px;
        }
        
        .profile-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }
        
        .profile-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #2c87d1 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
            position: relative;
        }
        
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 4px solid white;
            background-color: #eee;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: var(--secondary-color);
        }
        
        .profile-body {
            padding: 25px;
            background-color: white;
        }
        
        .info-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .info-label {
            font-size: 0.85rem;
            color: #6c757d;
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 1rem;
            color: var(--dark-color);
            font-weight: 500;
        }
        
        .profile-actions {
            background-color: #f8f9fa;
            padding: 15px;
            border-top: 1px solid #eee;
        }
        
        .btn-edit {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-edit:hover {
            background-color: #2c87d1;
            border-color: #2c87d1;
        }
        
        /* Estilos para el navbar */
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .dropdown-toggle {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: white;
        }
        
        .dropdown-toggle::after {
            margin-left: 5px;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .dropdown-item {
            padding: 8px 15px;
        }
        
        .dropdown-item:hover {
            background-color: #f8f9fa;
        }
        
        .dropdown-divider {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <!-- Navbar Responsive con Menú Desplegable -->
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
                        <a class="nav-link" href="list.php"><i class="bi bi-people me-1"></i> Usuarios</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <div class="dropdown">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['user']['nombre']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink">
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

    <!-- Contenido Principal -->
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card profile-card">
                    <!-- Encabezado con avatar -->
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <h4><?php echo htmlspecialchars($_SESSION['user']['nombre']); ?></h4>
                        <p class="mb-0"><?php echo htmlspecialchars($rol); ?></p>
                    </div>
                    
                    <!-- Cuerpo con información -->
                    <div class="profile-body">
                        <div class="info-item">
                            <div class="info-label">Nombre completo</div>
                            <div class="info-value"><?php echo htmlspecialchars($_SESSION['user']['nombre']); ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Correo electrónico</div>
                            <div class="info-value"><?php echo htmlspecialchars($_SESSION['user']['email']); ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Rol en el sistema</div>
                            <div class="info-value"><?php echo htmlspecialchars($rol); ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Miembro desde</div>
                            <div class="info-value"><?php echo htmlspecialchars($fecha_registro); ?></div>
                        </div>
                    </div>
                    
                    <!-- Acciones -->
                    <div class="profile-actions text-center">
                        <a href="../usuarios/edit.php?id=<?php echo $_SESSION['user']['id_usuario']; ?>" class="btn btn-edit btn-sm me-2">
                            <i class="bi bi-pencil-square"></i> Editar Perfil
                        </a>
                        <a href="../../controllers/AuthController.php?logout=true" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>