<?php
// modelos/Conexion.php

class Conexion {
    private $host = "localhost";
    private $db   = "comcafe_db";
    private $user = "root";
    private $pass = "";
    private $charset = "utf8mb4";

    public function conectar() {
        try {
            $pdo = new PDO("mysql:host={$this->host};dbname={$this->db};charset={$this->charset}", 
                           $this->user, 
                           $this->pass, 
                           [
                               PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                               PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                           ]);
            return $pdo;
        } catch (PDOException $e) {
            die("❌ Error en la conexión a la base de datos: " . $e->getMessage());
        }
    }
}
?>