<?php
// app/views/partidos/crear.php

// Variables que más adelante podemos pasar desde el controlador.
// De momento, las dejamos preparadas.
$categorias = isset($categorias) && is_array($categorias) ? $categorias : [];
$estados = array(
    'pendiente'  => 'Pendiente',
    'en_juego'   => 'En juego',
    'finalizado' => 'Finalizado',
);
?>

<section class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="h5 mb-0">Agregar partido</h2>
            <small class="text-muted">Cargar un nuevo partido al fixture</small>
        </div>

        <a
            href="<?= htmlspecialchars(base_url('index.php?c=partidos&a=index'), ENT_QUOTES, 'UTF-8') ?>"
            class="btn btn-sm btn-outline-secondary"
        >
            Volver al fixture
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form
                method="post"
                action="<?= htmlspecialchars(base_url('index.php?c=partidos&a=guardar'), ENT_QUOTES, 'UTF-8') ?>"
                class="row g-3"
            >
                <!-- Categoría -->
                <div class="col-12 col-md-4">
                    <label for="categoria_id" class="form-label form-label-sm">Categoría</label>
                    <select
                        name="categoria_id"
                        id="categoria_id"
                        class="form-select form-select-sm"
                        required
                    >
                        <option value="">Seleccionar...</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option
                                value="<?= (int) $cat['id'] ?>"
                            >
                                <?= htmlspecialchars($cat['nombre'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Equipo A -->
                <div class="col-12 col-md-4">
                    <label for="equipo_a" class="form-label form-label-sm">Equipo A</label>
                    <input
                        type="text"
                        name="equipo_a"
                        id="equipo_a"
                        class="form-control form-control-sm"
                        required
                    >
                </div>

                <!-- Equipo B -->
                <div class="col-12 col-md-4">
                    <label for="equipo_b" class="form-label form-label-sm">Equipo B</label>
                    <input
                        type="text"
                        name="equipo_b"
                        id="equipo_b"
                        class="form-control form-control-sm"
                        required
                    >
                </div>

                <!-- Fecha y hora -->
                <div class="col-12 col-md-4">
                    <label for="fecha_hora" class="form-label form-label-sm">Fecha y hora</label>
                    <input
                        type="datetime-local"
                        name="fecha_hora"
                        id="fecha_hora"
                        class="form-control form-control-sm"
                        required
                    >
                </div>

                <!-- Cancha -->
                <div class="col-12 col-md-4">
                    <label for="cancha" class="form-label form-label-sm">Cancha</label>
                    <input
                        type="text"
                        name="cancha"
                        id="cancha"
                        class="form-control form-control-sm"
                        placeholder="Cancha 1, Cancha 2..."
                    >
                </div>

                <!-- Estado -->
                <div class="col-12 col-md-4">
                    <label for="estado" class="form-label form-label-sm">Estado</label>
                    <select
                        name="estado"
                        id="estado"
                        class="form-select form-select-sm"
                        required
                    >
                        <?php foreach ($estados as $value => $label): ?>
                            <option value="<?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Observaciones -->
                <div class="col-12">
                    <label for="observaciones" class="form-label form-label-sm">Observaciones</label>
                    <textarea
                        name="observaciones"
                        id="observaciones"
                        class="form-control form-control-sm"
                        rows="2"
                        placeholder="Notas sobre el partido, cambios, etc."
                    ></textarea>
                </div>

                <!-- Botones -->
                <div class="col-12 d-flex justify-content-end gap-2 pt-2">
                    <a
                        href="<?= htmlspecialchars(base_url('index.php?c=partidos&a=index'), ENT_QUOTES, 'UTF-8') ?>"
                        class="btn btn-sm btn-outline-secondary"
                    >
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-sm btn-primary">
                        Guardar partido
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
