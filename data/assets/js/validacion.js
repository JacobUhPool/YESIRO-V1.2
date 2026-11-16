/* =====================================================================
   assets/js/validacion.js  →  JavaScript GENERAL (se carga en todas las páginas)
   1) Validación de formularios con la clase "needs-validation"
   2) Confirmación antes de eliminar (formularios con data-confirmar="...")
   ===================================================================== */

// 1) Si falta algo en el formulario, se marca en rojo y NO se envía
document.querySelectorAll('form.needs-validation').forEach(function (form) {
    form.addEventListener('submit', function (evento) {
        if (!form.checkValidity()) {
            evento.preventDefault();
            evento.stopPropagation();
        }
        form.classList.add('was-validated');   // Bootstrap pinta rojo/verde
    });
});

// 2) Formularios con data-confirmar="¿Eliminar?" piden confirmación
document.querySelectorAll('form[data-confirmar]').forEach(function (form) {
    form.addEventListener('submit', function (evento) {
        if (!confirm(form.getAttribute('data-confirmar'))) {
            evento.preventDefault();               // el usuario canceló
        }
    });
});
