<?php
session_start();

// Desactivar caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once '../config/database.php';
require_once '../models/Usuarios.php';

class AuthController {
    private $db;
    private $usuarioModel;

    public function __construct() {
        $this->db = new Database();
        $this->usuarioModel = new Usuarios($this->db->connect());
    }

    public function register($nombre, $email, $password) {
        $id_rol = 2; // Rol por defecto (Conductor)

        try {
            $result = $this->usuarioModel->create($nombre, $email, $password, $id_rol);
            
            if ($result) {
                $_SESSION['success'] = "Registro exitoso. Por favor inicie sesión.";
                header('Location: ../views/auth/login.php');
                exit();
            } else {
                $_SESSION['error'] = "El correo electrónico ya está registrado.";
                header('Location: ../views/auth/register.php');
                exit();
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error en el registro: " . $e->getMessage();
            header('Location: ../views/auth/register.php');
            exit();
        }
    }

    public function login($email, $password) {
        // Obtener el usuario con el nombre del rol desde la base de datos
        $user = $this->usuarioModel->getUserByEmail($email);
    
        if ($user && password_verify($password, $user['password'])) {
            // Login exitoso - Incluir todos los datos necesarios
            $_SESSION['user'] = [
                'id_usuario' => $user['id_usuario'],
                'nombre' => $user['nombre'],
                'email' => $user['email'],
                'fecha_registro' => $user['fecha_registro'],
                'id_rol' => $user['id_rol'],
                'nombre_rol' => $user['nombre_rol'] ?? 'Desconocido' // Valor por defecto si nombre_rol es NULL
            ];
    
            header('Location: ../views/auth/dashboard.php');
            exit();
        } else {
            $_SESSION['error'] = "Credenciales incorrectas";
            header('Location: ../views/auth/login.php');
            exit();
        }
    }

    

    public function logout() {
        // Limpiar todas las variables de sesión
        $_SESSION = [];

        // Limpiar la cookie de sesión si existe
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Destruir la sesión
        session_destroy();

        // Redirigir al login
        header('Location: ../views/auth/login.php');
        exit();
    }
}


// Manejo de solicitudes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = new AuthController();
    
    if (isset($_POST['register'])) {
        $auth->register($_POST['nombre'], $_POST['email'], $_POST['password']);
    } elseif (isset($_POST['login'])) {
        $auth->login($_POST['email'], $_POST['password']);
    }
}

if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    $auth = new AuthController();
    $auth->logout();
}