<?php
// app/controllers/DashboardController.php

declare(strict_types=1);

class DashboardController
{
    private PDO $db;

    public function __construct()
    {
        // Cargamos la conexión PDO
        require_once BASE_PATH . '/config/database.php';
        $this->db = getPDO();
    }

    public function index(): void
    {
        // Totales de partidos
        $stmt = $this->db->query('SELECT COUNT(*) AS total FROM partidos');
        $totalPartidos = (int) $stmt->fetch()['total'];

        // Totales de pedidos
        $stmt = $this->db->query('SELECT COUNT(*) AS total FROM pedidos_fotos');
        $totalPedidos = (int) $stmt->fetch()['total'];

        // Recaudación total
        $stmt = $this->db->query("
            SELECT 
                COALESCE(SUM(monto_total), 0) AS total
            FROM pedidos_fotos
            WHERE estado_pago = 'pagado'
        ");
        $recaudacionTotal = (float) $stmt->fetch()['total'];

        // Recaudación por forma de pago: efectivo
        $stmt = $this->db->query("
            SELECT 
                COALESCE(SUM(monto_total), 0) AS total
            FROM pedidos_fotos
            WHERE estado_pago = 'pagado'
              AND forma_pago = 'efectivo'
        ");
        $recaudacionEfectivo = (float) $stmt->fetch()['total'];

        // Recaudación por forma de pago: transferencia
        $stmt = $this->db->query("
            SELECT 
                COALESCE(SUM(monto_total), 0) AS total
            FROM pedidos_fotos
            WHERE estado_pago = 'pagado'
              AND forma_pago = 'transferencia'
        ");
        $recaudacionTransferencia = (float) $stmt->fetch()['total'];

        // Datos que vamos a mandar a la vista del dashboard
        $data = [
            'totalPartidos'          => $totalPartidos,
            'totalPedidos'           => $totalPedidos,
            'recaudacionTotal'       => $recaudacionTotal,
            'recaudacionEfectivo'    => $recaudacionEfectivo,
            'recaudacionTransferencia' => $recaudacionTransferencia,
        ];

        // Hacemos disponibles las variables en la vista
        extract($data);

        // Render del layout + vista del dashboard
        require BASE_PATH . '/app/views/layout/header.php';
        require BASE_PATH . '/app/views/layout/navbar.php';
        require BASE_PATH . '/app/views/dashboard/index.php';
        require BASE_PATH . '/app/views/layout/footer.php';
    }
}
