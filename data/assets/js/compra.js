/* =====================================================================
   assets/js/compra.js  →  JavaScript de la pantalla "Nueva compra"
   Agrega / quita filas de insumos y calcula el total de la compra.
   ===================================================================== */
(function () {
    const filas = document.getElementById('filas-compra');
    const plantilla = document.getElementById('fila-plantilla');

    // Suma cantidad × costo de cada fila y muestra el total
    function recalcular() {
        let total = 0;
        filas.querySelectorAll('tr').forEach(function (tr) {
            const cant = parseFloat(tr.querySelector('.cant').value) || 0;
            const costo = parseFloat(tr.querySelector('.costo').value) || 0;
            total += cant * costo;
        });
        document.getElementById('total-compra').textContent = '$' + total.toFixed(2);
    }

    // Agrega una fila nueva copiando la plantilla
    function agregarFila() {
        filas.appendChild(plantilla.content.cloneNode(true));
        recalcular();
    }

    // Eventos dentro de la tabla: cambiar producto (pone su costo), escribir, quitar fila
    filas.addEventListener('change', function (e) {
        if (e.target.classList.contains('producto')) {
            const costo = e.target.selectedOptions[0].getAttribute('data-costo') || 0;
            e.target.closest('tr').querySelector('.costo').value = costo;
            recalcular();
        }
    });
    filas.addEventListener('input', recalcular);
    filas.addEventListener('click', function (e) {
        if (e.target.classList.contains('quitar')) {
            e.target.closest('tr').remove();
            recalcular();
        }
    });

    document.getElementById('agregar-fila').addEventListener('click', agregarFila);
    agregarFila();   // una fila inicial
})();
