<?php
// app/views/partidos/index.php

declare(strict_types=1);

// Variables que vienen del controlador:
$partidos      = $partidos      ?? [];
$page          = $page          ?? 1;
$totalPages    = $totalPages    ?? 1;
$totalPartidos = $totalPartidos ?? 0;

// Función para traducir estado a texto amigable
function label_estado_partido(string $estado): string {
    switch ($estado) {
        case 'en_juego':
            return 'En juego';
        case 'finalizado':
            return 'Finalizado';
        default:
            return 'Pendiente';
    }
}
?>

<section class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="h5 mb-0">Fixture de partidos</h2>
            <small class="text-muted">
                Total: <?= (int) $totalPartidos ?> partido<?= $totalPartidos === 1 ? '' : 's' ?>
            </small>
        </div>

        <div class="d-flex gap-2">
            <!-- Botón para agregar nuevo partido -->
            <a
                href="<?= htmlspecialchars(base_url('index.php?c=partidos&a=crear'), ENT_QUOTES, 'UTF-8') ?>"
                class="btn btn-sm btn-primary"
            >
                + Agregar partido
            </a>
            
            <!-- Botón para gestionar categorías -->
            <a
                href="<?= htmlspecialchars(base_url('index.php?c=categorias&a=index'), ENT_QUOTES, 'UTF-8') ?>"
                class="btn btn-sm btn-outline-secondary"
                title="Gestionar categorías"
            >
                <i class="fas fa-tags me-1"></i> Categorías
            </a>
        </div>
    </div>

    <div class="table-responsive shadow-sm bg-white rounded">
        <table class="table table-sm table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Fecha / hora</th>
                    <th>Categoría</th>
                    <th>Tipo torneo</th>
                    <th>Equipos</th>
                    <th>Cancha</th>
                    <th>Estado</th>
                    <th class="text-center">Resultado</th>
                    <th class="text-end">Acciones</th>
                </tr>
                <tr id="filters">
                    <th><input type="text" class="form-control form-control-sm filter" data-column="0" placeholder="Filtrar..."></th>
                    <th><input type="date" class="form-control form-control-sm filter" data-column="1" placeholder="Filtrar..."></th>
                    <th>
                        <select class="form-select form-select-sm filter" data-column="2">
                            <option value="">Todas</option>
                            <?php 
                            $categoriasUnicas = [];
                            foreach ($partidos as $p) {
                                $categoriasUniques[$p['categoria_nombre']] = $p['categoria_nombre'];
                            }
                            foreach ($categoriasUniques as $categoria): ?>
                                <option value="<?= htmlspecialchars($categoria) ?>"><?= htmlspecialchars($categoria) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </th>
                    <th>
                        <select class="form-select form-select-sm filter" data-column="3">
                            <option value="">Todos</option>
                            <?php 
                            $tiposUnicos = [];
                            foreach ($partidos as $p) {
                                $tiposUnicos[$p['tipo_torneo_nombre']] = $p['tipo_torneo_nombre'];
                            }
                            foreach ($tiposUnicos as $tipo): ?>
                                <option value="<?= htmlspecialchars($tipo) ?>"><?= htmlspecialchars($tipo) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </th>
                    <th><input type="text" class="form-control form-control-sm filter" data-column="4" placeholder="Equipo A o B"></th>
                    <th><input type="text" class="form-control form-control-sm filter" data-column="5" placeholder="Filtrar..."></th>
                    <th>
                        <select class="form-select form-select-sm filter" data-column="6">
                            <option value="">Todos</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="en_juego">En juego</option>
                            <option value="finalizado">Finalizado</option>
                        </select>
                    </th>
                    <th class="text-center">
                        <select class="form-select form-select-sm filter" data-column="7">
                            <option value="">Todos</option>
                            <option value="con_resultado">Con resultado</option>
                            <option value="sin_resultado">Sin resultado</option>
                        </select>
                    </th>
                    <th class="text-end">
                        <button id="resetFilters" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-undo"></i>
                        </button>
                    </th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($partidos)): ?>
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">
                        No hay partidos cargados todavía.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($partidos as $partido): ?>
                    <?php
                        $id        = (int) $partido['id'];
                        $equipoA   = (string) $partido['equipo_a'];
                        $equipoB   = (string) $partido['equipo_b'];
                        $fechaHora = (string) $partido['fecha_hora'];
                        $cancha    = (string) ($partido['cancha'] ?? '');
                        $estado    = (string) $partido['estado'];
                        $catNombre = (string) $partido['categoria_nombre'];
                        $tipoTorneo = (string) $partido['tipo_torneo_nombre'];

                        $golesA = $partido['goles_equipo_a'];
                        $golesB = $partido['goles_equipo_b'];

                        if ($golesA === null || $golesB === null) {
                            $resultado = '-';
                        } else {
                            $resultado = (int) $golesA . ' - ' . (int) $golesB;
                        }

                        // Clase visual según estado
                        $badgeClass = 'secondary';
                        if ($estado === 'pendiente') {
                            $badgeClass = 'warning';
                        } elseif ($estado === 'en_juego') {
                            $badgeClass = 'info';
                        } elseif ($estado === 'finalizado') {
                            $badgeClass = 'success';
                        }
                    ?>
                    <tr>
                        <td><?= $id ?></td>
                        <td>
                            <span class="d-block small">
                                <?= htmlspecialchars($fechaHora, ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($catNombre, ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                <?= htmlspecialchars(ucfirst($tipoTorneo), ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($equipoA, ENT_QUOTES, 'UTF-8') ?></strong>
                            <span class="text-muted">vs</span>
                            <strong><?= htmlspecialchars($equipoB, ENT_QUOTES, 'UTF-8') ?></strong>
                        </td>
                        <td><?= $cancha !== '' ? htmlspecialchars($cancha, ENT_QUOTES, 'UTF-8') : '-' ?></td>
                        <td>
                            <span class="badge bg-<?= $badgeClass ?>">
                                <?= htmlspecialchars(label_estado_partido($estado), ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <?= htmlspecialchars($resultado, ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="text-end">
                            <!-- Acciones básicas: editar / eliminar (las implementaremos luego) -->
                            <a
                                href="<?= htmlspecialchars(base_url('index.php?c=partidos&a=editar&id=' . $id), ENT_QUOTES, 'UTF-8') ?>"
                                class="btn btn-sm btn-outline-secondary"
                            >
                                Editar
                            </a>
                            <a
                                href="<?= htmlspecialchars(base_url('index.php?c=partidos&a=eliminar&id=' . $id), ENT_QUOTES, 'UTF-8') ?>"
                                class="btn btn-sm btn-outline-danger"
                                onclick="return confirm('¿Seguro que querés eliminar este partido?');"
                            >
                                Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <?php if ($totalPages > 1): ?>
        <nav class="mt-3">
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a
                        class="page-link"
                        href="<?= $page <= 1 ? '#' : htmlspecialchars(base_url('index.php?c=partidos&a=index&page=' . ($page - 1)), ENT_QUOTES, 'UTF-8') ?>"
                    >
                        &laquo; Anterior
                    </a>
                </li>

                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                        <a
                            class="page-link"
                            href="<?= htmlspecialchars(base_url('index.php?c=partidos&a=index&page=' . $p), ENT_QUOTES, 'UTF-8') ?>"
                        >
                            <?= $p ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                    <a
                        class="page-link"
                        href="<?= $page >= $totalPages ? '#' : htmlspecialchars(base_url('index.php?c=partidos&a=index&page=' . ($page + 1)), ENT_QUOTES, 'UTF-8') ?>"
                    >
                        Siguiente &raquo;
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</section>

<style>
/* Estilos para los filtros */
#filters th {
    padding: 8px 4px;
    vertical-align: middle;
}
#filters .form-control,
#filters .form-select {
    font-size: 0.8rem;
    min-width: 80px;
}
#resetFilters {
    min-width: 32px;
    height: 31px;
    padding: 0.25rem 0.5rem;
}
/* Mejoras para la tabla */
.table th {
    white-space: nowrap;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = document.querySelector('table');
    const rows = table.querySelectorAll('tbody tr');
    const filters = {};
    
    // Inicializar filtros
    document.querySelectorAll('.filter').forEach(input => {
        const column = input.getAttribute('data-column');
        filters[column] = '';
        
        input.addEventListener('input', function() {
            filters[column] = this.value.toLowerCase();
            filterTable();
        });
    });
    
    // Botón para limpiar filtros
    document.getElementById('resetFilters').addEventListener('click', function() {
        document.querySelectorAll('.filter').forEach(input => {
            input.value = '';
            const column = input.getAttribute('data-column');
            filters[column] = '';
        });
        filterTable();
    });
    
    function filterTable() {
        rows.forEach(row => {
            let showRow = true;
            const cells = row.querySelectorAll('td');
            
            // Verificar cada columna contra los filtros
            Object.keys(filters).forEach(column => {
                const filterValue = filters[column];
                if (!filterValue) return;
                
                const cell = cells[column];
                if (!cell) return;
                
                let cellText = cell.textContent.toLowerCase();
                
                // Manejo especial para la columna de resultado (7)
                if (column === '7') {
                    if (filterValue === 'con_resultado' && cellText.trim() === '-') {
                        showRow = false;
                    } else if (filterValue === 'sin_resultado' && cellText.trim() !== '-') {
                        showRow = false;
                    }
                } 
                // Manejo especial para la columna de equipos (4)
                else if (column === '4') {
                    const equipos = cellText.split(' vs ');
                    if (!equipos.some(equipo => equipo.includes(filterValue))) {
                        showRow = false;
                    }
                }
                // Filtro estándar para otras columnas
                else if (!cellText.includes(filterValue)) {
                    showRow = false;
                }
            });
            
            // Mostrar u ocultar fila según los filtros
            row.style.display = showRow ? '' : 'none';
        });
    }
});
</script>
