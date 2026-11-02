<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'comcafe_db';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

$sql = "SELECT id, nombre, correo, rol, fecha_registro FROM usuarios LIMIT 100";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "✅ Usuarios encontrados en la base de datos:\n\n";
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row["id"] . " | Nombre: " . $row["nombre"] . " | Correo: " . $row["correo"] . " | Rol: " . $row["rol"] . "\n";
    }
} else {
    echo "No se encontraron resultados.";
}

$conn->close();
?>