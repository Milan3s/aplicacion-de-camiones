<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Incidencia.php';

// Definir la URL base de la aplicación
define('BASE_URL', '/AppCamiones');

class IncidenciaController {
    public $db;
    public $incidencia;

    public function __construct() {
        $this->db = new Database();
        $this->incidencia = new Incidencia($this->db->connect());
    }

    // Crear nueva incidencia
    public function crearIncidencia($data) {
        try {
            // Validar datos requeridos
            $required = ['titulo', 'descripcion', 'id_tipo_incidencia', 'id_prioridad', 'id_estado_incidencia'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return ['error' => "El campo $field es obligatorio"];
                }
            }

            // Añadir usuario que reporta
            $data['id_usuario_reporta'] = $_SESSION['user']['id_usuario'];

            if ($this->incidencia->create($data)) {
                return ['success' => 'Incidencia creada exitosamente'];
            }
            return ['error' => 'No se pudo crear la incidencia'];
        } catch (PDOException $e) {
            error_log("Error al crear incidencia: " . $e->getMessage());
            return ['error' => 'Error en la base de datos'];
        }
    }

    // Obtener incidencia por ID
    public function obtenerIncidencia($id) {
        try {
            $incidencia = $this->incidencia->readById($id);
            
            if (!$incidencia) {
                return ['error' => 'Incidencia no encontrada'];
            }
            
            return ['incidencia' => $incidencia];
        } catch (PDOException $e) {
            error_log("Error al obtener incidencia: " . $e->getMessage());
            return ['error' => 'Error al obtener la incidencia'];
        }
    }

    // Listar incidencias
    public function listarIncidencias() {
        try {
            $incidencias = $this->incidencia->readAll();
            $total = $this->incidencia->count();
            
            return [
                'data' => $incidencias,
                'total' => $total
            ];
        } catch (PDOException $e) {
            error_log("Error al listar incidencias: " . $e->getMessage());
            return [
                'data' => [],
                'total' => 0
            ];
        }
    }

    // Obtener incidencias por usuario
    public function getIncidenciasByUserId($id_usuario, $limit = null) {
        try {
            return $this->incidencia->readByUserId($id_usuario, $limit);
        } catch (PDOException $e) {
            error_log("Error al obtener incidencias por usuario: " . $e->getMessage());
            return [];
        }
    }

    // Manejar la solicitud de edición
    public function handleEditRequest() {
        $data = [];

        // Verificar si se está enviando el formulario para actualizar
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'editar') {
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $postData = [
                'titulo' => $_POST['titulo'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'id_tipo_incidencia' => $_POST['id_tipo_incidencia'] ?? '',
                'id_prioridad' => $_POST['id_prioridad'] ?? '',
                'id_estado_incidencia' => $_POST['id_estado_incidencia'] ?? '',
                'id_camion' => !empty($_POST['id_camion']) ? (int)$_POST['id_camion'] : null,
                'id_ruta' => !empty($_POST['id_ruta']) ? (int)$_POST['id_ruta'] : null,
                'id_usuario_asignado' => !empty($_POST['id_usuario_asignado']) ? (int)$_POST['id_usuario_asignado'] : null,
                'fecha_resolucion' => !empty($_POST['fecha_resolucion']) ? $_POST['fecha_resolucion'] : null,
            ];

            // Actualizar la incidencia
            $result = $this->actualizarIncidencia($id, $postData);
            if (isset($result['success'])) {
                // En lugar de redirigir, almacenamos el mensaje de éxito y recargamos los datos
                $data['success'] = $result['success'];
                // Recargar la incidencia actualizada
                $incidenciaResult = $this->obtenerIncidencia($id);
                if (isset($incidenciaResult['error'])) {
                    $data['error'] = $incidenciaResult['error'];
                    $data['incidencia'] = [];
                } else {
                    $data['incidencia'] = $incidenciaResult['incidencia'];
                }
            } else {
                $data['error'] = $result['error'];
                // Si hay un error, mantenemos los datos actuales de la incidencia
                $incidenciaResult = $this->obtenerIncidencia($id);
                if (isset($incidenciaResult['error'])) {
                    $data['error'] = $incidenciaResult['error'] . ' - ' . $data['error'];
                    $data['incidencia'] = [];
                } else {
                    $data['incidencia'] = $incidenciaResult['incidencia'];
                }
            }
        } else {
            // Obtener la incidencia para mostrar en el formulario (cuando no es POST)
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $incidenciaResult = $this->obtenerIncidencia($id);
            if (isset($incidenciaResult['error'])) {
                $data['error'] = $incidenciaResult['error'];
                $data['incidencia'] = [];
            } else {
                $data['incidencia'] = $incidenciaResult['incidencia'];
            }
        }

        // Obtener datos para los selects
        $data['tiposIncidencia'] = $this->obtenerTiposIncidencia();
        $data['prioridades'] = $this->obtenerPrioridades();
        $data['estados'] = $this->obtenerEstadosIncidencia();
        $data['camiones'] = $this->obtenerCamiones();
        $data['rutas'] = $this->obtenerRutas();
        $data['usuarios'] = $this->obtenerUsuarios();

        return $data;
    }

    // Manejar la solicitud de visualización
    public function handleViewRequest() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $result = $this->obtenerIncidencia($id);
        
        if (isset($result['error'])) {
            return ['error' => $result['error']];
        }
        
        return $result['incidencia'];
    }

    // Actualizar incidencia
    public function actualizarIncidencia($id, $data) {
        try {
            // Validar datos requeridos
            $required = ['titulo', 'descripcion', 'id_tipo_incidencia', 'id_prioridad', 'id_estado_incidencia'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return ['error' => "El campo $field es obligatorio"];
                }
            }

            if ($this->incidencia->update($id, $data)) {
                return ['success' => 'Incidencia actualizada exitosamente'];
            }
            return ['error' => 'No se pudo actualizar la incidencia'];
        } catch (PDOException $e) {
            error_log("Error al actualizar incidencia: " . $e->getMessage());
            return ['error' => 'Error al actualizar la incidencia'];
        }
    }

    // Eliminar incidencia
    public function eliminarIncidencia($id) {
        try {
            if ($this->incidencia->delete($id)) {
                return ['success' => 'Incidencia eliminada exitosamente'];
            }
            return ['error' => 'No se pudo eliminar la incidencia'];
        } catch (PDOException $e) {
            error_log("Error al eliminar incidencia: " . $e->getMessage());
            return ['error' => 'Error al eliminar la incidencia'];
        }
    }

    // Métodos para obtener datos de selects
    public function obtenerTiposIncidencia() {
        return $this->ejecutarConsulta("SELECT id_tipo_incidencia, nombre FROM tipos_incidencia ORDER BY nombre");
    }

    public function obtenerPrioridades() {
        return $this->ejecutarConsulta("SELECT id_prioridad, nombre FROM niveles_prioridad ORDER BY id_prioridad");
    }

    public function obtenerEstadosIncidencia() {
        return $this->ejecutarConsulta("SELECT id_estado_incidencia, nombre FROM estados_incidencia ORDER BY nombre");
    }

    public function obtenerCamiones() {
        return $this->ejecutarConsulta("SELECT id_camion, matricula FROM camiones ORDER BY matricula");
    }

    public function obtenerRutas() {
        return $this->ejecutarConsulta("SELECT id_ruta, origen, destino FROM rutas ORDER BY origen");
    }

    public function obtenerUsuarios() {
        return $this->ejecutarConsulta("SELECT id_usuario, nombre FROM usuarios ORDER BY nombre");
    }

    // Método auxiliar para ejecutar consultas
    private function ejecutarConsulta($sql) {
        try {
            $stmt = $this->db->connect()->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en consulta: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerEstadisticasTiempoResolucion() {
        $query = "SELECT 
                    AVG(TIMESTAMPDIFF(HOUR, fecha_reporte, fecha_resolucion)) as avg_horas,
                    MIN(TIMESTAMPDIFF(HOUR, fecha_reporte, fecha_resolucion)) as min_horas,
                    MAX(TIMESTAMPDIFF(HOUR, fecha_reporte, fecha_resolucion)) as max_horas
                  FROM incidencias 
                  WHERE fecha_resolucion IS NOT NULL";
        $stmt = $this->db->connect()->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}