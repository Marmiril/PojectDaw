<?php
require_once '../includes/db_connect.php';
require_once '../includes/session_start.php';

if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validar que los campos no estén vacíos.
    if (empty($email) || empty($password)) {
        //echo "Por favor, llena todos los campos.";
        //header("Location: ../views/login.php?mensaje=Por%20favor%20complete%20todos%20los%20campos&tipo=error");
        // die("Error: campos vacíos.");
        $_SESSION['mensaje-error'] = "Por favor, llena todos los campos.";
        header("Location: ../views/login.php");
        exit();
    }

    //Conexión con la base de datos.
    $conexion = connectDB();

    //Búsqueda de usuario por mail ya que es único.
    $query = "SELECT id, password FROM usuarios WHERE email = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();

        // Verificación de contraseña.
        if (password_verify($password, $usuario['password'])) {
        // Inicio de sesión.
            $_SESSION ['usuario_id'] = $usuario['id'];
            header("Location: ../views/perfil.php");
        } else {
            $_SESSION['mensaje-error'] = 'Contraseña incorrecta.';
            header("Location: ../views/login.php");
            exit();
        }
    } else {
        $_SESSION['mensaje-error'] = "Usuario no encontrado.";
        header("Location: ../views/login.php");
        exit();
    }

    $stmt->close();
    $conexion->close();
    exit();
} else {
    header("Location: ../views/login.php");


}
?>
