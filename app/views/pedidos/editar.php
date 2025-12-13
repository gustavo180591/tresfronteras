<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<style>
/* Estilos para Select2 */
.select2-container--bootstrap-5 .select2-selection {
    min-height: 38px;
    padding: 5px 10px;
}

.select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
    padding-left: 0;
    line-height: 28px;
}

.select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
    height: 36px;
}

/* Mejorar la visualización de los resultados */
.select2-results__option {
    padding: 8px 12px;
}

/* Ajustar el ancho del dropdown */
.select2-container--bootstrap-5 .select2-dropdown {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
</style>

<div class="col-12">
    <h6 class="text-uppercase text-xs fw-bold mb-3">Partido</h6>
    <select class="form-select form-select-sm" id="partido_id" name="partido_id" required>
        <option value="">Buscar partido...</option>
        <?php foreach ($partidos as $partido): ?>
            <option 
                value="<?= $partido['id'] ?>" 
                data-equipo-a="<?= htmlspecialchars($partido['equipo_a']) ?>" 
                data-equipo-b="<?= htmlspecialchars($partido['equipo_b']) ?>" 
                data-categoria="<?= htmlspecialchars($partido['categoria_nombre']) ?>"
                <?= (isset($pedido['partido_id']) && $pedido['partido_id'] == $partido['id']) ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($partido['categoria_nombre']) ?> - 
                <?= htmlspecialchars($partido['equipo_a']) ?> vs <?= htmlspecialchars($partido['equipo_b']) ?> 
                (<?= date('d/m/Y H:i', strtotime($partido['fecha_hora'])) ?>)
            </option>
        <?php endforeach; ?>
    </select>
    <div class="invalid-feedback">
        Por favor seleccione un partido.
    </div>
    <div class="small text-muted mt-1" id="partido_info">
        <?php if (isset($pedido['partido_id'])): ?>
            <strong><?= htmlspecialchars($pedido['categoria_nombre'] ?? '') ?></strong> - 
            <?= htmlspecialchars(($pedido['equipo_a'] ?? '') . ' vs ' . ($pedido['equipo_b'] ?? '')) ?>
        <?php else: ?>
            Seleccione un partido
        <?php endif; ?>
    </div>
</div>

<!-- jQuery (requerido por Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
// Inicializar Select2 cuando el DOM esté listo
$(document).ready(function() {
    // Inicializar Select2 en el select de partidos
    $('#partido_id').select2({
        theme: 'bootstrap-5',
        placeholder: 'Buscar partido...',
        allowClear: true,
        width: '100%',
        language: {
            noResults: function() {
                return "No se encontraron resultados";
            },
            searching: function() {
                return "Buscando...";
            },
            inputTooShort: function(args) {
                return "Ingrese al menos " + args.minimum + " caracteres";
            }
        }
    });

    // Actualizar el div de información cuando se selecciona un partido
    $('#partido_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        if (selectedOption.val()) {
            const categoria = selectedOption.data('categoria');
            const equipoA = selectedOption.data('equipo-a');
            const equipoB = selectedOption.data('equipo-b');
            $('#partido_info').html('<strong>' + categoria + '</strong> - ' + equipoA + ' vs ' + equipoB);
        } else {
            $('#partido_info').html('Seleccione un partido');
        }
    });
});
</script>