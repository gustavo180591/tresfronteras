<?php
// app/views/pedidos/index.php

// Helper para formatear fecha
function formatDate($dateString) {
    try {
        return (new DateTime($dateString))->format('d/m/Y H:i');
    } catch (Exception $e) {
        return $dateString;
    }
}

// Filtros desde GET
$filtro_estado      = $_GET['estado']      ?? 'todos';
$filtro_forma_pago  = $_GET['forma_pago']  ?? 'todas';
$filtro_busqueda    = $_GET['buscar']      ?? '';

// Totales para tarjetas
$totalPagado    = 0;
$totalPendiente = 0;

foreach ($pedidos as $pedido) {
    if ($pedido['estado_pago'] === 'pagado') {
        $totalPagado += (float) $pedido['monto_total'];
    } else {
        $totalPendiente += (float) $pedido['monto_total'];
    }
}
?>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Gestión de Pedidos</h1>
        <div>
            <a href="index.php?c=pedidos&a=nuevo" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nuevo Pedido
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header bg-white py-2">
            <h5 class="mb-0">Filtrar Pedidos</h5>
        </div>
        <div class="card-body">
            <form method="get" class="row g-3" action="index.php?c=pedidos&a=index">
                <input type="hidden" name="c" value="pedidos">
                <input type="hidden" name="a" value="index">

                <div class="col-md-4">
                    <label for="estado" class="form-label">Estado</label>
                    <select class="form-select" id="estado" name="estado">
                        <option value="todos" <?= $filtro_estado === 'todos' ? 'selected' : '' ?>>Todos los estados</option>
                        <option value="pagado" <?= $filtro_estado === 'pagado' ? 'selected' : '' ?>>Pagados</option>
                        <option value="no_pagado" <?= $filtro_estado === 'no_pagado' ? 'selected' : '' ?>>Pendientes de pago</option>
                        <option value="entregado" <?= $filtro_estado === 'entregado' ? 'selected' : '' ?>>Entregados</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="forma_pago" class="form-label">Forma de Pago</label>
                    <select class="form-select" id="forma_pago" name="forma_pago">
                        <option value="todas" <?= $filtro_forma_pago === 'todas' ? 'selected' : '' ?>>Todas</option>
                        <option value="efectivo" <?= $filtro_forma_pago === 'efectivo' ? 'selected' : '' ?>>Efectivo</option>
                        <option value="transferencia" <?= $filtro_forma_pago === 'transferencia' ? 'selected' : '' ?>>Transferencia</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="buscar" class="form-label">Buscar</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="buscar" name="buscar" 
                               placeholder="Cliente, partido..." value="<?= htmlspecialchars($filtro_busqueda) ?>">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i> Filtrar
                    </button>
                    <a href="index.php?c=pedidos&a=index" class="btn btn-outline-secondary">
                        <i class="fas fa-sync-alt me-1"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tarjetas resumen -->
    <div class="row mb-4">
        <!-- Total pedidos -->
        <div class="col-md-4 mb-3">
            <div class="card border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Pedidos</h6>
                            <h4 class="mb-0"><?= (int) $totalPedidos ?></h4>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                            <i class="fas fa-shopping-cart text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Pagado -->
        <div class="col-md-4 mb-3">
            <div class="card border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Pagado</h6>
                            <h4 class="mb-0 total-pagado">
                                $<?= number_format($totalPagado, 2, ',', '.') ?>
                            </h4>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-3">
                            <i class="fas fa-check-circle text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pendiente de pago -->
        <div class="col-md-4 mb-3">
            <div class="card border-start border-warning border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Pendiente de Pago</h6>
                            <h4 class="mb-0 total-pendiente">
                                $<?= number_format($totalPendiente, 2, ',', '.') ?>
                            </h4>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-3">
                            <i class="fas fa-clock text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->

    <!-- Tabla de pedidos -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3"># Pedido</th>
                            <th>Cliente</th>
                            <th>Partido</th>
                            <th class="text-center">Fotos</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th class="text-end pe-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pedidos)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p class="mb-0">No se encontraron pedidos</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pedidos as $pedido): ?>
                                <?php
                                    $isPaid   = $pedido['estado_pago'] === 'pagado';
                                    $rowClass = $isPaid ? 'table-success' : '';
                                ?>
                                <tr class="<?= $rowClass ?>">
                                    <td class="ps-3 fw-medium">
                                        #<?= str_pad($pedido['id'], 5, '0', STR_PAD_LEFT) ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-sm">
                                                    <div class="avatar-title bg-light rounded-circle text-primary fw-bold">
                                                        <?= strtoupper(substr($pedido['nombre_cliente'], 0, 1)) ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-2">
                                                <h6 class="mb-0">
                                                    <?= htmlspecialchars($pedido['nombre_cliente'], ENT_QUOTES, 'UTF-8') ?>
                                                </h6>
                                                <small class="text-muted">
                                                    <?= htmlspecialchars($pedido['telefono'], ENT_QUOTES, 'UTF-8') ?>
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium">
                                                <?= htmlspecialchars($pedido['categoria_nombre'], ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                            <small class="text-muted">
                                                <?= htmlspecialchars($pedido['equipo_a'], ENT_QUOTES, 'UTF-8') ?>
                                                vs
                                                <?= htmlspecialchars($pedido['equipo_b'], ENT_QUOTES, 'UTF-8') ?>
                                            </small>
                                            <small class="text-muted">
                                                <i class="far fa-calendar-alt me-1"></i>
                                                <?= formatDate($pedido['fecha_hora']) ?>
                                            </small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary rounded-pill">
                                            <?= (int) $pedido['cantidad_fotos'] ?>
                                        </span>
                                    </td>
                                    <td class="monto-col">
                                        <span class="fw-medium">
                                            $<?= number_format($pedido['monto_total'], 2, ',', '.') ?>
                                        </span>
                                        <small class="d-block text-muted">
                                            <?= $pedido['forma_pago'] === 'efectivo' ? 'Efectivo' : 'Transferencia' ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php $labelTexto = $isPaid ? 'Pagado' : 'Pendiente'; ?>
                                        <div class="form-check form-switch d-inline-block">
                                            <input
                                                class="form-check-input payment-toggle"
                                                type="checkbox"
                                                role="switch"
                                                id="toggle-<?= $pedido['id'] ?>"
                                                data-pedido-id="<?= (int) $pedido['id'] ?>"
                                                data-monto="<?= (float) $pedido['monto_total'] ?>"
                                                <?= $isPaid ? 'checked' : '' ?>
                                                style="width: 2.5em; height: 1.5em; cursor: pointer; margin-left: 0;"
                                            >
                                            <label
                                                class="form-check-label ms-2 fw-medium"
                                                for="toggle-<?= $pedido['id'] ?>"
                                                style="cursor: pointer;"
                                            >
                                                <?= $labelTexto ?>
                                            </label>
                                        </div>
                                        <?php if ($pedido['estado_entrega'] === 'entregado'): ?>
                                            <span class="badge bg-info ms-2">Entregado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a
                                                href="index.php?c=pedidos&a=ver&id=<?= (int) $pedido['id'] ?>"
                                                class="btn btn-sm btn-outline-primary rounded-circle p-2"
                                                data-bs-toggle="tooltip"
                                                title="Ver detalles"
                                            >
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <a
                                                href="index.php?c=pedidos&a=editar&id=<?= (int) $pedido['id'] ?>"
                                                class="btn btn-sm btn-outline-secondary rounded-circle p-2"
                                                data-bs-toggle="tooltip"
                                                title="Editar pedido"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-danger rounded-circle p-2"
                                                onclick="confirmarEliminar(<?= (int) $pedido['id'] ?>)"
                                                data-bs-toggle="tooltip"
                                                title="Eliminar pedido"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <?php if ($totalPages > 1): ?>
                <div class="d-flex justify-content-between align-items-center px-3 py-3 border-top">
                    <div class="text-muted">
                        Mostrando <?= count($pedidos) ?> de <?= (int) $totalPedidos ?> registros
                    </div>
                    <nav aria-label="Paginación de pedidos">
                        <ul class="pagination mb-0">
                            <?php if ($currentPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?c=pedidos&a=index&page=<?= $currentPage - 1 ?>" aria-label="Anterior">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php
                            $start = max(1, $currentPage - 2);
                            $end   = min($start + 4, $totalPages);

                            if ($start > 1) {
                                echo '<li class="page-item"><a class="page-link" href="?c=pedidos&a=index&page=1">1</a></li>';
                                if ($start > 2) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                            }

                            for ($i = $start; $i <= $end; $i++): ?>
                                <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="?c=pedidos&a=index&page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor;

                            if ($end < $totalPages) {
                                if ($end < $totalPages - 1) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                                echo '<li class="page-item"><a class="page-link" href="?c=pedidos&a=index&page=' . $totalPages . '">' . $totalPages . '</a></li>';
                            }
                            ?>

                            <?php if ($currentPage < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?c=pedidos&a=index&page=<?= $currentPage + 1 ?>" aria-label="Siguiente">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Eliminar -->
<div class="modal fade" id="confirmarEliminarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro que desea eliminar este pedido? Esta acción no se puede deshacer.</p>
                <p class="text-danger fw-medium">Se eliminarán todos los datos asociados al pedido.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a id="btnEliminar" href="#" class="btn btn-danger">Eliminar</a>
            </div>
        </div>
    </div>
</div>

<script>
// Tooltips
document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Listeners para los toggles de pago
    const toggles = document.querySelectorAll('.payment-toggle');
    toggles.forEach(function (toggle) {
        toggle.addEventListener('change', function () {
            const pedidoId   = this.getAttribute('data-pedido-id');
            const monto      = parseFloat(this.getAttribute('data-monto')) || 0;
            const nuevoEstado = this.checked ? 'pagado' : 'no_pagado';

            cambiarEstadoPago(this, pedidoId, monto, nuevoEstado);
        });
    });
});

// Confirmar eliminación
function confirmarEliminar(id) {
    const btnEliminar = document.getElementById('btnEliminar');
    btnEliminar.href = 'index.php?c=pedidos&a=eliminar&id=' + id;

    const modal = new bootstrap.Modal(document.getElementById('confirmarEliminarModal'));
    modal.show();
}

// Cambiar estado de pago por AJAX
function cambiarEstadoPago(toggle, pedidoId, monto, nuevoEstado) {
    const row   = toggle.closest('tr');
    const label = row.querySelector('.form-check-label');

    const totalPagadoEl    = document.querySelector('.total-pagado');
    const totalPendienteEl = document.querySelector('.total-pendiente');

    // Valores numéricos actuales
    let totalPagado    = parseFloat(totalPagadoEl.textContent.replace(/[^0-9,-]+/g, '').replace(',', '.')) || 0;
    let totalPendiente = parseFloat(totalPendienteEl.textContent.replace(/[^0-9,-]+/g, '').replace(',', '.')) || 0;

    const montoNum = parseFloat(monto) || 0;

    // Aplicar cambios optimistas en UI
    if (nuevoEstado === 'pagado') {
        row.classList.add('table-success');
        label.textContent = 'Pagado';
        totalPagado    += montoNum;
        totalPendiente -= montoNum;
    } else {
        row.classList.remove('table-success');
        label.textContent = 'Pendiente';
        totalPagado    -= montoNum;
        totalPendiente += montoNum;
    }

    // Normalizamos a no-negativos
    if (totalPagado < 0) totalPagado = 0;
    if (totalPendiente < 0) totalPendiente = 0;

    // Actualizar textos
    totalPagadoEl.textContent    = '$' + totalPagado.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    totalPendienteEl.textContent = '$' + totalPendiente.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    // Deshabilitar mientras se procesa
    toggle.disabled = true;

    // Enviar AJAX al backend
    fetch('index.php?c=pedidos&a=cambiarEstadoPago', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=' + encodeURIComponent(pedidoId) +
              '&estado=' + encodeURIComponent(nuevoEstado)
    })
    .then(function (response) {
        return response.json();
    })
    .then(function (data) {
        if (!data.success) {
            throw new Error(data.message || 'Error al actualizar el estado de pago');
        }
        showToast('success', 'Estado de pago actualizado correctamente');
    })
    .catch(function (error) {
        console.error(error);

        // Revertir cambios en caso de error
        if (nuevoEstado === 'pagado') {
            row.classList.remove('table-success');
            label.textContent = 'Pendiente';
            totalPagado    -= montoNum;
            totalPendiente += montoNum;
            toggle.checked = false;
        } else {
            row.classList.add('table-success');
            label.textContent = 'Pagado';
            totalPagado    += montoNum;
            totalPendiente -= montoNum;
            toggle.checked = true;
        }

        if (totalPagado < 0) totalPagado = 0;
        if (totalPendiente < 0) totalPendiente = 0;

        totalPagadoEl.textContent    = '$' + totalPagado.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        totalPendienteEl.textContent = '$' + totalPendiente.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        showToast('danger', 'Error: ' + error.message);
    })
    .finally(function () {
        toggle.disabled = false;
    });
}

// Toasts
function showToast(type, message) {
    const container = document.getElementById('toast-container') || createToastContainer();

    const toast = document.createElement('div');
    toast.className = 'toast show align-items-center text-white bg-' + type + ' border-0 mb-2';
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');

    toast.innerHTML = '' +
        '<div class="d-flex">' +
            '<div class="toast-body">' + message + '</div>' +
            '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>' +
        '</div>';

    container.appendChild(toast);

    setTimeout(function () {
        toast.remove();
    }, 3000);
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'position-fixed bottom-0 end-0 p-3';
    container.style.zIndex = '1080';
    container.style.maxWidth = '350px';
    document.body.appendChild(container);
    return container;
}

// CSS extra vía JS
const style = document.createElement('style');
style.textContent = `
    .table-success {
        background-color: rgba(25, 135, 84, 0.05) !important;
    }
    .form-switch .form-check-input:checked {
        background-color: #198754;
        border-color: #198754;
    }
    .form-switch .form-check-input:focus {
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
    }
`;
document.head.appendChild(style);
</script>
