<?php
require_once __DIR__ . '/../../controllers/CamionesController.php';

session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: list.php?error=ID no proporcionado');
    exit();
}

$controller = new CamionesController();
$id = $_GET['id'];

try {
    // Verificar si el camión existe antes de eliminar
    $camion = $controller->camion->getCamionById($id);
    if (!$camion) {
        header('Location: list.php?error=Camion no encontrado');
        exit();
    }

    // Eliminar el camión
    if ($controller->camion->delete($id)) {
        header('Location: list.php?success=Camion eliminado correctamente');
    } else {
        header('Location: list.php?error=Error al eliminar el camión');
    }
} catch (PDOException $e) {
    error_log("Error al eliminar camión: " . $e->getMessage());
    header('Location: list.php?error=Error al eliminar el camión');
}
?>