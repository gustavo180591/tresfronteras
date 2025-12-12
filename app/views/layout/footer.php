<?php
// app/views/layout/footer.php

declare(strict_types=1);
?>

</main>

<footer class="border-top bg-white text-muted small py-3 mt-4">
    <div class="container">
        <div class="text-center mb-2">
            <img src="<?= base_url('assets/logo.png') ?>" alt="TORNEO TRES FRONTERAS" style="height: 50px; width: auto; margin-bottom: 10px;">
        </div>
        <div class="text-center">
            <p class="mb-1">TORNEO TRES FRONTERAS &copy; <?= date('Y') ?></p>
            <p class="mb-0">Panel de control - Gestión de pedidos y fotos</p>
        </div>
    </div>
</footer>

<!-- Bootstrap JS (bundle con Popper) -->
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"
></script>

<!-- JS propio -->
<script src="<?= htmlspecialchars(base_url('js/app.js'), ENT_QUOTES, 'UTF-8') ?>"></script>

</body>
</html>
