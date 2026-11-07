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

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'POST') {
    $inputJSON = file_get_contents('php://input');
    $datos = json_decode($inputJSON, true);

    $correo = trim($datos['correo'] ?? '');
    $pass = trim($datos['password'] ?? '');

    if (empty($correo) || empty($pass)) {
        echo json_encode(['success' => false, 'message' => 'Por favor completa todos los campos.']);
        exit();
    }

    try {
        // Buscar el usuario por su correo
        $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE correo = :correo LIMIT 1");
        $stmt->execute([':correo' => $correo]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            // Validar la contraseña (soporta texto plano o password_verify de PHP)
            $passwordValida = ($pass === $usuario['password']) || password_verify($pass, $usuario['password']);

            if ($passwordValida) {
                $_SESSION['usuario_id'] = $usuario['id'] ?? null;
                $_SESSION['usuario_nombre'] = $usuario['nombre'] ?? $usuario['correo'];
                $_SESSION['usuario_rol'] = $usuario['rol'] ?? 'operador';

                echo json_encode(['success' => true, 'message' => 'Acceso concedido']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Contraseña incorrecta.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'El correo electrónico no está registrado.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error en consulta: ' . $e->getMessage()]);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Método no permitido']);
?>