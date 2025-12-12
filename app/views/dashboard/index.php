<?php
// app/views/dashboard/index.php

declare(strict_types=1);

// Valores por defecto por si algo viene sin setear
$totalPartidos             = $totalPartidos             ?? 0;
$totalPedidos              = $totalPedidos              ?? 0;
$recaudacionTotal          = $recaudacionTotal          ?? 0.0;
$recaudacionEfectivo       = $recaudacionEfectivo       ?? 0.0;
$recaudacionTransferencia  = $recaudacionTransferencia  ?? 0.0;

$currency = defined('CURRENCY') ? CURRENCY : 'ARS';

function format_money(float $value): string {
    return number_format($value, 2, ',', '.');
}
?>

<section class="mb-4">
    <h2 class="h5 mb-3">Resumen general</h2>

    <div class="row g-3">
        <div class="col-12 col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h3 class="h6 text-muted mb-1">Partidos cargados</h3>
                    <p class="display-6 mb-0 fw-semibold"><?= (int) $totalPartidos ?></p>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h3 class="h6 text-muted mb-1">Pedidos de fotos</h3>
                    <p class="display-6 mb-0 fw-semibold"><?= (int) $totalPedidos ?></p>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h3 class="h6 text-muted mb-1">Recaudación total</h3>
                    <p class="h4 mb-0 fw-semibold">
                        <?= htmlspecialchars($currency, ENT_QUOTES, 'UTF-8') ?>
                        <?= format_money($recaudacionTotal) ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h3 class="h6 text-muted mb-1">Detalle por pago</h3>
                    <p class="mb-1 small">
                        <span class="badge bg-success me-1">Efectivo</span>
                        <?= htmlspecialchars($currency, ENT_QUOTES, 'UTF-8') ?>
                        <?= format_money($recaudacionEfectivo) ?>
                    </p>
                    <p class="mb-0 small">
                        <span class="badge bg-primary me-1">Transferencia</span>
                        <?= htmlspecialchars($currency, ENT_QUOTES, 'UTF-8') ?>
                        <?= format_money($recaudacionTransferencia) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<section>
    <h2 class="h6 text-muted mb-2">Accesos rápidos</h2>
    <div class="d-flex flex-wrap gap-2">
        <a
            href="<?= htmlspecialchars(base_url('index.php?c=partidos&a=index'), ENT_QUOTES, 'UTF-8') ?>"
            class="btn btn-sm btn-outline-primary"
        >
            Gestionar fixture de partidos
        </a>

        <a
            href="<?= htmlspecialchars(base_url('index.php?c=pedidos&a=index'), ENT_QUOTES, 'UTF-8') ?>"
            class="btn btn-sm btn-outline-success"
        >
            Ver pedidos de fotos
        </a>

        <a
            href="<?= htmlspecialchars(base_url('index.php?c=recaudacion&a=index'), ENT_QUOTES, 'UTF-8') ?>"
            class="btn btn-sm btn-outline-dark"
        >
            Ver recaudación
        </a>
    </div>
</section>
