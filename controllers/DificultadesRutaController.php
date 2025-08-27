<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/DificultadesRuta.php';

class DificultadesRutaController {
    private $db;
    private $dificultadRuta;

    public function __construct() {
        $this->db = new Database();
        $this->dificultadRuta = new DificultadesRuta($this->db->connect());
    }

    // Listar todas las dificultades
    public function listarDificultades() {
        return $this->dificultadRuta->obtenerTodos();
    }

    // Obtener dificultad por ID
    public function obtenerDificultadPorId($id) {
        return $this->dificultadRuta->getById($id);
    }

    // Procesar creación de nueva dificultad
    public function procesarCreacion() {
        session_start();
        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);
        
        if (empty($nombre)) {
            $_SESSION['error'] = "El nombre es obligatorio";
            header('Location: add.php');
            exit();
        }
        
        if ($this->dificultadRuta->crear($nombre, $descripcion)) {
            $_SESSION['success'] = "Dificultad creada correctamente";
            header('Location: list.php');
        } else {
            $_SESSION['error'] = "Error al crear la dificultad";
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
        
        if ($this->dificultadRuta->actualizar($id, $nombre, $descripcion)) {
            $_SESSION['success'] = "Dificultad actualizada correctamente";
        } else {
            $_SESSION['error'] = "Error al actualizar la dificultad";
        }
        header("Location: edit.php?id=$id");
        exit();
    }

    // Procesar eliminación
    public function procesarEliminacion() {
        session_start();
        $id = $_POST['id'];
        
        if ($this->dificultadRuta->eliminar($id)) {
            $_SESSION['success'] = "Dificultad eliminada correctamente";
        } else {
            $_SESSION['error'] = "Error al eliminar (¿tiene rutas asociadas?)";
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