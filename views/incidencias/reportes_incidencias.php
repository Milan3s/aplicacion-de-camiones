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

// Obtener datos para los reportes
$incidencias = $controller->listarIncidencias();
$totalIncidencias = $incidencias['total'];
$datosIncidencias = $incidencias['data'];

// Procesar datos para gráficos
$estados = [];
$tipos = [];
$prioridades = [];
$usuariosReportan = [];
$usuariosAsignados = [];

foreach ($datosIncidencias as $incidencia) {
    // Conteo por estado
    $estado = $incidencia['nombre_estado'];
    $estados[$estado] = ($estados[$estado] ?? 0) + 1;
    
    // Conteo por tipo
    $tipo = $incidencia['tipo_incidencia'];
    $tipos[$tipo] = ($tipos[$tipo] ?? 0) + 1;
    
    // Conteo por prioridad
    $prioridad = $incidencia['nombre_prioridad'];
    $prioridades[$prioridad] = ($prioridades[$prioridad] ?? 0) + 1;
    
    // Conteo por usuario que reporta
    $usuarioReporta = $incidencia['nombre_usuario_reporta'];
    $usuariosReportan[$usuarioReporta] = ($usuariosReportan[$usuarioReporta] ?? 0) + 1;
    
    // Conteo por usuario asignado
    $usuarioAsignado = $incidencia['nombre_usuario_asignado'] ?? 'Sin asignar';
    $usuariosAsignados[$usuarioAsignado] = ($usuariosAsignados[$usuarioAsignado] ?? 0) + 1;
}

// Preparar datos para gráficos
$estadosLabels = json_encode(array_keys($estados));
$estadosData = json_encode(array_values($estados));

$tiposLabels = json_encode(array_keys($tipos));
$tiposData = json_encode(array_values($tipos));

$prioridadesLabels = json_encode(array_keys($prioridades));
$prioridadesData = json_encode(array_values($prioridades));

$usuariosReportanLabels = json_encode(array_keys($usuariosReportan));
$usuariosReportanData = json_encode(array_values($usuariosReportan));

$usuariosAsignadosLabels = json_encode(array_keys($usuariosAsignados));
$usuariosAsignadosData = json_encode(array_values($usuariosAsignados));

// Obtener estadísticas de tiempo de resolución
$query = "SELECT 
            AVG(TIMESTAMPDIFF(HOUR, fecha_reporte, fecha_resolucion)) as avg_horas,
            MIN(TIMESTAMPDIFF(HOUR, fecha_reporte, fecha_resolucion)) as min_horas,
            MAX(TIMESTAMPDIFF(HOUR, fecha_reporte, fecha_resolucion)) as max_horas
          FROM incidencias 
          WHERE fecha_resolucion IS NOT NULL";
