<?php 
require_once '../includes/session_start.php';
require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar si el usuario está autenticado
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: ../views/login.php");
        exit();
    }

    $usuario_id = $_SESSION['usuario_id'];
    $cuento_id = intval($_POST['cuento_id']);
    $fragmento = trim($_POST['fragmento']);

    // Validación de datos.
    if (empty($cuento_id) || empty($fragmento)) {
        $_SESSION['error_message'] = "El fragmento no puede estar vacío.";
        header("Location: ../views/consultar_cuento.php?id=$cuento_id");
        exit();
    }

    // Conexión con la bbdd.
    $conexion = connectDB();

    // Iniciar una transacción para garantizar la consistencia
    $conexion->begin_transaction();

    try {
        // Insertar el fragmento en la tabla de colaboraciones
        $query = "INSERT INTO colaboraciones (cuento_id, usuario_id, fragmento) VALUES (?, ?, ?)";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param('iis', $cuento_id, $usuario_id, $fragmento);
        $stmt->execute();
        $stmt->close();

        // Actualizar el texto completo concatenando el nuevo fragmento
        $updateQuery = "UPDATE cuentos
                        SET texto_completo = CONCAT(texto_completo, '\n\n', ?)
                        WHERE id = ?";
        $updateStmt = $conexion->prepare($updateQuery);
        $updateStmt->bind_param('si', $fragmento, $cuento_id);
        $updateStmt->execute();
        $updateStmt->close();

        // Confirmar la transacción
        $conexion->commit();

        // Guardar mensaje de éxito en la sesión
        $_SESSION['success_message'] = "Fragmento guardado exitosamente.";
        header("Location: ../views/consultar_cuento.php?id=$cuento_id");
        exit();
    } catch (Exception $e) {
        // Revertir la transacción en caso de error.
        $conexion->rollback();
        $_SESSION['error_message'] = "Error al agregar el fragmento: " . $e->getMessage();
        header("Location: ../views/consultar_cuento.php?id=$cuento_id");
        exit();
    } finally {
        $conexion->close();
    }
}
?>