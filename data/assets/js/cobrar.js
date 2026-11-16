/* =====================================================================
   assets/js/cobrar.js  →  LÓGICA DE LA PANTALLA DE COBRO (carrito)
   - Dibuja los productos (con filtro por sección y buscador)
   - Maneja el carrito: agregar, quitar, cambiar cantidad, total
   - Calcula el cambio cuando se paga en efectivo
   - Deja el carrito listo en un campo oculto para enviarlo al servidor
   (El servidor vuelve a calcular precios y stock: nunca confía en el navegador.)
   ===================================================================== */
(function () {
    // Productos que manda PHP desde modulos/ventas/cobrar.php
    const productos = window.PRODUCTOS || [];
    const carrito = {};              // { id_producto: cantidad }
    let seccionActual = 'todas';

    const $ = (id) => document.getElementById(id);
    const dinero = (n) => '$' + Number(n).toFixed(2);

    // ---------- Productos (columna izquierda) ----------
    function dibujarProductos() {
        const texto = $('buscar').value.trim().toLowerCase();
        const cont = $('lista-productos');
        cont.innerHTML = '';

        productos
            .filter(p => (seccionActual === 'todas' || p.categoria === seccionActual)
                      && p.nombre.toLowerCase().includes(texto))
            .forEach(p => {
                const enCarrito = carrito[p.id] || 0;
                const disponible = p.stock - enCarrito;

                const col = document.createElement('div');
                col.className = 'col-6 col-xl-4';

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-outline-secondary w-100 h-100 text-start tarjeta-producto';
                btn.disabled = disponible <= 0;
                btn.addEventListener('click', () => agregar(p.id));

                const nombre = document.createElement('div');
                nombre.className = 'fw-bold';
                nombre.textContent = p.nombre;                 // textContent evita inyección de HTML

                const precio = document.createElement('div');
                precio.className = 'fw-bold text-success';
                precio.textContent = dinero(p.precio);

                const stock = document.createElement('div');
                stock.className = 'small text-muted';
                stock.textContent = 'Disponibles: ' + disponible;

                btn.append(nombre, precio, stock);
                col.appendChild(btn);
                cont.appendChild(col);
            });

        if (!cont.children.length) {
            cont.innerHTML = '<div class="text-muted p-3">No hay productos en esta sección.</div>';
        }
    }

    // ---------- Carrito ----------
    function agregar(id) {
        const p = productos.find(x => x.id === id);
        if ((carrito[id] || 0) + 1 > p.stock) return;           // no pasar del stock
        carrito[id] = (carrito[id] || 0) + 1;
        dibujarCarrito();
    }
    function cambiar(id, delta) {
        const p = productos.find(x => x.id === id);
        const nueva = (carrito[id] || 0) + delta;
        if (nueva <= 0) { delete carrito[id]; }
        else if (nueva <= p.stock) { carrito[id] = nueva; }
        dibujarCarrito();
    }
    function quitar(id) { delete carrito[id]; dibujarCarrito(); }

    function totalActual() {
        return Object.keys(carrito).reduce((suma, id) => {
            const p = productos.find(x => x.id === Number(id));
            return suma + p.precio * carrito[id];
        }, 0);
    }

    function dibujarCarrito() {
        const tbody = $('carrito-filas');
        tbody.innerHTML = '';

        Object.keys(carrito).forEach(idTxt => {
            const id = Number(idTxt);
            const p = productos.find(x => x.id === id);
            const tr = document.createElement('tr');

            const tdNombre = document.createElement('td');
            tdNombre.textContent = p.nombre;

            const tdCant = document.createElement('td');
            tdCant.className = 'text-nowrap';
            const menos = document.createElement('button');
            menos.type = 'button'; menos.className = 'btn btn-sm btn-outline-secondary'; menos.textContent = '−';
            menos.addEventListener('click', () => cambiar(id, -1));
            const num = document.createElement('span');
            num.className = 'mx-2'; num.textContent = carrito[id];
            const mas = document.createElement('button');
            mas.type = 'button'; mas.className = 'btn btn-sm btn-outline-secondary'; mas.textContent = '+';
            mas.addEventListener('click', () => cambiar(id, 1));
            tdCant.append(menos, num, mas);

            const tdSub = document.createElement('td');
            tdSub.className = 'text-end'; tdSub.textContent = dinero(p.precio * carrito[id]);

            const tdX = document.createElement('td');
            const x = document.createElement('button');
            x.type = 'button'; x.className = 'btn btn-sm btn-outline-danger'; x.textContent = '✕';
            x.addEventListener('click', () => quitar(id));
            tdX.appendChild(x);

            tr.append(tdNombre, tdCant, tdSub, tdX);
            tbody.appendChild(tr);
        });

        $('carrito-vacio').classList.toggle('d-none', Object.keys(carrito).length > 0);
        $('total').textContent = dinero(totalActual());

        // Carrito en formato JSON dentro del campo oculto que se envía al servidor
        $('campo-carrito').value = JSON.stringify(
            Object.keys(carrito).map(id => ({ id: Number(id), cantidad: carrito[id] }))
        );
        calcularCambio();
        dibujarProductos();
    }

    // ---------- Pago ----------
    function metodoPago() {
        return document.querySelector('input[name="metodo_pago"]:checked').value;
    }
    function calcularCambio() {
        const esEfectivo = metodoPago() === 'Efectivo';
        $('bloque-efectivo').classList.toggle('d-none', !esEfectivo);
        const recibido = parseFloat($('recibido').value) || 0;
        const cambio = recibido - totalActual();
        $('cambio').textContent = cambio >= 0 ? dinero(cambio) : '—';
    }

    // ---------- Eventos ----------
    $('buscar').addEventListener('input', dibujarProductos);
    document.querySelectorAll('[data-seccion]').forEach(btn => {
        btn.addEventListener('click', () => {
            seccionActual = btn.getAttribute('data-seccion');
            document.querySelectorAll('[data-seccion]').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            dibujarProductos();
        });
    });
    document.querySelectorAll('input[name="metodo_pago"]').forEach(r => r.addEventListener('change', calcularCambio));
    $('recibido').addEventListener('input', calcularCambio);
    $('btn-exacto').addEventListener('click', () => { $('recibido').value = totalActual().toFixed(2); calcularCambio(); });

    // Antes de enviar: revisar que haya productos y que alcance el efectivo
    $('form-cobro').addEventListener('submit', function (e) {
        if (Object.keys(carrito).length === 0) {
            e.preventDefault(); alert('Agrega al menos un producto al carrito.'); return;
        }
        if (metodoPago() === 'Efectivo' && (parseFloat($('recibido').value) || 0) < totalActual()) {
            e.preventDefault(); alert('El efectivo recibido no alcanza para pagar el total.');
        }
    });

    dibujarProductos();
    dibujarCarrito();
})();
