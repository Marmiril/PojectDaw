<?php
require_once '../includes/db_connect.php';

// Verificar si se ha proporcionado un ID en la URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Error: ID no válido');
}

$cuento_id = intval($_GET['id']);
$conexion = connectDB();

// Consultar los detalles del cuento
$query = "SELECT c.titulo, c.tema, c.palabra_guia, c.pasos, c.texto_completo, c.fecha_creacion, c.creador_id, u.nombre AS creador
          FROM cuentos c
          JOIN usuarios u ON c.creador_id = u.id
          WHERE c.id = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param('i', $cuento_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $cuento = $result->fetch_assoc();
} else {
    die("Cuento no encontrado.");
}

$stmt->close();
$conexion->close();
?>