$stmt = $controller->db->connect()->query($query);
$tiemposResolucion = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes de Incidencias - AppCamiones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            padding-top: 70px;
            background-color: #f8f9fa;
        }
        
        .card {
            margin-bottom: 20px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .card-header {
            background-color: #0d6efd;
            color: white;
            font-weight: 600;
        }
        
        .stat-card {
            text-align: center;
            padding: 20px;
            border-radius: 8px;
            background-color: white;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .stat-value {
            font-size: 2.5rem;
            font-weight: bold;
            color: #0d6efd;
        }
        
        .stat-label {
            font-size: 1rem;
            color: #6c757d;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        .badge {
            font-size: 0.9em;
            padding: 0.5em 0.75em;
        }
    </style>
</head>
<body>
    <!-- Barra de navegación -->
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
                        <a class="nav-link active" href="reportes_incidencias.php"><i class="bi bi-graph-up me-1"></i> Reportes</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <span class="text-white me-3">
                        <i class="bi bi-person-circle me-1"></i>
                        <?= htmlspecialchars($_SESSION['user']['nombre']) ?>
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

    <!-- Contenido principal -->
    <div class="container py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2><i class="bi bi-graph-up me-2"></i>Reportes de Incidencias</h2>
                <p class="text-muted">Estadísticas y análisis de incidencias registradas</p>
            </div>
        </div>

        <!-- Resumen estadístico -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-value"><?= $totalIncidencias ?></div>
                    <div class="stat-label">Total Incidencias</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-value"><?= $estados['Resuelta'] ?? 0 ?></div>
                    <div class="stat-label">Incidencias Resueltas</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-value"><?= $estados['Pendiente'] ?? 0 ?></div>
                    <div class="stat-label">Incidencias Pendientes</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-value"><?= round($tiemposResolucion['avg_horas'] ?? 0, 1) ?>h</div>
                    <div class="stat-label">Tiempo promedio de resolución</div>
                </div>
            </div>
        </div>

        <!-- Gráficos principales -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-pie-chart me-2"></i>Incidencias por Estado
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="estadosChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-bar-chart me-2"></i>Incidencias por Tipo
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="tiposChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Segunda fila de gráficos -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-people me-2"></i>Incidencias por Usuario que Reporta
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="usuariosReportanChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-person-check me-2"></i>Incidencias por Usuario Asignado
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="usuariosAsignadosChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de resumen -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-table me-2"></i>Resumen de Incidencias
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Estado</th>
                                        <th>Tipo</th>
                                        <th>Prioridad</th>
                                        <th>Cantidad</th>
                                        <th>Porcentaje</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $contador = 0;
                                    foreach ($datosIncidencias as $incidencia):
                                        $contador++;
                                        $porcentaje = ($contador / $totalIncidencias) * 100;
                                    ?>
                                    <tr>
                                        <td><span class="badge bg-primary"><?= htmlspecialchars($incidencia['nombre_estado']) ?></span></td>
                                        <td><?= htmlspecialchars($incidencia['tipo_incidencia']) ?></td>
                                        <td><?= htmlspecialchars($incidencia['nombre_prioridad']) ?></td>
                                        <td><?= $contador ?></td>
                                        <td><?= round($porcentaje, 2) ?>%</td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Colores para los gráficos
        const backgroundColors = [
            'rgba(54, 162, 235, 0.7)',
            'rgba(255, 99, 132, 0.7)',
            'rgba(75, 192, 192, 0.7)',
            'rgba(255, 159, 64, 0.7)',
            'rgba(153, 102, 255, 0.7)',
            'rgba(255, 205, 86, 0.7)',
            'rgba(201, 203, 207, 0.7)'
        ];
        
        const borderColors = [
            'rgba(54, 162, 235, 1)',
            'rgba(255, 99, 132, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(255, 159, 64, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 205, 86, 1)',
            'rgba(201, 203, 207, 1)'
        ];

        // Gráfico de estados
        const estadosCtx = document.getElementById('estadosChart').getContext('2d');
        new Chart(estadosCtx, {
            type: 'pie',
            data: {
                labels: <?= $estadosLabels ?>,
                datasets: [{
                    data: <?= $estadosData ?>,
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });

        // Gráfico de tipos
        const tiposCtx = document.getElementById('tiposChart').getContext('2d');
        new Chart(tiposCtx, {
            type: 'bar',
            data: {
                labels: <?= $tiposLabels ?>,
                datasets: [{
                    label: 'Incidencias por Tipo',
                    data: <?= $tiposData ?>,
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de usuarios que reportan
        const usuariosReportanCtx = document.getElementById('usuariosReportanChart').getContext('2d');
        new Chart(usuariosReportanCtx, {
            type: 'doughnut',
            data: {
                labels: <?= $usuariosReportanLabels ?>,
                datasets: [{
                    data: <?= $usuariosReportanData ?>,
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });

        // Gráfico de usuarios asignados
        const usuariosAsignadosCtx = document.getElementById('usuariosAsignadosChart').getContext('2d');
        new Chart(usuariosAsignadosCtx, {
            type: 'polarArea',
            data: {
                labels: <?= $usuariosAsignadosLabels ?>,
                datasets: [{
                    data: <?= $usuariosAsignadosData ?>,
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
    </script>
</body>
</html>