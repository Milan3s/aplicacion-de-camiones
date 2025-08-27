<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Roles.php';

class RolesController {
    private $db;
    private $rol;

    public function __construct() {
        $this->db = new Database();
        $this->rol = new Roles($this->db->connect());
    }

    public function getRolesModel() {
        return $this->rol;
    }

    // Métodos para mostrar formularios
    public function mostrarFormulario() {
        try {
            return [];
        } catch (PDOException $e) {
            error_log("Error al cargar formulario: " . $e->getMessage());
            return ['error' => 'Error al cargar el formulario'];
        }
    }

    public function mostrarFormularioEdicion($id) {
        try {
            $rol = $this->rol->getRolById($id);
            if (!$rol) {
                error_log("Rol no encontrado con ID: " . $id);
                return ['error' => 'Rol no encontrado'];
            }
    
            return [
                'rol' => $rol
            ];
        } catch (PDOException $e) {
            error_log("Error en mostrarFormularioEdicion: " . $e->getMessage());
            return ['error' => 'Error al cargar el formulario de edición'];
        }
    }

    // Métodos CRUD
    public function agregarRol($data) {
        try {
            $errors = $this->validarDatos($data);
            if (!empty($errors)) {
                return ['error' => implode(', ', $errors)];
            }

            if ($this->rol->insert($data)) {
                return ['success' => 'Rol agregado correctamente'];
            }
            return ['error' => 'Error al guardar el rol'];
        } catch (PDOException $e) {
            error_log("Error al agregar rol: " . $e->getMessage());
            return ['error' => 'Error al agregar el rol'];
        }
    }

    public function editarRol($id, $data) {
        try {
            $errors = $this->validarDatos($data, $id);
            if (!empty($errors)) {
                return ['error' => implode(', ', $errors)];
            }

            if ($this->rol->update($id, $data)) {
                return ['success' => 'Rol actualizado correctamente'];
            }
            return ['error' => 'Error al actualizar el rol'];
        } catch (PDOException $e) {
            error_log("Error al editar rol: " . $e->getMessage());
            return ['error' => 'Error al editar el rol'];
        }
    }

    public function eliminarRol($id) {
        try {
            if ($this->rol->delete($id)) {
                return ['success' => 'Rol eliminado correctamente'];
            }
            return ['error' => 'Error al eliminar el rol'];
        } catch (PDOException $e) {
            error_log("Error al eliminar rol: " . $e->getMessage());
            return ['error' => 'Error al eliminar el rol'];
        }
    }

    // Métodos auxiliares
    private function validarDatos($data, $id = null) {
        $errors = [];
        
        if (empty($data['nombre_rol'])) {
            $errors[] = 'Nombre del rol es requerido';
        }
        if (empty($data['descripcion'])) {
            $errors[] = 'Descripción es requerida';
        }
        
        // Validar si el nombre del rol ya existe (excepto para el rol actual en edición)
        if ($this->rol->nombreRolExists($data['nombre_rol'], $id)) {
            $errors[] = 'El nombre del rol ya existe';
        }
        
        return $errors;
    }

    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
            if ($_GET['action'] === 'agregar') {
                $result = $this->agregarRol($_POST);
                return array_merge($result, $this->mostrarFormulario());
            } elseif ($_GET['action'] === 'editar' && isset($_GET['id'])) {
                $result = $this->editarRol($_GET['id'], $_POST);
                return array_merge($result, $this->mostrarFormularioEdicion($_GET['id']));
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
            if ($_GET['action'] === 'editar' && isset($_GET['id'])) {
                return $this->mostrarFormularioEdicion($_GET['id']);
            } elseif ($_GET['action'] === 'eliminar' && isset($_GET['id'])) {
                return $this->eliminarRol($_GET['id']);
            }
        }
        
        return $this->mostrarFormulario();
    }
}