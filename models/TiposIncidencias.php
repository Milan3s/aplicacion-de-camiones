<?php
class TiposIncidencias {
    private $conn;
    private $table_name = "tipos_incidencia";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todos los tipos
    public function obtenerTodos() {
        $query = "SELECT id_tipo_incidencia, nombre, descripcion 
                  FROM " . $this->table_name . " 
                  ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un tipo por ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE id_tipo_incidencia = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear nuevo tipo
    public function crear($nombre, $descripcion) {
        $query = "INSERT INTO " . $this->table_name . " 
                 (nombre, descripcion) VALUES (:nombre, :descripcion)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        return $stmt->execute();
    }

    // Actualizar tipo
    public function actualizar($id, $nombre, $descripcion) {
        $query = "UPDATE " . $this->table_name . " 
                  SET nombre = :nombre, descripcion = :descripcion
                  WHERE id_tipo_incidencia = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Eliminar tipo
    public function eliminar($id) {
        $query = "DELETE FROM " . $this->table_name . " 
                  WHERE id_tipo_incidencia = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}