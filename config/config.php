<?php
// config/config.php

declare(strict_types=1);

/**
 * Configuración general de la aplicación Tresfronteras.
 * Todo lo "ajustable" debería salir de acá.
 */

// Nombre del evento
const EVENT_NAME = 'Tresfronteras';

// Moneda usada en la recaudación
const CURRENCY = 'ARS';

// Precio unitario por foto (puede usarse como valor por defecto,
// aunque más adelante también lo guardemos en la tabla configuracion)
const DEFAULT_PHOTO_PRICE = 500.00;

// Paginación: cantidad de registros a mostrar por página
const ITEMS_PER_PAGE = 20;

// Zona horaria por defecto
date_default_timezone_set('America/Argentina/Cordoba');

/**
 * Devuelve la URL base de la aplicación, hasta la carpeta /public.
 *
 * Ejemplo: http://localhost/tresfronteras/public
 *
 * Esto es útil para armar enlaces en las vistas:
 *   <a href="<?= base_url('?c=pedidos&a=index') ?>">Pedidos</a>
 */
function base_url(string $path = ''): string
{
    // Detectamos protocolo
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

    // Host (localhost, 192.168.x.x, etc.)
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    // Directorio del script actual (normalmente /tresfronteras/public)
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $dir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

    $base = "{$scheme}://{$host}{$dir}";

    if ($path === '') {
        return $base;
    }

    // Aseguramos que el path comience con /
    if ($path[0] !== '?'
        && $path[0] !== '/'
    ) {
        $path = '/' . $path;
    }

    return $base . $path;
}
