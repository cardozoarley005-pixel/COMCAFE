// js/registro.js

document.addEventListener('DOMContentLoaded', () => {
    const formRegistro = document.getElementById('form-registro');

    formRegistro?.addEventListener('submit', async (e) => {
        e.preventDefault();

        const datos = {
            nombre: document.getElementById('nombre').value.trim(),
            correo: document.getElementById('correo').value.trim(),
            rol: document.getElementById('rol').value,
            password: document.getElementById('password').value.trim()
        };

        try {
            const res = await fetch('controladores/RegistroController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datos)
            });

            const r = await res.json();

            if (r.success) {
                alert('✅ ' + r.message);
                window.location.href = 'login.html';
            } else {
                alert('❌ ' + r.message);
            }
        } catch (error) {
            alert('⚠️ Error de conexión al registrar el usuario.');
        }
    });
});