<?php
// app/controllers/CategoriasController.php

// Iniciar la sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class CategoriasController
{
    /**
     * @var PDO
     */
    private $db;

    public function __construct()
    {
        require_once BASE_PATH . '/config/database.php';
        require_once BASE_PATH . '/config/config.php';

        $this->db = getPDO();
    }

    /**
     * Muestra el listado de categorías
     * URL: index.php?c=categorias&a=index
     */
    public function index()
    {
        // Obtener todas las categorías con su tipo de torneo
        $stmt = $this->db->query("
            SELECT 
                c.id,
                c.nombre,
                t.nombre AS tipo_torneo,
                t.id AS tipo_torneo_id
            FROM categorias c
            JOIN tipos_torneo t ON c.tipo_torneo_id = t.id
            ORDER BY t.nombre, c.nombre
        ");
        $categorias = $stmt->fetchAll();

        // Obtener los tipos de torneo para el formulario
        $stmt = $this->db->query("SELECT id, nombre FROM tipos_torneo ORDER BY nombre");
        $tiposTorneo = $stmt->fetchAll();

        // Si estamos editando, obtener los datos de la categoría
        $categoriaEditar = null;
        if (isset($_GET['editar'])) {
            $id = filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT);
            if ($id) {
                $stmt = $this->db->prepare("
                    SELECT c.*, t.nombre as tipo_torneo_nombre 
                    FROM categorias c 
                    JOIN tipos_torneo t ON c.tipo_torneo_id = t.id 
                    WHERE c.id = :id
                ");
                $stmt->execute([':id' => $id]);
                $categoriaEditar = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }

        $pageTitle = 'Gestión de Categorías - ' . (defined('EVENT_NAME') ? EVENT_NAME : 'Tresfronteras');

        require BASE_PATH . '/app/views/layout/header.php';
        require BASE_PATH . '/app/views/layout/navbar.php';
        require BASE_PATH . '/app/views/categorias/index.php';
        require BASE_PATH . '/app/views/layout/footer.php';
    }

    /**
     * Procesa el formulario de creación/edición de categoría
     * URL: index.php?c=categorias&a=guardar (POST)
     */
    public function guardar()
    {
        // Validar que sea una petición POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?c=categorias&a=index');
            exit;
        }

        // Validar y sanitizar los datos
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nombre = trim(filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING));
        $tipoTorneoId = filter_input(INPUT_POST, 'tipo_torneo_id', FILTER_VALIDATE_INT);

        // Validaciones
        $errores = [];
        if (empty($nombre)) {
            $errores[] = 'El nombre de la categoría es obligatorio.';
        }
        if (!$tipoTorneoId) {
            $errores[] = 'Debe seleccionar un tipo de torneo.';
        }

        if (!empty($errores)) {
            $_SESSION['mensaje_error'] = implode('<br>', $errores);
            header('Location: index.php?c=categorias&a=index');
            exit;
        }

        try {
            if ($id) {
                // Actualizar categoría existente
                $stmt = $this->db->prepare("
                    UPDATE categorias 
                    SET nombre = :nombre, tipo_torneo_id = :tipo_torneo_id
                    WHERE id = :id
                ");
                $stmt->execute([
                    ':id' => $id,
                    ':nombre' => $nombre,
                    ':tipo_torneo_id' => $tipoTorneoId
                ]);
                $mensaje = 'Categoría actualizada correctamente.';
            } else {
                // Crear nueva categoría
                $stmt = $this->db->prepare("
                    INSERT INTO categorias (nombre, tipo_torneo_id)
                    VALUES (:nombre, :tipo_torneo_id)
                ");
                $stmt->execute([
                    ':nombre' => $nombre,
                    ':tipo_torneo_id' => $tipoTorneoId
                ]);
                $mensaje = 'Categoría creada correctamente.';
            }

            $_SESSION['mensaje_exito'] = $mensaje;
        } catch (PDOException $e) {
            $_SESSION['mensaje_error'] = 'Error al guardar la categoría: ' . $e->getMessage();
        }

        header('Location: index.php?c=categorias&a=index');
        exit;
    }

    /**
     * Elimina una categoría
     * URL: index.php?c=categorias&a=eliminar&id=X
     */
    public function eliminar()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $_SESSION['mensaje_error'] = 'ID de categoría no válido.';
            header('Location: index.php?c=categorias&a=index');
            exit;
        }

        try {
            // Verificar si la categoría tiene partidos asociados
            $stmt = $this->db->prepare("
                SELECT COUNT(*) AS total 
                FROM partidos 
                WHERE categoria_id = :id
            ");
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch();

            if ($result && $result['total'] > 0) {
                throw new Exception('No se puede eliminar la categoría porque tiene partidos asociados.');
            }

            // Eliminar la categoría
            $stmt = $this->db->prepare("DELETE FROM categorias WHERE id = :id");
            $stmt->execute([':id' => $id]);

            $_SESSION['mensaje_exito'] = 'Categoría eliminada correctamente.';
        } catch (Exception $e) {
            $_SESSION['mensaje_error'] = $e->getMessage();
        }

        header('Location: index.php?c=categorias&a=index');
        exit;
    }
}