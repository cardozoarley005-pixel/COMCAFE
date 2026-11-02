<?php
// modelos/Usuario.php
require_once __DIR__ . '/Conexion.php';

class Usuario {
    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->conectar();
    }

    // Busca un usuario por su correo electrónico
    public function obtenerPorCorreo($correo) {
        $sql = "SELECT * FROM usuarios WHERE correo = :correo LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    }
}
?>