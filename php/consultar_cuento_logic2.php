<?php
require_once '../includes/db_connect.php';
require_once '../includes/session_start.php';

// Verificar si se ha proporcionado un ID en la URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['error' => 'ID no válido']);
    exit();
}

$cuento_id = intval($_GET['id']);
$usuario_id = $_SESSION['usuario_id'] ?? null; //ID del usuario autenticado (si existe).
$conexion = connectDB();

// Consultar los detalles del cuento.
$query ="SELECT c.titulo, c.tema, c.palabra_guia, c.pasos, c.texto_completo, c.fecha_creacion, c.creador_id, u.nombre AS creador
        FROM cunentos c
        JOIN usuarios u ON c.creador_id = u.id
        WHERE c.id = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param('i', $cuento_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $cuento = $result->fetch_assoc();

    $puede_continuar = true; // Por defecto, el usuario puede continuar
    $usuario_nombre = null;

    if ($usuario_id) {
        // Verificar si el usuario es el creador del cuento
        if ($cuento['creador_id'] === $usuario_id) {
            $puede_continuar = false; // El creador no puede continuar el cuento
        } else {
            // Verificar si el usuario ya ha colaborado en el cuento
            $colaboracionQuery = "SELECT id FROM colaboraciones WHERE cuento_id = ? AND usuario_id = ?";
            $colaboracionStmt = $conexion->prepare($colaboracionQuery);
            $colaboracionStmt->bind_param('ii', $cuento_id, $usuario_id);
            $colaboracionStmt->execute();
            $colaboracionResult = $colaboracionStmt->get_result();

            if ($colaboracionResult->num_rows > 0) {
                $puede_continuar = false; // El usuario ya ha colaborado
            }
            $colaboracionStmt->close();
        }
    }

    // Agregar el campo 'puede_continuar' al resultado
    $cuento['puede_continuar'] = $puede_continuar;

    // Devolver los datos en formato JSON
    echo json_encode($cuento);
} else {
    echo json_encode(['error' => 'Cuento no encontrado.']);
}
$stmt->close();
$conexion->close();

?>