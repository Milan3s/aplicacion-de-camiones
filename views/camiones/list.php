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
$camionModel = $controller->getCamionModel();

// Manejar eliminación
if (isset($_GET['action']) && $_GET['action'] === 'eliminar' && isset($_GET['id'])) {
    $result = $controller->eliminarCamion($_GET['id']);
    if (isset($result['success'])) {
        header('Location: list.php?success=' . urlencode($result['success']));
    } else {
        header('Location: list.php?error=' . urlencode($result['error']));
    }
    exit();
}

// Obtener datos
$stmt = $camionModel->read();
$camiones = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalCamiones = $camionModel->count();

// Mensajes
$success = isset($_GET['success']) ? $_GET['success'] : null;
$error = isset($_GET['error']) ? $_GET['error'] : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Camiones - AppCamiones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- DataTables CSS -->
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
            display: flex;
            flex-direction: column;
            justify-content: center; /* Centrado vertical */
            padding: 2rem 0;
        }
        .mover-div {
            display: flex;
            align-items: center;
            margin-left: 64px;
            width: 90%;
            justify-content: space-between;
        }

        .badge-disponible {
            background-color: #28a745;
        }
        .badge-mantenimiento {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-ruta {
            background-color: #007bff;
        }
        .badge-inactivo {
            background-color: #6c757d;
        }
        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        /* Contenedor de la tabla centrado */
        .table-wrapper {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        /* Estilo de la tabla */
        #camionesTable {
            width: 100% !important;
            margin: 30px auto; /* Margen superior e inferior aumentado */
            border: 1px solid #dee2e6;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        #camionesTable thead th {
            background-color: #0d6efd;
            border-bottom: 2px solid #dee2e6;
            padding: 12px 15px;
            vertical-align: middle;
            color: white;
        }

       
        #camionesTable tbody td {
            padding: 10px 15px;
            vertical-align: middle;
        }
        /* Estilos para DataTables */
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
        /* Header de la página */
        .page-header {
            margin-bottom: 2rem;
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
                        <a class="nav-link active" href="list.php"><i class="bi bi-truck me-1"></i> Camiones</a>
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

    <!-- Contenido principal - Centrado verticalmente -->
    <div class="main-container">
        <div class="container">
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

            <div class="mover-div">
                <h2>
                    <i class="bi bi-truck me-2"></i>Listado de Camiones
                </h2>
                <a href="add.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Nuevo Camión
                </a>
            </div>

            <div class="table-wrapper">
                <div class="table-responsive">
                    <table id="camionesTable" class="table table-striped table-hover table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Matrícula</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Capacidad (ton)</th>
                                <th>Estado</th>
                                <th>Responsable</th>
                                <th class="action-buttons">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($camiones as $camion): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($camion['id_camion']); ?></td>
                                    <td><?php echo htmlspecialchars($camion['matricula']); ?></td>
                                    <td><?php echo htmlspecialchars($camion['marca']); ?></td>
                                    <td><?php echo htmlspecialchars($camion['modelo']); ?></td>
                                    <td><?php echo htmlspecialchars($camion['capacidad']); ?></td>
                                    <td>
                                        <?php 
                                        $badgeClass = '';
                                        switch($camion['estado']) {
                                            case 'disponible': $badgeClass = 'badge-disponible'; break;
                                            case 'en_mantenimiento': $badgeClass = 'badge-mantenimiento'; break;
                                            case 'en_ruta': $badgeClass = 'badge-ruta'; break;
                                            case 'inactivo': $badgeClass = 'badge-inactivo'; break;
                                            default: $badgeClass = 'bg-secondary';
                                        }
                                        ?>
                                        <span class="badge <?php echo $badgeClass; ?>">
                                            <?php echo htmlspecialchars($camion['estado']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($camion['responsable'] ?? 'Sin asignar'); ?></td>
                                    <td class="action-buttons">
                                        <div class="d-flex gap-2">
                                            <a href="view.php?id=<?php echo $camion['id_camion']; ?>" class="btn btn-sm btn-info" title="Ver">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            
                                            <a href="edit.php?action=editar&id=<?php echo $camion['id_camion']; ?>" class="btn btn-sm btn-warning" title="Editar"> 
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            <a href="list.php?action=eliminar&id=<?php echo $camion['id_camion']; ?>" 
                                               class="btn btn-sm btn-danger" 
                                               title="Eliminar" 
                                               onclick="return confirm('¿Estás seguro de eliminar este camión?')">
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
            $('#camionesTable').DataTable({
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
                        targets: [7],
                        className: 'text-center' // Centrar columna de acciones
                    }
                ],
                drawCallback: function() {
                    // Ajustar el ancho de la tabla después de dibujar
                    $('#camionesTable').css('width', '100%');
                }
            });
        });
    </script>
</body>
</html>