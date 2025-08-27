<?php
class Usuarios {
    private $conn;
    private $table_name = "usuarios";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear un nuevo usuario
    public function create($nombre, $email, $password, $id_rol) {
        $query = "INSERT INTO " . $this->table_name . " (nombre, email, password, id_rol) VALUES (:nombre, :email, :password, :id_rol)";
        $stmt = $this->conn->prepare($query);

        $nombre = htmlspecialchars(strip_tags($nombre));
        $email = htmlspecialchars(strip_tags($email));
        $password = password_hash($password, PASSWORD_BCRYPT);
        $id_rol = htmlspecialchars(strip_tags($id_rol));

        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':id_rol', $id_rol);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return false;
            }
            throw $e;
        }
    }

    // Leer todos los usuarios
    public function read() {
        $query = "SELECT u.id_usuario, u.nombre, u.email, u.fecha_registro, r.nombre_rol, u.id_rol 
                  FROM " . $this->table_name . " u 
                  LEFT JOIN roles r ON u.id_rol = r.id_rol";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Leer un usuario por ID
    public function readOne($id_usuario) {
        $query = "SELECT u.id_usuario, u.nombre, u.email, u.fecha_registro, r.nombre_rol, u.id_rol 
                  FROM " . $this->table_name . " u 
                  LEFT JOIN roles r ON u.id_rol = r.id_rol 
                  WHERE u.id_usuario = :id_usuario LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $id_usuario = htmlspecialchars(strip_tags($id_usuario));
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar un usuario
    public function update($id_usuario, $nombre, $email, $id_rol, $password = null) {
        $query = "UPDATE " . $this->table_name . " 
                  SET nombre = :nombre, email = :email, id_rol = :id_rol" . 
                  ($password ? ", password = :password" : "") . 
                  " WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);

        $id_usuario = htmlspecialchars(strip_tags($id_usuario));
        $nombre = htmlspecialchars(strip_tags($nombre));
        $email = htmlspecialchars(strip_tags($email));
        $id_rol = htmlspecialchars(strip_tags($id_rol));

        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id_rol', $id_rol);

        if ($password) {
            $password = password_hash($password, PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $password);
        }

        try {
            $success = $stmt->execute();
            $rows_affected = $stmt->rowCount();
            error_log("Depuración UPDATE: id_usuario = $id_usuario, id_rol = $id_rol, éxito = " . ($success ? 'true' : 'false') . ", filas afectadas = $rows_affected");
            return $success && $rows_affected > 0;
        } catch (PDOException $e) {
            error_log("Error al actualizar usuario: " . $e->getMessage());
            return false;
        }
    }

    public function getUserByEmail($email) {
        $query = "SELECT u.*, r.nombre_rol 
                  FROM usuarios u 
                  LEFT JOIN roles r ON u.id_rol = r.id_rol 
                  WHERE u.email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Eliminar un usuario
    public function delete($id_usuario) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);

        $id_usuario = htmlspecialchars(strip_tags($id_usuario));
        $stmt->bindParam(':id_usuario', $id_usuario);

        try {
            $success = $stmt->execute();
            $rows_affected = $stmt->rowCount();
            error_log("Depuración DELETE: id_usuario = $id_usuario, éxito = " . ($success ? 'true' : 'false') . ", filas afectadas = $rows_affected");

            if ($success && $rows_affected > 0) {
                return ['success' => true, 'message' => 'Usuario eliminado correctamente.'];
            } else {
                error_log("Depuración DELETE: No se eliminó ninguna fila para id_usuario = $id_usuario");
                return ['success' => false, 'message' => 'Error: No se encontró el usuario o no se pudo eliminar.'];
            }
        } catch (PDOException $e) {
            error_log("Error al eliminar usuario: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al eliminar el usuario: ' . $e->getMessage()];
        }
    }
}