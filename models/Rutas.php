<?php
class Rutas {
    private $conn;
    private $table_name = "rutas";
    private $table_dificultades = "dificultades_ruta";
    private $table_estados = "estados_ruta";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Método para obtener todas las rutas con información relacionada
    public function read() {
        $query = "SELECT 
                    r.id_ruta, 
                    r.origen, 
                    r.destino, 
                    r.distancia, 
                    r.tiempo_estimado,
                    r.fecha_registro,
                    dr.nombre as dificultad,
                    dr.descripcion as descripcion_dificultad,
                    er.nombre as estado,
                    er.descripcion as descripcion_estado
                  FROM " . $this->table_name . " r
                  LEFT JOIN " . $this->table_dificultades . " dr ON r.id_dificultad = dr.id_dificultad
                  LEFT JOIN " . $this->table_estados . " er ON r.id_estado_ruta = er.id_estado_ruta
                  ORDER BY r.fecha_registro DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Método para contar el total de rutas
    public function count() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Método para crear una nueva ruta
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (origen, destino, distancia, tiempo_estimado, id_dificultad, id_estado_ruta, fecha_registro) 
                  VALUES (:origen, :destino, :distancia, :tiempo_estimado, :id_dificultad, :id_estado_ruta, NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        // Limpiar y vincular los datos
        $stmt->bindParam(":origen", $data['origen']);
        $stmt->bindParam(":destino", $data['destino']);
        $stmt->bindParam(":distancia", $data['distancia']);
        $stmt->bindParam(":tiempo_estimado", $data['tiempo_estimado']);
        $stmt->bindParam(":id_dificultad", $data['id_dificultad']);
        $stmt->bindParam(":id_estado_ruta", $data['id_estado_ruta']);
        
        if ($stmt->execute()) {
            return ['success' => 'Ruta creada correctamente'];
        } else {
            return ['error' => 'Error al crear la ruta'];
        }
    }

    // Método para obtener una ruta por ID
    public function readOne($id) {
        $query = "SELECT 
                    r.id_ruta, 
                    r.origen, 
                    r.destino, 
                    r.distancia, 
                    r.tiempo_estimado,
                    r.id_dificultad,
                    r.id_estado_ruta,
                    r.fecha_registro,
                    dr.nombre as dificultad,
                    er.nombre as estado
                  FROM " . $this->table_name . " r
                  LEFT JOIN " . $this->table_dificultades . " dr ON r.id_dificultad = dr.id_dificultad
                  LEFT JOIN " . $this->table_estados . " er ON r.id_estado_ruta = er.id_estado_ruta
                  WHERE r.id_ruta = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Método para actualizar una ruta
    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET 
                    origen = :origen, 
                    destino = :destino, 
                    distancia = :distancia, 
                    tiempo_estimado = :tiempo_estimado,
                    id_dificultad = :id_dificultad,
                    id_estado_ruta = :id_estado_ruta
                  WHERE id_ruta = :id_ruta";
        
        $stmt = $this->conn->prepare($query);
        
        // Limpiar y vincular los datos
        $stmt->bindParam(":origen", $data['origen']);
        $stmt->bindParam(":destino", $data['destino']);
        $stmt->bindParam(":distancia", $data['distancia']);
        $stmt->bindParam(":tiempo_estimado", $data['tiempo_estimado']);
        $stmt->bindParam(":id_dificultad", $data['id_dificultad']);
        $stmt->bindParam(":id_estado_ruta", $data['id_estado_ruta']);
        $stmt->bindParam(":id_ruta", $id);
        
        if ($stmt->execute()) {
            return ['success' => 'Ruta actualizada correctamente'];
        } else {
            return ['error' => 'Error al actualizar la ruta'];
        }
    }

    // Método para eliminar una ruta
    public function delete($id) {
        // Primero verificamos si la ruta existe
        $checkQuery = "SELECT id_ruta FROM " . $this->table_name . " WHERE id_ruta = ?";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(1, $id);
        $checkStmt->execute();
        
        if ($checkStmt->rowCount() == 0) {
            return ['error' => 'La ruta no existe'];
        }

        // Si existe, procedemos a eliminar
        $query = "DELETE FROM " . $this->table_name . " WHERE id_ruta = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        
        if ($stmt->execute()) {
            return ['success' => 'Ruta eliminada correctamente'];
        } else {
            return ['error' => 'Error al eliminar la ruta'];
        }
    }

    // Método para obtener todas las dificultades
    public function getDificultades() {
        $query = "SELECT * FROM " . $this->table_dificultades . " ORDER BY nombre";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Método para obtener todos los estados de ruta
    public function getEstadosRuta() {
        $query = "SELECT * FROM " . $this->table_estados . " ORDER BY nombre";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}