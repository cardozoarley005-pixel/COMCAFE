// js/liquidacion.js
import { baseCaficultores } from './caficultores.js';

// Elementos del DOM
const txtCedula = document.getElementById('cedula');
const txtNombre = document.getElementById('nombre');
const txtFinca = document.getElementById('finca');
const inputSacos = document.getElementById('sacos');
const inputPesoBruto = document.getElementById('peso_bruto');
const inputTara = document.getElementById('tara');
const inputPesoNeto = document.getElementById('peso_neto');
const inputHumedad = document.getElementById('humedad');
const alertHumedad = document.getElementById('alerta-humedad');
const inputFactor = document.getElementById('factor');
const lblAjuste = document.getElementById('lbl_ajuste');
const lblDescuento = document.getElementById('lbl_descuento');
const lblPrecioFinal = document.getElementById('lbl_precio_final');
const lblTotalNeto = document.getElementById('lbl_total_neto');
const formCompra = document.getElementById('form-compra');
const tablaCompras = document.getElementById('tabla-compras');

let primeraCompra = true;


// 1. Búsqueda de caficultores
txtCedula.addEventListener('input', () => {
    const cedulaVal = txtCedula.value.trim();
    if (baseCaficultores[cedulaVal]) {
        txtNombre.value = baseCaficultores[cedulaVal].nombre;
        txtFinca.value = baseCaficultores[cedulaVal].finca;
    } else {
        txtNombre.value = "";
        txtFinca.value = "";
    }
});

// 2. Cálculo de Liquidación Dinámica
function calcularLiquidacion() {
    const PRECIO_BASE = 12500;

    const sacos = parseFloat(inputSacos.value) || 0;
    const pesoBruto = parseFloat(inputPesoBruto.value) || 0;
    const taraTotal = sacos * 1.0; 
    inputTara.value = taraTotal.toFixed(1);
    
    const pesoNeto = pesoBruto - taraTotal;
    const pesoNetoFinal = pesoNeto > 0 ? pesoNeto : 0;
    inputPesoNeto.value = pesoNetoFinal.toFixed(1);

    const humedad = parseFloat(inputHumedad.value) || 0;
    let descuentoHumedad = 0;
    if (humedad > 12.0) {
        alertHumedad.style.display = 'block';
        inputHumedad.classList.add('input-alert');
        descuentoHumedad = Math.round((humedad - 12.0) * 150);
    } else {
        alertHumedad.style.display = 'none';
        inputHumedad.classList.remove('input-alert');
    }

    const factor = parseFloat(inputFactor.value) || 94;
    let ajusteFactor = Math.round((94 - factor) * 100); 

    lblAjuste.innerText = (ajusteFactor >= 0 ? '+$' : '-$') + Math.abs(ajusteFactor).toLocaleString('es-CO');
    lblAjuste.style.color = ajusteFactor >= 0 ? 'var(--secondary)' : 'var(--alert-warn)';
    
    lblDescuento.innerText = '-$' + descuentoHumedad.toLocaleString('es-CO');

    const precioFinalKg = PRECIO_BASE + ajusteFactor - descuentoHumedad;
    lblPrecioFinal.innerText = '$' + precioFinalKg.toLocaleString('es-CO');

    const totalDinero = pesoNetoFinal * precioFinalKg;
    lblTotalNeto.innerText = '$' + totalDinero.toLocaleString('es-CO', { maximumFractionDigits: 0 });
}

// Oyentes de eventos
[inputSacos, inputPesoBruto, inputHumedad, inputFactor].forEach(input => {
    input.addEventListener('input', calcularLiquidacion);
});

// 3. Envío del Formulario e Inserción en la tabla
formCompra.addEventListener('submit', (e) => {
    e.preventDefault();

    if (primeraCompra) {
        tablaCompras.innerHTML = "";
        primeraCompra = false;
    }

    const tipoSeleccionado = document.getElementById('tipo_cafe').options[document.getElementById('tipo_cafe').selectedIndex].text;
    const nuevaFila = `
        <tr>
            <td>${txtCedula.value}</td>
            <td>${txtNombre.value}</td>
            <td>${tipoSeleccionado}</td>
            <td>${inputPesoNeto.value} Kg</td>
            <td>${lblPrecioFinal.innerText}</td>
            <td style="font-weight: bold; color: var(--secondary);">${lblTotalNeto.innerText}</td>
        </tr>
    `;
    tablaCompras.innerHTML += nuevaFila;
    resetearFormulario();
});

function resetearFormulario() {
    formCompra.reset();
    calcularLiquidacion();
    txtCedula.focus();
}