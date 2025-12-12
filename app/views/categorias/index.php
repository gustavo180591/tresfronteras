<?php
// app/views/categorias/index.php

// Variables que vienen del controlador:
$categorias = $categorias ?? [];
$tiposTorneo = $tiposTorneo ?? [];
$errores = $_SESSION['mensaje_error'] ?? null;
$exito = $_SESSION['mensaje_exito'] ?? null;

// Limpiar mensajes después de mostrarlos
unset($_SESSION['mensaje_error']);
unset($_SESSION['mensaje_exito']);
?>

<section class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 mb-0">Gestión de Categorías</h2>
    </div>

    <!-- Mensajes de éxito/error -->
    <?php if ($errores): ?>
        <div class="alert alert-danger"><?= $errores ?></div>
    <?php endif; ?>
    
    <?php if ($exito): ?>
        <div class="alert alert-success"><?= $exito ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Formulario de categoría -->
        <div class="col-12 col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">
                        <?= isset($categoriaEditar) ? 'Editar' : 'Nueva' ?> Categoría
                        <?php if (isset($categoriaEditar)): ?>
                            <span class="badge bg-primary ms-2">Editando</span>
                        <?php endif; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <form method="post" action="index.php?c=categorias&a=guardar" class="needs-validation" novalidate>
                        <input type="hidden" name="id" value="<?= $categoriaEditar['id'] ?? '' ?>">
                        
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre de la categoría</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="nombre" 
                                name="nombre" 
                                value="<?= htmlspecialchars($categoriaEditar['nombre'] ?? '') ?>" 
                                required
                                placeholder="Ej: Sub 12, Femenino, Libre, etc."
                            >
                            <div class="invalid-feedback">
                                Por favor ingrese un nombre para la categoría.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="tipo_torneo_id" class="form-label">Tipo de Torneo</label>
                            <select 
                                class="form-select" 
                                id="tipo_torneo_id" 
                                name="tipo_torneo_id" 
                                required
                            >
                                <option value="">Seleccione un tipo de torneo</option>
                                <?php foreach ($tiposTorneo as $tipo): ?>
                                    <option 
                                        value="<?= $tipo['id'] ?>"
                                        <?= isset($categoriaEditar) && $categoriaEditar['tipo_torneo_id'] == $tipo['id'] ? 'selected' : '' ?>
                                    >
                                        <?= htmlspecialchars($tipo['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                Por favor seleccione un tipo de torneo.
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                <?= isset($categoriaEditar) ? 'Actualizar' : 'Guardar' ?>
                            </button>
                            
                            <?php if (isset($categoriaEditar)): ?>
                                <a href="index.php?c=categorias&a=index" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i> Cancelar
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Listado de categorías -->
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">Listado de Categorías</h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($categorias)): ?>
                        <div class="alert alert-info m-3">No hay categorías cargadas.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Nombre</th>
                                        <th>Tipo de Torneo</th>
                                        <th class="text-end pe-3">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <tr>
                                            <td class="ps-3 align-middle fw-medium"><?= htmlspecialchars($categoria['nombre']) ?></td>
                                            <td class="align-middle">
                                                <span class="badge bg-light text-dark">
                                                    <?= htmlspecialchars($categoria['tipo_torneo']) ?>
                                                </span>
                                            </td>
                                            <td class="text-end pe-3">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <!-- Edit Button -->
                                                    <a 
                                                        href="index.php?c=categorias&a=index&editar=<?= $categoria['id'] ?>" 
                                                        class="btn-action btn-edit"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="Editar categoría"
                                                        aria-label="Editar categoría"
                                                    >
                                                        <i class="fas fa-pen-to-square"></i>
                                                    </a>
                                                    
                                                    <!-- Delete Button -->
                                                    <button 
                                                        type="button" 
                                                        class="btn-action btn-delete"
                                                        onclick="confirmarEliminar(<?= $categoria['id'] ?>, '<?= htmlspecialchars(addslashes($categoria['nombre'])) ?>')"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="Eliminar categoría"
                                                        aria-label="Eliminar categoría"
                                                    >
                                                        <i class="fas fa-trash-can"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="confirmarEliminarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                ¿Está seguro que desea eliminar la categoría "<span id="nombreCategoria"></span>"?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a id="btnEliminar" href="#" class="btn btn-danger">Eliminar</a>
            </div>
        </div>
    </div>
</div>

<style>
/* Action buttons styling */
.btn-action {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    background: white;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    color: #fff;
    position: relative;
    overflow: hidden;
}

/* Edit button specific */
.btn-edit {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
}

/* Delete button specific */
.btn-delete {
    background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%);
}

/* Hover effects */
.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.btn-action:active {
    transform: translateY(0);
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

/* Ripple effect */
.btn-action::after {
    content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    background: rgba(255, 255, 255, 0.3);
    opacity: 0;
    border-radius: 50%;
    transform: scale(0);
    transition: transform 0.3s ease-out, opacity 0.3s ease-out;
}

.btn-action:active::after {
    transform: scale(2);
    opacity: 0;
}

/* Focus states */
.btn-action:focus {
    outline: none;
    box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
}

/* Icon styling */
.btn-action i {
    font-size: 0.9rem;
    transition: transform 0.2s ease;
}

.btn-action:hover i {
    transform: scale(1.1);
}
</style>

<script>
// Initialize tooltips and form validation
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Form validation
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
});

function confirmarEliminar(id, nombre) {
    document.getElementById('nombreCategoria').textContent = nombre;
    document.getElementById('btnEliminar').href = `index.php?c=categorias&a=eliminar&id=${id}`;
    
    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('confirmarEliminarModal'));
    modal.show();
}
</script>