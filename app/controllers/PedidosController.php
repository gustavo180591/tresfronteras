<?php
class PedidosController
{
    private $db;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        require_once BASE_PATH . '/config/database.php';
        $this->db = getPDO();
    }

    public function index()
    {
        // Obtener parámetros de filtrado
        $filtro_estado = $_GET['estado'] ?? 'todos';
        $filtro_forma_pago = $_GET['forma_pago'] ?? 'todas';
        $filtro_buscar = $_GET['buscar'] ?? '';
        
        // Lógica para listar pedidos
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        try {
            // Construir consulta base
            $sqlCount = "SELECT COUNT(*) as total 
                        FROM pedidos_fotos pf
                        JOIN partidos p ON pf.partido_id = p.id
                        JOIN categorias c ON p.categoria_id = c.id
                        WHERE 1=1";
            
            $sql = "SELECT pf.*, p.fecha_hora, p.equipo_a, p.equipo_b, c.nombre as categoria_nombre
                    FROM pedidos_fotos pf
                    JOIN partidos p ON pf.partido_id = p.id
                    JOIN categorias c ON p.categoria_id = c.id
                    WHERE 1=1";
            
            // Aplicar filtros
            $params = [];
            
            // Filtro por estado
            if ($filtro_estado !== 'todos') {
                if ($filtro_estado === 'pagado' || $filtro_estado === 'no_pagado') {
                    $sql .= " AND pf.estado_pago = :estado";
                    $sqlCount .= " AND pf.estado_pago = :estado";
                    $params[':estado'] = $filtro_estado;
                } elseif ($filtro_estado === 'entregado') {
                    $sql .= " AND pf.estado_entrega = 'entregado'";
                    $sqlCount .= " AND pf.estado_entrega = 'entregado'";
                }
            }
            
            // Filtro por forma de pago
            if ($filtro_forma_pago !== 'todas') {
                $sql .= " AND pf.forma_pago = :forma_pago";
                $sqlCount .= " AND pf.forma_pago = :forma_pago";
                $params[':forma_pago'] = $filtro_forma_pago;
            }
            
            // Filtro de búsqueda
            if (!empty($filtro_buscar)) {
                $searchTerm = "%$filtro_buscar%";
                $sql .= " AND (
                    pf.nombre_cliente LIKE :buscar OR 
                    pf.telefono LIKE :buscar OR
                    p.equipo_a LIKE :buscar OR
                    p.equipo_b LIKE :buscar OR
                    pf.archivos LIKE :buscar
                )";
                $sqlCount .= " AND (
                    pf.nombre_cliente LIKE :buscar OR 
                    pf.telefono LIKE :buscar OR
                    p.equipo_a LIKE :buscar OR
                    p.equipo_b LIKE :buscar OR
                    pf.archivos LIKE :buscar
                )";
                $params[':buscar'] = $searchTerm;
            }
            
            // Ordenar y paginar
            $sql .= " ORDER BY pf.fecha_pedido DESC LIMIT :limit OFFSET :offset";
            
            // Obtener total de pedidos con filtros
            $stmt = $this->db->prepare($sqlCount);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            $totalPedidos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            $totalPages = ceil($totalPedidos / $perPage);

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Cargar vista
            $pageTitle = 'Listado de Pedidos - ' . (defined('EVENT_NAME') ? EVENT_NAME : 'Tresfronteras');
            require BASE_PATH . '/app/views/layout/header.php';
            require BASE_PATH . '/app/views/layout/navbar.php';
            require BASE_PATH . '/app/views/pedidos/index.php';
            require BASE_PATH . '/app/views/layout/footer.php';

        } catch (PDOException $e) {
            error_log('Error en PedidosController::index(): ' . $e->getMessage());
            $_SESSION['error_message'] = 'Error al cargar el listado de pedidos.';
            header('Location: index.php');
            exit;
        }
    }

    public function nuevo()
    {
        try {
            // Obtener partidos
            $stmt = $this->db->query("
                SELECT p.id, p.fecha_hora, p.equipo_a, p.equipo_b, c.nombre as categoria_nombre
                FROM partidos p
                JOIN categorias c ON p.categoria_id = c.id
                WHERE p.fecha_hora >= NOW() - INTERVAL 7 DAY
                ORDER BY p.fecha_hora DESC
            ");
            $partidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Obtener precio por foto
            $precioPorFoto = 3000; // Valor por defecto
            $stmt = $this->db->query("SELECT valor FROM configuracion WHERE clave = 'precio_foto'");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                $precioPorFoto = (float)$result['valor'];
            }

            // Obtener datos del formulario guardados en sesión
            $formData = $_SESSION['form_data'] ?? [];
            unset($_SESSION['form_data']);

            // Obtener mensajes
            $error_message = $_SESSION['error_message'] ?? '';
            $success_message = $_SESSION['success_message'] ?? '';
            unset($_SESSION['error_message'], $_SESSION['success_message']);

            // Cargar vista
            $pageTitle = 'Nuevo Pedido - ' . (defined('EVENT_NAME') ? EVENT_NAME : 'Tresfronteras');
            require BASE_PATH . '/app/views/layout/header.php';
            require BASE_PATH . '/app/views/layout/navbar.php';
            require BASE_PATH . '/app/views/pedidos/form.php';
            require BASE_PATH . '/app/views/layout/footer.php';

        } catch (PDOException $e) {
            error_log('Error en PedidosController::nuevo(): ' . $e->getMessage());
            $_SESSION['error_message'] = 'Error al cargar el formulario de nuevo pedido.';
            header('Location: index.php?c=pedidos&a=index');
            exit;
        }
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?c=pedidos&a=nuevo');
            exit;
        }

        try {
            // Validar y obtener datos del formulario
            $partido_id = $_POST['partido_id'] ?? null;
            $nombre_cliente = trim($_POST['nombre_cliente'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $cantidad_fotos = (int)($_POST['cantidad_fotos'] ?? 0);
            $precio_por_foto = (float)str_replace(',', '.', $_POST['precio_por_foto'] ?? '0');
            $forma_pago = $_POST['forma_pago'] ?? 'efectivo';
            $estado_pago = $_POST['estado_pago'] ?? 'no_pagado';
            $monto_total = (float)($_POST['monto_total'] ?? 0);
            $archivos = $_POST['archivos'] ?? [];

            // Validaciones
            $errors = [];
            
            if (empty($partido_id)) {
                $errors[] = 'Debe seleccionar un partido';
            }
            
            if (empty($nombre_cliente)) {
                $errors[] = 'El nombre del cliente es obligatorio';
            }
            
            if (empty($telefono)) {
                $errors[] = 'El teléfono es obligatorio';
            }
            
            if ($cantidad_fotos <= 0) {
                $errors[] = 'La cantidad de fotos debe ser mayor a cero';
            }
            
            if ($precio_por_foto <= 0) {
                $errors[] = 'El precio por foto debe ser mayor a cero';
            }
            
            if (count($archivos) !== $cantidad_fotos) {
                $errors[] = 'Debe ingresar los nombres de todos los archivos';
            }

            // Si hay errores, volver al formulario
            if (!empty($errors)) {
                throw new Exception(implode('<br>', $errors));
            }

            // Convertir array de nombres de archivo a string
            $nombres_archivos = is_array($archivos) ? implode(', ', array_map('trim', $archivos)) : '';

            // Guardar en la base de datos
            $stmt = $this->db->prepare("
                INSERT INTO pedidos_fotos (
                    partido_id, nombre_cliente, telefono, cantidad_fotos,
                    forma_pago, estado_pago, monto_total, archivos, fecha_pedido
                ) VALUES (
                    :partido_id, :nombre_cliente, :telefono, :cantidad_fotos,
                    :forma_pago, :estado_pago, :monto_total, :archivos, NOW()
                )
            ");

            // Ejecutar la consulta
            $stmt->execute([
                ':partido_id' => $partido_id,
                ':nombre_cliente' => $nombre_cliente,
                ':telefono' => $telefono,
                ':cantidad_fotos' => $cantidad_fotos,
                ':forma_pago' => $forma_pago,
                ':estado_pago' => $estado_pago,
                ':monto_total' => $monto_total,
                ':archivos' => $nombres_archivos
            ]);

            // Éxito - redirigir al listado
            $_SESSION['success_message'] = 'El pedido se ha guardado correctamente.';
            header('Location: index.php?c=pedidos&a=index');
            exit;

        } catch (Exception $e) {
            // Error - guardar datos del formulario y mensaje de error
            $_SESSION['form_data'] = $_POST;
            $_SESSION['error_message'] = $e->getMessage();
            error_log('Error en PedidosController::guardar(): ' . $e->getMessage());
            
            // Redirigir de vuelta al formulario
            header('Location: index.php?c=pedidos&a=nuevo');
            exit;
        }
    }

   public function cambiarEstadoPago()
{
    header('Content-Type: application/json');

    // Solo aceptar POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Método no permitido'
        ]);
        exit;
    }

    try {
        // Datos enviados por AJAX
        $pedidoId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nuevoEstado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING);

        if (!$pedidoId) {
            throw new Exception('ID de pedido inválido');
        }

        if (!in_array($nuevoEstado, ['pagado', 'no_pagado'], true)) {
            throw new Exception('Estado de pago inválido');
        }

        // Actualización
        $stmt = $this->db->prepare("
            UPDATE pedidos_fotos
            SET estado_pago = :estado
            WHERE id = :id
        ");

        $stmt->execute([
            ':estado' => $nuevoEstado,
            ':id' => $pedidoId
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Estado actualizado correctamente'
        ]);

    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}

