<?php
// config/database.php

declare(strict_types=1);

/**
 * Configuración de la conexión a la base de datos MySQL.
 * Ajustá estos valores según tu entorno local.
 */
const DB_HOST = '127.0.0.1';
const DB_PORT = 3306;
const DB_NAME = 'tresfronteras';
const DB_USER = 'root';
const DB_PASS = '';

/**
 * Devuelve una instancia de PDO lista para usar.
 *
 * Uso típico en modelos:
 *   require_once BASE_PATH . '/config/database.php';
 *   $pdo = getPDO();
 */
function getPDO(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
        DB_HOST,
        DB_PORT,
        DB_NAME
    );

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        // En entorno de evento/local conviene ver el error claramente
        http_response_code(500);
        echo '<h1>Error de conexión a la base de datos</h1>';
        echo '<p>Verificá config/database.php</p>';
        echo '<pre>' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</pre>';
        exit;
    }

    return $pdo;
}
