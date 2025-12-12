<?php
// public/index.php

declare(strict_types=1);

// Mostrar errores en entorno local (puedes desactivar esto en producción)
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Ruta base del proyecto (un nivel arriba de /public)
define('BASE_PATH', dirname(__DIR__));

// Cargar configuración general (aunque esté vacía por ahora)
require_once BASE_PATH . '/config/config.php';

// Autoload muy simple para controllers y models
spl_autoload_register(function (string $className): void {
    $paths = [
        BASE_PATH . '/app/controllers/' . $className . '.php',
        BASE_PATH . '/app/models/' . $className . '.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Resolución simple de ruta usando parámetros ?c=...&a=...
// Ejemplos:
//   /public/index.php          → DashboardController@index
//   /public/index.php?c=partidos&a=index  → PartidosController@index
//   /public/index.php?c=pedidos&a=crear   → PedidosController@crear

$controllerParam = isset($_GET['c']) ? trim($_GET['c']) : 'dashboard';
$actionParam     = isset($_GET['a']) ? trim($_GET['a']) : 'index';

// Normalizamos el nombre del controlador (DashboardController, PartidosController, etc.)
$controllerName = ucfirst(strtolower($controllerParam)) . 'Controller';
$actionName     = $actionParam !== '' ? $actionParam : 'index';

// Verificar existencia del controlador
if (!class_exists($controllerName)) {
    http_response_code(404);
    echo "<h1>404 - Controlador no encontrado</h1>";
    echo "<p>Controlador: <strong>{$controllerName}</strong></p>";
    exit;
}

// Instanciar controlador
$controller = new $controllerName();

// Verificar existencia del método/acción
if (!method_exists($controller, $actionName)) {
    http_response_code(404);
    echo "<h1>404 - Acción no encontrada</h1>";
    echo "<p>Acción: <strong>{$actionName}</strong> en controlador <strong>{$controllerName}</strong></p>";
    exit;
}

// Ejecutar acción
$controller->{$actionName}();
