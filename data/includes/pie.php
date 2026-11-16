<?php
/* =====================================================================
   includes/pie.php  →  parte de ABAJO del marco
   Cierra el contenido, dibuja el pie (si MOSTRAR_PIE = true en
   config/config.php) y carga los JavaScript.

   Una página puede cargar su propio JS así (antes de incluir pie.php):
        $scripts = ['assets/js/cobrar.js'];
   ===================================================================== */
?>
        </main> <!-- fin del contenido -->
    </div> <!-- fin del cuerpo -->

    <?php if (MOSTRAR_PIE): ?>
    <!-- PIE DE PÁGINA (se activa/desactiva en config/config.php) -->
    <footer class="pie">©2026 Derechos reservados al equipo YEFIRO | <?php echo e(NOMBRE_SISTEMA); ?> </footer>
    <?php endif; ?>

    <!-- ===== BOOTSTRAP (JavaScript) ===== -->
    <?= bootstrap_js() ?>

    <!-- ===== JS general: validación de formularios y confirmaciones ===== -->
    <script src="<?= url('assets/js/validacion.js') ?>"></script>

    <!-- ===== JS propio de la página (si lo hay) ===== -->
    <?php if (!empty($scripts)): foreach ($scripts as $s): ?>
        <script src="<?= url($s) ?>?v=<?= filemtime(RUTA_RAIZ . '/' . $s) ?>"></script>
    <?php endforeach; endif; ?>
</body>
</html>
