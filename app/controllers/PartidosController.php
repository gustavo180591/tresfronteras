<?php
// app/controllers/PartidosController.php

class PartidosController
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
     * Verifica si la petición es AJAX.
     */
    private function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Listado de partidos (fixture) con paginación.
     *
     * URL: index.php?c=partidos&a=index&page=1
     */
    public function index()
    {
        // Página actual (mínimo 1)
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        if ($page < 1) {
            $page = 1;
        }

        // Registros por página
        $perPage = defined('ITEMS_PER_PAGE') ? (int) ITEMS_PER_PAGE : 20;
        if ($perPage < 1) {
            $perPage = 20;
        }

        $offset = ($page - 1) * $perPage;

        // Total de partidos
        $stmtTotal = $this->db->query('SELECT COUNT(*) AS total FROM partidos');
        $rowTotal = $stmtTotal->fetch();
        $totalPartidos = isset($rowTotal['total']) ? (int) $rowTotal['total'] : 0;

        $totalPages = $totalPartidos > 0 ? (int) ceil($totalPartidos / $perPage) : 1;
        if ($page > $totalPages) {
            $page = $totalPages;
            $offset = ($page - 1) * $perPage;
        }

        // Partidos con categoría y tipo de torneo
        $sql = "
            SELECT 
                p.id,
                p.equipo_a,
                p.equipo_b,
                p.fecha_hora,
                p.cancha,
                p.estado,
                p.goles_equipo_a,
                p.goles_equipo_b,
                c.nombre AS categoria_nombre,
                tt.nombre AS tipo_torneo_nombre
            FROM partidos p
            INNER JOIN categorias c ON p.categoria_id = c.id
            INNER JOIN tipos_torneo tt ON c.tipo_torneo_id = tt.id
            ORDER BY p.fecha_hora ASC, p.id ASC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $partidos = $stmt->fetchAll();

        $data = array(
            'partidos'      => $partidos,
            'page'          => $page,
            'perPage'       => $perPage,
            'totalPartidos' => $totalPartidos,
            'totalPages'    => $totalPages,
        );

        extract($data);

        $pageTitle = 'Fixture de partidos - ' . (defined('EVENT_NAME') ? EVENT_NAME : 'Tresfronteras');

        require BASE_PATH . '/app/views/layout/header.php';
        require BASE_PATH . '/app/views/layout/navbar.php';
        require BASE_PATH . '/app/views/partidos/index.php';
        require BASE_PATH . '/app/views/layout/footer.php';
    }

    /**
     * Muestra el formulario para crear un nuevo partido.
     * URL: index.php?c=partidos&a=crear
     */
    public function crear()
    {
        // Obtener todas las categorías para el select
        $stmt = $this->db->query("
            SELECT c.id, c.nombre, t.nombre AS tipo_torneo
            FROM categorias c
            JOIN tipos_torneo t ON c.tipo_torneo_id = t.id
            ORDER BY t.nombre, c.nombre
        ");
        $categorias = $stmt->fetchAll();

        // Obtener los tipos de torneo disponibles
        $stmt = $this->db->query("SELECT id, nombre FROM tipos_torneo ORDER BY nombre");
        $tiposTorneo = $stmt->fetchAll();

        // Datos por defecto para el formulario
        $partido = [
            'id' => null,
            'categoria_id' => '',
            'equipo_a' => '',
            'equipo_b' => '',
            'fecha_hora' => date('Y-m-d\TH:i'),
            'cancha' => '',
            'observaciones' => '',
            'estado' => 'pendiente',
            'ronda' => '',
            'numero_en_ronda' => ''
        ];

        $pageTitle = 'Nuevo Partido - ' . (defined('EVENT_NAME') ? EVENT_NAME : 'Tresfronteras');

        require BASE_PATH . '/app/views/layout/header.php';
        require BASE_PATH . '/app/views/layout/navbar.php';
        require BASE_PATH . '/app/views/partidos/form.php';
        require BASE_PATH . '/app/views/layout/footer.php';
    }

    /**
     * Procesa el formulario de creación de partido.
     * URL: index.php?c=partidos&a=guardar (POST)
     */
    public function guardar()
    {
        // Validar que sea una petición POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?c=partidos&a=index');
            exit;
        }

        // Validar y sanitizar los datos del formulario
        $categoriaId = filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT);
        $equipoA = trim(filter_input(INPUT_POST, 'equipo_a', FILTER_SANITIZE_STRING));
        $equipoB = trim(filter_input(INPUT_POST, 'equipo_b', FILTER_SANITIZE_STRING));
        $fechaHora = filter_input(INPUT_POST, 'fecha_hora', FILTER_SANITIZE_STRING);
        $cancha = trim(filter_input(INPUT_POST, 'cancha', FILTER_SANITIZE_STRING));
        $observaciones = trim(filter_input(INPUT_POST, 'observaciones', FILTER_SANITIZE_STRING));
        $estado = in_array($_POST['estado'] ?? '', ['pendiente', 'en_juego', 'finalizado']) 
                ? $_POST['estado'] 
                : 'pendiente';
        $ronda = trim(filter_input(INPUT_POST, 'ronda', FILTER_SANITIZE_STRING));
        $numeroEnRonda = filter_input(INPUT_POST, 'numero_en_ronda', FILTER_VALIDATE_INT);

        // Validaciones básicas
        $errores = [];

        if (!$categoriaId) {
            $errores[] = 'La categoría es obligatoria';
        }

        if (empty($equipoA)) {
            $errores[] = 'El equipo local es obligatorio';
        }

        if (empty($equipoB)) {
            $errores[] = 'El equipo visitante es obligatorio';
        }

        if (empty($fechaHora) || !strtotime($fechaHora)) {
            $errores[] = 'La fecha y hora son obligatorias';
        }

        // Si hay errores, volver al formulario
        if (!empty($errores)) {
            // Recargar categorías para el select
            $stmt = $this->db->query("
                SELECT c.id, c.nombre, t.nombre AS tipo_torneo
                FROM categorias c
                JOIN tipos_torneo t ON c.tipo_torneo_id = t.id
                ORDER BY t.nombre, c.nombre
            ");
            $categorias = $stmt->fetchAll();

            // Mantener los datos del formulario
            $partido = [
                'categoria_id' => $categoriaId,
                'equipo_a' => $equipoA,
                'equipo_b' => $equipoB,
                'fecha_hora' => $fechaHora,
                'cancha' => $cancha,
                'observaciones' => $observaciones,
                'estado' => $estado,
                'ronda' => $ronda,
                'numero_en_ronda' => $numeroEnRonda
            ];

            // Mostrar el formulario con errores
            $pageTitle = 'Nuevo Partido - ' . (defined('EVENT_NAME') ? EVENT_NAME : 'Tresfronteras');
            
            require BASE_PATH . '/app/views/layout/header.php';
            require BASE_PATH . '/app/views/layout/navbar.php';
            require BASE_PATH . '/app/views/partidos/form.php';
            require BASE_PATH . '/app/views/layout/footer.php';
            return;
        }

        // Insertar el nuevo partido en la base de datos
        $stmt = $this->db->prepare("
            INSERT INTO partidos (
                categoria_id, equipo_a, equipo_b, fecha_hora, 
                cancha, observaciones, estado, ronda, numero_en_ronda
            ) VALUES (
                :categoria_id, :equipo_a, :equipo_b, :fecha_hora,
                :cancha, :observaciones, :estado, :ronda, :numero_en_ronda
            )
        ");

        $stmt->execute([
            ':categoria_id' => $categoriaId,
            ':equipo_a' => $equipoA,
            ':equipo_b' => $equipoB,
            ':fecha_hora' => $fechaHora,
            ':cancha' => $cancha ?: null,
            ':observaciones' => $observaciones ?: null,
            ':estado' => $estado,
            ':ronda' => $ronda ?: null,
            ':numero_en_ronda' => $numeroEnRonda ?: null
        ]);

        // Redirigir al listado con mensaje de éxito
        $_SESSION['mensaje_exito'] = 'El partido se ha creado correctamente.';
        header('Location: index.php?c=partidos&a=index');
        exit;
    }

    /**
     * Elimina un partido existente.
     * URL: index.php?c=partidos&a=eliminar&id=X (POST)
     */
    public function eliminar()
    {
        // Verificar que sea una petición POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?c=partidos&a=index');
            exit;
        }

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $_SESSION['mensaje_error'] = 'ID de partido no válido.';
            header('Location: index.php?c=partidos&a=index');
            exit;
        }

        try {
            $stmt = $this->db->prepare('DELETE FROM partidos WHERE id = :id');
            $stmt->execute([':id' => $id]);

            $_SESSION['mensaje_exito'] = 'El partido se ha eliminado correctamente.';
        } catch (PDOException $e) {
            $_SESSION['mensaje_error'] = 'Error al eliminar el partido: ' . $e->getMessage();
        }

        header('Location: index.php?c=partidos&a=index');
        exit;
    }
    
   
   /**
 * Actualiza el resultado de un partido vía AJAX
 * URL: index.php?c=partidos&a=actualizarResultado (POST)
 */
public function actualizarResultado()
{
    // Solo permitir peticiones AJAX
    if (!$this->isAjax()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        exit;
    }

    // Validar datos
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $golesA = filter_input(INPUT_POST, 'goles_a', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    $golesB = filter_input(INPUT_POST, 'goles_b', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);

    if (!$id || $golesA === false || $golesB === false) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
        exit;
    }

    try {
        // Actualizar el partido en la base de datos
        $stmt = $this->db->prepare("
            UPDATE partidos 
            SET goles_equipo_a = :goles_a, 
                goles_equipo_b = :goles_b,
                estado = 'finalizado'
            WHERE id = :id
        ");

        $success = $stmt->execute([
            ':id' => $id,
            ':goles_a' => $golesA,
            ':goles_b' => $golesB
        ]);

        if ($success) {
            echo json_encode(['success' => true]);
        } else {
            throw new Exception('No se pudo actualizar el partido');
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false, 
            'message' => 'Error al actualizar el partido: ' . $e->getMessage()
        ]);
    }
    exit;
}
}
