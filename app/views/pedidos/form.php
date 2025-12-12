<?php
// app/views/pedidos/form.php

function formatDate($dateString) {
    return (new DateTime($dateString))->format('d/m/Y H:i');
}
?>

<div class="container py-3">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card">
                <div class="card-header bg-white py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Nuevo Pedido</h5>
                        <a href="index.php?c=pedidos&a=index" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body p-3">
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <?= $error_message ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                            <?= $success_message ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form id="formPedido" action="index.php?c=pedidos&a=<?= isset($formData['id']) ? 'actualizar' : 'guardar' ?>" method="post" class="needs-validation" novalidate>
    <?php if (isset($formData['id'])): ?>
    <input type="hidden" name="id" value="<?= htmlspecialchars($formData['id']) ?>">
    <?php endif; ?>
    <div class="row g-3">
                            <!-- Datos del Cliente -->
                            <div class="col-12">
                                <h6 class="text-uppercase text-xs fw-bold mb-3">Datos del Cliente</h6>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <input type="text" class="form-control form-control-sm" id="nombre_cliente" 
                                               name="nombre_cliente" placeholder="Nombre Completo *" 
                                               value="<?= htmlspecialchars($formData['nombre_cliente'] ?? '') ?>" required>
                                        <div class="invalid-feedback">
                                            Por favor ingrese el nombre del cliente.
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="tel" class="form-control form-control-sm" id="telefono" 
                                               name="telefono" placeholder="Teléfono *" 
                                               value="<?= htmlspecialchars($formData['telefono'] ?? '') ?>" required>
                                        <div class="invalid-feedback">
                                            Por favor ingrese el teléfono del cliente.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Selección de Partido -->
                            <div class="col-12">
                                <h6 class="text-uppercase text-xs fw-bold mb-3">Partido</h6>
                                <select class="form-select form-select-sm" id="partido_id" name="partido_id" required>
                                    <option value="">Seleccione un partido</option>
                                    <?php foreach ($partidos as $partido): 
                                        $selected = (isset($formData['partido_id']) && $formData['partido_id'] == $partido['id']) ? 'selected' : '';
                                    ?>
                                        <option value="<?= $partido['id'] ?>" 
                                                data-equipo-a="<?= htmlspecialchars($partido['equipo_a']) ?>"
                                                data-equipo-b="<?= htmlspecialchars($partido['equipo_b']) ?>"
                                                data-categoria="<?= htmlspecialchars($partido['categoria_nombre']) ?>"
                                                <?= $selected ?>>
                                            <?= htmlspecialchars($partido['categoria_nombre']) ?> - 
                                            <?= htmlspecialchars($partido['equipo_a']) ?> vs <?= htmlspecialchars($partido['equipo_b']) ?> 
                                            (<?= formatDate($partido['fecha_hora']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Por favor seleccione un partido.
                                </div>
                                <div class="small text-muted mt-1" id="partido_info">-</div>
                            </div>

                            <!-- Detalles del Pedido -->
                            <div class="col-12">
                                <h6 class="text-uppercase text-xs fw-bold mb-3">Detalles</h6>
                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Cantidad</span>
                                            <input type="number" class="form-control" id="cantidad_fotos" 
                                                   name="cantidad_fotos" min="1" 
                                                   value="<?= $formData['cantidad_fotos'] ?? 1 ?>" required>
                                        </div>
                                        <div class="invalid-feedback">
                                            La cantidad debe ser mayor a cero.
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control" id="precio_por_foto" 
                                                   name="precio_por_foto" 
                                                   value="<?= $formData['precio_por_foto'] ?? number_format($precioPorFoto, 2, '.', '') ?>" 
                                                   step="0.01" min="0.01" required>
                                        </div>
                                        <div class="invalid-feedback">
                                            El precio debe ser mayor a cero.
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex gap-2 align-items-center h-100">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="forma_pago" 
                                                       id="efectivo" value="efectivo" 
                                                       <?= (!isset($formData['forma_pago']) || $formData['forma_pago'] === 'efectivo') ? 'checked' : '' ?>>
                                                <label class="form-check-label small" for="efectivo">Efectivo</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="forma_pago" 
                                                       id="transferencia" value="transferencia"
                                                       <?= (isset($formData['forma_pago']) && $formData['forma_pago'] === 'transferencia') ? 'checked' : '' ?>>
                                                <label class="form-check-label small" for="transferencia">Transferencia</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex gap-2 align-items-center h-100">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="estado_pago" 
                                                       id="pagado" value="pagado"
                                                       <?= (isset($formData['estado_pago']) && $formData['estado_pago'] === 'pagado') ? 'checked' : '' ?>>
                                                <label class="form-check-label small text-success fw-bold" for="pagado">Pagado</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="estado_pago" 
                                                       id="no_pagado" value="no_pagado"
                                                       <?= (!isset($formData['estado_pago']) || $formData['estado_pago'] === 'no_pagado') ? 'checked' : '' ?>>
                                                <label class="form-check-label small text-danger" for="no_pagado">No Pagado</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12" id="archivos_container">
                                        <!-- Dynamic file inputs will be added here by JavaScript -->
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded">
                                            <span class="small fw-bold">Total a Pagar:</span>
                                            <h5 class="mb-0 text-primary" id="total_pagar">
                                                $<?= number_format($formData['monto_total'] ?? $precioPorFoto, 2, ',', '.') ?>
                                            </h5>
                                            <input type="hidden" name="monto_total" id="monto_total" 
                                                   value="<?= $formData['monto_total'] ?? $precioPorFoto ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                                <button type="reset" class="btn btn-sm btn-outline-secondary" id="btnLimpiar">
                                    <i class="fas fa-undo me-1"></i> Limpiar
                                </button>
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="fas fa-save me-1"></i> Guardar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formPedido');
    const cantidadFotos = document.getElementById('cantidad_fotos');
    const precioPorFoto = document.getElementById('precio_por_foto');
    const totalPagar = document.getElementById('total_pagar');
    const montoTotal = document.getElementById('monto_total');
    const partidoSelect = document.getElementById('partido_id');
    const partidoInfo = document.getElementById('partido_info');
    const archivosContainer = document.getElementById('archivos_container');
    const btnLimpiar = document.getElementById('btnLimpiar');

    // Function to update file inputs based on quantity
    function actualizarArchivosInputs() {
        const cantidad = parseInt(cantidadFotos.value) || 0;
        archivosContainer.innerHTML = '';
        
        if (cantidad > 0) {
            const row = document.createElement('div');
            row.className = 'row g-2';
            
            // Get existing file names from form data if available
            const archivosGuardados = <?= isset($formData['archivos']) ? json_encode($formData['archivos']) : '[]' ?>;
            
            for (let i = 0; i < cantidad; i++) {
                const col = document.createElement('div');
                col.className = 'col-md-6 mb-2';
                const valorArchivo = archivosGuardados[i] || '';
                
                col.innerHTML = `
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Archivo ${i + 1}</span>
                        <input type="text" class="form-control" 
                               name="archivos[]" 
                               placeholder="Nombre del archivo ${i + 1}" 
                               value="${valorArchivo}"
                               required>
                        <div class="invalid-feedback">
                            Por favor ingrese el nombre del archivo.
                        </div>
                    </div>
                `;
                row.appendChild(col);
            }
            
            archivosContainer.appendChild(row);
        }
        
        calcularTotal();
    }

    function calcularTotal() {
        const cantidad = parseInt(cantidadFotos.value) || 0;
        const precio = parseFloat(precioPorFoto.value) || 0;
        const total = cantidad * precio;
        
        totalPagar.textContent = '$' + total.toLocaleString('es-AR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        montoTotal.value = total;
    }

    function actualizarInfoPartido() {
        const selectedOption = partidoSelect.options[partidoSelect.selectedIndex];
        if (selectedOption.value) {
            const equipoA = selectedOption.getAttribute('data-equipo-a');
            const equipoB = selectedOption.getAttribute('data-equipo-b');
            const categoria = selectedOption.getAttribute('data-categoria');
            partidoInfo.innerHTML = `<strong>${categoria}</strong> - ${equipoA} vs ${equipoB}`;
        } else {
            partidoInfo.textContent = '-';
        }
    }

    // Event Listeners
    cantidadFotos.addEventListener('input', function() {
        actualizarArchivosInputs();
        calcularTotal();
    });
    
    precioPorFoto.addEventListener('input', calcularTotal);
    partidoSelect.addEventListener('change', actualizarInfoPartido);
    
    // Reset form
    btnLimpiar.addEventListener('click', function() {
        setTimeout(actualizarArchivosInputs, 0);
    });

    // Initialize
    actualizarArchivosInputs();
    actualizarInfoPartido();
    
    // Form validation
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>
</script>