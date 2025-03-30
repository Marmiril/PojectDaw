<?php
// Incluir el archivo de conexión a la BBDD.
include_once __DIR__ . "/../includes/db_connect.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtención de datos del formulario.
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validación de datos.
    if (empty($nombre) || empty($email) || empty($password)) {
        header("Location:../views/registro.html?mensaje=" . urlencode("Todos los campos son necesarios.") . "&tipo=error");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location:../views/registro.html?mensaje=" . urlencode("Formato de correo inválido.") . "&tipo=error");
        exit();
    }

    if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        header("Location:../views/registro.html?mensaje=" . urlencode("La contraseña debe tener al menos 8 caracteres, una letra mayúscula y un número.") . "&tipo=error");
        exit();
    }

    // Conectar con la base de datos.
    $conexion = connectDB();

    // Verificación de email.
    $query = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location:../views/registro.html?mensaje=" . urlencode("Este mail ya está registrado.") . "&tipo=error");
        exit();
    }

    // Cifrado de contraseña.
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Inserción de los datos en la base de datos.
    $insert_query = "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)";
    $insert_stmt = $conexion->prepare($insert_query);
    $insert_stmt->bind_param('sss', $nombre, $email, $hashed_password);

    if ($insert_stmt->execute()) {
        // Iniciar sesión para el nuevo usuario
        session_start();
        $_SESSION['usuario_id'] = $conexion->insert_id; // Obtener el ID del usuario recién insertado
        $_SESSION['nombre'] = $nombre; // Guardar el nombre del usuario en la sesión

        // Redirección a la página de preferencias si el registro es exitoso.
        header("Location:../views/formulario_preferencias.php?mensaje=" . urlencode("Registra tus preferencias.") . "&tipo=success");
        exit();
    } else {
        // Redirección en caso de error.
        header("Location:../views/registro.html?mensaje=" . urlencode("Error al registrar el usuario") . "&tipo=error");
    }

    $insert_stmt->close();
    $conexion->close();
}
?>