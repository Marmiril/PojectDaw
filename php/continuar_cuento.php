<?php
require_once '../includes/session_start.php';
require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //Verificar que el usuario está autenticado.
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: ../views/login.php");
        exit();
    }

    $usuario_id = $_SESSION['usuario_id'];
    $cuento_id = intval($_POST['cuento_id']);
    $fragmento = trim($_POST['fragmento']);

    //Validación de datos.
    if (empty($cuento_id) || empty($fragmento)) {
        $_SESSION['error_message'] = "El fragmento no puede estar vacío.";
        header("Location: ../views/consultar_cuento.php?id=$cuento_id");
        exit();
    }

    // Conexión con la bbdd.
    $conexion = connectDB();

    // Insertar colaboración.
    $query = "INSERT INTO colaboraciones (cuento_id, usuario_id, fragmento) VALUES (?, ?, ?)";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('iis', $cuento_id, $usuario_id, $fragmento);

    if ($stmt->execute()) {
        // Actualizar los pasos restantes y el estado del cuento
        $updateQuery = "UPDATE cuentos
                        SET pasos_restantes = pasos_restantes - 1,
                            estado = CASE WHEN pasos_restantes - 1 = 0 THEN 'cerrado' ELSE 'abierto' END
                        WHERE id = ?";
        $updateStmt = $conexion->prepare($updateQuery);
        $updateStmt->bind_param('i', $cuento_id);
        $updateStmt->execute();
        $updateStmt->close();

        // Guardar mensaje de éxito en la sesión
        $_SESSION['success_message'] = "Fragmento agregado exitosamente.";
        header("Location: ../views/consultar_cuento.php?id=$cuento_id");
        exit();
    } else {
        // Guardar mensaje de error en la sesión
        $_SESSION['error_message'] = "Error al agregar el fragmento: " . $stmt->error;
        header("Location: ../views/consultar_cuento.php?id=$cuento_id");
        exit();
    }

    $stmt->close();
    $conexion->close();
}
?>