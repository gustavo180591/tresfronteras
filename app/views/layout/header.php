<?php
// app/views/layout/header.php

declare(strict_types=1);

// Título por defecto si no se define otro
$pageTitle = $pageTitle ?? (defined('EVENT_NAME') ? EVENT_NAME . ' - Panel' : 'TORNEO TRES FRONTERAS - Panel');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS (para diseño rápido y responsive) -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    >

    <!-- Estilos propios -->
    <link rel="stylesheet" href="<?= htmlspecialchars(base_url('css/style.css'), ENT_QUOTES, 'UTF-8') ?>">
</head>
<body class="bg-light">

<header class="border-bottom bg-white shadow-sm mb-3">
    <div class="container-fluid py-2 px-3">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
            <!-- Título del sistema / evento -->
            <div class="d-flex align-items-center gap-2">
                <!-- Logo opcional: usarás public/assets/logo.png cuando lo tengas -->
                <div class="me-2">
                    <img src="<?= base_url('assets/logo.png') ?>" alt="Logo" style="height: 40px; width: auto;">
                </div>
                <div>
                    <h1 class="h4 mb-0">
                        TORNEO TRES FRONTERAS
                    </h1>
                    <small class="text-muted">Panel de control · Fixture · Fotos · Recaudación</small>
                </div>
            </div>

            <!-- Barra de búsqueda global -->
            <form
                class="d-flex align-items-center gap-2"
                method="get"
                action="<?= htmlspecialchars(base_url('index.php'), ENT_QUOTES, 'UTF-8') ?>"
            >
                <!--
                    NOTA:
                    - En esta primera versión, sólo mandamos el parámetro 'global_search'.
                    - Más adelante, desde los controladores (por ejemplo Pedidos/Partidos),
                      podremos leer $_GET['global_search'] para buscar por:
                        * número de pedido
                        * nombre de la persona
                        * nombre de equipo
                -->
                <input
                    type="text"
                    name="global_search"
                    class="form-control form-control-sm"
                    placeholder="Buscar por N° pedido, nombre o equipo..."
                    value="<?= isset($_GET['global_search']) ? htmlspecialchars((string) $_GET['global_search'], ENT_QUOTES, 'UTF-8') : '' ?>"
                >
                <button class="btn btn-sm btn-primary" type="submit">
                    Buscar
                </button>
            </form>
        </div>
    </div>
</header>

<main class="container-fluid mb-4">
