<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/AsignacionRuta.php';
require_once __DIR__ . '/../models/Incidencia.php';
require_once __DIR__ . '/IncidenciaController.php';

class DashboardController {
    private $db;
    private $usuarioModel;
    private $asignacionModel;
    private $incidenciaController;

    public function __construct() {
        $this->db = new Database();
        $conn = $this->db->connect();
        $this->usuarioModel = new Usuario($conn);
        $this->asignacionModel = new AsignacionRuta($conn);
        $this->incidenciaController = new IncidenciaController();
    }

    public function cargarDashboard() {
        if (!isset($_SESSION['user'])) {
            header('Location: login.php');
            exit();
        }

        // Obtener información del usuario
        $userData = $this->usuarioModel->obtenerDatosUsuario($_SESSION['user']['id_usuario']);
        $rol = $userData['nombre_rol'] ?? 'Desconocido';
        $fecha_registro = $this->usuarioModel->formatearFechaRegistro($userData['fecha_registro'] ?? null);
        
        // Verificar roles
        $roles = $this->usuarioModel->verificarRoles($_SESSION['user']['id_rol']);
        $esAdmin = $roles['esAdmin'];
        $esAdminOInformatico = $roles['esAdminOInformatico'];
        $esConductor = $roles['esConductor'];

        // Obtener asignaciones para conductores
        $camionAsignado = null;
        $rutaAsignada = null;
        if ($esConductor) {
            $asignacion = $this->asignacionModel->obtenerAsignacionConductor($_SESSION['user']['id_usuario']);
            
            if ($asignacion) {
                $camionAsignado = [
                    'id_camion' => $asignacion['id_camion'],
                    'matricula' => $asignacion['matricula']
                ];
                $rutaAsignada = [
                    'id_ruta' => $asignacion['id_ruta'],
                    'origen' => $asignacion['origen'],
                    'destino' => $asignacion['destino']
                ];
            }
        }

        // Obtener incidencias para conductores
        $incidenciasConductor = [];
        if ($esConductor) {
            $incidenciasConductor = $this->incidenciaController->getIncidenciasByUserId(
                $_SESSION['user']['id_usuario'], 
                3
            );
        }

        // Obtener estadísticas para admin
        $estadisticas = [];
        if ($esAdmin) {
            $estadisticas = $this->obtenerEstadisticas();
        }

        return [
            'usuario' => [
                'nombre' => $_SESSION['user']['nombre'],
                'email' => $_SESSION['user']['email'],
                'rol' => $rol,
                'fecha_registro' => $fecha_registro
            ],
            'roles' => [
                'esAdmin' => $esAdmin,
                'esAdminOInformatico' => $esAdminOInformatico,
                'esConductor' => $esConductor
            ],
            'asignaciones' => [
                'camionAsignado' => $camionAsignado,
                'rutaAsignada' => $rutaAsignada
            ],
            'incidencias' => $incidenciasConductor,
            'estadisticas' => $estadisticas
        ];
    }

    private function obtenerEstadisticas() {
        // Implementar lógica para obtener estadísticas
        return [
            'total_usuarios' => 0,
            'total_camiones' => 0,
            'total_rutas' => 0,
            'total_incidencias' => 0
        ];
    }
}
?>