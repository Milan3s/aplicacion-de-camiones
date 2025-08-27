<?php
session_start();

// Desactivar caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Usar nombre_rol desde la sesión, con un valor por defecto si es NULL
$rol = $_SESSION['user']['nombre_rol'] ?? 'Desconocido';

// Definir tipos de roles basados en id_rol
$esAdmin = $_SESSION['user']['id_rol'] == 1;      // ID 1 para Administrador
$esSupervisor = $_SESSION['user']['id_rol'] == 2; // ID 2 para Supervisor
$esConductor = $_SESSION['user']['id_rol'] == 3;  // ID 3 para Conductor
$esInformatico = $_SESSION['user']['id_rol'] == 4;// ID 4 para Informático

// Formatear fecha de registro
$fecha_registro = "No disponible";
if (isset($_SESSION['user']['fecha_registro'])) {
    $fecha = new DateTime($_SESSION['user']['fecha_registro']);
    $fecha_registro = $fecha->format('d/m/Y H:i:s');
}

// Definir BASE_URL si no está definido en otro lugar
if (!defined('BASE_URL')) {
    define('BASE_URL', '/AppCamiones');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - AppCamiones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --admin-color: #6f42c1;
            --conductor-color: #fd7e14;
            --supervisor-color: #20c997;
            --informatico-color: #dc3545;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 70px;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: white;
        }
        
        /* Estilos para el panel de bienvenida */
        .welcome-panel {
            background: linear-gradient(135deg, #ffffff 0%, #f1f8ff 100%);
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.08);
            border: none;
            overflow: hidden;
            position: relative;
            padding: 0;
        }
        
        .welcome-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(52, 152, 219, 0.1) 0%, rgba(52, 152, 219, 0.05) 100%);
            z-index: 1;
        }
        
        .welcome-content {
            positionresultado: relative;
            z-index: 2;
            padding: 2.5rem;
        }
        
        .welcome-date {
            background: var(--primary-color);
            color: white;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
        }
        
        .welcome-date i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .welcome-title {
            font-size: 2.2rem;
            font-weight: 800;
            background: linear-gradient(to right, var(--primary-color), #2c3e50);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }
        
        .welcome-subtitle {
            font-size: 1.2rem;
            color: #495057;
            margin-bottom: 1.5rem;
            position: relative;
            padding-left: 1.5rem;
        }
        
        .welcome-subtitle::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 70%;
            width: 5px;
            background: var(--primary-color);
            border-radius: 3px;
        }
        
        /* Estilos para paneles según rol */
        .panel-admin {
            border-left: 5px solid var(--admin-color);
        }
        
        .panel-conductor {
            border-left: 5px solid var(--conductor-color);
        }
        
        .panel-supervisor {
            border-left: 5px solid var(--supervisor-color);
        }
        
        .panel-informatico {
            border-left: 5px solid var(--informatico-color);
        }
        
        /* Tarjetas de acciones rápidas */
        .quick-action-card {
            transition: all 0.3s ease;
            height: 100%;
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .quick-action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .quick-action-card .card-body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            height: 100%;
        }
        
        /* Badges para roles */
        .badge-rol {
            font-size: 0.8rem;
            padding: 0.35em 0.65em;
            color: white;
            border-radius: 12px;
            font-weight: 500;
        }
        
        .badge-administrador {
            background-color: var(--admin-color);
            box-shadow: 0 0 8px rgba(111, 66, 193, 0.7);
            border: 1px solid #ffffff;
            animation: pulse-admin 2s infinite;
        }
        
        .badge-conductor {
            background-color: var(--conductor-color);
            box-shadow: 0 0 8px rgba(253, 126, 20, 0.7);
            border: 1px solid #ffffff;
            animation: pulse-conductor 2s infinite;
        }
        
        .badge-supervisor {
            background-color: var(--supervisor-color);
            box-shadow: 0 0 8px rgba(32, 201, 151, 0.7);
            border: 1px solid #ffffff;
            animation: pulse-supervisor 2s infinite;
        }
        
        .badge-informatico {
            background-color: var(--informatico-color);
            box-shadow: 0 0 8px rgba(220, 53, 69, 0.7);
            border: 1px solid #ffffff;
            animation: pulse-informatico 2s infinite;
        }

        .badge-desconocido {
            background-color: #6c757d;
            box-shadow: 0 0 8px rgba(108, 117, 125, 0.7);
            border: 1px solid #ffffff;
        }

        span.badge.badge-informatico {
            background-color: black;
            border-radius: 10px;
            padding: 8px;
            box-shadow: 0 0 8px rgba(108, 117, 125, 0.7);
        }

        /* Animaciones para los badges */
        @keyframes pulse-admin {
            0% { box-shadow: 0 0 8px rgba(111, 66, 193, 0.7); }
            50% { box-shadow: 0 0 12px rgba(111, 66, 193, 1); }
            100% { box-shadow: 0 0 8px rgba(111, 66, 193, 0.7); }
        }

        @keyframes pulse-conductor {
            0% { box-shadow: 0 0 8px rgba(253, 126, 20, 0.7); }
            50% { box-shadow: 0 0 12px rgba(253, 126, 20, 1); }
            100% { box-shadow: 0 0 8px rgba(253, 126, 20, 0.7); }
        }

        @keyframes pulse-supervisor {
            0% { box-shadow: 0 0 8px rgba(32, 201, 151, 0.7); }
            50% { box-shadow: 0 0 12px rgba(32, 201, 151, 1); }
            100% { box-shadow: 0 0 8px rgba(32, 201, 151, 0.7); }
        }

        @keyframes pulse-informatico {
            0% { box-shadow: 0 0 8px rgba(220, 53, 69, 0.7); }
            50% { box-shadow: 0 0 12px rgba(220, 53, 69, 1); }
            100% { box-shadow: 0 0 8px rgba(220, 53, 69, 0.7); }
        }
        
        /* Estilo para el dropdown con nombre de usuario */
        .user-dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .user-name {
            font-weight: 500;
            color: white;
        }
        
        /* Estilos para las tarjetas de estadísticas */
        .stats-card {
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-card-header {
            padding: 1rem;
            color: white;
            font-weight: 600;
        }
        
        .stats-card-body {
            padding: 1.5rem;
            background: white;
        }
        
        .stats-value {
            font-size: 2rem;
            font-weight: 700;
        }
        
        .stats-label {
            font-size: 0.9rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="bi bi-truck me-2"></i>AppCamiones
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    </li>
                    
                    <!-- Opciones para Administrador -->
                    <?php if ($esAdmin): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-people me-1"></i> Usuarios
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../usuarios/list.php">
                                <i class="bi bi-list me-2"></i> Gestión de Usuarios
                            </a></li>
                            <li><a class="dropdown-item" href="../usuarios/reportes_usuarios.php">
                                <i class="bi bi-bar-chart me-2"></i> Estadísticas de Usuarios
                            </a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-truck me-1"></i> Camiones
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../camiones/list.php">
                                <i class="bi bi-truck-front me-2"></i> Gestión de Camiones
                            </a></li>
                            <li><a class="dropdown-item" href="../estados_camion/list.php">
                                <i class="bi bi-truck-front-fill me-2"></i> Estado del Camión
                            </a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-signpost-2 me-1"></i> Rutas
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../rutas/list.php">
                                <i class="bi bi-signpost-2-fill me-2"></i> Gestión de Rutas
                            </a></li>
                            <li><a class="dropdown-item" href="../dificultades_ruta/list.php">
                                <i class="bi bi-exclamation-circle me-2"></i> Dificultad de Rutas
                            </a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-exclamation-triangle me-1"></i> Incidencias
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../incidencias/list.php">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i> Gestión de Incidencias
                            </a></li>
                            <li><a class="dropdown-item" href="../incidencias/reportes_incidencias.php">
                                <i class="bi bi-graph-up me-2"></i> Estadísticas de Incidencias
                            </a></li>
                            <li><a class="dropdown-item" href="../incidencias/add.php">
                                <i class="bi bi-exclamation-triangle me-2"></i> Reportar Incidencia
                            </a></li>
                            <li><a class="dropdown-item" href="../tipos_incidencias/list.php">
                                <i class="bi bi-list-ul me-2"></i> Tipos de Incidencias
                            </a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                    
                    <!-- Opciones para Supervisor -->
                    <?php if ($esSupervisor): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-people me-1"></i> Usuarios
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../usuarios/list.php">
                                <i class="bi bi-list me-2"></i> Gestión de Conductores
                            </a></li>
                            <li><a class="dropdown-item" href="../usuarios/reportes_usuarios.php">
                                <i class="bi bi-bar-chart me-2"></i> Estadísticas de Conductores
                            </a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-truck me-1"></i> Camiones
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../camiones/list.php">
                                <i class="bi bi-truck-front me-2"></i> Gestión de Camiones
                            </a></li>
                            <li><a class="dropdown-item" href="../estados_camion/list.php">
                                <i class="bi bi-truck-front-fill me-2"></i> Estado del Camión
                            </a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-signpost-2 me-1"></i> Rutas
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../rutas/list.php">
                                <i class="bi bi-signpost-2-fill me-2"></i> Gestión de Rutas
                            </a></li>
                            <li><a class="dropdown-item" href="../dificultades_ruta/list.php">
                                <i class="bi bi-exclamation-circle me-2"></i> Dificultad de Rutas
                            </a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-exclamation-triangle me-1"></i> Incidencias
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../incidencias/list.php">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i> Gestión de Incidencias
                            </a></li>
                            <li><a class="dropdown-item" href="../incidencias/reportes_incidencias.php">
                                <i class="bi bi-graph-up me-2"></i> Estadísticas de Incidencias
                            </a></li>
                            <li><a class="dropdown-item" href="../incidencias/add.php">
                                <i class="bi bi-exclamation-triangle me-2"></i> Reportar Incidencia
                            </a></li>
                            <li><a class="dropdown-item" href="../tipos_incidencias/list.php">
                                <i class="bi bi-list-ul me-2"></i> Tipos de Incidencias
                            </a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                    
                    <!-- Opciones para Conductor -->
                    <?php if ($esConductor): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-truck me-1"></i> Camiones
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../camiones/mi_camion.php">
                                <i class="bi bi-truck me-2"></i> Mi Camión
                            </a></li>
                            <li><a class="dropdown-item" href="../estados_camion/list.php">
                                <i class="bi bi-truck-front-fill me-2"></i> Estado del Camión
                            </a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-signpost-2 me-1"></i> Rutas
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../rutas/mi_ruta.php">
                                <i class="bi bi-signpost-2 me-2"></i> Mi Ruta
                            </a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-exclamation-triangle me-1"></i> Incidencias
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../incidencias/add.php">
                                <i class="bi bi-exclamation-triangle me-2"></i> Reportar Incidencia
                            </a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                    
                    <!-- Opciones para Informático -->
                    <?php if ($esInformatico): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-truck me-1"></i> Camiones
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../camiones/list.php">
                                <i class="bi bi-truck me-2"></i> Ver Camiones
                            </a></li>
                            <li><a class="dropdown-item" href="../estados_camion/list.php">
                                <i class="bi bi-truck-front-fill me-2"></i> Estado del Camión
                            </a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-signpost-2 me-1"></i> Rutas
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../rutas/list.php">
                                <i class="bi bi-signpost-2 me-2"></i> Ver Rutas
                            </a></li>
                            <li><a class="dropdown-item" href="../dificultades_ruta/list.php">
                                <i class="bi bi-exclamation-circle me-2"></i> Dificultad de Rutas
                            </a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-exclamation-triangle me-1"></i> Incidencias
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../incidencias/list.php">
                                <i class="bi bi-exclamation-triangle me-2"></i> Ver Incidencias
                            </a></li>
                            <li><a class="dropdown-item" href="../incidencias/reportes_incidencias.php">
                                <i class="bi bi-graph-up me-2"></i> Estadísticas de Incidencias
                            </a></li>
                            <li><a class="dropdown-item" href="../tipos_incidencias/list.php">
                                <i class="bi bi-list-ul me-2"></i> Tipos de Incidencias
                            </a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                </ul>
                <div class="d-flex align-items-center">
                    <div class="dropdown">
                        <a class="dropdown-toggle text-decoration-none user-dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="user-avatar">
                                <i class="bi bi-person-fill text-white"></i>
                            </div>
                            <span class="user-name"><?= htmlspecialchars($_SESSION['user']['nombre']) ?></span>
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
        <!-- Panel de Bienvenida -->
        <div class="welcome-panel mb-5">
            <div class="row g-0">
                <div class="col-md-8">
                    <div class="welcome-content">
                        <h1 class="welcome-title">
                            <i class="bi bi-speedometer2"></i> Panel de Control
                        </h1>
                        <p class="welcome-subtitle">Bienvenido, <?= htmlspecialchars($_SESSION['user']['nombre']) ?></p>
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge <?= 'badge-'.strtolower($rol) ?>">
                                <?= htmlspecialchars($rol) ?>
                            </span>
                            <span class="text-muted">
                                <i class="bi bi-calendar3 me-1"></i> Miembro desde: <?= $fecha_registro ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="welcome-date">
                        <i class="bi bi-calendar-check"></i>
                        <div class="h4 mb-0"><?= date('d/m/Y') ?></div>
                        <small><?= date('H:i') ?></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel para Administrador -->
        <?php if ($esAdmin): ?>
        <!-- Panel de Gestión de Usuarios y Roles -->
        <div class="card panel-admin mb-5">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-shield-lock me-2"></i> 
                    Panel de Administración - Gestión de Usuarios y Roles
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="../usuarios/list.php" class="text-decoration-none">
                            <div class="card quick-action-card">
                                <div class="card-body">
                                    <i class="bi bi-people-fill fs-1 text-primary mb-3"></i>
                                    <h5>Gestión de Usuarios</h5>
                                    <p class="text-muted mb-0">Administra todos los usuarios del sistema</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="../roles/list.php" class="text-decoration-none">
                            <div class="card quick-action-card">
                                <div class="card-body">
                                    <i class="bi bi-person-vcard fs-1 text-primary mb-3"></i>
                                    <h5>Gestión de Roles</h5>
                                    <p class="text-muted mb-0">Administra los roles del sistema</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de Gestión Operativa -->
        <div class="card panel-admin mb-5">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-truck me-2"></i> 
                    Panel de Administración - Gestión Operativa
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="../camiones/list.php" class="text-decoration-none">
                            <div class="card quick-action-card">
                                <div class="card-body">
                                    <i class="bi bi-truck-front fs-1 text-success mb-3"></i>
                                    <h5>Gestión de Camiones</h5>
                                    <p class="text-muted mb-0">Administra la flota de camiones</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="../rutas/list.php" class="text-decoration-none">
                            <div class="card quick-action-card">
                                <div class="card-body">
                                    <i class="bi bi-signpost-2-fill fs-1 text-info mb-3"></i>
                                    <h5>Gestión de Rutas</h5>
                                    <p class="text-muted mb-0">Administra las rutas disponibles</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de Configuraciones Adicionales y Reportes -->
        <div class="card panel-admin mb-5">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-gear me-2"></i> 
                    Panel de Administración - Configuraciones y Reportes
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="../estados_camion/list.php" class="text-decoration-none">
                            <div class="card quick-action-card">
                                <div class="card-body">
                                    <i class="bi bi-truck-front-fill fs-1 text-secondary mb-3"></i>
                                    <h5>Estado del Camión</h5>
                                    <p class="text-muted mb-0">Consulta el estado de los camiones</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="../dificultades_ruta/list.php" class="text-decoration-none">
                            <div class="card quick-action-card">
                                <div class="card-body">
                                    <i class="bi bi-exclamation-circle-fill fs-1 text-danger mb-3"></i>
                                    <h5>Ver Dificultades</h5>
                                    <p class="text-muted mb-0">Consulta las dificultades de las rutas</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="../tipos_incidencias/list.php" class="text-decoration-none">
                            <div class="card quick-action-card">
                                <div class="card-body">
                                    <i class="bi bi-list-ul fs-1 text-primary mb-3"></i>
                                    <h5>Gestión de Tipos</h5>
                                    <p class="text-muted mb-0">Administra los tipos de incidencias</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="../incidencias/reportes_incidencias.php" class="text-decoration-none">
                            <div class="card quick-action-card">
                                <div class="card-body">
                                    <i class="bi bi-graph-up fs-1 text-warning mb-3"></i>
                                    <h5>Estadísticas de Incidencias</h5>
                                    <p class="text-muted mb-0">Reportes y análisis de incidencias</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Panel para Supervisor -->
        <?php if ($esSupervisor): ?>
        <!-- Panel de Supervisión Operativa -->
        <div class="card panel-supervisor mb-5">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clipboard-check me-2"></i> 
                    Panel de Supervisión - Operaciones
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="../usuarios/list.php" class="text-decoration-none">
                            <div class="card quick-action-card">
                                <div class="card-body">
                                    <i class="bi bi-people-fill fs-1 text-primary mb-3"></i>
                                    <h5>Gestión de Conductores</h5>
                                    <p class="text-muted mb-0">Administra los conductores</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="../usuarios/reportes_usuarios.php" class="text-decoration-none">
                            <div class="card quick-action-card">
                                <div class="card-body">
                                    <i class="bi bi-bar-chart-line fs-1 text-info mb-3"></i>
                                    <h5>Estadísticas de Conductores</h5>
                                    <p class="text-muted mb-0">Consulta estadísticas de conductores</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="../camiones/list.php" class="text-decoration-none">
                            <div class="card quick-action-card">
                                <div class="card-body">
                                    <i class="bi bi-truck-front fs-1 text-success mb-3"></i>
                                    <h5>Gestión de Camiones</h5>
                                    <p class="text-muted mb-0">Administra la flota de camiones</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="../rutas/list.php" class="text-decoration-none">
                            <div class="card quick-action-card">
                                <div class="card-body">
                                    <i class="bi bi-signpost-2-fill fs-1 text-info mb-3"></i>
                                    <h5>Gestión de Rutas</h5>
                                    <p class="text-muted mb-0">Administra las rutas disponibles</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de Configuraciones Adicionales -->
        <div class="card panel-supervisor mb-5">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-gear me-2"></i> 
                    Panel de Supervisión - Configuraciones
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="../estados_camion/list.php" class="text-decoration-none">
                            <div class="card quick-action-card">
                                <div class="card-body">
                                    <i class="bi bi-truck-front-fill fs-1 text-secondary mb-3"></i>
                                    <h5>Estado del Camión</h5>
                                    <p class="text-muted mb-0">Consulta el estado de los camiones</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="../dificultades_ruta/list.php" class="text-decoration-none">
                            <div class="card quick-action-card">
                                <div class="card-body">
                                    <i class="bi bi-exclamation-circle-fill fs-1 text-danger mb-3"></i>
                                    <h5>Ver Dificultades</h5>
                                    <p class="text-muted mb-0">Consulta las dificultades de las rutas</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="../tipos_incidencias/list.php" class="text-decoration-none">
                            <div class="card quick-action-card">
                                <div class="card-body">
                                    <i class="bi bi-list-ul fs-1 text-primary mb-3"></i>
                                    <h5>Gestión de Tipos</h5>
                                    <p class="text-muted mb-0">Administra los tipos de incidencias</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de Reportes y Estadísticas -->
        <div class="card panel-supervisor mb-5">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-graph-up me-2"></i> 
                    Panel de Supervisión - Reportes
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="../usuarios/reportes_usuarios.php" class="text-decoration-none">
                            <div class="card quick-action-card">
                                <div class="card-body">
                                    <i class="bi bi-bar-chart-line fs-1 text-primary mb-3"></i>
                                    <h5>Estadísticas de Conductores</h5>
                                    <p class="text-muted mb-0">Reportes de actividad de conductores</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="../incidencias/reportes_incidencias.php" class="text-decoration-none">
                            <div class="card quick-action-card">
                                <div class="card-body">
                                    <i class="bi bi-graph-up fs-1 text-warning mb-3"></i>
                                    <h5>Estadísticas de Incidencias</h5>
                                    <p class="text-muted mb-0">Reportes y análisis de incidencias</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Panel para Conductor -->
        <?php if ($esConductor): ?>
        <div class="card panel-conductor mb-5">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-person-workspace me-2"></i> 
                    Panel del Conductor
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="../camiones/mi_camion.php" class="text-decoration-none">
                            <div class="card quick-action-card">
                                <div class="card-body">
                                    <i class="bi bi-truck fs-1 text-primary mb-3"></i>
                                    <h5>Mi Camión</h5>
                                    <p class="text-muted mb-0">Consulta tu camión asignado</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="../rutas/mi_ruta.php" class="text-decoration-none">
                            <div class="card quick-action-card">
                                <div class="card-body">
                                    <i class="bi bi-signpost-2 fs-1 text-info mb-3"></i>
                                    <h5>Mi Ruta</h5>
                                    <p class="text-muted mb-0">Consulta tu ruta asignada</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="../incidencias/add.php" class="text-decoration-none">
                            <div class="card quick-action-card">
                                <div class="card-body">
                                    <i class="bi bi-exclamation-triangle fs-1 text-warning mb-3"></i>
                                    <h5>Reportar Incidencia</h5>
                                    <p class="text-muted mb-0">Reporta una nueva incidencia</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de Configuraciones Adicionales -->
        <div class="card panel-conductor mb-5">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-gear me-2"></i> 
                    Panel del Conductor - Configuraciones
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="../estados_camion/list.php" class="text-decoration-none">
                            <div class="card quick-action-card">
                                <div class="card-body">
                                    <i class="bi bi-truck-front-fill fs-1 text-secondary mb-3"></i>
                                    <h5>Estado del Camión</h5>
                                    <p class="text-muted mb-0">Consulta el estado de tu camión</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="../incidencias/reportes_incidencias.php" class="text-decoration-none">
                            <div class="card quick-action-card">
                                <div class="card-body">
                                    <i class="bi bi-graph-up fs-1 text-primary mb-3"></i>
                                    <h5>Estadísticas de Incidencias</h5>
                                    <p class="text-muted mb-0">Consulta estadísticas de tus incidencias</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Panel para Informático -->
        <?php if ($esInformatico): ?>
        <!-- Panel de Consulta Operativa -->
        <div class="card panel-informatico mb-5">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-gear me-2"></i> 
                    Panel Informático - Consulta
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="../camiones/list.php" class="text-decoration-none">
                            <div class="card quick-action-card">
                                <div class="card-body">
                                    <i class="bi bi-truck fs-1 text-primary mb-3"></i>
                                    <h5>Ver Camiones</h5>
                                    <p class="text-muted mb-0">Consulta los camiones</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="../rutas/list.php" class="text-decoration-none">
                            <div class="card quick-action-card">
                                <div class="card-body">
                                    <i class="bi bi-signpost-2 fs-1 text-info mb-3"></i>
                                    <h5>Ver Rutas</h5>
                                    <p class="text-muted mb-0">Consulta las rutas</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="../incidencias/list.php" class="text-decoration-none">
                            <div class="card quick-action-card">
                                <div class="card-body">
                                    <i class="bi bi-exclamation-triangle fs-1 text-warning mb-3"></i>
                                    <h5>Ver Incidencias</h5>
                                    <p class="text-muted mb-0">Consulta las incidencias reportadas</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="../usuarios/list.php" class="text-decoration-none">
                            <div class="card quick-action-card">
                                <div class="card-body">
                                    <i class="bi bi-people-fill fs-1 text-primary mb-3"></i>
                                    <h5>Ver Usuarios</h5>
                                    <p class="text-muted mb-0">Consulta los usuarios del sistema</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de Configuraciones Adicionales -->
        <div class="card panel-informatico mb-5">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-gear me-2"></i> 
                    Panel Informático - Configuraciones
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="../estados_camion/list.php" class="text-decoration-none">
                            <div class="card quick-action-card">
                                <div class="card-body">
                                    <i class="bi bi-truck-front-fill fs-1 text-secondary mb-3"></i>
                                    <h5>Estado del Camión</h5>
                                    <p class="text-muted mb-0">Consulta el estado de los camiones</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="../dificultades_ruta/list.php" class="text-decoration-none">
                            <div class="card quick-action-card">
                                <div class="card-body">
                                    <i class="bi bi-exclamation-circle-fill fs-1 text-danger mb-3"></i>
                                    <h5>Ver Dificultades</h5>
                                    <p class="text-muted mb-0">Consulta las dificultades de las rutas</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="../tipos_incidencias/list.php" class="text-decoration-none">
                            <div class="card quick-action-card">
                                <div class="card-body">
                                    <i class="bi bi-list-ul fs-1 text-primary mb-3"></i>
                                    <h5>Ver Tipos</h5>
                                    <p class="text-muted mb-0">Consulta los tipos de incidencias</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de Reportes -->
        <div class="card panel-informatico mb-5">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-graph-up me-2"></i> 
                    Panel Informático - Reportes
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="../incidencias/reportes_incidencias.php" class="text-decoration-none">
                            <div class="card quick-action-card">
                                <div class="card-body">
                                    <i class="bi bi-graph-up fs-1 text-warning mb-3"></i>
                                    <h5>Estadísticas de Incidencias</h5>
                                    <p class="text-muted mb-0">Reportes técnicos de incidencias</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="../roles/list.php" class="text-decoration-none">
                            <div class="card quick-action-card">
                                <div class="card-body">
                                    <i class="bi bi-person-vcard fs-1 text-primary mb-3"></i>
                                    <h5>Ver Roles</h5>
                                    <p class="text-muted mb-0">Consulta los roles del sistema</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Mensaje de error si no se encuentra un panel para el rol -->
        <?php if (!$esAdmin && !$esConductor && !$esSupervisor && !$esInformatico): ?>
        <div class="alert alert-warning text-center" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            No se encontró un panel para el rol asignado (ID: <?= htmlspecialchars($_SESSION['user']['id_rol']) ?>). Por favor, contacta al administrador.
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>