<?php
require_once __DIR__ . '/includes/db_connect.php';

//Verificación si hay errores en la conexion.
$conexion = connectDB();

if($conexion) {
    echo "Conexión exitosa a la base de datos.";
}

$conexion->close();
?>