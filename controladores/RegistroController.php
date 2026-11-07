<?php
// controladores/RegistroController.php
header('Content-Type: application/json');

ini_set('display_errors', 0);
error_reporting(E_ALL);

$host = 'localhost';
$dbname = 'comcafe_db';
$username = 'root';
$password = '';

try {
    $conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión con la base de datos']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $nombre = trim($input['nombre'] ?? '');
    $correo = trim($input['correo'] ?? '');
    $rol = trim($input['rol'] ?? 'productor');
    $pass = trim($input['password'] ?? '');

    if (empty($nombre) || empty($correo) || empty($pass) || empty($rol)) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
        exit();
    }

    try {
        // Verificar si el correo ya existe
        $checkStmt = $conexion->prepare("SELECT id FROM usuarios WHERE correo = :correo");
        $checkStmt->execute([':correo' => $correo]);
        if ($checkStmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'El correo electrónico ya se encuentra registrado']);
            exit();
        }

        // Insertar nuevo usuario
        $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, correo, password, rol) VALUES (:nombre, :correo, :password, :rol)");
        $stmt->execute([
            ':nombre' => $nombre,
            ':correo' => $correo,
            ':password' => $pass,
            ':rol' => $rol
        ]);

        echo json_encode(['success' => true, 'message' => 'Usuario registrado exitosamente']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al registrar el usuario']);
    }
    exit();
}
?>