public function ver() {
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        $_SESSION['error_message'] = 'ID de pedido no válido';
        header('Location: index.php?c=pedidos&a=index');
        exit;
    }

    $pedidoId = (int)$_GET['id'];
    
    try {
        // Obtener los detalles del pedido con información relacionada
        $sql = "SELECT pf.*, p.fecha_hora, p.equipo_a, p.equipo_b, c.nombre as categoria_nombre
                FROM pedidos_fotos pf
                JOIN partidos p ON pf.partido_id = p.id
                JOIN categorias c ON p.categoria_id = c.id
                WHERE pf.id = :id";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $pedidoId, PDO::PARAM_INT);
        $stmt->execute();
        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pedido) {
            throw new Exception('Pedido no encontrado');
        }

        // Cargar vista
        $pageTitle = 'Pedido #' . str_pad($pedido['id'], 5, '0', STR_PAD_LEFT) . ' - ' . (defined('EVENT_NAME') ? EVENT_NAME : 'Tresfronteras');
        require BASE_PATH . '/app/views/layout/header.php';
        require BASE_PATH . '/app/views/layout/navbar.php';
        require BASE_PATH . '/app/views/pedidos/ver.php';
        require BASE_PATH . '/app/views/layout/footer.php';

    } catch (Exception $e) {
        error_log('Error en PedidosController::ver(): ' . $e->getMessage());
        $_SESSION['error_message'] = 'Error al cargar los detalles del pedido: ' . $e->getMessage();
        header('Location: index.php?c=pedidos&a=index');
        exit;
    }
}
public function editar() {
    // Verificar si el ID del pedido es válido
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        $_SESSION['error_message'] = 'ID de pedido no válido';
        error_log('Error: ID de pedido no proporcionado o inválido: ' . ($_GET['id'] ?? ''));
        header('Location: index.php?c=pedidos&a=index');
        exit;
    }

    $pedidoId = (int)$_GET['id'];
    
    try {
        // Obtener detalles del pedido
        $sql = "SELECT pf.*, p.fecha_hora, p.equipo_a, p.equipo_b, p.categoria_id, 
                       c.nombre as categoria_nombre
                FROM pedidos_fotos pf
                JOIN partidos p ON pf.partido_id = p.id
                JOIN categorias c ON p.categoria_id = c.id
                WHERE pf.id = :id";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $pedidoId, PDO::PARAM_INT);
        $stmt->execute();
        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pedido) {
            throw new Exception('No se encontró el pedido con ID: ' . $pedidoId);
        }

        // Obtener partidos para el desplegable
        $stmt = $this->db->query("
            SELECT p.id, p.fecha_hora, p.equipo_a, p.equipo_b, c.nombre as categoria_nombre 
            FROM partidos p
            JOIN categorias c ON p.categoria_id = c.id
            WHERE p.fecha_hora >= NOW() - INTERVAL 7 DAY
            ORDER BY p.fecha_hora DESC
        ");
        $partidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Obtener precio por foto
        $precioPorFoto = 3000; // Valor por defecto
        $stmt = $this->db->query("SELECT valor FROM configuracion WHERE clave = 'precio_foto'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $precioPorFoto = (float)$result['valor'];
        }

        // Pasar los datos a la vista
        $formData = $_SESSION['form_data'] ?? $pedido;  // Usar datos del pedido o datos del formulario si hay error
        unset($_SESSION['form_data']);  // Limpiar datos de sesión

        // Pasar mensajes a la vista
        $error_message = $_SESSION['error_message'] ?? '';
        $success_message = $_SESSION['success_message'] ?? '';
        unset($_SESSION['error_message'], $_SESSION['success_message']);

        // Cargar vista
        $pageTitle = 'Editar Pedido #' . str_pad($pedido['id'], 5, '0', STR_PAD_LEFT) . ' - ' . (defined('EVENT_NAME') ? EVENT_NAME : 'Tresfronteras');
        require BASE_PATH . '/app/views/layout/header.php';
        require BASE_PATH . '/app/views/layout/navbar.php';
        require BASE_PATH . '/app/views/pedidos/form.php';
        require BASE_PATH . '/app/views/layout/footer.php';

    } catch (Exception $e) {
        error_log('Error en PedidosController::editar(): ' . $e->getMessage());
        $_SESSION['error_message'] = 'Error al cargar el pedido: ' . $e->getMessage();
        header('Location: index.php?c=pedidos&a=index');
        exit;
    }
}
public function actualizar() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php?c=pedidos&a=index');
        exit;
    }

    try {
        // Validate and get form data
        $pedidoId = $_POST['id'] ?? null;
        $partidoId = $_POST['partido_id'] ?? null;
        $nombreCliente = trim($_POST['nombre_cliente'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $cantidadFotos = (int)($_POST['cantidad_fotos'] ?? 0);
        $precioPorFoto = (float)str_replace(',', '.', $_POST['precio_por_foto'] ?? '0');
        $formaPago = $_POST['forma_pago'] ?? 'efectivo';
        $estadoPago = $_POST['estado_pago'] ?? 'no_pagado';
        $montoTotal = $cantidadFotos * $precioPorFoto;
        $archivos = $_POST['archivos'] ?? [];
        $nombresArchivos = is_array($archivos) ? implode(', ', array_map('trim', $archivos)) : '';

        // Validate required fields
        $errors = [];
        
        if (empty($partidoId)) {
            $errors[] = 'Debe seleccionar un partido';
        }
        
        if (empty($nombreCliente)) {
            $errors[] = 'El nombre del cliente es obligatorio';
        }
        
        if (empty($telefono)) {
            $errors[] = 'El teléfono es obligatorio';
        }
        
        if ($cantidadFotos <= 0) {
            $errors[] = 'La cantidad de fotos debe ser mayor a cero';
        }
        
        if ($precioPorFoto <= 0) {
            $errors[] = 'El precio por foto debe ser mayor a cero';
        }
        
        if (count($archivos) !== $cantidadFotos) {
            $errors[] = 'Debe ingresar los nombres de todos los archivos';
        }

        // If there are errors, redirect back to the form with the data
        if (!empty($errors)) {
            $_SESSION['form_data'] = $_POST;
            $_SESSION['error_message'] = implode('<br>', $errors);
            header('Location: index.php?c=pedidos&a=editar&id=' . $pedidoId);
            exit;
        }

        // Update the order in the database
        $sql = "UPDATE pedidos_fotos SET
                    partido_id = :partido_id,
                    nombre_cliente = :nombre_cliente,
                    telefono = :telefono,
                    cantidad_fotos = :cantidad_fotos,
                    forma_pago = :forma_pago,
                    estado_pago = :estado_pago,
                    monto_total = :monto_total,
                    archivos = :archivos
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':partido_id' => $partidoId,
            ':nombre_cliente' => $nombreCliente,
            ':telefono' => $telefono,
            ':cantidad_fotos' => $cantidadFotos,
            ':forma_pago' => $formaPago,
            ':estado_pago' => $estadoPago,
            ':monto_total' => $montoTotal,
            ':archivos' => $nombresArchivos,
            ':id' => $pedidoId
        ]);

        // Success - redirect to the order details
        $_SESSION['success_message'] = 'El pedido se ha actualizado correctamente.';
        header('Location: index.php?c=pedidos&a=ver&id=' . $pedidoId);
        exit;

    } catch (Exception $e) {
        // Error - save form data and error message, then redirect back to the form
        error_log('Error en PedidosController::actualizar(): ' . $e->getMessage());
        $_SESSION['form_data'] = $_POST;
        $_SESSION['error_message'] = 'Error al actualizar el pedido: ' . $e->getMessage();
        header('Location: index.php?c=pedidos&a=editar&id=' . ($_POST['id'] ?? ''));
        exit;
    }
}
/**
 * Elimina un pedido existente
 * URL: index.php?c=pedidos&a=eliminar&id=X
 */
