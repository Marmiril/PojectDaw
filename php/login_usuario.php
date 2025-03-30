<?php
session_start();
require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validar que los campos no estén vacíos.
    if (empty($email) || empty($password)) {
        $_SESSION['mensaje-error'] = "Por favor, llena todos los campos";
        header("Location: ../views/login.php");
        exit();
    }

    $conexion = connectDB();
    $query = "SELECT id, nombre, password FROM usuarios WHERE email = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        if(password_verify($password, $usuario['password'])) {
            // Login exitoso.
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];

            // Redirigir a la página de origen o a perfil.php.
            $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '../views/perfil.php';
            header("Location: $redirect");
            exit();
        } else {
            $_SESSION['mensaje-error'] = "Contraseña incorrecta.";
            header("Location: $redirect"); // ("Location: ../views/login.php);
            exit();
        }
    } else {
        $_SESSION['mensaje-error'] = "Usuario no encontrado.";
        header("Location: $redirect");
        exit();
    }
    $stmt->close();
    $conexion->close();
}
?>