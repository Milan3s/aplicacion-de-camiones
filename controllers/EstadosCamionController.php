<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/EstadosCamion.php';

class EstadosCamionController {
    private $db;
    private $estadoCamion;

    public function __construct() {
        $this->db = new Database();
        $this->estadoCamion = new EstadosCamion($this->db->connect());
    }

    // Listar todos los estados
    public function listarEstados() {
        return $this->estadoCamion->obtenerTodos();
    }

    // Obtener estado por ID
    public function obtenerEstadoPorId($id) {
        return $this->estadoCamion->getById($id);
    }

    // Procesar creación de nuevo estado
    public function procesarCreacion() {
        session_start();
        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);
        
        if (empty($nombre)) {
            $_SESSION['error'] = "El nombre es obligatorio";
            header('Location: add.php');
            exit();
        }
        
        if ($this->estadoCamion->crear($nombre, $descripcion)) {
            $_SESSION['success'] = "Estado creado correctamente";
            header('Location: list.php');
        } else {
            $_SESSION['error'] = "Error al crear el estado";
            header('Location: add.php');
        }
        exit();
    }

    // Procesar edición
    public function procesarEdicion() {
        session_start();
        $id = $_POST['id'];
        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);
        
        if ($this->estadoCamion->actualizar($id, $nombre, $descripcion)) {
            $_SESSION['success'] = "Estado actualizado correctamente";
        } else {
            $_SESSION['error'] = "Error al actualizar el estado";
        }
        header("Location: edit.php?id=$id");
        exit();
    }

    // Procesar eliminación
    public function procesarEliminacion() {
        session_start();
        $id = $_POST['id'];
        
        if ($this->estadoCamion->eliminar($id)) {
            $_SESSION['success'] = "Estado eliminado correctamente";
        } else {
            $_SESSION['error'] = "Error al eliminar (¿tiene camiones asociados?)";
        }
        header('Location: list.php');
        exit();
    }

    // Manejar todas las peticiones
    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_GET['action'] ?? '';
            
            switch ($action) {
                case 'crear':
                    $this->procesarCreacion();
                    break;
                case 'editar':
                    $this->procesarEdicion();
                    break;
                case 'eliminar':
                    $this->procesarEliminacion();
                    break;
            }
        }
    }
}