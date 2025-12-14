<?php
// app/views/recaudacion/index.php
declare(strict_types=1);

// Función para formatear moneda
function formatCurrency($amount) {
    return '$' . number_format((float)$amount, 2, ',', '.');
}

// Variables para el breadcrumb
$pageTitle = 'Gestión de Recaudación';
$breadcrumbs = [
    ['url' => 'index.php?c=dashboard', 'label' => 'Inicio'],
    ['label' => 'Recaudación']
];

// Incluir el header
require_once BASE_PATH . '/app/views/layout/header.php';
?>

<style>
    /* Estilos específicos para la página de recaudación */
    .stat-card {
        border-left: 4px solid #4e73df;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .stat-card .card-body {
        padding: 1.25rem;
    }
    
    .stat-card .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2c3e50;
    }
    
    .stat-card .stat-label {
        font-size: 0.9rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .stat-card .stat-icon {
        font-size: 2rem;
        color: #b0b0b0;
    }
    
    .progress {
        height: 0.5rem;
        border-radius: 0.25rem;
    }
    
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.5px;
        color: #6c757d;
        border-top: none;
        padding: 1rem 1.5rem;
    }
    
    .table td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
    
    .stat-card {
        border-left: 4px solid;
        transition: all 0.3s;
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
    }
    
    .stat-card .card-body {
        padding: 1.25rem;
    }
    
    .stat-card h6 {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 0.5rem;
        color: #858796;
    }
    
    .stat-card h4 {
        font-weight: 700;
        margin: 0;
    }
    
    .stat-card .text-success {
        color: var(--success-color) !important;
    }
    
    .stat-card .text-warning {
        color: var(--warning-color) !important;
    }
    
    .stat-card .text-primary {
        color: var(--primary-color) !important;
    }
    
    .table th {
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.1em;
        color: #858796;
        border-top: none;
        padding: 1rem 1.5rem;
    }
    
    .table td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        border-top: 1px solid #e3e6f0;
    }
    
    .progress {
        height: 0.8rem;
        border-radius: 1rem;
        background-color: #eaecf4;
    }
    
    .progress-bar {
        border-radius: 1rem;
    }
    
    .avatar-sm {
        width: 2.5rem;
        height: 2.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background-color: #f8f9fc;
        color: var(--primary-color);
        font-weight: 700;
        font-size: 1.1rem;
    }
    
    .badge {
        font-weight: 600;
        padding: 0.4em 0.8em;
        font-size: 0.75rem;
        border-radius: 0.35rem;
    }
    
    .bg-light-primary {
        background-color: rgba(78, 115, 223, 0.1) !important;
        color: var(--primary-color) !important;
    }
    
    .bg-light-success {
        background-color: rgba(28, 200, 138, 0.1) !important;
        color: var(--success-color) !important;
    }
    
    .bg-light-warning {
        background-color: rgba(246, 194, 62, 0.1) !important;
        color: var(--warning-color) !important;
    }
    
    .section-title {
        position: relative;
        padding-bottom: 0.5rem;
        margin-bottom: 1.5rem;
    }
    
    .section-title:after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 50px;
        height: 3px;
        background: var(--primary-color);
        border-radius: 3px;
    }
    
    .btn-outline-primary {
        border-color: var(--primary-color);
        color: var(--primary-color);
    }
    
    .btn-outline-primary:hover {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .table-hover tbody tr:hover {
        background-color: #f8f9fc;
    }
    
    .text-muted {
        color: #858796 !important;
    }
</style>

<div class="container-fluid">
    <!-- Encabezado -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestión de Recaudación</h1>
        <a href="index.php?c=pedidos&a=index" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Volver a Pedidos
        </a>
    </div>
    
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <?php foreach ($breadcrumbs as $index => $item): ?>
                <?php if (isset($item['url'])): ?>
                    <li class="breadcrumb-item"><a href="<?= $item['url'] ?>"><?= $item['label'] ?></a></li>
                <?php else: ?>
                    <li class="breadcrumb-item active" aria-current="page"><?= $item['label'] ?></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ol>
    </nav>

    <!-- Resumen General -->
    <div class="row">
        <!-- Total Recaudado -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Recaudado</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= formatCurrency($resumenGeneral['monto_total_pagado'] ?? 0) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Pendiente -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Pendiente</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= formatCurrency($resumenGeneral['monto_total'] - ($resumenGeneral['monto_total_pagado'] ?? 0)) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total General -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total General</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= formatCurrency($resumenGeneral['monto_total'] ?? 0) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Porcentaje Recaudado -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                % Recaudado</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?= round(($resumenGeneral['monto_total_pagado'] ?? 0) / ($resumenGeneral['monto_total'] ?? 1) * 100) ?>%</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-success" style="width: <?= round(($resumenGeneral['monto_total_pagado'] ?? 0) / ($resumenGeneral['monto_total'] ?? 1) * 100) ?>%" 
                                             role="progressbar" 
                                             aria-valuenow="<?= round(($resumenGeneral['monto_total_pagado'] ?? 0) / ($resumenGeneral['monto_total'] ?? 1) * 100) ?>" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percent fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recaudación por Categoría -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list-alt fa-fw me-2"></i>Recaudación por Categoría
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th>Categoría</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-end">Pagado</th>
                                    <th class="text-end">Pendiente</th>
                                    <th class="text-center">% Cobrado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($resumenCategorias)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="py-4">
                                                <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                                                <p class="text-muted mb-0">No hay datos de recaudación disponibles</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($resumenCategorias as $categoria): ?>
                                        <?php
                                        $porcentaje = $categoria['monto_total'] > 0 
                                            ? round(($categoria['monto_pagado'] / $categoria['monto_total']) * 100) 
                                            : 0;
                                        $progressClass = $porcentaje >= 75 ? 'bg-success' : ($porcentaje >= 50 ? 'bg-primary' : ($porcentaje >= 25 ? 'bg-info' : 'bg-warning'));
                                        ?>
                                        <tr>
                                            <td class="font-weight-bold">
                                                <i class="fas fa-folder text-primary me-2"></i>
                                                <?= htmlspecialchars($categoria['categoria']) ?>
                                            </td>
                                            <td class="text-end font-weight-bold"><?= formatCurrency($categoria['monto_total'] ?? 0) ?></td>
                                            <td class="text-end text-success font-weight-bold"><?= formatCurrency($categoria['monto_pagado'] ?? 0) ?></td>
                                            <td class="text-end text-warning font-weight-bold"><?= formatCurrency($categoria['monto_pendiente'] ?? 0) ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 me-2">
                                                        <div 
                                                            class="progress-bar <?= $progressClass ?>" 
                                                            role="progressbar" 
                                                            style="width: <?= $porcentaje ?>%"
                                                            aria-valuenow="<?= $porcentaje ?>" 
                                                            aria-valuemin="0" 
                                                            aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <span class="font-weight-bold" style="min-width: 40px;"><?= $porcentaje ?>%</span>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Últimos Pedidos -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history fa-fw me-2"></i>Últimos Pedidos
                    </h6>
                    <a href="index.php?c=pedidos&a=index" class="btn btn-sm btn-primary">
                        <i class="fas fa-list fa-sm text-white-50"></i> Ver todos
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th># Pedido</th>
                                    <th>Cliente</th>
                                    <th>Partido</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center">Pago</th>
                                    <th class="text-center">Entrega</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($ultimosPedidos)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="py-4">
                                                <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                                                <p class="text-muted mb-0">No hay pedidos recientes</p>
                                            </div>
                                        </td>
                                    </tr>
                            <?php else: ?>
                                    <?php foreach ($ultimosPedidos as $pedido): 
                                        $fechaPedido = new DateTime($pedido['fecha_pedido']);
                                        $hoy = new DateTime();
                                        $diferencia = $hoy->diff($fechaPedido);
                                        $esNuevo = $diferencia->days < 1 && $hoy->format('Y-m-d') === $fechaPedido->format('Y-m-d');
                                    ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="font-weight-bold">#<?= str_pad((string)$pedido['id'], 5, '0', STR_PAD_LEFT) ?></span>
                                                    <?php if ($esNuevo): ?>
                                                        <span class="badge badge-danger ml-2">Nuevo</span>
                                                    <?php endif; ?>
                                                </div>
                                                <small class="text-muted"><?= date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])) ?></small>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img class="rounded-circle me-2" src="https://ui-avatars.com/api/?name=<?= urlencode($pedido['nombre_cliente']) ?>&background=4e73df&color=fff&size=32" alt="Cliente" width="32" height="32">
                                                    <div>
                                                        <div class="font-weight-bold"><?= htmlspecialchars($pedido['nombre_cliente']) ?></div>
                                                        <small class="text-muted"><?= htmlspecialchars($pedido['telefono']) ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-light rounded p-2 me-2">
                                                        <i class="fas fa-futbol text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-weight-bold"><?= htmlspecialchars($pedido['categoria_nombre']) ?></div>
                                                        <small class="text-muted d-block">
                                                            <?= htmlspecialchars($pedido['equipo_a'] . ' vs ' . $pedido['equipo_b']) ?>
                                                        </small>
                                                        <small class="text-muted">
                                                            <?= date('d/m/Y H:i', strtotime($pedido['fecha_hora'])) ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end font-weight-bold">
                                                <?= formatCurrency($pedido['monto_total']) ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($pedido['estado_pago'] === 'pagado'): ?>
                                                    <span class="badge bg-success">Pagado</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Pendiente</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($pedido['estado_entrega'] === 'entregado'): ?>
                                                    <span class="badge bg-success">Entregado</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Pendiente</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if (($pedido['estado_pago'] ?? '') === 'pagado'): ?>
                                                    <span class="badge bg-success">Pagado</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Pendiente</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if (($pedido['estado_entrega'] ?? '') === 'entregado'): ?>
                                                    <span class="badge bg-success">Entregado</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Pendiente</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center">
                                                    <a href="index.php?c=pedidos&a=ver&id=<?= $pedido['id'] ?>" 
                                                       class="btn btn-sm btn-primary mr-1" 
                                                       data-toggle="tooltip" 
                                                       data-placement="top"
                                                       title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="index.php?c=pedidos&a=generarComprobante&id=<?= $pedido['id'] ?>" 
                                                       class="btn btn-sm btn-success" 
                                                       data-toggle="tooltip" 
                                                       data-placement="top"
                                                       title="Descargar comprobante"
                                                       target="_blank">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Inicializar tooltips -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>