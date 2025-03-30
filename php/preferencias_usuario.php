<?php
require_once '../includes/session_start.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../views/login.php"); // Redirigir al login si no hay sesión
    exit();
}

$usuario_id = $_SESSION['usuario_id']; // Obtener el ID del usuario desde la sesión
require_once __DIR__ . '/../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario
    $color_favorito = trim($_POST['color_favorito']);
    $edad = intval($_POST['edad']);
    $altura = floatval($_POST['altura']);
    $peso = floatval($_POST['peso']);
    $genero = trim($_POST['genero']);

    // Validar los datos
    if (empty($color_favorito) || $edad <= 0 || $altura <= 0 || $peso <= 0 || !in_array($genero, ['M', 'F'])) {
        echo "Error: Datos inválidos.";
        exit();
    }

    // Conectar con la base de datos
    $conexion = connectDB();

    // Verificar si ya existen preferencias para este usuario
    $query = "SELECT * FROM preferencias_usuarios WHERE usuario_id = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('i', $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Si ya existen preferencias, actualizarlas
        $update_query = "UPDATE preferencias_usuarios SET color_favorito = ?, edad = ?, altura = ?, peso = ?, genero = ? WHERE usuario_id = ?";
        $update_stmt = $conexion->prepare($update_query);
        $update_stmt->bind_param('siddsi', $color_favorito, $edad, $altura, $peso, $genero, $usuario_id);
        $update_stmt->execute();
        $update_stmt->close();
    } else {
        // Si no existen preferencias, insertarlas
        $insert_query = "INSERT INTO preferencias_usuarios (usuario_id, color_favorito, edad, altura, peso, genero) VALUES (?, ?, ?, ?, ?, ?)";
        $insert_stmt = $conexion->prepare($insert_query);
        $insert_stmt->bind_param('isidds', $usuario_id, $color_favorito, $edad, $altura, $peso, $genero);
        $insert_stmt->execute();
        $insert_stmt->close();
    }

    $stmt->close();
    $conexion->close();

    // Redirigir al perfil después de guardar las preferencias
    header("Location: ../views/perfil.php");
    exit();
}
?>