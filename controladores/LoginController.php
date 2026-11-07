<?php
// controladores/LoginController.php
session_start();
header('Content-Type: application/json');

ini_set('display_errors', 0);
error_reporting(E_ALL);

// Parámetros de conexión a XAMPP / MySQL
$host = 'localhost';
$dbname = 'comcafe_db';
$username = 'root';
$password = '';

try {
    $conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión con MySQL']);
    exit();
}

// Procesar inicio de sesión cuando viene por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $correo = $input['correo'] ?? '';
    $pass = $input['password'] ?? '';

    if (empty($correo) || empty($pass)) {
        echo json_encode(['success' => false, 'message' => 'Por favor complete todos los campos']);
        exit();
    }

    try {
        $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE correo = :correo AND password = :password");
        $stmt->execute([
            ':correo' => $correo,
            ':password' => $pass
        ]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            // Guardar datos clave en la sesión de PHP
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_rol'] = $usuario['rol'];

            echo json_encode(['success' => true, 'message' => 'Login exitoso']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al consultar la base de datos']);
    }
    exit();
}
?>