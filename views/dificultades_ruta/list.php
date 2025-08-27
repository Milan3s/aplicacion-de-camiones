<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once __DIR__ . '/../../controllers/DificultadesRutaController.php';
$controller = new DificultadesRutaController();

// Manejar acción de eliminación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'eliminar') {
    $controller->handleRequest();
}

$dificultades = $controller->listarDificultades();
$error = is_array($dificultades) && isset($dificultades['error']) ? $dificultades['error'] : null;

// Mostrar mensajes de éxito/error
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dificultades de Ruta - AppCamiones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <style>
        body {
            padding-top: 56px;
            background-color: #f8f9fa;
        }
        #dificultadesTable {
            width: 100% !important;
            margin: 20px auto;
            border: 1px solid #dee2e6 !important;
        }
        #dificultadesTable thead th {
            background-color: #0d6efd;
            color: white;
            border: 1px solid #dee2e6;
            text-align: center;
            vertical-align: middle;
        }
        #dificultadesTable tbody td {
            padding: 8px 12px;
            border: 1px solid #dee2e6;
            text-align: center;
            vertical-align: middle;
        }
        #dificultadesTable tbody tr:nth-child(even) {
            background-color: rgba(0,0,0,0.02);
        }
        #dificultadesTable tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.1);
        }
        .btn-action {
            padding: 0.25rem 0.5rem;
            margin: 0 2px;
        }
        .table-title {
            margin: 20px 0;
        }
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 5px 10px;
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
                        <a class="nav-link" href="../auth/dashboard.php">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#">
                            <i class="bi bi-signpost-2 me-1"></i> Dificultades Ruta
                        </a>
                    </li>
                </ul>
                <div class="d-flex">
                    <span class="navbar-text text-white me-3">
                        <i class="bi bi-person-circle me-1"></i>
                        <?= htmlspecialchars($_SESSION['user']['nombre']) ?>
                    </span>
                    <a href="../../controllers/AuthController.php?logout=true" class="btn btn-outline-light">
                        <i class="bi bi-box-arrow-right me-1"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="container">
        <h2 class="table-title"><i class="bi bi-signpost-2 me-2"></i>Dificultades de Ruta</h2>

        <?php if (isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="mb-3">
            <a href="add.php" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Nueva Dificultad
            </a>
        </div>

        <table id="dificultadesTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (is_array($dificultades) && !isset($dificultades['error'])): ?>
                    <?php foreach ($dificultades as $dificultad): ?>
                        <tr>
                            <td><?= htmlspecialchars($dificultad['id_dificultad']) ?></td>
                            <td><?= htmlspecialchars($dificultad['nombre']) ?></td>
                            <td><?= htmlspecialchars($dificultad['descripcion'] ?? 'N/A') ?></td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <!-- Botón de Visualización -->
                                    <a href="views.php?id=<?= $dificultad['id_dificultad'] ?>" class="btn btn-info btn-action" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    <!-- Botón de Edición -->
                                    <a href="edit.php?id=<?= $dificultad['id_dificultad'] ?>" class="btn btn-warning btn-action" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    
                                    <!-- Botón de Eliminación -->
                                    <form method="post" action="list.php?action=eliminar" class="d-inline">
                                        <input type="hidden" name="action" value="eliminar">
                                        <input type="hidden" name="id" value="<?= $dificultad['id_dificultad'] ?>">
                                        <button type="submit" class="btn btn-danger btn-action" title="Eliminar" 
                                                onclick="return confirm('¿Estás seguro de eliminar esta dificultad de ruta?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">No hay dificultades de ruta registradas</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#dificultadesTable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json',
                    search: "Buscar:",
                    lengthMenu: "Mostrar _MENU_ registros por página",
                    zeroRecords: "No se encontraron registros",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    infoEmpty: "Mostrando 0 a 0 de 0 registros",
                    infoFiltered: "(filtrado de _MAX_ registros totales)",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior"
                    }
                },
                responsive: true,
                dom: '<"top"<"d-flex justify-content-between align-items-center"lf>>rt<"bottom"ip>',
                columnDefs: [
                    { 
                        orderable: false, 
                        targets: [3],
                        className: 'text-center'
                    },
                    {
                        className: 'text-center',
                        targets: [0, 1, 2]
                    }
                ],
                initComplete: function() {
                    $('.dataTables_filter input').addClass('form-control form-control-sm');
                    $('.dataTables_length select').addClass('form-select form-select-sm');
                }
            });
            
            // Cerrar alertas automáticamente
            setTimeout(() => {
                $('.alert').alert('close');
            }, 5000);
        });
    </script>
</body>
</html>