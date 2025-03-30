<?php
/*  ESTE CÓDIGO ES PRIMITIVO Y POCO MODULAR, EL SIGUIENTE MÁS SEGURO Y ESCALABLE.
$host = 'localhost';
$usuario = 'root';
$clave = '';
$base_de_datos = 'cuentacuentos_db';

$conexion = new mysqli($host, $usuario, $clave, $base_de_datos);

//Verificar conexión.
if($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
} else {
    echo "Conexión exitosa";
}
*/
require_once __DIR__ . '/../config/config.php';

function connectDB() {
    try {
        $conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $conexion->set_charset("utf8");

        if($conexion->connect_error) {
            throw new Exception("Error de conexión: " . $conexion->connect_error);
        }
        return $conexion;
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}

?>
