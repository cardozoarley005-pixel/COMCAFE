// js/login.js
const formLogin = document.getElementById('form-login');
const txtCorreo = document.getElementById('correo');
const txtPassword = document.getElementById('password');
const alertaError = document.getElementById('alerta-error');

if (formLogin) {
    formLogin.addEventListener('submit', async (e) => {
        e.preventDefault();

        if (alertaError) alertaError.style.display = 'none';

        const datosLogin = {
            correo: txtCorreo.value.trim(),
            password: txtPassword.value.trim()
        };

        try {
            const respuesta = await fetch('controladores/LoginController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datosLogin)
            });

            const resultado = await respuesta.json();

            if (resultado.success) {
                // Redirigir al panel de compras
                window.location.href = 'index.html';
            } else {
                if (alertaError) {
                    alertaError.innerText = '❌ ' + resultado.message;
                    alertaError.style.display = 'block';
                }
            }
        } catch (error) {
            console.error('Error:', error);
            if (alertaError) {
                alertaError.innerText = '⚠️ Error al conectar con el servidor PHP.';
                alertaError.style.display = 'block';
            }
        }
    });
}