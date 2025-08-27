<?php
class Database {
    private $host = 'localhost';       // Dirección del servidor de base de datos
    private $db_name = 'proyecto_transportes';  // Nombre de la base de datos
    private $username = 'dmilanes';        // Nombre de usuario de la base de datos
    private $password = 'milanes1982*-';            // Contraseña de la base de datos
    private $conn;                     // Instancia de la conexión PDO

    // Método para conectar a la base de datos
    public function connect() {
        $this->conn = null;

        try {
            // Conexión utilizando PDO
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            
            // Establecer el modo de error de PDO para excepciones
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
        } catch (PDOException $e) {
            // En caso de error, muestra el mensaje y termina la ejecución
            die("Error de conexión: " . $e->getMessage());
        }

        return $this->conn;
    }
}
?>
