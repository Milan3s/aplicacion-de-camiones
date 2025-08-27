<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Camiones.php';
require_once __DIR__ . '/../models/AsignacionRuta.php';

class CamionesController {
    private $db;
    private $camion;
    private $asignacion;

    public function __construct() {
        $this->db = new Database();
        $this->camion = new Camiones($this->db->connect());
        $this->asignacion = new AsignacionRuta($this->db->connect());
    }

    public function getCamionModel() {
        return $this->camion;
    }

    // Métodos para mostrar formularios
    public function mostrarFormulario() {
        try {
            return [
                'estados' => $this->camion->getEstadosCamion(),
                'conductores' => $this->camion->getConductores()
            ];
        } catch (PDOException $e) {
            error_log("Error al cargar formulario: " . $e->getMessage());
            return ['error' => 'Error al cargar el formulario'];
        }
    }

    public function mostrarFormularioEdicion($id) {
        try {
            $camion = $this->camion->getCamionById($id);
            if (!$camion) {
                error_log("Camión no encontrado con ID: ".$id);
                return ['error' => 'Camión no encontrado'];
            }
    
            return [
                'camion' => $camion,
                'estados' => $this->camion->getEstadosCamion(),
                'conductores' => $this->camion->getConductores()
            ];
        } catch (PDOException $e) {
            error_log("Error en mostrarFormularioEdicion: ".$e->getMessage());
            return ['error' => 'Error al cargar el formulario de edición'];
        }
    }

    // Métodos CRUD
    public function agregarCamion($data) {
        try {
            $errors = $this->validarDatos($data);
            if (!empty($errors)) {
                return ['error' => implode(', ', $errors)];
            }

            $data['fecha_adquisicion'] = date('Y-m-d', strtotime($data['fecha_adquisicion']));
            $data['ultima_revision'] = !empty($data['ultima_revision']) ? 
                date('Y-m-d', strtotime($data['ultima_revision'])) : null;

            if ($this->camion->insert($data)) {
                return ['success' => 'Camión agregado correctamente'];
            }
            return ['error' => 'Error al guardar el camión'];
        } catch (PDOException $e) {
            error_log("Error al agregar camión: " . $e->getMessage());
            return ['error' => 'Error al agregar el camión'];
        }
    }

    public function editarCamion($id, $data) {
        try {
            $errors = $this->validarDatos($data, $id);
            if (!empty($errors)) {
                return ['error' => implode(', ', $errors)];
            }

            $data['fecha_adquisicion'] = date('Y-m-d', strtotime($data['fecha_adquisicion']));
            $data['ultima_revision'] = !empty($data['ultima_revision']) ? 
                date('Y-m-d', strtotime($data['ultima_revision'])) : null;

            if ($this->camion->update($id, $data)) {
                return ['success' => 'Camión actualizado correctamente'];
            }
            return ['error' => 'Error al actualizar el camión'];
        } catch (PDOException $e) {
            error_log("Error al editar camión: " . $e->getMessage());
            return ['error' => 'Error al editar el camión'];
        }
    }

    public function eliminarCamion($id) {
        try {
            if ($this->camion->delete($id)) {
                return ['success' => 'Camión eliminado correctamente'];
            }
            return ['error' => 'Error al eliminar el camión'];
        } catch (PDOException $e) {
            error_log("Error al eliminar camión: " . $e->getMessage());
            return ['error' => 'Error al eliminar el camión'];
        }
    }

    // Método para mostrar el camión del conductor
    public function miCamion() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['id_rol'] != 3) {
            header('Location: ../../views/auth/login.php');
            exit();
        }

        $id_usuario = $_SESSION['user']['id_usuario'];
        // Buscar el camión a través de la asignación de ruta activa
        $asignacion = $this->asignacion->obtenerAsignacionConductor($id_usuario);
        $camion = null;
        $estado_camion = null;

        if ($asignacion) {
            $camion = [
                'matricula' => $asignacion['matricula'],
                'marca' => $asignacion['marca'],
                'modelo' => $asignacion['modelo'],
                'capacidad' => $asignacion['capacidad'],
                'id_estado_camion' => $asignacion['id_estado_camion'],
                'fecha_adquisicion' => $asignacion['fecha_adquisicion'],
                'ultima_revision' => $asignacion['ultima_revision']
            ];

            // Obtener el estado del camión
            $query = "SELECT * FROM estados_camion WHERE id_estado_camion = :id_estado_camion";
            $stmt = $this->db->connect()->prepare($query);
            $stmt->bindParam(':id_estado_camion', $camion['id_estado_camion']);
            $stmt->execute();
            $estado_camion = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return [
            'camion' => $camion,
            'estado_camion' => $estado_camion
        ];
    }

    // Métodos auxiliares
    private function validarDatos($data, $id = null) {
        $errors = [];
        
        if (empty($data['matricula'])) $errors[] = 'Matrícula es requerida';
        if (empty($data['marca'])) $errors[] = 'Marca es requerida';
        if (empty($data['modelo'])) $errors[] = 'Modelo es requerido';
        if (!is_numeric($data['capacidad']) || $data['capacidad'] <= 0) $errors[] = 'Capacidad inválida';
        if (empty($data['id_estado_camion'])) $errors[] = 'Estado es requerido';
        if (empty($data['fecha_adquisicion'])) $errors[] = 'Fecha adquisición es requerida';
        
        $currentCamion = $id ? $this->camion->getCamionById($id) : null;
        if ($currentCamion && $currentCamion['matricula'] !== $data['matricula'] && 
            $this->camion->matriculaExists($data['matricula'], $id)) {
            $errors[] = 'La matrícula ya existe';
        }
        
        return $errors;
    }

    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
            if ($_GET['action'] === 'agregar') {
                $result = $this->agregarCamion($_POST);
                return array_merge($result, $this->mostrarFormulario());
            } elseif ($_GET['action'] === 'editar' && isset($_GET['id'])) {
                $result = $this->editarCamion($_GET['id'], $_POST);
                return array_merge($result, $this->mostrarFormularioEdicion($_GET['id']));
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
            if ($_GET['action'] === 'editar' && isset($_GET['id'])) {
                return $this->mostrarFormularioEdicion($_GET['id']);
            } elseif ($_GET['action'] === 'eliminar' && isset($_GET['id'])) {
                return $this->eliminarCamion($_GET['id']);
            } elseif ($_GET['action'] === 'miCamion') {
                return $this->miCamion();
            }
        }
        
        return $this->mostrarFormulario();
    }
}