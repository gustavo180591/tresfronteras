<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/config/config.php';
require BASE_PATH . '/config/database.php';

echo "<pre>";

try {
    $pdo = getPDO();
    echo "OK CONEXIÓN A tresfronteras\n\n";

    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tablas:\n";
    print_r($tables);
} catch (Throwable $e) {
    echo "ERROR EN CONEXIÓN O CONSULTA:\n";
    echo $e->getMessage() . "\n";
}

echo "</pre>";

