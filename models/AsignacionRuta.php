<?php
require_once __DIR__ . '/../config/database.php';

class AsignacionRuta {
    private $conn;
    private $table_name = "asignaciones_rutas";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerAsignacionConductor($id_usuario) {
        $query = "SELECT ar.id_asignacion, ar.id_camion, ar.id_ruta, ar.fecha_salida, ar.fecha_llegada_estimada, 
                         ar.fecha_llegada_real, ar.carga_descripcion, ar.peso_carga, ar.observaciones,
                         c.matricula, c.marca, c.modelo, c.capacidad, c.id_estado_camion, c.fecha_adquisicion, c.ultima_revision,
                         r.origen, r.destino, r.distancia, r.tiempo_estimado, r.id_dificultad, r.id_estado_ruta,
                         dr.nombre as dificultad, dr.descripcion as descripcion_dificultad,
                         er.nombre as estado_ruta, er.descripcion as descripcion_estado_ruta,
                         ea.nombre as estado_asignacion
                  FROM {$this->table_name} ar
                  INNER JOIN camiones c ON ar.id_camion = c.id_camion
                  INNER JOIN rutas r ON ar.id_ruta = r.id_ruta
                  INNER JOIN estados_asignacion ea ON ar.id_estado_asignacion = ea.id_estado_asignacion
                  LEFT JOIN dificultades_ruta dr ON r.id_dificultad = dr.id_dificultad
                  LEFT JOIN estados_ruta er ON r.id_estado_ruta = er.id_estado_ruta
                  WHERE ar.id_usuario_conductor = :id_usuario AND ea.nombre = 'en proceso'
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerAsignacionesActivas() {
        $query = "SELECT ar.*, c.matricula, r.origen, r.destino, u.nombre as conductor
                  FROM {$this->table_name} ar
                  INNER JOIN camiones c ON ar.id_camion = c.id_camion
                  INNER JOIN rutas r ON ar.id_ruta = r.id_ruta
                  INNER JOIN usuarios u ON ar.id_usuario_conductor = u.id_usuario
                  INNER JOIN estados_asignacion ea ON ar.id_estado_asignacion = ea.id_estado_asignacion
                  WHERE ea.nombre = 'en proceso'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}