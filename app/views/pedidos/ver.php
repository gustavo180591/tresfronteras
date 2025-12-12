<?php
// app/views/pedidos/ver.php

// Helper functions
function formatDate($dateString) {
    return (new DateTime($dateString))->format('d/m/Y H:i');
}

function formatCurrency($amount) {
    return '$' . number_format($amount, 2, ',', '.');
}
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Detalles del Pedido #<?= str_pad($pedido['id'], 5, '0', STR_PAD_LEFT) ?></h1>
        <div class="d-flex gap-2">
            <?php if (isset($_SESSION['pdf_receipt_path'])): ?>
                <a href="<?= $_SESSION['pdf_receipt_path'] ?>" target="_blank" class="btn btn-sm btn-success">
                    <i class="fas fa-file-pdf me-1"></i> Descargar Comprobante
                </a>
                <?php unset($_SESSION['pdf_receipt_path']); ?>
            <?php endif; ?>
            <a href="index.php?c=pedidos&a=editar&id=<?= $pedido['id'] ?>" class="btn btn-sm btn-primary">
                <i class="fas fa-edit me-1"></i> Editar
            </a>
            <a href="index.php?c=pedidos&a=generarComprobante&id=<?= $pedido['id'] ?>" class="btn btn-sm btn-success" target="_blank">
                <i class="fas fa-file-pdf me-1"></i> Comprobante
            </a>
            <a href="index.php?c=pedidos&a=index" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Información del Pedido -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Información del Pedido</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1 text-muted">Fecha del Pedido</p>
                            <p class="mb-0">
                                <i class="far fa-calendar-alt me-2"></i>
                                <?= formatDate($pedido['fecha_pedido']) ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted">Estado de Pago</p>
                            <span class="badge bg-<?= $pedido['estado_pago'] === 'pagado' ? 'success' : 'warning' ?>">
                                <?= ucfirst($pedido['estado_pago']) ?>
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1 text-muted">Monto Total</p>
                            <h4 class="text-primary"><?= formatCurrency($pedido['monto_total']) ?></h4>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted">Forma de Pago</p>
                            <p class="mb-0">
                                <i class="fas fa-<?= $pedido['forma_pago'] === 'efectivo' ? 'money-bill-wave' : 'exchange-alt' ?> me-2"></i>
                                <?= ucfirst($pedido['forma_pago']) ?>
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <p class="mb-1 text-muted">Archivos</p>
                            <div class="border rounded p-3 bg-light">
                                <?php 
                                $archivos = !empty($pedido['archivos']) ? explode(',', $pedido['archivos']) : [];
                                if (!empty($archivos)): 
                                    foreach ($archivos as $archivo): 
                                        $archivo = trim($archivo);
                                        if (!empty($archivo)):
                                ?>
                                    <span class="badge bg-secondary me-2 mb-2">
                                        <i class="far fa-file me-1"></i> 
                                        <?= htmlspecialchars($archivo) ?>
                                    </span>
                                <?php 
                                        endif;
                                    endforeach; 
                                else: 
                                ?>
                                    <p class="mb-0 text-muted">No hay archivos asociados</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del Partido -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Información del Partido</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1 text-muted">Categoría</p>
                            <p class="mb-3"><?= htmlspecialchars($pedido['categoria_nombre']) ?></p>
                            
                            <p class="mb-1 text-muted">Equipos</p>
                            <h5><?= htmlspecialchars($pedido['equipo_a']) ?> vs <?= htmlspecialchars($pedido['equipo_b']) ?></h5>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted">Fecha y Hora</p>
                            <p class="mb-3">
                                <i class="far fa-calendar-alt me-2"></i>
                                <?= formatDate($pedido['fecha_hora']) ?>
                            </p>
                            
                            <p class="mb-1 text-muted">Cantidad de Fotos</p>
                            <p class="mb-0">
                                <span class="badge bg-primary">
                                    <?= (int)$pedido['cantidad_fotos'] ?> foto(s)
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Información del Cliente -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Información del Cliente</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-xl bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3">
                            <i class="fas fa-user text-primary"></i>
                        </div>
                        <div>
                            <h5 class="mb-0"><?= htmlspecialchars($pedido['nombre_cliente']) ?></h5>
                            <p class="text-muted mb-0">
                                <i class="fas fa-phone-alt me-1"></i>
                                <?= htmlspecialchars($pedido['telefono']) ?>
                            </p>
                        </div>
                    </div>

                    <?php if (!empty($pedido['email'])): ?>
                    <div class="d-flex align-items-center mb-2">
                        <i class="far fa-envelope me-2 text-muted"></i>
                        <a href="mailto:<?= htmlspecialchars($pedido['email']) ?>">
                            <?= htmlspecialchars($pedido['email']) ?>
                        </a>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($pedido['notas'])): ?>
                    <div class="mt-3">
                        <p class="mb-1 text-muted">Notas Adicionales</p>
                        <div class="border rounded p-3 bg-light">
                            <?= nl2br(htmlspecialchars($pedido['notas'])) ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Acciones -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Acciones</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="index.php?c=pedidos&a=editar&id=<?= $pedido['id'] ?>" 
                           class="btn btn-outline-primary">
                            <i class="fas fa-edit me-2"></i>Editar Pedido
                        </a>
                        
                        <button type="button" 
                                class="btn btn-outline-<?= $pedido['estado_pago'] === 'pagado' ? 'warning' : 'success' ?>"
                                onclick="cambiarEstadoPago(this, <?= $pedido['id'] ?>, <?= $pedido['monto_total'] ?>, '<?= $pedido['estado_pago'] === 'pagado' ? 'no_pagado' : 'pagado' ?>')">
                            <i class="fas <?= $pedido['estado_pago'] === 'pagado' ? 'fa-times' : 'fa-check' ?> me-2"></i>
                            <?= $pedido['estado_pago'] === 'pagado' ? 'Marcar como Pendiente' : 'Marcar como Pagado' ?>
                        </button>
                        
                        <button type="button" 
                                class="btn btn-outline-danger"
                                onclick="confirmarEliminar(<?= $pedido['id'] ?>)">
                            <i class="fas fa-trash-alt me-2"></i>Eliminar Pedido
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="confirmarEliminarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar este pedido? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a id="btnEliminar" href="#" class="btn btn-danger">Eliminar</a>
            </div>
        </div>
    </div>
