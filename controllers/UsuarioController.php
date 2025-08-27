<?php
session_start();
require_once '../config/database.php';
require_once '../models/Usuarios.php';

class UsuarioController {
    private $db;
    private $usuario;

    public function __construct() {
        $this->db = new Database();
        $this->usuario = new Usuarios($this->db->connect());
    }

    // Mostrar la página de gestión (index)
    public function index() {
        include_once '../views/usuarios/index.php';
    }

    // Mostrar el formulario para editar un usuario
    public function edit($id_usuario) {
        $usuario = $this->usuario->readOne($id_usuario);
        if ($usuario) {
            // Obtener los roles para el formulario
            $query = "SELECT id_rol, nombre_rol FROM roles";
            $stmt = $this->db->connect()->query($query);
            $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            include_once '../views/usuarios/edit.php';
        } else {
            echo "Usuario no encontrado.";
        }
    }

    // Actualizar un usuario
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_usuario = $_POST['id_usuario'];
            $nombre = $_POST['nombre'];
            $email = $_POST['email'];
            $id_rol = $_POST['id_rol'];
            $password = !empty($_POST['password']) ? $_POST['password'] : null;

            if ($this->usuario->update($id_usuario, $nombre, $email, $id_rol, $password)) {
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Usuario actualizado correctamente.'];
            } else {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'Error al actualizar el usuario. Es posible que el email ya esté registrado.'];
            }
            header('Location: index.php');
            exit();
        }
    }

    // Eliminar un usuario
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_usuario'])) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Error: Solicitud inválida.'];
            header('Location: index.php');
            exit();
        }

        $id_usuario = $_POST['id_usuario'];

        // Verificar si el usuario existe antes de intentar eliminarlo
        $usuario = $this->usuario->readOne($id_usuario);
        if (!$usuario) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Error: El usuario no existe.'];
            header('Location: index.php');
            exit();
        }

        // Intentar eliminar el usuario
        if ($this->usuario->delete($id_usuario)) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Usuario eliminado correctamente.'];
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Error al eliminar el usuario. Puede que esté relacionado con otros registros o no exista.'];
        }
        header('Location: index.php');
        exit();
    }
}

// Manejar las solicitudes
$controller = new UsuarioController();

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    switch ($action) {
        case 'edit':
            $controller->edit($_GET['id']);
            break;
        case 'update':
            $controller->update();
            break;
        case 'delete':
            $controller->delete();
            break;
        default:
            $controller->index();
            break;
    }
} else {
    $controller->index();
}