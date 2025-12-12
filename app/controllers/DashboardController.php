<?php
// app/controllers/DashboardController.php

class DashboardController
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

    public function index()
    {
        // Totales de partidos
        $stmt = $this->db->query('SELECT COUNT(*) AS total FROM partidos');
        $row = $stmt->fetch();
        $totalPartidos = isset($row['total']) ? (int) $row['total'] : 0;

        // Totales de pedidos
        $stmt = $this->db->query('SELECT COUNT(*) AS total FROM pedidos_fotos');
        $row = $stmt->fetch();
        $totalPedidos = isset($row['total']) ? (int) $row['total'] : 0;

        // Recaudación total
        $stmt = $this->db->query("
            SELECT 
                COALESCE(SUM(monto_total), 0) AS total
            FROM pedidos_fotos
            WHERE estado_pago = 'pagado'
        ");
        $row = $stmt->fetch();
        $recaudacionTotal = isset($row['total']) ? (float) $row['total'] : 0.0;

        // Recaudación efectivo
        $stmt = $this->db->query("
            SELECT 
                COALESCE(SUM(monto_total), 0) AS total
            FROM pedidos_fotos
            WHERE estado_pago = 'pagado'
              AND forma_pago = 'efectivo'
        ");
        $row = $stmt->fetch();
        $recaudacionEfectivo = isset($row['total']) ? (float) $row['total'] : 0.0;

        // Recaudación transferencia
        $stmt = $this->db->query("
            SELECT 
                COALESCE(SUM(monto_total), 0) AS total
            FROM pedidos_fotos
            WHERE estado_pago = 'pagado'
              AND forma_pago = 'transferencia'
        ");
        $row = $stmt->fetch();
        $recaudacionTransferencia = isset($row['total']) ? (float) $row['total'] : 0.0;

        $data = array(
            'totalPartidos'            => $totalPartidos,
            'totalPedidos'             => $totalPedidos,
            'recaudacionTotal'         => $recaudacionTotal,
            'recaudacionEfectivo'      => $recaudacionEfectivo,
            'recaudacionTransferencia' => $recaudacionTransferencia,
        );

        extract($data);

        $pageTitle = 'Dashboard - ' . (defined('EVENT_NAME') ? EVENT_NAME : 'Tresfronteras');

        require BASE_PATH . '/app/views/layout/header.php';
        require BASE_PATH . '/app/views/layout/navbar.php';
        require BASE_PATH . '/app/views/dashboard/index.php';
        require BASE_PATH . '/app/views/layout/footer.php';
    }
}
