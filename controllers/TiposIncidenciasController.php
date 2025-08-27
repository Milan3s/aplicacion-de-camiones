<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/TiposIncidencias.php';

class TiposIncidenciasController {
    private $db;
    private $tipoIncidencia;

    public function __construct() {
        $this->db = new Database();
        $this->tipoIncidencia = new TiposIncidencias($this->db->connect());
    }

    // Listar todos los tipos
    public function listarTipos() {
        return $this->tipoIncidencia->obtenerTodos();
    }

    // Obtener tipo por ID
    public function obtenerTipoPorId($id) {
        return $this->tipoIncidencia->getById($id);
    }

    // Mostrar formulario de edición
    public function mostrarFormularioEdicion($id) {
        $tipo = $this->obtenerTipoPorId($id);
        if (!$tipo) {
            header('Location: list.php');
            exit();
        }
        return $tipo;
    }

    // Procesar creación de nuevo tipo
    public function procesarCreacion() {
        session_start();
        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);
        
        if (empty($nombre)) {
            $_SESSION['error'] = "El nombre es obligatorio";
            header('Location: add.php');
            exit();
        }
        
        if ($this->tipoIncidencia->crear($nombre, $descripcion)) {
            $_SESSION['success'] = "Tipo creado correctamente";
            header('Location: list.php');
        } else {
            $_SESSION['error'] = "Error al crear el tipo";
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
        
        if ($this->tipoIncidencia->actualizar($id, $nombre, $descripcion)) {
            $_SESSION['success'] = "Tipo actualizado correctamente";
        } else {
            $_SESSION['error'] = "Error al actualizar el tipo";
        }
        header("Location: edit.php?id=$id");
        exit();
    }

    // Procesar eliminación
    public function procesarEliminacion() {
        session_start();
        $id = $_POST['id'];
        
        if ($this->tipoIncidencia->eliminar($id)) {
            $_SESSION['success'] = "Tipo eliminado correctamente";
        } else {
            $_SESSION['error'] = "Error al eliminar (¿tiene incidencias asociadas?)";
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