</div>

<script>
// Función para confirmar eliminación
function confirmarEliminar(id) {
    const btnEliminar = document.getElementById('btnEliminar');
    btnEliminar.href = 'index.php?c=pedidos&a=eliminar&id=' + id;
    
    const modal = new bootstrap.Modal(document.getElementById('confirmarEliminarModal'));
    modal.show();
}

// Función para cambiar estado de pago
function cambiarEstadoPago(button, pedidoId, monto, nuevoEstado) {
    const icon = button.querySelector('i');
    const originalText = button.innerHTML;
    
    // Mostrar indicador de carga
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Procesando...';
    
    // Enviar petición AJAX
    fetch('index.php?c=pedidos&a=cambiarEstadoPago', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=' + pedidoId + '&estado=' + nuevoEstado
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar notificación
            showToast('success', 'Estado de pago actualizado correctamente');
            
            // Actualizar botón
            if (nuevoEstado === 'pagado') {
                button.classList.remove('btn-outline-success');
                button.classList.add('btn-outline-warning');
                icon.className = 'fas fa-times me-2';
                button.innerHTML = icon.outerHTML + 'Marcar como Pendiente';
                button.onclick = function() { 
                    cambiarEstadoPago(this, pedidoId, monto, 'no_pagado'); 
                };
            } else {
                button.classList.remove('btn-outline-warning');
                button.classList.add('btn-outline-success');
                icon.className = 'fas fa-check me-2';
                button.innerHTML = icon.outerHTML + 'Marcar como Pagado';
                button.onclick = function() { 
                    cambiarEstadoPago(this, pedidoId, monto, 'pagado'); 
                };
            }
            
            // Recargar la página después de 1 segundo
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            throw new Error(data.message || 'Error al actualizar el estado de pago');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('danger', 'Error: ' + error.message);
    })
    .finally(() => {
        button.disabled = false;
    });
}

// Función para mostrar notificaciones toast
function showToast(type, message) {
    const container = document.getElementById('toast-container') || createToastContainer();
    
    const toast = document.createElement('div');
    toast.className = `toast show align-items-center text-white bg-${type} border-0`;
    toast.role = 'alert';
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    
    container.appendChild(toast);
    
    // Eliminar el toast después de 3 segundos
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Función para crear el contenedor de toasts si no existe
function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'position-fixed bottom-0 end-0 p-3';
    container.style.zIndex = '1080';
    document.body.appendChild(container);
    return container;
}
</script>
