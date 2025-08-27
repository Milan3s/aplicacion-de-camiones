<?php
require_once __DIR__ . '/../config/database.php';

class Incidencia {
    private $conn;
    private $table_name = "incidencias";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear nueva incidencia
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " (
            titulo, descripcion, id_tipo_incidencia, id_prioridad,
            id_estado_incidencia, id_camion, id_ruta,
            id_usuario_reporta, id_usuario_asignado, fecha_reporte
        ) VALUES (
            :titulo, :descripcion, :id_tipo_incidencia, :id_prioridad,
            :id_estado_incidencia, :id_camion, :id_ruta,
            :id_usuario_reporta, :id_usuario_asignado, NOW()
        )";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $data = array_map(function($item) {
            return htmlspecialchars(strip_tags($item));
        }, $data);

        // Vincular parámetros
        $stmt->bindParam(':titulo', $data['titulo']);
        $stmt->bindParam(':descripcion', $data['descripcion']);
        $stmt->bindParam(':id_tipo_incidencia', $data['id_tipo_incidencia'], PDO::PARAM_INT);
        $stmt->bindParam(':id_prioridad', $data['id_prioridad'], PDO::PARAM_INT);
        $stmt->bindParam(':id_estado_incidencia', $data['id_estado_incidencia'], PDO::PARAM_INT);
        $stmt->bindParam(':id_camion', $data['id_camion'], PDO::PARAM_INT);
        $stmt->bindParam(':id_ruta', $data['id_ruta'], PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario_reporta', $data['id_usuario_reporta'], PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario_asignado', $data['id_usuario_asignado'], PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Leer incidencia por ID
    public function readById($id) {
        $query = "SELECT i.*, 
                 ti.nombre as tipo_incidencia,
                 np.nombre as nombre_prioridad,
                 ei.nombre as nombre_estado,
                 ur.nombre as nombre_usuario_reporta,
                 ua.nombre as nombre_usuario_asignado,
                 c.matricula as matricula_camion
                 FROM " . $this->table_name . " i
                 LEFT JOIN tipos_incidencia ti ON i.id_tipo_incidencia = ti.id_tipo_incidencia
                 LEFT JOIN niveles_prioridad np ON i.id_prioridad = np.id_prioridad
                 LEFT JOIN estados_incidencia ei ON i.id_estado_incidencia = ei.id_estado_incidencia
                 LEFT JOIN usuarios ur ON i.id_usuario_reporta = ur.id_usuario
                 LEFT JOIN usuarios ua ON i.id_usuario_asignado = ua.id_usuario
                 LEFT JOIN camiones c ON i.id_camion = c.id_camion
                 WHERE i.id_incidencia = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Leer todas las incidencias
    public function readAll() {
        $query = "SELECT i.*, 
                 ti.nombre as tipo_incidencia,
                 np.nombre as nombre_prioridad,
                 ei.nombre as nombre_estado,
                 ur.nombre as nombre_usuario_reporta,
                 ua.nombre as nombre_usuario_asignado,
                 c.matricula as matricula_camion
                 FROM " . $this->table_name . " i
                 LEFT JOIN tipos_incidencia ti ON i.id_tipo_incidencia = ti.id_tipo_incidencia
                 LEFT JOIN niveles_prioridad np ON i.id_prioridad = np.id_prioridad
                 LEFT JOIN estados_incidencia ei ON i.id_estado_incidencia = ei.id_estado_incidencia
                 LEFT JOIN usuarios ur ON i.id_usuario_reporta = ur.id_usuario
                 LEFT JOIN usuarios ua ON i.id_usuario_asignado = ua.id_usuario
                 LEFT JOIN camiones c ON i.id_camion = c.id_camion
                 ORDER BY i.fecha_reporte DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Leer incidencias por usuario
    public function readByUserId($id_usuario, $limit = null) {
        $query = "SELECT i.*, 
                 ti.nombre as tipo_incidencia,
                 np.nombre as nombre_prioridad,
                 ei.nombre as nombre_estado,
                 ur.nombre as nombre_usuario_reporta,
                 ua.nombre as nombre_usuario_asignado,
                 c.matricula as matricula_camion
                 FROM " . $this->table_name . " i
                 LEFT JOIN tipos_incidencia ti ON i.id_tipo_incidencia = ti.id_tipo_incidencia
                 LEFT JOIN niveles_prioridad np ON i.id_prioridad = np.id_prioridad
                 LEFT JOIN estados_incidencia ei ON i.id_estado_incidencia = ei.id_estado_incidencia
                 LEFT JOIN usuarios ur ON i.id_usuario_reporta = ur.id_usuario
                 LEFT JOIN usuarios ua ON i.id_usuario_asignado = ua.id_usuario
                 LEFT JOIN camiones c ON i.id_camion = c.id_camion
                 WHERE i.id_usuario_reporta = ? 
                 ORDER BY i.fecha_reporte DESC";
        
        if ($limit) {
            $query .= " LIMIT ?";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id_usuario, PDO::PARAM_INT);
        
        if ($limit) {
            $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Actualizar incidencia
    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " SET 
            titulo = :titulo,
            descripcion = :descripcion,
            id_tipo_incidencia = :id_tipo_incidencia,
            id_prioridad = :id_prioridad,
            id_estado_incidencia = :id_estado_incidencia,
            id_camion = :id_camion,
            id_ruta = :id_ruta,
            id_usuario_asignado = :id_usuario_asignado,
            fecha_resolucion = :fecha_resolucion
            WHERE id_incidencia = :id";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $data = array_map(function($item) {
            return is_string($item) ? htmlspecialchars(strip_tags($item)) : $item;
        }, $data);

        // Vincular parámetros
        $stmt->bindParam(':titulo', $data['titulo']);
        $stmt->bindParam(':descripcion', $data['descripcion']);
        $stmt->bindParam(':id_tipo_incidencia', $data['id_tipo_incidencia'], PDO::PARAM_INT);
        $stmt->bindParam(':id_prioridad', $data['id_prioridad'], PDO::PARAM_INT);
        $stmt->bindParam(':id_estado_incidencia', $data['id_estado_incidencia'], PDO::PARAM_INT);
        $stmt->bindParam(':id_camion', $data['id_camion'], PDO::PARAM_INT);
        $stmt->bindParam(':id_ruta', $data['id_ruta'], PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario_asignado', $data['id_usuario_asignado'], PDO::PARAM_INT);
        $stmt->bindParam(':fecha_resolucion', $data['fecha_resolucion']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Eliminar incidencia
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_incidencia = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Contar total de incidencias
    public function count() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
}