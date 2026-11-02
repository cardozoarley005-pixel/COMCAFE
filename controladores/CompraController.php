<?php
// controladores/CompraController.php
header('Content-Type: application/json');
require_once __DIR__ . '/../modelos/Compra.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Leer el cuerpo de la petición enviada por fetch()
    $inputJSON = file_get_contents('php://input');
    $datos = json_decode($inputJSON, true);

    if (!$datos) {
        echo json_encode(['success' => false, 'message' => 'Datos no válidos']);
        exit();
    }

    $compraModel = new Compra();
    $resultado = $compraModel->guardar($datos);

    if ($resultado) {
        echo json_encode(['success' => true, 'message' => 'Compra registrada con éxito en MySQL']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar la compra']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>