<?php
// app/views/partidos/form.php
declare(strict_types=1);

// Variables del controlador:
// - $partido: array con los datos del partido
// - $categorias: array de categorías para el select
// - $errores: array con mensajes de error (opcional)

$errores = $errores ?? [];

// Usar ruta base relativa
$basePath = dirname($_SERVER['PHP_SELF']);
$baseUrl = rtrim(str_replace('index.php', '', $basePath), '/');
?>

<section class="mb-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h4 class="h5 mb-0">
                            <?= isset($partido['id']) ? 'Editar' : 'Nuevo' ?> Partido
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($errores)): ?>
                            <div class="alert alert-danger">
                                <strong>Por favor, corregí los siguientes errores:</strong>
                                <ul class="mb-0">
                                    <?php foreach ($errores as $error): ?>
                                        <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="<?= htmlspecialchars('index.php?c=partidos&a=' . (isset($partido['id']) ? 'actualizar&id=' . $partido['id'] : 'guardar'), ENT_QUOTES, 'UTF-8') ?>">
                            <div class="row g-3">
                                <!-- Categoría -->
                                <div class="col-md-6">
                                    <label for="categoria_id" class="form-label">Categoría <span class="text-danger">*</span></label>
                                    <select class="form-select" id="categoria_id" name="categoria_id" required>
                                        <option value="">Seleccionar categoría</option>
                                        <?php foreach ($categorias as $categoria): ?>
                                            <option 
                                                value="<?= (int) $categoria['id'] ?>"
                                                <?= ($partido['categoria_id'] ?? '') == $categoria['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($categoria['nombre'] . ' (' . ucfirst($categoria['tipo_torneo']) . ')', ENT_QUOTES, 'UTF-8') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Fecha y Hora -->
                                <div class="col-md-6">
                                    <label for="fecha_hora" class="form-label">Fecha y Hora <span class="text-danger">*</span></label>
                                    <input 
                                        type="datetime-local" 
                                        class="form-control" 
                                        id="fecha_hora" 
                                        name="fecha_hora" 
                                        value="<?= htmlspecialchars($partido['fecha_hora'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                        required>
                                </div>

                                <!-- Equipo Local -->
                                <div class="col-md-6">
                                    <label for="equipo_a" class="form-label">Equipo Local <span class="text-danger">*</span></label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        id="equipo_a" 
                                        name="equipo_a" 
                                        value="<?= htmlspecialchars($partido['equipo_a'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                        required>
                                </div>

                                <!-- Equipo Visitante -->
                                <div class="col-md-6">
                                    <label for="equipo_b" class="form-label">Equipo Visitante <span class="text-danger">*</span></label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        id="equipo_b" 
                                        name="equipo_b" 
                                        value="<?= htmlspecialchars($partido['equipo_b'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                        required>
                                </div>

                                <!-- Cancha -->
                                <div class="col-md-6">
                                    <label for="cancha" class="form-label">Cancha</label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        id="cancha" 
                                        name="cancha" 
                                        value="<?= htmlspecialchars($partido['cancha'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                </div>

                                <!-- Estado -->
                                <div class="col-md-6">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select class="form-select" id="estado" name="estado">
                                        <option value="pendiente" <?= ($partido['estado'] ?? 'pendiente') === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                        <option value="en_juego" <?= ($partido['estado'] ?? '') === 'en_juego' ? 'selected' : '' ?>>En Juego</option>
                                        <option value="finalizado" <?= ($partido['estado'] ?? '') === 'finalizado' ? 'selected' : '' ?>>Finalizado</option>
                                    </select>
                                </div>

                                <!-- Ronda y Número en Ronda -->
                                <div class="col-md-6">
                                    <label for="ronda" class="form-label">Ronda</label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        id="ronda" 
                                        name="ronda" 
                                        placeholder="Ej: Cuartos de Final, Semifinal, Final"
                                        value="<?= htmlspecialchars($partido['ronda'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                </div>

                                <div class="col-md-6">
                                    <label for="numero_en_ronda" class="form-label">Número en Ronda</label>
                                    <input 
                                        type="number" 
                                        class="form-control" 
                                        id="numero_en_ronda" 
                                        name="numero_en_ronda" 
                                        min="1"
                                        value="<?= isset($partido['numero_en_ronda']) ? (int) $partido['numero_en_ronda'] : '' ?>">
                                </div>

                                <!-- Observaciones -->
                                <div class="col-12">
                                    <label for="observaciones" class="form-label">Observaciones</label>
                                    <textarea 
                                        class="form-control" 
                                        id="observaciones" 
                                        name="observaciones" 
                                        rows="3"><?= htmlspecialchars($partido['observaciones'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                                </div>

                                <!-- Botones -->
                                <div class="col-12 mt-4">
                                    <div class="d-flex justify-content-between">
                                        <a href="<?= htmlspecialchars('index.php?c=partidos&a=index', ENT_QUOTES, 'UTF-8') ?>" class="btn btn-outline-secondary">
                                            <i class="bi bi-arrow-left me-1"></i> Volver al listado
                                        </a>
                                        <div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-save me-1"></i> Guardar Partido
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
