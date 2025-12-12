<?php
// app/views/layout/navbar.php

declare(strict_types=1);

// Determinar sección activa según el parámetro ?c=
$currentController = isset($_GET['c']) && $_GET['c'] !== ''
    ? strtolower((string) $_GET['c'])
    : 'dashboard';
?>

<nav class="mb-3">
    <ul class="nav nav-pills small">
        <li class="nav-item">
            <a
                class="nav-link <?= $currentController === 'dashboard' ? 'active' : '' ?>"
                href="<?= htmlspecialchars(base_url('index.php'), ENT_QUOTES, 'UTF-8') ?>"
            >
                Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a
                class="nav-link <?= $currentController === 'partidos' ? 'active' : '' ?>"
                href="<?= htmlspecialchars(base_url('index.php?c=partidos&a=index'), ENT_QUOTES, 'UTF-8') ?>"
            >
                Fixture de partidos
            </a>
        </li>

        <li class="nav-item">
            <a
                class="nav-link <?= $currentController === 'pedidos' ? 'active' : '' ?>"
                href="<?= htmlspecialchars(base_url('index.php?c=pedidos&a=index'), ENT_QUOTES, 'UTF-8') ?>"
            >
                Pedidos de fotos
            </a>
        </li>

        <li class="nav-item">
            <a
                class="nav-link <?= $currentController === 'recaudacion' ? 'active' : '' ?>"
                href="<?= htmlspecialchars(base_url('index.php?c=recaudacion&a=index'), ENT_QUOTES, 'UTF-8') ?>"
            >
                Recaudación
            </a>
        </li>
    </ul>
</nav>
