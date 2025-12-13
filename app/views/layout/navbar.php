<?php
// app/views/layout/navbar.php

declare(strict_types=1);

// Determinar sección activa según el parámetro ?c=
$currentController = isset($_GET['c']) && $_GET['c'] !== ''
    ? strtolower((string) $_GET['c'])
    : 'dashboard';
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light mb-3">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand me-4" href="<?= htmlspecialchars(base_url('index.php'), ENT_QUOTES, 'UTF-8') ?>">
            <img src="<?= htmlspecialchars(base_url('assets/cdn.png'), ENT_QUOTES, 'UTF-8') ?>" alt="Logo" height="40" class="d-inline-block align-text-top">
        </a>
        
        <!-- Navigation Links -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item me-2">
            <a
                class="nav-link <?= $currentController === 'dashboard' ? 'active' : '' ?>"
                href="<?= htmlspecialchars(base_url('index.php'), ENT_QUOTES, 'UTF-8') ?>"
            >
                Dashboard
            </a>
                </li>
                <li class="nav-item me-2">
            <a
                class="nav-link <?= $currentController === 'partidos' ? 'active' : '' ?>"
                href="<?= htmlspecialchars(base_url('index.php?c=partidos&a=index'), ENT_QUOTES, 'UTF-8') ?>"
            >
                Fixture de partidos
            </a>
                </li>
                <li class="nav-item me-2">
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
        </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
