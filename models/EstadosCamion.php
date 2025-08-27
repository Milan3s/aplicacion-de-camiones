<?php
class EstadosCamion {
    private $conn;
    private $table_name = "estados_camion";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todos los estados
    public function obtenerTodos() {
        $query = "SELECT id_estado_camion, nombre, descripcion 
                  FROM " . $this->table_name . " 
                  ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un estado por ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE id_estado_camion = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear nuevo estado
    public function crear($nombre, $descripcion) {
        $query = "INSERT INTO " . $this->table_name . " 
                 (nombre, descripcion) VALUES (:nombre, :descripcion)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        return $stmt->execute();
    }

    // Actualizar estado
    public function actualizar($id, $nombre, $descripcion) {
        $query = "UPDATE " . $this->table_name . " 
                  SET nombre = :nombre, descripcion = :descripcion
                  WHERE id_estado_camion = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Eliminar estado
    public function eliminar($id) {
        $query = "DELETE FROM " . $this->table_name . " 
                  WHERE id_estado_camion = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}