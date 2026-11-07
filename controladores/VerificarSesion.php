<?php
// controladores/VerificarSesion.php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['usuario_id'])) {
    echo json_encode([
        'autenticado' => true,
        'usuario' => $_SESSION['usuario_nombre'],
        'rol' => $_SESSION['usuario_rol']
    ]);
} else {
    echo json_encode(['autenticado' => false]);
}
?>