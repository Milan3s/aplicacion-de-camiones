<?php
class Roles {
    private $conn;
    private $table_name = "roles";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Métodos CRUD
    public function read() {
        $query = "SELECT 
                    id_rol, 
                    nombre_rol, 
                    descripcion
                  FROM " . $this->table_name . "
                  ORDER BY id_rol ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function insert($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (nombre_rol, descripcion)
                  VALUES (:nombre_rol, :descripcion)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nombre_rol', $data['nombre_rol']);
        $stmt->bindParam(':descripcion', $data['descripcion']);
        
        return $stmt->execute();
    }

    public function getRolById($id) {
        $query = "SELECT 
                    id_rol, 
                    nombre_rol, 
                    descripcion
                  FROM " . $this->table_name . "
                  WHERE id_rol = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            error_log("No se encontró rol con ID: " . $id);
        }
        
        return $result;
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET 
                    nombre_rol = :nombre_rol,
                    descripcion = :descripcion
                  WHERE id_rol = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nombre_rol', $data['nombre_rol']);
        $stmt->bindParam(':descripcion', $data['descripcion']);
        
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_rol = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Métodos auxiliares
    public function count() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function nombreRolExists($nombre_rol, $excludeId = null) {
        $query = "SELECT id_rol FROM " . $this->table_name . " WHERE nombre_rol = :nombre_rol";
        if ($excludeId) {
            $query .= " AND id_rol != :exclude_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre_rol', $nombre_rol);
        if ($excludeId) {
            $stmt->bindParam(':exclude_id', $excludeId);
        }
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}