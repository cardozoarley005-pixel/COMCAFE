<?php
// controladores/CompraController.php
header('Content-Type: application/json');

// Habilitar reporte de errores para diagnóstico
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Parámetros directos de conexión a XAMPP / MySQL
$host = 'localhost';
$dbname = 'comcafe_db';
$username = 'root';
$password = '';

try {
    $conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión MySQL: ' . $e->getMessage()]);
    exit();
}

$metodo = $_SERVER['REQUEST_METHOD'];

// 1. CONSULTAR COMPRAS (Petición GET al cargar la página)
if ($metodo === 'GET') {
    try {
        $stmt = $conexion->prepare("SELECT * FROM compras ORDER BY id DESC");
        $stmt->execute();
        $compras = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'data' => $compras]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error en consulta: ' . $e->getMessage()]);
    }
    exit();
}

// 2. INSERTAR NUEVA COMPRA (Petición POST)
if ($metodo === 'POST') {
    $inputJSON = file_get_contents('php://input');
    $datos = json_decode($inputJSON, true);

    if (!$datos) {
        echo json_encode(['success' => false, 'message' => 'Datos JSON recibidos están vacíos']);
        exit();
    }

    try {
        $sql = "INSERT INTO compras 
                (cedula_caficultor, nombre_caficultor, finca, tipo_cafe, sacos, peso_bruto, tara, peso_neto, humedad, broca, defectos, factor_rendimiento, ajuste_factor, descuento_humedad, precio_final_kg, total_neto) 
                VALUES 
                (:cedula, :nombre, :finca, :tipo_cafe, :sacos, :peso_bruto, :tara, :peso_neto, :humedad, :broca, :defectos, :factor, :ajuste_factor, :descuento_humedad, :precio_final_kg, :total_neto)";
        
        $stmt = $conexion->prepare($sql);
        $exito = $stmt->execute([
            ':cedula'            => $datos['cedula'] ?? '',
            ':nombre'            => $datos['nombre'] ?? '',
            ':finca'             => $datos['finca'] ?? '',
            ':tipo_cafe'         => $datos['tipo_cafe'] ?? 'pergamino',
            ':sacos'             => $datos['sacos'] ?? 0,
            ':peso_bruto'        => $datos['peso_bruto'] ?? 0,
            ':tara'              => $datos['tara'] ?? 0,
            ':peso_neto'         => $datos['peso_neto'] ?? 0,
            ':humedad'           => $datos['humedad'] ?? 0,
            ':broca'             => $datos['broca'] ?? 0,
            ':defectos'          => $datos['defectos'] ?? 0,
            ':factor'            => $datos['factor'] ?? 94,
            ':ajuste_factor'     => $datos['ajuste_factor'] ?? 0,
            ':descuento_humedad' => $datos['descuento_humedad'] ?? 0,
            ':precio_final_kg'   => $datos['precio_final_kg'] ?? 0,
            ':total_neto'        => $datos['total_neto'] ?? 0
        ]);

        if ($exito) {
            echo json_encode(['success' => true, 'message' => 'Compra registrada con éxito en MySQL']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo guardar la compra']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error SQL: ' . $e->getMessage()]);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Método no permitido']);
?>