<?php
// Manejo de sesión solo en la vista
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once __DIR__ . '/../../controllers/RutasController.php';
$controller = new RutasController();
$rutaModel = $controller->getRutasModel();

// Manejar eliminación
if (isset($_GET['action']) && $_GET['action'] === 'eliminar' && isset($_GET['id'])) {
    $result = $controller->eliminarRuta($_GET['id']);
    if (isset($result['success'])) {
        header('Location: list.php?success=' . urlencode($result['success']));
    } else {
        header('Location: list.php?error=' . urlencode($result['error']));
    }
    exit();
}

// Obtener datos
$stmt = $rutaModel->read();
$rutas = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRutas = $rutaModel->count();

// Mensajes
$success = isset($_GET['success']) ? $_GET['success'] : null;
$error = isset($_GET['error']) ? $_GET['error'] : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Rutas - AppCamiones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <style>
        body {
            padding-top: 70px;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        .main-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center; /* Centra verticalmente el contenido */
            padding: 2rem 0;
        }
        
        .page-header {
            padding: 1rem 0;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #dee2e6;
        }
        
        .page-title {
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            color: #212529;
        }
        
        .page-title i {
            margin-right: 10px;
        }

        .badge-activa {
            background-color: #28a745;
        }
        .badge-inactiva {
            background-color: #6c757d;
        }
        .badge-mantenimiento {
            background-color: #ffc107;
            color: #212529;
        }
        
        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .table-container {
            width: 100%;
            margin: 0 auto;
        }
        
        #rutasTable {
            width: 100% !important;
            margin: 20px 0;
            border: 1px solid #dee2e6;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        #rutasTable thead th {
            background-color: #0d6efd; /* Color azul de Bootstrap */
            color: white; /* Letras blancas */
            border-bottom: 2px solid #dee2e6;
            padding: 12px 15px;
            vertical-align: middle;
            font-weight: 600;
        }
        
        #rutasTable tbody td {
            padding: 10px 15px;
            vertical-align: middle;
        }
        
        .dataTables_wrapper {
            padding-top: 10px;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.375rem 0.75rem;
            border: 1px solid #dee2e6;
            margin-left: -1px;
            color: #0d6efd;
            transition: all 0.2s;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #0d6efd;
            color: white !important;
            border-color: #0d6efd;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #e9ecef;
            border-color: #dee2e6;
            color: #0a58ca !important;
            text-decoration: none;
        }
        
        .dataTables_wrapper .dataTables_info {
            padding-top: 0.75rem;
            color: #6c757d;
        }
        
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            padding: 0.375rem 0.75rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #86b7fe;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        
        .alert {
            border-radius: 0.25rem;
        }
    </style>
</head>
<body>
    <!-- Navbar de Bootstrap -->
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
                        <a class="nav-link active" href="list.php"><i class="bi bi-signpost-2 me-1"></i> Rutas</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['user']['nombre']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="../usuarios/perfil.php"><i class="bi bi-person me-2"></i> Mi Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="../../controllers/AuthController.php?logout=true"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="main-container container">
        <!-- Mostrar mensajes -->
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Cabecera normal -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="page-title">
                    <i class="bi bi-signpost-2"></i> Listado de Rutas
                </h2>
                <a href="add.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Nueva Ruta
                </a>
            </div>
        </div>

        <div class="table-container">
            <div class="table-responsive">
                <table id="rutasTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Distancia (km)</th>
                            <th>Tiempo Estimado</th>
                            <th>Dificultad</th>
                            <th>Estado</th>
                            <th>Fecha Registro</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rutas as $ruta): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($ruta['id_ruta']); ?></td>
                                <td><?php echo htmlspecialchars($ruta['origen']); ?></td>
                                <td><?php echo htmlspecialchars($ruta['destino']); ?></td>
                                <td><?php echo number_format($ruta['distancia'], 2); ?></td>
                                <td><?php echo htmlspecialchars($ruta['tiempo_estimado']); ?></td>
                                <td><?php echo htmlspecialchars($ruta['dificultad']); ?></td>
                                <td>
                                    <?php 
                                    $badgeClass = $ruta['estado'] === 'activa' ? 'badge-activa' : ($ruta['estado'] === 'inactiva' ? 'badge-inactiva' : 'badge-mantenimiento');
                                    ?>
                                    <span class="badge <?php echo $badgeClass; ?>">
                                        <?php echo htmlspecialchars($ruta['estado']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($ruta['fecha_registro'])); ?></td>
                                <td class="action-buttons text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="view.php?id=<?php echo $ruta['id_ruta']; ?>" class="btn btn-sm btn-info" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        
                                        <a href="edit.php?action=editar&id=<?php echo $ruta['id_ruta']; ?>" class="btn btn-sm btn-warning" title="Editar"> 
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <a href="list.php?action=eliminar&id=<?php echo $ruta['id_ruta']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           title="Eliminar" 
                                           onclick="return confirm('¿Estás seguro de eliminar esta ruta?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery y DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        // Cerrar automáticamente los mensajes después de 5 segundos
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Inicializar DataTable con configuración en español local
        $(document).ready(function() {
            $('#rutasTable').DataTable({
                language: {
                    "decimal": "",
                    "emptyTable": "No hay datos disponibles en la tabla",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                    "infoFiltered": "(filtrado de _MAX_ registros totales)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Mostrar _MENU_ registros por página",
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando...",
                    "search": "Buscar:",
                    "zeroRecords": "No se encontraron registros coincidentes",
                    "paginate": {
                        "first": "Primero",
                        "last": "Último",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    },
                    "aria": {
                        "sortAscending": ": activar para ordenar la columna ascendente",
                        "sortDescending": ": activar para ordenar la columna descendente"
                    }
                },
                responsive: true,
                dom: '<"top"<"d-flex justify-content-between align-items-center"lf>>rt<"bottom"ip>',
                initComplete: function() {
                    // Mover el buscador a la derecha
                    $('.dataTables_filter').addClass('float-end');
                },
                columnDefs: [
                    { 
                        orderable: false, 
                        targets: [8],
                        className: 'text-center'
                    },
                    {
                        targets: [0, 3, 4, 5, 6, 7],
                        className: 'text-center'
                    }
                ],
                drawCallback: function() {
                    // Ajustar el ancho de la tabla después de dibujar
                    $('#rutasTable').css('width', '100%');
                }
            });
        });
    </script>
</body>
</html>