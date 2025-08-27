<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once '../../config/database.php';
require_once '../../models/Usuarios.php';
$db = new Database();
$usuarioModel = new Usuarios($db->connect());

$stmt = $usuarioModel->read();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Usuarios - AppCamiones</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <style>
        body {
            padding-top: 56px;
            background-color: #f8f9fa;
        }
        .main-content {
            margin-top: 342px; /* Posición original */
        }
        /* Estilos para DataTables */
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 5px 10px;
            margin-left: 10px;
        }
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 5px;
        }
        /* Estilo original de la tabla con mejoras */
        #usuariosTable {
            border: 1px solid #dee2e6;
            margin-top: 20px;
        }
        #usuariosTable thead th {
            background-color: #0d6efd; /* Azul original */
            color: white;
            text-align: center;
            vertical-align: middle;
            border-bottom: 2px solid #dee2e6;
        }
        #usuariosTable tbody td {
            padding: 8px 12px;
            vertical-align: middle;
            border-top: 1px solid #dee2e6;
            text-align: center;
        }
        #usuariosTable tbody tr:nth-child(even) {
            background-color: rgba(0,0,0,0.02);
        }
        #usuariosTable tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.1);
        }
        /* Botones mejorados manteniendo esencia original */
        .btn-outline-primary {
            border-color: #0d6efd;
            color: #0d6efd;
        }
        .btn-outline-primary:hover {
            background-color: #0d6efd;
            color: white;
        }
        .btn-outline-danger {
            border-color: #dc3545;
            color: #dc3545;
        }
        .btn-outline-danger:hover {
            background-color: #dc3545;
            color: white;
        }
        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            margin: 0 2px;
            transition: all 0.2s;
        }
        select.form-select.form-select-sm {
            width: 120px !important;
        }
        /* Ajustes de posición */
        .table-container {
            margin-bottom: 20px;
        }
        .table-title {
            color: #2c3e50;
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

    <!-- Contenido Principal - Versión fiel al original con mejoras -->
    <div class="container main-content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="table-title mb-0"><i class="bi bi-people me-2"></i>Listado de Usuarios</h2>
            <a href="add.php" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i> Agregar Usuario
            </a>
        </div>
        
        <!-- Tabla con DataTables - Estilo original mejorado -->
        <div class="table-responsive table-container">
            <table id="usuariosTable" class="table table-striped table-hover table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($usuario['id_usuario']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['nombre_rol']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['fecha_registro']); ?></td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <a href="edit.php?id=<?php echo $usuario['id_usuario']; ?>" class="btn btn-outline-primary btn-action">
                                        <i class="bi bi-pencil-square"></i> Editar
                                    </a>
                                    <form action="delete.php" method="POST" class="d-inline">
                                        <input type="hidden" name="id_usuario" value="<?php echo $usuario['id_usuario']; ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-action" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">
                                            <i class="bi bi-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <a href="../auth/dashboard.php" class="btn btn-outline-secondary mt-3">
            <i class="bi bi-arrow-left me-1"></i> Volver al Dashboard
        </a>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (necesario para DataTables) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <!-- DataTables Bootstrap 5 JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#usuariosTable').DataTable({
                language: {
                    "decimal": "",
                    "emptyTable": "No hay datos disponibles en la tabla",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                    "infoFiltered": "(filtrado de _MAX_ registros totales)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Mostrar _MENU_ registros",
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
                dom: '<"top"lf>rt<"bottom"ip><"clear">',
                pageLength: 10,
                order: [[0, 'asc']],
                columnDefs: [
                    { orderable: false, targets: 5 } // Deshabilitar ordenación para la columna de acciones
                ]
            });
        });
    </script>
</body>
</html>