public function eliminar()
{
    // Verificar que la solicitud sea GET
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        $_SESSION['error_message'] = 'Método no permitido';
        header('Location: index.php?c=pedidos&a=index');
        exit;
    }

    // Validar el ID
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$id) {
        $_SESSION['error_message'] = 'ID de pedido no válido';
        header('Location: index.php?c=pedidos&a=index');
        exit;
    }

    try {
        // Iniciar transacción para asegurar la integridad de los datos
        $this->db->beginTransaction();

        // 1. Primero, verificar si el pedido existe
        $stmt = $this->db->prepare("SELECT id FROM pedidos_fotos WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() === 0) {
            throw new Exception('El pedido no existe o ya ha sido eliminado');
        }

        // 2. Aquí podrías agregar lógica adicional, como eliminar archivos asociados
        // Por ejemplo, si los archivos se almacenan en el sistema de archivos:
        // $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
        // if (file_exists($rutaArchivos . $pedido['archivo'])) {
        //     unlink($rutaArchivos . $pedido['archivo']);
        // }

        // 3. Eliminar el pedido de la base de datos
        $stmt = $this->db->prepare("DELETE FROM pedidos_fotos WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Confirmar la transacción
        $this->db->commit();

        $_SESSION['success_message'] = 'El pedido ha sido eliminado correctamente';
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }
        
        error_log('Error al eliminar el pedido: ' . $e->getMessage());
        $_SESSION['error_message'] = 'Error al eliminar el pedido: ' . $e->getMessage();
    }

    // Redirigir de vuelta al listado
    header('Location: index.php?c=pedidos&a=index');
    exit;
}
}