<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Rutas.php';
require_once __DIR__ . '/../models/AsignacionRuta.php';

class RutasController {
    private $db;
    private $rutas;
    private $asignacion;

    public function __construct() {
        $this->db = new Database();
        $this->rutas = new Rutas($this->db->connect());
        $this->asignacion = new AsignacionRuta($this->db->connect());
    }

    public function getRutasModel() {
        return $this->rutas;
    }

    // Método para listar todas las rutas
    public function listarRutas() {
        return $this->rutas->read();
    }

    // Método para contar rutas
    public function contarRutas() {
        return $this->rutas->count();
    }

    // Método para crear una nueva ruta
    public function crearRuta($data) {
        if (empty($data['origen']) || empty($data['destino']) || empty($data['distancia'])) {
            return ['error' => 'Todos los campos obligatorios deben estar completos'];
        }
        return $this->rutas->create($data);
    }

    // Método para obtener una ruta por ID
    public function obtenerRuta($id) {
        return $this->rutas->readOne($id);
    }

    // Método para obtener detalles de ruta (usando readOne)
    public function obtenerDetallesRuta($id) {
        return $this->rutas->readOne($id);
    }

    // Método para actualizar una ruta
    public function actualizarRuta($id, $data) {
        if (empty($data['origen']) || empty($data['destino']) || empty($data['distancia'])) {
            return ['error' => 'Todos los campos obligatorios deben estar completos'];
        }
        return $this->rutas->update($id, $data);
    }

    // Método para eliminar una ruta
    public function eliminarRuta($id) {
        return $this->rutas->delete($id);
    }

    // Método para obtener dificultades
    public function obtenerDificultades() {
        return $this->rutas->getDificultades();
    }

    // Método para obtener estados de ruta
    public function obtenerEstadosRuta() {
        return $this->rutas->getEstadosRuta();
    }

    // Método para mostrar la ruta del conductor
    public function miRuta() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['id_rol'] != 3) {
            header('Location: ../../views/auth/login.php');
            exit();
        }

        $id_usuario = $_SESSION['user']['id_usuario'];
        $asignacion = $this->asignacion->obtenerAsignacionConductor($id_usuario);

        // Obtener el estado del camión si hay una asignación
        $estado_camion = null;
        if ($asignacion) {
            $query = "SELECT * FROM estados_camion WHERE id_estado_camion = :id_estado_camion";
            $stmt = $this->db->connect()->prepare($query);
            $stmt->bindParam(':id_estado_camion', $asignacion['id_estado_camion']);
            $stmt->execute();
            $estado_camion = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return [
            'asignacion' => $asignacion,
            'estado_camion' => $estado_camion
        ];
    }

    // Método para manejar la solicitud de visualización
    public function handleViewRequest() {
        if (!isset($_GET['id'])) {
            header('Location: list.php?error=' . urlencode('ID de ruta no especificado'));
            exit();
        }
        
        $ruta = $this->obtenerDetallesRuta($_GET['id']);
        
        if (!$ruta) {
            header('Location: list.php?error=' . urlencode('La ruta no existe'));
            exit();
        }
        
        return $ruta;
    }

    // Método para manejar la solicitud de edición
    public function handleEditRequest() {
        $data = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'editar') {
            $id = $_GET['id'] ?? null;
            if ($id) {
                $result = $this->actualizarRuta($id, $_POST);
                
                if (isset($result['success'])) {
                    header('Location: list.php?success=' . urlencode($result['success']));
                    exit();
                } else {
                    $data['error'] = $result['error'];
                }
            }
        }
        
        $data['dificultades'] = $this->obtenerDificultades()->fetchAll(PDO::FETCH_ASSOC);
        $data['estados'] = $this->obtenerEstadosRuta()->fetchAll(PDO::FETCH_ASSOC);
        
        if (isset($_GET['id'])) {
            $data['ruta'] = $this->obtenerRuta($_GET['id']);
            if (!$data['ruta']) {
                header('Location: list.php?error=' . urlencode('La ruta no existe'));
                exit();
            }
        } else {
            header('Location: list.php?error=' . urlencode('ID de ruta no especificado'));
            exit();
        }
        
        return $data;
    }

    // Método para manejar la solicitud de eliminación
    public function handleDeleteRequest() {
        if (isset($_GET['id'])) {
            $result = $this->eliminarRuta($_GET['id']);
            
            if (isset($result['success'])) {
                header('Location: list.php?success=' . urlencode($result['success']));
                exit();
            } else {
                header('Location: list.php?error=' . urlencode($result['error']));
                exit();
            }
        } else {
            header('Location: list.php?error=' . urlencode('ID de ruta no especificado'));
            exit();
        }
    }

    // Método para manejar todas las solicitudes
    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
            if ($_GET['action'] === 'miRuta') {
                return $this->miRuta();
            }
        }
        return [];
    }
}