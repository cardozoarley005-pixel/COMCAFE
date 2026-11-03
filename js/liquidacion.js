// js/liquidacion.js
const txtCedula = document.getElementById('cedula');
const txtNombre = document.getElementById('nombre');
const txtFinca = document.getElementById('finca');
const inputSacos = document.getElementById('sacos');
const inputPesoBruto = document.getElementById('peso_bruto');
const inputTara = document.getElementById('tara');
const inputPesoNeto = document.getElementById('peso_neto');
const inputHumedad = document.getElementById('humedad');
const inputFactor = document.getElementById('factor');
const lblPrecioFinal = document.getElementById('lbl_precio_final');
const lblTotalNeto = document.getElementById('lbl_total_neto');
const formCompra = document.getElementById('form-compra');
const tablaCompras = document.getElementById('tabla-compras');

let valPrecioFinalKg = 12500, valTotalNeto = 0;

// 1. Cargar historial desde MySQL
document.addEventListener('DOMContentLoaded', async () => {
    if (!tablaCompras) return;
    try {
        const res = await fetch('controladores/CompraController.php');
        const data = await res.json();
        if (data.success && data.data) {
            tablaCompras.innerHTML = data.data.map(c => `
                <tr>
                    <td>${c.cedula_caficultor}</td>
                    <td>${c.nombre_caficultor}</td>
                    <td style="text-transform: capitalize;">${c.tipo_cafe}</td>
                    <td>${parseFloat(c.peso_neto).toFixed(1)} Kg</td>
                    <td>$${parseInt(c.precio_final_kg).toLocaleString('es-CO')}</td>
                    <td style="font-weight: bold; color: var(--secondary);">$${parseInt(c.total_neto).toLocaleString('es-CO')}</td>
                </tr>
            `).join('');
        }
    } catch (e) { console.error(e); }
});

// 2. Autocompletar Caficultor
txtCedula?.addEventListener('input', () => {
    const c = txtCedula.value.trim();
    txtNombre.value = baseCaficultores?.[c]?.nombre || '';
    txtFinca.value = baseCaficultores?.[c]?.finca || '';
});

// 3. Cálculo de Liquidación
function calcularLiquidacion() {
    const sacos = parseFloat(inputSacos?.value) || 0;
    const bruto = parseFloat(inputPesoBruto?.value) || 0;
    const tara = sacos * 1.0;
    if (inputTara) inputTara.value = tara.toFixed(1);
    
    const neto = Math.max(0, bruto - tara);
    if (inputPesoNeto) inputPesoNeto.value = neto.toFixed(1);

    const humedad = parseFloat(inputHumedad?.value) || 12;
    const descHumedad = humedad > 12 ? Math.round((humedad - 12) * 150) : 0;
    const factor = parseFloat(inputFactor?.value) || 94;
    const ajFactor = Math.round((94 - factor) * 100);

    valPrecioFinalKg = 12500 + ajFactor - descHumedad;
    valTotalNeto = neto * valPrecioFinalKg;

    if (lblPrecioFinal) lblPrecioFinal.innerText = '$' + valPrecioFinalKg.toLocaleString('es-CO');
    if (lblTotalNeto) lblTotalNeto.innerText = '$' + valTotalNeto.toLocaleString('es-CO', { maximumFractionDigits: 0 });
}

[inputSacos, inputPesoBruto, inputHumedad, inputFactor].forEach(i => i?.addEventListener('input', calcularLiquidacion));

// 4. Generar PDF compacto (Tirilla)
function generarPDFCompra(d) {
    if (!window.jspdf) return;
    const doc = new window.jspdf.jsPDF({ unit: 'mm', format: [80, 140] });
    
    doc.setFont("helvetica", "bold").setFontSize(12);
    doc.text("☕ COMCAFE", 40, 10, { align: "center" });
    doc.setFontSize(8).setFont("helvetica", "normal");
    doc.text(`Fecha: ${new Date().toLocaleDateString('es-CO')}`, 5, 18);
    doc.text(`Cliente: ${d.nombre} (${d.cedula})`, 5, 24);
    doc.text(`Finca: ${d.finca}`, 5, 30);
    doc.text("--------------------------------------------------", 40, 35, { align: "center" });
    
    doc.text(`Tipo Café: ${d.tipo_cafe}`, 5, 42);
    doc.text(`Peso Bruto: ${d.peso_bruto} Kg | Tara: ${d.tara} Kg`, 5, 48);
    doc.setFont("helvetica", "bold").text(`Peso Neto: ${d.peso_neto} Kg`, 5, 54);
    doc.setFont("helvetica", "normal").text(`Factor: ${d.factor} | Humedad: ${d.humedad}%`, 5, 60);
    doc.text(`Precio/Kg: $${d.precio_final_kg.toLocaleString('es-CO')}`, 5, 66);
    doc.text("--------------------------------------------------", 40, 72, { align: "center" });
    
    doc.setFontSize(10).setFont("helvetica", "bold");
    doc.text(`TOTAL: $${d.total_neto.toLocaleString('es-CO')}`, 5, 80);
    doc.save(`Recibo_${d.cedula}.pdf`);
}

// 5. Envío del Formulario
formCompra?.addEventListener('submit', async (e) => {
    e.preventDefault();
    calcularLiquidacion();

    const datos = {
        cedula: txtCedula.value.trim(),
        nombre: txtNombre.value.trim(),
        finca: txtFinca.value.trim(),
        tipo_cafe: document.getElementById('tipo_cafe')?.value || 'pergamino',
        sacos: parseFloat(inputSacos?.value) || 0,
        peso_bruto: parseFloat(inputPesoBruto?.value) || 0,
        tara: parseFloat(inputTara?.value) || 0,
        peso_neto: parseFloat(inputPesoNeto?.value) || 0,
        humedad: parseFloat(inputHumedad?.value) || 0,
        broca: parseFloat(document.getElementById('broca')?.value) || 0,
        defectos: parseInt(document.getElementById('defectos')?.value) || 0,
        factor: parseInt(inputFactor?.value) || 94,
        ajuste_factor: 0,
        descuento_humedad: 0,
        precio_final_kg: valPrecioFinalKg,
        total_neto: valTotalNeto
    };

    try {
        const res = await fetch('controladores/CompraController.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        });
        const r = await res.json();
        if (r.success) {
            alert('✅ ' + r.message);
            generarPDFCompra(datos);
            location.reload();
        } else {
            alert('❌ ' + r.message);
        }
    } catch (err) {
        alert('⚠️ Error de conexión con el servidor.');
    }
});