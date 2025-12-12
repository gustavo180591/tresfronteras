<?php
// app/controllers/RecaudacionController.php

declare(strict_types=1);

class RecaudacionController
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
        try {
            // Obtener resumen de recaudación por categoría
            $sql = "SELECT 
                        c.nombre as categoria,
                        COUNT(pf.id) as total_pedidos,
                        SUM(CASE WHEN pf.estado_pago = 'pagado' THEN 1 ELSE 0 END) as pagados,
                        SUM(CASE WHEN pf.estado_pago = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                        SUM(pf.monto_total) as monto_total,
                        SUM(CASE WHEN pf.estado_pago = 'pagado' THEN pf.monto_total ELSE 0 END) as monto_pagado,
                        SUM(CASE WHEN pf.estado_pago = 'pendiente' THEN pf.monto_total ELSE 0 END) as monto_pendiente
                    FROM categorias c
                    LEFT JOIN partidos p ON c.id = p.categoria_id
                    LEFT JOIN pedidos_fotos pf ON p.id = pf.partido_id
                    GROUP BY c.id, c.nombre
                    ORDER BY c.nombre";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $resumenCategorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Obtener resumen general
            $sql = "SELECT 
                        COUNT(*) as total_pedidos,
                        SUM(CASE WHEN estado_pago = 'pagado' THEN 1 ELSE 0 END) as total_pagados,
                        SUM(CASE WHEN estado_pago = 'pendiente' THEN 1 ELSE 0 END) as total_pendientes,
                        SUM(monto_total) as monto_total,
                        SUM(CASE WHEN estado_pago = 'pagado' THEN monto_total ELSE 0 END) as monto_total_pagado,
                        SUM(CASE WHEN estado_pago = 'pendiente' THEN monto_total ELSE 0 END) as monto_total_pendiente
                    FROM pedidos_fotos";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $resumenGeneral = $stmt->fetch(PDO::FETCH_ASSOC);

            // Obtener últimos pedidos
            $sql = "SELECT pf.*, p.fecha_hora, p.equipo_a, p.equipo_b, c.nombre as categoria_nombre
                    FROM pedidos_fotos pf
                    JOIN partidos p ON pf.partido_id = p.id
                    JOIN categorias c ON p.categoria_id = c.id
                    ORDER BY pf.fecha_pedido DESC
                    LIMIT 10";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $ultimosPedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Cargar la vista
            $viewPath = BASE_PATH . '/app/views/recaudacion/index.php';
            if (!file_exists($viewPath)) {
                throw new Exception("La vista no existe: $viewPath");
            }

            require_once $viewPath;

        } catch (Exception $e) {
            error_log('Error en RecaudacionController: ' . $e->getMessage());
            $_SESSION['error_message'] = 'Error al cargar la información de recaudación';
            header('Location: index.php?c=dashboard');
            exit;
        }
    }
}
?>