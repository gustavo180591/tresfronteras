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
    
    /**
     * Genera y descarga el comprobante PDF
     */
    public function generarComprobante() {
        try {
            // Verificar si se proporcionó un ID de pedido
            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                throw new Exception('ID de pedido no válido');
            }
            
            $pedidoId = (int)$_GET['id'];
            
            // Generar el PDF (esto guardará el archivo en el servidor)
            $pdfPath = $this->generarComprobantePDF($pedidoId);
            
            if (!$pdfPath) {
                throw new Exception('No se pudo generar el comprobante');
            }
            
            // Forzar la descarga del archivo
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . basename($pdfPath) . '"');
            header('Content-Length: ' . filesize($pdfPath));
            readfile($pdfPath);
            
            // Opcional: eliminar el archivo después de la descarga
            // unlink($pdfPath);
            
            exit;
            
        } catch (Exception $e) {
            error_log('Error en PedidosController::generarComprobante: ' . $e->getMessage());
            $_SESSION['error_message'] = 'Error al generar el comprobante: ' . $e->getMessage();
            header('Location: index.php?c=pedidos&a=index');
            exit;
        }
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
        
        // Convertir la cadena de archivos en un array
        if (!empty($pedido['archivos'])) {
            $pedido['archivos'] = array_map('trim', explode(',', $pedido['archivos']));
        } else {
            $pedido['archivos'] = [];
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

        // Generate PDF receipt
        $pdfPath = $this->generarComprobantePDF($pedidoId);
        
        if ($pdfPath) {
            // If PDF was generated successfully, store the path in session
            $_SESSION['pdf_receipt_path'] = $pdfPath;
            $_SESSION['success_message'] = 'El pedido se ha actualizado correctamente. <a href="' . $pdfPath . '" target="_blank" class="alert-link">Descargar comprobante</a>';
        } else {
            $_SESSION['success_message'] = 'El pedido se ha actualizado correctamente.';
        }
        
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

    /**
     * Genera un comprobante PDF para un pedido
     */
    private function generarComprobantePDF($pedidoId) {
    try {
        // Obtener los datos del pedido
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
            throw new Exception('No se encontró el pedido con ID: ' . $pedidoId);
        }

        // Calcular precio unitario (monto_total / cantidad_fotos)
        $precioUnitario = 0;
        if ((int)$pedido['cantidad_fotos'] > 0) {
            $precioUnitario = (float)$pedido['monto_total'] / (int)$pedido['cantidad_fotos'];
        }

        // Incluir TCPDF
        require_once(BASE_PATH . '/vendor/tecnickcom/tcpdf/tcpdf.php');

        // Crear nuevo documento PDF tamaño ticket (A7)
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A7', true, 'UTF-8', false);

        // Configuración del documento
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Torneo Tres Fronteras');
        $pdf->SetTitle('Comprobante de Pedido #' . $pedido['id']);
        $pdf->SetSubject('Comprobante de Pedido');

        // Sin cabecera ni pie automáticos
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Márgenes pequeños
        $pdf->SetMargins(5, 5, 5);
        $pdf->SetAutoPageBreak(false);

        // Añadir una página
        $pdf->AddPage();

        // HTML del comprobante (1 página)
        $html = '
        <style>
            body { font-family: helvetica; font-size: 8pt; line-height: 1.2; }
            .header { text-align: center; margin-bottom: 4px; }
            .title { font-size: 12px; font-weight: bold; margin: 0; text-transform: uppercase; }
            .subtitle { font-size: 9px; color: #555; margin: 1px 0; }
            .divider { border-top: 1px dashed #999; margin: 4px 0; }
            .row { overflow: hidden; margin-bottom: 2px; }
            .label { font-weight: bold; float: left; width: 40%; }
            .value { float: left; width: 60%; }
            .total { 
                font-size: 11px;
                font-weight: bold;
                text-align: right;
                margin-top: 6px;
                padding-top: 4px;
                border-top: 1px dashed #000;
            }
            .status {
                text-align: center;
                margin: 5px 0;
                padding: 4px;
                background-color: ' . ($pedido['estado_pago'] === 'pagado' ? '#d4edda' : '#f8d7da') . ';
                color: ' . ($pedido['estado_pago'] === 'pagado' ? '#155724' : '#721c24') . ';
                font-weight: bold;
                border-radius: 3px;
                font-size: 8pt;
            }
            .info-footer {
                text-align: center;
                margin-top: 6px;
                font-size: 7pt;
                color: #777;
            }
            .small-note {
                font-size: 6.5pt;
                text-align: center;
                margin-top: 4px;
                color: #555;
            }
            .clear { clear: both; }
        </style>

        <div class="header">
            <div class="title">TORNEO TRES FRONTERAS</div>
            <div class="subtitle">COMPROBANTE DE PEDIDO DE FOTOS</div>
            <div class="subtitle">Nº ' . str_pad($pedido['id'], 5, '0', STR_PAD_LEFT) . '</div>
        </div>

        <div class="divider"></div>

        <!-- Datos del Cliente -->
        <div class="row">
            <div class="label">Cliente:</div>
            <div class="value">' . htmlspecialchars($pedido['nombre_cliente']) . '</div>
        </div>
        <div class="row">
            <div class="label">Teléfono:</div>
            <div class="value">' . htmlspecialchars($pedido['telefono']) . '</div>
        </div>
        <div class="row">
            <div class="label">Fecha pedido:</div>
            <div class="value">' . date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])) . '</div>
        </div>

        <div class="divider"></div>

        <!-- Detalles del Partido -->
        <div class="row">
            <div class="label">Categoría:</div>
            <div class="value">' . htmlspecialchars($pedido['categoria_nombre']) . '</div>
        </div>
        <div class="row">
            <div class="label">Partido:</div>
            <div class="value">' . htmlspecialchars($pedido['equipo_a'] . ' vs ' . $pedido['equipo_b']) . '</div>
        </div>
        <div class="row">
            <div class="label">Fecha partido:</div>
            <div class="value">' . date('d/m/Y H:i', strtotime($pedido['fecha_hora'])) . '</div>
        </div>

        <div class="divider"></div>

        <!-- Detalles del Pedido -->
        <div class="row">
            <div class="label">Cant. fotos:</div>
            <div class="value">' . (int)$pedido['cantidad_fotos'] . ' x $' . number_format($precioUnitario, 2, ',', '.') . '</div>
        </div>
        <div class="row">
            <div class="label">Forma de pago:</div>
            <div class="value">' . ucfirst($pedido['forma_pago']) . '</div>
        </div>
        <div class="row">
            <div class="label">Estado pago:</div>
            <div class="value">
                <span style="color: ' . ($pedido['estado_pago'] === 'pagado' ? 'green' : 'red') . '; font-weight: bold;">
                    ' . ($pedido['estado_pago'] === 'pagado' ? 'PAGADO' : 'PENDIENTE DE PAGO') . '
                </span>
            </div>
        </div>

        <div class="total">
            TOTAL: $' . number_format($pedido['monto_total'], 2, ',', '.') . '
        </div>

        <div class="status">
            ' . ($pedido['estado_pago'] === 'pagado' ? 'PAGO REGISTRADO' : 'PAGO PENDIENTE') . '
        </div>

        <div class="small-note">
            Comprobante de pedido de fotos.<br/>
            No válido como factura oficial.
        </div>

        <div class="info-footer">
            Generado el ' . date('d/m/Y H:i') . '<br/>
            Tres Fronteras - Organización del torneo
        </div>
        ';

        // Escribir el contenido HTML (una sola página)
        $pdf->writeHTML($html, true, false, true, false, '');

        // Generar un nombre de archivo único
        $filename = 'comprobante_pedido_' . $pedidoId . '_' . date('YmdHis') . '.pdf';
        $filepath = BASE_PATH . '/public/comprobantes/' . $filename;
        
        // Crear el directorio si no existe
        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        // Guardar el archivo en el servidor
        $pdf->Output($filepath, 'F');
        
        // Retornar la ruta WEB del archivo generado
        return '/comprobantes/' . $filename;
        
    } catch (Exception $e) {
        error_log('Error al generar el comprobante PDF: ' . $e->getMessage());
        return false;
    }
}}