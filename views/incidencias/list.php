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

// Obtener incidencias usando el método del controlador
$result = $controller->listarIncidencias();
$incidencias = $result['data'];
$totalIncidencias = $result['total'];

$success = isset($_GET['success']) ? $_GET['success'] : null;
$error = isset($_GET['error']) ? $_GET['error'] : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Incidencias - AppCamiones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <style>
        body {
            padding-top: 56px;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        .main-container {
            flex: 1;
            padding: 2rem 0;
        }

        .mover-div {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 15px;
            margin-bottom: 2rem;
        }

        .badge-disponible { background-color: #28a745; }
        .badge-mantenimiento { background-color: #ffc107; color: #212529; }
        .badge-ruta { background-color: #007bff; }
        .badge-inactivo { background-color: #6c757d; }

        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        #incidenciasTable {
            width: 100% !important;
            margin: 0;
            border: 1px solid #dee2e6;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        #incidenciasTable thead th {
            background-color: #0d6efd;
            border-bottom: 2px solid #dee2e6;
            padding: 12px 15px;
            vertical-align: middle;
            color: white;
        }

        #incidenciasTable tbody td {
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

        .table-responsive {
            overflow-x: hidden;
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
                        <a class="nav-link active" href="management_incidencias.php"><i class="bi bi-exclamation-triangle me-1"></i> Gestión de Incidencias</a>
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
    <div class="main-container">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
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

                    <!-- Encabezado -->
                    <div class="mover-div">
                        <h2>
                            <i class="bi bi-exclamation-triangle me-2"></i>Gestión de Incidencias
                        </h2>
                        <a href="add.php" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> Nueva Incidencia
                        </a>
                    </div>

                    <!-- Tabla -->
                    <div class="table-responsive">
                        <table id="incidenciasTable" class="table table-striped table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Título</th>
                                    <th>Descripción</th>
                                    <th>Tipo</th>
                                    <th>Prioridad</th>
                                    <th>Estado</th>
                                    <th>Camión</th>
                                    <th>Reportada por</th>
                                    <th>Asignada a</th>
                                    <th>Fecha Reporte</th>
                                    <th>Resolución</th>
                                    <th class="action-buttons">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($incidencias as $incidencia): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($incidencia['id_incidencia']) ?></td>
                                        <td><?= htmlspecialchars($incidencia['titulo']) ?></td>
                                        <td><?= htmlspecialchars($incidencia['descripcion']) ?></td>
                                        <td><span class="badge bg-secondary"><?= htmlspecialchars($incidencia['tipo_incidencia']) ?></span></td>
                                        <td>
                                            <?php 
                                            $badgeClass = '';
                                            switch($incidencia['id_prioridad']) {
                                                case 1: $badgeClass = 'badge-disponible'; break;
                                                case 2: $badgeClass = 'badge-mantenimiento'; break;
                                                case 3: $badgeClass = 'badge-ruta'; break;
                                                default: $badgeClass = 'badge-inactivo';
                                            }
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($incidencia['nombre_prioridad']) ?></span>
                                        </td>
                                        <td>
                                            <?php 
                                            $badgeClass = '';
                                            switch($incidencia['id_estado_incidencia']) {
                                                case 1: $badgeClass = 'badge-disponible'; break;
                                                case 2: $badgeClass = 'badge-mantenimiento'; break;
                                                case 3: $badgeClass = 'badge-ruta'; break;
                                                default: $badgeClass = 'badge-inactivo';
                                            }
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($incidencia['nombre_estado']) ?></span>
                                        </td>
                                        <td><span class="badge bg-dark"><?= htmlspecialchars($incidencia['matricula_camion'] ?? 'N/A') ?></span></td>
                                        <td><span class="badge bg-primary"><?= htmlspecialchars($incidencia['nombre_usuario_reporta']) ?></span></td>
                                        <td><span class="badge bg-success"><?= htmlspecialchars($incidencia['nombre_usuario_asignado'] ?? 'N/A') ?></span></td>
                                        <td><?= date('d/m/Y H:i', strtotime($incidencia['fecha_reporte'])) ?></td>
                                        <td>
                                            <?php 
                                            $badgeClass = $incidencia['fecha_resolucion'] ? 'badge-disponible' : 'badge-mantenimiento';
                                            ?>
                                            <span class="badge <?= $badgeClass ?>">
                                                <?= $incidencia['fecha_resolucion'] ? date('d/m/Y H:i', strtotime($incidencia['fecha_resolucion'])) : 'Pendiente' ?>
                                            </span>
                                        </td>
                                        <td class="action-buttons">
                                            <div class="d-flex gap-2">
                                                <a href="view.php?id=<?= $incidencia['id_incidencia'] ?>" class="btn btn-sm btn-info" title="Ver">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="edit.php?id=<?= $incidencia['id_incidencia'] ?>" class="btn btn-sm btn-warning" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="delete.php?id=<?= $incidencia['id_incidencia'] ?>" class="btn btn-sm btn-danger" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar esta incidencia?')">
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
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // Cerrar mensajes automáticamente después de 5 segundos
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Configuración de DataTables
        $(document).ready(function() {
            $('#incidenciasTable').DataTable({
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
                scrollX: false,
                dom: '<"top"<"d-flex justify-content-between align-items-center"lf>>rt<"bottom"ip>',
                initComplete: function() {
                    $('.dataTables_filter').addClass('float-end');
                },
                columnDefs: [
                    { 
                        orderable: false, 
                        targets: [11],
                        className: 'text-center'
                    }
                ],
                drawCallback: function() {
                    $('#incidenciasTable').css('width', '100%');
                }
            });
        });
    </script>
</body>
</html>