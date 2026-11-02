<?php
// modelos/Compra.php
require_once __DIR__ . '/Conexion.php';

class Compra {
    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->conectar();
    }

    public function guardar($datos) {
        $sql = "INSERT INTO compras (
                    cedula_caficultor, nombre_caficultor, finca, tipo_cafe, 
                    sacos, peso_bruto, tara, peso_neto, humedad, broca, 
                    defectos, factor_rendimiento, precio_base, ajuste_factor, 
                    descuento_humedad, precio_final_kg, total_neto
                ) VALUES (
                    :cedula, :nombre, :finca, :tipo_cafe, 
                    :sacos, :peso_bruto, :tara, :peso_neto, :humedad, :broca, 
                    :defectos, :factor, :precio_base, :ajuste_factor, 
                    :descuento_humedad, :precio_final_kg, :total_neto
                )";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':cedula'            => $datos['cedula'],
            ':nombre'            => $datos['nombre'],
            ':finca'             => $datos['finca'],
            ':tipo_cafe'         => $datos['tipo_cafe'],
            ':sacos'             => $datos['sacos'],
            ':peso_bruto'        => $datos['peso_bruto'],
            ':tara'              => $datos['tara'],
            ':peso_neto'         => $datos['peso_neto'],
            ':humedad'           => $datos['humedad'],
            ':broca'             => $datos['broca'] ?? 0,
            ':defectos'          => $datos['defectos'] ?? 0,
            ':factor'            => $datos['factor'],
            ':precio_base'       => 12500, // Precio base fijo o dinámico
            ':ajuste_factor'     => $datos['ajuste_factor'],
            ':descuento_humedad' => $datos['descuento_humedad'],
            ':precio_final_kg'   => $datos['precio_final_kg'],
            ':total_neto'        => $datos['total_neto']
        ]);
    }
}
?>