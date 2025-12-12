<?php
// app/views/layout/footer.php

declare(strict_types=1);
?>

</main>

<footer class="border-top bg-white text-muted small py-2 mt-auto">
    <div class="container-fluid px-3 d-flex justify-content-between">
        <span>
            <?= htmlspecialchars(defined('EVENT_NAME') ? EVENT_NAME : 'Tresfronteras', ENT_QUOTES, 'UTF-8') ?>
            &middot;
            Panel de control
        </span>
        <span>
            &copy; <?= date('Y') ?>
        </span>
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
