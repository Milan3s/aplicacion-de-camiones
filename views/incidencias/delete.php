<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once __DIR__ . '/../../controllers/IncidenciaController.php';
$controller = new IncidenciaController();

$id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : null;

if ($id && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $controller->eliminarIncidencia($id);
    if (isset($result['success'])) {
        header('Location: list.php?success=' . urlencode($result['success']));
    } else {
        header('Location: list.php?error=' . urlencode($result['error']));
    }
    exit();
} else {
    header('Location: list.php?error=' . urlencode('ID de incidencia no válido'));
    exit();
}