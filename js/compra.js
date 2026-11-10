document.addEventListener('DOMContentLoaded', () => {
    const formCompra = document.getElementById('form-compra');

    formCompra?.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Extraer y estructurar los campos con las llaves que exige CompraController.php
        const datos = {
            cedula: document.getElementById('cedula').value.trim(),
            nombre: document.getElementById('nombre').value.trim(),
            finca: document.getElementById('finca').value.trim(),
            tipo_cafe: document.getElementById('tipo_cafe').value || 'pergamino',
            sacos: parseInt(document.getElementById('sacos').value) || 0,
            peso_bruto: parseFloat(document.getElementById('peso_bruto').value) || 0,
            tara: parseFloat(document.getElementById('tara').value) || 0,
            peso_neto: parseFloat(document.getElementById('peso_neto').value) || 0,
            humedad: parseFloat(document.getElementById('humedad').value) || 0,
            broca: parseFloat(document.getElementById('broca').value) || 0,
            defectos: parseFloat(document.getElementById('defectos').value) || 0,
            factor: parseFloat(document.getElementById('factor').value) || 94,
            ajuste_factor: parseFloat(document.getElementById('ajuste_factor').value) || 0,
            descuento_humedad: parseFloat(document.getElementById('descuento_humedad').value) || 0,
            precio_final_kg: parseFloat(document.getElementById('precio_final_kg').value) || 0,
            total_neto: parseFloat(document.getElementById('total_neto').value) || 0
        };

        try {
            const res = await fetch('../controladores/CompraController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datos)
            });

            const r = await res.json();

            if (r.success) {
                alert('✅ ' + r.message);
                formCompra.reset();
            } else {
                alert('❌ ' + r.message);
            }
        } catch (error) {
            alert('⚠️ Error de conexión al registrar la compra.');
        }
    });
});