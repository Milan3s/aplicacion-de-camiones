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

        // Sanitizar y vincular parámetros
        $nombre = htmlspecialchars(strip_tags($nombre));
        $email = htmlspecialchars(strip_tags($email));
        $password = password_hash($password, PASSWORD_BCRYPT); // Encriptar la contraseña
        $id_rol = htmlspecialchars(strip_tags($id_rol));

        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':id_rol', $id_rol);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Manejar errores, como email duplicado
            if ($e->getCode() == 23000) { // Código de error para violación de clave única (email duplicado)
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

        // Sanitizar y vincular parámetros
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
            return $stmt->execute();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Código de error para violación de clave única (email duplicado)
                return false;
            }
            throw $e;
        }
    }

    // Eliminar un usuario
    public function delete($id_usuario) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);

        // Sanitizar y vincular parámetros
        $id_usuario = htmlspecialchars(strip_tags($id_usuario));
        $stmt->bindParam(':id_usuario', $id_usuario);

        try {
            $success = $stmt->execute();
            // Verificar si se eliminó alguna fila
            if ($success && $stmt->rowCount() > 0) {
                return true; // Eliminación exitosa
            }
            return false; // No se encontró el usuario o no se eliminó
        } catch (PDOException $e) {
            // Manejar errores, como restricciones de clave foránea
            return false;
        }
    }
}