<?php
// controladores/LoginController.php
session_start();
require_once __DIR__ . '/../modelos/Usuario.php';

class LoginController {

    public function iniciarSesion() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $correo   = trim($_POST['correo'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($correo) || empty($password)) {
                return "Por favor, completa todos los campos.";
            }

            $modeloUsuario = new Usuario();
            $usuario = $modeloUsuario->obtenerPorCorreo($correo);

            // Verificación del usuario y la contraseña (o coincidencia temporal si usas datos de prueba)
            if ($usuario && ($password === $usuario['password'] || password_verify($password, $usuario['password']))) {
                // Guardar la sesión del usuario
                $_SESSION['usuario_id']     = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_rol']    = $usuario['rol'];

                // Redireccionar al panel principal
                header('Location: ../index.html');
                exit();
            } else {
                return "Credenciales incorrectas. Verifica tu correo y contraseña.";
            }
        }
    }
}

// Ejecutar si la petición viene del formulario login.html
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new LoginController();
    $error = $controller->iniciarSesion();
    
    if (isset($error)) {
        echo "<script>alert('{$error}'); window.location.href='../login.html';</script>";
    }
}
?>