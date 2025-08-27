<?php
class Camiones {
    private $conn;
    private $table_name = "camiones";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Métodos CRUD
    public function read() {
        $query = "SELECT 
                    c.id_camion, 
                    c.matricula, 
                    c.marca, 
                    c.modelo, 
                    c.capacidad, 
                    ec.nombre as estado,
                    c.fecha_adquisicion, 
                    c.ultima_revision,
                    u.nombre as responsable
                  FROM " . $this->table_name . " c
                  LEFT JOIN estados_camion ec ON c.id_estado_camion = ec.id_estado_camion
                  LEFT JOIN usuarios u ON c.id_usuario_responsable = u.id_usuario
                  ORDER BY c.id_camion ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function insert($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (matricula, marca, modelo, capacidad, id_estado_camion, fecha_adquisicion, ultima_revision, id_usuario_responsable)
                  VALUES (:matricula, :marca, :modelo, :capacidad, :estado, :fecha_adquisicion, :ultima_revision, :responsable)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':matricula', $data['matricula']);
        $stmt->bindParam(':marca', $data['marca']);
        $stmt->bindParam(':modelo', $data['modelo']);
        $stmt->bindParam(':capacidad', $data['capacidad']);
        $stmt->bindParam(':estado', $data['id_estado_camion']);
        $stmt->bindParam(':fecha_adquisicion', $data['fecha_adquisicion']);
        $stmt->bindParam(':ultima_revision', $data['ultima_revision']);
        $stmt->bindParam(':responsable', $data['id_usuario_responsable']);
        
        return $stmt->execute();
    }

    public function getCamionById($id) {
        $query = "SELECT 
                    c.id_camion, 
                    c.matricula, 
                    c.marca, 
                    c.modelo, 
                    c.capacidad, 
                    c.id_estado_camion,
                    c.fecha_adquisicion, 
                    c.ultima_revision,
                    c.id_usuario_responsable
                  FROM " . $this->table_name . " c
                  WHERE c.id_camion = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            error_log("No se encontró camión con ID: ".$id);
        }
        
        return $result;
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                SET 
                    matricula = :matricula,
                    marca = :marca,
                    modelo = :modelo,
                    capacidad = :capacidad,
                    id_estado_camion = :estado,
                    fecha_adquisicion = :fecha_adquisicion,
                    ultima_revision = :ultima_revision,
                    id_usuario_responsable = :responsable
                WHERE id_camion = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':matricula', $data['matricula']);
        $stmt->bindParam(':marca', $data['marca']);
        $stmt->bindParam(':modelo', $data['modelo']);
        $stmt->bindParam(':capacidad', $data['capacidad']);
        $stmt->bindParam(':estado', $data['id_estado_camion']);
        $stmt->bindParam(':fecha_adquisicion', $data['fecha_adquisicion']);
        $stmt->bindParam(':ultima_revision', $data['ultima_revision']);
        $stmt->bindParam(':responsable', $data['id_usuario_responsable']);
        
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_camion = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Método para obtener el camión asignado a un conductor
    public function getCamionByConductor($id_usuario) {
        $query = "SELECT 
                    c.id_camion, 
                    c.matricula, 
                    c.marca, 
                    c.modelo, 
                    c.capacidad, 
                    c.id_estado_camion,
                    c.fecha_adquisicion, 
                    c.ultima_revision,
                    c.id_usuario_responsable
                  FROM " . $this->table_name . " c
                  WHERE c.id_usuario_responsable = :id_usuario LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Métodos auxiliares
    public function count() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getEstadosCamion() {
        $query = "SELECT * FROM estados_camion";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getConductores() {
        $query = "SELECT id_usuario, nombre FROM usuarios WHERE id_rol = 3 ORDER BY nombre";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function matriculaExists($matricula, $excludeId = null) {
        $query = "SELECT id_camion FROM " . $this->table_name . " WHERE matricula = :matricula";
        if ($excludeId) {
            $query .= " AND id_camion != :exclude_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':matricula', $matricula);
        if ($excludeId) {
            $stmt->bindParam(':exclude_id', $excludeId);
        }
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}